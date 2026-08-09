<?php

namespace App\Modules\Leads\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Contracts\OutreachDraftGenerator;
use App\Modules\Leads\Http\Requests\AddNoteRequest;
use App\Modules\Leads\Http\Requests\AttachTagRequest;
use App\Modules\Leads\Http\Requests\SaveLeadsRequest;
use App\Modules\Leads\Http\Requests\UpdateStatusRequest;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Models\Tag;
use App\Modules\Leads\Services\LeadService;
use App\Modules\Leads\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class LeadsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:leads.view', only: ['index', 'map', 'show']),
            new Middleware('permission:leads.manage', except: ['index', 'map', 'show']),
        ];
    }

    public function __construct(
        protected LeadService $service,
        protected TagService $tagService
    ) {}

    /**
     * The "All leads" screen — every found/enriched business.
     */
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->forUser($request->user()->id)
            ->with(['place', 'tags'])
            ->latest()
            ->paginate(20);

        return view('leads::user.leads.index', [
            'leads' => $leads,
        ]);
    }

    /**
     * The "Map view" screen — leads plotted by location.
     */
    public function map(Request $request): View
    {
        $leads = Lead::query()
            ->forUser($request->user()->id)
            ->with('place')
            ->latest()
            ->get();

        return view('leads::user.leads.map', [
            'leads' => $leads,
        ]);
    }

    /**
     * The "Lead details" screen — one business with its score, draft, and activity.
     */
    public function show(Request $request, Lead $lead): View
    {
        $this->authorizeOwnership($request, $lead);

        $lead->load(['place', 'tags', 'notes.author', 'activities.causedBy']);

        return view('leads::user.leads.show', [
            'lead' => $lead,
            'verdict' => app(LeadScorer::class)->score($lead),
            'draft' => app(OutreachDraftGenerator::class)->draft($lead),
        ]);
    }

    /**
     * Save selected search results as leads. This is where enrichment
     * happens and credits are spent — see LeadService::saveFromSearch().
     */
    public function saveFromSearch(SaveLeadsRequest $request): RedirectResponse
    {
        $searchRun = $request->validated('search_run_id')
            ? SearchRun::query()->find($request->validated('search_run_id'))
            : null;

        if ($searchRun) {
            abort_unless($searchRun->user_id === $request->user()->id, 404);
        }

        $result = $this->service->saveFromSearch(
            $request->user(),
            $searchRun,
            $request->validated('place_id')
        );

        $savedCount = $result['saved']->count();
        $message = trans_choice(':count lead saved.|:count leads saved.', $savedCount, ['count' => $savedCount]);

        if ($result['already_saved']) {
            $message .= ' '.__(':count already in your leads.', ['count' => $result['already_saved']]);
        }

        if ($result['insufficient_credits']) {
            $message .= ' '.__('Some businesses were skipped — not enough credits.');
        }

        return redirect()
            ->route('user.leads.index')
            ->with('success', $message);
    }

    public function updateStatus(UpdateStatusRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorizeOwnership($request, $lead);

        $from = $lead->status;
        $to = $request->validated('status');

        if ($from !== $to) {
            $lead->update(['status' => $to]);
            LeadActivity::logStatusChanged($lead, $from, $to, $request->user());
        }

        return redirect()
            ->route('user.leads.show', $lead)
            ->with('success', __('Status updated.'));
    }

    public function attachTag(AttachTagRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorizeOwnership($request, $lead);

        $tag = $this->tagService->findOrCreate($request->user(), $request->validated('tag'));

        if (! $lead->tags->contains($tag->id)) {
            $lead->tags()->attach($tag);
            LeadActivity::logTagAdded($lead, $tag, $request->user());
        }

        return redirect()
            ->route('user.leads.show', $lead)
            ->with('success', __('Tag added.'));
    }

    public function detachTag(Request $request, Lead $lead, Tag $tag): RedirectResponse
    {
        $this->authorizeOwnership($request, $lead);

        $lead->tags()->detach($tag);
        LeadActivity::logTagRemoved($lead, $tag, $request->user());

        return redirect()
            ->route('user.leads.show', $lead)
            ->with('success', __('Tag removed.'));
    }

    public function addNote(AddNoteRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorizeOwnership($request, $lead);

        $lead->notes()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        LeadActivity::logNoteAdded($lead, $request->user());

        return redirect()
            ->route('user.leads.show', $lead)
            ->with('success', __('Note added.'));
    }

    /**
     * Soft-deletes the lead. The enrichment credit already spent is never
     * refunded — matches the product's stated posture that charges aren't
     * reversed even when the outcome disappoints.
     */
    public function destroy(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorizeOwnership($request, $lead);

        $lead->delete();

        return redirect()
            ->route('user.leads.index')
            ->with('success', __('Lead deleted.'));
    }

    public function bulkTag(Request $request): RedirectResponse
    {
        $request->validate([
            'lead_id' => ['required', 'array', 'min:1'],
            'lead_id.*' => ['integer'],
            'tag' => ['required', 'string', 'max:30'],
        ]);

        $tag = $this->tagService->findOrCreate($request->user(), $request->input('tag'));

        $leads = Lead::query()->forUser($request->user()->id)->whereIn('id', $request->input('lead_id'))->get();

        foreach ($leads as $lead) {
            if (! $lead->tags->contains($tag->id)) {
                $lead->tags()->attach($tag);
                LeadActivity::logTagAdded($lead, $tag, $request->user());
            }
        }

        return redirect()
            ->route('user.leads.index')
            ->with('success', __('Tag added to :count leads.', ['count' => $leads->count()]));
    }

    public function bulkStatus(UpdateStatusRequest $request): RedirectResponse
    {
        $request->validate(['lead_id' => ['required', 'array', 'min:1'], 'lead_id.*' => ['integer']]);

        $to = $request->validated('status');
        $leads = Lead::query()->forUser($request->user()->id)->whereIn('id', $request->input('lead_id'))->get();

        foreach ($leads as $lead) {
            if ($lead->status !== $to) {
                $from = $lead->status;
                $lead->update(['status' => $to]);
                LeadActivity::logStatusChanged($lead, $from, $to, $request->user());
            }
        }

        return redirect()
            ->route('user.leads.index')
            ->with('success', __('Status updated for :count leads.', ['count' => $leads->count()]));
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate(['lead_id' => ['required', 'array', 'min:1'], 'lead_id.*' => ['integer']]);

        $count = Lead::query()
            ->forUser($request->user()->id)
            ->whereIn('id', $request->input('lead_id'))
            ->delete();

        return redirect()
            ->route('user.leads.index')
            ->with('success', __(':count leads deleted.', ['count' => $count]));
    }

    /**
     * Ensure the signed-in user owns the lead, otherwise 404.
     */
    protected function authorizeOwnership(Request $request, Lead $lead): void
    {
        abort_unless($lead->user_id === $request->user()->id, 404);
    }
}

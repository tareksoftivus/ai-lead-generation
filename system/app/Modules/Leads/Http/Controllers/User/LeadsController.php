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
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Models\Tag;
use App\Modules\Leads\Services\LeadService;
use App\Modules\Leads\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
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
        $activeList = null;
        $query = Lead::query()
            ->forUser($request->user()->id)
            ->with(['place', 'tags']);

        if ($request->filled('list')) {
            $activeList = LeadList::query()
                ->forUser($request->user()->id)
                ->findOrFail($request->integer('list'));

            $query->whereHas('lists', fn ($listQuery) => $listQuery->whereKey($activeList->id));
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        return view('leads::user.leads.index', [
            'leads' => $leads,
            'activeList' => $activeList,
        ]);
    }

    /**
     * The "Map view" screen — leads plotted by location.
     */
    public function map(Request $request): View
    {
        $leads = Lead::query()
            ->forUser($request->user()->id)
            ->with(['place', 'tags', 'lists'])
            ->latest()
            ->get();

        $statuses = Lead::statuses();
        $mapLeads = $leads
            ->filter(fn (Lead $lead) => $lead->place && $lead->place->lat !== null && $lead->place->lng !== null)
            ->values()
            ->map(function (Lead $lead) use ($statuses): array {
                $place = $lead->place;
                $bucket = Lead::scoreBucket($lead->score);

                return [
                    'id' => $lead->id,
                    'name' => $place->name,
                    'address' => $place->formatted_address,
                    'lat' => (float) $place->lat,
                    'lng' => (float) $place->lng,
                    'score' => $lead->score,
                    'bucket' => $bucket,
                    'status' => $lead->status,
                    'status_label' => $statuses[$lead->status]['label'] ?? ucfirst($lead->status),
                    'contact' => $lead->hasContact() ? 'yes' : 'no',
                    'contact_label' => $lead->hasContact() ? __('Email on file') : ($place->phone ? __('Phone only') : __('No contact yet')),
                    'email' => $lead->email,
                    'phone' => $place->phone,
                    'category' => $place->google_category,
                    'rating' => $place->rating,
                    'reviews' => $place->review_count,
                    'tags' => $lead->tags->pluck('name')->values(),
                    'lists' => $lead->lists->pluck('name')->values(),
                    'url' => route('user.leads.show', $lead),
                    'maps_url' => 'https://www.google.com/maps/search/?api=1&query='.urlencode($place->name.' '.$place->formatted_address),
                ];
            });

        $center = $mapLeads->isNotEmpty()
            ? [
                'lat' => round((float) $mapLeads->avg('lat'), 6),
                'lng' => round((float) $mapLeads->avg('lng'), 6),
            ]
            : ['lat' => 23.8103, 'lng' => 90.4125];

        return view('leads::user.leads.map', [
            'leads' => $leads,
            'mapLeads' => $mapLeads,
            'mapCenter' => $center,
            'statuses' => $statuses,
            'lists' => LeadList::query()->forUser($request->user()->id)->orderBy('name')->get(),
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
     * Save selected generated search results as leads. Credits are spent
     * during generation; this action only persists and list-attaches.
     */
    public function saveFromSearch(SaveLeadsRequest $request): RedirectResponse
    {
        $searchRun = $request->validated('search_run_id')
            ? SearchRun::query()->find($request->validated('search_run_id'))
            : null;

        if ($searchRun) {
            abort_unless($searchRun->user_id === $request->user()->id, 404);
        } elseif ($request->boolean('save_all')) {
            return redirect()
                ->route('user.search.new')
                ->with('error', __('Run a search before saving all generated leads.'));
        }

        $placeIds = $request->boolean('save_all') && $searchRun
            ? $searchRun->places()->pluck('places.id')->all()
            : $request->validated('place_id');

        Log::info('Saving leads from search.', [
            'user_id' => $request->user()->id,
            'search_run_id' => $searchRun?->id,
            'save_all' => $request->boolean('save_all'),
            'place_count' => count($placeIds),
        ]);

        $result = $this->service->saveFromSearch(
            $request->user(),
            $searchRun,
            $placeIds
        );

        $savedCount = $result['saved']->count();

        Log::info('Saved leads from search.', [
            'user_id' => $request->user()->id,
            'search_run_id' => $searchRun?->id,
            'saved_count' => $savedCount,
            'already_saved' => $result['already_saved'],
            'insufficient_credits' => $result['insufficient_credits'],
            'list_id' => $result['list']?->id,
        ]);

        $message = trans_choice(':count lead saved.|:count leads saved.', $savedCount, ['count' => $savedCount]);

        if ($result['already_saved']) {
            $message .= ' '.__(':count already in your leads.', ['count' => $result['already_saved']]);
        }

        if ($result['list']) {
            $message .= ' '.__('Saved under the ":name" list.', ['name' => $result['list']->name]);
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
     * Soft-deletes the lead. Generation credits are not refunded when a saved
     * generated lead is later removed.
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

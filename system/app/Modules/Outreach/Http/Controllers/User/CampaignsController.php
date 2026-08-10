<?php

namespace App\Modules\Outreach\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Outreach\Models\LeadCampaign;
use App\Modules\Outreach\Services\CampaignService;
use App\Modules\Outreach\Services\LeadSourceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampaignsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:outreach.view', only: ['index']),
            new Middleware('permission:outreach.manage', except: ['index']),
        ];
    }

    public function __construct(
        protected CampaignService $campaigns,
        protected LeadSourceService $sources
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $campaigns = LeadCampaign::query()
            ->forUser($user->id)
            ->withCount('leads')
            ->latest()
            ->get();

        $leadCount = Lead::query()->forUser($user->id)->count();
        $contactableCount = Lead::query()->forUser($user->id)->whereNotNull('email')->count();
        $lists = LeadList::query()->forUser($user->id)->withCount('leads')->orderBy('name')->get();
        $searchRuns = SearchRun::query()->forUser($user->id)->with('search')->where('results_count', '>', 0)->latest()->limit(20)->get();

        return view('outreach::user.campaigns.index', [
            'campaigns' => $campaigns,
            'statuses' => LeadCampaign::statuses(),
            'leadCount' => $leadCount,
            'contactableCount' => $contactableCount,
            'lists' => $lists,
            'searchRuns' => $searchRuns,
            'kpis' => [
                'review' => $campaigns->where('status', LeadCampaign::STATUS_REVIEW)->count(),
                'active' => $campaigns->where('status', LeadCampaign::STATUS_ACTIVE)->count(),
                'opened_rate' => $this->rate($campaigns->sum('opened_count'), $campaigns->sum('sent_count')),
                'replied_rate' => $this->rate($campaigns->sum('replied_count'), $campaigns->sum('sent_count')),
                'messages_to_review' => $campaigns->where('status', LeadCampaign::STATUS_REVIEW)->sum('recipients_count'),
                'sending_sent' => $campaigns->where('status', LeadCampaign::STATUS_ACTIVE)->sum('sent_count'),
                'sending_recipients' => $campaigns->where('status', LeadCampaign::STATUS_ACTIVE)->sum('recipients_count'),
                'replies' => $campaigns->sum('replied_count'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSource($request, [
            'name' => ['required', 'string', 'max:160'],
            'daily_limit' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $selectedIds = $this->selectedIds($request);
        $count = $this->sources->count(
            $request->user(),
            $data['source_type'],
            $data['source_id'] ?? null,
            $selectedIds,
            true
        );

        if ($count < 1) {
            return back()->withInput()->with('error', __('No contactable leads match that campaign source.'));
        }

        $campaign = $this->campaigns->create($request->user(), $data, $selectedIds);

        Log::info('Lead campaign created.', [
            'user_id' => $request->user()->id,
            'campaign_id' => $campaign->id,
            'recipients_count' => $campaign->recipients_count,
        ]);

        return redirect()->route('user.campaigns.index')->with('success', __('Campaign created for :count recipients.', [
            'count' => $campaign->recipients_count,
        ]));
    }

    public function update(Request $request, LeadCampaign $campaign): RedirectResponse
    {
        $this->authorizeOwnership($request, $campaign);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(LeadCampaign::statuses()))],
        ]);

        $this->campaigns->updateStatus($campaign, $validated['status']);

        return redirect()->route('user.campaigns.index')->with('success', __('Campaign updated.'));
    }

    public function duplicate(Request $request, LeadCampaign $campaign): RedirectResponse
    {
        $this->authorizeOwnership($request, $campaign);

        $copy = $this->campaigns->duplicate($campaign);

        return redirect()->route('user.campaigns.index')->with('success', __('Campaign duplicated as ":name".', [
            'name' => $copy->name,
        ]));
    }

    public function destroy(Request $request, LeadCampaign $campaign): RedirectResponse
    {
        $this->authorizeOwnership($request, $campaign);
        $campaign->delete();

        return redirect()->route('user.campaigns.index')->with('success', __('Campaign deleted.'));
    }

    /**
     * @param  array<string, array<int, mixed>>  $extraRules
     * @return array<string, mixed>
     */
    protected function validateSource(Request $request, array $extraRules = []): array
    {
        $validator = Validator::make($request->all(), array_merge([
            'source_type' => ['required', Rule::in([
                LeadSourceService::SOURCE_ALL,
                LeadSourceService::SOURCE_SELECTION,
                LeadSourceService::SOURCE_LIST,
                LeadSourceService::SOURCE_SEARCH,
            ])],
            'source_id' => ['nullable', 'integer'],
            'selected_ids' => ['nullable', 'string'],
        ], $extraRules));

        $validator->after(function ($validator) use ($request): void {
            $type = $request->input('source_type');
            $sourceId = $request->integer('source_id');

            if ($type === LeadSourceService::SOURCE_LIST) {
                $exists = LeadList::query()->forUser($request->user()->id)->whereKey($sourceId)->exists();
                if (! $exists) {
                    $validator->errors()->add('source_id', __('Choose a valid lead list.'));
                }
            }

            if ($type === LeadSourceService::SOURCE_SEARCH) {
                $exists = SearchRun::query()->forUser($request->user()->id)->whereKey($sourceId)->exists();
                if (! $exists) {
                    $validator->errors()->add('source_id', __('Choose a valid search.'));
                }
            }
        });

        return $validator->validate();
    }

    /**
     * @return array<int, int>
     */
    protected function selectedIds(Request $request): array
    {
        return collect(explode(',', (string) $request->input('selected_ids')))
            ->map(fn (string $id): int => (int) trim($id))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function authorizeOwnership(Request $request, LeadCampaign $campaign): void
    {
        abort_unless($campaign->user_id === $request->user()->id, 404);
    }

    protected function rate(int $part, int $whole): int
    {
        return $whole > 0 ? (int) round(($part / $whole) * 100) : 0;
    }
}

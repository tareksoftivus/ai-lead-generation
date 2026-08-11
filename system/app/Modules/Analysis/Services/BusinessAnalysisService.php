<?php

namespace App\Modules\Analysis\Services;

use App\Models\User;
use App\Modules\Analysis\Models\BusinessAnalysisItem;
use App\Modules\Analysis\Models\BusinessAnalysisRun;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BusinessAnalysisService
{
    public function __construct(
        protected CreditLedger $ledger,
        protected LeadScorer $scorer
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function dashboardData(User $user, ?string $selectedListId = null): array
    {
        $lists = LeadList::query()
            ->forUser($user->id)
            ->withCount('leads')
            ->latest()
            ->get();

        $selectedList = $this->selectedList($lists, $selectedListId);
        $analysisCounts = $this->analysisCountsForLists($user, $lists);

        $lastRun = BusinessAnalysisRun::query()
            ->forUser($user->id)
            ->where('businesses_count', '>', 0)
            ->with(['leadList', 'items.lead.place'])
            ->latest()
            ->first();

        return [
            'lists' => $lists,
            'selectedList' => $selectedList,
            'analysisCounts' => $analysisCounts,
            'lastRun' => $lastRun,
            'lastItems' => $lastRun?->items->sortByDesc('score')->values() ?? collect(),
        ];
    }

    public function run(User $user, int $leadListId, string $focus, bool $skipAnalysed): BusinessAnalysisRun
    {
        $startedAt = now();

        return DB::transaction(function () use ($user, $leadListId, $focus, $skipAnalysed, $startedAt) {
            $lockedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            $leadList = LeadList::query()
                ->forUser($lockedUser->id)
                ->findOrFail($leadListId);

            $allLeadIds = $leadList->leads()
                ->where('leads.user_id', $lockedUser->id)
                ->pluck('leads.id');

            $existingLeadIds = BusinessAnalysisItem::query()
                ->forUser($lockedUser->id)
                ->whereIn('lead_id', $allLeadIds)
                ->pluck('lead_id');

            $leadIdsToAnalyse = $skipAnalysed
                ? $allLeadIds->diff($existingLeadIds)->values()
                : $allLeadIds->values();

            $creditsToSpend = $leadIdsToAnalyse->diff($existingLeadIds)->count();

            $run = BusinessAnalysisRun::query()->create([
                'user_id' => $lockedUser->id,
                'lead_list_id' => $leadList->id,
                'focus' => $focus,
                'skip_analysed' => $skipAnalysed,
                'status' => BusinessAnalysisRun::STATUS_DONE,
                'businesses_count' => $leadIdsToAnalyse->count(),
                'credits_spent' => 0,
                'started_at' => $startedAt,
                'finished_at' => now(),
            ]);

            if ($creditsToSpend > 0) {
                $this->ledger->spend($lockedUser, $creditsToSpend, 'business_analysis', $run, [
                    'lead_list_id' => $leadList->id,
                    'focus' => $focus,
                    'skip_analysed' => $skipAnalysed,
                ]);
            }

            $leads = Lead::query()
                ->forUser($lockedUser->id)
                ->with('place')
                ->whereIn('id', $leadIdsToAnalyse)
                ->get();

            foreach ($leads as $lead) {
                BusinessAnalysisItem::query()->updateOrCreate(
                    [
                        'user_id' => $lockedUser->id,
                        'lead_id' => $lead->id,
                    ],
                    $this->analysisPayload($lead, $run)
                );
            }

            $run->update([
                'credits_spent' => $creditsToSpend,
                'finished_at' => now(),
            ]);

            return $run->fresh(['leadList', 'items.lead.place']);
        });
    }

    /**
     * @param  Collection<int, LeadList>  $lists
     */
    protected function selectedList(Collection $lists, ?string $selectedListId): ?LeadList
    {
        if ($lists->isEmpty()) {
            return null;
        }

        if ($selectedListId) {
            $selected = $lists->firstWhere('id', (int) $selectedListId);

            if ($selected) {
                return $selected;
            }
        }

        return $lists->first();
    }

    /**
     * @param  Collection<int, LeadList>  $lists
     * @return array<int, int>
     */
    protected function analysisCountsForLists(User $user, Collection $lists): array
    {
        if ($lists->isEmpty()) {
            return [];
        }

        $rows = DB::table('lead_list_lead')
            ->join('business_analysis_items', function ($join) use ($user) {
                $join->on('business_analysis_items.lead_id', '=', 'lead_list_lead.lead_id')
                    ->where('business_analysis_items.user_id', '=', $user->id);
            })
            ->whereIn('lead_list_lead.lead_list_id', $lists->pluck('id'))
            ->select('lead_list_lead.lead_list_id', DB::raw('count(*) as analysed_count'))
            ->groupBy('lead_list_lead.lead_list_id')
            ->pluck('analysed_count', 'lead_list_id')
            ->map(fn ($count): int => (int) $count)
            ->all();

        return $rows;
    }

    /**
     * @return array<string, mixed>
     */
    protected function analysisPayload(Lead $lead, BusinessAnalysisRun $run): array
    {
        $scored = $this->scorer->score($lead);
        $score = (int) ($lead->score ?? $scored['score']);
        $place = $lead->place;
        $signals = $this->signals($lead, $place, $score);

        return [
            'business_analysis_run_id' => $run->id,
            'score' => $score,
            'read' => $this->read($place, $signals),
            'gap' => $this->gap($place, $signals),
            'fit' => $this->fit($place, $signals, $score),
            'fit_status' => $score >= 65 ? BusinessAnalysisItem::FIT_YES : BusinessAnalysisItem::FIT_MAYBE,
            'signals' => $signals,
            'analysed_at' => now(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function signals(Lead $lead, ?Place $place, int $score): array
    {
        $reviewCount = (int) ($place?->review_count ?? 0);

        return [
            'name' => $place?->name ?? __('This business'),
            'category' => $place?->google_category,
            'review_count' => $reviewCount,
            'rating' => $place?->rating,
            'has_website' => $place?->hasWebsite() ?? false,
            'has_phone' => $place?->hasPhone() ?? false,
            'has_email' => $lead->hasContact(),
            'score' => $score,
        ];
    }

    /**
     * @param  array<string, mixed>  $signals
     */
    protected function read(?Place $place, array $signals): string
    {
        $name = $signals['name'];
        $reviewText = $signals['review_count'] > 0
            ? __(':count public reviews', ['count' => number_format($signals['review_count'])])
            : __('limited public review signal');

        $parts = [
            __(':name has :reviews', ['name' => $name, 'reviews' => $reviewText]),
        ];

        if ($signals['rating']) {
            $parts[] = __('and averages :rating stars', ['rating' => number_format((float) $signals['rating'], 1)]);
        }

        $parts[] = $signals['has_website']
            ? __('with a website buyers can inspect before contacting them')
            : __('but no website was found, so trust has to be built elsewhere');

        $parts[] = $signals['has_phone']
            ? __('A phone number is listed, which keeps same-day enquiries possible.')
            : __('No phone number is listed, which makes fast buyer intent harder to capture.');

        return implode(' ', $parts);
    }

    /**
     * @param  array<string, mixed>  $signals
     */
    protected function gap(?Place $place, array $signals): string
    {
        if (! $signals['has_website']) {
            return __('No website found for a business people need to trust before they call');
        }

        if (! $signals['has_phone']) {
            return __('Website exists, but there is no phone number for urgent enquiries');
        }

        if (! $signals['has_email']) {
            return __('No direct email captured yet, so outreach still depends on public contact paths');
        }

        if (($signals['review_count'] ?? 0) < 20) {
            return __('Local proof is thin, so the opening should focus on visibility and reputation');
        }

        return __('Enough public demand exists to open with conversion, booking, or follow-up friction');
    }

    /**
     * @param  array<string, mixed>  $signals
     */
    protected function fit(?Place $place, array $signals, int $score): string
    {
        if ($score >= 80) {
            return __('Strong fit - enough demand signal to justify a direct, specific opener');
        }

        if ($score >= 65) {
            return __('Good fit - visible enough to personalise, with a clear operational gap');
        }

        if ($score >= 45) {
            return __('Possible fit - use a lighter opener until more intent is visible');
        }

        return __('Weaker fit - enrich or verify before spending time on a custom pitch');
    }
}

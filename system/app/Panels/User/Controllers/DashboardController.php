<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\PricingPlan\Services\PricingPlanService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected PricingPlanService $pricingPlanService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $userId = $user->id;
        $now = CarbonImmutable::now();
        $monthStart = $now->startOfMonth();
        $lastMonthStart = $monthStart->subMonth();
        $lastMonthEnd = $monthStart->subSecond();

        $searchesThisMonth = SearchRun::query()
            ->forUser($userId)
            ->where('created_at', '>=', $monthStart)
            ->count();

        $searchesLastMonth = SearchRun::query()
            ->forUser($userId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();

        $leadsThisMonth = Lead::query()
            ->forUser($userId)
            ->where('created_at', '>=', $monthStart)
            ->count();

        $leadsLastMonth = Lead::query()
            ->forUser($userId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();

        $creditsSpentThisMonth = (int) abs(CreditTransaction::query()
            ->forUser($userId)
            ->where('type', 'spend')
            ->where('created_at', '>=', $monthStart)
            ->sum('amount'));

        $featuredPlan = $this->pricingPlanService->featuredPlan();
        $monthlyCredits = max((int) ($featuredPlan?->credits_monthly ?? 0), (int) $user->credits_balance + $creditsSpentThisMonth);

        $runningSearches = SearchRun::query()
            ->forUser($userId)
            ->with('search')
            ->withCount(['leads', 'places'])
            ->whereIn('status', [SearchRun::STATUS_PENDING, SearchRun::STATUS_RUNNING])
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (SearchRun $run) => $this->formatSearchRun($run));

        $topLeads = Lead::query()
            ->forUser($userId)
            ->with('place')
            ->whereNotNull('score')
            ->orderByDesc('score')
            ->latest()
            ->limit(5)
            ->get();

        $recentSearches = SearchRun::query()
            ->forUser($userId)
            ->with('search')
            ->withCount(['leads', 'places'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (SearchRun $run) => $this->formatSearchRun($run));

        $leadChartData = $this->leadChartData($userId);

        return view('panels.user.dashboard', [
            'creditsRemaining' => (int) $user->credits_balance,
            'monthlyCredits' => $monthlyCredits,
            'searchesThisMonth' => $searchesThisMonth,
            'searchesDelta' => $searchesThisMonth - $searchesLastMonth,
            'leadsFound' => Lead::query()->forUser($userId)->count(),
            'leadsDeltaPercent' => $this->percentChange($leadsThisMonth, $leadsLastMonth),
            'averageLeadScore' => (int) round((float) Lead::query()->forUser($userId)->whereNotNull('score')->avg('score')),
            'runningSearches' => $runningSearches,
            'runningSearchCount' => $runningSearches->count(),
            'newLeadsSinceYesterday' => Lead::query()->forUser($userId)->where('created_at', '>=', $now->subDay())->count(),
            'chartLabels' => $leadChartData['labels'],
            'chartValues' => $leadChartData['values'],
            'topLeads' => $topLeads,
            'recentSearches' => $recentSearches,
            'hasDashboardActivity' => $recentSearches->isNotEmpty() || $topLeads->isNotEmpty(),
        ]);
    }

    /**
     * @return array{labels: string, values: string}
     */
    protected function leadChartData(int $userId): array
    {
        $start = CarbonImmutable::now()->subDays(29)->startOfDay();

        $counts = Lead::query()
            ->forUser($userId)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as lead_date, COUNT(*) as aggregate')
            ->groupBy('lead_date')
            ->pluck('aggregate', 'lead_date');

        $days = collect(range(0, 29))->map(fn (int $offset) => $start->addDays($offset));

        return [
            'labels' => $days->map(fn (CarbonImmutable $day) => $day->format('j M'))->implode(','),
            'values' => $days->map(fn (CarbonImmutable $day) => (int) ($counts[$day->toDateString()] ?? 0))->implode(','),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatSearchRun(SearchRun $run): array
    {
        $target = $this->targetCount($run);
        $found = max((int) $run->results_count, (int) ($run->places_count ?? 0), (int) ($run->leads_count ?? 0));

        return [
            'id' => $run->id,
            'label' => $this->searchLabel($run),
            'keyword' => $this->firstFilterValue($run->filters, 'keyword') ?: ($run->search?->name ?: __('Search')),
            'location' => $this->firstFilterValue($run->filters, 'location') ?: __('Any location'),
            'found' => $found,
            'target' => $target,
            'credits' => (int) $run->credits_spent,
            'status' => $run->status,
            'started_at' => $run->started_at ?: $run->created_at,
            'created_at' => $run->created_at,
            'progress' => $target > 0 ? min(100, (int) round(($found / $target) * 100)) : 0,
            'url' => route('user.search.results', $run),
        ];
    }

    protected function searchLabel(SearchRun $run): string
    {
        $keyword = $this->firstFilterValue($run->filters, 'keyword') ?: ($run->search?->name ?: __('Search'));
        $location = $this->firstFilterValue($run->filters, 'location');

        return $location ? __(':keyword in :location', ['keyword' => $keyword, 'location' => $location]) : $keyword;
    }

    /**
     * @param  array<string, mixed>|null  $filters
     */
    protected function firstFilterValue(?array $filters, string $key): ?string
    {
        $value = $filters[$key] ?? null;
        $value = is_array($value) ? reset($value) : $value;
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    protected function targetCount(SearchRun $run): int
    {
        $requested = $run->filters['requested_count'] ?? null;
        $requested = is_numeric($requested) ? (int) $requested : 0;

        return max($requested, (int) $run->results_count, (int) ($run->places_count ?? 0), (int) ($run->leads_count ?? 0), 1);
    }

    protected function percentChange(int $current, int $previous): int
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return (int) round((($current - $previous) / $previous) * 100);
    }
}

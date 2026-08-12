<?php

namespace App\Modules\Leads\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Http\Requests\RunSearchRequest;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadBank;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Search;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Services\SearchEstimateService;
use App\Modules\Leads\Services\SearchPromptParser;
use App\Modules\Leads\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SearchController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:leads.search'),
        ];
    }

    public function __construct(
        protected SearchService $service,
        protected SearchEstimateService $estimateService
    ) {}

    /**
     * The "New search" screen — filters rail + cost estimate + results.
     */
    public function new(Request $request): View
    {
        $request->session()->forget('_old_input');

        return view('leads::user.search.new', $this->viewData($request));
    }

    /**
     * Run a search. Renders the results inline for the common (synchronous)
     * case, or redirects to history for a queued multi-combination run.
     */
    public function run(RunSearchRequest $request): View|RedirectResponse
    {
        $filters = $this->filtersFromRequest($request);

        try {
            $searchRun = $this->service->run($request->user(), $filters);
        } catch (InsufficientCreditsException) {
            return back()
                ->withInput()
                ->with('error', __('You dont have sufficient credits please upgrade your plan'));
        }

        if (! $searchRun->isTerminal()) {
            return redirect()
                ->route('user.search.history')
                ->with('success', __('Your search is running — this can take a moment for multiple filters.'));
        }

        $scorer = app(LeadScorer::class);

        $results = $searchRun->places->map(function (Place $place) use ($scorer) {
            $ephemeralLead = new Lead(['place_id' => $place->id]);
            $ephemeralLead->setRelation('place', $place);

            return [
                'place' => $place,
                'score' => $scorer->score($ephemeralLead)['score'],
            ];
        });

        return view('leads::user.search.new', $this->viewData($request, $searchRun, [
            'searchRun' => $searchRun,
            'results' => $results,
        ]));
    }

    /**
     * Live cost/result estimate as filters change. Never calls the Places
     * API — see SearchEstimateService for why.
     */
    public function estimate(Request $request): JsonResponse
    {
        $filters = $this->filtersFromRequest($request);

        return response()->json($this->estimateService->estimate($request->user(), $filters));
    }

    /**
     * Save the current filter rail as a named, reusable search — a distinct
     * action from running one.
     */
    public function saveSearch(RunSearchRequest $request): RedirectResponse
    {
        $filters = $this->filtersFromRequest($request);
        $name = $request->validated('name') ?: __('Untitled search');

        Search::query()->create([
            'user_id' => $request->user()->id,
            'name' => $name,
            'filters' => $filters,
        ]);

        return redirect()
            ->route('user.search.new')
            ->with('success', __('Search saved.'));
    }

    /**
     * The "Search history" screen — past searches, re-run + delete actions.
     */
    public function history(Request $request): View
    {
        $searchRuns = SearchRun::query()
            ->forUser($request->user()->id)
            ->with('search')
            ->latest()
            ->paginate(20);

        return view('leads::user.search.history', [
            'searchRuns' => $searchRuns,
        ]);
    }

    /**
     * View a past run's results.
     */
    public function results(Request $request, SearchRun $searchRun): View
    {
        $this->authorizeOwnership($request, $searchRun);

        $scorer = app(LeadScorer::class);

        $results = $searchRun->places->map(function (Place $place) use ($scorer) {
            $ephemeralLead = new Lead(['place_id' => $place->id]);
            $ephemeralLead->setRelation('place', $place);

            return [
                'place' => $place,
                'score' => $scorer->score($ephemeralLead)['score'],
            ];
        });

        return view('leads::user.search.new', $this->viewData($request, $searchRun, [
            'searchRun' => $searchRun,
            'results' => $results,
        ]));
    }

    /**
     * Re-run a past search with the same filter snapshot. Un-enriched
     * businesses reappear and can be saved/enriched again; already-saved
     * leads are naturally excluded when skip_owned was set on the original.
     */
    public function rerun(Request $request, SearchRun $searchRun): RedirectResponse
    {
        $this->authorizeOwnership($request, $searchRun);

        try {
            $this->service->run($request->user(), $searchRun->filters);
        } catch (InsufficientCreditsException) {
            return back()
                ->with('error', __('You dont have sufficient credits please upgrade your plan'));
        }

        return redirect()
            ->route('user.search.history')
            ->with('success', __('Search re-run started.'));
    }

    /**
     * Delete a search run from history. Leads already saved from it are
     * never touched — search_run_id is nulled, not cascaded.
     */
    public function destroy(Request $request, SearchRun $searchRun): RedirectResponse
    {
        $this->authorizeOwnership($request, $searchRun);

        $searchRun->delete();

        return redirect()
            ->route('user.search.history')
            ->with('success', __('Search removed from your history.'));
    }

    /**
     * Ensure the signed-in user owns the search run, otherwise 404.
     */
    protected function authorizeOwnership(Request $request, SearchRun $searchRun): void
    {
        abort_unless($searchRun->user_id === $request->user()->id, 404);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    protected function viewData(Request $request, ?SearchRun $searchRun = null, array $extra = []): array
    {
        $userId = $request->user()->id;

        $savedSearches = Search::query()
            ->forUser($userId)
            ->latest()
            ->limit(5)
            ->get();

        $recentSearches = SearchRun::query()
            ->forUser($userId)
            ->latest()
            ->limit(5)
            ->get();

        return array_merge([
            'activeFilters' => $searchRun?->filters ?? [],
            'savedSearches' => $savedSearches,
            'recentSearches' => $recentSearches,
            'filterOptions' => $this->filterOptions($savedSearches, $recentSearches),
        ], $extra);
    }

    /**
     * @return array{keywords: array<int, string>, locations: array<int, string>, excludes: array<int, string>, categories: array<int, string>}
     */
    protected function filterOptions(Collection $savedSearches, Collection $recentSearches): array
    {
        $filters = $savedSearches
            ->pluck('filters')
            ->merge($recentSearches->pluck('filters'));

        $keywords = $this->valuesFromFilters($filters, 'keyword', ['dentists', 'orthodontists', 'dental clinics', 'cosmetic dentists']);
        $locations = $this->valuesFromFilters($filters, 'location', ['Austin, TX', 'Dallas, TX', 'Houston, TX', 'San Antonio, TX']);
        $excludes = $this->valuesFromFilters($filters, 'exclude_keyword', ['franchise', 'permanently closed', 'hospital']);
        $categories = $this->valuesFromFilters($filters, 'category', ['Dentist', 'Orthodontist', 'Dental clinic', 'Cosmetic dentist']);

        if (Schema::hasTable('leads_bank')) {
            $keywords = $this->mergeOptions($keywords, LeadBank::query()->whereNotNull('business_type')->distinct()->limit(20)->pluck('business_type')->all());
            $locations = $this->mergeOptions($locations, LeadBank::query()->whereNotNull('location')->distinct()->limit(20)->pluck('location')->all());
            $categories = $this->mergeOptions($categories, LeadBank::query()->whereNotNull('google_category')->distinct()->limit(20)->pluck('google_category')->all());
        }

        return [
            'keywords' => $keywords,
            'locations' => $locations,
            'excludes' => $excludes,
            'categories' => $categories,
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>|null>  $filters
     * @param  array<int, string>  $fallback
     * @return array<int, string>
     */
    protected function valuesFromFilters(Collection $filters, string $key, array $fallback): array
    {
        $values = $filters
            ->flatMap(fn (?array $filter) => (array) ($filter[$key] ?? []))
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->values()
            ->all();

        return $this->mergeOptions($fallback, $values);
    }

    /**
     * @param  array<int, string>  $base
     * @param  array<int, string>  $extra
     * @return array<int, string>
     */
    protected function mergeOptions(array $base, array $extra): array
    {
        return collect($base)
            ->merge($extra)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn ($value) => mb_strtolower($value))
            ->take(20)
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function filtersFromRequest(Request $request): array
    {
        $prompt = trim((string) $request->input('prompt', ''));
        $parsed = $prompt !== ''
            ? app(SearchPromptParser::class)->parse($prompt)
            : [];

        $value = fn (string $key, mixed $default = null) => $request->filled($key)
            ? $request->input($key)
            : ($parsed[$key] ?? $default);

        return [
            'keyword' => (array) $value('keyword', []),
            'location' => (array) $value('location', []),
            'radius' => $request->input('radius'),
            'exclude_keyword' => (array) $request->input('exclude_keyword', []),
            'min_rating' => $value('min_rating'),
            'min_reviews' => $value('min_reviews'),
            'min_reviews_from' => $value('min_reviews_from'),
            'min_reviews_to' => $request->input('min_reviews_to'),
            'requested_count' => $value('requested_count'),
            'has_website' => $request->boolean('has_website'),
            'has_phone' => $request->boolean('has_phone'),
            'category' => (array) $request->input('category', []),
            'skip_owned' => $request->boolean('skip_owned'),
            'one_per_business' => $request->boolean('one_per_business'),
            'prompt' => $prompt !== '' ? $prompt : null,
        ];
    }
}

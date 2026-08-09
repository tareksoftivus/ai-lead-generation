<?php

namespace App\Modules\Leads\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Http\Requests\RunSearchRequest;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Search;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Services\SearchEstimateService;
use App\Modules\Leads\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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
    public function new(): View
    {
        return view('leads::user.search.new');
    }

    /**
     * Run a search. Renders the results inline for the common (synchronous)
     * case, or redirects to history for a queued multi-combination run.
     */
    public function run(RunSearchRequest $request): View|RedirectResponse
    {
        $filters = $this->filtersFromRequest($request);

        $searchRun = $this->service->run($request->user(), $filters);

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

        return view('leads::user.search.new', [
            'searchRun' => $searchRun,
            'results' => $results,
        ]);
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

        return view('leads::user.search.new', [
            'searchRun' => $searchRun,
            'results' => $results,
        ]);
    }

    /**
     * Re-run a past search with the same filter snapshot. Un-enriched
     * businesses reappear and can be saved/enriched again; already-saved
     * leads are naturally excluded when skip_owned was set on the original.
     */
    public function rerun(Request $request, SearchRun $searchRun): RedirectResponse
    {
        $this->authorizeOwnership($request, $searchRun);

        $this->service->run($request->user(), $searchRun->filters);

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
     * @return array<string, mixed>
     */
    protected function filtersFromRequest(Request $request): array
    {
        return [
            'keyword' => (array) $request->input('keyword', []),
            'location' => (array) $request->input('location', []),
            'radius' => $request->input('radius'),
            'exclude_keyword' => (array) $request->input('exclude_keyword', []),
            'min_rating' => $request->input('min_rating'),
            'min_reviews' => $request->input('min_reviews'),
            'min_reviews_from' => $request->input('min_reviews_from'),
            'min_reviews_to' => $request->input('min_reviews_to'),
            'has_website' => $request->boolean('has_website'),
            'has_phone' => $request->boolean('has_phone'),
            'category' => (array) $request->input('category', []),
            'skip_owned' => $request->boolean('skip_owned'),
            'one_per_business' => $request->boolean('one_per_business'),
        ];
    }
}

<?php

namespace App\Modules\Leads\Services;

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\Leads\Jobs\RunPlacesSearchJob;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Search;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Services\GooglePlaces\GooglePlacesClient;
use App\Modules\Leads\Services\GooglePlaces\PlacesResultMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Sleep;
use Throwable;

class SearchService
{
    protected const DEFAULT_TARGET_COUNT = 5;

    protected const MAX_TARGET_COUNT = 30;

    protected const MAX_GOOGLE_PAGES = 3;

    public function __construct(
        protected GooglePlacesClient $client,
        protected LeadBankService $leadBank,
        protected CreditLedger $ledger
    ) {}

    /**
     * Run a search for the given user. A single keyword×location combination
     * runs synchronously (a few seconds, tolerable inline); multiple
     * combinations are queued so a slow multi-combination search doesn't tie
     * up the request — this is exactly what the "Running" status/tab is for.
     *
     * @param  array<string, mixed>  $filters
     */
    public function run(User $user, array $filters, ?string $searchName = null): SearchRun
    {
        $this->ensureCanGenerate($user, $filters);

        $search = null;

        if ($searchName) {
            $search = Search::query()->create([
                'user_id' => $user->id,
                'name' => $searchName,
                'filters' => $filters,
            ]);
        }

        $searchRun = SearchRun::query()->create([
            'user_id' => $user->id,
            'search_id' => $search?->id,
            'filters' => $filters,
            'status' => SearchRun::STATUS_PENDING,
        ]);

        $combinations = $this->combinationCount($filters);

        if ($combinations > 1) {
            $searchRun->update(['status' => SearchRun::STATUS_RUNNING]);
            RunPlacesSearchJob::dispatch($searchRun->id);

            return $searchRun;
        }

        $this->execute($searchRun);

        return $searchRun->fresh();
    }

    /**
     * Execute a search run's Places API calls and persist the result cache.
     * Called inline for the synchronous path, and from the queued job for
     * the multi-combination path.
     */
    public function execute(SearchRun $searchRun): void
    {
        $searchRun->update(['status' => SearchRun::STATUS_RUNNING, 'started_at' => now()]);

        try {
            $filters = $searchRun->filters;
            $keywords = array_filter((array) ($filters['keyword'] ?? []));
            $locations = array_filter((array) ($filters['location'] ?? []));
            $targetCount = $this->targetCount($filters);
            $orderedPlaces = [];

            $bankPlaces = $this->leadBank->matchingPlaces($filters, $searchRun->user_id, $targetCount);

            foreach ($bankPlaces as $place) {
                $orderedPlaces[$place->google_place_id] = $place;
            }

            $places = [];

            if (count($orderedPlaces) < $targetCount) {
                $places = $this->fetchGooglePlacesUntilTarget(
                    $keywords,
                    $locations,
                    $filters,
                    $searchRun->user_id,
                    $orderedPlaces,
                    $targetCount
                );
            }

            $places = $this->postFilter($places, $filters, $searchRun->user_id);

            $neededFromGoogle = max(0, $targetCount - count($orderedPlaces));
            $places = $neededFromGoogle > 0
                ? array_slice($places, 0, $neededFromGoogle, true)
                : [];

            DB::transaction(function () use ($places, $searchRun, &$orderedPlaces, $targetCount) {
                foreach ($places as $mapped) {
                    $place = Place::findOrCreateFromPlacesResult(Arr::except($mapped, ['_search_keyword', '_search_location']));
                    $this->leadBank->remember($place, $mapped);
                    $orderedPlaces[$place->google_place_id] = $place;
                }

                $orderedPlaces = array_slice($orderedPlaces, 0, $targetCount, true);

                $searchRun->places()->sync(collect($orderedPlaces)->pluck('id')->all());
            });

            $creditsSpent = $this->spendGenerationCredits($searchRun, count($orderedPlaces));

            $searchRun->update([
                'status' => SearchRun::STATUS_DONE,
                'results_count' => count($orderedPlaces),
                'credits_spent' => $creditsSpent,
                'finished_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            $searchRun->update([
                'status' => SearchRun::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
                'finished_at' => now(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function generationCreditCost(array $filters): int
    {
        return $this->targetCount($filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function canGenerate(User $user, array $filters): bool
    {
        return $this->ledger->canAfford($user->fresh(), $this->generationCreditCost($filters));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function ensureCanGenerate(User $user, array $filters): void
    {
        $cost = $this->generationCreditCost($filters);

        if ($cost > 0 && ! $this->ledger->canAfford($user->fresh(), $cost)) {
            throw new InsufficientCreditsException($user->fresh(), $cost);
        }
    }

    protected function spendGenerationCredits(SearchRun $searchRun, int $resultCount): int
    {
        if ($resultCount <= 0) {
            return 0;
        }

        if ((int) $searchRun->credits_spent > 0) {
            return (int) $searchRun->credits_spent;
        }

        $this->ledger->spend($searchRun->user, $resultCount, 'lead_generation', $searchRun, [
            'results_count' => $resultCount,
            'filters' => $searchRun->filters,
        ]);

        return $resultCount;
    }

    /**
     * Pull Google one page at a time and stop as soon as the already-filtered
     * candidate pool can fill the missing lead count. This keeps quota usage
     * small: bank first, then at most the Google pages needed to complete the
     * requested/default target.
     *
     * @param  array<int, string>  $keywords
     * @param  array<int, string>  $locations
     * @param  array<string, mixed>  $filters
     * @param  array<string, Place>  $existingPlaces
     * @return array<string, array<string, mixed>>
     */
    protected function fetchGooglePlacesUntilTarget(
        array $keywords,
        array $locations,
        array $filters,
        int $userId,
        array $existingPlaces,
        int $targetCount
    ): array {
        $places = [];
        $remaining = max(0, $targetCount - count($existingPlaces));

        if ($remaining === 0) {
            return [];
        }

        foreach ($keywords as $keyword) {
            foreach ($locations as $location) {
                $query = "{$keyword} in {$location}";
                $pageToken = null;

                for ($page = 0; $page < self::MAX_GOOGLE_PAGES; $page++) {
                    $result = $this->client->textSearch($query, pageToken: $pageToken);

                    foreach ($result->places as $rawPlace) {
                        $mapped = PlacesResultMapper::mapTextSearchResult($rawPlace);

                        if (! $mapped['google_place_id']) {
                            continue;
                        }

                        if (isset($existingPlaces[$mapped['google_place_id']]) || isset($places[$mapped['google_place_id']])) {
                            continue;
                        }

                        $mapped['_search_keyword'] = $keyword;
                        $mapped['_search_location'] = $location;

                        $places[$mapped['google_place_id']] = $mapped;
                    }

                    if (count($this->postFilter($places, $filters, $userId)) >= $remaining) {
                        return $places;
                    }

                    if (! $result->hasMorePages()) {
                        break;
                    }

                    $pageToken = $result->nextPageToken;
                    Sleep::for(2)->seconds();
                }
            }
        }

        return $places;
    }

    /**
     * Apply every filter the Places API has no native param for.
     *
     * @param  array<string, array<string, mixed>>  $places  Keyed by google_place_id.
     * @param  array<string, mixed>  $filters
     * @return array<string, array<string, mixed>>
     */
    protected function postFilter(array $places, array $filters, int $userId): array
    {
        if (! empty($filters['has_website'])) {
            $places = array_filter($places, fn ($p) => ! empty($p['website']));
        }

        if (! empty($filters['has_phone'])) {
            $places = array_filter($places, fn ($p) => ! empty($p['phone']));
        }

        if (! empty($filters['min_rating'])) {
            $minRating = (float) $filters['min_rating'];
            $places = array_filter($places, fn ($p) => ($p['rating'] ?? 0) >= $minRating);
        }

        $minReviews = $this->resolveMinReviews($filters);
        if ($minReviews !== null) {
            $places = array_filter($places, fn ($p) => ($p['review_count'] ?? 0) >= $minReviews);
        }

        if (! empty($filters['min_reviews_to'])) {
            $maxReviews = (int) $filters['min_reviews_to'];
            $places = array_filter($places, fn ($p) => ($p['review_count'] ?? 0) <= $maxReviews);
        }

        if (! empty($filters['exclude_keyword'])) {
            $exclude = array_map('mb_strtolower', (array) $filters['exclude_keyword']);
            $places = array_filter($places, function ($p) use ($exclude) {
                $haystack = mb_strtolower(($p['name'] ?? '').' '.($p['google_category'] ?? ''));

                foreach ($exclude as $term) {
                    if ($term !== '' && str_contains($haystack, $term)) {
                        return false;
                    }
                }

                return true;
            });
        }

        if (! empty($filters['category'])) {
            $categories = array_map('mb_strtolower', (array) $filters['category']);
            $places = array_filter($places, function ($p) use ($categories) {
                return in_array(mb_strtolower((string) ($p['google_category'] ?? '')), $categories, true);
            });
        }

        if (! empty($filters['skip_owned'])) {
            $ownedPlaceIds = Lead::query()
                ->forUser($userId)
                ->pluck('place_id')
                ->all();

            if ($ownedPlaceIds) {
                $ownedGooglePlaceIds = Place::query()
                    ->whereIn('id', $ownedPlaceIds)
                    ->pluck('google_place_id')
                    ->all();

                $places = array_filter($places, fn ($p) => ! in_array($p['google_place_id'], $ownedGooglePlaceIds, true));
            }
        }

        // one_per_business: results are already keyed by google_place_id
        // above, which is itself the dedupe — nothing further needed here.

        return $places;
    }

    protected function resolveMinReviews(array $filters): ?int
    {
        $minReviews = $filters['min_reviews'] ?? null;

        if ($minReviews === 'custom') {
            return isset($filters['min_reviews_from']) ? (int) $filters['min_reviews_from'] : null;
        }

        return $minReviews ? (int) $minReviews : null;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function combinationCount(array $filters): int
    {
        $keywords = max(1, count(array_filter((array) ($filters['keyword'] ?? []))));
        $locations = max(1, count(array_filter((array) ($filters['location'] ?? []))));

        return $keywords * $locations;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function targetCount(array $filters): int
    {
        if (empty($filters['requested_count'])) {
            return self::DEFAULT_TARGET_COUNT;
        }

        return min(self::MAX_TARGET_COUNT, max(1, (int) $filters['requested_count']));
    }
}

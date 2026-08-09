<?php

namespace App\Modules\Leads\Services;

use App\Models\User;
use App\Modules\Leads\Jobs\RunPlacesSearchJob;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Search;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Services\GooglePlaces\GooglePlacesClient;
use App\Modules\Leads\Services\GooglePlaces\PlacesResultMapper;
use Illuminate\Support\Facades\DB;
use Throwable;

class SearchService
{
    public function __construct(
        protected GooglePlacesClient $client
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

            $places = [];

            foreach ($keywords as $keyword) {
                foreach ($locations as $location) {
                    $query = "{$keyword} in {$location}";
                    $raw = $this->client->textSearchAllPages($query);

                    foreach ($raw as $rawPlace) {
                        $mapped = PlacesResultMapper::mapTextSearchResult($rawPlace);

                        if (! $mapped['google_place_id']) {
                            continue;
                        }

                        $places[$mapped['google_place_id']] = $mapped;
                    }
                }
            }

            $places = $this->postFilter($places, $filters, $searchRun->user_id);

            DB::transaction(function () use ($places, $searchRun) {
                foreach ($places as $mapped) {
                    $place = Place::findOrCreateFromPlacesResult($mapped);
                    $searchRun->places()->syncWithoutDetaching([$place->id]);
                }
            });

            $searchRun->update([
                'status' => SearchRun::STATUS_DONE,
                'results_count' => count($places),
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
}

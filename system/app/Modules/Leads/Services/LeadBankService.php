<?php

namespace App\Modules\Leads\Services;

use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadBank;
use App\Modules\Leads\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LeadBankService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Place>
     */
    public function matchingPlaces(array $filters, int $userId, ?int $limit = null): Collection
    {
        $keywords = $this->normalizedValues((array) ($filters['keyword'] ?? []));
        $locations = $this->normalizedValues((array) ($filters['location'] ?? []));

        if ($keywords === [] || $locations === []) {
            return collect();
        }

        $query = LeadBank::query()
            ->with('place')
            ->whereHas('place')
            ->where(function (Builder $query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('business_type_normalized', 'like', "%{$keyword}%")
                        ->orWhere('searchable_text_normalized', 'like', "%{$keyword}%");
                }
            })
            ->where(function (Builder $query) use ($locations) {
                foreach ($locations as $location) {
                    $query->orWhere('location_normalized', 'like', "%{$location}%")
                        ->orWhere('location_text_normalized', 'like', "%{$location}%");
                }
            });

        $this->applyFilters($query, $filters, $userId);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query
            ->orderByDesc('review_count')
            ->orderByDesc('rating')
            ->get()
            ->pluck('place')
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * @param  array<string, mixed>  $mapped
     */
    public function remember(Place $place, array $mapped): LeadBank
    {
        $businessType = $mapped['_search_keyword'] ?? $place->google_category;
        $location = $mapped['_search_location'] ?? $place->formatted_address;

        $bank = LeadBank::query()->firstOrNew(['place_id' => $place->id]);

        $searchableText = $this->normalize(implode(' ', array_filter([
            $bank->searchable_text_normalized,
            $businessType,
            $place->name,
            $place->google_category,
            $place->formatted_address,
        ])));

        $locationText = $this->normalize(implode(' ', array_filter([
            $bank->location_text_normalized,
            $location,
            $place->formatted_address,
        ])));

        $bank->fill([
            'google_place_id' => $place->google_place_id,
            'name' => $place->name,
            'formatted_address' => $place->formatted_address,
            'business_type' => $businessType,
            'business_type_normalized' => $this->normalize((string) $businessType),
            'location' => $location,
            'location_normalized' => $this->normalize((string) $location),
            'phone' => $place->phone,
            'website' => $place->website,
            'google_category' => $place->google_category,
            'rating' => $place->rating,
            'review_count' => $place->review_count,
            'searchable_text_normalized' => $searchableText,
            'location_text_normalized' => $locationText,
            'raw_response' => $mapped['raw_response'] ?? $place->raw_response,
            'last_seen_at' => now(),
        ]);
        $bank->save();

        return $bank;
    }

    public function normalize(string $value): string
    {
        $words = preg_split('/\s+/', Str::of($value)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', ' ')->trim()->value()) ?: [];

        return collect($words)
            ->filter()
            ->map(fn (string $word) => Str::singular($word))
            ->implode(' ');
    }

    /**
     * @param  array<int, mixed>  $values
     * @return array<int, string>
     */
    protected function normalizedValues(array $values): array
    {
        return collect($values)
            ->map(fn ($value) => $this->normalize((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function applyFilters(Builder $query, array $filters, int $userId): void
    {
        if (! empty($filters['has_website'])) {
            $query->whereNotNull('website')->where('website', '!=', '');
        }

        if (! empty($filters['has_phone'])) {
            $query->whereNotNull('phone')->where('phone', '!=', '');
        }

        if (! empty($filters['min_rating'])) {
            $query->where('rating', '>=', (float) $filters['min_rating']);
        }

        if (($minReviews = $this->resolveMinReviews($filters)) !== null) {
            $query->where('review_count', '>=', $minReviews);
        }

        if (! empty($filters['min_reviews_to'])) {
            $query->where('review_count', '<=', (int) $filters['min_reviews_to']);
        }

        if (! empty($filters['exclude_keyword'])) {
            foreach ($this->normalizedValues((array) $filters['exclude_keyword']) as $term) {
                $query->where('searchable_text_normalized', 'not like', "%{$term}%");
            }
        }

        if (! empty($filters['category'])) {
            $categories = $this->normalizedValues((array) $filters['category']);
            $query->where(function (Builder $query) use ($categories) {
                foreach ($categories as $category) {
                    $query->orWhere('google_category', 'like', "%{$category}%")
                        ->orWhere('searchable_text_normalized', 'like', "%{$category}%");
                }
            });
        }

        if (! empty($filters['skip_owned'])) {
            $ownedPlaceIds = Lead::query()
                ->forUser($userId)
                ->pluck('place_id')
                ->all();

            if ($ownedPlaceIds) {
                $query->whereNotIn('place_id', $ownedPlaceIds);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function resolveMinReviews(array $filters): ?int
    {
        $minReviews = $filters['min_reviews'] ?? null;

        if ($minReviews === 'custom') {
            return isset($filters['min_reviews_from']) ? (int) $filters['min_reviews_from'] : null;
        }

        return $minReviews ? (int) $minReviews : null;
    }
}

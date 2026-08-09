<?php

namespace App\Modules\Leads\Services\GooglePlaces;

class PlacesResultMapper
{
    /**
     * Map one "place" object from a Places API (New) Text Search response into
     * the attribute shape the `places` table expects.
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function mapTextSearchResult(array $raw): array
    {
        return [
            'google_place_id' => $raw['id'] ?? null,
            'name' => $raw['displayName']['text'] ?? ($raw['displayName'] ?? null),
            'formatted_address' => $raw['formattedAddress'] ?? null,
            'lat' => $raw['location']['latitude'] ?? null,
            'lng' => $raw['location']['longitude'] ?? null,
            'phone' => $raw['nationalPhoneNumber'] ?? null,
            'website' => $raw['websiteUri'] ?? null,
            'google_category' => $raw['primaryType'] ?? ($raw['types'][0] ?? null),
            'rating' => $raw['rating'] ?? null,
            'review_count' => $raw['userRatingCount'] ?? null,
            'raw_response' => $raw,
            'details_fetched_at' => null,
        ];
    }

    /**
     * Map a Place Details response into the subset of `places` columns that
     * enrichment fills in (phone/website, sometimes missing from Text Search).
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function mapDetailsResult(array $raw): array
    {
        return [
            'phone' => $raw['nationalPhoneNumber'] ?? null,
            'website' => $raw['websiteUri'] ?? null,
            'details_fetched_at' => now(),
        ];
    }
}

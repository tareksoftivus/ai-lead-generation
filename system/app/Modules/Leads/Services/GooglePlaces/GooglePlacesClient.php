<?php

namespace App\Modules\Leads\Services\GooglePlaces;

use App\Modules\GoogleMapsSettings\Services\GoogleMapsApiLogService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use RuntimeException;
use Throwable;

class GooglePlacesClient
{
    protected const BASE_URL = 'https://places.googleapis.com/v1';

    protected const TEXT_SEARCH_FIELD_MASK = 'places.id,places.displayName,places.formattedAddress,places.location,'
        .'places.rating,places.userRatingCount,places.websiteUri,places.nationalPhoneNumber,'
        .'places.primaryType,places.types,nextPageToken';

    protected const DETAILS_FIELD_MASK = 'id,websiteUri,nationalPhoneNumber';

    /**
     * Miles-to-meters conversion, capped at the Places API's max radius.
     */
    protected const MAX_RADIUS_METERS = 50000;

    public function __construct(
        protected GoogleMapsApiLogService $apiLog
    ) {}

    protected function ensureConfigured(): void
    {
        if (! google_maps_setting('google_maps_enrichment_enabled', true)) {
            throw new RuntimeException('Google Places search is disabled. Enable it in Settings → Google Maps API.');
        }

        if (empty(google_maps_setting('google_maps_api_key', ''))) {
            throw new RuntimeException('Google Maps API key is not configured. Set it in Settings → Google Maps API.');
        }
    }

    /**
     * @param  array{lat: float, lng: float, radiusMiles: float}|null  $locationBias
     */
    public function textSearch(string $query, ?array $locationBias = null, ?string $pageToken = null): PlacesSearchResult
    {
        $this->ensureConfigured();

        $payload = ['textQuery' => $query];

        if ($pageToken) {
            $payload['pageToken'] = $pageToken;
        } elseif ($locationBias) {
            $payload['locationBias'] = [
                'circle' => [
                    'center' => ['latitude' => $locationBias['lat'], 'longitude' => $locationBias['lng']],
                    'radius' => min($locationBias['radiusMiles'] * 1609.34, self::MAX_RADIUS_METERS),
                ],
            ];
        }

        $url = self::BASE_URL.'/places:searchText';

        $response = $this->request('text_search', 'POST', $url, $payload, fn () => Http::withHeaders([
            'X-Goog-Api-Key' => google_maps_setting('google_maps_api_key'),
            'X-Goog-FieldMask' => self::TEXT_SEARCH_FIELD_MASK,
        ])->post($url, $payload));

        $body = $response->json() ?? [];

        return new PlacesSearchResult(
            places: $body['places'] ?? [],
            nextPageToken: $body['nextPageToken'] ?? null,
        );
    }

    /**
     * Fetch every page of a text search up to Google's practical cap
     * (~60 results / 3 pages), honoring the ~2s delay required before a
     * next_page_token becomes valid.
     *
     * @param  array{lat: float, lng: float, radiusMiles: float}|null  $locationBias
     * @return array<int, array<string, mixed>>
     */
    public function textSearchAllPages(string $query, ?array $locationBias = null, int $maxPages = 3): array
    {
        $places = [];
        $pageToken = null;

        for ($page = 0; $page < $maxPages; $page++) {
            $result = $this->textSearch($query, $locationBias, $pageToken);
            $places = array_merge($places, $result->places);

            if (! $result->hasMorePages()) {
                break;
            }

            $pageToken = $result->nextPageToken;
            Sleep::for(2)->seconds();
        }

        return $places;
    }

    /**
     * @return array<string, mixed>
     */
    public function placeDetails(string $placeId): array
    {
        $this->ensureConfigured();

        $url = self::BASE_URL.'/places/'.$placeId;

        $response = $this->request('place_details', 'GET', $url, ['place_id' => $placeId], fn () => Http::withHeaders([
            'X-Goog-Api-Key' => google_maps_setting('google_maps_api_key'),
            'X-Goog-FieldMask' => self::DETAILS_FIELD_MASK,
        ])->get($url));

        return $response->json() ?? [];
    }

    /**
     * Run an HTTP call with a single retry on failure/5xx, matching the
     * synchronous search path's error budget (the queued job path gets its
     * own native queue retries instead).
     */
    protected function request(string $action, string $method, string $url, ?array $payload, callable $call): Response
    {
        $startedAt = microtime(true);
        $attempts = 1;
        $response = null;

        try {
            $response = $call();

            if ($response->serverError()) {
                Sleep::for(1)->second();
                $attempts++;
                $response = $call();
            }

            $response->throw();
            $this->logRequest($action, $method, $url, $payload, $response, true, $attempts, $startedAt);

            return $response;
        } catch (Throwable $exception) {
            $this->logRequest($action, $method, $url, $payload, $response, false, $attempts, $startedAt, $exception);
            report($exception);

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    protected function logRequest(
        string $action,
        string $method,
        string $url,
        ?array $payload,
        ?Response $response,
        bool $successful,
        int $attempts,
        float $startedAt,
        ?Throwable $exception = null
    ): void {
        $this->apiLog->record([
            'action' => $action,
            'method' => $method,
            'url' => $url,
            'request_payload' => $payload,
            'status_code' => $response?->status(),
            'successful' => $successful,
            'attempts' => $attempts,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'response_body' => $response?->json(),
            'error_message' => $exception?->getMessage(),
        ]);
    }
}

<?php

use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Services\GooglePlaces\GooglePlacesClient;
use App\Modules\Leads\Services\GooglePlaces\PlacesResultMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function configureGoogleMapsKey(string $key = 'test-key'): void
{
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', $key);
}

function fakeTextSearchResponse(array $places = [], ?string $nextPageToken = null): array
{
    return array_filter([
        'places' => $places,
        'nextPageToken' => $nextPageToken,
    ], fn ($value) => $value !== null);
}

function fakePlace(string $id = 'ChIJ-test-1'): array
{
    return [
        'id' => $id,
        'displayName' => ['text' => 'Barton Springs Dental'],
        'formattedAddress' => '1401 S Lamar Blvd, Austin, TX',
        'location' => ['latitude' => 30.2540, 'longitude' => -97.7660],
        'rating' => 4.7,
        'userRatingCount' => 312,
        'websiteUri' => 'https://bartonspringsdental.com',
        'nationalPhoneNumber' => '(512) 555-0143',
        'primaryType' => 'dentist',
        'types' => ['dentist', 'health'],
    ];
}

it('throws when the Google Maps API key is not configured', function () {
    $client = app(GooglePlacesClient::class);

    expect(fn () => $client->textSearch('dentists in Austin, TX'))
        ->toThrow(RuntimeException::class, 'API key is not configured');
});

it('throws when search is disabled even with a key configured', function () {
    configureGoogleMapsKey();
    app(GoogleMapsSettingsService::class)->set('google_maps_enrichment_enabled', false);

    $client = app(GooglePlacesClient::class);

    expect(fn () => $client->textSearch('dentists in Austin, TX'))
        ->toThrow(RuntimeException::class, 'is disabled');
});

it('maps a text search response into place records', function () {
    configureGoogleMapsKey();

    Http::fake([
        'places.googleapis.com/*' => Http::response(fakeTextSearchResponse([fakePlace()])),
    ]);

    $client = app(GooglePlacesClient::class);
    $result = $client->textSearch('dentists in Austin, TX', ['lat' => 30.27, 'lng' => -97.74, 'radiusMiles' => 10]);

    expect($result->places)->toHaveCount(1)
        ->and($result->hasMorePages())->toBeFalse();

    $mapped = PlacesResultMapper::mapTextSearchResult($result->places[0]);
    $place = Place::findOrCreateFromPlacesResult($mapped);

    expect($place->google_place_id)->toBe('ChIJ-test-1')
        ->and($place->name)->toBe('Barton Springs Dental')
        ->and($place->lat)->toBe(30.254)
        ->and($place->lng)->toBe(-97.766)
        ->and($place->rating)->toBe(4.7)
        ->and($place->review_count)->toBe(312)
        ->and($place->website)->toBe('https://bartonspringsdental.com')
        ->and($place->google_category)->toBe('dentist');

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(), 'places:searchText')
            && $request->method() === 'POST'
            && $request->hasHeader('X-Goog-Api-Key', 'test-key');
    });
});

it('paginates text search using the next page token, sleeping between pages', function () {
    configureGoogleMapsKey();
    Sleep::fake();

    Http::fake([
        'places.googleapis.com/*' => Http::sequence()
            ->push(fakeTextSearchResponse([fakePlace('p1')], 'token-page-2'))
            ->push(fakeTextSearchResponse([fakePlace('p2')], 'token-page-3'))
            ->push(fakeTextSearchResponse([fakePlace('p3')])),
    ]);

    $client = app(GooglePlacesClient::class);
    $places = $client->textSearchAllPages('dentists in Austin, TX', ['lat' => 30.27, 'lng' => -97.74, 'radiusMiles' => 10]);

    expect($places)->toHaveCount(3);

    Http::assertSentCount(3);
    Sleep::assertSequence([
        Sleep::for(2)->seconds(),
        Sleep::for(2)->seconds(),
    ]);
});

it('stops paginating once a page has no next_page_token', function () {
    configureGoogleMapsKey();
    Sleep::fake();

    Http::fake([
        'places.googleapis.com/*' => Http::response(fakeTextSearchResponse([fakePlace()])),
    ]);

    $client = app(GooglePlacesClient::class);
    $places = $client->textSearchAllPages('dentists in Austin, TX');

    expect($places)->toHaveCount(1);
    Http::assertSentCount(1);
});

it('retries once on a 5xx response and succeeds on the retry', function () {
    configureGoogleMapsKey();
    Sleep::fake();

    Http::fake([
        'places.googleapis.com/*' => Http::sequence()
            ->push(['error' => 'server error'], 503)
            ->push(fakeTextSearchResponse([fakePlace()])),
    ]);

    $client = app(GooglePlacesClient::class);
    $result = $client->textSearch('dentists in Austin, TX');

    expect($result->places)->toHaveCount(1);
    Http::assertSentCount(2);
});

it('fails gracefully after a retry still returns a server error', function () {
    configureGoogleMapsKey();
    Sleep::fake();

    Http::fake([
        'places.googleapis.com/*' => Http::response(['error' => 'server error'], 503),
    ]);

    $client = app(GooglePlacesClient::class);

    expect(fn () => $client->textSearch('dentists in Austin, TX'))
        ->toThrow(RequestException::class);

    Http::assertSentCount(2);
});

it('fetches place details for enrichment', function () {
    configureGoogleMapsKey();

    Http::fake([
        'places.googleapis.com/v1/places/ChIJ-test-1' => Http::response([
            'id' => 'ChIJ-test-1',
            'websiteUri' => 'https://bartonspringsdental.com',
            'nationalPhoneNumber' => '(512) 555-0143',
        ]),
    ]);

    $client = app(GooglePlacesClient::class);
    $details = $client->placeDetails('ChIJ-test-1');

    $mapped = PlacesResultMapper::mapDetailsResult($details);

    expect($mapped['website'])->toBe('https://bartonspringsdental.com')
        ->and($mapped['phone'])->toBe('(512) 555-0143')
        ->and($mapped['details_fetched_at'])->not->toBeNull();
});

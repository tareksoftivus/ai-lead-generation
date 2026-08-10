<?php

use App\Models\User;
use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use App\Modules\Leads\Jobs\RunPlacesSearchJob;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadBank;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Services\LeadBankService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function searchUser(array $attributes = []): User
{
    foreach (['leads.search', 'leads.view', 'leads.manage'] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user = User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));

    $user->givePermissionTo('leads.search', 'leads.view', 'leads.manage');

    return $user;
}

function fakeSearchPlace(string $id, string $name = 'Barton Springs Dental'): array
{
    return [
        'id' => $id,
        'displayName' => ['text' => $name],
        'formattedAddress' => '1401 S Lamar Blvd, Austin, TX',
        'location' => ['latitude' => 30.2540, 'longitude' => -97.7660],
        'rating' => 4.7,
        'userRatingCount' => 312,
        'websiteUri' => 'https://example.com',
        'nationalPhoneNumber' => '(512) 555-0143',
        'primaryType' => 'dentist',
        'types' => ['dentist'],
    ];
}

it('runs a synchronous search and spends one credit per generated lead', function () {
    $user = searchUser(['credits_balance' => 100]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');

    Http::fake([
        'places.googleapis.com/*' => Http::response(['places' => [fakeSearchPlace('p1')]]),
    ]);

    $response = $this->actingAs($user)->post(route('user.search.run'), [
        'keyword' => ['dentists'],
        'location' => ['Austin, TX'],
        'radius' => 10,
    ]);

    $response->assertSuccessful()
        ->assertSee('Barton Springs Dental');

    $searchRun = SearchRun::first();

    expect($searchRun->status)->toBe('done')
        ->and($searchRun->results_count)->toBe(1)
        ->and($searchRun->credits_spent)->toBe(1)
        ->and($user->fresh()->credits_balance)->toBe(99);
});

it('returns matching leads from the leads bank without calling Google', function () {
    $user = searchUser(['credits_balance' => 100]);

    $place = Place::query()->create([
        'google_place_id' => 'bank-dhaka-1',
        'name' => 'Dhaka Dental Studio',
        'formatted_address' => 'Gulshan, Dhaka',
        'rating' => 4.6,
        'review_count' => 24,
        'phone' => '+880 1700-000000',
        'google_category' => 'dentist',
    ]);

    app(LeadBankService::class)->remember($place, [
        '_search_keyword' => 'dentists',
        '_search_location' => 'Dhaka',
    ]);

    Http::fake();

    $response = $this->actingAs($user)->post(route('user.search.run'), [
        'keyword' => ['dentists'],
        'location' => ['Dhaka'],
        'min_reviews' => 'custom',
        'min_reviews_from' => 15,
        'requested_count' => 1,
    ]);

    $response->assertSuccessful()
        ->assertSee('Dhaka Dental Studio');

    Http::assertNothingSent();

    $searchRun = SearchRun::first();
    expect($searchRun->results_count)->toBe(1)
        ->and($searchRun->credits_spent)->toBe(1)
        ->and($searchRun->places)->toHaveCount(1)
        ->and($user->fresh()->credits_balance)->toBe(99);
});

it('uses Google for the missing count when the leads bank only partially matches', function () {
    $user = searchUser(['credits_balance' => 100]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');

    $place = Place::query()->create([
        'google_place_id' => 'bank-dhaka-1',
        'name' => 'Dhaka Dental Studio',
        'formatted_address' => 'Gulshan, Dhaka',
        'rating' => 4.6,
        'review_count' => 24,
        'google_category' => 'dentist',
    ]);

    app(LeadBankService::class)->remember($place, [
        '_search_keyword' => 'dentists',
        '_search_location' => 'Dhaka',
    ]);

    Http::fake([
        'places.googleapis.com/*' => Http::response(['places' => [
            fakeSearchPlace('google-dhaka-2', 'Banani Dental Care'),
        ]]),
    ]);

    $response = $this->actingAs($user)->post(route('user.search.run'), [
        'keyword' => ['dentists'],
        'location' => ['Dhaka'],
        'min_reviews' => 'custom',
        'min_reviews_from' => 15,
        'requested_count' => 2,
    ]);

    $response->assertSuccessful()
        ->assertSee('Dhaka Dental Studio')
        ->assertSee('Banani Dental Care');

    $searchRun = SearchRun::first();
    expect($searchRun->results_count)->toBe(2)
        ->and($searchRun->places)->toHaveCount(2)
        ->and($searchRun->credits_spent)->toBe(2)
        ->and(LeadBank::query()->count())->toBe(2)
        ->and($user->fresh()->credits_balance)->toBe(98);
});

it('only persists the requested number of generated leads for a prompt count', function () {
    $user = searchUser(['credits_balance' => 100]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');

    Http::fake([
        'places.googleapis.com/*' => Http::response(['places' => [
            fakeSearchPlace('google-dhaka-1', 'Dhaka Dental One'),
            fakeSearchPlace('google-dhaka-2', 'Dhaka Dental Two'),
            fakeSearchPlace('google-dhaka-3', 'Dhaka Dental Three'),
        ]]),
    ]);

    $response = $this->actingAs($user)->post(route('user.search.run'), [
        'prompt' => 'Find the dentists in Dhaka with 15 reviews and 2 leads',
    ]);

    $response->assertSuccessful()
        ->assertSee('Dhaka Dental One')
        ->assertSee('Dhaka Dental Two')
        ->assertDontSee('Dhaka Dental Three');

    $searchRun = SearchRun::first();
    expect($searchRun->results_count)->toBe(2)
        ->and($searchRun->places)->toHaveCount(2)
        ->and($searchRun->credits_spent)->toBe(2)
        ->and(LeadBank::query()->count())->toBe(2)
        ->and($user->fresh()->credits_balance)->toBe(98);
});

it('defaults generation to five leads when no count is requested', function () {
    $user = searchUser(['credits_balance' => 100]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');

    Http::fake([
        'places.googleapis.com/*' => Http::response(['places' => [
            fakeSearchPlace('google-default-1', 'Default Dental One'),
            fakeSearchPlace('google-default-2', 'Default Dental Two'),
            fakeSearchPlace('google-default-3', 'Default Dental Three'),
            fakeSearchPlace('google-default-4', 'Default Dental Four'),
            fakeSearchPlace('google-default-5', 'Default Dental Five'),
            fakeSearchPlace('google-default-6', 'Default Dental Six'),
        ]]),
    ]);

    $response = $this->actingAs($user)->post(route('user.search.run'), [
        'prompt' => 'Find the dentists in Dhaka with 15 reviews',
    ]);

    $response->assertSuccessful()
        ->assertSee('Default Dental One')
        ->assertSee('Default Dental Five')
        ->assertDontSee('Default Dental Six');

    $searchRun = SearchRun::first();
    expect($searchRun->results_count)->toBe(5)
        ->and($searchRun->places)->toHaveCount(5)
        ->and($searchRun->credits_spent)->toBe(5)
        ->and(LeadBank::query()->count())->toBe(5)
        ->and($user->fresh()->credits_balance)->toBe(95);
});

it('blocks generation before touching Google when credits are insufficient', function () {
    $user = searchUser(['credits_balance' => 4]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');

    Http::fake();

    $this->actingAs($user)->post(route('user.search.run'), [
        'prompt' => 'Find dentists in Dhaka',
    ])->assertSessionHas('error', 'You dont have sufficient credits please upgrade your plan');

    Http::assertNothingSent();

    expect(SearchRun::query()->count())->toBe(0)
        ->and($user->fresh()->credits_balance)->toBe(4);
});

it('parses a natural language prompt into filters before searching the leads bank', function () {
    $user = searchUser(['credits_balance' => 100]);

    $place = Place::query()->create([
        'google_place_id' => 'bank-dhaka-1',
        'name' => 'Dhaka Dental Studio',
        'formatted_address' => 'Gulshan, Dhaka',
        'rating' => 4.6,
        'review_count' => 24,
        'google_category' => 'dentist',
    ]);

    app(LeadBankService::class)->remember($place, [
        '_search_keyword' => 'dentists',
        '_search_location' => 'Dhaka',
    ]);

    Http::fake();

    $response = $this->actingAs($user)->post(route('user.search.run'), [
        'prompt' => 'Find the dentists in Dhaka with 15 reviews atleat 1 leads',
    ]);

    $response->assertSuccessful()
        ->assertSee('Dhaka Dental Studio');

    Http::assertNothingSent();

    $searchRun = SearchRun::first();
    expect($searchRun->filters['keyword'])->toBe(['dentists'])
        ->and($searchRun->filters['location'])->toBe(['Dhaka'])
        ->and($searchRun->filters['min_reviews'])->toBe('custom')
        ->and($searchRun->filters['min_reviews_from'])->toBe(15)
        ->and($searchRun->filters['requested_count'])->toBe(1);
});

it('returns a live cost estimate without calling the Places API', function () {
    $user = searchUser(['credits_balance' => 500]);

    Http::fake();

    $response = $this->actingAs($user)->postJson(route('user.search.estimate'), [
        'keyword' => ['dentists'],
        'location' => ['Austin, TX'],
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['count', 'cost', 'credits_left']);

    Http::assertNothingSent();
});

it('estimates five leads by default when no count is requested', function () {
    $user = searchUser(['credits_balance' => 500]);

    Http::fake();

    $response = $this->actingAs($user)->postJson(route('user.search.estimate'), [
        'keyword' => ['dentists'],
        'location' => ['Austin, TX'],
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'count' => 5,
            'cost' => 5,
            'credits_left' => 495,
        ]);

    Http::assertNothingSent();
});

it('queues the search job when multiple keyword-location combinations are selected', function () {
    $user = searchUser();
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    Queue::fake();

    $this->actingAs($user)->post(route('user.search.run'), [
        'keyword' => ['dentists', 'orthodontists'],
        'location' => ['Austin, TX'],
    ])->assertRedirect(route('user.search.history'));

    Queue::assertPushed(RunPlacesSearchJob::class);

    expect(SearchRun::first()->status)->toBe('running');
});

it('deletes a search run without deleting leads already saved from it', function () {
    $user = searchUser();

    $searchRun = SearchRun::query()->create([
        'user_id' => $user->id,
        'filters' => ['keyword' => ['dentists'], 'location' => ['Austin, TX']],
        'status' => 'done',
    ]);

    $place = Place::query()->create([
        'google_place_id' => 'p1',
        'name' => 'Barton Springs Dental',
    ]);

    $lead = Lead::query()->create([
        'user_id' => $user->id,
        'place_id' => $place->id,
        'search_run_id' => $searchRun->id,
    ]);

    $this->actingAs($user)
        ->delete(route('user.search.destroy', $searchRun))
        ->assertRedirect(route('user.search.history'));

    expect(SearchRun::query()->count())->toBe(0)
        ->and(Lead::query()->count())->toBe(1)
        ->and($lead->fresh()->search_run_id)->toBeNull();
});

it('404s search history results for a run the user does not own', function () {
    $user = searchUser();
    $other = searchUser();

    $searchRun = SearchRun::query()->create([
        'user_id' => $other->id,
        'filters' => ['keyword' => ['dentists'], 'location' => ['Austin, TX']],
        'status' => 'done',
    ]);

    $this->actingAs($user)
        ->get(route('user.search.results', $searchRun))
        ->assertNotFound();
});

it('shows the search history screen scoped to the current user', function () {
    $owner = searchUser();
    $other = searchUser();

    SearchRun::query()->create([
        'user_id' => $owner->id,
        'filters' => ['keyword' => ['dentists'], 'location' => ['Austin, TX']],
        'status' => 'done',
    ]);
    SearchRun::query()->create([
        'user_id' => $other->id,
        'filters' => ['keyword' => ['orthodontists'], 'location' => ['Dallas, TX']],
        'status' => 'done',
    ]);

    $this->actingAs($owner)
        ->get(route('user.search.history'))
        ->assertSuccessful()
        ->assertSee('dentists')
        ->assertDontSee('orthodontists');
});

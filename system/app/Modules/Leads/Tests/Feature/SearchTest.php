<?php

use App\Models\User;
use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use App\Modules\Leads\Jobs\RunPlacesSearchJob;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\SearchRun;
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

it('runs a synchronous search and shows results without spending credits', function () {
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
        ->and($user->fresh()->credits_balance)->toBe(100);
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

<?php

use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\SearchRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function enrichmentUser(array $attributes = []): User
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

function enrichmentPlace(array $attributes = []): Place
{
    return Place::query()->create(array_merge([
        'google_place_id' => 'place-'.uniqid(),
        'name' => 'Barton Springs Dental',
        'website' => 'https://bartonspringsdental.example-site.test',
        'details_fetched_at' => now(),
    ], $attributes));
}

function fakeWebsiteWithEmail(): void
{
    Http::fake([
        '*bartonspringsdental*' => Http::response('<a href="mailto:hello@bartonsprings.com">Email us</a>'),
        'places.googleapis.com/*' => Http::response(['id' => 'x']),
    ]);
}

function fakeWebsiteWithoutEmail(): void
{
    Http::fake([
        '*bartonspringsdental*' => Http::response('<p>No contact info here.</p>'),
        'places.googleapis.com/*' => Http::response(['id' => 'x']),
    ]);
}

it('does not spend credits when generated leads are saved', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)
        ->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]])
        ->assertRedirect(route('user.leads.index'));

    expect($user->fresh()->credits_balance)->toBe(10)
        ->and(CreditTransaction::query()->forUser($user->id)->where('reason', 'enrichment')->count())->toBe(0);

    $lead = Lead::first();
    expect($lead->email)->toBe('hello@bartonsprings.com')
        ->and($lead->enrichment_credit_spent)->toBeFalse();
});

it('does not double charge when the same place is saved twice', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);
    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);

    expect($user->fresh()->credits_balance)->toBe(10)
        ->and(Lead::query()->count())->toBe(1);
});

it('saves every generated place from a search run into the user leads and a search list', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $searchRun = SearchRun::query()->create([
        'user_id' => $user->id,
        'filters' => [
            'prompt' => 'Find the dentists in Dhaka with 15 reviews and atlest 2 leads.',
            'keyword' => ['dentists'],
            'location' => ['Dhaka'],
        ],
        'status' => SearchRun::STATUS_DONE,
        'results_count' => 2,
    ]);

    $places = collect([
        enrichmentPlace(['google_place_id' => 'generated-dhaka-1']),
        enrichmentPlace(['google_place_id' => 'generated-dhaka-2', 'name' => 'Dhaka Dental Care']),
    ]);

    $searchRun->places()->sync($places->pluck('id')->all());

    $this->actingAs($user)
        ->post(route('user.leads.save-from-search'), [
            'search_run_id' => $searchRun->id,
            'save_all' => '1',
        ])
        ->assertRedirect(route('user.leads.index'));

    $list = LeadList::query()->first();

    expect(Lead::query()->forUser($user->id)->count())->toBe(2)
        ->and($list->source)->toBe(LeadList::SOURCE_SEARCH)
        ->and($list->search_run_id)->toBe($searchRun->id)
        ->and($list->leads()->count())->toBe(2);
});

it('saves generated leads even when the user has no credits left after generation', function () {
    $user = enrichmentUser(['credits_balance' => 0]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)
        ->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]])
        ->assertRedirect(route('user.leads.index'));

    expect(Lead::query()->count())->toBe(1)
        ->and($user->fresh()->credits_balance)->toBe(0);
});

it('does not require credits to save every generated lead from a search run', function () {
    $user = enrichmentUser(['credits_balance' => 1]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $searchRun = SearchRun::query()->create([
        'user_id' => $user->id,
        'filters' => [
            'prompt' => 'Find the dentists in Dhaka with 15 reviews and 2 leads.',
            'keyword' => ['dentists'],
            'location' => ['Dhaka'],
            'requested_count' => 2,
        ],
        'status' => SearchRun::STATUS_DONE,
        'results_count' => 2,
    ]);

    $places = collect([
        enrichmentPlace(['google_place_id' => 'generated-dhaka-1']),
        enrichmentPlace(['google_place_id' => 'generated-dhaka-2', 'name' => 'Dhaka Dental Care']),
    ]);

    $searchRun->places()->sync($places->pluck('id')->all());

    $this->actingAs($user)
        ->post(route('user.leads.save-from-search'), [
            'search_run_id' => $searchRun->id,
            'save_all' => '1',
        ])
        ->assertRedirect(route('user.leads.index'));

    expect(Lead::query()->count())->toBe(2)
        ->and(LeadList::query()->count())->toBe(1)
        ->and($user->fresh()->credits_balance)->toBe(1);
});

it('does not charge on save even when no email is found on the business website', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithoutEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);

    $lead = Lead::first();

    expect($user->fresh()->credits_balance)->toBe(10)
        ->and($lead->email)->toBeNull()
        ->and($lead->enrichment_credit_spent)->toBeFalse();

    $activity = LeadActivity::query()->where('lead_id', $lead->id)->where('type', 'contact_found')->first();
    expect($activity->payload['found'])->toBeFalse();
});

it('restores a soft-deleted lead instead of violating the unique constraint when re-saved', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);
    $lead = Lead::first();
    $lead->delete();

    expect(Lead::query()->count())->toBe(0);

    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);

    expect(Lead::query()->count())->toBe(1)
        ->and(Lead::withTrashed()->count())->toBe(1)
        ->and(Lead::first()->id)->toBe($lead->id)
        ->and($user->fresh()->credits_balance)->toBe(10);
});

it('scopes leads index to the current user only', function () {
    $owner = enrichmentUser();
    $other = enrichmentUser();

    $ownedPlace = enrichmentPlace(['google_place_id' => 'p-owner']);
    $otherPlace = enrichmentPlace(['google_place_id' => 'p-other']);

    Lead::query()->create(['user_id' => $owner->id, 'place_id' => $ownedPlace->id]);
    Lead::query()->create(['user_id' => $other->id, 'place_id' => $otherPlace->id]);

    $this->actingAs($owner)
        ->get(route('user.leads.index'))
        ->assertSuccessful()
        ->assertSee($ownedPlace->name);
});

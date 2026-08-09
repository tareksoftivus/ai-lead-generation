<?php

use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\Place;
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

it('spends exactly one credit per newly enriched lead on save to leads', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)
        ->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]])
        ->assertRedirect(route('user.leads.index'));

    expect($user->fresh()->credits_balance)->toBe(9)
        ->and(CreditTransaction::query()->forUser($user->id)->where('reason', 'enrichment')->count())->toBe(1);

    $lead = Lead::first();
    expect($lead->email)->toBe('hello@bartonsprings.com')
        ->and($lead->enrichment_credit_spent)->toBeTrue();
});

it('does not double charge when the same place is saved twice', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);
    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);

    expect($user->fresh()->credits_balance)->toBe(9)
        ->and(Lead::query()->count())->toBe(1);
});

it('skips saving and does not charge when the user has insufficient credits', function () {
    $user = enrichmentUser(['credits_balance' => 0]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)
        ->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]])
        ->assertRedirect(route('user.leads.index'));

    expect(Lead::query()->count())->toBe(0)
        ->and($user->fresh()->credits_balance)->toBe(0);
});

it('charges a credit even when no email is found on the business website', function () {
    $user = enrichmentUser(['credits_balance' => 10]);
    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');
    fakeWebsiteWithoutEmail();

    $place = enrichmentPlace();

    $this->actingAs($user)->post(route('user.leads.save-from-search'), ['place_id' => [$place->id]]);

    $lead = Lead::first();

    expect($user->fresh()->credits_balance)->toBe(9)
        ->and($lead->email)->toBeNull()
        ->and($lead->enrichment_credit_spent)->toBeTrue();

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
        // Charged again since the restored lead's enrichment fields were reset — treated as a fresh save.
        ->and($user->fresh()->credits_balance)->toBe(8);
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

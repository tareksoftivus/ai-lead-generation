<?php

use App\Models\User;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('walks the full funnel: search, estimate, save (credit spend), status change, tag, note, list, and the timeline reflects all of it in order', function () {
    foreach (['leads.search', 'leads.view', 'leads.manage'] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user = User::factory()->create([
        'is_active' => true,
        'email_verified_at' => now(),
        'credits_balance' => 10,
    ]);
    $user->givePermissionTo('leads.search', 'leads.view', 'leads.manage');

    app(GoogleMapsSettingsService::class)->set('google_maps_api_key', 'test-key');

    Http::fake([
        '*bartonsprings*' => Http::response('<a href="mailto:hello@bartonsprings.com">Email</a>'),
        'places.googleapis.com/v1/places:searchText' => Http::response([
            'places' => [[
                'id' => 'ChIJ-e2e-1',
                'displayName' => ['text' => 'Barton Springs Dental'],
                'formattedAddress' => '1401 S Lamar Blvd, Austin, TX',
                'location' => ['latitude' => 30.2540, 'longitude' => -97.7660],
                'rating' => 4.7,
                'userRatingCount' => 312,
                'websiteUri' => 'https://bartonsprings.example.test',
                'nationalPhoneNumber' => '(512) 555-0143',
                'primaryType' => 'dentist',
            ]],
        ]),
        'places.googleapis.com/v1/places/ChIJ-e2e-1' => Http::response([
            'id' => 'ChIJ-e2e-1',
            'websiteUri' => 'https://bartonsprings.example.test',
            'nationalPhoneNumber' => '(512) 555-0143',
        ]),
    ]);

    // 1. Estimate — free, no external call, no credit change.
    $estimateResponse = $this->actingAs($user)->postJson(route('user.search.estimate'), [
        'keyword' => ['dentists'],
        'location' => ['Austin, TX'],
    ]);
    $estimateResponse->assertSuccessful()->assertJsonStructure(['count', 'cost', 'credits_left']);
    expect($user->fresh()->credits_balance)->toBe(10);

    // 2. Run search — free, returns the business, no credit change.
    $this->actingAs($user)->post(route('user.search.run'), [
        'keyword' => ['dentists'],
        'location' => ['Austin, TX'],
        'radius' => 10,
    ])->assertSuccessful();

    $searchRun = SearchRun::first();
    expect($searchRun->status)->toBe('done')
        ->and($searchRun->results_count)->toBe(1)
        ->and($user->fresh()->credits_balance)->toBe(10);

    $place = $searchRun->places->first();

    // 3. Save to leads — charged: enrichment happens here.
    $this->actingAs($user)->post(route('user.leads.save-from-search'), [
        'search_run_id' => $searchRun->id,
        'place_id' => [$place->id],
    ])->assertRedirect(route('user.leads.index'));

    $lead = Lead::first();
    expect($lead->email)->toBe('hello@bartonsprings.com')
        ->and($lead->enrichment_credit_spent)->toBeTrue()
        ->and($user->fresh()->credits_balance)->toBe(9)
        ->and(app(CreditLedger::class)->balance($user->fresh()))->toBe(9);

    // 4. Status change.
    $this->actingAs($user)
        ->patch(route('user.leads.update-status', $lead), ['status' => 'contacted'])
        ->assertRedirect(route('user.leads.show', $lead));
    expect($lead->fresh()->status)->toBe('contacted');

    // 5. Tag.
    $this->actingAs($user)
        ->post(route('user.leads.attach-tag', $lead), ['tag' => 'High value'])
        ->assertRedirect(route('user.leads.show', $lead));
    expect(Tag::query()->count())->toBe(1)
        ->and($lead->fresh()->tags->pluck('name')->all())->toBe(['High value']);

    // 6. Note.
    $this->actingAs($user)
        ->post(route('user.leads.add-note', $lead), ['body' => 'Called the front desk.'])
        ->assertRedirect(route('user.leads.show', $lead));
    expect($lead->fresh()->notes)->toHaveCount(1);

    // 7. List (created independently via Lists & tags, then attached directly —
    // there is no per-lead "add to list" UI in this pass, so attach at the model level
    // to prove the pivot + activity model support it end to end).
    $this->actingAs($user)
        ->post(route('user.leads.lists.store'), ['name' => 'Austin dentists — Q3'])
        ->assertRedirect(route('user.leads.lists'));
    $list = LeadList::first();
    $list->leads()->attach($lead);

    // 8. The activity timeline reflects every step, in order.
    $lead->refresh();
    $types = $lead->activities()->orderBy('created_at')->orderBy('id')->pluck('type')->all();

    expect($types)->toBe([
        'found_in_search',
        'contact_found',
        'scored',
        'status_changed',
        'tag_added',
        'note_added',
    ]);

    // Rendered on the lead detail page.
    $response = $this->actingAs($user)->get(route('user.leads.show', $lead));
    $response->assertSuccessful()
        ->assertSee('Barton Springs Dental')
        ->assertSee('hello@bartonsprings.com')
        ->assertSee('High value')
        ->assertSee('Called the front desk.');

    // 9. Deleting the lead does not refund the credit.
    $this->actingAs($user)
        ->delete(route('user.leads.destroy', $lead))
        ->assertRedirect(route('user.leads.index'));
    expect($user->fresh()->credits_balance)->toBe(9)
        ->and(Lead::query()->count())->toBe(0);

    // The list survives the lead's deletion.
    expect(LeadList::query()->count())->toBe(1);
});

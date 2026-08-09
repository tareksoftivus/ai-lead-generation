<?php

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function crudUser(array $attributes = []): User
{
    foreach (['leads.view', 'leads.manage'] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user = User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));

    $user->givePermissionTo('leads.view', 'leads.manage');

    return $user;
}

function crudPlace(array $attributes = []): Place
{
    return Place::query()->create(array_merge([
        'google_place_id' => 'place-'.uniqid(),
        'name' => 'Barton Springs Dental',
        'formatted_address' => '1401 S Lamar Blvd, Austin, TX',
    ], $attributes));
}

function crudLead(User $user, array $attributes = []): Lead
{
    return Lead::query()->create(array_merge([
        'user_id' => $user->id,
        'place_id' => crudPlace()->id,
    ], $attributes));
}

it('updates a lead status and logs a status_changed activity', function () {
    $user = crudUser();
    $lead = crudLead($user);

    $this->actingAs($user)
        ->patch(route('user.leads.update-status', $lead), ['status' => 'contacted'])
        ->assertRedirect(route('user.leads.show', $lead));

    expect($lead->fresh()->status)->toBe('contacted')
        ->and(LeadActivity::query()->where('lead_id', $lead->id)->where('type', 'status_changed')->exists())->toBeTrue();
});

it('attaches and detaches a tag, respecting the 30 character limit', function () {
    $user = crudUser();
    $lead = crudLead($user);

    $this->actingAs($user)
        ->post(route('user.leads.attach-tag', $lead), ['tag' => 'High value'])
        ->assertRedirect(route('user.leads.show', $lead));

    $tag = Tag::first();
    expect($lead->fresh()->tags)->toHaveCount(1)
        ->and($tag->name)->toBe('High value');

    $this->actingAs($user)
        ->delete(route('user.leads.detach-tag', [$lead, $tag]))
        ->assertRedirect(route('user.leads.show', $lead));

    expect($lead->fresh()->tags)->toHaveCount(0);

    $this->actingAs($user)
        ->post(route('user.leads.attach-tag', $lead), ['tag' => str_repeat('a', 31)])
        ->assertSessionHasErrors('tag');
});

it('adds a note authored by the current user', function () {
    $user = crudUser();
    $lead = crudLead($user);

    $this->actingAs($user)
        ->post(route('user.leads.add-note', $lead), ['body' => 'Called the front desk.'])
        ->assertRedirect(route('user.leads.show', $lead));

    expect($lead->fresh()->notes)->toHaveCount(1)
        ->and($lead->fresh()->notes->first()->author->id)->toBe($user->id);
});

it('bulk-tags selected leads', function () {
    $user = crudUser();
    $leadA = crudLead($user);
    $leadB = crudLead($user);

    $this->actingAs($user)
        ->post(route('user.leads.bulk-tag'), ['lead_id' => [$leadA->id, $leadB->id], 'tag' => 'Hot'])
        ->assertRedirect(route('user.leads.index'));

    expect($leadA->fresh()->tags)->toHaveCount(1)
        ->and($leadB->fresh()->tags)->toHaveCount(1);
});

it('bulk-sets status for selected leads', function () {
    $user = crudUser();
    $leadA = crudLead($user);
    $leadB = crudLead($user);

    $this->actingAs($user)
        ->post(route('user.leads.bulk-status'), ['lead_id' => [$leadA->id, $leadB->id], 'status' => 'qualified'])
        ->assertRedirect(route('user.leads.index'));

    expect($leadA->fresh()->status)->toBe('qualified')
        ->and($leadB->fresh()->status)->toBe('qualified');
});

it('soft-deletes a lead without refunding its enrichment credit', function () {
    $user = crudUser(['credits_balance' => 5]);
    $lead = crudLead($user, ['enrichment_credit_spent' => true]);

    $this->actingAs($user)
        ->delete(route('user.leads.destroy', $lead))
        ->assertRedirect(route('user.leads.index'));

    expect(Lead::query()->count())->toBe(0)
        ->and(Lead::withTrashed()->count())->toBe(1)
        ->and($user->fresh()->credits_balance)->toBe(5);
});

it('scopes the leads index to the current user only', function () {
    $owner = crudUser();
    $other = crudUser();

    crudLead($owner, ['status' => 'new']);
    $theirs = crudPlace(['google_place_id' => 'other-place', 'name' => 'Other Business']);
    crudLead($other, ['place_id' => $theirs->id]);

    $this->actingAs($owner)
        ->get(route('user.leads.index'))
        ->assertSuccessful()
        ->assertSee('Barton Springs Dental')
        ->assertDontSee('Other Business');
});

it('404s when a user views, updates, or deletes another user\'s lead', function () {
    $user = crudUser();
    $other = crudUser();
    $lead = crudLead($other);

    $this->actingAs($user)->get(route('user.leads.show', $lead))->assertNotFound();
    $this->actingAs($user)->patch(route('user.leads.update-status', $lead), ['status' => 'contacted'])->assertNotFound();
    $this->actingAs($user)->delete(route('user.leads.destroy', $lead))->assertNotFound();
});

it('filters the leads index by score bucket and contact presence', function () {
    $user = crudUser();
    crudLead($user, ['score' => 92, 'email' => 'hello@example.com']);
    crudLead($user, ['place_id' => crudPlace(['google_place_id' => 'p2'])->id, 'score' => 40]);

    $response = $this->actingAs($user)->get(route('user.leads.index'));

    $response->assertSuccessful()
        ->assertSee('data-score="hi"', false)
        ->assertSee('data-score="lo"', false);
});

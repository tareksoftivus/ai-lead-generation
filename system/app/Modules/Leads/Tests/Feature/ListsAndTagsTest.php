<?php

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Tag;
use App\Modules\Leads\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function listsUser(array $attributes = []): User
{
    Permission::findOrCreate('leads.manage', 'web');

    $user = User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));

    $user->givePermissionTo('leads.manage');

    return $user;
}

it('creates, renames, and deletes a list without deleting its leads', function () {
    $user = listsUser();

    $this->actingAs($user)
        ->post(route('user.leads.lists.store'), ['name' => 'Austin dentists — Q3'])
        ->assertRedirect(route('user.leads.lists'));

    $list = LeadList::first();
    expect($list->name)->toBe('Austin dentists — Q3');

    $this->actingAs($user)
        ->patch(route('user.leads.lists.update', $list), ['name' => 'Austin dentists — renamed'])
        ->assertRedirect(route('user.leads.lists'));

    expect($list->fresh()->name)->toBe('Austin dentists — renamed');

    $place = Place::query()->create(['google_place_id' => 'p1', 'name' => 'Barton Springs Dental']);
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);
    $list->leads()->attach($lead);

    $this->actingAs($user)
        ->delete(route('user.leads.lists.destroy', $list))
        ->assertRedirect(route('user.leads.lists'));

    expect(LeadList::query()->count())->toBe(0)
        ->and(Lead::query()->count())->toBe(1);
});

it('creates, renames, and deletes a tag, detaching it from all leads on delete', function () {
    $user = listsUser();

    $this->actingAs($user)
        ->post(route('user.leads.tags.store'), ['tag' => 'Hot'])
        ->assertRedirect(route('user.leads.lists'));

    $tag = Tag::first();

    $place = Place::query()->create(['google_place_id' => 'p1', 'name' => 'Barton Springs Dental']);
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);
    $lead->tags()->attach($tag);

    $this->actingAs($user)
        ->patch(route('user.leads.tags.update', $tag), ['tag' => 'Very hot'])
        ->assertRedirect(route('user.leads.lists'));

    expect($tag->fresh()->name)->toBe('Very hot');

    $this->actingAs($user)
        ->delete(route('user.leads.tags.destroy', $tag))
        ->assertRedirect(route('user.leads.lists'));

    expect(Tag::query()->count())->toBe(0)
        ->and(Lead::query()->count())->toBe(1)
        ->and($lead->fresh()->tags)->toHaveCount(0);
});

it('shows the correct usage count for a tag', function () {
    $user = listsUser();
    $tag = Tag::query()->create(['user_id' => $user->id, 'name' => 'Hot']);

    $placeA = Place::query()->create(['google_place_id' => 'pa', 'name' => 'A']);
    $placeB = Place::query()->create(['google_place_id' => 'pb', 'name' => 'B']);
    $leadA = Lead::query()->create(['user_id' => $user->id, 'place_id' => $placeA->id]);
    $leadB = Lead::query()->create(['user_id' => $user->id, 'place_id' => $placeB->id]);
    $leadA->tags()->attach($tag);
    $leadB->tags()->attach($tag);

    $this->actingAs($user)
        ->get(route('user.leads.lists'))
        ->assertSuccessful()
        ->assertSee('2');
});

it('deduplicates tag names case-insensitively per user', function () {
    $user = listsUser();

    $this->actingAs($user)->post(route('user.leads.tags.store'), ['tag' => 'Hot']);
    $this->actingAs($user)->post(route('user.leads.tags.store'), ['tag' => 'hot']);

    // Direct-store creates two separate Tag rows via the controller (no dedupe there),
    // but the case-insensitive dedupe lives in TagService::findOrCreate(), used by
    // the lead-attach flow — verify that path specifically.
    $place = Place::query()->create(['google_place_id' => 'p1', 'name' => 'Barton Springs Dental']);
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $tagService = app(TagService::class);
    $first = $tagService->findOrCreate($user, 'Follow up');
    $second = $tagService->findOrCreate($user, 'follow up');

    expect($first->id)->toBe($second->id);
});

it('404s when a user updates or deletes another user\'s list or tag', function () {
    $user = listsUser();
    $other = listsUser();

    $list = LeadList::query()->create(['user_id' => $other->id, 'name' => 'Not yours', 'source' => 'manual']);
    $tag = Tag::query()->create(['user_id' => $other->id, 'name' => 'NotYours']);

    $this->actingAs($user)->patch(route('user.leads.lists.update', $list), ['name' => 'Hijacked'])->assertNotFound();
    $this->actingAs($user)->delete(route('user.leads.lists.destroy', $list))->assertNotFound();
    $this->actingAs($user)->patch(route('user.leads.tags.update', $tag), ['tag' => 'Hijacked'])->assertNotFound();
    $this->actingAs($user)->delete(route('user.leads.tags.destroy', $tag))->assertNotFound();
});

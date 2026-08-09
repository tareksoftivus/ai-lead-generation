<?php

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\LeadNote;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Search;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function leadsUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));
}

function leadsPlace(array $attributes = []): Place
{
    return Place::query()->create(array_merge([
        'google_place_id' => 'place-'.uniqid(),
        'name' => 'Barton Springs Dental',
        'formatted_address' => '1401 S Lamar Blvd, Austin, TX',
        'lat' => 30.2540,
        'lng' => -97.7660,
        'phone' => '+15125550143',
        'website' => 'https://bartonspringsdental.com',
        'google_category' => 'Dentist',
        'rating' => 4.7,
        'review_count' => 312,
    ], $attributes));
}

it('finds or creates a place by google_place_id, deduping globally', function () {
    $attributes = [
        'google_place_id' => 'ChIJ-abc123',
        'name' => 'Lamar Family Dentistry',
        'formatted_address' => null,
        'lat' => null,
        'lng' => null,
        'phone' => null,
        'website' => null,
        'google_category' => null,
        'rating' => null,
        'review_count' => null,
        'raw_response' => null,
        'details_fetched_at' => null,
    ];

    $first = Place::findOrCreateFromPlacesResult($attributes);
    $second = Place::findOrCreateFromPlacesResult(array_merge($attributes, ['rating' => 4.9]));

    expect($first->id)->toBe($second->id)
        ->and(Place::query()->count())->toBe(1)
        ->and($second->fresh()->rating)->toBe(4.9);
});

it('enforces one lead per (user, place) via the unique index', function () {
    $user = leadsUser();
    $place = leadsPlace();

    Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    expect(fn () => Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]))
        ->toThrow(QueryException::class);
});

it('restores a soft-deleted lead instead of creating a duplicate row', function () {
    $user = leadsUser();
    $place = leadsPlace();

    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);
    $lead->delete();

    expect(Lead::query()->count())->toBe(0)
        ->and(Lead::withTrashed()->count())->toBe(1);

    $restored = Lead::withTrashed()->where(['user_id' => $user->id, 'place_id' => $place->id])->first();
    $restored->restore();

    expect(Lead::query()->count())->toBe(1)
        ->and($restored->id)->toBe($lead->id);
});

it('scopes leads to a single user', function () {
    $owner = leadsUser();
    $other = leadsUser();
    $place = leadsPlace();
    $otherPlace = leadsPlace(['google_place_id' => 'place-other']);

    Lead::query()->create(['user_id' => $owner->id, 'place_id' => $place->id]);
    Lead::query()->create(['user_id' => $other->id, 'place_id' => $otherPlace->id]);

    expect(Lead::query()->forUser($owner->id)->count())->toBe(1);
});

it('buckets scores into hi/mid/lo consistently', function () {
    expect(Lead::scoreBucket(92))->toBe('hi')
        ->and(Lead::scoreBucket(80))->toBe('hi')
        ->and(Lead::scoreBucket(79))->toBe('mid')
        ->and(Lead::scoreBucket(50))->toBe('mid')
        ->and(Lead::scoreBucket(49))->toBe('lo')
        ->and(Lead::scoreBucket(null))->toBe('lo');
});

it('filters leads by score bucket and contact presence', function () {
    $user = leadsUser();

    $hi = Lead::query()->create([
        'user_id' => $user->id,
        'place_id' => leadsPlace(['google_place_id' => 'p1'])->id,
        'score' => 92,
        'email' => 'hello@example.com',
    ]);
    $lo = Lead::query()->create([
        'user_id' => $user->id,
        'place_id' => leadsPlace(['google_place_id' => 'p2'])->id,
        'score' => 40,
    ]);

    expect(Lead::query()->forUser($user->id)->scoreBucket('hi')->pluck('id')->all())->toBe([$hi->id])
        ->and(Lead::query()->forUser($user->id)->hasContact()->pluck('id')->all())->toBe([$hi->id])
        ->and(Lead::query()->forUser($user->id)->scoreBucket('lo')->pluck('id')->all())->toBe([$lo->id]);
});

it('attaches tags, lists, notes, and activities to a lead', function () {
    $user = leadsUser();
    $place = leadsPlace();
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $tag = Tag::query()->create(['user_id' => $user->id, 'name' => 'High value']);
    $lead->tags()->attach($tag);

    $list = LeadList::query()->create(['user_id' => $user->id, 'name' => 'Austin dentists', 'source' => 'manual']);
    $lead->lists()->attach($list);

    LeadNote::query()->create(['lead_id' => $lead->id, 'user_id' => $user->id, 'body' => 'Called the front desk.']);

    LeadActivity::logStatusChanged($lead, 'new', 'contacted', $user);
    LeadActivity::logTagAdded($lead, $tag, $user);

    $lead->refresh();

    expect($lead->tags)->toHaveCount(1)
        ->and($lead->tags->first()->name)->toBe('High value')
        ->and($lead->lists)->toHaveCount(1)
        ->and($lead->notes)->toHaveCount(1)
        ->and($lead->notes->first()->author->id)->toBe($user->id)
        ->and($lead->activities)->toHaveCount(2)
        ->and($lead->activities->pluck('type')->all())->toContain('status_changed', 'tag_added');
});

it('nulls the search_run_id on a lead when its search run is deleted, without deleting the lead', function () {
    $user = leadsUser();
    $place = leadsPlace();

    $searchRun = SearchRun::query()->create([
        'user_id' => $user->id,
        'filters' => ['keyword' => ['dentists'], 'location' => ['Austin, TX']],
        'status' => 'done',
    ]);

    $lead = Lead::query()->create([
        'user_id' => $user->id,
        'place_id' => $place->id,
        'search_run_id' => $searchRun->id,
    ]);

    $searchRun->delete();

    expect(Lead::query()->count())->toBe(1)
        ->and($lead->fresh()->search_run_id)->toBeNull();
});

it('keeps leads in a list when the list is deleted', function () {
    $user = leadsUser();
    $place = leadsPlace();
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $list = LeadList::query()->create(['user_id' => $user->id, 'name' => 'Warm', 'source' => 'manual']);
    $lead->lists()->attach($list);

    $list->delete();

    expect(Lead::query()->count())->toBe(1)
        ->and(DB::table('lead_list_lead')->count())->toBe(0);
});

it('detaches a tag from all leads when the tag is deleted, without deleting the leads', function () {
    $user = leadsUser();
    $place = leadsPlace();
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $tag = Tag::query()->create(['user_id' => $user->id, 'name' => 'Hot']);
    $lead->tags()->attach($tag);

    $tag->delete();

    expect(Lead::query()->count())->toBe(1)
        ->and($lead->fresh()->tags)->toHaveCount(0);
});

it('stores a search filter snapshot independently on each run even when the parent search changes', function () {
    $user = leadsUser();

    $search = Search::query()->create([
        'user_id' => $user->id,
        'name' => 'Austin dentists',
        'filters' => ['keyword' => ['dentists'], 'location' => ['Austin, TX']],
    ]);

    $run = SearchRun::query()->create([
        'user_id' => $user->id,
        'search_id' => $search->id,
        'filters' => $search->filters,
        'status' => 'done',
    ]);

    $search->update(['filters' => ['keyword' => ['orthodontists'], 'location' => ['Austin, TX']]]);

    expect($run->fresh()->filters['keyword'])->toBe(['dentists'])
        ->and($search->fresh()->filters['keyword'])->toBe(['orthodontists']);
});

it('enforces the 30 character tag name cap at the database level', function () {
    // Column is string(30) and MySQL strict mode rejects (rather than silently
    // truncates) an over-length value — this is the hard backstop behind the
    // TagsController request validation rule, which must not exceed 30 either.
    $user = leadsUser();

    expect(fn () => Tag::query()->create(['user_id' => $user->id, 'name' => str_repeat('a', 31)]))
        ->toThrow(QueryException::class);
});

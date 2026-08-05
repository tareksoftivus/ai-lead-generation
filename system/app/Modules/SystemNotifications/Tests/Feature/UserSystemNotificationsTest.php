<?php

use App\Models\User;
use App\Modules\SystemNotifications\Models\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Create a verified, active user.
 */
function notifUser(): User
{
    return User::factory()->create([
        'name' => 'Amara Rivera',
        'email' => 'amara@riveragrowth.co',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
}

it('renders the notifications index under the reference layout', function () {
    $user = notifUser();

    SystemNotification::create([
        'type' => 'search',
        'notifiable_type' => $user->getMorphClass(),
        'notifiable_id' => $user->getKey(),
        'data' => [
            'title' => 'Search “dentists in Austin” finished',
            'body' => '172 businesses enriched, 18 credits returned where no contact was found.',
            'icon' => 'ph-check-circle',
            'type' => 'success',
            'url' => route('user.search.history'),
        ],
    ]);

    SystemNotification::create([
        'type' => 'credits',
        'notifiable_type' => $user->getMorphClass(),
        'notifiable_id' => $user->getKey(),
        'data' => [
            'title' => 'Credit balance below 500',
            'body' => '486 left in the June cycle.',
            'icon' => 'ph-coins',
            'type' => 'success',
            'url' => route('user.credits.index'),
        ],
        'read_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('user.system-notifications.index'))
        ->assertSuccessful()
        ->assertSee('app-sidebar', false)
        ->assertSee('app-content', false)
        ->assertSee('data-list', false)
        ->assertSee('app-tablist', false)
        ->assertSee('data-list-search', false)
        ->assertSee('data-list-filter="kind"', false)
        ->assertSee('notif', false)
        ->assertSee('notif__icon', false)
        ->assertSee('notif__body', false)
        ->assertSee('data-list-key="unread"', false)
        ->assertSee('data-list-key="read"', false)
        ->assertSee('data-kind="search"', false)
        ->assertSee('data-kind="credits"', false)
        ->assertSee(route('user.system-notifications.mark-all-read'))
        ->assertSee(route('user.settings.index'))
        ->assertSee('Search “dentists in Austin” finished')
        ->assertSee('Credit balance below 500');
});

it('shows the empty state on the notifications index when the user has none', function () {
    $user = notifUser();

    $this->actingAs($user)
        ->get(route('user.system-notifications.index'))
        ->assertSuccessful()
        ->assertSee('empty__title', false)
        ->assertSee(route('user.search.new'))
        ->assertDontSee('data-list-table', false);
});

it('marks all notifications as read from the full page', function () {
    $user = notifUser();

    SystemNotification::create([
        'type' => 'search',
        'notifiable_type' => $user->getMorphClass(),
        'notifiable_id' => $user->getKey(),
        'data' => ['title' => 'One', 'body' => 'Body', 'icon' => 'ph-bell', 'type' => 'info'],
    ]);
    SystemNotification::create([
        'type' => 'search',
        'notifiable_type' => $user->getMorphClass(),
        'notifiable_id' => $user->getKey(),
        'data' => ['title' => 'Two', 'body' => 'Body', 'icon' => 'ph-bell', 'type' => 'info'],
    ]);

    $this->actingAs($user)
        ->post(route('user.system-notifications.mark-all-read'))
        ->assertRedirect(route('user.system-notifications.index'));

    expect(SystemNotification::forNotifiable($user)->unread()->count())->toBe(0);
});

it('keeps the mark-all-read endpoint JSON for the bell', function () {
    $user = notifUser();

    SystemNotification::create([
        'type' => 'search',
        'notifiable_type' => $user->getMorphClass(),
        'notifiable_id' => $user->getKey(),
        'data' => ['title' => 'One', 'body' => 'Body', 'icon' => 'ph-bell', 'type' => 'info'],
    ]);

    $this->actingAs($user)
        ->post(route('user.system-notifications.mark-all-read'), [], [
            'Accept' => 'application/json',
        ])
        ->assertJson(['success' => true]);
});

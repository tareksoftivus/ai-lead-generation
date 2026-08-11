<?php

use App\Enums\NotificationTemplateSlug;
use App\Models\Admin;
use App\Models\User;
use App\Modules\NotificationTemplates\Models\NotificationLog;
use App\Modules\NotificationTemplates\Models\NotificationTemplate;
use App\Modules\NotificationTemplates\Notifications\SendAutoNotification;
use App\Modules\SystemNotifications\Models\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Create a verified, active user.
 */
function notifUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'name' => 'Amara Rivera',
        'email' => 'amara-'.uniqid().'@riveragrowth.co',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));
}

function notifAdmin(array $attributes = []): Admin
{
    return Admin::query()->create(array_merge([
        'name' => 'Notification Admin',
        'email' => 'notification-admin-'.uniqid().'@example.com',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));
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

it('marks only the signed-in user notification as read', function () {
    $user = notifUser();
    $otherUser = notifUser();

    $ownedNotification = SystemNotification::create([
        'type' => 'search',
        'notifiable_type' => $user->getMorphClass(),
        'notifiable_id' => $user->getKey(),
        'data' => ['title' => 'Owned', 'body' => 'Body', 'icon' => 'ph-bell', 'type' => 'info'],
    ]);

    $otherNotification = SystemNotification::create([
        'type' => 'search',
        'notifiable_type' => $otherUser->getMorphClass(),
        'notifiable_id' => $otherUser->getKey(),
        'data' => ['title' => 'Other', 'body' => 'Body', 'icon' => 'ph-bell', 'type' => 'info'],
    ]);

    $this->actingAs($user)
        ->post(route('user.system-notifications.mark-read', $ownedNotification), [], [
            'Accept' => 'application/json',
        ])
        ->assertJson(['success' => true]);

    $this->actingAs($user)
        ->post(route('user.system-notifications.mark-read', $otherNotification), [], [
            'Accept' => 'application/json',
        ])
        ->assertNotFound();

    expect($ownedNotification->fresh()->read_at)->not->toBeNull()
        ->and($otherNotification->fresh()->read_at)->toBeNull();
});

it('keeps admin notification read actions scoped to the signed-in admin', function () {
    $admin = notifAdmin();
    $otherAdmin = notifAdmin();

    $ownedNotification = SystemNotification::create([
        'type' => 'announcement',
        'notifiable_type' => $admin->getMorphClass(),
        'notifiable_id' => $admin->getKey(),
        'data' => ['title' => 'Owned admin', 'body' => 'Body', 'icon' => 'ph-bell', 'type' => 'info'],
    ]);

    $otherNotification = SystemNotification::create([
        'type' => 'announcement',
        'notifiable_type' => $otherAdmin->getMorphClass(),
        'notifiable_id' => $otherAdmin->getKey(),
        'data' => ['title' => 'Other admin', 'body' => 'Body', 'icon' => 'ph-bell', 'type' => 'info'],
    ]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.system-notifications.mark-read', $ownedNotification), [], [
            'Accept' => 'application/json',
        ])
        ->assertJson(['success' => true]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.system-notifications.mark-read', $otherNotification), [], [
            'Accept' => 'application/json',
        ])
        ->assertNotFound();

    expect($ownedNotification->fresh()->read_at)->not->toBeNull()
        ->and($otherNotification->fresh()->read_at)->toBeNull();
});

it('creates in-app system notifications from notification templates', function () {
    $user = notifUser();

    NotificationTemplate::query()->create([
        'slug' => NotificationTemplateSlug::WELCOME->value,
        'name' => 'Welcome',
        'channels' => ['in_app'],
        'variables' => ['user_name' => 'Name'],
        'in_app_title' => 'Welcome {{user_name}}',
        'in_app_body' => 'Your account is ready.',
        'is_active' => true,
    ]);

    $user->notify(new SendAutoNotification(NotificationTemplateSlug::WELCOME));

    $notification = SystemNotification::forNotifiable($user)->first();
    $log = NotificationLog::query()
        ->where('template_slug', NotificationTemplateSlug::WELCOME->value)
        ->where('channel', 'in_app')
        ->where('notifiable_type', $user->getMorphClass())
        ->where('notifiable_id', $user->getKey())
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->type)->toBe(NotificationTemplateSlug::WELCOME->value)
        ->and($notification->getTitle())->toBe('Welcome Amara Rivera')
        ->and($log)->not->toBeNull()
        ->and($log->status)->toBe('sent');
});

it('sends admin system announcements to active users in a selected role', function () {
    Permission::findOrCreate('system-notifications.send', 'admin');

    $admin = notifAdmin();
    $admin->givePermissionTo('system-notifications.send');

    $recipientRole = Role::findOrCreate('notification-recipient', 'web');
    $recipient = notifUser();
    $recipient->assignRole($recipientRole);

    $inactiveRecipient = notifUser(['is_active' => false]);
    $inactiveRecipient->assignRole($recipientRole);

    notifUser();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.system-notifications.send'), [
            'title' => 'Role announcement',
            'body' => 'Only active users in the selected role should receive this.',
            'recipient_type' => 'role',
            'role_id' => $recipientRole->id,
        ], [
            'Accept' => 'application/json',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Notification sent to 1 recipients.',
        ]);

    expect(SystemNotification::forNotifiable($recipient)->count())->toBe(1)
        ->and(SystemNotification::forNotifiable($inactiveRecipient)->count())->toBe(0);
});

<?php

use App\Models\Admin;
use App\Models\User;
use App\Modules\Media\Models\Media;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function adminUserAvatarAdmin(): Admin
{
    $role = Role::findOrCreate('super-admin', 'admin');

    foreach (['users.view', 'users.delete'] as $permission) {
        $role->givePermissionTo(Permission::findOrCreate($permission, 'admin'));
    }

    $admin = Admin::query()->create([
        'name' => 'Avatar Admin',
        'email' => 'avatar-admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);

    $admin->assignRole('super-admin');

    return $admin;
}

it('shows media library user avatars in the admin users list', function (): void {
    $media = Media::query()->create([
        'name' => 'avatar',
        'file_name' => 'avatar.jpg',
        'original_name' => 'avatar.jpg',
        'mime_type' => 'image/jpeg',
        'extension' => 'jpg',
        'type' => 'image',
        'size' => 1024,
        'disk' => 'public',
        'path' => 'media/avatars/avatar.jpg',
    ]);

    $user = User::factory()->create([
        'name' => 'Media Avatar User',
        'email' => 'media-avatar@example.com',
        'avatar' => (string) $media->id,
        'is_active' => true,
    ]);

    $this->actingAs(adminUserAvatarAdmin(), 'admin')
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($media->url, false)
        ->assertDontSee('/storage/'.$media->id, false);
});

it('permanently deletes users from the admin panel so the email can register again', function (): void {
    Event::fake();

    $email = 'deleted-user-register-again@example.com';
    $user = User::factory()->create([
        'email' => $email,
        'is_active' => true,
    ]);

    $this->actingAs(adminUserAvatarAdmin(), 'admin')
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    expect(User::withTrashed()->where('email', $email)->exists())->toBeFalse();

    $this->post(route('register'), [
        'name' => 'Deleted User',
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'terms' => 'on',
    ])->assertRedirect(route('user.dashboard'));

    expect(User::query()->where('email', $email)->count())->toBe(1);
});

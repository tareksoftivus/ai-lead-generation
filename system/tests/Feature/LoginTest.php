<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs the user in when remember me is checked', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $response->assertRedirect(route('user.dashboard'));
    $this->assertAuthenticatedAs($user);
    $this->assertNotNull($user->fresh()->getRememberToken());
});

it('logs the user in when remember me is left unchecked', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('user.dashboard'));
    $this->assertAuthenticatedAs($user);
});

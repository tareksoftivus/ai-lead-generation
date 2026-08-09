<?php

use App\Models\Admin;
use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Create an admin with the super-admin role (all permissions via Gate::before).
 */
function googleMapsAdmin(): Admin
{
    $role = Role::findOrCreate('super-admin', 'admin');

    $admin = Admin::query()->create([
        'name' => 'Maps Admin',
        'email' => 'maps-admin@example.com',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $admin->assignRole($role);

    return $admin;
}

it('lets an admin set and retrieve the Google Maps API key', function () {
    $admin = googleMapsAdmin();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.google-maps-settings.update'), [
            'google_maps_api_key' => 'test-key-123',
            'google_maps_enrichment_enabled' => '1',
            'google_maps_daily_search_cap' => 500,
        ])
        ->assertRedirect(route('admin.google-maps-settings.index'));

    $service = app(GoogleMapsSettingsService::class);

    expect($service->get('google_maps_api_key'))->toBe('test-key-123')
        ->and($service->get('google_maps_enrichment_enabled'))->toBeTrue()
        ->and($service->get('google_maps_daily_search_cap'))->toBe(500);
});

it('defaults enrichment to enabled and the key to null when nothing is stored', function () {
    $service = app(GoogleMapsSettingsService::class);

    expect($service->get('google_maps_api_key'))->toBeNull()
        ->and($service->get('google_maps_enrichment_enabled'))->toBeTrue()
        ->and($service->get('google_maps_daily_search_cap'))->toBeNull();
});

it('caches settings and clears the cache on update', function () {
    $admin = googleMapsAdmin();
    $service = app(GoogleMapsSettingsService::class);

    $service->set('google_maps_api_key', 'first-key');
    expect(Cache::has('google_maps_settings_cache'))->toBeFalse();

    // Warm the cache by reading.
    $service->get('google_maps_api_key');
    expect(Cache::has('google_maps_settings_cache'))->toBeTrue();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.google-maps-settings.update'), [
            'google_maps_api_key' => 'second-key',
        ]);

    expect(Cache::has('google_maps_settings_cache'))->toBeFalse()
        ->and($service->get('google_maps_api_key'))->toBe('second-key');
});

it('blocks a non-admin guard from managing Google Maps settings', function () {
    $this->get(route('admin.google-maps-settings.index'))
        ->assertRedirect(route('admin.login'));
});

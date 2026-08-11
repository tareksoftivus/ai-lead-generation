<?php

use App\Models\Admin;
use App\Models\User;
use App\Modules\ApiIntegrations\Models\ApiIntegrationProvider;
use App\Modules\ApiIntegrations\Models\UserIntegrationConnection;
use App\Modules\ApiIntegrations\Services\ApiKeyService;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function apiIntegrationsUser(array $attributes = []): User
{
    Permission::findOrCreate('api-integrations.manage', 'web');

    $user = User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));

    $user->givePermissionTo('api-integrations.manage');

    return $user;
}

function apiIntegrationsAdmin(): Admin
{
    $role = Role::findOrCreate('super-admin', 'admin');

    $admin = Admin::query()->create([
        'name' => 'API Admin',
        'email' => 'api-admin@example.com',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $admin->assignRole($role);

    return $admin;
}

function apiIntegrationsPlace(array $attributes = []): Place
{
    return Place::query()->create(array_merge([
        'google_place_id' => 'api-place-'.uniqid(),
        'name' => 'Barton Springs Dental',
        'formatted_address' => '1401 S Lamar Blvd, Austin, TX',
        'phone' => '+1 512 555 0142',
        'website' => 'https://example.com',
        'google_category' => 'Dentist',
        'rating' => 4.8,
        'review_count' => 312,
    ], $attributes));
}

function apiIntegrationsLead(User $user, array $attributes = []): Lead
{
    return Lead::query()->create(array_merge([
        'user_id' => $user->id,
        'place_id' => apiIntegrationsPlace()->id,
        'status' => Lead::STATUS_NEW,
        'email' => 'dana@example.com',
        'score' => 92,
        'score_signals' => ['High review count'],
    ], $attributes));
}

it('creates and revokes a managed api key', function () {
    $user = apiIntegrationsUser();

    $this->actingAs($user)
        ->post(route('user.api.keys.store'), [
            'key_name' => 'Production',
            'key_scope' => 'read',
        ])
        ->assertRedirect(route('user.api.keys'))
        ->assertSessionHas('new_api_key');

    expect($user->tokens()->count())->toBe(1)
        ->and($user->tokens()->first()->abilities)->toBe([ApiKeyService::ABILITY_READ]);

    $token = $user->tokens()->first();

    $this->actingAs($user)
        ->delete(route('user.api.keys.destroy', $token))
        ->assertRedirect(route('user.api.keys'));

    expect($user->tokens()->count())->toBe(0);
});

it('connects updates and disconnects an integration provider', function () {
    $user = apiIntegrationsUser();
    $provider = ApiIntegrationProvider::query()->where('slug', 'hubspot')->firstOrFail();

    $this->actingAs($user)
        ->post(route('user.api.integrations.store', $provider), [
            'account_name' => 'acme-marketing',
            'sync_new_leads' => '1',
            'minimum_score' => 70,
        ])
        ->assertRedirect(route('user.api.integrations'));

    $connection = UserIntegrationConnection::query()->firstOrFail();

    expect($connection->account_name)->toBe('acme-marketing')
        ->and($connection->status)->toBe(UserIntegrationConnection::STATUS_CONFIGURED)
        ->and($connection->settings['minimum_score'])->toBe(70)
        ->and($connection->settings['sync_new_leads'])->toBeTrue();

    $this->actingAs($user)
        ->put(route('user.api.integrations.update', $connection), [
            'account_name' => 'acme-sales',
            'minimum_score' => 85,
        ])
        ->assertRedirect(route('user.api.integrations'));

    expect($connection->fresh()->account_name)->toBe('acme-sales')
        ->and($connection->fresh()->settings['minimum_score'])->toBe(85)
        ->and($connection->fresh()->settings['sync_new_leads'])->toBeFalse();

    $this->actingAs($user)
        ->delete(route('user.api.integrations.destroy', $connection))
        ->assertRedirect(route('user.api.integrations'));

    expect(UserIntegrationConnection::query()->count())->toBe(0);
});

it('returns authenticated leads through the public api', function () {
    $user = apiIntegrationsUser();
    $lead = apiIntegrationsLead($user);
    apiIntegrationsLead($user, ['score' => 35]);
    apiIntegrationsLead(User::factory()->create(['email_verified_at' => now()]));

    $list = LeadList::query()->create([
        'user_id' => $user->id,
        'name' => 'Austin pipeline',
        'source' => LeadList::SOURCE_MANUAL,
    ]);
    $list->leads()->attach($lead);

    $plainToken = $user->createToken('leadatlas-api:Read', [ApiKeyService::ABILITY_READ])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$plainToken)
        ->getJson('/api/v1/leads?min_score=70&list_id='.$list->id)
        ->assertOk()
        ->assertJsonPath('data.0.id', $lead->id)
        ->assertJsonPath('data.0.name', 'Barton Springs Dental')
        ->assertJsonPath('has_more', false)
        ->assertJsonCount(1, 'data');
});

it('requires a read ability for lead api access', function () {
    $user = apiIntegrationsUser();
    apiIntegrationsLead($user);

    $plainToken = $user->createToken('leadatlas-api:Write', [ApiKeyService::ABILITY_SEARCH_WRITE])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$plainToken)
        ->getJson('/api/v1/leads')
        ->assertForbidden();
});

it('lets admins update provider availability', function () {
    $admin = apiIntegrationsAdmin();
    $provider = ApiIntegrationProvider::query()->where('slug', 'slack')->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.api-integrations.update', $provider), [
            'name' => 'Slack',
            'description' => 'Post high scoring leads into a channel.',
            'category' => 'notification',
            'sort_order' => 12,
        ])
        ->assertRedirect(route('admin.api-integrations.index'));

    expect($provider->fresh()->is_active)->toBeFalse()
        ->and($provider->fresh()->sort_order)->toBe(12)
        ->and($provider->fresh()->description)->toBe('Post high scoring leads into a channel.');
});

it('keeps existing configurations visible when a provider is disabled', function () {
    $user = apiIntegrationsUser();
    $provider = ApiIntegrationProvider::query()->where('slug', 'hubspot')->firstOrFail();

    UserIntegrationConnection::query()->create([
        'user_id' => $user->id,
        'api_integration_provider_id' => $provider->id,
        'account_name' => 'acme-marketing',
        'status' => UserIntegrationConnection::STATUS_CONFIGURED,
        'settings' => ['sync_new_leads' => true, 'minimum_score' => 50],
        'configured_at' => now(),
    ]);

    $provider->update(['is_active' => false]);

    $this->actingAs($user)
        ->get(route('user.api.integrations'))
        ->assertOk()
        ->assertSee('acme-marketing')
        ->assertSee('Unavailable');
});

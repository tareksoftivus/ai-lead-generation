<?php

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function withCsrf(array $payload): array
{
    return array_merge(['_token' => 'test-token'], $payload);
}

function userSettingsUser(array $attributes = []): User
{
    $user = User::factory()->create(array_merge([
        'name' => 'Amara Rivera',
        'email' => 'amara@example.com',
        'is_active' => true,
        'email_verified_at' => now(),
        'credits_balance' => 42,
    ], $attributes));

    return $user;
}

it('renders account settings with real user data instead of sample content', function () {
    $user = userSettingsUser();

    $this->actingAs($user)
        ->get(route('user.settings.index'))
        ->assertSuccessful()
        ->assertSee('Amara Rivera')
        ->assertSee('amara@example.com')
        ->assertSee('Amara Rivera&#039;s workspace', false)
        ->assertDontSee('Rivera Growth Studio')
        ->assertDontSee('Luis Ferrer')
        ->assertDontSee('Priya Raman');

    expect(UserSetting::query()->where('user_id', $user->id)->exists())->toBeTrue();
});

it('updates workspace display settings', function () {
    $user = userSettingsUser();

    $this->actingAs($user)
        ->withSession(['_token' => 'test-token'])
        ->put(route('user.settings.general.update'), withCsrf([
            'workspace_name' => 'Northstar Leads',
            'timezone' => 'Asia/Dhaka',
        ]))
        ->assertRedirect(route('user.settings.index').'#general')
        ->assertSessionHas('success');

    $settings = UserSetting::query()->where('user_id', $user->id)->first();

    expect($settings->workspace_name)->toBe('Northstar Leads')
        ->and($settings->timezone)->toBe('Asia/Dhaka');
});

it('updates search defaults and applies them to the new search page', function () {
    $user = userSettingsUser();
    Permission::findOrCreate('leads.search', 'web');
    $user->givePermissionTo('leads.search');

    $this->actingAs($user)
        ->withSession(['_token' => 'test-token'])
        ->put(route('user.settings.search-defaults.update'), withCsrf([
            'default_location' => 'Dhaka',
            'default_radius' => 25,
            'min_rating' => '4',
            'min_reviews' => 75,
            'skip_no_phone' => '1',
            'skip_closed' => '1',
            'skip_seen' => '1',
        ]))
        ->assertRedirect(route('user.settings.index').'#defaults')
        ->assertSessionHas('success');

    $settings = UserSetting::query()->where('user_id', $user->id)->first();

    expect($settings->mergedSearchDefaults()['default_location'])->toBe('Dhaka')
        ->and($settings->searchFilters()['location'])->toBe(['Dhaka'])
        ->and($settings->searchFilters()['min_reviews'])->toBe('custom')
        ->and($settings->searchFilters()['min_reviews_from'])->toBe(75);

    $this->actingAs($user)
        ->get(route('user.search.new'))
        ->assertSuccessful()
        ->assertSee('value="Dhaka"', false)
        ->assertSee('value="25"', false);
});

it('updates email preferences', function () {
    $user = userSettingsUser();

    $this->actingAs($user)
        ->withSession(['_token' => 'test-token'])
        ->put(route('user.settings.email-preferences.update'), withCsrf([
            'email_weekly' => '1',
            'email_product' => '1',
        ]))
        ->assertRedirect(route('user.settings.index').'#email')
        ->assertSessionHas('success');

    $preferences = UserSetting::query()
        ->where('user_id', $user->id)
        ->first()
        ->mergedEmailPreferences();

    expect($preferences['email_search_done'])->toBeFalse()
        ->and($preferences['email_low_credits'])->toBeFalse()
        ->and($preferences['email_weekly'])->toBeTrue()
        ->and($preferences['email_product'])->toBeTrue();
});

<?php

use App\Modules\Settings\Database\Seeders\SettingSeeder;
use App\Modules\Settings\Services\SettingsService;
use App\Rules\RecaptchaV2Valid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

function setPlugin(string $key, mixed $value): void
{
    app(SettingsService::class)->set($key, $value);
}

function recaptchaPasses(mixed $value): bool
{
    $validator = Validator::make(
        ['g-recaptcha-response' => $value],
        ['g-recaptcha-response' => [new RecaptchaV2Valid]]
    );

    return $validator->passes();
}

it('registers the plugins settings group with the three plugins', function () {
    $groups = config('settings');

    expect($groups)->toHaveKey('plugins')
        ->and($groups['plugins']['settings'])
        ->toHaveKeys([
            'plugin_ga4_enabled',
            'plugin_tawk_enabled',
            'plugin_turnstile_enabled',
        ]);
});

it('seeds plugin settings disabled by default', function () {
    $this->seed(SettingSeeder::class);

    expect((bool) setting('plugin_ga4_enabled'))->toBeFalse()
        ->and((bool) setting('plugin_tawk_enabled'))->toBeFalse()
        ->and((bool) setting('plugin_turnstile_enabled'))->toBeFalse();
});

it('passes recaptcha validation when the plugin is disabled', function () {
    setPlugin('plugin_turnstile_enabled', false);

    expect(recaptchaPasses(''))->toBeTrue()
        ->and(recaptchaPasses(null))->toBeTrue();

    Http::assertNothingSent();
});

it('passes recaptcha validation when enabled but no secret configured', function () {
    setPlugin('plugin_turnstile_enabled', true);
    setPlugin('plugin_turnstile_site_key', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI');
    setPlugin('plugin_turnstile_secret_key', '');

    expect(recaptchaPasses('any-token'))->toBeTrue();

    Http::assertNothingSent();
});

it('fails recaptcha validation when enabled with an empty response', function () {
    setPlugin('plugin_turnstile_enabled', true);
    setPlugin('plugin_turnstile_site_key', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI');
    setPlugin('plugin_turnstile_secret_key', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');

    expect(recaptchaPasses(''))->toBeFalse();
});

it('verifies a valid recaptcha token against the siteverify api', function () {
    setPlugin('plugin_turnstile_enabled', true);
    setPlugin('plugin_turnstile_site_key', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI');
    setPlugin('plugin_turnstile_secret_key', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');

    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
    ]);

    expect(recaptchaPasses('good-token'))->toBeTrue();

    Http::assertSent(fn ($request) => $request['secret'] === '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe' && $request['response'] === 'good-token');
});

it('rejects an invalid recaptcha token', function () {
    setPlugin('plugin_turnstile_enabled', true);
    setPlugin('plugin_turnstile_site_key', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI');
    setPlugin('plugin_turnstile_secret_key', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');

    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']]),
    ]);

    expect(recaptchaPasses('bad-token'))->toBeFalse();
});

it('renders GA4 script only when enabled and configured', function () {
    setPlugin('plugin_ga4_enabled', true);
    setPlugin('plugin_ga4_measurement_id', 'G-ABC123');

    $html = view()->make('components.plugins.head-scripts')->render();

    expect($html)->toContain('G-ABC123')->toContain('googletagmanager.com');
});

it('renders nothing for GA4 when disabled', function () {
    setPlugin('plugin_ga4_enabled', false);

    $html = view()->make('components.plugins.head-scripts')->render();

    expect(trim($html))->not->toContain('googletagmanager.com');
});

@props([
    'providers' => null,
])

@php
    /**
     * Renders a "continue with …" button for each enabled + configured social
     * provider. Mirrors SocialLoginController::isEnabled() so buttons only show
     * for providers that will actually work.
     */
    $providerCatalog = collect([
        ['key' => 'google',   'label' => 'Google',   'icon' => 'ph ph-google-logo',   'iconClass' => 'social-provider-icon--google'],
        ['key' => 'facebook', 'label' => 'Facebook', 'icon' => 'ph ph-facebook-logo', 'iconClass' => 'social-provider-icon--facebook'],
        ['key' => 'github',   'label' => 'GitHub',   'icon' => 'ph ph-github-logo',   'iconClass' => 'social-provider-icon--github'],
    ])->keyBy('key');

    $requestedProviderKeys = collect(
        $providers === null
            ? $providerCatalog->keys()->all()
            : (is_array($providers) ? $providers : explode(',', (string) $providers))
    )->map(fn ($provider) => trim((string) $provider))->filter();

    $visibleProviders = $requestedProviderKeys->map(fn ($provider) => $providerCatalog->get($provider))
        ->filter()
        ->filter(fn ($p) => \App\Support\SocialLoginConfig::configured($p['key']));
@endphp

@if($visibleProviders->isNotEmpty())
    <div class="my-6 flex items-center gap-3">
        <span class="h-px flex-1 bg-neutral-100"></span>
        <span class="text-xs font-medium uppercase tracking-wide text-neutral-400">{{ __('or continue with') }}</span>
        <span class="h-px flex-1 bg-neutral-100"></span>
    </div>

    <div class="flex flex-col gap-3">
        @foreach($visibleProviders as $provider)
            <a href="{{ route('social.redirect', $provider['key']) }}"
               class="inline-flex items-center justify-center gap-2.5 rounded-xl border border-neutral-200 bg-neutral-0 px-4 py-2.5 text-sm font-semibold text-neutral-700 transition-colors duration-150 hover:bg-neutral-50">
                <i class="{{ $provider['icon'] }} {{ $provider['iconClass'] }} text-lg"></i>
                {{ __('Continue with :provider', ['provider' => $provider['label']]) }}
            </a>
        @endforeach
    </div>
@endif

<section class="spy-section">
    <div class="container">
        <div class="unsupported-section">
            <p class="sec-eyebrow unsupported-section__eyebrow">{{ __('Theme Fallback') }}</p>
            <h2 class="mt-2 font-title text-[1.25rem] font-bold text-title">{{ __('This section is not supported by the current theme.') }}</h2>
            <p class="mt-2 text-[0.9375rem] leading-[1.6] text-body">
                {{ __('Section type') }}: <strong>{{ config('frontend-sections.'.$section->type.'.label', $section->type) }}</strong>.
                {{ __('Add a theme-specific renderer or switch to a compatible theme to show this section publicly.') }}
            </p>
        </div>
    </div>
</section>

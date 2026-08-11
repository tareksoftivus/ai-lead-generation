@extends('layouts.guest')

@section('title', __('Create your account'))

@section('aside')
    <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-discover-deep uppercase">
        {{ __('Free to start') }}
    </p>
    <p class="mt-4 font-title text-[1.75rem] leading-[1.15] font-bold tracking-[-0.02em] text-balance text-title xl:text-[2rem]">
        {{ __(':count businesses are already on the map.', ['count' => '128M']) }}
    </p>
    <p class="mt-4 text-[1rem] leading-[1.6]">
        {{ __('Your account opens with :credits credits. Spend them on a real search before you decide anything.', ['credits' => '100']) }}
    </p>

    <ul class="assure">
        <li class="assure__item">
            <i class="ph ph-credit-card" aria-hidden="true"></i>
            {{ __('No card required, and no trial that expires') }}
        </li>
        <li class="assure__item">
            <i class="ph ph-sliders-horizontal" aria-hidden="true"></i>
            {{ __('Tune searches before you spend credits') }}
        </li>
        <li class="assure__item">
            <i class="ph ph-download-simple" aria-hidden="true"></i>
            {{ __('Every lead you export stays yours') }}
        </li>
    </ul>
@endsection

@section('content')
    <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-neutral-0 py-1 pr-3.5 pl-1.5 text-[0.75rem] font-medium text-body">
        <span class="auth__step-dot numeric">1</span>
        {{ __('Takes about a minute') }}
    </p>

    <div class="auth__head">
        <h1 class="auth__title">{{ __('Create your account') }}</h1>
        <p class="auth__sub">
            {{ __(':credits free credits, no card. You are one form away from your first list.', ['credits' => '100']) }}
        </p>
    </div>

    <form class="auth__form" method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="field">
            <label for="reg-name" class="form-label">{{ __('Full name') }}</label>
            <div class="field__control">
                <i class="ph ph-user field__icon" aria-hidden="true"></i>
                <input
                    type="text"
                    id="reg-name"
                    name="name"
                    class="form-input"
                    autocomplete="name"
                    placeholder="{{ __('Alex Morgan') }}"
                    value="{{ old('name') }}"
                    required
                />
            </div>
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="reg-email" class="form-label">{{ __('Work email') }}</label>
            <div class="field__control">
                <i class="ph ph-envelope-simple field__icon" aria-hidden="true"></i>
                <input
                    type="email"
                    id="reg-email"
                    name="email"
                    class="form-input"
                    autocomplete="email"
                    placeholder="alex@agency.com"
                    value="{{ old('email') }}"
                    required
                />
            </div>
            <p class="field__hint">{{ __('We send the login link here. No newsletter unless you ask.') }}</p>
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        @if (setting('require_sms_verification', false))
            <div class="field">
                <label for="reg-phone" class="form-label">{{ __('Phone number') }}</label>
                <div class="field__control">
                    <i class="ph ph-device-mobile field__icon" aria-hidden="true"></i>
                    <input
                        type="tel"
                        id="reg-phone"
                        name="phone"
                        class="form-input"
                        autocomplete="tel"
                        placeholder="+14155550100"
                        value="{{ old('phone') }}"
                        required
                    />
                </div>
                @error('phone')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="field">
            <label for="reg-password" class="form-label">{{ __('Password') }}</label>
            <div class="password-field field__control">
                <i class="ph ph-lock-simple field__icon" aria-hidden="true"></i>
                <input
                    type="password"
                    id="reg-password"
                    name="password"
                    class="form-input"
                    autocomplete="new-password"
                    minlength="8"
                    placeholder="{{ __('At least 8 characters') }}"
                    required
                    aria-describedby="reg-password-hint"
                />
                <button
                    type="button"
                    class="password-field__toggle"
                    data-password-toggle
                    aria-label="{{ __('Show password') }}"
                >
                    <i class="ph ph-eye" aria-hidden="true"></i>
                    <i class="ph ph-eye-slash" aria-hidden="true"></i>
                </button>
            </div>
            <p class="field__hint" id="reg-password-hint">{{ __('At least :count characters.', ['count' => '8']) }}</p>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="reg-password-confirmation" class="form-label">{{ __('Confirm password') }}</label>
            <div class="password-field field__control">
                <i class="ph ph-lock-simple field__icon" aria-hidden="true"></i>
                <input
                    type="password"
                    id="reg-password-confirmation"
                    name="password_confirmation"
                    class="form-input"
                    autocomplete="new-password"
                    minlength="8"
                    placeholder="{{ __('Repeat password') }}"
                    required
                />
                <button
                    type="button"
                    class="password-field__toggle"
                    data-password-toggle
                    aria-label="{{ __('Show password') }}"
                >
                    <i class="ph ph-eye" aria-hidden="true"></i>
                    <i class="ph ph-eye-slash" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <label class="consent">
            <input type="checkbox" name="terms" class="form-check" required />
            <span class="text-[0.875rem] leading-[1.55]">
                {{ __('I agree to the') }}
                <a href="{{ route('frontend.page', 'terms') }}" class="consent__link" target="_blank" rel="noopener">{{ __('terms') }}</a>
                {{ __('and the') }}
                <a href="#" class="consent__link">{{ __('privacy policy') }}</a>.
            </span>
        </label>

        <x-plugins.turnstile />

        <button type="submit" class="btn btn-accent auth__submit">
            <span class="btn__label">
                <span>{{ __('Create account') }}</span>
                <span aria-hidden="true">{{ __('Create account') }}</span>
            </span>
            <i class="ph ph-arrow-right" aria-hidden="true"></i>
        </button>
    </form>

    <x-auth.social-buttons />

    <p class="auth__alt">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="auth__alt-link">{{ __('Sign in') }}</a>
    </p>
@endsection

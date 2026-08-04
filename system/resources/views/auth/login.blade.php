@extends('layouts.guest')

@section('title', __('Sign in'))

@section('aside')
    <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-discover-deep uppercase">
        {{ __('Welcome back') }}
    </p>
    <p class="mt-4 font-title text-[1.75rem] leading-[1.15] font-bold tracking-[-0.02em] text-balance text-title xl:text-[2rem]">
        {{ __('Your searches are where you left them.') }}
    </p>
    <p class="mt-4 text-[1rem] leading-[1.6]">
        {{ __('Every list, score, and drafted email is still on your account — along with whatever credits you had left.') }}
    </p>

    <ul class="assure">
        <li class="assure__item">
            <i class="ph ph-bookmark-simple" aria-hidden="true"></i>
            {{ __('Saved searches ready to run again') }}
        </li>
        <li class="assure__item">
            <i class="ph ph-kanban" aria-hidden="true"></i>
            {{ __('Your pipeline exactly as you left it') }}
        </li>
        <li class="assure__item">
            <i class="ph ph-coins" aria-hidden="true"></i>
            {{ __('Unused credits roll over') }}
        </li>
    </ul>
@endsection

@section('content')
    <div class="auth__head">
        <h1 class="auth__title">{{ __('Sign in') }}</h1>
        <p class="auth__sub">{{ __('Pick up where you left off.') }}</p>
    </div>

    <form class="auth__form" method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="field">
            <label for="login-email" class="form-label">{{ __('Work email') }}</label>
            <div class="field__control">
                <i class="ph ph-envelope-simple field__icon" aria-hidden="true"></i>
                <input
                    type="email"
                    id="login-email"
                    name="email"
                    class="form-input"
                    autocomplete="email"
                    placeholder="{{ __('alex@agency.com') }}"
                    value="{{ old('email') }}"
                    required
                />
            </div>
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="login-password" class="form-label">{{ __('Password') }}</label>
            <div class="password-field field__control">
                <i class="ph ph-lock-simple field__icon" aria-hidden="true"></i>
                <input
                    type="password"
                    id="login-password"
                    name="password"
                    class="form-input"
                    autocomplete="current-password"
                    placeholder="{{ __('Your password') }}"
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
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth__row">
            <label class="consent">
                <input
                    type="checkbox"
                    name="remember"
                    class="form-check"
                    @checked(old('remember', true))
                />
                <span class="text-[0.875rem] leading-[1.55]">{{ __('Keep me signed in') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-[0.875rem] font-medium text-title underline decoration-neutral-300 underline-offset-2 transition-colors hover:decoration-title"
                >
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-plugins.turnstile />

        <button type="submit" class="btn btn-primary auth__submit">
            <span class="btn__label">
                <span>{{ __('Sign in') }}</span>
                <span aria-hidden="true">{{ __('Sign in') }}</span>
            </span>
            <i class="ph ph-arrow-right" aria-hidden="true"></i>
        </button>
    </form>

    <x-auth.social-buttons />

    @if (Route::has('register'))
        <p class="auth__alt">
            {{ __('New to :app?', ['app' => setting('site_name', config('app.name', 'LeadAtlas'))]) }}
            <a href="{{ route('register') }}" class="auth__alt-link">{{ __('Create an account') }}</a>
        </p>
    @endif
@endsection

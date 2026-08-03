@php
    $logoText = $themeVars['logo_text'] ?? 'LeadAtlas';
@endphp

<header class="site-header">
    <div class="container">
        <div class="f-between h-[72px] gap-4">
            <a href="{{ route('home') }}" class="shrink-0">
                <img
                    src="{{ asset('assets/frontend/leadatlas/images/logo-lockup.png') }}"
                    alt="{{ $logoText }}"
                    width="482"
                    height="140"
                    class="site-header__logo h-10 w-auto sm:h-11"
                />
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('Main') }}">
                <a href="{{ route('home') }}" class="nav-link{{ request()->is('/') ? ' is-active' : '' }}">{{ __('Home') }}</a>
                <a href="{{ url('features') }}" class="nav-link{{ request()->is('features') ? ' is-active' : '' }}">{{ __('Features') }}</a>
                <a href="{{ url('pricing') }}" class="nav-link{{ request()->is('pricing') ? ' is-active' : '' }}">{{ __('Pricing') }}</a>
                <a href="{{ url('blog') }}" class="nav-link{{ request()->is('blog') ? ' is-active' : '' }}">{{ __('Blog') }}</a>
                <a href="{{ url('contact') }}" class="nav-link{{ request()->is('contact') ? ' is-active' : '' }}">{{ __('Contact') }}</a>
            </nav>

            <div class="f-start gap-2">
                <a href="{{ route('login') }}" class="site-header__signin btn btn-ghost max-sm:hidden">{{ __('Sign in') }}</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn--collapse">
                    <span class="btn__label">
                        <span>{{ __('Start free') }}</span>
                        <span aria-hidden="true">{{ __('Start free') }}</span>
                    </span>
                    <i class="ph ph-arrow-right"></i>
                </a>

                <button
                    type="button"
                    class="site-header__burger f-center h-10 w-10 rounded-xl lg:hidden"
                    aria-label="{{ __('Open menu') }}"
                    aria-expanded="false"
                    data-nav-open
                >
                    <i class="ph ph-list text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<div class="mobile-nav__backdrop" data-nav-backdrop aria-hidden="true"></div>

<div class="mobile-nav" role="dialog" aria-label="{{ __('Menu') }}">
    <div class="f-between mb-6">
        <span class="font-title font-bold text-title">{{ __('Menu') }}</span>
        <button
            type="button"
            class="f-center h-10 w-10 rounded-xl text-title"
            aria-label="{{ __('Close menu') }}"
            data-nav-close
        >
            <i class="ph ph-x text-xl"></i>
        </button>
    </div>

    <nav class="flex flex-col gap-1" aria-label="{{ __('Mobile') }}">
        <a href="{{ route('home') }}" class="nav-link">{{ __('Home') }}</a>
        <a href="{{ url('features') }}" class="nav-link">{{ __('Features') }}</a>
        <a href="{{ url('pricing') }}" class="nav-link">{{ __('Pricing') }}</a>
        <a href="#faq" class="nav-link">{{ __('FAQ') }}</a>
        <a href="{{ url('contact') }}" class="nav-link">{{ __('Contact') }}</a>
    </nav>

    <div class="mt-6 flex flex-col gap-2">
        <a href="{{ route('login') }}" class="btn btn-outline w-full">{{ __('Sign in') }}</a>
        <a href="{{ route('register') }}" class="btn btn-accent w-full">
            <span class="btn__label">
                <span>{{ __('Start free') }}</span>
                <span aria-hidden="true">{{ __('Start free') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </div>
</div>

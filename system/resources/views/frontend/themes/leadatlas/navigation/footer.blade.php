@php
    $logoText = $themeVars['logo_text'] ?? 'LeadAtlas';
@endphp

<footer class="border-t border-neutral-200 bg-neutral-0">
    <div class="container">
        <div class="flex flex-col gap-8 pt-14 pb-12 lg:flex-row lg:items-end lg:justify-between lg:gap-12 lg:pt-16">
            <a href="{{ route('home') }}" class="ft__mark">
                <img
                    src="{{ asset('assets/frontend/leadatlas/images/logo-lockup.png') }}"
                    alt="{{ $logoText }}"
                    width="482"
                    height="140"
                />
            </a>

            <dl class="grid grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-3 lg:shrink-0 lg:gap-x-12">
                <div class="border-t border-neutral-300 pt-3">
                    <dt class="text-[0.75rem] leading-tight">{{ __('Businesses indexed') }}</dt>
                    <dd class="mt-1.5 text-[1.375rem] leading-none font-bold text-discover-deep numeric">128M</dd>
                </div>
                <div class="border-t border-neutral-300 pt-3">
                    <dt class="text-[0.75rem] leading-tight">{{ __('Countries covered') }}</dt>
                    <dd class="mt-1.5 text-[1.375rem] leading-none font-bold text-discover-deep numeric">190</dd>
                </div>
                <div class="border-t border-neutral-300 pt-3">
                    <dt class="text-[0.75rem] leading-tight">{{ __('Free credits to start') }}</dt>
                    <dd class="mt-1.5 text-[1.375rem] leading-none font-bold text-discover-deep numeric">100</dd>
                </div>
            </dl>
        </div>

        <nav class="grid gap-x-8 gap-y-10 border-t border-neutral-200 py-12 sm:grid-cols-2 lg:grid-cols-4" aria-label="{{ __('Footer') }}">
            <div class="ft__group">
                <h2 class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">{{ __('Product') }}</h2>
                <ul class="mt-5 flex flex-col gap-3">
                    <li><a href="{{ url('features') }}" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Features') }}</a></li>
                    <li><a href="{{ url('pricing') }}" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Pricing') }}</a></li>
                    <li><a href="#" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('API docs') }}</a></li>
                    <li><a href="#" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Integrations') }}</a></li>
                </ul>
            </div>

            <div class="ft__group">
                <h2 class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">{{ __('Company') }}</h2>
                <ul class="mt-5 flex flex-col gap-3">
                    <li><a href="{{ url('blog') }}" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Blog') }}</a></li>
                    <li><a href="{{ url('contact') }}" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Contact') }}</a></li>
                    <li><a href="#" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Help center') }}</a></li>
                </ul>
            </div>

            <div class="ft__group">
                <h2 class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">{{ __('Legal') }}</h2>
                <ul class="mt-5 flex flex-col gap-3">
                    <li><a href="#" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Privacy policy') }}</a></li>
                    <li><a href="#" class="text-[0.9375rem] text-body transition-colors duration-200 hover:text-title">{{ __('Terms & conditions') }}</a></li>
                </ul>
            </div>

            <div class="ft__group ft__group--act">
                <h2 class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">{{ __('Start') }}</h2>
                <p class="ft__pitch">
                    {{ __('One search, :credits free credits, no card.', ['credits' => 100]) }}
                </p>
                <a href="{{ route('register') }}" class="btn btn-accent btn-sm mt-5">
                    <span class="btn__label">
                        <span>{{ __('Start free') }}</span>
                        <span aria-hidden="true">{{ __('Start free') }}</span>
                    </span>
                    <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </nav>

        <div class="flex flex-col gap-4 border-t border-neutral-200 py-7 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[0.8125rem]">
                &copy; <span class="numeric">{{ now()->year }}</span> {{ $logoText }}. {{ __('Business data is sourced from public listings.') }}
            </p>

            <ul class="flex flex-wrap items-center gap-1">
                <li>
                    <a href="#" class="flex h-10 w-10 items-center justify-center rounded-lg border border-neutral-200 text-[1.0625rem] text-body transition-colors duration-200 hover:border-neutral-300 hover:bg-neutral-50 hover:text-title" aria-label="{{ __(':name on X', ['name' => $logoText]) }}">
                        <i class="ph ph-x-logo" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex h-10 w-10 items-center justify-center rounded-lg border border-neutral-200 text-[1.0625rem] text-body transition-colors duration-200 hover:border-neutral-300 hover:bg-neutral-50 hover:text-title" aria-label="{{ __(':name on LinkedIn', ['name' => $logoText]) }}">
                        <i class="ph ph-linkedin-logo" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex h-10 w-10 items-center justify-center rounded-lg border border-neutral-200 text-[1.0625rem] text-body transition-colors duration-200 hover:border-neutral-300 hover:bg-neutral-50 hover:text-title" aria-label="{{ __(':name on YouTube', ['name' => $logoText]) }}">
                        <i class="ph ph-youtube-logo" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</footer>

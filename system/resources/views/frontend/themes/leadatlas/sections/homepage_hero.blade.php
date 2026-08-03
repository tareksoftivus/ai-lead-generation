@php
    $data = $section->data;
@endphp
<section class="relative isolate overflow-hidden bg-neutral-0 pt-[72px]">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <div class="hero__glow"></div>
        <div class="hero__grid"></div>
        <div class="hero__sweep"></div>
    </div>

    <div class="container relative">
        <div class="mx-auto max-w-[44rem] pt-10 text-center md:pt-12 xl:pt-14" data-anim-load>
            <p class="hero__proof">
                <i class="ph-fill ph-map-pin-line"></i>
                <span><span class="numeric">{{ $data['proof_count'] ?? '' }}</span>
                    {{ strtr($data['proof_text'] ?? '', [':countries' => $data['proof_countries'] ?? '']) }}</span>
            </p>

            <h1
                class="mt-4 font-title text-[2.125rem] leading-[1.02] font-bold tracking-[-0.035em] text-balance text-title md:text-[2.75rem] xl:text-[3.25rem]">
                {{ $data['title'] ?? '' }}
                @if(!empty($data['title_secondary']))
                    <span class="block text-body">{{ $data['title_secondary'] }}</span>
                @endif
            </h1>

            @if(!empty($data['subtitle']))
                <p class="mx-auto mt-4 max-w-[46ch] text-[1rem] leading-[1.6] md:text-[1.0625rem]">
                    {{ $data['subtitle'] }}
                </p>
            @endif

            <div class="mt-7 flex flex-wrap items-center justify-center gap-x-6 gap-y-4">
                @if(!empty($data['primary_button_text']))
                    <a href="{{ $data['primary_button_link'] ?: '#' }}" class="btn btn-primary">
                        <span class="btn__label">
                            <span>{{ $data['primary_button_text'] }}</span>
                            <span aria-hidden="true">{{ $data['primary_button_text'] }}</span>
                        </span>
                        <i class="ph ph-arrow-right"></i>
                    </a>
                @endif
                @if(!empty($data['secondary_button_text']))
                    <a href="{{ $data['secondary_button_link'] ?: '#' }}"
                        class="text-[0.9375rem] font-semibold text-title underline decoration-neutral-300 decoration-2 underline-offset-[6px] transition-colors hover:decoration-discover-deep">{{ $data['secondary_button_text'] }}</a>
                @endif
            </div>
        </div>

        <div class="relative mt-12 md:mt-14" data-anim-load>
            <p class="pin pin--find">
                <i class="ph-fill ph-map-pin"></i>
                {{ __('Pulled from Google Maps') }}
            </p>
            <p class="pin pin--ai">
                <i class="ph-fill ph-sparkle"></i>
                {{ __('Scored by AI') }}
            </p>

            <div class="shot__frame">
                <div class="flex items-center gap-1.5 px-3 py-2.5" aria-hidden="true">
                    <span class="h-2 w-2 rounded-full bg-neutral-300"></span>
                    <span class="h-2 w-2 rounded-full bg-neutral-300"></span>
                    <span class="h-2 w-2 rounded-full bg-neutral-300"></span>
                    <span
                        class="mx-auto flex items-center gap-1.5 rounded-md bg-neutral-0 px-3 py-1 text-[0.6875rem] text-neutral-500">
                        <i class="ph ph-lock-simple text-[0.75rem] text-discover-deep"></i>
                        {{ $data['screenshot_caption'] ?? '' }}
                    </span>
                </div>

                <img src="{{ asset('assets/frontend/leadatlas/images/hero-frame.png') }}"
                    alt="{{ __('The LeadAtlas dashboard: two searches running, 1,284 leads found, and the highest-scoring businesses ranked by AI score.') }}"
                    width="1913" height="955" fetchpriority="high" decoding="async" class="shot__img" />

                <div class="lead-card">
                    <p
                        class="mb-2 flex items-center gap-1.5 text-[0.625rem] font-semibold tracking-[0.08em] text-ai-deep uppercase">
                        <i class="ph-fill ph-sparkle"></i>
                        {{ $data['lead_card_label'] ?? '' }}
                    </p>
                    <p class="font-title text-[0.9375rem] font-bold text-title">
                        {{ $data['lead_card_name'] ?? '' }}
                    </p>
                    <p class="mt-1.5 text-[0.75rem] leading-[1.5]">
                        {{ $data['lead_card_summary'] ?? '' }}
                    </p>
                    <div class="mt-3 flex items-center gap-2 border-t border-neutral-100 pt-3">
                        <span
                            class="font-title text-[1.5rem] leading-none font-bold text-ai-deep numeric">{{ $data['lead_card_score'] ?? '' }}</span>
                        <span
                            class="text-[0.625rem] font-semibold tracking-[0.08em] text-neutral-500 uppercase">{{ __('AI score') }}</span>
                        <span class="lead-card__mail">
                            <i class="ph ph-envelope-simple"></i>{{ __('Email found') }}
                        </span>
                    </div>
                </div>

                <div class="shot__fade" aria-hidden="true"></div>
            </div>
        </div>
    </div>
</section>
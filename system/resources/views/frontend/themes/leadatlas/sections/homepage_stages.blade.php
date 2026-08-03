@php
    $data = $section->data;
    $stages = $data['stages'] ?? [];
@endphp
<section class="spy-section bg-primary/5 run" data-anim>
    <div class="container">
        <div class="max-w-[44rem]" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="font-mono text-[0.75rem] font-medium tracking-[0.2em] text-discover-deep uppercase">
                    {{ $data['eyebrow'] }}
                </p>
            @endif
            <h2 class="mt-4 font-title text-[1.875rem] leading-[1.08] font-bold tracking-[-0.03em] text-balance text-title md:text-[2.5rem] xl:text-[2.875rem]">
                {{ $data['title'] ?? '' }}
            </h2>
            @if(!empty($data['subtitle']))
                <p class="mt-4 max-w-[46ch] text-[1rem] leading-[1.6] md:text-[1.0625rem]">
                    {{ $data['subtitle'] }}
                </p>
            @endif
        </div>

        <ol class="mt-12 grid gap-x-8 gap-y-10 md:mt-16 md:grid-cols-2 lg:grid-cols-4 lg:gap-x-10" data-anim-item>
            @foreach($stages as $index => $stage)
                <li class="rstage rstage--{{ $stage['variant'] ?? 'find' }}">
                    <p class="rstage__count numeric">{{ $stage['count'] ?? '' }}</p>
                    <p class="mt-2 font-mono text-[0.8125rem] tracking-[0.08em]">{{ $stage['label'] ?? '' }}</p>
                    <div class="rstage__meter" aria-hidden="true">
                        <span class="block h-full rounded-full rstage__bar--{{ $index + 1 }}"></span>
                    </div>
                    <p class="mt-5 max-w-[34ch] text-[0.875rem] leading-[1.65]">
                        {{ $stage['description'] ?? '' }}
                    </p>
                </li>
            @endforeach
        </ol>

        @if(!empty($data['callout_name']))
            <p class="mt-14 flex flex-col gap-x-3 gap-y-2 border-t border-neutral-200 pt-6 text-[0.9375rem] md:mt-16 md:flex-row md:flex-wrap md:items-baseline" data-anim-item>
                @if(!empty($data['callout_label']))
                    <span class="font-mono text-[0.75rem] tracking-[0.14em] text-accent-dark uppercase">{{ $data['callout_label'] }}</span>
                @endif
                <span class="font-title font-bold text-title">{{ $data['callout_name'] }}</span>
                <span class="text-body">
                    {{ __('scored') }} <span class="numeric">{{ $data['callout_score'] ?? '' }}</span>
                    @if(!empty($data['callout_text']))
                        — {{ $data['callout_text'] }}
                    @endif
                </span>
            </p>
        @endif
    </div>
</section>

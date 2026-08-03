@php
    $data = $section->data;
    $items = $data['items'] ?? [];
@endphp
<section class="spy-section bg-primary/5" data-anim aria-labelledby="voices-heading">
    <div class="container">
        <div class="sec-head sec-head--center" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="sec-eyebrow">{{ $data['eyebrow'] }}</p>
            @endif
            <h2 class="sec-title" id="voices-heading">{{ $data['title'] ?? '' }}</h2>
            @if(!empty($data['subtitle']))
                <p class="sec-lead">{{ $data['subtitle'] }}</p>
            @endif
        </div>
    </div>

    <div class="voices" data-voices-slider>
        <div class="swiper-wrapper">
            @foreach($items as $voice)
                <figure class="voice swiper-slide">
                    <span class="voice__mark" aria-hidden="true"></span>
                    <p class="flex items-center gap-2 text-[0.6875rem] font-semibold tracking-[0.12em] uppercase voice__stage--{{ $voice['variant'] ?? 'find' }}">
                        <i class="ph {{ $voice['icon'] ?? 'ph-quotes' }}"></i>
                        {{ $voice['label'] ?? '' }}
                    </p>
                    <blockquote class="mt-3 mb-6 flex-1 text-[0.9375rem] leading-[1.65] text-title">
                        {{ $voice['quote'] ?? '' }}
                    </blockquote>
                    <figcaption class="mt-6 flex items-center gap-3 border-t border-neutral-100 pt-4">
                        <img src="{{ asset('assets/frontend/leadatlas/images/avatars/'.($voice['avatar'] ?? 'avatar-1.jpg')) }}" alt="" width="80" height="80" loading="lazy" decoding="async"
                            class="size-11 shrink-0 rounded-full bg-neutral-100 object-cover" />
                        <span class="min-w-0">
                            <span class="block truncate text-[0.875rem] font-semibold text-title">{{ $voice['name'] ?? '' }}</span>
                            <span class="block truncate text-[0.8125rem] text-body">{{ $voice['role'] ?? '' }}</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>

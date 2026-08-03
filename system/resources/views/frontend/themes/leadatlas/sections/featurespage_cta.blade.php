@php
    $data = $section->data;
@endphp
<section class="spy-section bg-[#f6f5ff] cta" data-anim>
    <div class="container">
        <div class="cta__inner" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="cta__eyebrow">{{ $data['eyebrow'] }}</p>
            @endif
            <h2 class="cta__title">
                {{ $data['title'] ?? '' }}
            </h2>
            @if(!empty($data['body']))
                <p class="mx-auto mt-5 max-w-[46ch] text-[1rem] leading-[1.6] md:text-[1.0625rem]">
                    {{ $data['body'] }}
                </p>
            @endif
            <a href="{{ $data['button_link'] ?: '#' }}" class="btn btn-accent pricing-cta__btn">
                <span class="btn__label">
                    <span>{{ $data['button_text'] ?? __('Get started') }}</span>
                    <span aria-hidden="true">{{ $data['button_text'] ?? __('Get started') }}</span>
                </span>
                <i class="ph ph-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>

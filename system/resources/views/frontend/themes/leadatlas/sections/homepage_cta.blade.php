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

            <form class="cta__form" action="{{ $data['form_action'] ?: '#' }}" method="get">
                <div class="cta__field">
                    <label for="cta-keyword-{{ $section->id }}"
                        class="font-mono text-[0.625rem] font-medium tracking-[0.14em] text-body uppercase">{{ $data['keyword_label'] ?? __('What you sell to') }}</label>
                    <input type="text" id="cta-keyword-{{ $section->id }}" name="keyword"
                        class="mt-1 w-full border-0 bg-transparent p-0 text-[1rem] font-medium text-title placeholder:text-neutral-400 focus:outline-none"
                        value="{{ $data['keyword_placeholder'] ?? '' }}" placeholder="{{ $data['keyword_placeholder'] ?? '' }}" required />
                </div>

                <div class="cta__field">
                    <label for="cta-location-{{ $section->id }}"
                        class="font-mono text-[0.625rem] font-medium tracking-[0.14em] text-body uppercase">{{ $data['location_label'] ?? __('Where') }}</label>
                    <input type="text" id="cta-location-{{ $section->id }}" name="location"
                        class="mt-1 w-full border-0 bg-transparent p-0 text-[1rem] font-medium text-title placeholder:text-neutral-400 focus:outline-none"
                        value="{{ $data['location_placeholder'] ?? '' }}" placeholder="{{ $data['location_placeholder'] ?? '' }}" required />
                </div>

                <button type="submit"
                    class="btn btn-primary m-1.5 shrink-0 justify-center md:m-0 md:min-h-full md:rounded-none md:rounded-r-[0.875rem] md:px-7">
                    <span class="btn__label">
                        <span>{{ $data['button_text'] ?? __('Search the map') }}</span>
                        <span aria-hidden="true">{{ $data['button_text'] ?? __('Search the map') }}</span>
                    </span>
                    <i class="ph ph-arrow-right"></i>
                </button>
            </form>

            @if(!empty($data['note']))
                <p class="mt-6 text-[0.8125rem]">
                    {{ $data['note'] }}
                </p>
            @endif
        </div>
    </div>
</section>

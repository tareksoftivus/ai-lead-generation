@php
    $data = $section->data;
    $plans = $data['plans'] ?? [];
@endphp
<section class="spy-section" data-anim id="pricing">
    <div class="container">
        <div class="sec-head sec-head--center" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="sec-eyebrow">{{ $data['eyebrow'] }}</p>
            @endif
            <h2 class="sec-title">{{ $data['title'] ?? '' }}</h2>
            @if(!empty($data['subtitle']))
                <p class="sec-lead">{{ $data['subtitle'] }}</p>
            @endif
        </div>

        <div class="@container mt-12 md:mt-16">
            <div class="plans">
                @foreach($plans as $plan)
                    <article class="plan @if(!empty($plan['featured'])) plan--featured @endif" data-anim-item>
                        @if(!empty($plan['featured']) && !empty($plan['badge']))
                            <p class="absolute -top-3 left-6 rounded-full bg-primary px-3 py-1 text-[0.6875rem] font-semibold tracking-[0.06em] text-neutral-0 uppercase">
                                {{ $plan['badge'] }}
                            </p>
                        @endif
                        <p class="plan__name">{{ $plan['name'] ?? '' }}</p>
                        <p class="plan__price">
                            <span class="plan__amount numeric">{{ $plan['price'] ?? '' }}</span>
                            <span class="plan__per">{{ $plan['period'] ?? '' }}</span>
                        </p>
                        @if(!empty($plan['credits']))
                            <p class="plan__credits">
                                <i class="ph-fill ph-coins"></i>
                                <span class="numeric">{{ $plan['credits'] }}</span>
                            </p>
                        @endif
                        <ul class="plan__list">
                            @foreach(array_filter(explode("\n", (string) ($plan['features'] ?? ''))) as $feature)
                                <li class="plan__item"><i class="ph ph-check"></i>{{ trim($feature) }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ $plan['button_link'] ?: '#' }}" class="btn @if(!empty($plan['featured'])) btn-accent @else btn-outline @endif plan__cta">
                            {{ $plan['button_text'] ?? __('Start free') }}
                        </a>
                    </article>
                @endforeach
            </div>
        </div>

        @if(!empty($data['note']))
            <p class="plans__note" data-anim-item>
                {{ $data['note'] }}
                @if(!empty($data['note_link_text']))
                    <a href="{{ $data['note_link'] ?: '#' }}"
                        class="inline-flex items-center gap-1.5 font-semibold text-primary underline decoration-neutral-300 decoration-2 underline-offset-[5px] transition-colors hover:decoration-primary">
                        {{ $data['note_link_text'] }}
                        <i class="ph ph-arrow-right"></i>
                    </a>
                @endif
            </p>
        @endif
    </div>
</section>

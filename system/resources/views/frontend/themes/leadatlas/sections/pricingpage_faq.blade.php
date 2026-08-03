@php
    $data = $section->data;
    $items = $data['items'] ?? [];
@endphp
<section class="spy-section" data-anim>
    <div class="container">
        <div class="sec-head sec-head--center" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="sec-eyebrow">{{ $data['eyebrow'] }}</p>
            @endif
            <h2 class="sec-title">{{ $data['title'] ?? '' }}</h2>
            @if(!empty($data['contact_text']))
                <p class="sec-lead faq-lead">
                    {{ __('Anything else,') }}
                    <a href="{{ $data['contact_link'] ?: '#' }}" class="font-semibold whitespace-nowrap text-primary underline decoration-neutral-300 decoration-2 underline-offset-[5px] transition-colors hover:decoration-primary">{{ $data['contact_text'] }}</a>.
                </p>
            @endif
        </div>

        <div class="mx-auto mt-12 max-w-[52rem] md:mt-16" data-anim-item>
            @foreach($items as $item)
                <details class="faq__item" name="faq-{{ $section->slug }}">
                    <summary class="faq__q">
                        {{ $item['question'] ?? '' }}
                        <i class="ph ph-plus faq__sign" aria-hidden="true"></i>
                    </summary>
                    <div class="faq__a">
                        <p>{{ $item['answer'] ?? '' }}</p>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>

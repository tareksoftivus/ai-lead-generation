@php
    $data = $section->data;
    $items = $data['items'] ?? [];
@endphp
<section class="spy-section bg-[#f6f5ff]" data-anim id="faq">
    <div class="container">
        <div class="sec-head sec-head--center" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="sec-eyebrow">{{ $data['eyebrow'] }}</p>
            @endif
            <h2 class="sec-title">{{ $data['title'] ?? '' }}</h2>
            @if(!empty($data['subtitle']))
                <p class="sec-lead faq-lead">{{ $data['subtitle'] }}</p>
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

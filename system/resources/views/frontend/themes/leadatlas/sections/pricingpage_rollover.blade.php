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
        </div>

        <div class="mt-12 grid gap-x-8 gap-y-8 md:mt-14 md:grid-cols-3" data-anim-item>
            @foreach($items as $item)
                <div class="out">
                    <p class="font-title text-[1.0625rem] font-bold text-title">
                        {{ $item['title'] ?? '' }}
                    </p>
                    <p class="mt-2 max-w-[34ch] text-[0.9375rem] leading-[1.65]">
                        {{ $item['description'] ?? '' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@php
    $data = $section->data;
    $items = $data['items'] ?? [];
@endphp
<section class="pt-[calc(72px+3.5rem)] md:pt-[calc(72px+5rem)] py-12 xl:pt-[calc(72px+7rem)] spb-section" data-anim>
    <div class="container">
        <div class="sec-head sec-head--center" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="sec-eyebrow">{{ $data['eyebrow'] }}</p>
            @endif
            <h1 class="sec-title">{{ $data['title'] ?? '' }}</h1>
            @if(!empty($data['subtitle']))
                <p class="sec-lead">{{ $data['subtitle'] }}</p>
            @endif
        </div>

        <div class="mx-auto mt-12 max-w-[44rem] rounded-2xl border border-neutral-200 bg-neutral-0 p-6 md:mt-14 md:p-8"
            data-anim-item>
            @if(!empty($data['unit_label']))
                <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">
                    {{ $data['unit_label'] }}
                </p>
            @endif
            <ul class="mt-5 flex flex-col">
                @foreach($items as $item)
                    <li class="unit__item unit__item--{{ $item['variant'] ?? 'find' }}">
                        <i class="{{ $item['icon'] ?? 'ph ph-check' }}" aria-hidden="true"></i>
                        <span class="min-w-0 flex-1 text-title">{{ $item['label'] ?? '' }}</span>
                        <span
                            class="shrink-0 text-[0.8125rem] font-medium text-body @if(!empty($item['numeric'])) numeric @endif">{{ $item['value'] ?? '' }}</span>
                    </li>
                @endforeach
            </ul>
            @if(!empty($data['footnote']))
                <p class="mt-5 border-t border-neutral-200 pt-5 text-[0.875rem]">
                    {{ $data['footnote'] }}
                </p>
            @endif
        </div>
    </div>
</section>
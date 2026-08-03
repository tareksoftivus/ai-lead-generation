@php
    $data = $section->data;
    $items = $data['items'] ?? [];
@endphp
<section class="spy-section" data-anim id="features">
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

        <div class="mt-12 grid gap-5 sm:grid-cols-2 md:mt-16 lg:grid-cols-3">
            @foreach($items as $item)
                <article class="rounded-2xl border border-neutral-200 bg-neutral-0 p-6" data-anim-item>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl text-[1.375rem] feat__icon--{{ $item['variant'] ?? 'find' }}">
                        <i class="ph {{ $item['icon'] ?? 'ph-check' }}"></i>
                    </span>
                    <h3 class="mt-5 font-title text-[1.0625rem] font-bold text-title md:text-[1.125rem]">
                        {{ $item['title'] ?? '' }}
                    </h3>
                    <p class="mt-2 text-[0.9375rem] leading-[1.6]">
                        {{ $item['description'] ?? '' }}
                    </p>
                    @if(!empty($item['meta']))
                        <p class="mt-4 border-t border-neutral-100 pt-3 text-[0.75rem] font-medium tracking-[0.04em] text-neutral-500 uppercase feat__meta--{{ $item['variant'] ?? 'find' }}">
                            {{ $item['meta'] }}
                        </p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>

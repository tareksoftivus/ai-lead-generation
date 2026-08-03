@php
    $data = $section->data;
    $items = $data['items'] ?? [];
@endphp
<section class="spy-section" data-anim>
    <div class="container">
        <div class="max-w-[42rem]" data-anim-item>
            <p class="stage-head__key stage-head__key--act">
                <i class="{{ $data['key_icon'] ?? 'ph ph-paper-plane-tilt' }}" aria-hidden="true"></i>
                {{ $data['key_label'] ?? '' }}
            </p>
            <h2 class="mt-4 font-title text-[1.75rem] leading-[1.12] font-bold tracking-[-0.03em] text-balance text-title md:text-[2.25rem]">
                {{ $data['title'] ?? '' }}
            </h2>
            @if(!empty($data['subtitle']))
                <p class="stage-head__lead">{{ $data['subtitle'] }}</p>
            @endif
        </div>

        <div class="@container mt-12 grid gap-5 md:mt-14 sm:grid-cols-2 lg:grid-cols-4" data-anim-item>
            @foreach($items as $item)
                <article class="rounded-xl border border-neutral-200 bg-neutral-0 p-5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg text-[1.25rem] fcard__icon--act">
                        <i class="ph {{ $item['icon'] ?? 'ph-star' }}" aria-hidden="true"></i>
                    </span>
                    <h3 class="mt-4 font-title text-[1rem] font-bold text-title">
                        {{ $item['title'] ?? '' }}
                    </h3>
                    <p class="mt-2 text-[0.875rem] leading-[1.65]">
                        {{ $item['description'] ?? '' }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>

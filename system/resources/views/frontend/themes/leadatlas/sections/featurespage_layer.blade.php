@php
    $data = $section->data;
    $items = $data['items'] ?? [];
    $signals = $data['showcase_signals'] ?? [];
@endphp
<section class="spy-section bg-[#f6f5ff]" data-anim>
    <div class="container">
        <div class="max-w-[42rem]" data-anim-item>
            <p class="stage-head__key stage-head__key--ai">
                <i class="{{ $data['key_icon'] ?? 'ph-fill ph-sparkle' }}" aria-hidden="true"></i>
                {{ $data['key_label'] ?? '' }}
            </p>
            <h2 class="mt-4 font-title text-[1.75rem] leading-[1.12] font-bold tracking-[-0.03em] text-balance text-title md:text-[2.25rem]">
                {{ $data['title'] ?? '' }}
            </h2>
            @if(!empty($data['subtitle']))
                <p class="stage-head__lead">{{ $data['subtitle'] }}</p>
            @endif
        </div>

        <div class="score-work" data-anim-item>
            <div class="bg-neutral-0 p-6 md:p-8">
                <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">
                    {{ $data['showcase_reads_label'] ?? __('What it reads') }}
                </p>
                <ul class="signals">
                    @foreach($signals as $signal)
                        <li class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 border-b border-neutral-100 py-3 last:border-b-0 last:pb-0">
                            <span class="text-[0.9375rem] text-title">{{ $signal['signal'] ?? '' }}</span>
                            <span class="text-[0.8125rem]">{{ $signal['note'] ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-neutral-0 p-6 md:p-8 score-work__col--out">
                <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase text-ai-deep">
                    {{ $data['showcase_produces_label'] ?? __('What it produces') }}
                </p>

                <div class="mt-5 flex items-center gap-4">
                    <span class="shrink-0 font-title text-[2.75rem] leading-none font-bold text-ai-deep numeric">{{ $data['showcase_score'] ?? '' }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="font-title text-[0.9375rem] font-bold text-title">{{ $data['showcase_name'] ?? '' }}</p>
                        <div class="verdict__bar" aria-hidden="true">
                            <span class="block h-full w-[{{ $data['showcase_score'] ?? 0 }}%] rounded-full bg-ai"></span>
                        </div>
                    </div>
                </div>

                @if(!empty($data['showcase_why']))
                    <p class="verdict__why">
                        &ldquo;{{ $data['showcase_why'] }}&rdquo;
                    </p>
                @endif

                @if(!empty($data['showcase_note']))
                    <p class="verdict__note">
                        <i class="ph ph-info" aria-hidden="true"></i>
                        {{ $data['showcase_note'] }}
                    </p>
                @endif
            </div>
        </div>

        <div class="@container mt-12 grid gap-5 md:mt-14 sm:grid-cols-2 lg:grid-cols-4 fgrid--tight" data-anim-item>
            @foreach($items as $item)
                <article class="rounded-xl border border-neutral-200 bg-neutral-0 p-5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg text-[1.25rem] fcard__icon--ai">
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

@php
    $data = $section->data;
    $coverImage = $data['cover_image_url'] ?? null;

    if (! $coverImage && ! empty($data['cover_image'])) {
        $coverValue = (string) $data['cover_image'];

        if (filter_var($coverValue, FILTER_VALIDATE_URL) || str_starts_with($coverValue, '/')) {
            $coverImage = $coverValue;
        } elseif (is_numeric($coverValue)) {
            $coverImage = media_url($coverValue);
        } else {
            $coverImage = media_url($coverValue) ?: $coverValue;
        }
    }

    $coverImage = $coverImage ?: asset('assets/frontend/leadatlas/images/blog/blog-thumb-01.jpg');
    $authorAvatar = media_url($data['author_avatar'] ?? null) ?: ($data['author_avatar'] ?? asset('assets/frontend/leadatlas/images/avatars/avatar-3.jpg'));
@endphp
<article>
    <section class="spb-section pt-10 md:pt-12" data-anim>
        <div class="container">
            <div class="byline" data-anim-item>
                <img
                    src="{{ $authorAvatar }}"
                    alt="{{ $data['author_name'] ?? '' }}"
                    class="post__avatar"
                    width="80"
                    height="80"
                />
                <div class="mr-auto">
                    <p class="text-base font-semibold text-title">{{ $data['author_name'] ?? '' }}</p>
                    <p class="mt-1 flex flex-wrap items-center gap-x-2 text-[0.875rem]">
                        @if(!empty($data['published_date']))
                            <time datetime="{{ $data['published_date'] }}">{{ $data['published_date_display'] ?? '' }}</time>
                            <span class="post__dot" aria-hidden="true">&middot;</span>
                        @endif
                        <span><span class="numeric">{{ $data['read_minutes'] ?? 1 }}</span> {{ __('min read') }}</span>
                    </p>
                </div>
                @if(!empty($data['category']))
                    <span class="post__topic post__topic--{{ $data['variant'] ?? 'find' }} byline__topic">
                        {{ $data['category'] }}
                    </span>
                @endif
            </div>

            <div class="mx-auto my-8 w-full max-w-3xl overflow-hidden rounded-2xl border border-neutral-200" data-anim-item>
                <img
                    src="{{ $coverImage }}"
                    alt="{{ $data['title'] ?? '' }}"
                    class="block aspect-video h-auto w-full object-cover"
                />
            </div>

            <div class="prose" data-anim-item>
                {!! $data['body'] ?? '' !!}
            </div>

            <aside class="post-cta" data-anim-item>
                <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-accent-dark uppercase">
                    {{ __('Try it yourself') }}
                </p>
                <p class="mt-3 max-w-[44ch] text-[0.9375rem] leading-[1.65]">
                    {{ __('Run one search on :credits free credits and see what the scoring does with your own market.', ['credits' => 100]) }}
                </p>
                <a href="{{ route('register') }}" class="btn btn-accent btn-sm mt-5">
                    <span class="btn__label">
                        <span>{{ __('Start free') }}</span>
                        <span aria-hidden="true">{{ __('Start free') }}</span>
                    </span>
                    <i class="ph ph-arrow-right" aria-hidden="true"></i>
                </a>
            </aside>
        </div>
    </section>
</article>

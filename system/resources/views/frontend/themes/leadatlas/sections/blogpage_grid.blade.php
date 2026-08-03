@php
    $data = $section->data;
    $topics = $data['topics'] ?? [];
    $posts = $data['posts'] ?? [];
    $pages = $data['pages'] ?? [];

    $resolveImage = function (?string $image): ?string {
        if (empty($image)) {
            return null;
        }

        return str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')
            ? $image
            : asset('assets/frontend/leadatlas/images/blog/'.$image);
    };

    $leadImage = $resolveImage($data['lead_image'] ?? null);
@endphp
<section class="spb-section pt-12 md:pt-14" data-anim>
    <div class="container">
        <nav class="flex flex-wrap items-center gap-2" aria-label="{{ __('Article categories') }}" data-anim-item>
            @foreach($topics as $topic)
                <a href="{{ !empty($topic['value']) ? '?topic='.$topic['value'] : url('blog') }}" class="topic{{ !empty($topic['active']) ? ' is-active' : '' }}"@if(!empty($topic['active'])) aria-current="page"@endif>
                    {{ $topic['label'] ?? '' }}
                </a>
            @endforeach
        </nav>

        <article class="relative mt-8 grid gap-6 rounded-2xl border border-neutral-200 bg-neutral-0 p-6 transition-colors duration-200 hover:border-neutral-300 md:mt-10 md:grid-cols-12 md:items-center md:p-9" data-anim-item>
            <a href="{{ $data['lead_link'] ?: '#' }}" class="post__media relative block overflow-hidden rounded-xl aspect-[16/10] md:col-span-5" tabindex="-1" aria-hidden="true">
                @if($leadImage)
                    <img src="{{ $leadImage }}" alt="{{ $data['lead_title'] ?? '' }}" class="post__img transition-transform duration-300 hover:scale-105" />
                @else
                    <div class="flex h-full w-full items-center justify-center bg-neutral-100 text-neutral-300">
                        <i class="ph ph-image text-4xl" aria-hidden="true"></i>
                    </div>
                @endif
            </a>
            <div class="max-w-[44rem] md:col-span-7">
                <p class="post__meta">
                    <span class="post__topic post__topic--{{ $data['lead_variant'] ?? 'find' }}">{{ $data['lead_topic'] ?? '' }}</span>
                    <span class="post__dot" aria-hidden="true">·</span>
                    <time datetime="{{ $data['lead_date'] ?? '' }}">{{ $data['lead_date_display'] ?? '' }}</time>
                    <span class="post__dot" aria-hidden="true">·</span>
                    <span><span class="numeric">{{ $data['lead_read_minutes'] ?? '' }}</span> {{ __('min read') }}</span>
                </p>

                <h2 class="mt-4 font-title text-[1.5rem] leading-[1.15] font-bold tracking-[-0.02em] text-balance text-title md:text-[1.875rem]">
                    <a href="{{ $data['lead_link'] ?: '#' }}" class="post__link">
                        {{ $data['lead_title'] ?? '' }}
                    </a>
                </h2>

                <p class="mt-4 max-w-[58ch] text-[1rem] leading-[1.65]">
                    {{ $data['lead_excerpt'] ?? '' }}
                </p>

                <p class="post__by">
                    <img src="{{ asset('assets/frontend/leadatlas/images/avatars/'.($data['lead_author_avatar'] ?: 'avatar-1.jpg')) }}" alt="{{ $data['lead_author_name'] ?? '' }}" class="post__avatar" width="80" height="80" />
                    {{ $data['lead_author_name'] ?? '' }}
                </p>
            </div>
        </article>

        <div class="posts" data-anim-item>
            @foreach($posts as $post)
                @php($postImage = $resolveImage($post['image'] ?? null))
                <article class="post">
                    <a href="{{ $post['link'] ?: '#' }}" class="post__media" tabindex="-1" aria-hidden="true">
                        @if($postImage)
                            <img src="{{ $postImage }}" alt="{{ $post['title'] ?? '' }}" class="post__img" />
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-neutral-100 text-neutral-300">
                                <i class="ph ph-image text-4xl" aria-hidden="true"></i>
                            </div>
                        @endif
                        <span class="post__date">
                            <span class="post__date-day numeric">{{ $post['date_day'] ?? '' }}</span>
                            <span class="post__date-mon">{{ $post['date_month'] ?? '' }}</span>
                        </span>
                    </a>

                    <div class="post__body">
                        <p class="post__meta">
                            <span class="post__topic post__topic--{{ $post['variant'] ?? 'find' }}">{{ $post['topic'] ?? '' }}</span>
                            <span class="post__dot" aria-hidden="true">·</span>
                            <span><span class="numeric">{{ $post['read_minutes'] ?? '' }}</span> {{ __('min read') }}</span>
                        </p>

                        <h2 class="post__title">
                            <a href="{{ $post['link'] ?: '#' }}" class="post__link">{{ $post['title'] ?? '' }}</a>
                        </h2>

                        <p class="post__excerpt">
                            {{ $post['excerpt'] ?? '' }}
                        </p>

                        <p class="post__foot">
                            <span class="post__by">
                                <img src="{{ asset('assets/frontend/leadatlas/images/avatars/'.($post['author_avatar'] ?: 'avatar-1.jpg')) }}" alt="{{ $post['author_name'] ?? '' }}" class="post__avatar" width="80" height="80" />
                                {{ $post['author_name'] ?? '' }}
                            </span>
                            <a href="{{ $post['link'] ?: '#' }}" class="btn btn-primary btn-sm post__more" tabindex="-1" aria-hidden="true">
                                <span class="btn__label">
                                    <span>{{ __('Read more') }}</span>
                                    <span aria-hidden="true">{{ __('Read more') }}</span>
                                </span>
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        @if(!empty($pages))
            <nav class="mt-12 flex flex-wrap items-center justify-between gap-4 border-t border-neutral-200 pt-8 md:mt-14" aria-label="{{ __('Pagination') }}" data-anim-item>
                <span class="pager__step is-disabled" aria-disabled="true">
                    <i class="ph ph-caret-left" aria-hidden="true"></i>
                    {{ __('Previous') }}
                </span>

                <ol class="flex items-center gap-1">
                    @foreach($pages as $page)
                        <li>
                            <a href="{{ !empty($page['active']) ? url('blog') : url('blog').'?page='.($page['number'] ?? '') }}" class="pager__num{{ !empty($page['active']) ? ' is-active' : '' }}"@if(!empty($page['active'])) aria-current="page"@endif>{{ $page['number'] ?? '' }}</a>
                        </li>
                    @endforeach
                </ol>

                <a href="{{ url('blog') }}?page=2" class="pager__step">
                    {{ __('Next') }}
                    <i class="ph ph-caret-right" aria-hidden="true"></i>
                </a>
            </nav>
        @endif
    </div>
</section>

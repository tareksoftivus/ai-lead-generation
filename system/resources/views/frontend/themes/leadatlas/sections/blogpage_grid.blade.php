@php
    $data = $section->data;
    $topics = $data['topics'] ?? [];
    $rawPosts = $data['posts'] ?? [];
    $pages = $data['pages'] ?? [];
    $previousPageUrl = $data['previous_page_url'] ?? null;
    $nextPageUrl = $data['next_page_url'] ?? null;

    $defaultThumbnails = [
        0 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-02.jpg'),
        1 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-03.jpg'),
        2 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-04.jpg'),
        3 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-05.jpg'),
        4 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-06.jpg'),
        5 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-07.jpg'),
    ];

    $defaultAuthorAvatars = [
        0 => asset('assets/frontend/leadatlas/images/avatars/avatar-4.jpg'),
        1 => asset('assets/frontend/leadatlas/images/avatars/avatar-3.jpg'),
        2 => asset('assets/frontend/leadatlas/images/avatars/avatar-2.jpg'),
        3 => asset('assets/frontend/leadatlas/images/avatars/avatar-4.jpg'),
    ];

    $posts = collect($rawPosts)
        ->values()
        ->map(function ($post, $index) use ($defaultThumbnails, $defaultAuthorAvatars) {
            $post['image_url'] = media_url($post['image'] ?? null) ?: ($defaultThumbnails[$index % 6] ?? null);
            $post['author_avatar_url'] = media_url($post['author_avatar'] ?? null) ?: ($defaultAuthorAvatars[$index % 4] ?? null);

            return $post;
        });

    $leadImage = media_url($data['lead_image'] ?? null) ?: asset('assets/frontend/leadatlas/images/blog/blog-thumb-01.jpg');
    $leadAuthorAvatar = media_url($data['lead_author_avatar'] ?? null) ?: asset('assets/frontend/leadatlas/images/avatars/avatar-3.jpg');
@endphp
<section class="spb-section pt-12 md:pt-14" data-anim>
    <div class="container">
        <nav class="flex flex-wrap items-center gap-2" aria-label="{{ __('Article categories') }}" data-anim-item>
            @foreach($topics as $topic)
                <a href="{{ $topic['url'] ?? route('blog.index', !empty($topic['value']) ? ['category' => $topic['value']] : []) }}" class="topic{{ !empty($topic['active']) ? ' is-active' : '' }}"@if(!empty($topic['active'])) aria-current="page"@endif>
                    {{ $topic['label'] ?? '' }}
                </a>
            @endforeach
        </nav>

        <article class="relative mt-8 grid gap-6 rounded-2xl border border-neutral-200 bg-neutral-0 p-6 transition-colors duration-200 hover:border-neutral-300 md:mt-10 md:grid-cols-12 md:items-center md:p-9" data-anim-item>
            <a href="{{ $data['lead_link'] ?: '#' }}" class="post__media relative block overflow-hidden rounded-xl aspect-[16/10] md:col-span-5" tabindex="-1" aria-hidden="true">
                @if($leadImage)
                    <img src="{{ $leadImage }}" alt="{{ $data['lead_title'] ?? '' }}" class="post__img transition-transform duration-300 hover:scale-105">
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
                    <img src="{{ $leadAuthorAvatar }}" alt="{{ $data['lead_author_name'] ?? '' }}" class="post__avatar" width="80" height="80">
                    {{ $data['lead_author_name'] ?? '' }}
                </p>
            </div>
        </article>

        <div class="posts" data-anim-item>
            @foreach($posts as $post)
                <article class="post">
                    <a href="{{ $post['link'] ?: '#' }}" class="post__media" tabindex="-1" aria-hidden="true">
                        @if($post['image_url'])
                            <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] ?? '' }}" class="post__img">
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
                                @if($post['author_avatar_url'])
                                    <img src="{{ $post['author_avatar_url'] }}" alt="{{ $post['author_name'] ?? '' }}" class="post__avatar" width="80" height="80">
                                @endif
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
                @if($previousPageUrl)
                    <a href="{{ $previousPageUrl }}" class="pager__step">
                        <i class="ph ph-caret-left" aria-hidden="true"></i>
                        {{ __('Previous') }}
                    </a>
                @else
                    <span class="pager__step is-disabled" aria-disabled="true">
                        <i class="ph ph-caret-left" aria-hidden="true"></i>
                        {{ __('Previous') }}
                    </span>
                @endif

                <ol class="flex items-center gap-1">
                    @foreach($pages as $page)
                        <li>
                            <a href="{{ $page['url'] ?? route('blog.index', ['page' => $page['number'] ?? 1]) }}" class="pager__num{{ !empty($page['active']) ? ' is-active' : '' }}"@if(!empty($page['active'])) aria-current="page"@endif>{{ $page['number'] ?? '' }}</a>
                        </li>
                    @endforeach
                </ol>

                @if($nextPageUrl)
                    <a href="{{ $nextPageUrl }}" class="pager__step">
                        {{ __('Next') }}
                        <i class="ph ph-caret-right" aria-hidden="true"></i>
                    </a>
                @else
                    <span class="pager__step is-disabled" aria-disabled="true">
                        {{ __('Next') }}
                        <i class="ph ph-caret-right" aria-hidden="true"></i>
                    </span>
                @endif
            </nav>
        @endif
    </div>
</section>

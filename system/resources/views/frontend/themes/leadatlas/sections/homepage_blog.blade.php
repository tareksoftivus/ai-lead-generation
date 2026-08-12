@php
    $data = $section->data;
    $rawPosts = $data['posts'] ?? [];

    $defaultThumbnails = [
        0 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-01.jpg'),
        1 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-02.jpg'),
        2 => asset('assets/frontend/leadatlas/images/blog/blog-thumb-03.jpg'),
    ];

    $defaultAuthorAvatars = [
        0 => asset('assets/frontend/leadatlas/images/avatars/avatar-1.jpg'),
        1 => asset('assets/frontend/leadatlas/images/avatars/avatar-2.jpg'),
        2 => asset('assets/frontend/leadatlas/images/avatars/avatar-3.jpg'),
        3 => asset('assets/frontend/leadatlas/images/avatars/avatar-4.jpg'),
    ];

    $posts = collect($rawPosts)
        ->values()
        ->map(function ($post, $index) use ($defaultThumbnails, $defaultAuthorAvatars) {
            $post['image_url'] = media_url($post['image'] ?? null) ?: ($defaultThumbnails[$index % 3] ?? null);
            $post['author_avatar_url'] = media_url($post['author_avatar'] ?? null) ?: ($defaultAuthorAvatars[$index % 4] ?? null);

            return $post;
        });
@endphp
<section class="spy-section" data-anim>
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

        <div class="posts posts--three" data-anim-item>
            @foreach($posts as $post)
                <article class="post">
                    <a href="{{ $post['link'] ?: '#' }}" class="post__media" tabindex="-1" aria-hidden="true">
                        @if($post['image_url'])
                            <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] ?? '' }}" class="post__img">
                        @endif
                        <span class="post__date">
                            <span class="post__date-day numeric">{{ $post['date_day'] ?? '' }}</span>
                            <span class="post__date-mon">{{ $post['date_month'] ?? '' }}</span>
                        </span>
                    </a>

                    <div class="post__body">
                        <p class="post__meta">
                            <span
                                class="post__topic post__topic--{{ $post['variant'] ?? 'find' }}">{{ $post['topic'] ?? '' }}</span>
                            <span class="post__dot" aria-hidden="true">·</span>
                            <span><span class="numeric">{{ $post['read_minutes'] ?? '' }}</span> {{ __('min read') }}</span>
                        </p>

                        <h3 class="post__title">
                            <a href="{{ $post['link'] ?: '#' }}" class="post__link">
                                {{ $post['title'] ?? '' }}
                            </a>
                        </h3>

                        <p class="post__excerpt">
                            {{ $post['excerpt'] ?? '' }}
                        </p>

                        <p class="post__foot">
                            <span class="post__by">
                                @if($post['author_avatar_url'])
                                    <img src="{{ $post['author_avatar_url'] }}" alt="{{ $post['author_name'] ?? '' }}"
                                        class="post__avatar" width="80" height="80">
                                @endif
                                {{ $post['author_name'] ?? '' }}
                            </span>
                            <a href="{{ $post['link'] ?: '#' }}" class="btn btn-primary btn-sm post__more" tabindex="-1"
                                aria-hidden="true">
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

        @if(!empty($data['all_articles_text']))
            <div class="mt-10 flex justify-center md:mt-12" data-anim-item>
                <a href="{{ $data['all_articles_link'] ?: '#' }}" class="btn btn-outline">
                    <span class="btn__label">
                        <span>{{ $data['all_articles_text'] }}</span>
                        <span aria-hidden="true">{{ $data['all_articles_text'] }}</span>
                    </span>
                    <i class="ph ph-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        @endif
    </div>
</section>

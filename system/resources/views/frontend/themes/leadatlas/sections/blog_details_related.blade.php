@php
    $posts = collect($section->data['posts'] ?? [])->map(function ($post) {
        $post['image_url'] = media_url($post['image'] ?? null) ?: ($post['image'] ?? asset('assets/frontend/leadatlas/images/blog/blog-thumb-02.jpg'));
        $post['author_avatar_url'] = media_url($post['author_avatar'] ?? null) ?: ($post['author_avatar'] ?? asset('assets/frontend/leadatlas/images/avatars/avatar-4.jpg'));

        return $post;
    });
@endphp

@if($posts->isNotEmpty())
    <section class="spy-section bg-[#f6f5ff]" data-anim>
        <div class="container">
            <div class="sec-head" data-anim-item>
                <h2 class="font-title text-[1.5rem] leading-[1.15] font-bold tracking-[-0.02em] text-title md:text-[1.75rem]">
                    {{ __('Keep reading') }}
                </h2>
            </div>

            <div class="posts posts--three" data-anim-item>
                @foreach($posts as $post)
                    <article class="post">
                        <a href="{{ $post['url'] ?? '#' }}" class="post__media" tabindex="-1" aria-hidden="true">
                            <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] ?? '' }}" class="post__img">
                            @if(!empty($post['date_day']) || !empty($post['date_month']))
                                <span class="post__date">
                                    <span class="post__date-day numeric">{{ $post['date_day'] ?? '' }}</span>
                                    <span class="post__date-mon">{{ $post['date_month'] ?? '' }}</span>
                                </span>
                            @endif
                        </a>
                        <div class="post__body">
                            <p class="post__meta">
                                @if(!empty($post['category']))
                                    <span class="post__topic post__topic--{{ $post['variant'] ?? 'find' }}">{{ $post['category'] }}</span>
                                    <span class="post__dot" aria-hidden="true">&middot;</span>
                                @endif
                                <span><span class="numeric">{{ $post['read_minutes'] ?? 1 }}</span> {{ __('min read') }}</span>
                            </p>
                            <h3 class="post__title">
                                <a href="{{ $post['url'] ?? '#' }}" class="post__link">
                                    {{ $post['title'] ?? '' }}
                                </a>
                            </h3>
                            @if(!empty($post['excerpt']))
                                <p class="post__excerpt">
                                    {{ $post['excerpt'] }}
                                </p>
                            @endif

                            <p class="post__foot">
                                <span class="post__by">
                                    <img
                                        src="{{ $post['author_avatar_url'] }}"
                                        alt="{{ $post['author_name'] ?? '' }}"
                                        class="post__avatar"
                                        width="80"
                                        height="80"
                                    >
                                    {{ $post['author_name'] ?? '' }}
                                </span>
                                <a href="{{ $post['url'] ?? '#' }}" class="btn btn-primary btn-sm post__more" tabindex="-1" aria-hidden="true">
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
        </div>
    </section>
@endif

@php
    $data = $section->data;
@endphp
<header class="relative isolate overflow-hidden border-b border-neutral-200 bg-[#f6f5ff] pt-[calc(72px+2.5rem)] pb-10 md:pt-[calc(72px+3.5rem)] md:pb-12" data-anim>
    <div class="phead__field" aria-hidden="true"></div>

    <div class="container">
        <nav class="mb-6" aria-label="{{ __('Breadcrumb') }}" data-anim-item>
            <ol class="flex flex-wrap items-center gap-2">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="crumb__link">
                        <i class="ph ph-house" aria-hidden="true"></i>
                        {{ __('Home') }}
                    </a>
                </li>
                <li class="flex items-center" aria-hidden="true">
                    <i class="ph ph-caret-right text-[0.75rem] text-neutral-500"></i>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('blog.index') }}" class="crumb__link">{{ __('Blog') }}</a>
                </li>
                <li class="flex items-center" aria-hidden="true">
                    <i class="ph ph-caret-right text-[0.75rem] text-neutral-500"></i>
                </li>
                <li class="flex items-center">
                    <span class="text-[0.8125rem] font-semibold text-title" aria-current="page">{{ $data['title'] ?? '' }}</span>
                </li>
            </ol>
        </nav>

        <div class="max-w-[44rem]" data-anim-item>
            <h1 class="font-title text-[2rem] leading-[1.05] font-bold tracking-[-0.035em] text-balance text-title md:text-[2.75rem] xl:text-[3rem]">{{ $data['title'] ?? '' }}</h1>
        </div>
    </div>
</header>

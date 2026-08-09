<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') - {{ setting('site_name', config('app.name', 'LeadAtlas')) }}</title>
    <link rel="stylesheet" href="{{ asset('assets/global/fonts/phosphor/regular/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/leadatlas.js'])
    @include('components.layouts.partials.branding')
    <x-plugins.head-scripts />
</head>
@hasSection('aside')

    <body class="overflow-x-hidden bg-neutral-0">
        @php
            $siteName = setting('site_name', config('app.name', 'LeadAtlas'));
            $settingLogo = setting('site_logo') && media_url(setting('site_logo')) ? media_url(setting('site_logo')) : null;
            $brandLogo = $settingLogo ?? asset('assets/frontend/leadatlas/images/logo-lockup.png');
        @endphp

        <main class="auth">
            <aside class="relative isolate hidden flex-col overflow-hidden bg-[#f6f5ff] p-10 lg:flex xl:p-12">
                <div class="auth__brand-field" aria-hidden="true"></div>

                <a href="{{ route('home') }}" class="auth__logo" aria-label="{{ $siteName }} home">
                    <img src="{{ $brandLogo }}" alt="{{ $siteName }}" width="482" height="140" class="h-9 w-auto" />
                </a>

                <div class="my-auto max-w-[26rem] py-10">
                    @yield('aside')
                </div>

                <p class="shrink-0 text-[0.8125rem]">
                    &copy; <span class="numeric">{{ now()->year }}</span> {{ $siteName }}
                </p>
            </aside>

            <div class="auth__main">
                <div class="auth__panel">
                    <a href="{{ route('home') }}" class="auth__logo auth__logo--mobile" aria-label="{{ $siteName }} home">
                        <img src="{{ $brandLogo }}" alt="{{ $siteName }}" width="482" height="140" class="h-9 w-auto" />
                    </a>

                    @include('components.layouts.partials.session-flash')

                    @yield('content')
                </div>
            </div>
        </main>

        <div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>
        <x-ui.flash />
    </body>
@else

    <body class="min-h-screen flex items-center justify-center bg-neutral-50 dark:bg-neutral-0 p-4 antialiased">
        @php
            $siteName = setting('site_name', config('app.name', 'Admin Panel'));
            $settingLogo = setting('site_logo') && media_url(setting('site_logo')) ? media_url(setting('site_logo')) : null;
            // Prefer the uploaded site logo (a wide wordmark); it renders at its
            // natural aspect. Only the packaged square mark uses the rounded badge.
            $brandLogo = $settingLogo ?? asset('assets/uploads/brand/leadatlas-logo.png');
        @endphp

        <div class="section-card w-full max-w-md">
            {{-- Logo (the wordmark already carries the brand name). --}}
            <div class="mb-8 flex justify-center">
                <img src="{{ $brandLogo }}" alt="{{ $siteName }}" class="h-12 w-auto max-w-48 object-contain">
            </div>

            @include('components.layouts.partials.session-flash')

            @yield('content')
        </div>

        <div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>
        <x-ui.flash />
    </body>
@endif

</html>
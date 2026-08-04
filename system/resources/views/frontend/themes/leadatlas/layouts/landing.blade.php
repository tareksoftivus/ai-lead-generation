<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>{{ $page->meta_title ?: $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description ?: __('Find, enrich, and score local business leads from Google Maps with AI. Search by keyword and location, then export outreach-ready prospects.') }}" />
    <link rel="icon" href="{{ asset('assets/frontend/leadatlas/images/logo-32.png') }}" type="image/png" sizes="32x32" />
    <link rel="apple-touch-icon" href="{{ asset('assets/frontend/leadatlas/images/logo-180.png') }}" />
    @vite(['resources/js/leadatlas.js'])
</head>

<body class="overflow-x-hidden">
    @include('frontend.themes.leadatlas.navigation.header', ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])

    @foreach($resolvedSections as $resolved)
        @include($resolved['view'], ['section' => $resolved['section'], 'themeKey' => $themeKey, 'themeVars' => $themeVars, 'supported' => $resolved['supported']])
    @endforeach

    @include('frontend.themes.leadatlas.navigation.footer', ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])

    <div id="toastContainer" class="toast-container"></div>
    @include('frontend.themes.leadatlas.partials.flash')
</body>

</html>

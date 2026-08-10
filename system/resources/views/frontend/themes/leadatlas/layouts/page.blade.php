<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title ?: $page->title }}</title>
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @if(setting('site_favicon') && media_url(setting('site_favicon')))
        <link rel="icon" href="{{ media_url(setting('site_favicon')) }}" />
    @else
        <link rel="icon" href="{{ asset('assets/frontend/leadatlas/images/logo-32.png') }}" type="image/png" sizes="32x32" />
    @endif
    <link rel="apple-touch-icon" href="{{ asset('assets/frontend/leadatlas/images/logo-180.png') }}" />
    <style>
        :root { --primary: {{ $themeVars['primary_color'] ?? '#4F39F6' }}; --accent: {{ $themeVars['accent_color'] ?? '#0F172A' }}; }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/js/leadatlas.js'])
</head>
<body>
    @include('frontend.themes.leadatlas.navigation.header', ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])
    @foreach($resolvedSections as $resolved)
        @include($resolved['view'], ['section' => $resolved['section'], 'themeKey' => $themeKey, 'themeVars' => $themeVars, 'supported' => $resolved['supported']])
    @endforeach
    @include('frontend.themes.leadatlas.navigation.footer', ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])

    <div id="toastContainer" class="toast-container"></div>
    @include('frontend.themes.leadatlas.partials.flash')
</body>
</html>

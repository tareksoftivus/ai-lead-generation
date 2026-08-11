@php
    $pageTitle = $seo['title'] ?? ($page->meta_title ?: $page->title);
    $pageDescription = $seo['description'] ?? $page->meta_description;
    $canonicalUrl = $seo['canonical'] ?? null;
    $ogType = $seo['og_type'] ?? 'website';
    $ogImage = $seo['image'] ?? null;
    $publishedTime = $seo['published_time'] ?? null;
    $modifiedTime = $seo['modified_time'] ?? null;
    $author = $seo['author'] ?? null;
    $jsonLd = $seo['json_ld'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    @if($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    @if($canonicalUrl)
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    @if($pageDescription)
        <meta property="og:description" content="{{ $pageDescription }}">
    @endif
    @if($canonicalUrl)
        <meta property="og:url" content="{{ $canonicalUrl }}">
    @endif
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @if($ogType === 'article')
        @if($publishedTime)
            <meta property="article:published_time" content="{{ $publishedTime }}">
        @endif
        @if($modifiedTime)
            <meta property="article:modified_time" content="{{ $modifiedTime }}">
        @endif
        @if($author)
            <meta property="article:author" content="{{ $author }}">
        @endif
    @endif
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    @if($pageDescription)
        <meta name="twitter:description" content="{{ $pageDescription }}">
    @endif
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif
    @if($jsonLd)
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
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
    <x-plugins.head-scripts />
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

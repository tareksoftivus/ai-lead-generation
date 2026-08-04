<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title ?: $page->title }}</title>
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    <style>
        :root { --primary: {{ $themeVars['primary_color'] ?? '#4F39F6' }}; --accent: {{ $themeVars['accent_color'] ?? '#0F172A' }}; }
    </style>
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

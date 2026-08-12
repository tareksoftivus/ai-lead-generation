@props([
    'title' => 'Dashboard',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $currentDirection ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'LeadAtlas') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Loaded non-blocking (media="print" swapped to "all" on load) so a slow/roundtrip
         request to Google Fonts can't delay first paint of the page's own CSS. --}}
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet"></noscript>

    {{-- Vite Assets (LeadAtlas design system + app shell JS) --}}
    <x-vite :entrypoints="['resources/js/panel-user.js', 'resources/js/app.js']"></x-vite>

    {{-- Additional Styles --}}
    @stack('styles')

    {{-- Branding: favicon + dynamic theme colors --}}
    @include('components.layouts.partials.branding')
</head>
<body class="bg-neutral-0 overflow-x-hidden font-body text-body antialiased">

    {{-- Impersonation Banner --}}
    <x-ui.impersonation-banner />

    {{-- Sidebar Navigation --}}
    <x-navigation.user-sidebar />

    {{-- Main Content --}}
    <div class="app-main">
        {{-- Topbar --}}
        <x-navigation.user-topbar :title="$title" />

        {{-- Page Content --}}
        <main class="app-content">
            {{ $slot }}
        </main>
    </div>

    {{-- Toast Notification Container --}}
    <x-ui.toast />

    {{-- Flash Messages --}}
    <x-ui.flash />

    {{-- Drawers --}}
    @stack('drawers')

    {{-- Modals --}}
    @stack('modals')

    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>

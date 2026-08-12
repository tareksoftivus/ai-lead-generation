@php
    // Use the view-shared $panelConfig (from PanelAccess middleware) or fall back to the singleton
    $panelConfig = $panelConfig ?? app('current.panel') ?? [];
    $navigation  = $panelConfig['navigation'] ?? [];
    $siteName = setting('site_name', config('app.name', 'LeadAtlas'));
    $brandLogo = setting('site_logo') && media_url(setting('site_logo'))
        ? media_url(setting('site_logo'))
        : asset('assets/uploads/brand/leadatlas-logo.png');

    // Group navigation items by their 'group' key
    $groups = [];
    foreach ($navigation as $item) {
        $groupName = $item['group'] ?? null;
        $groups[$groupName][] = $item;
    }

    $panelKey = $panel ?? 'user';
    $homeUrl = $panelKey === 'user' && \Illuminate\Support\Facades\Route::has('user.search.new')
        ? route('user.search.new')
        : (\Illuminate\Support\Facades\Route::has("{$panelKey}.dashboard")
            ? route("{$panelKey}.dashboard")
            : url('/'));
@endphp

<aside class="app-sidebar" data-sidebar>
    <div class="f-between h-[72px] shrink-0 border-b border-r border-neutral-200 bg-neutral-0 px-5">
        <a href="{{ $homeUrl }}" class="ml-1 flex min-w-0 items-center" aria-label="{{ __('Go to new search') }}">
            <img src="{{ $brandLogo }}" alt="{{ $siteName }}" class="max-w-[150px]" data-live-logo="site_logo" data-default-src="{{ asset('assets/uploads/brand/leadatlas-logo.png') }}">
        </a>
        <button type="button"
                class="f-center h-9 w-9 shrink-0 rounded-lg text-body hover:bg-neutral-100 lg:hidden"
                aria-label="{{ __('Close menu') }}"
                data-sidebar-close>
            <i class="ph ph-x text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 pt-4 pb-6" aria-label="{{ __('User navigation') }}">
        @foreach($groups as $groupTitle => $items)
            @if($groupTitle)
                <p class="sidebar-group">{{ __($groupTitle) }}</p>
            @endif

            @foreach($items as $item)
                @php
                    $itemRoute = $item['route'] ?? '';
                    $active = $itemRoute ? request()->routeIs($itemRoute) : false;
                    $routeBase = explode('.*', $itemRoute)[0];
                    $routeName = str_contains($itemRoute, '.*') ? $routeBase . '.index' : $routeBase;

                    try {
                        $href = $itemRoute ? route($routeName) : '#';
                    } catch (\Throwable $e) {
                        $href = '#';
                    }
                @endphp

                @if(isset($item['permission']))
                    @can($item['permission'])
                        <a href="{{ $href }}" class="sidebar-link{{ $active ? ' is-active' : '' }}">
                            <i class="ph {{ $item['icon'] ?? 'ph-circle' }}"></i>{{ __($item['label'] ?? '') }}
                        </a>
                    @endcan
                @else
                    <a href="{{ $href }}" class="sidebar-link{{ $active ? ' is-active' : '' }}">
                        <i class="ph {{ $item['icon'] ?? 'ph-circle' }}"></i>{{ __($item['label'] ?? '') }}
                    </a>
                @endif
            @endforeach
        @endforeach
    </nav>
</aside>

<div class="app-sidebar__backdrop" data-sidebar-backdrop aria-hidden="true"></div>

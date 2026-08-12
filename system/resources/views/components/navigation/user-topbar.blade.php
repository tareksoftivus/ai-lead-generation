@props([
    'title' => '',
])

@php
    $currentUser = $authUser ?? auth()->user();
    $initials = strtoupper(substr($currentUser->name ?? 'U', 0, 2));

    $panelKey = app('current.panel')['key'] ?? 'user';
    $creditBalance = $currentUser instanceof \App\Models\User
        ? app(\App\Modules\Credits\Services\CreditLedger::class)->balance($currentUser)
        : null;
    $bellConfig = [
        'unreadCountUrl' => route($panelKey . '.system-notifications.unread-count'),
        'recentUrl' => route($panelKey . '.system-notifications.recent'),
        'markReadUrl' => route($panelKey . '.system-notifications.mark-read', ['notification' => '__ID__']),
        'markAllReadUrl' => route($panelKey . '.system-notifications.mark-all-read'),
        'viewAllUrl' => route($panelKey . '.system-notifications.index'),
    ];
@endphp

<header class="sticky top-0 z-50 flex h-[72px] items-center gap-3 border-b border-neutral-200 bg-neutral-0/90 px-4 backdrop-blur-sm sm:px-6">
    <button type="button"
            class="relative h-10 w-10 shrink-0 items-center justify-center rounded-xl text-title transition-colors hover:bg-neutral-100 inline-flex lg:hidden"
            aria-label="{{ __('Open menu') }}"
            data-sidebar-open>
        <i class="ph ph-list text-xl"></i>
    </button>

    <h1 class="heading-4 truncate">{{ $title }}</h1>

    <div class="ml-auto f-start gap-1 sm:gap-2">
        @if($creditBalance !== null && Route::has($panelKey . '.credits.index'))
            <a href="{{ route($panelKey . '.credits.index') }}" class="badge badge-discover max-sm:hidden">
                <i class="ph-fill ph-coins"></i>
                <span class="numeric">{{ number_format($creditBalance) }}</span> {{ __('credits') }}
            </a>
        @endif

        {{-- Notifications (bell icon) --}}
        <div class="relative" x-data="notificationBell({{ Js::from($bellConfig) }})">
            <button @click="togglePanel()"
                    class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-title transition-colors hover:bg-neutral-100"
                    aria-label="{{ __('Notifications') }}"
                    aria-haspopup="true"
                    :aria-expanded="isOpen ? 'true' : 'false'">
                <i class="ph ph-bell text-xl"></i>
                <span x-show="unreadCount > 0" x-cloak
                      class="absolute top-2 right-2 h-2 w-2 rounded-full bg-accent"
                      aria-hidden="true"></span>
            </button>

            <div x-show="isOpen" x-cloak
                 @click.outside="isOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="bg-neutral-0 absolute right-0 top-full z-50 mt-2 w-80 overflow-hidden rounded-xl border border-neutral-200 shadow-xl md:w-96">

                <div class="flex items-center justify-between border-b border-neutral-100 p-4">
                    <h4 class="font-title font-bold text-title">{{ __('Notifications') }}</h4>
                    <button @click="markAllRead()" x-show="unreadCount > 0" class="text-xs font-medium text-primary hover:underline">
                        {{ __('Mark all read') }}
                    </button>
                </div>

                <div class="max-h-80 overflow-y-auto scrollbar-hide">
                    <div x-show="loading" class="flex items-center justify-center p-8">
                        <div class="datatable-spinner"></div>
                    </div>

                    <template x-if="!loading">
                        <div>
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.url || 'javascript:void(0)'"
                                   @click="handleNotificationClick(n, $event)"
                                   class="flex cursor-pointer gap-3 border-b border-neutral-50 p-4 transition-colors hover:bg-neutral-50"
                                   :class="{ 'bg-discover-soft/40': !n.read_at }">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                                         :class="n.icon_bg || 'bg-primary/10 text-primary'">
                                        <i class="ph" :class="n.icon || 'ph-bell'"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-title" x-text="n.title"></p>
                                        <p class="truncate text-xs text-body" x-text="n.body"></p>
                                        <p class="mt-1 text-xs text-neutral-400" x-text="n.time_ago"></p>
                                    </div>
                                    <div x-show="!n.read_at" class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-primary"></div>
                                </a>
                            </template>

                            <div x-show="notifications.length === 0" class="p-8 text-center text-sm text-neutral-400">
                                {{ __('No notifications') }}
                            </div>
                        </div>
                    </template>
                </div>

                <a :href="viewAllUrl" class="block border-t border-neutral-100 py-3 text-center text-sm font-medium text-primary transition-colors hover:bg-neutral-50">
                    {{ __('See all notifications') }}
                </a>
            </div>
        </div>

        {{-- Account menu --}}
        <div class="menu" data-dropdown>
            <button type="button"
                    class="f-center h-10 w-10 shrink-0 overflow-hidden rounded-full bg-primary text-[0.8125rem] font-semibold text-neutral-0"
                    aria-label="{{ __('Account menu') }}"
                    data-dropdown-toggle
                    aria-haspopup="true"
                    aria-expanded="false">
                @if($currentUser?->avatar_url)
                    <img src="{{ $currentUser->avatar_url }}" alt="{{ $currentUser->name ?? __('User') }}" class="h-full w-full object-cover" />
                @else
                    {{ $initials }}
                @endif
            </button>
            <div class="menu__panel" data-dropdown-panel>
                @if(Route::has($panelKey . '.profile.edit'))
                    <a href="{{ route($panelKey . '.profile.edit') }}" class="menu__item">
                        <i class="ph ph-user"></i>{{ __('Profile') }}
                    </a>
                @endif
                @if(Route::has($panelKey . '.settings.index'))
                    <a href="{{ route($panelKey . '.settings.index') }}" class="menu__item">
                        <i class="ph ph-gear-six"></i>{{ __('Account settings') }}
                    </a>
                @endif
                @if(Route::has($panelKey . '.credits.index'))
                    <a href="{{ route($panelKey . '.credits.index') }}" class="menu__item">
                        <i class="ph ph-coins"></i>{{ __('Credits & billing') }}
                    </a>
                @endif
                @if(Route::has($panelKey . '.support-tickets.index'))
                    <a href="{{ route($panelKey . '.support-tickets.index') }}" class="menu__item">
                        <i class="ph ph-lifebuoy"></i>{{ __('Support') }}
                    </a>
                @endif
                <div class="menu__sep"></div>
                <form method="POST" action="{{ $panelKey === 'admin' ? route('admin.logout') : route('logout') }}">
                    @csrf
                    <button type="submit" class="menu__item menu__item--danger">
                        <i class="ph ph-sign-out"></i>{{ __('Sign out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

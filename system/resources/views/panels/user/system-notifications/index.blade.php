<x-layouts.user :title="__('Notifications')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Notifications') }}</h2>
            <p class="m-text mt-1">
                {{ __('What happened while you were away — finished searches, AI results, and anything about your credit balance.') }}
            </p>
        </div>

        @if($notifications->isNotEmpty())
            <form method="POST" action="{{ route('user.system-notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm shrink-0">
                    <span class="btn__label">
                        <span>{{ __('Mark all read') }}</span>
                        <span aria-hidden="true">{{ __('Mark all read') }}</span>
                    </span>
                    <i class="ph ph-checks"></i>
                </button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="empty">
            <span class="empty__icon" aria-hidden="true">
                <i class="ph ph-bell-slash"></i>
            </span>
            <h2 class="empty__title">{{ __('Nothing yet') }}</h2>
            <p class="empty__body">
                {{ __('Alerts land here when a search finishes, the AI scores leads, or your credits run low.') }}
            </p>
            <a href="{{ route('user.search.new') }}" class="btn btn-primary btn-sm">
                <span class="btn__label">
                    <span>{{ __('New search') }}</span>
                    <span aria-hidden="true">{{ __('New search') }}</span>
                </span>
                <i class="ph ph-arrow-right"></i>
            </a>
        </div>
    @else
        <div class="panel" data-list>
            <nav class="app-tablist" aria-label="{{ __('Filter notifications') }}">
                <button
                    type="button"
                    class="app-tab is-active"
                    data-list-tab="all"
                    aria-current="page"
                >
                    {{ __('All') }}
                    <span class="app-tab__count">{{ $notifications->total() }}</span>
                </button>
                <button type="button" class="app-tab" data-list-tab="unread">
                    {{ __('Unread') }}
                    <span class="app-tab__count">{{ $unreadCount }}</span>
                </button>
            </nav>

            <div class="list-toolbar">
                <label for="n-search" class="sr-only">{{ __('Search notifications') }}</label>
                <div class="search-field">
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input
                        type="search"
                        id="n-search"
                        class="form-input"
                        placeholder="{{ __('Search notifications') }}"
                        data-list-search
                    />
                </div>

                <div
                    class="menu shrink-0"
                    data-dropdown
                    data-dropdown-select
                    data-list-filter="kind"
                    data-value="all"
                >
                    <button
                        type="button"
                        class="filter-btn"
                        data-dropdown-toggle
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="ph ph-funnel-simple" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any type') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">
                            {{ __('Any type') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="search">
                            {{ __('Searches') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="ai">
                            {{ __('AI results') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="credits">
                            {{ __('Credits') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div data-list-table>
                @foreach($notifications as $notification)
                    @php
                        $iconBg = match ($notification->getType()) {
                            'success' => 'bg-success/10 text-success',
                            'warning' => 'bg-warning/10 text-warning',
                            'danger' => 'bg-error/10 text-error',
                            default => 'bg-primary/10 text-primary',
                        };
                        $date = $notification->created_at;
                        if ($date->isToday()) {
                            $dateLabel = __('Today') . ', ' . $date->format('H:i');
                        } elseif ($date->isYesterday()) {
                            $dateLabel = __('Yesterday') . ', ' . $date->format('H:i');
                        } else {
                            $dateLabel = $date->format('j M') . ', ' . $date->format('H:i');
                        }
                    @endphp

                    @if($notification->getUrl())
                        <a
                            href="{{ $notification->getUrl() }}"
                            class="notif {{ ! $notification->isRead() ? 'is-unread' : '' }}"
                            data-list-key="{{ $notification->isRead() ? 'read' : 'unread' }}"
                            data-kind="{{ $notification->getKind() }}"
                        >
                    @else
                        <div
                            class="notif {{ ! $notification->isRead() ? 'is-unread' : '' }}"
                            data-list-key="{{ $notification->isRead() ? 'read' : 'unread' }}"
                            data-kind="{{ $notification->getKind() }}"
                        >
                    @endif
                        <span class="notif__icon {{ $iconBg }}">
                            <i class="ph {{ $notification->getIcon() }}" aria-hidden="true"></i>
                        </span>
                        <span class="notif__body">
                            <span class="text-[0.875rem] font-semibold text-title">
                                {{ $notification->getTitle() }}
                            </span>
                            <span class="mt-0.5 text-[0.8125rem] leading-[1.55] text-body">
                                {{ $notification->getBody() }}
                            </span>
                            <span class="mt-1 text-[0.8125rem] text-neutral-600">{{ $dateLabel }}</span>
                        </span>
                        @if(! $notification->isRead())
                            <span
                                class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent"
                                aria-label="{{ __('Unread') }}"
                            ></span>
                        @endif
                    @if($notification->getUrl())
                        </a>
                    @else
                        </div>
                    @endif
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="px-4 py-3">
                    <x-tables.pagination :paginator="$notifications" />
                </div>
            @endif

            <div class="no-results is-hidden" data-list-empty>
                <span class="no-results__icon" aria-hidden="true">
                    <i class="ph ph-magnifying-glass"></i>
                </span>
                <p class="no-results__title">{{ __('Nothing matches') }}</p>
                <p class="no-results__body">
                    {{ __('Try a different type, or clear the search to see everything.') }}
                </p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('user.settings.email-preferences.update') }}" class="panel mt-4 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="panel__head">
            <h3 class="panel__title">{{ __('What appears here') }}</h3>
            <a href="{{ route('user.settings.index') }}" class="panel__link">
                {{ __('Email preferences') }}
                <i class="ph ph-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="px-4 pt-1 pb-2 sm:px-5">
            <div class="setting-row">
                <div class="setting-row__text">
                    <label for="n-search-done" class="setting-row__label">
                        {{ __('Email when a search finishes') }}
                    </label>
                    <p class="setting-row__hint">
                        {{ __('Including searches that stop early, and what you were charged.') }}
                    </p>
                </div>
                <input
                    type="checkbox"
                    id="n-search-done"
                    name="search_done"
                    value="1"
                    class="switch"
                    @checked($emailPreferences['search_done'] ?? true)
                />
            </div>

            <div class="setting-row">
                <div class="setting-row__text">
                    <label for="n-ai" class="setting-row__label">
                        {{ __('Weekly digest email') }}
                    </label>
                    <p class="setting-row__hint">
                        {{ __('A short summary of saved leads, exports, and usage trends.') }}
                    </p>
                </div>
                <input
                    type="checkbox"
                    id="n-ai"
                    name="weekly"
                    value="1"
                    class="switch"
                    @checked($emailPreferences['weekly'] ?? false)
                />
            </div>

            <div class="setting-row">
                <div class="setting-row__text">
                    <label for="n-credits" class="setting-row__label">
                        {{ __('Email when credits run low') }}
                    </label>
                    <p class="setting-row__hint">
                        {{ __('At') }}
                        <span class="numeric">10%</span>
                        {{ __('of your monthly allowance, so a search does not stop halfway.') }}
                    </p>
                </div>
                <input
                    type="checkbox"
                    id="n-credits"
                    name="low_credits"
                    value="1"
                    class="switch"
                    @checked($emailPreferences['low_credits'] ?? true)
                />
            </div>

            <div class="setting-row">
                <div class="setting-row__text">
                    <label for="n-replies" class="setting-row__label">
                        {{ __('Product update email') }}
                    </label>
                    <p class="setting-row__hint">
                        {{ __('Occasional release notes and feature improvements.') }}
                    </p>
                </div>
                <input
                    type="checkbox"
                    id="n-replies"
                    name="product"
                    value="1"
                    class="switch"
                    @checked($emailPreferences['product'] ?? false)
                />
            </div>
        </div>

        <div class="border-t border-border px-4 py-3 text-right sm:px-5">
            <button type="submit" class="btn btn-primary btn-sm">
                <span class="btn__label">
                    <span>{{ __('Save preferences') }}</span>
                    <span aria-hidden="true">{{ __('Save preferences') }}</span>
                </span>
                <i class="ph ph-check"></i>
            </button>
        </div>
    </form>
</x-layouts.user>

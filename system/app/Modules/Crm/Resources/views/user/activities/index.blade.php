<x-layouts.user :title="__('Notes & activities')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Notes & activities') }}</h2>
        </div>

        <button type="button" class="btn btn-primary btn-sm shrink-0" data-modal-open="activityModal" @disabled($leads->isEmpty())>
            <span class="btn__label">
                <span>{{ __('Add note') }}</span>
                <span aria-hidden="true">{{ __('Add note') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </button>
    </div>

    <div class="panel{{ $activities->isEmpty() ? ' is-hidden' : '' }}" data-list>
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Activity') }}</h3>
            <span class="panel__meta">
                {{ __('Across') }} <span class="numeric">{{ $leadCount }}</span> {{ __('leads') }}
            </span>
        </div>

        <nav class="app-tablist" aria-label="{{ __('Filter activity') }}">
            <button type="button" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }} <span class="app-tab__count">{{ $activities->count() }}</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="mine">
                {{ __('By me') }} <span class="app-tab__count">{{ $activities->whereNotNull('caused_by_user_id')->count() }}</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="auto">
                {{ __('By LeadAtlas') }} <span class="app-tab__count">{{ $activities->whereNull('caused_by_user_id')->count() }}</span>
            </button>
        </nav>

        <div class="list-toolbar">
            <label for="a-search" class="sr-only">{{ __('Search activity') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="a-search" class="form-input" placeholder="{{ __('Search by lead or note') }}" data-list-search />
            </div>

            <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="kind" data-value="all">
                <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                    <i class="ph ph-funnel-simple" aria-hidden="true"></i>
                    <span data-dropdown-label>{{ __('Any type') }}</span>
                    <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                </button>

                <div class="menu__panel" data-dropdown-panel>
                    <button type="button" class="menu__item is-selected" data-value="all">{{ __('Any type') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                    <button type="button" class="menu__item" data-value="note">{{ __('Notes') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                    <button type="button" class="menu__item" data-value="call">{{ __('Calls') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                    <button type="button" class="menu__item" data-value="email">{{ __('Emails') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                    <button type="button" class="menu__item" data-value="stage">{{ __('Stage changes') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                    <button type="button" class="menu__item" data-value="ai">{{ __('AI results') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>

        <div class="acts" data-list-table>
            @foreach ($activityGroups as $day => $items)
                <p class="mt-4 mb-1 text-[0.6875rem] font-semibold tracking-[0.08em] text-neutral-500 uppercase first:mt-2" data-feed-day>
                    {{ $day }}
                </p>

                @foreach ($items as $item)
                    <article class="act" data-list-key="{{ $item['list_key'] }}" data-kind="{{ $item['kind'] }}">
                        <span class="act__dot {{ $item['is_auto'] ? 'act__dot--ai' : 'act__dot--act' }}" aria-hidden="true">
                            <i class="ph {{ $item['icon'] }}"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="act__title">{{ $item['title'] }}</p>
                            <p class="act__meta">
                                @if ($item['lead'])
                                    <a href="{{ route('user.leads.show', $item['lead']) }}" class="font-medium text-primary hover:underline">
                                        {{ $item['lead_name'] }}
                                    </a>
                                @else
                                    <span class="font-medium text-primary hover:underline act__lead--none">{{ __('Lead') }}</span>
                                @endif
                                <span aria-hidden="true">·</span>
                                {{ __('by :actor', ['actor' => $item['actor']]) }}
                                <span aria-hidden="true">·</span>
                                <time datetime="{{ $item['created_at']?->toDateString() }}">{{ $item['created_at']?->format('H:i') }}</time>
                            </p>
                        </div>
                    </article>
                @endforeach
            @endforeach
        </div>

        <div class="no-results is-hidden" data-list-empty>
            <span class="no-results__icon" aria-hidden="true">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <p class="no-results__title">{{ __('Nothing matches') }}</p>
            <p class="no-results__body">{{ __('Try a different type, or clear the search to see everything.') }}</p>
        </div>
    </div>

    <section class="panel empty" @if (! $activities->isEmpty()) hidden @endif>
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-clock-counter-clockwise"></i>
        </span>
        <h2 class="empty__title">{{ __('No activity yet') }}</h2>
        <p class="empty__body">
            {{ __('Notes, calls, and everything the AI does land here as soon as you start working your leads.') }}
        </p>
        <a href="{{ route('user.leads.index') }}" class="btn btn-primary btn-sm">
            <span class="btn__label">
                <span>{{ __('Open your leads') }}</span>
                <span aria-hidden="true">{{ __('Open your leads') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </section>

    @push('modals')
        <div class="modal" id="activityModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-lg p-6" role="dialog" aria-modal="true" aria-labelledby="activityModalTitle">
                <form action="{{ route('user.activities.store') }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="activityModalTitle">{{ __('Add to the timeline') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('A note to yourself, or a record of a call you made. Neither costs a credit.') }}
                    </p>

                    <div class="act__form">
                        <div>
                            <label for="a-type" class="form-label">{{ __('Type') }}</label>
                            <select id="a-type" name="type" class="form-input">
                                <option value="note" selected>{{ __('Note') }}</option>
                                <option value="call">{{ __('Call') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="a-lead" class="form-label">{{ __('Lead') }}</label>
                            <select id="a-lead" name="lead_id" class="form-input" required>
                                <option value="">{{ __('Choose a lead') }}</option>
                                @foreach ($leads as $lead)
                                    <option value="{{ $lead->id }}">{{ $lead->place?->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="a-body" class="form-label">{{ __('What happened') }}</label>
                        <textarea id="a-body" name="body" class="form-input" rows="4" placeholder="{{ __('Spoke to the practice manager — call back Thursday.') }}" required></textarea>
                        <p class="form-hint">{{ __('Only your team sees this.') }}</p>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            <span class="btn__label"><span>{{ __('Cancel') }}</span><span aria-hidden="true">{{ __('Cancel') }}</span></span>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label"><span>{{ __('Add to timeline') }}</span><span aria-hidden="true">{{ __('Add to timeline') }}</span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endpush
</x-layouts.user>

<x-layouts.user :title="__('Notes & activities')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Notes & activities') }}</h2>
            <p class="m-text mt-1">
                {{ __('Everything that has happened across your leads — what you did, and what the platform did for you.') }}
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary btn-sm shrink-0"
            data-modal-open="activityModal"
        >
            <span class="btn__label">
                <span>{{ __('Add note') }}</span>
                <span aria-hidden="true">{{ __('Add note') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </button>
    </div>

    <div class="panel" data-list>
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Activity') }}</h3>
            <span class="panel__meta">
                {{ __('Across') }} <span class="numeric">5</span> {{ __('leads') }}
            </span>
        </div>

        <nav class="app-tablist" aria-label="{{ __('Filter activity') }}">
            <button type="button" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }}
                <span class="app-tab__count">9</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="mine">
                {{ __('By me') }}
                <span class="app-tab__count">5</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="auto">
                {{ __('By LeadAtlas') }}
                <span class="app-tab__count">4</span>
            </button>
        </nav>

        <div class="list-toolbar">
            <label for="a-search" class="sr-only">{{ __('Search activity') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    id="a-search"
                    class="form-input"
                    placeholder="{{ __('Search by lead or note') }}"
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
                    <button type="button" class="menu__item" data-value="note">
                        {{ __('Notes') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="call">
                        {{ __('Calls') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="email">
                        {{ __('Emails') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="stage">
                        {{ __('Stage changes') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="ai">
                        {{ __('AI results') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="acts" data-list-table>
            <p class="mt-4 mb-1 text-[0.6875rem] font-semibold tracking-[0.08em] text-neutral-500 uppercase first:mt-2" data-feed-day>
                {{ __('Today') }}
            </p>

            <article class="act" data-list-key="mine" data-kind="call">
                <span class="act__dot act__dot--act" aria-hidden="true">
                    <i class="ph ph-phone"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">
                        {{ __('Called the front desk — practice manager is Dana, back Thursday') }}
                    </p>
                    <p class="act__meta">
                        <a href="#" class="font-medium text-primary hover:underline">
                            {{ __('Barton Springs Dental') }}
                        </a>
                        <span aria-hidden="true">·</span>
                        {{ __('by Amara Rivera') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-21">09:20</time>
                    </p>
                </div>
            </article>

            <article class="act" data-list-key="auto" data-kind="ai">
                <span class="act__dot act__dot--ai" aria-hidden="true">
                    <i class="ph ph-sparkle"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">
                        {{ __('Scored') }} <span class="numeric">120</span> {{ __('new leads') }}
                    </p>
                    <p class="act__meta">
                        <span class="font-medium text-primary hover:underline act__lead--none">
                            {{ __('dentists in Austin, TX') }}
                        </span>
                        <span aria-hidden="true">·</span>
                        {{ __('by LeadAtlas') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-21">09:14</time>
                    </p>
                </div>
            </article>

            <article class="act" data-list-key="auto" data-kind="ai">
                <span class="act__dot act__dot--ai" aria-hidden="true">
                    <i class="ph ph-sparkle"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">
                        {{ __('Drafted an opening line for Zilker Smile Studio') }}
                    </p>
                    <p class="act__meta">
                        <a href="#" class="font-medium text-primary hover:underline">
                            {{ __('Zilker Smile Studio') }}
                        </a>
                        <span aria-hidden="true">·</span>
                        {{ __('by LeadAtlas') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-21">09:16</time>
                    </p>
                </div>
            </article>

            <p class="mt-4 mb-1 text-[0.6875rem] font-semibold tracking-[0.08em] text-neutral-500 uppercase first:mt-2" data-feed-day>
                {{ __('Yesterday') }}
            </p>

            <article class="act" data-list-key="mine" data-kind="email">
                <span class="act__dot act__dot--act" aria-hidden="true">
                    <i class="ph ph-paper-plane-tilt"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">{{ __('Sent the opening email you approved') }}</p>
                    <p class="act__meta">
                        <a href="#" class="font-medium text-primary hover:underline">
                            {{ __('Lamar Family Dentistry') }}
                        </a>
                        <span aria-hidden="true">·</span>
                        {{ __('by Amara Rivera') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-20">16:05</time>
                    </p>
                </div>
            </article>

            <article class="act" data-list-key="mine" data-kind="stage">
                <span class="act__dot act__dot--act" aria-hidden="true">
                    <i class="ph ph-kanban"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">
                        {{ __('Moved to') }}
                        <span class="status status--qualified align-middle">
                            {{ __('Qualified') }}
                        </span>
                    </p>
                    <p class="act__meta">
                        <a href="#" class="font-medium text-primary hover:underline">
                            {{ __('Zilker Smile Studio') }}
                        </a>
                        <span aria-hidden="true">·</span>
                        {{ __('by Amara Rivera') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-20">14:32</time>
                    </p>
                </div>
            </article>

            <p class="mt-4 mb-1 text-[0.6875rem] font-semibold tracking-[0.08em] text-neutral-500 uppercase first:mt-2" data-feed-day>
                {{ __('19 July') }}
            </p>

            <article class="act" data-list-key="mine" data-kind="stage">
                <span class="act__dot act__dot--act" aria-hidden="true">
                    <i class="ph ph-tag"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">{{ __('Tagged “High value”') }}</p>
                    <p class="act__meta">
                        <a href="#" class="font-medium text-primary hover:underline">
                            {{ __('Barton Springs Dental') }}
                        </a>
                        <span aria-hidden="true">·</span>
                        {{ __('by Amara Rivera') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-19">11:48</time>
                    </p>
                </div>
            </article>

            <article class="act" data-list-key="auto" data-kind="ai">
                <span class="act__dot act__dot--ai" aria-hidden="true">
                    <i class="ph ph-sparkle"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">
                        {{ __('Scored') }} <span class="numeric">92</span> {{ __('— busy practice with no way to book online') }}
                    </p>
                    <p class="act__meta">
                        <a href="#" class="font-medium text-primary hover:underline">
                            {{ __('Barton Springs Dental') }}
                        </a>
                        <span aria-hidden="true">·</span>
                        {{ __('by LeadAtlas') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-19">09:31</time>
                    </p>
                </div>
            </article>

            <article class="act" data-list-key="mine" data-kind="note">
                <span class="act__dot" aria-hidden="true">
                    <i class="ph ph-note"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">
                        {{ __('Their site has not been touched since 2019 — lead with the booking angle') }}
                    </p>
                    <p class="act__meta">
                        <a href="#" class="font-medium text-primary hover:underline">
                            {{ __('Barton Springs Dental') }}
                        </a>
                        <span aria-hidden="true">·</span>
                        {{ __('by Amara Rivera') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-19">09:40</time>
                    </p>
                </div>
            </article>

            <p class="mt-4 mb-1 text-[0.6875rem] font-semibold tracking-[0.08em] text-neutral-500 uppercase first:mt-2" data-feed-day>
                {{ __('18 July') }}
            </p>

            <article class="act" data-list-key="auto" data-kind="ai">
                <span class="act__dot act__dot--ai" aria-hidden="true">
                    <i class="ph ph-list-magnifying-glass"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="act__title">
                        {{ __('Analysed') }} <span class="numeric">43</span> {{ __('businesses in bulk') }}
                    </p>
                    <p class="act__meta">
                        <span class="font-medium text-primary hover:underline act__lead--none">
                            {{ __('med spas in Phoenix, AZ') }}
                        </span>
                        <span aria-hidden="true">·</span>
                        {{ __('by LeadAtlas') }}
                        <span aria-hidden="true">·</span>
                        <time datetime="2026-07-18">16:40</time>
                    </p>
                </div>
            </article>
        </div>

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

    <div class="empty is-hidden">
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
    </div>

    @push('modals')
        <div class="modal" id="activityModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-lg p-6" role="dialog" aria-modal="true" aria-labelledby="activityModalTitle">
                <form action="#" method="post">
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
                                <option value="1">{{ __('Barton Springs Dental') }}</option>
                                <option value="2">{{ __('Zilker Smile Studio') }}</option>
                                <option value="3">{{ __('Lamar Family Dentistry') }}</option>
                                <option value="4">{{ __('Hyde Park Dental Care') }}</option>
                                <option value="5">{{ __('Desert Bloom Med Spa') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="a-body" class="form-label">{{ __('What happened') }}</label>
                        <textarea
                            id="a-body"
                            name="body"
                            class="form-input"
                            rows="4"
                            placeholder="{{ __('Spoke to the practice manager — call back Thursday.') }}"
                            required
                        ></textarea>
                        <p class="form-hint">{{ __('Only your team sees this.') }}</p>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            <span class="btn__label">
                                <span>{{ __('Cancel') }}</span>
                                <span aria-hidden="true">{{ __('Cancel') }}</span>
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label">
                                <span>{{ __('Add to timeline') }}</span>
                                <span aria-hidden="true">{{ __('Add to timeline') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endpush
</x-layouts.user>

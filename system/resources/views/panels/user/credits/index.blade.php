<x-layouts.user :title="__('Credits & billing')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Credit usage') }}</h2>
            <p class="m-text mt-1">
                {{ __('Every credit this account has spent, and every one returned. Searching is free — credits are spent when a business is enriched.') }}
            </p>
        </div>

        <a href="{{ route('user.credits.buy') }}" class="btn btn-accent btn-sm shrink-0">
            <span class="btn__label">
                <span>{{ __('Buy credits') }}</span>
                <span aria-hidden="true">{{ __('Buy credits') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </a>
    </div>

    <div class="kpis">
        <article class="kpi">
            <p class="kpi__label">{{ __('Credits remaining') }}</p>
            <p class="kpi__value numeric">2,480</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    {{ __('of') }} <span class="numeric">5,000</span> {{ __('this month') }}
                </span>
                <a href="{{ route('user.credits.buy') }}" class="kpi__link">{{ __('Buy more') }}</a>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Spent this month') }}</p>
            <p class="kpi__value numeric">2,547</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    {{ __('across') }} <span class="numeric">18</span> {{ __('searches') }}
                </span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Returned') }}</p>
            <p class="kpi__value numeric">67</p>
            <p class="kpi__foot">
                <span class="kpi__note">{{ __('no contact found') }}</span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Renews') }}</p>
            <p class="kpi__value">1 {{ __('Aug') }}</p>
            <p class="kpi__foot">
                <span class="kpi__note">{{ __('unused credits roll over') }}</span>
            </p>
        </article>
    </div>

    <section class="panel">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Credits used per day') }}</h3>
            <span class="panel__meta">{{ __('Last 14 days') }}</span>
        </div>

        <div class="panel__body">
            <canvas
                data-chart="bar"
                data-chart-values="0,0,324,0,141,150,0,131,0,190,88,154,172,312"
                data-chart-labels="8 Jul,9 Jul,10 Jul,11 Jul,12 Jul,13 Jul,14 Jul,15 Jul,16 Jul,17 Jul,18 Jul,19 Jul,20 Jul,21 Jul"
                aria-label="{{ __('Credits used per day over the last 14 days') }}"
            ></canvas>
        </div>
    </section>

    <div class="panel" data-list>
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Itemised usage') }}</h3>
            <span class="panel__meta">
                <span class="numeric">9</span> {{ __('entries') }}
            </span>
        </div>

        <nav class="app-tablist" aria-label="{{ __('Filter usage') }}">
            <button type="button" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }}
                <span class="app-tab__count">9</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="spend">
                {{ __('Spent') }}
                <span class="app-tab__count">7</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="return">
                {{ __('Returned') }}
                <span class="app-tab__count">2</span>
            </button>
        </nav>

        <div class="list-toolbar">
            <label for="c-search" class="sr-only">{{ __('Search usage') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    id="c-search"
                    class="form-input"
                    placeholder="{{ __('Search by action or keyword') }}"
                    data-list-search
                />
            </div>

            <div
                class="menu shrink-0"
                data-dropdown
                data-dropdown-select
                data-list-filter="period"
                data-value="all"
            >
                <button
                    type="button"
                    class="filter-btn"
                    data-dropdown-toggle
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <i class="ph ph-calendar-blank" aria-hidden="true"></i>
                    <span data-dropdown-label>{{ __('This month') }}</span>
                    <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                </button>

                <div class="menu__panel" data-dropdown-panel>
                    <button type="button" class="menu__item is-selected" data-value="all">
                        {{ __('This month') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="week">
                        {{ __('Last 7 days') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="prev">
                        {{ __('Last month') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="tbl-wrap">
            <table class="d-table d-table--cards" data-list-table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('Action') }}</th>
                        <th scope="col">{{ __('Search') }}</th>
                        <th scope="col">{{ __('When') }}</th>
                        <th scope="col" class="text-right">{{ __('Credits') }}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr data-list-key="spend" data-period="week">
                        <td data-card-title>
                            <span class="ledger__act">
                                <i class="ph ph-users-three" aria-hidden="true"></i>
                                {{ __('Contact enrichment') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">312</span> {{ __('businesses enriched') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('orthodontists in Dallas, TX') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-20">{{ __('Today') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">−312</td>
                    </tr>

                    <tr data-list-key="spend" data-period="week">
                        <td data-card-title>
                            <span class="ledger__act">
                                <i class="ph ph-users-three" aria-hidden="true"></i>
                                {{ __('Contact enrichment') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">172</span> {{ __('businesses enriched') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('dentists in Austin, TX') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-19">19 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">−172</td>
                    </tr>

                    <tr data-list-key="return" data-period="week">
                        <td data-card-title>
                            <span class="ledger__act ledger__act--return">
                                <i class="ph ph-arrow-u-down-left" aria-hidden="true"></i>
                                {{ __('Returned — no contact found') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">18</span> {{ __('businesses had no reachable contact') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('dentists in Austin, TX') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-19">19 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric font-semibold text-success text-right">+18</td>
                    </tr>

                    <tr data-list-key="spend" data-period="week">
                        <td data-card-title>
                            <span class="ledger__act">
                                <i class="ph ph-users-three" aria-hidden="true"></i>
                                {{ __('Contact enrichment') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">88</span> {{ __('businesses enriched') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('med spas in Phoenix, AZ') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-18">18 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">−88</td>
                    </tr>

                    <tr data-list-key="spend" data-period="week">
                        <td data-card-title>
                            <span class="ledger__act ledger__act--ai">
                                <i class="ph ph-sparkle" aria-hidden="true"></i>
                                {{ __('AI business analysis') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">43</span> {{ __('businesses analysed in bulk') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('med spas in Phoenix, AZ') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-18">18 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">−43</td>
                    </tr>

                    <tr data-list-key="spend" data-period="month">
                        <td data-card-title>
                            <span class="ledger__act">
                                <i class="ph ph-users-three" aria-hidden="true"></i>
                                {{ __('Contact enrichment') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">141</span> {{ __('businesses enriched') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('chiropractors in Denver, CO') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-16">16 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">−141</td>
                    </tr>

                    <tr data-list-key="return" data-period="month">
                        <td data-card-title>
                            <span class="ledger__act ledger__act--return">
                                <i class="ph ph-arrow-u-down-left" aria-hidden="true"></i>
                                {{ __('Returned — no contact found') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">9</span> {{ __('businesses had no reachable contact') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('chiropractors in Denver, CO') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-16">16 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric font-semibold text-success text-right">+9</td>
                    </tr>

                    <tr data-list-key="spend" data-period="month">
                        <td data-card-title>
                            <span class="ledger__act">
                                <i class="ph ph-users-three" aria-hidden="true"></i>
                                {{ __('Contact enrichment') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">298</span> {{ __('businesses enriched') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('law firms in Seattle, WA') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-14">14 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">−298</td>
                    </tr>

                    <tr data-list-key="spend" data-period="month">
                        <td data-card-title>
                            <span class="ledger__act ledger__act--ai">
                                <i class="ph ph-sparkle" aria-hidden="true"></i>
                                {{ __('AI email drafts') }}
                            </span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">26</span> {{ __('opening lines drafted') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Search') }}">
                            <a href="{{ route('user.search.history') }}" class="d-table__id">
                                {{ __('law firms in Seattle, WA') }}
                            </a>
                        </td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-14">14 {{ __('Jul') }}</time>
                        </td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">−26</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="ledger__foot border-t border-neutral-200 bg-neutral-50">
            <p class="text-[0.875rem] text-body">
                {{ __('Earlier this month') }}
                <span class="d-table__muted">· {{ __('1–13 Jul, not listed') }}</span>
            </p>
            <p class="shrink-0 text-[0.9375rem] font-medium text-title numeric">−1,467</p>
        </div>

        <div class="ledger__total">
            <p class="font-title text-[0.9375rem] font-bold text-title">{{ __('Remaining') }}</p>
            <p class="font-title text-[1.125rem] font-bold text-title numeric">2,480</p>
        </div>

        <div class="no-results is-hidden" data-list-empty>
            <span class="no-results__icon" aria-hidden="true">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <p class="no-results__title">{{ __('Nothing matches that filter') }}</p>
            <p class="no-results__body">
                {{ __('Try a different period, or clear the search to see every entry.') }}
            </p>
        </div>
    </div>

    <p class="ledger__note">
        <i class="ph ph-info" aria-hidden="true"></i>
        <span>
            {{ __('Running a search and viewing results costs nothing — a credit is spent only when a business is enriched, and returned if no contact is found. Re-opening a lead, re-reading its analysis, and exporting are always free.') }}
        </span>
    </p>
</x-layouts.user>

<x-layouts.user :title="__('Email campaigns')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Email campaigns') }}</h2>
            <p class="m-text mt-1">
                {{ __('Sequences built from drafts you have already read. Nothing goes out until you approve it.') }}
            </p>
        </div>

        <a href="#" class="btn btn-primary btn-sm shrink-0">
            <span class="btn__label">
                <span>{{ __('New campaign') }}</span>
                <span aria-hidden="true">{{ __('New campaign') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </a>
    </div>

    <div class="kpis">
        <article class="kpi">
            <p class="kpi__label">{{ __('Awaiting your review') }}</p>
            <p class="kpi__value numeric">1</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    <span class="numeric">48</span> {{ __('messages to read') }}
                </span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Sending now') }}</p>
            <p class="kpi__value numeric">1</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    <span class="numeric">96</span> {{ __('of') }}
                    <span class="numeric">120</span> {{ __('sent') }}
                </span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Opened') }}</p>
            <p class="kpi__value numeric">41%</p>
            <p class="kpi__foot">
                <span class="kpi__note">{{ __('across all sent campaigns') }}</span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Replied') }}</p>
            <p class="kpi__value numeric">12%</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    <span class="numeric">31</span> {{ __('replies to follow up') }}
                </span>
            </p>
        </article>
    </div>

    <div class="panel" data-list>
        <div class="panel__head">
            <h3 class="panel__title">{{ __('All campaigns') }}</h3>
            <span class="panel__meta">
                {{ __('Building a new one starts from your approved drafts') }}
            </span>
        </div>

        <nav class="app-tablist" aria-label="{{ __('Filter campaigns') }}">
            <button type="button" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }}
                <span class="app-tab__count">5</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="review">
                {{ __('Needs review') }}
                <span class="app-tab__count">1</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="active">
                {{ __('Sending') }}
                <span class="app-tab__count">1</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="done">
                {{ __('Finished') }}
                <span class="app-tab__count">3</span>
            </button>
        </nav>

        <div class="list-toolbar">
            <label for="c-search" class="sr-only">{{ __('Search campaigns') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    id="c-search"
                    class="form-input"
                    placeholder="{{ __('Search by campaign name') }}"
                    data-list-search
                />
            </div>
        </div>

        <div class="tbl-wrap">
            <table class="d-table d-table--cards" data-list-table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('Campaign') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col" class="text-right">{{ __('Recipients') }}</th>
                        <th scope="col" class="text-right">{{ __('Sent') }}</th>
                        <th scope="col" class="text-right">{{ __('Opened') }}</th>
                        <th scope="col" class="text-right">{{ __('Replied') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                    </tr>
                </thead>

                <tbody>
                    <tr data-list-key="review">
                        <td data-card-title>
                            <a href="#" class="d-table__id">
                                {{ __('Austin dentists — no online booking') }}
                            </a>
                            <p class="d-table__muted text-[0.8125rem]">
                                {{ __('Built from') }} <span class="numeric">48</span> {{ __('approved drafts') }}
                                · <time datetime="2026-07-21">{{ __('today') }}</time>
                            </p>
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--review">
                                <i class="ph ph-eye" aria-hidden="true"></i>
                                {{ __('Needs your review') }}
                            </span>
                        </td>
                        <td data-label="{{ __('Recipients') }}" class="numeric text-right">48</td>
                        <td data-label="{{ __('Sent') }}" class="d-table__muted text-right">—</td>
                        <td data-label="{{ __('Opened') }}" class="d-table__muted text-right">—</td>
                        <td data-label="{{ __('Replied') }}" class="d-table__muted text-right">—</td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <a href="#" class="btn btn-sm btn-primary">
                                    <span class="btn__label">
                                        <span>{{ __('Review') }}</span>
                                        <span aria-hidden="true">{{ __('Review') }}</span>
                                    </span>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="active">
                        <td data-card-title>
                            <a href="#" class="d-table__id">
                                {{ __('Phoenix med spas — spring outreach') }}
                            </a>
                            <p class="d-table__muted text-[0.8125rem]">
                                {{ __('Approved') }} <time datetime="2026-07-20">20 {{ __('Jul') }}</time>
                                · {{ __('sending') }} <span class="numeric">40</span> {{ __('a day') }}
                            </p>
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--running">
                                <span class="live-dot" aria-hidden="true"></span>
                                {{ __('Sending') }}
                            </span>
                        </td>
                        <td data-label="{{ __('Recipients') }}" class="numeric text-right">120</td>
                        <td data-label="{{ __('Sent') }}" class="numeric text-right">96</td>
                        <td data-label="{{ __('Opened') }}" class="numeric text-right">44</td>
                        <td data-label="{{ __('Replied') }}" class="numeric text-right">11</td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button type="button" class="row-icon" aria-label="{{ __('Pause') }} {{ __('Phoenix med spas — spring outreach') }}">
                                    <i class="ph ph-pause" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this campaign?') }}"
                                    data-confirm-body="{{ __('96 messages have already gone out and those stay sent. The remaining 24 will not be sent, and the campaign\'s replies are removed from your reports.') }}"
                                    data-confirm-label="{{ __('Delete campaign') }}"
                                    data-confirm-variant="error"
                                    data-id="2"
                                    aria-label="{{ __('Delete') }} {{ __('Phoenix med spas — spring outreach') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="done">
                        <td data-card-title>
                            <a href="#" class="d-table__id">
                                {{ __('Dallas orthodontists — first touch') }}
                            </a>
                            <p class="d-table__muted text-[0.8125rem]">
                                {{ __('Finished') }} <time datetime="2026-07-14">14 {{ __('Jul') }}</time>
                            </p>
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--done">{{ __('Finished') }}</span>
                        </td>
                        <td data-label="{{ __('Recipients') }}" class="numeric text-right">86</td>
                        <td data-label="{{ __('Sent') }}" class="numeric text-right">86</td>
                        <td data-label="{{ __('Opened') }}" class="numeric text-right">39</td>
                        <td data-label="{{ __('Replied') }}" class="numeric text-right">12</td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button type="button" class="row-icon" aria-label="{{ __('Duplicate') }} {{ __('Dallas orthodontists — first touch') }}">
                                    <i class="ph ph-copy-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this campaign?') }}"
                                    data-confirm-body="{{ __('The 86 messages stay sent — deleting only removes the campaign and its replies from your reports.') }}"
                                    data-confirm-label="{{ __('Delete campaign') }}"
                                    data-confirm-variant="error"
                                    data-id="3"
                                    aria-label="{{ __('Delete') }} {{ __('Dallas orthodontists — first touch') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="done">
                        <td data-card-title>
                            <a href="#" class="d-table__id">
                                {{ __('Seattle law firms — intro') }}
                            </a>
                            <p class="d-table__muted text-[0.8125rem]">
                                {{ __('Finished') }} <time datetime="2026-07-09">9 {{ __('Jul') }}</time>
                            </p>
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--done">{{ __('Finished') }}</span>
                        </td>
                        <td data-label="{{ __('Recipients') }}" class="numeric text-right">142</td>
                        <td data-label="{{ __('Sent') }}" class="numeric text-right">142</td>
                        <td data-label="{{ __('Opened') }}" class="numeric text-right">51</td>
                        <td data-label="{{ __('Replied') }}" class="numeric text-right">8</td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button type="button" class="row-icon" aria-label="{{ __('Duplicate') }} {{ __('Seattle law firms — intro') }}">
                                    <i class="ph ph-copy-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this campaign?') }}"
                                    data-confirm-body="{{ __('The 142 messages stay sent — deleting only removes the campaign and its replies from your reports.') }}"
                                    data-confirm-label="{{ __('Delete campaign') }}"
                                    data-confirm-variant="error"
                                    data-id="4"
                                    aria-label="{{ __('Delete') }} {{ __('Seattle law firms — intro') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="done">
                        <td data-card-title>
                            <a href="#" class="d-table__id">
                                {{ __('Denver chiropractors — follow-up') }}
                            </a>
                            <p class="d-table__muted text-[0.8125rem]">
                                {{ __('Finished') }} <time datetime="2026-06-28">28 {{ __('Jun') }}</time>
                            </p>
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--done">{{ __('Finished') }}</span>
                        </td>
                        <td data-label="{{ __('Recipients') }}" class="numeric text-right">64</td>
                        <td data-label="{{ __('Sent') }}" class="numeric text-right">64</td>
                        <td data-label="{{ __('Opened') }}" class="numeric text-right">22</td>
                        <td data-label="{{ __('Replied') }}" class="numeric text-right">3</td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button type="button" class="row-icon" aria-label="{{ __('Duplicate') }} {{ __('Denver chiropractors — follow-up') }}">
                                    <i class="ph ph-copy-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this campaign?') }}"
                                    data-confirm-body="{{ __('The 64 messages stay sent — deleting only removes the campaign and its replies from your reports.') }}"
                                    data-confirm-label="{{ __('Delete campaign') }}"
                                    data-confirm-variant="error"
                                    data-id="5"
                                    aria-label="{{ __('Delete') }} {{ __('Denver chiropractors — follow-up') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="no-results is-hidden" data-list-empty>
            <span class="no-results__icon" aria-hidden="true">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <p class="no-results__title">{{ __('No campaigns match') }}</p>
            <p class="no-results__body">
                {{ __('Try a different tab, or clear the search to see them all.') }}
            </p>
        </div>
    </div>

    <div class="empty is-hidden">
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-paper-plane-tilt"></i>
        </span>
        <h2 class="empty__title">{{ __('No campaigns yet') }}</h2>
        <p class="empty__body">
            {{ __('A campaign is built from drafts you have already read and approved. Generate a few, then group them into a sequence.') }}
        </p>
        <a href="{{ route('user.email.index') }}" class="btn btn-primary btn-sm">
            <span class="btn__label">
                <span>{{ __('Write a draft') }}</span>
                <span aria-hidden="true">{{ __('Write a draft') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </div>

    <p class="camp__note">
        <i class="ph ph-info" aria-hidden="true"></i>
        <span>
            {{ __('Campaigns cost no credits — the credit was spent when the lead was enriched. Every message is read and approved by you before it sends.') }}
        </span>
    </p>

    @push('modals')
        <div id="confirmDialog" class="modal" role="dialog" aria-modal="true" aria-hidden="true">
            <div class="modal__backdrop"></div>
            <div class="modal__panel max-w-md p-6">
                <h2 class="heading-3" data-confirm-title-target>{{ __('Are you sure?') }}</h2>
                <p class="m-text mt-2" data-confirm-body-target>{{ __('This action cannot be undone.') }}</p>
                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button" class="btn btn-outline" data-confirm-cancel>
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn confirm-accept" data-confirm-accept>
                        {{ __('Confirm') }}
                    </button>
                </div>
            </div>
        </div>
    @endpush
</x-layouts.user>

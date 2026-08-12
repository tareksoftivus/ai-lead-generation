<x-layouts.user :title="__('Email campaigns')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Email campaigns') }}</h2>
        </div>

        <button type="button" class="btn btn-primary btn-sm shrink-0" data-modal-open="campaignModal">
            <span class="btn__label">
                <span>{{ __('New campaign') }}</span>
                <span aria-hidden="true">{{ __('New campaign') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </button>
    </div>

    <div class="kpis">
        <article class="kpi">
            <p class="kpi__label">{{ __('Awaiting your review') }}</p>
            <p class="kpi__value numeric">{{ $kpis['review'] }}</p>
            <p class="kpi__foot"><span class="kpi__note"><span class="numeric">{{ $kpis['messages_to_review'] }}</span> {{ __('messages to read') }}</span></p>
        </article>
        <article class="kpi">
            <p class="kpi__label">{{ __('Sending now') }}</p>
            <p class="kpi__value numeric">{{ $kpis['active'] }}</p>
            <p class="kpi__foot"><span class="kpi__note"><span class="numeric">{{ $kpis['sending_sent'] }}</span> {{ __('of') }} <span class="numeric">{{ $kpis['sending_recipients'] }}</span> {{ __('sent') }}</span></p>
        </article>
        <article class="kpi">
            <p class="kpi__label">{{ __('Opened') }}</p>
            <p class="kpi__value numeric">{{ $kpis['opened_rate'] }}%</p>
            <p class="kpi__foot"><span class="kpi__note">{{ __('across all sent campaigns') }}</span></p>
        </article>
        <article class="kpi">
            <p class="kpi__label">{{ __('Replied') }}</p>
            <p class="kpi__value numeric">{{ $kpis['replied_rate'] }}%</p>
            <p class="kpi__foot"><span class="kpi__note"><span class="numeric">{{ $kpis['replies'] }}</span> {{ __('replies to follow up') }}</span></p>
        </article>
    </div>

    @if ($campaigns->isNotEmpty())
        <div class="panel" data-list>
            <div class="panel__head">
                <h3 class="panel__title">{{ __('All campaigns') }}</h3>
            </div>

            <nav class="app-tablist" aria-label="{{ __('Filter campaigns') }}">
                <button type="button" class="app-tab is-active" data-list-tab="all" aria-current="page">
                    {{ __('All') }} <span class="app-tab__count">{{ $campaigns->count() }}</span>
                </button>
                <button type="button" class="app-tab" data-list-tab="review">
                    {{ __('Needs review') }} <span class="app-tab__count">{{ $campaigns->where('status', 'review')->count() }}</span>
                </button>
                <button type="button" class="app-tab" data-list-tab="active">
                    {{ __('Sending') }} <span class="app-tab__count">{{ $campaigns->where('status', 'active')->count() }}</span>
                </button>
                <button type="button" class="app-tab" data-list-tab="done">
                    {{ __('Finished') }} <span class="app-tab__count">{{ $campaigns->where('status', 'done')->count() }}</span>
                </button>
            </nav>

            <div class="list-toolbar">
                <label for="c-search" class="sr-only">{{ __('Search campaigns') }}</label>
                <div class="search-field">
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" id="c-search" class="form-input" placeholder="{{ __('Search by campaign name') }}" data-list-search />
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
                        @foreach ($campaigns as $campaign)
                            @php
                                $variant = $campaign->statusVariant();
                                $statusKey = $campaign->status === 'paused' ? 'active' : $campaign->status;
                            @endphp
                            <tr data-list-key="{{ $statusKey }}">
                                <td data-card-title>
                                    <span class="d-table__id">{{ $campaign->name }}</span>
                                    <p class="d-table__muted text-[0.8125rem]">
                                        {{ __('Built from') }} <span class="numeric">{{ $campaign->recipients_count }}</span> {{ __('contactable leads') }}
                                        · <time datetime="{{ $campaign->created_at->toDateString() }}">{{ $campaign->created_at->diffForHumans() }}</time>
                                    </p>
                                </td>
                                <td data-label="{{ __('Status') }}">
                                    <span class="status status--{{ $variant }}">
                                        @if ($campaign->status === 'active')
                                            <span class="live-dot" aria-hidden="true"></span>
                                        @elseif ($campaign->status === 'review')
                                            <i class="ph ph-eye" aria-hidden="true"></i>
                                        @endif
                                        {{ __($campaign->statusLabel()) }}
                                    </span>
                                </td>
                                <td data-label="{{ __('Recipients') }}" class="numeric text-right">{{ $campaign->recipients_count }}</td>
                                <td data-label="{{ __('Sent') }}" class="numeric text-right">{{ $campaign->sent_count ?: '—' }}</td>
                                <td data-label="{{ __('Opened') }}" class="numeric text-right">{{ $campaign->opened_count ?: '—' }}</td>
                                <td data-label="{{ __('Replied') }}" class="numeric text-right">{{ $campaign->replied_count ?: '—' }}</td>
                                <td data-card-actions class="text-right">
                                    <div class="row-actions">
                                        @if (in_array($campaign->status, ['review', 'paused'], true))
                                            <form action="{{ route('user.campaigns.update', $campaign) }}" method="post">
                                                @csrf
                                                @method('patch')
                                                <input type="hidden" name="status" value="active" />
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <span class="btn__label"><span>{{ __('Send') }}</span><span aria-hidden="true">{{ __('Send') }}</span></span>
                                                </button>
                                            </form>
                                        @elseif ($campaign->status === 'active')
                                            <form action="{{ route('user.campaigns.update', $campaign) }}" method="post">
                                                @csrf
                                                @method('patch')
                                                <input type="hidden" name="status" value="paused" />
                                                <button type="submit" class="row-icon" aria-label="{{ __('Pause') }} {{ $campaign->name }}">
                                                    <i class="ph ph-pause" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('user.campaigns.update', $campaign) }}" method="post">
                                                @csrf
                                                @method('patch')
                                                <input type="hidden" name="status" value="done" />
                                                <button type="submit" class="row-icon" aria-label="{{ __('Mark finished') }} {{ $campaign->name }}">
                                                    <i class="ph ph-check" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('user.campaigns.duplicate', $campaign) }}" method="post">
                                                @csrf
                                                <button type="submit" class="row-icon" aria-label="{{ __('Duplicate') }} {{ $campaign->name }}">
                                                    <i class="ph ph-copy-simple" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form id="delete-campaign-{{ $campaign->id }}" action="{{ route('user.campaigns.destroy', $campaign) }}" method="post">
                                            @csrf
                                            @method('delete')
                                        </form>
                                        <button
                                            type="button"
                                            class="row-icon"
                                            data-confirm
                                            data-submit-form="delete-campaign-{{ $campaign->id }}"
                                            data-confirm-title="{{ __('Delete this campaign?') }}"
                                            data-confirm-body="{{ __('Messages already marked sent stay in activity history. The remaining queued recipients will not be sent.') }}"
                                            data-confirm-label="{{ __('Delete campaign') }}"
                                            data-confirm-variant="error"
                                            aria-label="{{ __('Delete') }} {{ $campaign->name }}"
                                        >
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="no-results is-hidden" data-list-empty>
                <span class="no-results__icon" aria-hidden="true"><i class="ph ph-magnifying-glass"></i></span>
                <p class="no-results__title">{{ __('No campaigns match') }}</p>
                <p class="no-results__body">{{ __('Try a different tab, or clear the search to see them all.') }}</p>
            </div>
        </div>
    @else
        <div class="empty">
            <span class="empty__icon" aria-hidden="true"><i class="ph ph-paper-plane-tilt"></i></span>
            <h2 class="empty__title">{{ __('No campaigns yet') }}</h2>
            <p class="empty__body">{{ __('Create a campaign from leads that already have an email address.') }}</p>
            <button type="button" class="btn btn-primary btn-sm" data-modal-open="campaignModal">
                <span class="btn__label"><span>{{ __('New campaign') }}</span><span aria-hidden="true">{{ __('New campaign') }}</span></span>
                <i class="ph ph-plus"></i>
            </button>
        </div>
    @endif

    @push('modals')
        <div class="modal" id="campaignModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>
            <div class="modal__panel max-w-lg p-6" role="dialog" aria-modal="true" aria-labelledby="campaignModalTitle">
                <form action="{{ route('user.campaigns.store') }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="campaignModalTitle">{{ __('New campaign') }}</h2>
                    <p class="m-text mt-2 mb-5">{{ __('Pick a lead source. Only leads with an email address become recipients.') }}</p>

                    <div>
                        <label for="campaign-name" class="form-label">{{ __('Campaign name') }}</label>
                        <input type="text" id="campaign-name" name="name" class="form-input" placeholder="{{ __('Dhaka dentists follow-up') }}" required />
                    </div>

                    <div class="mt-4 grid gap-2">
                        <label class="pickr">
                            <input type="radio" name="source_type" value="all" class="form-check" checked />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('Every contactable lead') }}</span>
                                <span class="pickr__meta"><span class="numeric">{{ $contactableCount }}</span> {{ __('leads with email') }}</span>
                            </span>
                        </label>
                        <label class="pickr">
                            <input type="radio" name="source_type" value="list" class="form-check" />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('A list') }}</span>
                                <span class="pickr__meta">{{ $lists->count() ? __('Use one of your lead lists') : __('No lists yet') }}</span>
                            </span>
                        </label>
                        <label class="pickr">
                            <input type="radio" name="source_type" value="search" class="form-check" />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('One search result set') }}</span>
                                <span class="pickr__meta">{{ $searchRuns->count() ? __('Use a previous search') : __('No search history yet') }}</span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-4">
                        <label for="campaign-source" class="form-label">{{ __('Which one') }}</label>
                        <select id="campaign-source" name="source_id" class="form-input">
                            @foreach ($lists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }} ({{ $list->leads_count }} {{ __('leads') }})</option>
                            @endforeach
                            @foreach ($searchRuns as $run)
                                <option value="{{ $run->id }}">{{ $run->search?->prompt ?? __('Search #:id', ['id' => $run->id]) }} ({{ $run->results_count }} {{ __('found') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="campaign-daily" class="form-label">{{ __('Daily send limit') }}</label>
                        <input type="number" id="campaign-daily" name="daily_limit" class="form-input numeric" min="1" max="500" value="40" />
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label"><span>{{ __('Create campaign') }}</span><span aria-hidden="true">{{ __('Create campaign') }}</span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="confirmDialog" class="modal" role="dialog" aria-modal="true" aria-hidden="true">
            <div class="modal__backdrop"></div>
            <div class="modal__panel max-w-md p-6">
                <h2 class="heading-3" data-confirm-title-target>{{ __('Are you sure?') }}</h2>
                <p class="m-text mt-2" data-confirm-body-target>{{ __('This action cannot be undone.') }}</p>
                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button" class="btn btn-outline" data-confirm-cancel>{{ __('Cancel') }}</button>
                    <button type="button" class="btn confirm-accept" data-confirm-accept>{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    @endpush
</x-layouts.user>

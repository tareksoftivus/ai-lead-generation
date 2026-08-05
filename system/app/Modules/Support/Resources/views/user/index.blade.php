<x-layouts.user :title="__('Support')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Support') }}</h2>
            <p class="m-text mt-1">
                {{ __('Ask us anything about your account, a search that went wrong, or a charge you did not expect.') }}
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary btn-sm shrink-0"
            data-modal-open="ticketModal"
        >
            <span class="btn__label">
                <span>{{ __('New ticket') }}</span>
                <span aria-hidden="true">{{ __('New ticket') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </button>
    </div>

    @if($tickets->isEmpty())
        <div class="empty">
            <span class="empty__icon" aria-hidden="true">
                <i class="ph ph-lifebuoy"></i>
            </span>
            <h2 class="empty__title">{{ __('No tickets yet') }}</h2>
            <p class="empty__body">
                {{ __('When something is not working or a charge looks wrong, open a ticket and we will pick it up.') }}
            </p>
            <button
                type="button"
                class="btn btn-primary btn-sm"
                data-modal-open="ticketModal"
            >
                <span class="btn__label">
                    <span>{{ __('New ticket') }}</span>
                    <span aria-hidden="true">{{ __('New ticket') }}</span>
                </span>
                <i class="ph ph-plus"></i>
            </button>
        </div>
    @else
        <div class="panel" data-list>
            <div class="panel__head">
                <h3 class="panel__title">{{ __('Your tickets') }}</h3>
                <span class="panel__meta">
                    {{ __('Replies usually within') }} <span class="numeric">1</span> {{ __('business day') }}
                </span>
            </div>

            <nav class="app-tablist" aria-label="{{ __('Filter tickets') }}">
                <button
                    type="button"
                    class="app-tab is-active"
                    data-list-tab="all"
                    aria-current="page"
                >
                    {{ __('All') }}
                    <span class="app-tab__count">{{ $tickets->total() }}</span>
                </button>
                <button type="button" class="app-tab" data-list-tab="open">
                    {{ __('Open') }}
                    <span class="app-tab__count">{{ $openCount }}</span>
                </button>
                <button type="button" class="app-tab" data-list-tab="closed">
                    {{ __('Resolved') }}
                    <span class="app-tab__count">{{ $closedCount }}</span>
                </button>
            </nav>

            <div class="list-toolbar">
                <label for="t-search" class="sr-only">{{ __('Search tickets') }}</label>
                <div class="search-field">
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input
                        type="search"
                        id="t-search"
                        class="form-input"
                        placeholder="{{ __('Search by subject') }}"
                        data-list-search
                    />
                </div>

                <div
                    class="menu shrink-0"
                    data-dropdown
                    data-dropdown-select
                    data-list-filter="topic"
                    data-value="all"
                >
                    <button
                        type="button"
                        class="filter-btn"
                        data-dropdown-toggle
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="ph ph-tag" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any topic') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">
                            {{ __('Any topic') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="billing">
                            {{ __('Billing & credits') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="search">
                            {{ __('Searches & results') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="account">
                            {{ __('Account') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tbl-wrap">
                <table class="d-table d-table--cards" data-list-table>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Subject') }}</th>
                            <th scope="col">{{ __('Topic') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Last update') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($tickets as $ticket)
                            @php($statusMeta = \App\Modules\Support\Models\SupportTicket::statuses()[$ticket->status] ?? ['label' => $ticket->status, 'variant' => 'default'])
                            <tr data-list-key="{{ $ticket->status === 'resolved' || $ticket->status === 'closed' ? 'closed' : 'open' }}" data-topic="{{ $ticket->category }}">
                                <td data-card-title>
                                    <a href="{{ route('user.support-tickets.show', $ticket) }}" class="d-table__id">
                                        {{ $ticket->subject }}
                                    </a>
                                    <p class="d-table__muted text-[0.8125rem]">
                                        <span class="numeric">{{ $ticket->reference }}</span> ·
                                        <span class="numeric">{{ $ticket->replies_count }}</span> {{ $ticket->replies_count === 1 ? __('reply') : __('replies') }}
                                    </p>
                                </td>
                                <td data-label="{{ __('Topic') }}">{{ $ticket->category ? __(ucfirst($ticket->category)) : __('—') }}</td>
                                <td data-label="{{ __('Status') }}">
                                    @if($ticket->status === 'open')
                                        <span class="status status--running">
                                            <span class="live-dot" aria-hidden="true"></span>
                                            {{ __('Open') }}
                                        </span>
                                    @elseif($ticket->status === 'pending')
                                        <span class="status status--replied">
                                            {{ __('Answered — your turn') }}
                                        </span>
                                    @else
                                        <span class="status status--done">{{ $statusMeta['label'] }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('Last update') }}" class="d-table__muted whitespace-nowrap">
                                    @if($ticket->last_reply_at && $ticket->last_reply_at->isToday())
                                        <time datetime="{{ $ticket->last_reply_at->toDateString() }}">{{ __('Today') }}, {{ $ticket->last_reply_at->format('H:i') }}</time>
                                    @else
                                        <time datetime="{{ ($ticket->last_reply_at ?? $ticket->created_at)->toDateString() }}">{{ ($ticket->last_reply_at ?? $ticket->created_at)->format('j M') }}</time>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="px-4 py-3">
                    <x-tables.pagination :paginator="$tickets" />
                </div>
            @endif

            <div class="no-results is-hidden" data-list-empty>
                <span class="no-results__icon" aria-hidden="true">
                    <i class="ph ph-magnifying-glass"></i>
                </span>
                <p class="no-results__title">{{ __('No tickets match') }}</p>
                <p class="no-results__body">
                    {{ __('Try a different topic, or clear the search to see them all.') }}
                </p>
            </div>
        </div>
    @endif

    @push('modals')
        <div class="modal" id="ticketModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div
                class="modal__panel max-w-lg p-6"
                role="dialog"
                aria-modal="true"
                aria-labelledby="ticketModalTitle"
            >
                <form method="POST" action="{{ route('user.support-tickets.store') }}">
                    @csrf
                    <h2 class="heading-3" id="ticketModalTitle">{{ __('New ticket') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Tell us what happened. The more specific, the faster we can fix it.') }}
                    </p>

                    <div>
                        <label for="t-topic" class="form-label">{{ __('Topic') }}</label>
                        <select id="t-topic" name="category" class="form-input" required>
                            <option value="">{{ __('Choose a topic') }}</option>
                            <option value="billing" @selected(old('category') === 'billing')>{{ __('Billing & credits') }}</option>
                            <option value="search" @selected(old('category') === 'search')>{{ __('Searches & results') }}</option>
                            <option value="account" @selected(old('category') === 'account')>{{ __('Account') }}</option>
                            <option value="other" @selected(old('category') === 'other')>{{ __('Something else') }}</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="t-subject" class="form-label">{{ __('Subject') }}</label>
                        <input
                            type="text"
                            id="t-subject"
                            name="subject"
                            class="form-input"
                            value="{{ old('subject') }}"
                            placeholder="{{ __('Credits charged for an empty search') }}"
                            required
                        />
                    </div>

                    <div class="mt-4">
                        <label for="t-body" class="form-label">{{ __('What happened') }}</label>
                        <textarea
                            id="t-body"
                            name="body"
                            class="form-input"
                            rows="5"
                            placeholder="{{ __('Which search, roughly when, and what you expected instead.') }}"
                            required
                        >{{ old('body') }}</textarea>
                    </div>

                    <p class="tkt__attach">
                        <i class="ph ph-paperclip" aria-hidden="true"></i>
                        <span>
                            {{ __('Sent with your account details so you do not have to explain them:') }}
                            <strong class="text-title">{{ __('Growth') }}</strong> {{ __('plan') }},
                            <span class="numeric">2,480</span> {{ __('of') }}
                            <span class="numeric">5,000</span> {{ __('credits left this month') }}.
                        </span>
                    </p>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            <span class="btn__label">
                                <span>{{ __('Cancel') }}</span>
                                <span aria-hidden="true">{{ __('Cancel') }}</span>
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label">
                                <span>{{ __('Send ticket') }}</span>
                                <span aria-hidden="true">{{ __('Send ticket') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endpush
</x-layouts.user>

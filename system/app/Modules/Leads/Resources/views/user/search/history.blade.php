<x-layouts.user :title="__('Search history')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Search history') }}</h2>
            <p class="m-text mt-1">
                {{ __('Every search this account has run. Re-run one to pick up businesses that have opened since.') }}
            </p>
        </div>

        <a href="{{ route('user.search.new') }}" class="btn btn-primary btn-sm shrink-0">
            <span class="btn__label">
                <span>{{ __('New search') }}</span>
                <span aria-hidden="true">{{ __('New search') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </a>
    </div>

    <div class="panel{{ $searchRuns->isEmpty() ? ' is-hidden' : '' }}" data-list>
        <nav class="app-tablist" aria-label="{{ __('Filter searches') }}">
            <a href="#" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }}
                <span class="app-tab__count">{{ $searchRuns->total() }}</span>
            </a>
            <a href="#" class="app-tab" data-list-tab="running">
                {{ __('Running') }}
                <span class="app-tab__count">{{ $searchRuns->where('status', 'running')->count() }}</span>
            </a>
            <a href="#" class="app-tab" data-list-tab="done">
                {{ __('Finished') }}
                <span class="app-tab__count">{{ $searchRuns->where('status', 'done')->count() }}</span>
            </a>
            <a href="#" class="app-tab" data-list-tab="saved">
                {{ __('Saved') }}
                <span class="app-tab__count">{{ $searchRuns->whereNotNull('search_id')->count() }}</span>
            </a>
        </nav>

        <div class="list-toolbar">
            <label for="h-search" class="sr-only">{{ __('Search by keyword') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    id="h-search"
                    class="form-input"
                    placeholder="{{ __('Search by keyword or place') }}"
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
                    <span data-dropdown-label>{{ __('Any time') }}</span>
                    <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                </button>

                <div class="menu__panel" data-dropdown-panel>
                    <button type="button" class="menu__item is-selected" data-value="all">
                        {{ __('Any time') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="week">
                        {{ __('This week') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="month">
                        {{ __('This month') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="quarter">
                        {{ __('Last 3 months') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <p class="list-count">
                <span class="numeric" data-list-count>{{ $searchRuns->total() }}</span> {{ __('searches') }}
            </p>
        </div>

        <table class="d-table d-table--cards" data-list-table>
            <thead>
                <tr>
                    <th scope="col">{{ __('Search') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col" class="text-right">{{ __('Found') }}</th>
                    <th scope="col" class="text-right">{{ __('Credits') }}</th>
                    <th scope="col">{{ __('When') }}</th>
                    <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($searchRuns as $searchRun)
                    @php
                        $statusMeta = \App\Modules\Leads\Models\SearchRun::statuses()[$searchRun->status] ?? ['label' => $searchRun->status];
                        $listKey = $searchRun->search_id ? 'saved' : $searchRun->status;
                        $label = trim(implode(' ', array_filter([
                            implode(', ', (array) ($searchRun->filters['keyword'] ?? [])),
                            'in',
                            implode(', ', (array) ($searchRun->filters['location'] ?? [])),
                        ])));
                    @endphp
                    <tr data-list-key="{{ $listKey }}">
                        <td data-card-title>
                            <span class="d-table__id">{{ $label ?: __('Untitled search') }}</span>
                            @if (! empty($searchRun->filters['radius']))
                                <p class="d-table__muted text-[0.8125rem]">
                                    <span class="numeric">{{ $searchRun->filters['radius'] }}</span> {{ __('mile radius') }}
                                </p>
                            @endif
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--{{ $searchRun->status }}">
                                @if ($searchRun->status === 'running')
                                    <span class="live-dot" aria-hidden="true"></span>
                                @endif
                                {{ $statusMeta['label'] }}
                            </span>
                        </td>
                        <td data-label="{{ __('Found') }}" class="numeric text-right">{{ $searchRun->results_count }}</td>
                        <td data-label="{{ __('Credits') }}" class="numeric text-right">{{ $searchRun->credits_spent }}</td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="{{ $searchRun->created_at->toDateString() }}">{{ $searchRun->created_at->format('d M') }}</time>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                @if ($searchRun->isTerminal())
                                    <a href="{{ route('user.search.results', $searchRun) }}" class="btn btn-sm btn-outline">
                                        <span class="btn__label">
                                            <span>{{ __('Results') }}</span>
                                            <span aria-hidden="true">{{ __('Results') }}</span>
                                        </span>
                                    </a>

                                    <form action="{{ route('user.search.rerun', $searchRun) }}" method="post" class="contents">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="row-icon"
                                            aria-label="{{ __('Re-run this search') }}"
                                            data-confirm
                                            data-confirm-title="{{ __('Re-run this search?') }}"
                                            data-confirm-body="{{ __('This spends credits again for the leads generated by this search.') }}"
                                            data-confirm-label="{{ __('Re-run') }}"
                                        >
                                            <i class="ph ph-arrow-clockwise" aria-hidden="true"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('user.search.destroy', $searchRun) }}" method="post" class="contents">
                                        @csrf
                                        @method('delete')
                                        <button
                                            type="submit"
                                            class="row-icon"
                                            aria-label="{{ __('Delete this search') }}"
                                            data-confirm
                                            data-confirm-title="{{ __('Delete this search?') }}"
                                            data-confirm-body="{{ __('This removes the search from your history. The leads it found stay in your account.') }}"
                                            data-confirm-label="{{ __('Delete') }}"
                                            data-confirm-variant="error"
                                        >
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="btn btn-sm btn-outline" aria-disabled="true">
                                        <span class="btn__label">
                                            <span>{{ __('Running') }}</span>
                                            <span aria-hidden="true">{{ __('Running') }}</span>
                                        </span>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <nav class="tbl-pager" aria-label="{{ __('Pagination') }}">
            {{ $searchRuns->links() }}
        </nav>

        <div class="no-results is-hidden" data-list-empty>
            <span class="no-results__icon" aria-hidden="true">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <p class="no-results__title">{{ __('No searches match') }}</p>
            <p class="no-results__body">
                {{ __('Try a different keyword, or widen the date range.') }}
            </p>
        </div>
    </div>

    <section class="panel empty" @if (! $searchRuns->isEmpty()) hidden @endif>
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-clock-counter-clockwise"></i>
        </span>
        <h2 class="empty__title">{{ __('No searches yet') }}</h2>
        <p class="empty__body">
            {{ __('Once you run a search it appears here, so you can re-open the results or run it again later. Searching itself costs nothing.') }}
        </p>
        <a href="{{ route('user.search.new') }}" class="btn btn-primary">
            <span class="btn__label">
                <span>{{ __('Run your first search') }}</span>
                <span aria-hidden="true">{{ __('Run your first search') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.addEventListener('confirm:accepted', (event) => {
                    const trigger = event.target;
                    if (trigger.tagName === 'BUTTON' && trigger.form) {
                        trigger.form.submit();
                    }
                });
            });
        </script>
    @endpush

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

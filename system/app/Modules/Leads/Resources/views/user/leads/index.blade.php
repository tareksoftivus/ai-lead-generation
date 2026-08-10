<x-layouts.user :title="__('All leads')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ $activeList?->name ?? __('All leads') }}</h2>
            <p class="m-text mt-1">
                @if ($activeList)
                    {{ __('Leads saved under this list. You can still manage status, tags, and notes from here.') }}
                @else
                    {{ __('Every business you have saved. Exporting is free — a lead you already hold is yours to download as often as you like.') }}
                @endif
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

    <div class="panel{{ $leads->isEmpty() ? ' is-hidden' : '' }}" data-list data-bulk>
        @php
            $statuses = \App\Modules\Leads\Models\Lead::statuses();
        @endphp
        <nav class="app-tablist" aria-label="{{ __('Filter leads') }}">
            <a href="#" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }}
                <span class="app-tab__count">{{ $leads->total() }}</span>
            </a>
            @foreach (['new' => 'New', 'contacted' => 'Contacted', 'replied' => 'Replied', 'qualified' => 'Qualified'] as $key => $label)
                <a href="#" class="app-tab" data-list-tab="{{ $key }}">
                    {{ __($label) }}
                    <span class="app-tab__count">{{ $leads->where('status', $key)->count() }}</span>
                </a>
            @endforeach
        </nav>

        <div class="list-toolbar">
            <label for="l-search" class="sr-only">{{ __('Search leads') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="l-search" class="form-input" placeholder="{{ __('Search by business name') }}" data-list-search />
            </div>

            <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="score" data-value="all">
                <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                    <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                    <span data-dropdown-label>{{ __('Any score') }}</span>
                    <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                </button>

                <div class="menu__panel" data-dropdown-panel>
                    <button type="button" class="menu__item is-selected" data-value="all">
                        {{ __('Any score') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="hi">
                        {{ __('80 and above') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="mid">
                        {{ __('50 to 79') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="lo">
                        {{ __('Below 50') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="contact" data-value="all">
                <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                    <i class="ph ph-envelope-simple" aria-hidden="true"></i>
                    <span data-dropdown-label>{{ __('Any contact') }}</span>
                    <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                </button>

                <div class="menu__panel" data-dropdown-panel>
                    <button type="button" class="menu__item is-selected" data-value="all">
                        {{ __('Any contact') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="yes">
                        {{ __('Has an email') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="no">
                        {{ __('No email found') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <p class="list-count">
                <span class="numeric" data-list-count>{{ $leads->total() }}</span> {{ __('leads') }}
            </p>
        </div>

        <div class="bulk-bar is-hidden" data-bulk-bar>
            <p class="text-[0.875rem] font-semibold text-title">
                <span class="numeric" data-bulk-count>0</span> {{ __('selected') }}
            </p>

            <div class="bulk-bar__actions">
                <button type="button" class="btn btn-sm btn-outline" data-modal-open="tagModal">
                    <span class="btn__label">
                        <span>{{ __('Add tag') }}</span>
                        <span aria-hidden="true">{{ __('Add tag') }}</span>
                    </span>
                    <i class="ph ph-tag"></i>
                </button>

                <button type="button" class="btn btn-sm btn-outline" data-modal-open="statusModal">
                    <span class="btn__label">
                        <span>{{ __('Set status') }}</span>
                        <span aria-hidden="true">{{ __('Set status') }}</span>
                    </span>
                    <i class="ph ph-flag"></i>
                </button>

                <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete selected leads') }}"
                        form="bulk-delete-form" formaction="{{ route('user.leads.bulk-delete') }}"
                        data-confirm data-confirm-title="{{ __('Delete the selected leads?') }}"
                        data-confirm-body="{{ __('They are removed from your account and from any list they belong to. Credits already spent generating them are not returned.') }}"
                        data-confirm-label="{{ __('Delete leads') }}" data-confirm-variant="error">
                    <i class="ph ph-trash" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <table class="d-table d-table--cards" data-list-table>
            <thead>
                <tr>
                    <th scope="col" class="d-table__check">
                        <input type="checkbox" class="form-check" data-bulk-all aria-label="{{ __('Select all visible leads') }}" />
                    </th>
                    <th scope="col">{{ __('Business') }}</th>
                    <th scope="col">{{ __('Contact') }}</th>
                    <th scope="col" class="text-right">{{ __('Score') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col">{{ __('Tags') }}</th>
                    <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($leads as $lead)
                    @php
                        $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($lead->score);
                    @endphp
                    <tr data-list-key="{{ $lead->status }}" data-score="{{ $bucket }}" data-contact="{{ $lead->hasContact() ? 'yes' : 'no' }}">
                        <td class="d-table__check">
                            <input type="checkbox" name="lead_id[]" value="{{ $lead->id }}" form="bulk-delete-form" class="form-check" data-bulk-item aria-label="{{ __('Select :name', ['name' => $lead->place?->name]) }}" />
                        </td>
                        <td data-card-title>
                            <a href="{{ route('user.leads.show', $lead) }}" class="d-table__id">{{ $lead->place?->name }}</a>
                            <p class="d-table__place">
                                <i class="ph ph-map-pin" aria-hidden="true"></i>
                                {{ $lead->place?->formatted_address }}
                            </p>
                        </td>
                        <td data-label="{{ __('Contact') }}">
                            @if ($lead->email)
                                <a href="mailto:{{ $lead->email }}" class="d-table__mail">{{ $lead->email }}</a>
                            @else
                                <span class="d-table__muted">{{ __('No email published') }}</span>
                            @endif
                        </td>
                        <td data-label="{{ __('Score') }}" class="text-right">
                            <span class="score score--{{ $bucket }} numeric">{{ $lead->score }}</span>
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--{{ $lead->status }}">{{ $statuses[$lead->status]['label'] ?? $lead->status }}</span>
                        </td>
                        <td data-label="{{ __('Tags') }}">
                            @foreach ($lead->tags as $tag)
                                <span class="tag-pill">{{ $tag->name }}</span>
                            @endforeach
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                @if (! $lead->is_in_pipeline && \Illuminate\Support\Facades\Route::has('user.pipeline.update-status'))
                                    <form action="{{ route('user.pipeline.update-status', $lead) }}" method="post" class="inline-flex">
                                        @csrf
                                        @method('patch')
                                        <input type="hidden" name="status" value="{{ $lead->status }}" />
                                        <button type="submit" class="btn btn-sm btn-outline">
                                            <span class="btn__label">
                                                <span>{{ __('Add to pipeline') }}</span>
                                                <span aria-hidden="true">{{ __('Add to pipeline') }}</span>
                                            </span>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('user.leads.show', $lead) }}" class="btn btn-sm btn-outline">
                                    <span class="btn__label">
                                        <span>{{ __('Open') }}</span>
                                        <span aria-hidden="true">{{ __('Open') }}</span>
                                    </span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="no-results is-hidden" data-list-empty>
            <span class="no-results__icon" aria-hidden="true">
                <i class="ph ph-funnel"></i>
            </span>
            <p class="no-results__title">{{ __('No leads match') }}</p>
            <p class="no-results__body">
                {{ __('Try a different search term, or widen the score and contact filters.') }}
            </p>
        </div>

        <nav class="tbl-pager" aria-label="{{ __('Pagination') }}">
            {{ $leads->links() }}
        </nav>
    </div>

    <section class="panel empty" @if (! $leads->isEmpty()) hidden @endif>
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-users-three"></i>
        </span>
        <h2 class="empty__title">{{ __('No leads yet') }}</h2>
        <p class="empty__body">
            {{ __('Run a search and every business we find lands here, scored and ready to work.') }}
        </p>
        <a href="{{ route('user.search.new') }}" class="btn btn-primary">
            <span class="btn__label">
                <span>{{ __('Find your first leads') }}</span>
                <span aria-hidden="true">{{ __('Find your first leads') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </section>

    <form id="bulk-delete-form" action="{{ route('user.leads.bulk-delete') }}" method="post">
        @csrf
        @method('delete')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.addEventListener('confirm:accepted', (event) => {
                    const trigger = event.target;
                    if (trigger.dataset.formaction) {
                        const form = document.getElementById(trigger.getAttribute('form'));
                        if (form) {
                            form.action = trigger.dataset.formaction;
                            form.submit();
                        }
                    }
                });
            });
        </script>
    @endpush

    @push('modals')
        <div class="modal" id="tagModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="tagModalTitle">
                <form action="{{ route('user.leads.bulk-tag') }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="tagModalTitle">{{ __('Tag the selected leads') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Tags are your own labels — they do not affect the AI score.') }}
                    </p>

                    <div>
                        <label for="tag-name" class="form-label">{{ __('Tag') }}</label>
                        <input type="text" id="tag-name" name="tag" class="form-input" placeholder="{{ __('Follow up in Q3') }}" maxlength="30" required />
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
                                <span>{{ __('Add tag') }}</span>
                                <span aria-hidden="true">{{ __('Add tag') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="statusModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="statusModalTitle">
                <form action="{{ route('user.leads.bulk-status') }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="statusModalTitle">{{ __('Set status') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Where these leads sit in your pipeline.') }}
                    </p>

                    <div>
                        <label for="status-value" class="form-label">{{ __('Status') }}</label>
                        <select id="status-value" name="status" class="form-input" required>
                            @foreach ($statuses as $key => $meta)
                                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
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
                                <span>{{ __('Set status') }}</span>
                                <span aria-hidden="true">{{ __('Set status') }}</span>
                            </span>
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

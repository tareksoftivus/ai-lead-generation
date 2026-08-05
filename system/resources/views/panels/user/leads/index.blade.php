<x-layouts.user :title="__('All leads')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('All leads') }}</h2>
            <p class="m-text mt-1">
                {{ __('Every business you have found and enriched. Exporting is free — a lead you already hold is yours to download as often as you like.') }}
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

    <div class="panel" data-list data-bulk>
        <nav class="app-tablist" aria-label="{{ __('Filter leads') }}">
            <a href="#" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }}
                <span class="app-tab__count">1,284</span>
            </a>
            <a href="#" class="app-tab" data-list-tab="new">
                {{ __('New') }}
                <span class="app-tab__count">842</span>
            </a>
            <a href="#" class="app-tab" data-list-tab="contacted">
                {{ __('Contacted') }}
                <span class="app-tab__count">318</span>
            </a>
            <a href="#" class="app-tab" data-list-tab="replied">
                {{ __('Replied') }}
                <span class="app-tab__count">86</span>
            </a>
            <a href="#" class="app-tab" data-list-tab="qualified">
                {{ __('Qualified') }}
                <span class="app-tab__count">38</span>
            </a>
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
                        {{ __('60 to 79') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="lo">
                        {{ __('Below 60') }}
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
                <span class="numeric" data-list-count>6</span> {{ __('leads') }}
            </p>
        </div>

        <div class="bulk-bar is-hidden" data-bulk-bar>
            <p class="text-[0.875rem] font-semibold text-title">
                <span class="numeric" data-bulk-count>0</span> {{ __('selected') }}
            </p>

            <div class="bulk-bar__actions">
                <a href="#" class="btn btn-sm btn-primary">
                    <span class="btn__label">
                        <span>{{ __('Export') }}</span>
                        <span aria-hidden="true">{{ __('Export') }}</span>
                    </span>
                    <i class="ph ph-download-simple"></i>
                </a>

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
                        data-confirm data-confirm-title="{{ __('Delete the selected leads?') }}"
                        data-confirm-body="{{ __('They are removed from your account and from any list they belong to. Credits already spent enriching them are not returned.') }}"
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
                <tr data-list-key="new" data-score="hi" data-contact="yes">
                    <td class="d-table__check">
                        <input type="checkbox" class="form-check" data-bulk-item aria-label="{{ __('Select Barton Springs Dental') }}" />
                    </td>
                    <td data-card-title>
                        <a href="{{ route('user.leads.show', ['lead' => 'barton-springs-dental']) }}" class="d-table__id">{{ __('Barton Springs Dental') }}</a>
                        <p class="d-table__place">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Austin, TX') }}
                        </p>
                    </td>
                    <td data-label="{{ __('Contact') }}">
                        <a href="#" class="d-table__mail">hello@bartonsprings.com</a>
                    </td>
                    <td data-label="{{ __('Score') }}" class="text-right">
                        <span class="score score--hi numeric">92</span>
                    </td>
                    <td data-label="{{ __('Status') }}">
                        <span class="status status--new">{{ __('New') }}</span>
                    </td>
                    <td data-label="{{ __('Tags') }}">
                        <span class="tag-pill">{{ __('High value') }}</span>
                    </td>
                    <td data-card-actions class="text-right">
                        <div class="row-actions">
                            <a href="{{ route('user.leads.show', ['lead' => 'barton-springs-dental']) }}" class="btn btn-sm btn-outline">
                                <span class="btn__label">
                                    <span>{{ __('Open') }}</span>
                                    <span aria-hidden="true">{{ __('Open') }}</span>
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-list-key="contacted" data-score="hi" data-contact="yes">
                    <td class="d-table__check">
                        <input type="checkbox" class="form-check" data-bulk-item aria-label="{{ __('Select Lamar Family Dentistry') }}" />
                    </td>
                    <td data-card-title>
                        <a href="{{ route('user.leads.show', ['lead' => 'lamar-family-dentistry']) }}" class="d-table__id">{{ __('Lamar Family Dentistry') }}</a>
                        <p class="d-table__place">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Austin, TX') }}
                        </p>
                    </td>
                    <td data-label="{{ __('Contact') }}">
                        <a href="#" class="d-table__mail">front@lamardental.com</a>
                    </td>
                    <td data-label="{{ __('Score') }}" class="text-right">
                        <span class="score score--hi numeric">88</span>
                    </td>
                    <td data-label="{{ __('Status') }}">
                        <span class="status status--contacted">{{ __('Contacted') }}</span>
                    </td>
                    <td data-label="{{ __('Tags') }}">
                        <span class="tag-pill">{{ __('Follow up') }}</span>
                    </td>
                    <td data-card-actions class="text-right">
                        <div class="row-actions">
                            <a href="{{ route('user.leads.show', ['lead' => 'lamar-family-dentistry']) }}" class="btn btn-sm btn-outline">
                                <span class="btn__label">
                                    <span>{{ __('Open') }}</span>
                                    <span aria-hidden="true">{{ __('Open') }}</span>
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-list-key="replied" data-score="hi" data-contact="yes">
                    <td class="d-table__check">
                        <input type="checkbox" class="form-check" data-bulk-item aria-label="{{ __('Select Hyde Park Dental Care') }}" />
                    </td>
                    <td data-card-title>
                        <a href="{{ route('user.leads.show', ['lead' => 'hyde-park-dental-care']) }}" class="d-table__id">{{ __('Hyde Park Dental Care') }}</a>
                        <p class="d-table__place">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Austin, TX') }}
                        </p>
                    </td>
                    <td data-label="{{ __('Contact') }}">
                        <a href="#" class="d-table__mail">care@hydeparkdental.com</a>
                    </td>
                    <td data-label="{{ __('Score') }}" class="text-right">
                        <span class="score score--hi numeric">84</span>
                    </td>
                    <td data-label="{{ __('Status') }}">
                        <span class="status status--replied">{{ __('Replied') }}</span>
                    </td>
                    <td data-label="{{ __('Tags') }}">
                        <span class="tag-pill">{{ __('Hot') }}</span>
                    </td>
                    <td data-card-actions class="text-right">
                        <div class="row-actions">
                            <a href="{{ route('user.leads.show', ['lead' => 'hyde-park-dental-care']) }}" class="btn btn-sm btn-outline">
                                <span class="btn__label">
                                    <span>{{ __('Open') }}</span>
                                    <span aria-hidden="true">{{ __('Open') }}</span>
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-list-key="new" data-score="mid" data-contact="yes">
                    <td class="d-table__check">
                        <input type="checkbox" class="form-check" data-bulk-item aria-label="{{ __('Select Zilker Smile Studio') }}" />
                    </td>
                    <td data-card-title>
                        <a href="{{ route('user.leads.show', ['lead' => 'zilker-smile-studio']) }}" class="d-table__id">{{ __('Zilker Smile Studio') }}</a>
                        <p class="d-table__place">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Austin, TX') }}
                        </p>
                    </td>
                    <td data-label="{{ __('Contact') }}">
                        <a href="#" class="d-table__mail">info@zilkersmile.com</a>
                    </td>
                    <td data-label="{{ __('Score') }}" class="text-right">
                        <span class="score score--mid numeric">79</span>
                    </td>
                    <td data-label="{{ __('Status') }}">
                        <span class="status status--new">{{ __('New') }}</span>
                    </td>
                    <td data-label="{{ __('Tags') }}">
                        <span class="tag-pill">{{ __('Austin') }}</span>
                    </td>
                    <td data-card-actions class="text-right">
                        <div class="row-actions">
                            <a href="{{ route('user.leads.show', ['lead' => 'zilker-smile-studio']) }}" class="btn btn-sm btn-outline">
                                <span class="btn__label">
                                    <span>{{ __('Open') }}</span>
                                    <span aria-hidden="true">{{ __('Open') }}</span>
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-list-key="new" data-score="mid" data-contact="no">
                    <td class="d-table__check">
                        <input type="checkbox" class="form-check" data-bulk-item aria-label="{{ __('Select Congress Ave Dental') }}" />
                    </td>
                    <td data-card-title>
                        <a href="{{ route('user.leads.show', ['lead' => 'congress-ave-dental']) }}" class="d-table__id">{{ __('Congress Ave Dental') }}</a>
                        <p class="d-table__place">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Austin, TX') }}
                        </p>
                    </td>
                    <td data-label="{{ __('Contact') }}">
                        <span class="text-[0.8125rem] text-neutral-500 italic">{{ __('No email published') }}</span>
                    </td>
                    <td data-label="{{ __('Score') }}" class="text-right">
                        <span class="score score--mid numeric">64</span>
                    </td>
                    <td data-label="{{ __('Status') }}">
                        <span class="status status--new">{{ __('New') }}</span>
                    </td>
                    <td data-label="{{ __('Tags') }}">
                        <span class="tag-pill">{{ __('Phone only') }}</span>
                    </td>
                    <td data-card-actions class="text-right">
                        <div class="row-actions">
                            <a href="{{ route('user.leads.show', ['lead' => 'congress-ave-dental']) }}" class="btn btn-sm btn-outline">
                                <span class="btn__label">
                                    <span>{{ __('Open') }}</span>
                                    <span aria-hidden="true">{{ __('Open') }}</span>
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-list-key="qualified" data-score="lo" data-contact="yes">
                    <td class="d-table__check">
                        <input type="checkbox" class="form-check" data-bulk-item aria-label="{{ __('Select Desert Bloom Med Spa') }}" />
                    </td>
                    <td data-card-title>
                        <a href="{{ route('user.leads.show', ['lead' => 'desert-bloom-med-spa']) }}" class="d-table__id">{{ __('Desert Bloom Med Spa') }}</a>
                        <p class="d-table__place">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Phoenix, AZ') }}
                        </p>
                    </td>
                    <td data-label="{{ __('Contact') }}">
                        <a href="#" class="d-table__mail">hello@desertbloom.com</a>
                    </td>
                    <td data-label="{{ __('Score') }}" class="text-right">
                        <span class="score score--lo numeric">52</span>
                    </td>
                    <td data-label="{{ __('Status') }}">
                        <span class="status status--qualified">{{ __('Qualified') }}</span>
                    </td>
                    <td data-label="{{ __('Tags') }}">
                        <span class="tag-pill">{{ __('Phoenix') }}</span>
                    </td>
                    <td data-card-actions class="text-right">
                        <div class="row-actions">
                            <a href="{{ route('user.leads.show', ['lead' => 'desert-bloom-med-spa']) }}" class="btn btn-sm btn-outline">
                                <span class="btn__label">
                                    <span>{{ __('Open') }}</span>
                                    <span aria-hidden="true">{{ __('Open') }}</span>
                                </span>
                            </a>
                        </div>
                    </td>
                </tr>
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
            <p class="order-2 text-[0.8125rem] text-body @2xl:order-none">
                <span class="numeric">1</span>–<span class="numeric">6</span> {{ __('of') }}
                <span class="numeric">1,284</span>
            </p>

            <div class="tbl-pager__controls">
                <span class="tbl-pager__step is-disabled" aria-disabled="true" aria-label="{{ __('Previous page') }}">
                    <i class="ph ph-caret-left" aria-hidden="true"></i>
                </span>

                <a href="#" class="tbl-pager__num is-active" aria-current="page">1</a>
                <a href="#" class="tbl-pager__num">2</a>
                <a href="#" class="tbl-pager__num">3</a>

                <a href="#" class="tbl-pager__step" aria-label="{{ __('Next page') }}">
                    <i class="ph ph-caret-right" aria-hidden="true"></i>
                </a>
            </div>
        </nav>
    </div>

    <section class="panel empty" hidden>
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-users-three"></i>
        </span>
        <h2 class="empty__title">{{ __('No leads yet') }}</h2>
        <p class="empty__body">
            {{ __('Run a search and every business we find lands here, scored and ready to work. You have') }}
            <span class="numeric">100</span>
            {{ __('free credits to start.') }}
        </p>
        <a href="{{ route('user.search.new') }}" class="btn btn-primary">
            <span class="btn__label">
                <span>{{ __('Find your first leads') }}</span>
                <span aria-hidden="true">{{ __('Find your first leads') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </section>

    @push('modals')
        <div class="modal" id="tagModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="tagModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="tagModalTitle">{{ __('Tag the selected leads') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Tags are your own labels — they do not affect the AI score.') }}
                    </p>

                    <div>
                        <label for="tag-name" class="form-label">{{ __('Tag') }}</label>
                        <input type="text" id="tag-name" name="tag" class="form-input" placeholder="{{ __('Follow up in Q3') }}" list="tag-suggestions" required />
                        <datalist id="tag-suggestions">
                            <option value="{{ __('High value') }}"></option>
                            <option value="{{ __('Follow up') }}"></option>
                            <option value="{{ __('Hot') }}"></option>
                            <option value="{{ __('Phone only') }}"></option>
                        </datalist>
                        <p class="form-hint">{{ __('Pick an existing tag or type a new one.') }}</p>
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
                <form action="#" method="post">
                    <h2 class="heading-3" id="statusModalTitle">{{ __('Set status') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Where these leads sit in your pipeline.') }}
                    </p>

                    <div>
                        <label for="status-value" class="form-label">{{ __('Status') }}</label>
                        <select id="status-value" name="status" class="form-input" required>
                            <option value="new">{{ __('New') }}</option>
                            <option value="contacted">{{ __('Contacted') }}</option>
                            <option value="replied">{{ __('Replied') }}</option>
                            <option value="qualified">{{ __('Qualified') }}</option>
                            <option value="lost">{{ __('Lost') }}</option>
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
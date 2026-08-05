<x-layouts.user :title="__('Contacts')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Contacts') }}</h2>
            <p class="m-text mt-1">
                {{ __('The people behind your leads. One business can have several — mark the one you actually reach as primary.') }}
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary btn-sm shrink-0"
            data-modal-open="contactModal"
        >
            <span class="btn__label">
                <span>{{ __('Add contact') }}</span>
                <span aria-hidden="true">{{ __('Add contact') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </button>
    </div>

    <div class="panel" data-list>
        <div class="panel__head">
            <h3 class="panel__title">{{ __('All contacts') }}</h3>
            <span class="panel__meta">
                <span class="numeric">7</span> {{ __('people') }} ·
                <span class="numeric">6</span> {{ __('businesses') }}
            </span>
        </div>

        <nav class="app-tablist" aria-label="{{ __('Filter contacts') }}">
            <button type="button" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }}
                <span class="app-tab__count">7</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="primary">
                {{ __('Primary') }}
                <span class="app-tab__count">5</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="nophone">
                {{ __('No phone') }}
                <span class="app-tab__count">2</span>
            </button>
        </nav>

        <div class="list-toolbar">
            <label for="c-search" class="sr-only">{{ __('Search contacts') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    id="c-search"
                    class="form-input"
                    placeholder="{{ __('Search by name, business, or email') }}"
                    data-list-search
                />
            </div>

            <div
                class="menu shrink-0"
                data-dropdown
                data-dropdown-select
                data-list-filter="role"
                data-value="all"
            >
                <button
                    type="button"
                    class="filter-btn"
                    data-dropdown-toggle
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <i class="ph ph-user-circle" aria-hidden="true"></i>
                    <span data-dropdown-label>{{ __('Any role') }}</span>
                    <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                </button>

                <div class="menu__panel" data-dropdown-panel>
                    <button type="button" class="menu__item is-selected" data-value="all">
                        {{ __('Any role') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="owner">
                        {{ __('Owner') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="manager">
                        {{ __('Manager') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="menu__item" data-value="desk">
                        {{ __('Front desk') }}
                        <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="tbl-wrap">
            <table class="d-table d-table--cards" data-list-table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Role') }}</th>
                        <th scope="col">{{ __('Email') }}</th>
                        <th scope="col">{{ __('Phone') }}</th>
                        <th scope="col">{{ __('Business') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                    </tr>
                </thead>

                <tbody>
                    <tr data-list-key="primary" data-role="manager">
                        <td data-card-title>
                            <span class="ct__who">
                                <span class="ct__avatar" aria-hidden="true">DW</span>
                                <span class="min-w-0">
                                    <span class="ct__name">
                                        {{ __('Dana Whitfield') }}
                                        <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Primary') }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                        {{ __('Reached on 19 Jul') }}
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td data-label="{{ __('Role') }}">{{ __('Practice manager') }}</td>
                        <td data-label="{{ __('Email') }}">
                            <a href="#" class="d-table__mail">dana@bartonsprings.com</a>
                        </td>
                        <td data-label="{{ __('Phone') }}" class="numeric whitespace-nowrap">
                            (512) 555-0143
                        </td>
                        <td data-label="{{ __('Business') }}">
                            <a href="#" class="d-table__id">{{ __('Barton Springs Dental') }}</a>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-modal-open="contactModal"
                                    aria-label="{{ __('Edit') }} {{ __('Dana Whitfield') }}"
                                >
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete Dana Whitfield?') }}"
                                    data-confirm-body="{{ __('The contact is removed. Barton Springs Dental stays in your leads, with its other contact.') }}"
                                    data-confirm-label="{{ __('Delete contact') }}"
                                    data-confirm-variant="error"
                                    data-id="1"
                                    aria-label="{{ __('Delete') }} {{ __('Dana Whitfield') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="nophone" data-role="desk">
                        <td data-card-title>
                            <span class="ct__who">
                                <span class="ct__avatar" aria-hidden="true">FD</span>
                                <span class="min-w-0">
                                    <span class="ct__name">{{ __('Front desk') }}</span>
                                    <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                        {{ __('General enquiries') }}
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td data-label="{{ __('Role') }}">{{ __('Front desk') }}</td>
                        <td data-label="{{ __('Email') }}">
                            <a href="#" class="d-table__mail">hello@bartonsprings.com</a>
                        </td>
                        <td data-label="{{ __('Phone') }}" class="d-table__muted">—</td>
                        <td data-label="{{ __('Business') }}">
                            <a href="#" class="d-table__id">{{ __('Barton Springs Dental') }}</a>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-modal-open="contactModal"
                                    aria-label="{{ __('Edit') }} {{ __('Front desk') }}"
                                >
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this contact?') }}"
                                    data-confirm-body="{{ __('The front desk contact is removed. Barton Springs Dental stays in your leads, with Dana Whitfield as its primary contact.') }}"
                                    data-confirm-label="{{ __('Delete contact') }}"
                                    data-confirm-variant="error"
                                    data-id="2"
                                    aria-label="{{ __('Delete') }} {{ __('Front desk') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="primary" data-role="owner">
                        <td data-card-title>
                            <span class="ct__who">
                                <span class="ct__avatar" aria-hidden="true">MO</span>
                                <span class="min-w-0">
                                    <span class="ct__name">
                                        {{ __('Marcus Oyelaran') }}
                                        <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Primary') }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                        {{ __('Owner-operator') }}
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td data-label="{{ __('Role') }}">{{ __('Owner') }}</td>
                        <td data-label="{{ __('Email') }}">
                            <a href="#" class="d-table__mail">info@zilkersmile.com</a>
                        </td>
                        <td data-label="{{ __('Phone') }}" class="numeric whitespace-nowrap">
                            (512) 555-0198
                        </td>
                        <td data-label="{{ __('Business') }}">
                            <a href="#" class="d-table__id">{{ __('Zilker Smile Studio') }}</a>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-modal-open="contactModal"
                                    aria-label="{{ __('Edit') }} {{ __('Marcus Oyelaran') }}"
                                >
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete Marcus Oyelaran?') }}"
                                    data-confirm-body="{{ __('The contact is removed. Zilker Smile Studio stays in your leads.') }}"
                                    data-confirm-label="{{ __('Delete contact') }}"
                                    data-confirm-variant="error"
                                    data-id="3"
                                    aria-label="{{ __('Delete') }} {{ __('Marcus Oyelaran') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="primary" data-role="manager">
                        <td data-card-title>
                            <span class="ct__who">
                                <span class="ct__avatar" aria-hidden="true">SK</span>
                                <span class="min-w-0">
                                    <span class="ct__name">
                                        {{ __('Sofia Kaur') }}
                                        <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Primary') }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                        {{ __('Handles scheduling') }}
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td data-label="{{ __('Role') }}">{{ __('Office manager') }}</td>
                        <td data-label="{{ __('Email') }}">
                            <a href="#" class="d-table__mail">front@lamardental.com</a>
                        </td>
                        <td data-label="{{ __('Phone') }}" class="numeric whitespace-nowrap">
                            (512) 555-0176
                        </td>
                        <td data-label="{{ __('Business') }}">
                            <a href="#" class="d-table__id">{{ __('Lamar Family Dentistry') }}</a>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-modal-open="contactModal"
                                    aria-label="{{ __('Edit') }} {{ __('Sofia Kaur') }}"
                                >
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete Sofia Kaur?') }}"
                                    data-confirm-body="{{ __('The contact is removed. Lamar Family Dentistry stays in your leads.') }}"
                                    data-confirm-label="{{ __('Delete contact') }}"
                                    data-confirm-variant="error"
                                    data-id="4"
                                    aria-label="{{ __('Delete') }} {{ __('Sofia Kaur') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="primary" data-role="owner">
                        <td data-card-title>
                            <span class="ct__who">
                                <span class="ct__avatar" aria-hidden="true">EB</span>
                                <span class="min-w-0">
                                    <span class="ct__name">
                                        {{ __('Elena Brady') }}
                                        <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Primary') }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                        {{ __('Founder') }}
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td data-label="{{ __('Role') }}">{{ __('Owner') }}</td>
                        <td data-label="{{ __('Email') }}">
                            <a href="#" class="d-table__mail">care@hydeparkdental.com</a>
                        </td>
                        <td data-label="{{ __('Phone') }}" class="numeric whitespace-nowrap">
                            (512) 555-0121
                        </td>
                        <td data-label="{{ __('Business') }}">
                            <a href="#" class="d-table__id">{{ __('Hyde Park Dental Care') }}</a>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-modal-open="contactModal"
                                    aria-label="{{ __('Edit') }} {{ __('Elena Brady') }}"
                                >
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete Elena Brady?') }}"
                                    data-confirm-body="{{ __('The contact is removed. Hyde Park Dental Care stays in your leads.') }}"
                                    data-confirm-label="{{ __('Delete contact') }}"
                                    data-confirm-variant="error"
                                    data-id="5"
                                    aria-label="{{ __('Delete') }} {{ __('Elena Brady') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="primary" data-role="manager">
                        <td data-card-title>
                            <span class="ct__who">
                                <span class="ct__avatar" aria-hidden="true">TN</span>
                                <span class="min-w-0">
                                    <span class="ct__name">
                                        {{ __('Thao Nguyen') }}
                                        <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Primary') }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                        {{ __('Bookings and spa floor') }}
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td data-label="{{ __('Role') }}">{{ __('Spa manager') }}</td>
                        <td data-label="{{ __('Email') }}">
                            <a href="#" class="d-table__mail">hello@desertbloom.com</a>
                        </td>
                        <td data-label="{{ __('Phone') }}" class="numeric whitespace-nowrap">
                            (602) 555-0164
                        </td>
                        <td data-label="{{ __('Business') }}">
                            <a href="#" class="d-table__id">{{ __('Desert Bloom Med Spa') }}</a>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-modal-open="contactModal"
                                    aria-label="{{ __('Edit') }} {{ __('Thao Nguyen') }}"
                                >
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete Thao Nguyen?') }}"
                                    data-confirm-body="{{ __('The contact is removed. Desert Bloom Med Spa stays in your leads.') }}"
                                    data-confirm-label="{{ __('Delete contact') }}"
                                    data-confirm-variant="error"
                                    data-id="6"
                                    aria-label="{{ __('Delete') }} {{ __('Thao Nguyen') }}"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-list-key="nophone" data-role="owner">
                        <td data-card-title>
                            <span class="ct__who">
                                <span class="ct__avatar" aria-hidden="true">RO</span>
                                <span class="min-w-0">
                                    <span class="ct__name">{{ __('Rafael Ortiz') }}</span>
                                    <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                        {{ __('Email only — no number found') }}
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td data-label="{{ __('Role') }}">{{ __('Owner') }}</td>
                        <td data-label="{{ __('Email') }}">
                            <a href="#" class="d-table__mail">rafael@southlamarortho.com</a>
                        </td>
                        <td data-label="{{ __('Phone') }}" class="d-table__muted">—</td>
                        <td data-label="{{ __('Business') }}">
                            <a href="#" class="d-table__id">{{ __('South Lamar Orthodontics') }}</a>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-modal-open="contactModal"
                                    aria-label="{{ __('Edit') }} {{ __('Rafael Ortiz') }}"
                                >
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete Rafael Ortiz?') }}"
                                    data-confirm-body="{{ __('The contact is removed. South Lamar Orthodontics stays in your leads.') }}"
                                    data-confirm-label="{{ __('Delete contact') }}"
                                    data-confirm-variant="error"
                                    data-id="7"
                                    aria-label="{{ __('Delete') }} {{ __('Rafael Ortiz') }}"
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
            <p class="no-results__title">{{ __('No contacts match') }}</p>
            <p class="no-results__body">
                {{ __('Try a different role, or clear the search to see everyone.') }}
            </p>
        </div>
    </div>

    <div class="empty is-hidden">
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-address-book"></i>
        </span>
        <h2 class="empty__title">{{ __('No contacts yet') }}</h2>
        <p class="empty__body">
            {{ __('Contacts appear when a search finds who to reach at a business. Run a search, and the people come with the leads.') }}
        </p>
        <a href="{{ route('user.search.new') }}" class="btn btn-primary btn-sm">
            <span class="btn__label">
                <span>{{ __('New search') }}</span>
                <span aria-hidden="true">{{ __('New search') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </div>

    @push('modals')
        <div class="modal" id="contactModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-lg p-6" role="dialog" aria-modal="true" aria-labelledby="contactModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="contactModalTitle">{{ __('Add a contact') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('A person at one of your businesses. Adding a contact costs no credits.') }}
                    </p>

                    <div class="ct__form">
                        <div>
                            <label for="ct-name" class="form-label">{{ __('Full name') }}</label>
                            <input
                                type="text"
                                id="ct-name"
                                name="name"
                                class="form-input"
                                placeholder="{{ __('Dana Whitfield') }}"
                                required
                            />
                        </div>

                        <div>
                            <label for="ct-role" class="form-label">{{ __('Role') }}</label>
                            <input
                                type="text"
                                id="ct-role"
                                name="role"
                                class="form-input"
                                placeholder="{{ __('Practice manager') }}"
                            />
                        </div>

                        <div>
                            <label for="ct-email" class="form-label">{{ __('Email') }}</label>
                            <input
                                type="email"
                                id="ct-email"
                                name="email"
                                class="form-input"
                                placeholder="name@business.com"
                            />
                        </div>

                        <div>
                            <label for="ct-phone" class="form-label">{{ __('Phone') }}</label>
                            <input
                                type="tel"
                                id="ct-phone"
                                name="phone"
                                class="form-input"
                                placeholder="(512) 555-0143"
                            />
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="ct-biz" class="form-label">{{ __('Business') }}</label>
                        <select id="ct-biz" name="business_id" class="form-input" required>
                            <option value="">{{ __('Choose a business') }}</option>
                            <option value="1">{{ __('Barton Springs Dental') }}</option>
                            <option value="2">{{ __('Zilker Smile Studio') }}</option>
                            <option value="3">{{ __('Lamar Family Dentistry') }}</option>
                            <option value="4">{{ __('Hyde Park Dental Care') }}</option>
                            <option value="5">{{ __('Desert Bloom Med Spa') }}</option>
                            <option value="6">{{ __('South Lamar Orthodontics') }}</option>
                        </select>
                        <p class="form-hint">
                            {{ __('A business can have as many contacts as you need.') }}
                        </p>
                    </div>

                    <label class="ct__primary" for="ct-primary">
                        <input
                            type="checkbox"
                            id="ct-primary"
                            name="is_primary"
                            class="form-check"
                        />
                        <span>
                            <span class="block text-[0.875rem] font-semibold text-title">{{ __('Primary contact') }}</span>
                            <span class="mt-1 block text-[0.8125rem] leading-[1.55] text-body">
                                {{ __('Who outreach is addressed to. You still read and approve every email before it goes.') }}
                            </span>
                        </span>
                    </label>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            <span class="btn__label">
                                <span>{{ __('Cancel') }}</span>
                                <span aria-hidden="true">{{ __('Cancel') }}</span>
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label">
                                <span>{{ __('Save contact') }}</span>
                                <span aria-hidden="true">{{ __('Save contact') }}</span>
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

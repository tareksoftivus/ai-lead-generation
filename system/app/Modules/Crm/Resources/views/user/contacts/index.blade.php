<x-layouts.user :title="__('Contacts')">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Contacts') }}</h2>
        </div>

        <button type="button" class="btn btn-primary btn-sm shrink-0" data-modal-open="contactModal">
            <span class="btn__label">
                <span>{{ __('Add contact') }}</span>
                <span aria-hidden="true">{{ __('Add contact') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </button>
    </div>

    <div class="panel{{ $contacts->isEmpty() ? ' is-hidden' : '' }}" data-list>
        <div class="panel__head">
            <h3 class="panel__title">{{ __('All contacts') }}</h3>
            <span class="panel__meta">
                <span class="numeric">{{ $contacts->count() }}</span> {{ __('people') }} ·
                <span class="numeric">{{ $contacts->pluck('lead_id')->unique()->count() }}</span> {{ __('businesses') }}
            </span>
        </div>

        <nav class="app-tablist" aria-label="{{ __('Filter contacts') }}">
            <button type="button" class="app-tab is-active" data-list-tab="all" aria-current="page">
                {{ __('All') }} <span class="app-tab__count">{{ $contacts->count() }}</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="primary">
                {{ __('Primary') }} <span class="app-tab__count">{{ $contacts->where('is_primary', true)->count() }}</span>
            </button>
            <button type="button" class="app-tab" data-list-tab="nophone">
                {{ __('No phone') }} <span class="app-tab__count">{{ $contacts->filter(fn ($contact) => blank($contact->phone))->count() }}</span>
            </button>
        </nav>

        <div class="list-toolbar">
            <label for="c-search" class="sr-only">{{ __('Search contacts') }}</label>
            <div class="search-field">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="c-search" class="form-input" placeholder="{{ __('Search by name, business, or email') }}" data-list-search />
            </div>

            <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="role" data-value="all">
                <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                    <i class="ph ph-user-circle" aria-hidden="true"></i>
                    <span data-dropdown-label>{{ __('Any role') }}</span>
                    <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                </button>

                <div class="menu__panel" data-dropdown-panel>
                    <button type="button" class="menu__item is-selected" data-value="all">{{ __('Any role') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                    @foreach ($contacts->pluck('role')->filter()->map(fn ($role) => \Illuminate\Support\Str::slug($role))->unique() as $roleKey)
                        <button type="button" class="menu__item" data-value="{{ $roleKey }}">
                            {{ \Illuminate\Support\Str::of($roleKey)->replace('-', ' ')->title() }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                    @endforeach
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
                    @foreach ($contacts as $contact)
                        @php
                            $roleKey = $contact->role ? \Illuminate\Support\Str::slug($contact->role) : 'unknown';
                            $initials = \Illuminate\Support\Str::of($contact->name)->explode(' ')->filter()->take(2)->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->implode('');
                        @endphp
                        <tr data-list-key="{{ trim(($contact->is_primary ? 'primary ' : '').(blank($contact->phone) ? 'nophone' : '')) ?: 'contact' }}" data-role="{{ $roleKey }}">
                            <td data-card-title>
                                <span class="ct__who">
                                    <span class="ct__avatar" aria-hidden="true">{{ $initials ?: 'CT' }}</span>
                                    <span class="min-w-0">
                                        <span class="ct__name">
                                            {{ $contact->name }}
                                            @if ($contact->is_primary)
                                                <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Primary') }}</span>
                                            @endif
                                        </span>
                                        <span class="mt-0.5 block truncate text-[0.8125rem] text-body">
                                            {{ $contact->note ?: ($contact->last_contacted_at ? __('Reached :date', ['date' => $contact->last_contacted_at->format('j M')]) : __('Contact from saved lead')) }}
                                        </span>
                                    </span>
                                </span>
                            </td>
                            <td data-label="{{ __('Role') }}">{{ $contact->role ?: __('Contact') }}</td>
                            <td data-label="{{ __('Email') }}">
                                @if ($contact->email)
                                    <a href="mailto:{{ $contact->email }}" class="d-table__mail">{{ $contact->email }}</a>
                                @else
                                    <span class="d-table__muted">{{ __('No email') }}</span>
                                @endif
                            </td>
                            <td data-label="{{ __('Phone') }}" class="numeric whitespace-nowrap">
                                @if ($contact->phone)
                                    <a href="tel:{{ $contact->phone }}" class="d-table__tel numeric">{{ $contact->phone }}</a>
                                @else
                                    <span class="d-table__muted">-</span>
                                @endif
                            </td>
                            <td data-label="{{ __('Business') }}">
                                <a href="{{ route('user.leads.show', $contact->lead) }}" class="d-table__id">{{ $contact->lead?->place?->name }}</a>
                            </td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button
                                        type="button"
                                        class="row-icon"
                                        data-modal-open="contactModal"
                                        data-contact-edit
                                        data-action="{{ route('user.contacts.update', $contact) }}"
                                        data-name="{{ $contact->name }}"
                                        data-role="{{ $contact->role }}"
                                        data-email="{{ $contact->email }}"
                                        data-phone="{{ $contact->phone }}"
                                        data-note="{{ $contact->note }}"
                                        data-lead-id="{{ $contact->lead_id }}"
                                        data-primary="{{ $contact->is_primary ? '1' : '0' }}"
                                        aria-label="{{ __('Edit :name', ['name' => $contact->name]) }}"
                                    >
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="row-icon"
                                        data-confirm
                                        data-confirm-title="{{ __('Delete :name?', ['name' => $contact->name]) }}"
                                        data-confirm-body="{{ __('The contact is removed. The business stays in your leads.') }}"
                                        data-confirm-label="{{ __('Delete contact') }}"
                                        data-confirm-variant="error"
                                        data-submit-form="delete-contact-{{ $contact->id }}"
                                        aria-label="{{ __('Delete :name', ['name' => $contact->name]) }}"
                                    >
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                    <form id="delete-contact-{{ $contact->id }}" action="{{ route('user.contacts.destroy', $contact) }}" method="post" hidden>
                                        @csrf
                                        @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="no-results is-hidden" data-list-empty>
            <span class="no-results__icon" aria-hidden="true">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <p class="no-results__title">{{ __('No contacts match') }}</p>
            <p class="no-results__body">{{ __('Try a different role, or clear the search to see everyone.') }}</p>
        </div>
    </div>

    <section class="panel empty" @if (! $contacts->isEmpty()) hidden @endif>
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-address-book"></i>
        </span>
        <h2 class="empty__title">{{ __('No contacts yet') }}</h2>
        <p class="empty__body">
            {{ __('Contacts appear when a search finds who to reach at a business. You can also add one manually.') }}
        </p>
        <a href="{{ route('user.search.new') }}" class="btn btn-primary btn-sm">
            <span class="btn__label">
                <span>{{ __('New search') }}</span>
                <span aria-hidden="true">{{ __('New search') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </section>

    @push('modals')
        <div class="modal" id="contactModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-lg p-6" role="dialog" aria-modal="true" aria-labelledby="contactModalTitle">
                <form action="{{ route('user.contacts.store') }}" method="post" data-crm-contact-form>
                    @csrf
                    <input type="hidden" name="_method" value="post" data-crm-method />

                    <h2 class="heading-3" id="contactModalTitle" data-crm-contact-title>{{ __('Add a contact') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('A person at one of your businesses. Adding a contact costs no credits.') }}
                    </p>

                    <div class="ct__form">
                        <div>
                            <label for="ct-name" class="form-label">{{ __('Full name') }}</label>
                            <input type="text" id="ct-name" name="name" class="form-input" placeholder="{{ __('Dana Whitfield') }}" required />
                        </div>

                        <div>
                            <label for="ct-role" class="form-label">{{ __('Role') }}</label>
                            <input type="text" id="ct-role" name="role" class="form-input" placeholder="{{ __('Practice manager') }}" />
                        </div>

                        <div>
                            <label for="ct-email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" id="ct-email" name="email" class="form-input" placeholder="name@business.com" />
                        </div>

                        <div>
                            <label for="ct-phone" class="form-label">{{ __('Phone') }}</label>
                            <input type="tel" id="ct-phone" name="phone" class="form-input" placeholder="+880 1711-000101" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="ct-note" class="form-label">{{ __('Note') }}</label>
                        <input type="text" id="ct-note" name="note" class="form-input" placeholder="{{ __('Handles scheduling') }}" />
                    </div>

                    <div class="mt-4">
                        <label for="ct-biz" class="form-label">{{ __('Business') }}</label>
                        <select id="ct-biz" name="lead_id" class="form-input" required>
                            <option value="">{{ __('Choose a business') }}</option>
                            @foreach ($leads as $lead)
                                <option value="{{ $lead->id }}">{{ $lead->place?->name }}</option>
                            @endforeach
                        </select>
                        <p class="form-hint">{{ __('A business can have as many contacts as you need.') }}</p>
                    </div>

                    <label class="ct__primary" for="ct-primary">
                        <input type="checkbox" id="ct-primary" name="is_primary" value="1" class="form-check" />
                        <span>
                            <span class="block text-[0.875rem] font-semibold text-title">{{ __('Primary contact') }}</span>
                            <span class="mt-1 block text-[0.8125rem] leading-[1.55] text-body">
                                {{ __('Who outreach is addressed to. You still read and approve every email before it goes.') }}
                            </span>
                        </span>
                    </label>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            <span class="btn__label"><span>{{ __('Cancel') }}</span><span aria-hidden="true">{{ __('Cancel') }}</span></span>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label"><span>{{ __('Save contact') }}</span><span aria-hidden="true">{{ __('Save contact') }}</span></span>
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

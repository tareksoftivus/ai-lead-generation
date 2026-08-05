<x-layouts.user :title="__('Lists & tags')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Lists & tags') }}</h2>
        <p class="m-text mt-1">
            {{ __('Your own way of grouping leads, kept apart from what the AI decided.') }}
        </p>
    </div>

    <div class="panel" data-tabs>
        <div class="app-tablist" role="tablist">
            <button type="button" class="app-tab is-active" data-tab="lists" role="tab" aria-selected="true">
                {{ __('Lists') }}
                <span class="app-tab__count">4</span>
            </button>
            <button type="button" class="app-tab" data-tab="tags" role="tab" aria-selected="false">
                {{ __('Tags') }}
                <span class="app-tab__count">5</span>
            </button>
        </div>

        <div data-tab-panel="lists">
            <div class="list-toolbar">
                <p class="m-text">
                    {{ __('A list is a saved set of leads. Opening one shows it in the usual table.') }}
                </p>

                <button type="button" class="btn btn-primary btn-sm ml-auto" data-modal-open="listModal">
                    <span class="btn__label">
                        <span>{{ __('New list') }}</span>
                        <span aria-hidden="true">{{ __('New list') }}</span>
                    </span>
                    <i class="ph ph-plus"></i>
                </button>
            </div>

            <div class="tbl-wrap">
                <table class="d-table d-table--cards">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('List') }}</th>
                            <th scope="col">{{ __('Source') }}</th>
                            <th scope="col" class="text-right">{{ __('Leads') }}</th>
                            <th scope="col">{{ __('Updated') }}</th>
                            <th scope="col" class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-card-title>
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">{{ __('Austin dentists — Q3') }}</a>
                                <p class="d-table__place">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>
                                    {{ __('Austin, TX') }}
                                </p>
                            </td>
                            <td data-label="{{ __('Source') }}">
                                <span class="tag-pill">{{ __('From a search') }}</span>
                            </td>
                            <td data-label="{{ __('Leads') }}" class="text-right">
                                <span class="numeric">142</span>
                            </td>
                            <td data-label="{{ __('Updated') }}">{{ __('2 days ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm">
                                        <span class="btn__label">
                                            <span>{{ __('Open') }}</span>
                                            <span aria-hidden="true">{{ __('Open') }}</span>
                                        </span>
                                    </a>
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="listModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this list?') }}"
                                            data-confirm-body="{{ __('“Austin dentists — Q3” will be removed. The 142 leads inside it stay in your account.') }}"
                                            data-confirm-label="{{ __('Delete list') }}" data-confirm-variant="error" data-id="1">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">{{ __('Warm — follow up') }}</a>
                                <p class="d-table__place">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>
                                    {{ __('Mixed') }}
                                </p>
                            </td>
                            <td data-label="{{ __('Source') }}">
                                <span class="tag-pill">{{ __('Added by hand') }}</span>
                            </td>
                            <td data-label="{{ __('Leads') }}" class="text-right">
                                <span class="numeric">38</span>
                            </td>
                            <td data-label="{{ __('Updated') }}">{{ __('5 days ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm">
                                        <span class="btn__label">
                                            <span>{{ __('Open') }}</span>
                                            <span aria-hidden="true">{{ __('Open') }}</span>
                                        </span>
                                    </a>
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="listModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this list?') }}"
                                            data-confirm-body="{{ __('“Warm — follow up” will be removed. The 38 leads inside it stay in your account.') }}"
                                            data-confirm-label="{{ __('Delete list') }}" data-confirm-variant="error" data-id="2">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">{{ __('Chicago clinics') }}</a>
                                <p class="d-table__place">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>
                                    {{ __('Chicago, IL') }}
                                </p>
                            </td>
                            <td data-label="{{ __('Source') }}">
                                <span class="tag-pill">{{ __('From a search') }}</span>
                            </td>
                            <td data-label="{{ __('Leads') }}" class="text-right">
                                <span class="numeric">96</span>
                            </td>
                            <td data-label="{{ __('Updated') }}">{{ __('2 weeks ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm">
                                        <span class="btn__label">
                                            <span>{{ __('Open') }}</span>
                                            <span aria-hidden="true">{{ __('Open') }}</span>
                                        </span>
                                    </a>
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="listModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this list?') }}"
                                            data-confirm-body="{{ __('“Chicago clinics” will be removed. The 96 leads inside it stay in your account.') }}"
                                            data-confirm-label="{{ __('Delete list') }}" data-confirm-variant="error" data-id="3">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">{{ __('Do not contact') }}</a>
                                <p class="d-table__place">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>
                                    {{ __('Mixed') }}
                                </p>
                            </td>
                            <td data-label="{{ __('Source') }}">
                                <span class="tag-pill">{{ __('Added by hand') }}</span>
                            </td>
                            <td data-label="{{ __('Leads') }}" class="text-right">
                                <span class="numeric">12</span>
                            </td>
                            <td data-label="{{ __('Updated') }}">{{ __('1 month ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm">
                                        <span class="btn__label">
                                            <span>{{ __('Open') }}</span>
                                            <span aria-hidden="true">{{ __('Open') }}</span>
                                        </span>
                                    </a>
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="listModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this list?') }}"
                                            data-confirm-body="{{ __('“Do not contact” will be removed. The 12 leads inside it stay in your account.') }}"
                                            data-confirm-label="{{ __('Delete list') }}" data-confirm-variant="error" data-id="4">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="empty is-hidden">
                <span class="empty__icon" aria-hidden="true">
                    <i class="ph ph-folders"></i>
                </span>
                <h2 class="empty__title">{{ __('No lists yet') }}</h2>
                <p class="empty__body">
                    {{ __('A list keeps a set of leads together — everyone you found in one city, or everyone worth a second call. Save a search result to start one.') }}
                </p>
                <button type="button" class="btn btn-primary" data-modal-open="listModal">
                    <span class="btn__label">
                        <span>{{ __('Create a list') }}</span>
                        <span aria-hidden="true">{{ __('Create a list') }}</span>
                    </span>
                    <i class="ph ph-plus"></i>
                </button>
            </div>
        </div>

        <div data-tab-panel="tags" class="is-hidden">
            <div class="list-toolbar">
                <p class="m-text">
                    {{ __('A tag is your own label. It never changes a lead\'s AI score.') }}
                </p>

                <button type="button" class="btn btn-primary btn-sm ml-auto" data-modal-open="tagModal">
                    <span class="btn__label">
                        <span>{{ __('New tag') }}</span>
                        <span aria-hidden="true">{{ __('New tag') }}</span>
                    </span>
                    <i class="ph ph-plus"></i>
                </button>
            </div>

            <div class="tbl-wrap">
                <table class="d-table d-table--cards">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Tag') }}</th>
                            <th scope="col" class="text-right">{{ __('Used on') }}</th>
                            <th scope="col">{{ __('Added') }}</th>
                            <th scope="col" class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-card-title>
                                <span class="tag-pill">{{ __('High value') }}</span>
                            </td>
                            <td data-label="{{ __('Used on') }}" class="text-right">
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">
                                    <span class="numeric">64</span> {{ __('leads') }}
                                </a>
                            </td>
                            <td data-label="{{ __('Added') }}">{{ __('3 months ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="tagModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this tag?') }}"
                                            data-confirm-body="{{ __('“High value” will be taken off 64 leads. The leads themselves are not deleted.') }}"
                                            data-confirm-label="{{ __('Delete tag') }}" data-confirm-variant="error" data-id="1">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <span class="tag-pill">{{ __('Follow up') }}</span>
                            </td>
                            <td data-label="{{ __('Used on') }}" class="text-right">
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">
                                    <span class="numeric">41</span> {{ __('leads') }}
                                </a>
                            </td>
                            <td data-label="{{ __('Added') }}">{{ __('3 months ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="tagModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this tag?') }}"
                                            data-confirm-body="{{ __('“Follow up” will be taken off 41 leads. The leads themselves are not deleted.') }}"
                                            data-confirm-label="{{ __('Delete tag') }}" data-confirm-variant="error" data-id="2">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <span class="tag-pill">{{ __('Phone only') }}</span>
                            </td>
                            <td data-label="{{ __('Used on') }}" class="text-right">
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">
                                    <span class="numeric">27</span> {{ __('leads') }}
                                </a>
                            </td>
                            <td data-label="{{ __('Added') }}">{{ __('6 weeks ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="tagModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this tag?') }}"
                                            data-confirm-body="{{ __('“Phone only” will be taken off 27 leads. The leads themselves are not deleted.') }}"
                                            data-confirm-label="{{ __('Delete tag') }}" data-confirm-variant="error" data-id="3">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <span class="tag-pill">{{ __('Hot') }}</span>
                            </td>
                            <td data-label="{{ __('Used on') }}" class="text-right">
                                <a href="{{ route('user.leads.index') }}" class="d-table__id">
                                    <span class="numeric">9</span> {{ __('leads') }}
                                </a>
                            </td>
                            <td data-label="{{ __('Added') }}">{{ __('2 weeks ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="tagModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this tag?') }}"
                                            data-confirm-body="{{ __('“Hot” will be taken off 9 leads. The leads themselves are not deleted.') }}"
                                            data-confirm-label="{{ __('Delete tag') }}" data-confirm-variant="error" data-id="4">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <span class="tag-pill">{{ __('Seasonal') }}</span>
                            </td>
                            <td data-label="{{ __('Used on') }}" class="text-right">
                                <span class="d-table__muted">{{ __('Not used yet') }}</span>
                            </td>
                            <td data-label="{{ __('Added') }}">{{ __('1 week ago') }}</td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button type="button" class="row-icon" aria-label="{{ __('Rename') }}" data-modal-open="tagModal">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                            data-confirm data-confirm-title="{{ __('Delete this tag?') }}"
                                            data-confirm-body="{{ __('“Seasonal” is not on any lead, so nothing else changes.') }}"
                                            data-confirm-label="{{ __('Delete tag') }}" data-confirm-variant="error" data-id="5">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="empty is-hidden">
                <span class="empty__icon" aria-hidden="true">
                    <i class="ph ph-tag"></i>
                </span>
                <h2 class="empty__title">{{ __('No tags yet') }}</h2>
                <p class="empty__body">
                    {{ __('Tags are short labels you write yourself — “Hot”, “Phone only”, “Follow up in Q3”. Add one from any lead, or start it here.') }}
                </p>
                <button type="button" class="btn btn-primary" data-modal-open="tagModal">
                    <span class="btn__label">
                        <span>{{ __('Create a tag') }}</span>
                        <span aria-hidden="true">{{ __('Create a tag') }}</span>
                    </span>
                    <i class="ph ph-plus"></i>
                </button>
            </div>
        </div>
    </div>

    @push('modals')
        <div class="modal" id="listModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="listModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="listModalTitle">{{ __('New list') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('A list holds a set of leads. You can add to it from any search result.') }}
                    </p>

                    <div>
                        <label for="list-name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" id="list-name" name="name" class="form-input" placeholder="{{ __('Austin dentists — Q3') }}" required />
                        <p class="form-hint">{{ __('Only you see this.') }}</p>
                    </div>

                    <div class="mt-4">
                        <label for="list-note" class="form-label">{{ __('Note') }}</label>
                        <textarea id="list-note" name="note" class="form-input" rows="2" placeholder="{{ __('Who is in here and why') }}"></textarea>
                        <p class="form-hint">{{ __('Optional.') }}</p>
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
                                <span>{{ __('Save list') }}</span>
                                <span aria-hidden="true">{{ __('Save list') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="tagModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="tagModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="tagModalTitle">{{ __('New tag') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Tags are your own labels — they do not affect the AI score.') }}
                    </p>

                    <div>
                        <label for="tag-label" class="form-label">{{ __('Tag') }}</label>
                        <input type="text" id="tag-label" name="tag" class="form-input" placeholder="{{ __('Follow up in Q3') }}" maxlength="30" required />
                        <p class="form-hint">{{ __('Short labels read best — up to 30 letters.') }}</p>
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
                                <span>{{ __('Save tag') }}</span>
                                <span aria-hidden="true">{{ __('Save tag') }}</span>
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
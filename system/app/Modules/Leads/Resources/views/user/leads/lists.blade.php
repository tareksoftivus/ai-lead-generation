<x-layouts.user :title="__('Lists & tags')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Lists & tags') }}</h2>
    </div>

    <div class="panel" data-tabs>
        <div class="app-tablist" role="tablist">
            <button type="button" class="app-tab is-active" data-tab="lists" role="tab" aria-selected="true">
                {{ __('Lists') }}
                <span class="app-tab__count">{{ $lists->count() }}</span>
            </button>
            <button type="button" class="app-tab" data-tab="tags" role="tab" aria-selected="false">
                {{ __('Tags') }}
                <span class="app-tab__count">{{ $tags->count() }}</span>
            </button>
        </div>

        <div data-tab-panel="lists">
            <div class="list-toolbar">
                <button type="button" class="btn btn-primary btn-sm ml-auto" data-modal-open="listModal">
                    <span class="btn__label">
                        <span>{{ __('New list') }}</span>
                        <span aria-hidden="true">{{ __('New list') }}</span>
                    </span>
                    <i class="ph ph-plus"></i>
                </button>
            </div>

            <div class="tbl-wrap{{ $lists->isEmpty() ? ' is-hidden' : '' }}">
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
                        @foreach ($lists as $list)
                            <tr>
                                <td data-card-title>
                                    <a href="{{ route('user.leads.index', ['list' => $list]) }}" class="d-table__id">{{ $list->name }}</a>
                                    @if ($list->note)
                                        <p class="d-table__place">{{ $list->note }}</p>
                                    @endif
                                </td>
                                <td data-label="{{ __('Source') }}">
                                    <span class="tag-pill">{{ $list->source === 'search' ? __('From a search') : __('Added by hand') }}</span>
                                </td>
                                <td data-label="{{ __('Leads') }}" class="text-right">
                                    <span class="numeric">{{ $list->leads_count }}</span>
                                </td>
                                <td data-label="{{ __('Updated') }}">{{ $list->updated_at->diffForHumans() }}</td>
                                <td data-card-actions class="text-right">
                                    <div class="row-actions">
                                        <a href="{{ route('user.leads.index', ['list' => $list]) }}" class="btn btn-outline btn-sm">
                                            <span class="btn__label">
                                                <span>{{ __('Open') }}</span>
                                                <span aria-hidden="true">{{ __('Open') }}</span>
                                            </span>
                                        </a>
                                        <button type="button" class="row-icon" aria-label="{{ __('Rename') }}"
                                                data-modal-open="listModal" data-rename-id="{{ $list->id }}"
                                                data-rename-name="{{ $list->name }}" data-rename-note="{{ $list->note }}"
                                                data-rename-action="{{ route('user.leads.lists.update', $list) }}">
                                            <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                        </button>
                                        <form action="{{ route('user.leads.lists.destroy', $list) }}" method="post" class="contents">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                                    data-confirm data-confirm-title="{{ __('Delete this list?') }}"
                                                    data-confirm-body="{{ __(':name will be removed. The leads inside it stay in your account.', ['name' => $list->name]) }}"
                                                    data-confirm-label="{{ __('Delete list') }}" data-confirm-variant="error">
                                                <i class="ph ph-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="empty{{ $lists->isEmpty() ? '' : ' is-hidden' }}">
                <span class="empty__icon" aria-hidden="true">
                    <i class="ph ph-folders"></i>
                </span>
                <h2 class="empty__title">{{ __('No lists yet') }}</h2>
                <p class="empty__body">
                    {{ __('A list keeps a set of leads together — everyone you found in one city, or everyone worth a second call.') }}
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

            <div class="tbl-wrap{{ $tags->isEmpty() ? ' is-hidden' : '' }}">
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
                        @foreach ($tags as $tag)
                            <tr>
                                <td data-card-title>
                                    <span class="tag-pill">{{ $tag->name }}</span>
                                </td>
                                <td data-label="{{ __('Used on') }}" class="text-right">
                                    @if ($tag->leads_count > 0)
                                        <a href="{{ route('user.leads.index') }}" class="d-table__id">
                                            <span class="numeric">{{ $tag->leads_count }}</span> {{ __('leads') }}
                                        </a>
                                    @else
                                        <span class="d-table__muted">{{ __('Not used yet') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('Added') }}">{{ $tag->created_at->diffForHumans() }}</td>
                                <td data-card-actions class="text-right">
                                    <div class="row-actions">
                                        <button type="button" class="row-icon" aria-label="{{ __('Rename') }}"
                                                data-modal-open="tagModal" data-rename-id="{{ $tag->id }}"
                                                data-rename-name="{{ $tag->name }}"
                                                data-rename-action="{{ route('user.leads.tags.update', $tag) }}">
                                            <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                        </button>
                                        <form action="{{ route('user.leads.tags.destroy', $tag) }}" method="post" class="contents">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="row-icon row-icon--danger" aria-label="{{ __('Delete') }}"
                                                    data-confirm data-confirm-title="{{ __('Delete this tag?') }}"
                                                    data-confirm-body="{{ __(':name will be taken off :count leads. The leads themselves are not deleted.', ['name' => $tag->name, 'count' => $tag->leads_count]) }}"
                                                    data-confirm-label="{{ __('Delete tag') }}" data-confirm-variant="error">
                                                <i class="ph ph-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="empty{{ $tags->isEmpty() ? '' : ' is-hidden' }}">
                <span class="empty__icon" aria-hidden="true">
                    <i class="ph ph-tag"></i>
                </span>
                <h2 class="empty__title">{{ __('No tags yet') }}</h2>
                <p class="empty__body">
                    {{ __('Tags are short labels you write yourself. Add one from any lead, or start it here.') }}
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.addEventListener('confirm:accepted', (event) => {
                    const trigger = event.target;
                    if (trigger.tagName === 'BUTTON' && trigger.form) {
                        trigger.form.submit();
                    }
                });

                document.addEventListener('click', (event) => {
                    const trigger = event.target.closest('[data-rename-action]');
                    if (!trigger) return;

                    const modalId = trigger.dataset.modalOpen;
                    const modal = document.getElementById(modalId);
                    if (!modal) return;

                    const form = modal.querySelector('form');
                    if (form) form.action = trigger.dataset.renameAction;

                    const nameField = modal.querySelector('[name="name"], [name="tag"]');
                    if (nameField) nameField.value = trigger.dataset.renameName || '';

                    const noteField = modal.querySelector('[name="note"]');
                    if (noteField) noteField.value = trigger.dataset.renameNote || '';
                });
            });
        </script>
    @endpush

    @push('modals')
        <div class="modal" id="listModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="listModalTitle">
                <form action="{{ route('user.leads.lists.store') }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="listModalTitle">{{ __('New list') }}</h2>

                    <div>
                        <label for="list-name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" id="list-name" name="name" class="form-input" placeholder="{{ __('Austin dentists — Q3') }}" required />
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
                <form action="{{ route('user.leads.tags.store') }}" method="post">
                    @csrf
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

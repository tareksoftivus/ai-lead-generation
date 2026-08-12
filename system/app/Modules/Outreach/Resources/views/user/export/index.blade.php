<x-layouts.user :title="__('Export center')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Export center') }}</h2>
    </div>

    <form action="{{ route('user.export.store') }}" method="post">
        @csrf
        <input type="hidden" name="selected_ids" value="{{ implode(',', $selectedIds) }}" />

        <div class="cnew">
            <div class="cnew__main">
                <section class="form-card">
                    <h3 class="form-card__title">{{ __('What to export') }}</h3>

                    <div class="mt-4 grid gap-2">
                        <label class="pickr">
                            <input type="radio" name="source_type" value="selection" class="form-check" @checked($selectedCount > 0) />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('The leads you selected') }}</span>
                                <span class="pickr__meta"><span class="numeric">{{ $selectedCount }}</span> {{ __('leads carried over from All leads') }}</span>
                            </span>
                        </label>
                        <label class="pickr">
                            <input type="radio" name="source_type" value="all" class="form-check" @checked($selectedCount === 0) />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('Every lead in your account') }}</span>
                                <span class="pickr__meta"><span class="numeric">{{ $allCount }}</span> {{ __('leads') }}</span>
                            </span>
                        </label>
                        <label class="pickr">
                            <input type="radio" name="source_type" value="list" class="form-check" />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('A list') }}</span>
                                <span class="pickr__meta">{{ $lists->count() ? __('Choose from your saved lists') : __('No lists yet') }}</span>
                            </span>
                        </label>
                        <label class="pickr">
                            <input type="radio" name="source_type" value="search" class="form-check" />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('One search\'s results') }}</span>
                                <span class="pickr__meta">{{ $searchRuns->count() ? __('Choose from recent searches') : __('No search history yet') }}</span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-4">
                        <label for="x-which" class="form-label">{{ __('Which one') }}</label>
                        <select id="x-which" name="source_id" class="form-input">
                            @foreach ($lists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }} ({{ $list->leads_count }} {{ __('leads') }})</option>
                            @endforeach
                            @foreach ($searchRuns as $run)
                                <option value="{{ $run->id }}">{{ $run->search?->prompt ?? __('Search #:id', ['id' => $run->id]) }} ({{ $run->results_count }} {{ __('found') }})</option>
                            @endforeach
                        </select>
                    </div>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('Columns') }}</h3>

                    <div class="mt-4 grid gap-x-4 gap-y-1 @lg:grid-cols-2 @3xl:grid-cols-3">
                        @foreach ($columns as $key => $column)
                            <label class="exp__col">
                                <input
                                    type="checkbox"
                                    name="columns[]"
                                    value="{{ $key }}"
                                    class="form-check"
                                    @checked($column['default'])
                                    @disabled($column['locked'] ?? false)
                                />
                                @if ($column['locked'] ?? false)
                                    <input type="hidden" name="columns[]" value="{{ $key }}" />
                                @endif
                                <span class="exp__col-body">
                                    <span class="exp__col-name">{{ __($column['label']) }}</span>
                                    <span class="exp__col-note">{{ __($column['note']) }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('File') }}</h3>

                    <div class="mt-4 grid gap-2 @xl:grid-cols-2">
                        <label class="pickr">
                            <input type="radio" name="format" value="csv" class="form-check" checked />
                            <span class="pickr__body">
                                <span class="pickr__name">CSV</span>
                                <span class="pickr__meta">{{ __('Sheets, Excel, any CRM import') }}</span>
                            </span>
                        </label>
                        <label class="pickr">
                            <input type="radio" name="format" value="xlsx" class="form-check" @disabled(! $supportsXlsx) />
                            <span class="pickr__body">
                                <span class="pickr__name">XLSX</span>
                                <span class="pickr__meta">{{ $supportsXlsx ? __('Excel workbook') : __('Requires PHP zip extension') }}</span>
                            </span>
                        </label>
                    </div>

                    <div class="setting-row mt-2">
                        <div class="setting-row__text">
                            <label for="x-noemail" class="setting-row__label">{{ __('Leave out leads with no email') }}</label>
                        </div>
                        <input type="checkbox" id="x-noemail" name="require_email" value="1" class="switch" />
                    </div>
                </section>
            </div>

            <aside class="min-w-0">
                <div class="form-card cnew__sum">
                    <h3 class="form-card__title">{{ __('This export') }}</h3>
                    <dl class="cnew__facts">
                        <div class="cnew__fact">
                            <dt class="cnew__fact-key">{{ __('Rows') }}</dt>
                            <dd class="cnew__fact-val numeric">{{ $selectedCount ?: $allCount }}</dd>
                        </div>
                        <div class="cnew__fact">
                            <dt class="cnew__fact-key">{{ __('Columns') }}</dt>
                            <dd class="cnew__fact-val numeric">{{ collect($columns)->where('default', true)->count() }}</dd>
                        </div>
                        <div class="cnew__fact">
                            <dt class="cnew__fact-key">{{ __('Format') }}</dt>
                            <dd class="cnew__fact-val">CSV</dd>
                        </div>
                    </dl>

                    <p class="exp__free">
                        <i class="ph ph-gift" aria-hidden="true"></i>
                        <span><strong class="text-title">{{ __('No credits.') }}</strong> {{ __('Downloading saved leads is free.') }}</span>
                    </p>

                    <button type="submit" class="btn btn-primary btn-sm mt-4 w-full" @disabled($allCount < 1)>
                        <span class="btn__label"><span>{{ __('Download') }}</span><span aria-hidden="true">{{ __('Download') }}</span></span>
                        <i class="ph ph-download-simple"></i>
                    </button>

                    <p class="mt-3 text-center text-[0.8125rem] leading-[1.5] text-body">
                        {{ __('Each download is kept in the export history below.') }}
                    </p>
                </div>
            </aside>
        </div>
    </form>

    <section class="panel mt-4">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Past exports') }}</h3>
        </div>

        @if ($exports->isNotEmpty())
            <div class="table-scroll">
                <table class="d-table d-table--cards">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('File') }}</th>
                            <th scope="col">{{ __('Source') }}</th>
                            <th scope="col" class="text-right">{{ __('Rows') }}</th>
                            <th scope="col">{{ __('When') }}</th>
                            <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exports as $export)
                            <tr>
                                <td data-card-title>
                                    <span class="d-table__key">{{ $export->filename }}</span>
                                    <p class="d-table__muted text-[0.8125rem]"><span class="numeric">{{ $export->columns_count }}</span> {{ __('columns') }} · {{ strtoupper($export->format) }}</p>
                                </td>
                                <td data-label="{{ __('Source') }}" class="d-table__muted">{{ $export->source_label }}</td>
                                <td data-label="{{ __('Rows') }}" class="numeric text-right">{{ $export->rows_count }}</td>
                                <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                                    <time datetime="{{ $export->created_at->toDateString() }}">{{ $export->created_at->diffForHumans() }}</time>
                                </td>
                                <td data-card-actions class="text-right">
                                    <div class="row-actions">
                                        <a href="{{ route('user.export.download', $export) }}" class="btn btn-sm btn-outline" download>
                                            <span class="btn__label"><span>{{ __('Download') }}</span><span aria-hidden="true">{{ __('Download') }}</span></span>
                                        </a>
                                        <form id="delete-export-{{ $export->id }}" action="{{ route('user.export.destroy', $export) }}" method="post">
                                            @csrf
                                            @method('delete')
                                        </form>
                                        <button
                                            type="button"
                                            class="row-icon"
                                            aria-label="{{ __('Delete this export') }}"
                                            data-confirm
                                            data-submit-form="delete-export-{{ $export->id }}"
                                            data-confirm-title="{{ __('Delete this export?') }}"
                                            data-confirm-body="{{ __('This removes the saved export history. Your leads are not affected.') }}"
                                            data-confirm-label="{{ __('Delete') }}"
                                            data-confirm-variant="error"
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
        @else
            <div class="empty">
                <span class="empty__icon" aria-hidden="true"><i class="ph ph-download-simple"></i></span>
                <h2 class="empty__title">{{ __('No exports yet') }}</h2>
            </div>
        @endif
    </section>

    @push('modals')
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

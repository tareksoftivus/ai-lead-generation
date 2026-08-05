<x-layouts.user :title="__('Export center')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Export center') }}</h2>
        <p class="m-text mt-1">
            {{ __('Your leads, in a file you own. Exporting is free and does not spend credits.') }}
        </p>
    </div>

    <form action="#" method="post">
        <div class="cnew">
            {{-- The decisions --}}
            <div class="cnew__main">
                <section class="form-card">
                    <h3 class="form-card__title">{{ __('What to export') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Starts with whatever you had selected. Pick something else if you would rather export a different set.') }}
                    </p>

                    <div class="mt-4 grid gap-2">
                        <label class="pickr">
                            <input
                                type="radio"
                                name="source"
                                value="selection"
                                class="form-check"
                                checked
                            />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('The leads you selected') }}</span>
                                <span class="pickr__meta">
                                    <span class="numeric">24</span> {{ __('leads, carried over from All leads') }}
                                </span>
                            </span>
                        </label>

                        <label class="pickr">
                            <input
                                type="radio"
                                name="source"
                                value="all"
                                class="form-check"
                            />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('Every lead in your account') }}</span>
                                <span class="pickr__meta">
                                    <span class="numeric">1,284</span> {{ __('leads') }}
                                </span>
                            </span>
                        </label>

                        <label class="pickr">
                            <input
                                type="radio"
                                name="source"
                                value="list"
                                class="form-check"
                            />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('A list') }}</span>
                                <span class="pickr__meta">
                                    {{ __('Austin dentists — Q3, Warm — follow up, and') }}
                                    <span class="numeric">2</span> {{ __('more') }}
                                </span>
                            </span>
                        </label>

                        <label class="pickr">
                            <input
                                type="radio"
                                name="source"
                                value="search"
                                class="form-check"
                            />
                            <span class="pickr__body">
                                <span class="pickr__name">{{ __('One search\'s results') }}</span>
                                <span class="pickr__meta">
                                    {{ __('dentists in Austin, TX and') }}
                                    <span class="numeric">17</span> {{ __('earlier searches') }}
                                </span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-4">
                        <label for="x-which" class="form-label">{{ __('Which one') }}</label>
                        <select id="x-which" name="source_id" class="form-input">
                            <option value="l1">{{ __('Austin dentists — Q3 (142 leads)') }}</option>
                            <option value="l2">{{ __('Warm — follow up (38 leads)') }}</option>
                            <option value="l3">{{ __('Chicago clinics (96 leads)') }}</option>
                            <option value="l4">{{ __('Do not contact (12 leads)') }}</option>
                            <option value="s1">
                                {{ __('dentists in Austin, TX — 19 Jul (184 found)') }}
                            </option>
                            <option value="s2">
                                {{ __('orthodontists in Dallas, TX — today (312 found)') }}
                            </option>
                        </select>
                        <p class="form-hint">
                            {{ __('Only used when you pick a list or a search above.') }}
                        </p>
                    </div>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('Columns') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Everything we hold on a lead. Untick what you do not need — fewer columns make a tidier spreadsheet.') }}
                    </p>

                    <div class="mt-4 grid gap-x-4 gap-y-1 @lg:grid-cols-2 @3xl:grid-cols-3">
                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="business"
                                class="form-check"
                                checked
                                disabled
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Business name') }}</span>
                                <span class="exp__col-note">{{ __('Always included') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="category"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Category') }}</span>
                                <span class="exp__col-note">{{ __('Dentist, clinic, café…') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="address"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Address') }}</span>
                                <span class="exp__col-note">
                                    {{ __('Street, city, postcode, country') }}
                                </span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="phone"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Phone') }}</span>
                                <span class="exp__col-note">{{ __('As listed') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="email"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Email') }}</span>
                                <span class="exp__col-note">{{ __('Blank where none found') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="website"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Website') }}</span>
                                <span class="exp__col-note">{{ __('Domain and full URL') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="score"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">
                                    {{ __('Lead score') }}
                                    <span class="rounded bg-ai/10 px-1.5 py-0.5 text-[0.625rem] font-bold tracking-wide text-ai uppercase">AI</span>
                                </span>
                                <span class="exp__col-note">{{ __('The number, 0–100') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="reasoning"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">
                                    {{ __('Why it scored that') }}
                                    <span class="rounded bg-ai/10 px-1.5 py-0.5 text-[0.625rem] font-bold tracking-wide text-ai uppercase">AI</span>
                                </span>
                                <span class="exp__col-note">{{ __('The written reasoning') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="status"
                                class="form-check"
                                checked
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Status') }}</span>
                                <span class="exp__col-note">
                                    {{ __('New, contacted, qualified…') }}
                                </span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="tags"
                                class="form-check"
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Tags') }}</span>
                                <span class="exp__col-note">{{ __('Comma separated') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="reviews"
                                class="form-check"
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Reviews') }}</span>
                                <span class="exp__col-note">{{ __('Count and rating') }}</span>
                            </span>
                        </label>

                        <label class="exp__col">
                            <input
                                type="checkbox"
                                name="columns[]"
                                value="coords"
                                class="form-check"
                            />
                            <span class="exp__col-body">
                                <span class="exp__col-name">{{ __('Coordinates') }}</span>
                                <span class="exp__col-note">{{ __('Latitude and longitude') }}</span>
                            </span>
                        </label>
                    </div>

                    <p class="exp__note">
                        <i class="ph ph-lightbulb" aria-hidden="true"></i>
                        <span>
                            {{ __('Keep') }} <strong class="text-title">{{ __('Why it scored that') }}</strong>
                            {{ __('ticked if you are handing this file to someone else. A score on its own tells them nothing they can act on.') }}
                        </span>
                    </p>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('File') }}</h3>
                    <p class="form-card__hint">
                        {{ __('CSV opens anywhere. XLSX keeps long text readable and holds the column widths.') }}
                    </p>

                    <div class="mt-4 grid gap-2 @xl:grid-cols-2">
                        <label class="pickr">
                            <input
                                type="radio"
                                name="format"
                                value="csv"
                                class="form-check"
                                checked
                            />
                            <span class="pickr__body">
                                <span class="pickr__name">CSV</span>
                                <span class="pickr__meta">
                                    {{ __('Sheets, Excel, any CRM import') }}
                                </span>
                            </span>
                        </label>

                        <label class="pickr">
                            <input
                                type="radio"
                                name="format"
                                value="xlsx"
                                class="form-check"
                            />
                            <span class="pickr__body">
                                <span class="pickr__name">XLSX</span>
                                <span class="pickr__meta">{{ __('Excel workbook, formatted') }}</span>
                            </span>
                        </label>
                    </div>

                    <div class="setting-row mt-2">
                        <div class="setting-row__text">
                            <label for="x-noemail" class="setting-row__label">
                                {{ __('Leave out leads with no email') }}
                            </label>
                            <p class="setting-row__hint">
                                {{ __('Useful for an email import. Off by default — a phone number is still a lead.') }}
                            </p>
                        </div>
                        <input
                            type="checkbox"
                            id="x-noemail"
                            name="require_email"
                            class="switch"
                        />
                    </div>
                </section>
            </div>

            {{-- The summary --}}
            <aside class="min-w-0">
                <div class="form-card cnew__sum">
                    <h3 class="form-card__title">{{ __('This export') }}</h3>

                    <dl class="cnew__facts">
                        <div class="cnew__fact">
                            <dt class="cnew__fact-key">{{ __('Rows') }}</dt>
                            <dd class="cnew__fact-val numeric">24</dd>
                        </div>
                        <div class="cnew__fact">
                            <dt class="cnew__fact-key">{{ __('Columns') }}</dt>
                            <dd class="cnew__fact-val numeric">9</dd>
                        </div>
                        <div class="cnew__fact">
                            <dt class="cnew__fact-key">{{ __('Format') }}</dt>
                            <dd class="cnew__fact-val">CSV</dd>
                        </div>
                    </dl>

                    <p class="exp__free">
                        <i class="ph ph-gift" aria-hidden="true"></i>
                        <span>
                            <strong class="text-title">{{ __('No credits.') }}</strong>
                            {{ __('You paid when these leads were found. Downloading them, now or a year from now, is free.') }}
                        </span>
                    </p>

                    <button
                        type="submit"
                        class="btn btn-primary btn-sm mt-4 w-full"
                    >
                        <span class="btn__label">
                            <span>{{ __('Download') }}</span>
                            <span aria-hidden="true">{{ __('Download') }}</span>
                        </span>
                        <i class="ph ph-download-simple"></i>
                    </button>

                    <p class="mt-3 text-center text-[0.8125rem] leading-[1.5] text-body">
                        {{ __('Large exports are prepared in the background — we will email you the link and keep it below.') }}
                    </p>
                </div>
            </aside>
        </div>
    </form>

    <section class="panel mt-4">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Past exports') }}</h3>
            <span class="panel__meta">
                {{ __('Kept for') }} <span class="numeric">90</span> {{ __('days') }}
            </span>
        </div>

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
                    <tr>
                        <td data-card-title>
                            <span class="d-table__key">austin-dentists-q3.csv</span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">9</span> {{ __('columns') }} · CSV
                            </p>
                        </td>
                        <td data-label="{{ __('Source') }}" class="d-table__muted">
                            {{ __('Austin dentists — Q3') }}
                        </td>
                        <td data-label="{{ __('Rows') }}" class="numeric text-right">142</td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-21">{{ __('Today') }}</time>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <a href="#" class="btn btn-sm btn-outline" download>
                                    <span class="btn__label">
                                        <span>{{ __('Download') }}</span>
                                        <span aria-hidden="true">{{ __('Download') }}</span>
                                    </span>
                                </a>
                                <button
                                    type="button"
                                    class="row-icon"
                                    aria-label="{{ __('Delete this export') }}"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this export?') }}"
                                    data-confirm-body="{{ __('This removes the saved file. Your leads are not affected, and you can export them again for free at any time.') }}"
                                    data-confirm-label="{{ __('Delete') }}"
                                    data-confirm-variant="error"
                                    data-id="e-914"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td data-card-title>
                            <span class="d-table__key">warm-follow-up.xlsx</span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">6</span> {{ __('columns') }} · XLSX
                            </p>
                        </td>
                        <td data-label="{{ __('Source') }}" class="d-table__muted">
                            {{ __('Warm — follow up') }}
                        </td>
                        <td data-label="{{ __('Rows') }}" class="numeric text-right">38</td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-19">19 {{ __('Jul') }}</time>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <a href="#" class="btn btn-sm btn-outline" download>
                                    <span class="btn__label">
                                        <span>{{ __('Download') }}</span>
                                        <span aria-hidden="true">{{ __('Download') }}</span>
                                    </span>
                                </a>
                                <button
                                    type="button"
                                    class="row-icon"
                                    aria-label="{{ __('Delete this export') }}"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this export?') }}"
                                    data-confirm-body="{{ __('This removes the saved file. Your leads are not affected, and you can export them again for free at any time.') }}"
                                    data-confirm-label="{{ __('Delete') }}"
                                    data-confirm-variant="error"
                                    data-id="e-908"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td data-card-title>
                            <span class="d-table__key">dentists-austin-tx.csv</span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">12</span> {{ __('columns') }} · CSV
                            </p>
                        </td>
                        <td data-label="{{ __('Source') }}" class="d-table__muted">
                            {{ __('Search · dentists in Austin, TX') }}
                        </td>
                        <td data-label="{{ __('Rows') }}" class="numeric text-right">184</td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-19">19 {{ __('Jul') }}</time>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <a href="#" class="btn btn-sm btn-outline" download>
                                    <span class="btn__label">
                                        <span>{{ __('Download') }}</span>
                                        <span aria-hidden="true">{{ __('Download') }}</span>
                                    </span>
                                </a>
                                <button
                                    type="button"
                                    class="row-icon"
                                    aria-label="{{ __('Delete this export') }}"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this export?') }}"
                                    data-confirm-body="{{ __('This removes the saved file. Your leads are not affected, and you can export them again for free at any time.') }}"
                                    data-confirm-label="{{ __('Delete') }}"
                                    data-confirm-variant="error"
                                    data-id="e-877"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td data-card-title>
                            <span class="d-table__key">all-leads.xlsx</span>
                            <p class="d-table__muted text-[0.8125rem]">
                                <span class="numeric">9</span> {{ __('columns') }} · XLSX
                            </p>
                        </td>
                        <td data-label="{{ __('Source') }}" class="d-table__muted">
                            {{ __('Every lead in the account') }}
                        </td>
                        <td data-label="{{ __('Rows') }}" class="numeric text-right">1,102</td>
                        <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                            <time datetime="2026-07-02">2 {{ __('Jul') }}</time>
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <a href="#" class="btn btn-sm btn-outline" download>
                                    <span class="btn__label">
                                        <span>{{ __('Download') }}</span>
                                        <span aria-hidden="true">{{ __('Download') }}</span>
                                    </span>
                                </a>
                                <button
                                    type="button"
                                    class="row-icon"
                                    aria-label="{{ __('Delete this export') }}"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this export?') }}"
                                    data-confirm-body="{{ __('This removes the saved file. Your leads are not affected, and you can export them again for free at any time.') }}"
                                    data-confirm-label="{{ __('Delete') }}"
                                    data-confirm-variant="error"
                                    data-id="e-812"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

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

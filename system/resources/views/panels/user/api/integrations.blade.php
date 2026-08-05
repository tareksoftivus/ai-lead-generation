<x-layouts.user :title="__('Integrations')">
    @include('panels.user.api.nav', ['apiActive' => 'integrations'])

    <div class="mb-4">
        <h2 class="heading-3">{{ __('Integrations') }}</h2>
        <p class="m-text mt-1">
            {{ __('Send your leads where you already work. Connecting is free — you only spend credits finding new businesses.') }}
        </p>
    </div>

    <section class="panel">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Connections') }}</h3>
            <span class="panel__meta">
                <span class="numeric">2</span> {{ __('of') }}
                <span class="numeric">6</span> {{ __('connected') }}
            </span>
        </div>

        <div class="panel__body">
            <p class="mt-5 mb-2 text-[0.75rem] font-semibold tracking-wider text-body uppercase">
                {{ __('Connected') }}
            </p>

            <div class="integs">
                <article class="integ is-connected">
                    <div class="integ__top">
                        <span class="integ__logo integ__logo--hubspot">
                            <i class="ph-fill ph-circles-three" aria-hidden="true"></i>
                        </span>
                        <span class="status status--done">
                            <i class="ph ph-check" aria-hidden="true"></i>
                            {{ __('Connected') }}
                        </span>
                    </div>

                    <h4 class="font-title text-[1rem] font-bold text-title">HubSpot</h4>
                    <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">
                        {{ __('New leads arrive as contacts, with the score and its reasoning on the record.') }}
                    </p>

                    <dl class="integ__facts">
                        <div class="integ__fact">
                            <dt>{{ __('Account') }}</dt>
                            <dd>acme-marketing</dd>
                        </div>
                        <div class="integ__fact">
                            <dt>{{ __('Last sync') }}</dt>
                            <dd><time datetime="2026-07-21T08:40">{{ __('Today, 08:40') }}</time></dd>
                        </div>
                        <div class="integ__fact">
                            <dt>{{ __('Synced') }}</dt>
                            <dd><span class="numeric">1,102</span> {{ __('leads') }}</dd>
                        </div>
                    </dl>

                    <div class="integ__act">
                        <a href="#" class="btn btn-sm btn-outline">
                            <span class="btn__label">
                                <span>{{ __('Settings') }}</span>
                                <span aria-hidden="true">{{ __('Settings') }}</span>
                            </span>
                        </a>
                        <button
                            type="button"
                            class="btn btn-sm btn-ghost text-body hover:bg-error/10 hover:text-error"
                            data-confirm
                            data-confirm-title="{{ __('Disconnect HubSpot?') }}"
                            data-confirm-body="{{ __('New leads stop syncing immediately. The 1,102 contacts already in HubSpot stay there, and your leads stay in LeadAtlas — nothing is deleted on either side.') }}"
                            data-confirm-label="{{ __('Disconnect') }}"
                            data-confirm-variant="error"
                            data-id="hubspot"
                        >
                            <span class="btn__label">
                                <span>{{ __('Disconnect') }}</span>
                                <span aria-hidden="true">{{ __('Disconnect') }}</span>
                            </span>
                        </button>
                    </div>
                </article>

                <article class="integ is-connected">
                    <div class="integ__top">
                        <span class="integ__logo integ__logo--sheets">
                            <i class="ph-fill ph-table" aria-hidden="true"></i>
                        </span>
                        <span class="status status--done">
                            <i class="ph ph-check" aria-hidden="true"></i>
                            {{ __('Connected') }}
                        </span>
                    </div>

                    <h4 class="font-title text-[1rem] font-bold text-title">{{ __('Google Sheets') }}</h4>
                    <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">
                        {{ __('Every new lead appends a row to a sheet you choose. Good for a team that lives in spreadsheets.') }}
                    </p>

                    <dl class="integ__facts">
                        <div class="integ__fact">
                            <dt>{{ __('Sheet') }}</dt>
                            <dd>{{ __('Austin pipeline') }}</dd>
                        </div>
                        <div class="integ__fact">
                            <dt>{{ __('Last sync') }}</dt>
                            <dd><time datetime="2026-07-21T08:40">{{ __('Today, 08:40') }}</time></dd>
                        </div>
                        <div class="integ__fact">
                            <dt>{{ __('Synced') }}</dt>
                            <dd><span class="numeric">284</span> {{ __('leads') }}</dd>
                        </div>
                    </dl>

                    <div class="integ__act">
                        <a href="#" class="btn btn-sm btn-outline">
                            <span class="btn__label">
                                <span>{{ __('Settings') }}</span>
                                <span aria-hidden="true">{{ __('Settings') }}</span>
                            </span>
                        </a>
                        <button
                            type="button"
                            class="btn btn-sm btn-ghost text-body hover:bg-error/10 hover:text-error"
                            data-confirm
                            data-confirm-title="{{ __('Disconnect Google Sheets?') }}"
                            data-confirm-body="{{ __('New leads stop appending. The sheet and its 284 rows stay exactly as they are.') }}"
                            data-confirm-label="{{ __('Disconnect') }}"
                            data-confirm-variant="error"
                            data-id="sheets"
                        >
                            <span class="btn__label">
                                <span>{{ __('Disconnect') }}</span>
                                <span aria-hidden="true">{{ __('Disconnect') }}</span>
                            </span>
                        </button>
                    </div>
                </article>
            </div>

            <p class="mt-5 mb-2 text-[0.75rem] font-semibold tracking-wider text-body uppercase">
                {{ __('Available') }}
            </p>

            <div class="integs">
                <article class="integ">
                    <div class="integ__top">
                        <span class="integ__logo integ__logo--pipedrive">
                            <i class="ph-fill ph-kanban" aria-hidden="true"></i>
                        </span>
                    </div>

                    <h4 class="font-title text-[1rem] font-bold text-title">Pipedrive</h4>
                    <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">
                        {{ __('Push qualified leads straight into a Pipedrive stage as deals.') }}
                    </p>

                    <div class="integ__act">
                        <button type="button" class="btn btn-sm btn-primary" data-modal-open="connectModal">
                            <span class="btn__label">
                                <span>{{ __('Connect') }}</span>
                                <span aria-hidden="true">{{ __('Connect') }}</span>
                            </span>
                            <i class="ph ph-plug"></i>
                        </button>
                    </div>
                </article>

                <article class="integ">
                    <div class="integ__top">
                        <span class="integ__logo integ__logo--salesforce">
                            <i class="ph-fill ph-cloud" aria-hidden="true"></i>
                        </span>
                    </div>

                    <h4 class="font-title text-[1rem] font-bold text-title">Salesforce</h4>
                    <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">
                        {{ __('Create leads in Salesforce with the score mapped to a custom field.') }}
                    </p>

                    <div class="integ__act">
                        <button type="button" class="btn btn-sm btn-primary" data-modal-open="connectModal">
                            <span class="btn__label">
                                <span>{{ __('Connect') }}</span>
                                <span aria-hidden="true">{{ __('Connect') }}</span>
                            </span>
                            <i class="ph ph-plug"></i>
                        </button>
                    </div>
                </article>

                <article class="integ">
                    <div class="integ__top">
                        <span class="integ__logo integ__logo--slack">
                            <i class="ph-fill ph-chat-circle-dots" aria-hidden="true"></i>
                        </span>
                    </div>

                    <h4 class="font-title text-[1rem] font-bold text-title">Slack</h4>
                    <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">
                        {{ __('Post a message to a channel when a search finishes or a lead scores above your threshold.') }}
                    </p>

                    <div class="integ__act">
                        <button type="button" class="btn btn-sm btn-primary" data-modal-open="connectModal">
                            <span class="btn__label">
                                <span>{{ __('Connect') }}</span>
                                <span aria-hidden="true">{{ __('Connect') }}</span>
                            </span>
                            <i class="ph ph-plug"></i>
                        </button>
                    </div>
                </article>

                <article class="integ">
                    <div class="integ__top">
                        <span class="integ__logo integ__logo--zapier">
                            <i class="ph-fill ph-lightning" aria-hidden="true"></i>
                        </span>
                    </div>

                    <h4 class="font-title text-[1rem] font-bold text-title">Zapier</h4>
                    <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">
                        {{ __('Reach the tools without a direct connection — 6,000 apps, no code.') }}
                    </p>

                    <div class="integ__act">
                        <button type="button" class="btn btn-sm btn-primary" data-modal-open="connectModal">
                            <span class="btn__label">
                                <span>{{ __('Connect') }}</span>
                                <span aria-hidden="true">{{ __('Connect') }}</span>
                            </span>
                            <i class="ph ph-plug"></i>
                        </button>
                    </div>
                </article>
            </div>

            <p class="integ__note">
                <i class="ph ph-coins" aria-hidden="true"></i>
                <span>
                    {{ __('Connecting a tool is free, and so is syncing a lead you already own. Credits are only ever spent finding a new business.') }}
                </span>
            </p>
        </div>
    </section>

    <section class="rounded-2xl border border-neutral-200 bg-[#f6f5ff] p-6 sm:p-8 mt-4">
        <span class="apik-lock__icon" aria-hidden="true">
            <i class="ph ph-lock-key"></i>
        </span>

        <h3 class="font-title text-[1.125rem] font-bold text-title">
            {{ __('Webhooks are on the Scale plan') }}
        </h3>
        <p class="mt-2 max-w-[52ch] text-[0.9375rem] leading-[1.6]">
            {{ __('You are on') }} <strong class="text-title">{{ __('Growth') }}</strong>. {{ __('The connections above work on your plan; webhooks and the REST API unlock on Scale.') }}
        </p>

        <ul class="apik-lock__list">
            <li>
                <i class="ph ph-check" aria-hidden="true"></i>
                <span>{{ __('Get a POST to your own endpoint the moment a search finishes') }}</span>
            </li>
            <li>
                <i class="ph ph-check" aria-hidden="true"></i>
                <span>{{ __('Signed payloads, so you can verify the call really came from us') }}</span>
            </li>
            <li>
                <i class="ph ph-check" aria-hidden="true"></i>
                <span>
                    <span class="numeric">20,000</span> {{ __('credits a month, up from') }}
                    <span class="numeric">5,000</span>
                </span>
            </li>
        </ul>

        <div class="apik-lock__act">
            <a href="#" class="btn btn-accent btn-sm">
                <span class="btn__label">
                    <span>{{ __('Compare plans') }}</span>
                    <span aria-hidden="true">{{ __('Compare plans') }}</span>
                </span>
                <i class="ph ph-arrow-right"></i>
            </a>
            <a href="{{ route('user.api.docs') }}" class="btn btn-outline btn-sm">
                <span class="btn__label">
                    <span>{{ __('Read the docs') }}</span>
                    <span aria-hidden="true">{{ __('Read the docs') }}</span>
                </span>
            </a>
        </div>
    </section>

    <section class="panel mt-4 is-hidden" id="webhooksUnlocked">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Webhooks') }}</h3>
            <button type="button" class="btn btn-sm btn-primary shrink-0" data-modal-open="hookModal">
                <span class="btn__label">
                    <span>{{ __('Add endpoint') }}</span>
                    <span aria-hidden="true">{{ __('Add endpoint') }}</span>
                </span>
                <i class="ph ph-plus"></i>
            </button>
        </div>

        <div class="table-scroll">
            <table class="d-table d-table--cards">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Endpoint') }}</th>
                        <th scope="col">{{ __('Events') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Last call') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                    </tr>
                </thead>

                <tbody>
                    <tr data-webhook-row data-test-result="ok">
                        <td data-card-title>
                            <span class="d-table__key integ__url">https://acme.com/hooks/leadatlas</span>
                        </td>
                        <td data-label="{{ __('Events') }}" class="d-table__muted">
                            search.finished, lead.created
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--done" data-webhook-status>
                                <i class="ph ph-check" aria-hidden="true"></i>
                                {{ __('Healthy') }}
                            </span>
                        </td>
                        <td data-label="{{ __('Last call') }}" class="d-table__muted whitespace-nowrap" data-webhook-last>
                            <time datetime="2026-07-21T08:40">08:40</time> · 200
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button type="button" class="btn btn-sm btn-outline" data-webhook-test>
                                    <span class="btn__label">
                                        <span>{{ __('Send test') }}</span>
                                        <span aria-hidden="true">{{ __('Send test') }}</span>
                                    </span>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    aria-label="{{ __('Delete this endpoint') }}"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this endpoint?') }}"
                                    data-confirm-body="{{ __('We stop calling it immediately. Anything downstream that relies on these events stops receiving them.') }}"
                                    data-confirm-label="{{ __('Delete') }}"
                                    data-confirm-variant="error"
                                    data-id="wh-1"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr data-webhook-row data-test-result="fail">
                        <td data-card-title>
                            <span class="d-table__key integ__url">https://acme.com/hooks/scoring</span>
                        </td>
                        <td data-label="{{ __('Events') }}" class="d-table__muted">
                            lead.scored
                        </td>
                        <td data-label="{{ __('Status') }}">
                            <span class="status status--failed" data-webhook-status>
                                <i class="ph ph-warning" aria-hidden="true"></i>
                                {{ __('Failing') }}
                            </span>
                        </td>
                        <td data-label="{{ __('Last call') }}" class="d-table__muted whitespace-nowrap" data-webhook-last>
                            <time datetime="2026-07-21T07:12">07:12</time> · 500
                        </td>
                        <td data-card-actions class="text-right">
                            <div class="row-actions">
                                <button type="button" class="btn btn-sm btn-outline" data-webhook-test>
                                    <span class="btn__label">
                                        <span>{{ __('Send test') }}</span>
                                        <span aria-hidden="true">{{ __('Send test') }}</span>
                                    </span>
                                </button>
                                <button
                                    type="button"
                                    class="row-icon"
                                    aria-label="{{ __('Delete this endpoint') }}"
                                    data-confirm
                                    data-confirm-title="{{ __('Delete this endpoint?') }}"
                                    data-confirm-body="{{ __('We stop calling it immediately. Anything downstream that relies on these events stops receiving them.') }}"
                                    data-confirm-label="{{ __('Delete') }}"
                                    data-confirm-variant="error"
                                    data-id="wh-2"
                                >
                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="sr-only" role="status" aria-live="polite" data-webhook-live></p>

        <p class="integ__note">
            <i class="ph ph-shield-check" aria-hidden="true"></i>
            <span>
                {{ __('Every call is signed. Verify the') }}
                <code class="doc__inline">X-LeadAtlas-Signature</code> {{ __('header before trusting a payload — the') }}
                <a href="{{ route('user.api.docs') }}" class="cnew__link">{{ __('API docs') }}</a>
                {{ __('show how.') }}
            </span>
        </p>
    </section>

    @push('modals')
        <div class="modal" id="connectModal" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="modal__backdrop" data-modal-close></div>
            <div class="modal__panel max-w-md p-6" aria-labelledby="connectModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="connectModalTitle">{{ __('Connect Pipedrive') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('You will be sent to Pipedrive to approve access, then brought back here. We never see your password.') }}
                    </p>

                    <div>
                        <label for="c-what" class="form-label">{{ __('Send them') }}</label>
                        <select id="c-what" name="sync_scope" class="form-input">
                            <option value="new" selected>{{ __('New leads as they arrive') }}</option>
                            <option value="qualified">{{ __('Only leads I mark qualified') }}</option>
                            <option value="score">{{ __('Only leads scoring 70 or above') }}</option>
                        </select>
                        <p class="form-hint">
                            {{ __('You can change this later without reconnecting.') }}
                        </p>
                    </div>

                    <div class="setting-row mt-4">
                        <div class="setting-row__text">
                            <label for="c-existing" class="setting-row__label">
                                {{ __('Send my existing leads too') }}
                            </label>
                            <p class="setting-row__hint">
                                {{ __('Pushes all 1,284 leads once, then keeps up with new ones. Free — you already own them.') }}
                            </p>
                        </div>
                        <input type="checkbox" id="c-existing" name="backfill" class="switch" />
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
                                <span>{{ __('Continue to Pipedrive') }}</span>
                                <span aria-hidden="true">{{ __('Continue to Pipedrive') }}</span>
                            </span>
                            <i class="ph ph-arrow-square-out"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="hookModal" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="modal__backdrop" data-modal-close></div>
            <div class="modal__panel max-w-md p-6" aria-labelledby="hookModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="hookModalTitle">{{ __('Add an endpoint') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('We POST JSON to this URL when the events you pick happen.') }}
                    </p>

                    <div>
                        <label for="h-url" class="form-label">{{ __('Endpoint URL') }}</label>
                        <input type="url" id="h-url" name="webhook_url" class="form-input" placeholder="https://yours.com/hooks/leadatlas" required />
                        <p class="form-hint">{{ __('Must be https.') }}</p>
                    </div>

                    <fieldset class="mt-4">
                        <legend class="form-label">{{ __('Events') }}</legend>
                        <div class="mt-2 grid gap-1">
                            <label class="exp__col">
                                <input type="checkbox" name="events[]" value="search.finished" class="form-check" checked />
                                <span class="exp__col-body">
                                    <span class="exp__col-name">search.finished</span>
                                    <span class="exp__col-note">{{ __('A search finished, with its totals') }}</span>
                                </span>
                            </label>
                            <label class="exp__col">
                                <input type="checkbox" name="events[]" value="lead.created" class="form-check" checked />
                                <span class="exp__col-body">
                                    <span class="exp__col-name">lead.created</span>
                                    <span class="exp__col-note">{{ __('A new business was enriched') }}</span>
                                </span>
                            </label>
                            <label class="exp__col">
                                <input type="checkbox" name="events[]" value="lead.scored" class="form-check" />
                                <span class="exp__col-body">
                                    <span class="exp__col-name">lead.scored</span>
                                    <span class="exp__col-note">{{ __('A lead was scored or re-scored') }}</span>
                                </span>
                            </label>
                            <label class="exp__col">
                                <input type="checkbox" name="events[]" value="lead.status_changed" class="form-check" />
                                <span class="exp__col-body">
                                    <span class="exp__col-name">lead.status_changed</span>
                                    <span class="exp__col-note">{{ __('Someone moved a lead in the pipeline') }}</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            <span class="btn__label">
                                <span>{{ __('Cancel') }}</span>
                                <span aria-hidden="true">{{ __('Cancel') }}</span>
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label">
                                <span>{{ __('Add endpoint') }}</span>
                                <span aria-hidden="true">{{ __('Add endpoint') }}</span>
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

<x-layouts.user :title="__('API keys')">
    @include('panels.user.api.nav', ['apiActive' => 'keys'])

    <div class="mb-6">
        <h2 class="heading-3">{{ __('API keys') }}</h2>
        <p class="m-text mt-1">
            {{ __('Pull your leads straight into your own systems. Keys authenticate every request.') }}
        </p>
    </div>

    <section class="rounded-2xl border border-neutral-200 bg-[#f6f5ff] p-6 sm:p-8">
        <span class="apik-lock__icon" aria-hidden="true">
            <i class="ph ph-lock-key"></i>
        </span>

        <h3 class="font-title text-[1.125rem] font-bold text-title">
            {{ __('The API is on the Scale plan') }}
        </h3>
        <p class="mt-2 max-w-[52ch] text-[0.9375rem] leading-[1.6]">
            {{ __('You are on') }} <strong class="text-title">{{ __('Growth') }}</strong>{{ __(', which includes') }}
            <span class="numeric">5,000</span> {{ __('credits a month and the full app. The REST API and webhooks unlock on Scale.') }}
        </p>

        <ul class="apik-lock__list">
            <li>
                <i class="ph ph-check" aria-hidden="true"></i>
                <span>{{ __('Pull scored leads into your own CRM as soon as a search finishes') }}</span>
            </li>
            <li>
                <i class="ph ph-check" aria-hidden="true"></i>
                <span>{{ __('Webhooks for search-finished and lead-scored events') }}</span>
            </li>
            <li>
                <i class="ph ph-check" aria-hidden="true"></i>
                <span>
                    <span class="numeric">20,000</span> {{ __('credits a month, up from') }}
                    <span class="numeric">5,000</span>
                </span>
            </li>
        </ul>

        <p class="apik-lock__note">
            <i class="ph ph-info" aria-hidden="true"></i>
            <span>
                {{ __('API calls spend credits at the same rate as the app — one per enriched business, returned if no contact is found. The API is a different door, not a different price.') }}
            </span>
        </p>

        <div class="apik-lock__act">
            <a href="#" class="btn btn-accent btn-sm">
                <span class="btn__label">
                    <span>{{ __('Compare plans') }}</span>
                    <span aria-hidden="true">{{ __('Compare plans') }}</span>
                </span>
                <i class="ph ph-arrow-right"></i>
            </a>
            <a href="{{ route('user.credits.index') }}" class="btn btn-outline btn-sm">
                <span class="btn__label">
                    <span>{{ __('Your usage') }}</span>
                    <span aria-hidden="true">{{ __('Your usage') }}</span>
                </span>
            </a>
        </div>
    </section>

    <div id="apiUnlocked" class="is-hidden">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <p class="m-text">
                <span class="numeric">2</span> {{ __('active keys. Treat them like passwords.') }}
            </p>

            <button type="button" class="btn btn-primary btn-sm shrink-0" data-modal-open="keyModal">
                <span class="btn__label">
                    <span>{{ __('New key') }}</span>
                    <span aria-hidden="true">{{ __('New key') }}</span>
                </span>
                <i class="ph ph-plus"></i>
            </button>
        </div>

        <div class="panel">
            <div class="panel__head">
                <h3 class="panel__title">{{ __('Your keys') }}</h3>
                <span class="panel__meta">{{ __('Live and test') }}</span>
            </div>

            <div class="tbl-wrap">
                <table class="d-table d-table--cards">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Key') }}</th>
                            <th scope="col">{{ __('Created') }}</th>
                            <th scope="col">{{ __('Last used') }}</th>
                            <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td data-card-title>
                                <span class="block font-medium text-title">{{ __('Production') }}</span>
                                <p class="d-table__muted text-[0.8125rem]">{{ __('Full access') }}</p>
                            </td>
                            <td data-label="{{ __('Key') }}">
                                <span class="inline-block rounded-lg bg-neutral-100 px-2.5 py-1 font-mono text-[0.8125rem] text-body numeric">
                                    sk_live_7f2a··········4c9e
                                </span>
                            </td>
                            <td data-label="{{ __('Created') }}" class="d-table__muted whitespace-nowrap">
                                <time datetime="2026-05-14">14 {{ __('May') }}</time>
                            </td>
                            <td data-label="{{ __('Last used') }}" class="d-table__muted whitespace-nowrap">
                                <time datetime="2026-07-21">{{ __('2 hours ago') }}</time>
                            </td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button type="button" class="row-icon copy-btn" data-copy="sk_live_7f2a4c9e" aria-label="{{ __('Copy the Production key') }}">
                                        <i class="ph ph-copy copy-btn__idle" aria-hidden="true"></i>
                                        <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="row-icon"
                                        data-confirm
                                        data-confirm-title="{{ __('Revoke the Production key?') }}"
                                        data-confirm-body="{{ __('Any request using it starts failing immediately. This cannot be undone — you would need to create a new key and update whatever uses it.') }}"
                                        data-confirm-label="{{ __('Revoke key') }}"
                                        data-confirm-variant="error"
                                        data-id="1"
                                        aria-label="{{ __('Revoke the Production key') }}"
                                    >
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td data-card-title>
                                <span class="block font-medium text-title">{{ __('Staging') }}</span>
                                <p class="d-table__muted text-[0.8125rem]">{{ __('Read-only') }}</p>
                            </td>
                            <td data-label="{{ __('Key') }}">
                                <span class="inline-block rounded-lg bg-neutral-100 px-2.5 py-1 font-mono text-[0.8125rem] text-body numeric">
                                    sk_test_91b6··········77c1
                                </span>
                            </td>
                            <td data-label="{{ __('Created') }}" class="d-table__muted whitespace-nowrap">
                                <time datetime="2026-06-02">2 {{ __('Jun') }}</time>
                            </td>
                            <td data-label="{{ __('Last used') }}" class="d-table__muted whitespace-nowrap">
                                <time datetime="2026-07-17">17 {{ __('Jul') }}</time>
                            </td>
                            <td data-card-actions class="text-right">
                                <div class="row-actions">
                                    <button type="button" class="row-icon copy-btn" data-copy="sk_test_91b677c1" aria-label="{{ __('Copy the Staging key') }}">
                                        <i class="ph ph-copy copy-btn__idle" aria-hidden="true"></i>
                                        <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="row-icon"
                                        data-confirm
                                        data-confirm-title="{{ __('Revoke the Staging key?') }}"
                                        data-confirm-body="{{ __('Any request using it starts failing immediately. This cannot be undone — you would need to create a new key and update whatever uses it.') }}"
                                        data-confirm-label="{{ __('Revoke key') }}"
                                        data-confirm-variant="error"
                                        data-id="2"
                                        aria-label="{{ __('Revoke the Staging key') }}"
                                    >
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
                    <i class="ph ph-key"></i>
                </span>
                <h2 class="empty__title">{{ __('No keys yet') }}</h2>
                <p class="empty__body">
                    {{ __('Create a key to start pulling leads over the API.') }}
                </p>
                <button type="button" class="btn btn-primary btn-sm" data-modal-open="keyModal">
                    <span class="btn__label">
                        <span>{{ __('New key') }}</span>
                        <span aria-hidden="true">{{ __('New key') }}</span>
                    </span>
                    <i class="ph ph-plus"></i>
                </button>
            </div>
        </div>

        <p class="apik__note">
            <i class="ph ph-info" aria-hidden="true"></i>
            <span>
                {{ __('API calls spend credits at the same rate as the app — one per enriched business, returned if no contact is found. Reading leads you already own is free.') }}
            </span>
        </p>
    </div>

    @push('modals')
        <div class="modal" id="keyModal" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="modal__backdrop" data-modal-close></div>
            <div class="modal__panel max-w-md p-6" aria-labelledby="keyModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="keyModalTitle">{{ __('New API key') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Name it after whatever will use it, so you know what breaks if you revoke it.') }}
                    </p>

                    <div>
                        <label for="k-name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" id="k-name" name="key_name" class="form-input" placeholder="{{ __('Production') }}" required />
                    </div>

                    <div class="mt-4">
                        <label for="k-scope" class="form-label">{{ __('Access') }}</label>
                        <select id="k-scope" name="key_scope" class="form-input">
                            <option value="full" selected>{{ __('Full access') }}</option>
                            <option value="read">{{ __('Read-only') }}</option>
                        </select>
                        <p class="form-hint">
                            {{ __('Read-only keys can fetch leads but cannot start a search, so they cannot spend credits.') }}
                        </p>
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
                                <span>{{ __('Create key') }}</span>
                                <span aria-hidden="true">{{ __('Create key') }}</span>
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

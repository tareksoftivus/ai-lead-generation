<x-layouts.user :title="__('API keys')">
    @include('api-integrations::user.api.nav', ['apiActive' => 'keys'])

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('API keys') }}</h2>
            <p class="m-text mt-1">
                {{ __('Create bearer tokens for your own systems. A key is shown once when it is created.') }}
            </p>
        </div>

        <button type="button" class="btn btn-primary btn-sm shrink-0" data-modal-open="keyModal">
            <span class="btn__label">
                <span>{{ __('New key') }}</span>
                <span aria-hidden="true">{{ __('New key') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </button>
    </div>

    @if (session('new_api_key'))
        <section class="rounded-2xl border border-success/20 bg-success/5 p-5 mb-4">
            <p class="font-title text-[0.9375rem] font-bold text-title">{{ __('Copy your new key') }}</p>
            <p class="m-text mt-1">{{ __('This is the only time the full secret for ":name" will be visible.', ['name' => session('new_api_key_name')]) }}</p>
            <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                <div class="doc__block-head">
                    <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Secret') }}</span>
                    <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#new-api-key">
                        <span class="btn__label">
                            <span>{{ __('Copy') }}</span>
                            <span aria-hidden="true">{{ __('Copy') }}</span>
                        </span>
                        <i class="ph ph-copy copy-btn__idle"></i>
                        <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                    </button>
                </div>
                <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="new-api-key"><code>{{ session('new_api_key') }}</code></pre>
            </div>
        </section>
    @endif

    <div class="panel">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Your keys') }}</h3>
            <span class="panel__meta">
                <span class="numeric">{{ $keys->count() }}</span> {{ __('active') }}
            </span>
        </div>

        @if ($keys->isNotEmpty())
            <div class="tbl-wrap">
                <table class="d-table d-table--cards">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Identifier') }}</th>
                            <th scope="col">{{ __('Created') }}</th>
                            <th scope="col">{{ __('Last used') }}</th>
                            <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($keys as $key)
                            <tr>
                                <td data-card-title>
                                    <span class="block font-medium text-title">{{ $apiKeys->displayName($key) }}</span>
                                    <p class="d-table__muted text-[0.8125rem]">{{ __($apiKeys->scopeLabel($key)) }}</p>
                                </td>
                                <td data-label="{{ __('Identifier') }}">
                                    <span class="inline-block rounded-lg bg-neutral-100 px-2.5 py-1 font-mono text-[0.8125rem] text-body numeric">
                                        {{ $apiKeys->preview($key) }}
                                    </span>
                                </td>
                                <td data-label="{{ __('Created') }}" class="d-table__muted whitespace-nowrap">
                                    <time datetime="{{ $key->created_at?->toDateString() }}">{{ $key->created_at?->format('d M Y') }}</time>
                                </td>
                                <td data-label="{{ __('Last used') }}" class="d-table__muted whitespace-nowrap">
                                    @if ($key->last_used_at)
                                        <time datetime="{{ $key->last_used_at->toDateString() }}">{{ $key->last_used_at->diffForHumans() }}</time>
                                    @else
                                        {{ __('Never') }}
                                    @endif
                                </td>
                                <td data-card-actions class="text-right">
                                    <form action="{{ route('user.api.keys.destroy', $key) }}" method="post" onsubmit="return confirm('{{ __('Revoke this API key? Requests using it will fail immediately.') }}')">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="row-icon" aria-label="{{ __('Revoke key') }}">
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <span class="empty__icon" aria-hidden="true">
                    <i class="ph ph-key"></i>
                </span>
                <h2 class="empty__title">{{ __('No keys yet') }}</h2>
                <p class="empty__body">{{ __('Create a key to start pulling leads over the API.') }}</p>
            </div>
        @endif
    </div>

    <p class="apik__note">
        <i class="ph ph-info" aria-hidden="true"></i>
        <span>{{ __('Read-only keys can fetch leads. Full-access keys are reserved for write endpoints as they are added.') }}</span>
    </p>

    @push('modals')
        <div class="modal" id="keyModal" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="modal__backdrop" data-modal-close></div>
            <div class="modal__panel max-w-md p-6" aria-labelledby="keyModalTitle">
                <form action="{{ route('user.api.keys.store') }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="keyModalTitle">{{ __('New API key') }}</h2>
                    <p class="m-text mt-2 mb-5">{{ __('Name it after the system that will use it.') }}</p>

                    <div>
                        <label for="k-name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" id="k-name" name="key_name" class="form-input" value="{{ old('key_name') }}" placeholder="{{ __('Production') }}" required />
                    </div>

                    <div class="mt-4">
                        <label for="k-scope" class="form-label">{{ __('Access') }}</label>
                        <select id="k-scope" name="key_scope" class="form-input">
                            <option value="full" @selected(old('key_scope', 'full') === 'full')>{{ __('Full access') }}</option>
                            <option value="read" @selected(old('key_scope') === 'read')>{{ __('Read-only') }}</option>
                        </select>
                        <p class="form-hint">{{ __('Read-only keys can fetch leads but cannot use write endpoints.') }}</p>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>{{ __('Cancel') }}</button>
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
    @endpush
</x-layouts.user>

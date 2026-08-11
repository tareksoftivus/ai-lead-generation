<x-layouts.user :title="__('Integrations')">
    @include('api-integrations::user.api.nav', ['apiActive' => 'integrations'])

    <div class="mb-4">
        <h2 class="heading-3">{{ __('Integrations') }}</h2>
        <p class="m-text mt-1">{{ __('Connect lead destinations and control their sync settings.') }}</p>
    </div>

    <section class="panel">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Connections') }}</h3>
            <span class="panel__meta">
                <span class="numeric">{{ $connected->count() }}</span> {{ __('configured') }}
            </span>
        </div>

        <div class="panel__body">
            @if ($connected->isNotEmpty())
                <p class="mt-5 mb-2 text-[0.75rem] font-semibold tracking-wider text-body uppercase">{{ __('Configured') }}</p>
                <div class="integs">
                    @foreach ($connected as $provider)
                        @php($connection = $provider->connections->first())
                        <article class="integ is-connected">
                            <div class="integ__top">
                                <span class="integ__logo {{ $provider->logo_class }}">
                                    <i class="{{ $provider->icon }}" aria-hidden="true"></i>
                                </span>
                                @if ($provider->is_active)
                                    <span class="status status--done">
                                        <i class="ph ph-check" aria-hidden="true"></i>{{ __('Configured') }}
                                    </span>
                                @else
                                    <span class="status status--failed">
                                        <i class="ph ph-warning" aria-hidden="true"></i>{{ __('Unavailable') }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="font-title text-[1rem] font-bold text-title">{{ $provider->name }}</h4>
                            <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">{{ $provider->description }}</p>

                            <dl class="integ__facts">
                                <div class="integ__fact">
                                    <dt>{{ __('Account') }}</dt>
                                    <dd>{{ $connection->account_name ?: __('Not named') }}</dd>
                                </div>
                                <div class="integ__fact">
                                    <dt>{{ __('Minimum score') }}</dt>
                                    <dd><span class="numeric">{{ $connection->settings['minimum_score'] ?? 0 }}</span></dd>
                                </div>
                                <div class="integ__fact">
                                    <dt>{{ __('Synced') }}</dt>
                                    <dd><span class="numeric">{{ number_format($connection->synced_leads_count) }}</span> {{ __('leads') }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4 space-y-3">
                                <form action="{{ route('user.api.integrations.update', $connection) }}" method="post" class="space-y-3">
                                    @csrf
                                    @method('put')
                                    <input type="text" name="account_name" class="form-input" value="{{ old('account_name', $connection->account_name) }}" placeholder="{{ __('Account, sheet, channel, or label') }}" />
                                    <input type="number" name="minimum_score" class="form-input" value="{{ old('minimum_score', $connection->settings['minimum_score'] ?? 0) }}" min="0" max="100" />
                                    <label class="flex items-center gap-2 text-[0.8125rem] text-body">
                                        <input type="hidden" name="sync_new_leads" value="0" />
                                        <input type="checkbox" name="sync_new_leads" value="1" @checked($connection->settings['sync_new_leads'] ?? true) @disabled(! $provider->is_active) />
                                        {{ __('Sync new leads automatically') }}
                                    </label>
                                    <button type="submit" class="btn btn-sm btn-outline" @disabled(! $provider->is_active)>
                                        <span class="btn__label"><span>{{ __('Save') }}</span><span aria-hidden="true">{{ __('Save') }}</span></span>
                                    </button>
                                </form>
                                <form action="{{ route('user.api.integrations.destroy', $connection) }}" method="post" onsubmit="return confirm('{{ __('Remove this integration configuration?') }}')">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-sm btn-ghost text-body hover:bg-error/10 hover:text-error">
                                            <span class="btn__label"><span>{{ __('Remove') }}</span><span aria-hidden="true">{{ __('Remove') }}</span></span>
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <p class="mt-5 mb-2 text-[0.75rem] font-semibold tracking-wider text-body uppercase">{{ __('Available') }}</p>
            <div class="integs">
                @forelse ($available as $provider)
                    <article class="integ">
                        <div class="integ__top">
                            <span class="integ__logo {{ $provider->logo_class }}">
                                <i class="{{ $provider->icon }}" aria-hidden="true"></i>
                            </span>
                        </div>

                        <h4 class="font-title text-[1rem] font-bold text-title">{{ $provider->name }}</h4>
                        <p class="mt-1 text-[0.8125rem] leading-[1.55] text-body">{{ $provider->description }}</p>

                        <form action="{{ route('user.api.integrations.store', $provider) }}" method="post" class="mt-4 space-y-3">
                            @csrf
                            <input type="text" name="account_name" class="form-input" placeholder="{{ $provider->config_schema['account_name'] ?? __('Account name') }}" />
                            <input type="number" name="minimum_score" class="form-input" value="0" min="0" max="100" />
                            <label class="flex items-center gap-2 text-[0.8125rem] text-body">
                                <input type="hidden" name="sync_new_leads" value="0" />
                                <input type="checkbox" name="sync_new_leads" value="1" checked />
                                {{ __('Sync new leads automatically') }}
                            </label>
                            <div class="integ__act">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span class="btn__label"><span>{{ __('Set up') }}</span><span aria-hidden="true">{{ __('Set up') }}</span></span>
                                    <i class="ph ph-plug"></i>
                                </button>
                            </div>
                        </form>
                    </article>
                @empty
                    <div class="empty">
                        <span class="empty__icon" aria-hidden="true"><i class="ph ph-plugs-connected"></i></span>
                        <h2 class="empty__title">{{ __('All available integrations are connected') }}</h2>
                    </div>
                @endforelse
            </div>

            <p class="integ__note">
                <i class="ph ph-coins" aria-hidden="true"></i>
                <span>{{ __('Saving integration settings is free. Provider delivery starts when a configured integration driver is enabled for that provider.') }}</span>
            </p>
        </div>
    </section>
</x-layouts.user>

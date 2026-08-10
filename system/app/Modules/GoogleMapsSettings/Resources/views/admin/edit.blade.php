<x-layouts.admin :title="__('Google Maps API Settings')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Google Maps API') }}</h1>
                <p class="s-body mt-1 text-neutral-500">
                    {{ __('The server-side API key used to search Google Places and enrich business contact details for every user on this site.') }}
                </p>
            </div>

            <x-ui.button variant="outline" href="{{ route('admin.google-maps-settings.logs') }}">
                <i class="ph ph-list-magnifying-glass"></i> {{ __('View logs') }}
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2">
                <div class="section-card">
                    <div class="mb-4 flex items-center gap-3 border-b border-neutral-100 pb-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <i class="ph ph-map-pin text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-neutral-900">{{ __('Credentials & limits') }}</h3>
                            <p class="text-xs text-neutral-500">{{ __('Applies to Places search and lead enrichment across the whole site.') }}</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.google-maps-settings.update') }}" method="post" class="space-y-5">
                        @csrf
                        @method('put')

                        <x-forms.input
                            :label="__('Google Maps API key')"
                            name="google_maps_api_key"
                            type="password"
                            icon="ph ph-key"
                            :value="$settings['google_maps_api_key']"
                            :hint="__('A server key with the Places API (New) enabled. Leave blank to keep the current key.')"
                        />

                        <x-forms.input
                            :label="__('Daily search cap')"
                            name="google_maps_daily_search_cap"
                            type="number"
                            min="1"
                            icon="ph ph-gauge"
                            :value="$settings['google_maps_daily_search_cap']"
                            :hint="__('Optional. Leave blank for no limit on searches per day.')"
                        />

                        <div class="setting-row">
                            <div class="setting-label">
                                <span class="label">{{ __('Enable search & enrichment') }}</span>
                                <span class="hint">{{ __('Turning this off blocks new searches and lead enrichment site-wide.') }}</span>
                            </div>
                            <x-forms.toggle
                                name="google_maps_enrichment_enabled"
                                :checked="$settings['google_maps_enrichment_enabled']"
                            />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-forms.submit :label="__('Save Changes')" />
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="section-card">
                    <h3 class="mb-4 text-sm font-semibold text-neutral-900">{{ __('Status') }}</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-500">{{ __('API key') }}</span>
                            @if ($settings['google_maps_api_key'])
                                <x-ui.badge variant="success"><i class="ph ph-check-circle"></i> {{ __('Configured') }}</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger"><i class="ph ph-warning-circle"></i> {{ __('Missing') }}</x-ui.badge>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-500">{{ __('Search & enrichment') }}</span>
                            @if ($settings['google_maps_enrichment_enabled'])
                                <x-ui.badge variant="success">{{ __('Enabled') }}</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">{{ __('Disabled') }}</x-ui.badge>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-500">{{ __('Daily search cap') }}</span>
                            <span class="text-sm font-medium text-neutral-900">
                                {{ $settings['google_maps_daily_search_cap'] ?? __('Unlimited') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="section-card border-info/20 bg-info/5">
                    <div class="flex gap-3">
                        <i class="ph ph-info text-lg text-info"></i>
                        <p class="text-xs text-neutral-600">
                            {{ __('Requests made with this key are recorded in the API logs (without exposing the key itself) so you can monitor usage, errors, and response times.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

<x-layouts.admin :title="__('Google Maps API Settings')">
    <div class="mb-6">
        <h2 class="heading-3">{{ __('Google Maps API') }}</h2>
        <p class="m-text mt-1">
            {{ __('The server-side API key used to search Google Places and enrich business contact details for every user on this site.') }}
        </p>
    </div>

    <div class="panel max-w-2xl p-6">
        <form action="{{ route('admin.google-maps-settings.update') }}" method="post">
            @csrf
            @method('put')

            <x-forms.input
                :label="__('Google Maps API key')"
                name="google_maps_api_key"
                type="password"
                :value="$settings['google_maps_api_key']"
                :hint="__('A server key with the Places API (New) enabled. Leave blank to keep the current key.')"
            />

            <div class="mt-4">
                <x-forms.input
                    :label="__('Daily search cap')"
                    name="google_maps_daily_search_cap"
                    type="number"
                    min="1"
                    :value="$settings['google_maps_daily_search_cap']"
                    :hint="__('Optional. Leave blank for no limit on searches per day.')"
                />
            </div>

            <div class="mt-4">
                <x-forms.toggle
                    :label="__('Enable search & enrichment')"
                    name="google_maps_enrichment_enabled"
                    :checked="$settings['google_maps_enrichment_enabled']"
                />
                <p class="form-hint">{{ __('Turning this off blocks new searches and lead enrichment site-wide.') }}</p>
            </div>

            <div class="mt-6">
                <x-forms.submit :label="__('Save Changes')" />
            </div>
        </form>
    </div>
</x-layouts.admin>

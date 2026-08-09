<?php

use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;

if (! function_exists('google_maps_setting')) {
    /**
     * Get a Google Maps API setting value.
     *
     * Usage: google_maps_setting('google_maps_api_key')
     *        google_maps_setting('google_maps_enrichment_enabled', true)
     */
    function google_maps_setting(string $key, mixed $default = null): mixed
    {
        return app(GoogleMapsSettingsService::class)->get($key, $default);
    }
}

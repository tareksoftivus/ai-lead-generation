<?php

namespace App\Modules\GoogleMapsSettings\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class GoogleMapsSettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:google-maps-settings.manage'),
        ];
    }

    public function __construct(
        protected GoogleMapsSettingsService $service
    ) {}

    public function edit(): View
    {
        return view('google-maps-settings::admin.edit', [
            'settings' => $this->service->all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'google_maps_api_key' => ['nullable', 'string', 'max:255'],
            'google_maps_enrichment_enabled' => ['nullable', 'boolean'],
            'google_maps_daily_search_cap' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->service->set('google_maps_api_key', $validated['google_maps_api_key'] ?? null);
        $this->service->set('google_maps_enrichment_enabled', $request->boolean('google_maps_enrichment_enabled'));
        $this->service->set('google_maps_daily_search_cap', $validated['google_maps_daily_search_cap'] ?? null);

        return redirect()
            ->route('admin.google-maps-settings.index')
            ->with('success', __('Google Maps API settings updated successfully.'));
    }
}

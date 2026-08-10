<?php

namespace App\Modules\GoogleMapsSettings\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\GoogleMapsSettings\Models\GoogleMapsApiLog;
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

    public function logs(Request $request): View
    {
        $logs = GoogleMapsApiLog::query()
            ->when($request->filled('status'), function ($query) use ($request) {
                $request->input('status') === 'successful'
                    ? $query->where('successful', true)
                    : $query->where('successful', false);
            })
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->input('action')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('google-maps-settings::admin.logs', [
            'logs' => $logs,
            'actions' => GoogleMapsApiLog::query()->distinct()->orderBy('action')->pluck('action'),
            'stats' => $this->logStats(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function logStats(): array
    {
        $today = GoogleMapsApiLog::query()->whereDate('created_at', today());
        $todayTotal = $today->count();
        $todayFailed = (clone $today)->where('successful', false)->count();

        return [
            'total' => GoogleMapsApiLog::query()->count(),
            'today' => $todayTotal,
            'success_rate' => $todayTotal > 0
                ? round((($todayTotal - $todayFailed) / $todayTotal) * 100)
                : 100,
            'avg_duration' => (int) GoogleMapsApiLog::query()->whereDate('created_at', today())->avg('duration_ms'),
        ];
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

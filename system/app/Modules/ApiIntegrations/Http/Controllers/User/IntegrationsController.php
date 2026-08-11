<?php

namespace App\Modules\ApiIntegrations\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\ApiIntegrations\Http\Requests\StoreIntegrationConnectionRequest;
use App\Modules\ApiIntegrations\Models\ApiIntegrationProvider;
use App\Modules\ApiIntegrations\Models\UserIntegrationConnection;
use App\Modules\ApiIntegrations\Services\IntegrationProviderCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IntegrationsController extends Controller
{
    public function index(Request $request, IntegrationProviderCatalog $catalog): View
    {
        $providers = $catalog->providersForUser($request->user());
        $connected = $providers->filter(fn (ApiIntegrationProvider $provider) => $provider->connections->isNotEmpty());

        return view('api-integrations::user.api.integrations', [
            'providers' => $providers,
            'connected' => $connected,
            'available' => $providers->diff($connected),
        ]);
    }

    public function store(StoreIntegrationConnectionRequest $request, ApiIntegrationProvider $provider, IntegrationProviderCatalog $catalog): RedirectResponse
    {
        abort_unless($provider->is_active, 404);

        $catalog->connect($request->user(), $provider, $request->validated());

        return redirect()
            ->route('user.api.integrations')
            ->with('success', __(':name sync settings saved.', ['name' => $provider->name]));
    }

    public function update(StoreIntegrationConnectionRequest $request, UserIntegrationConnection $connection, IntegrationProviderCatalog $catalog): RedirectResponse
    {
        abort_unless((int) $connection->user_id === (int) $request->user()->id, 404);
        abort_unless($connection->provider?->is_active, 404);

        $catalog->connect($request->user(), $connection->provider, $request->validated());

        return redirect()
            ->route('user.api.integrations')
            ->with('success', __('Integration sync settings saved.'));
    }

    public function destroy(Request $request, UserIntegrationConnection $connection): RedirectResponse
    {
        abort_unless((int) $connection->user_id === (int) $request->user()->id, 404);

        $name = $connection->provider?->name ?? __('Integration');
        $connection->delete();

        return redirect()
            ->route('user.api.integrations')
            ->with('success', __(':name disconnected.', ['name' => $name]));
    }
}

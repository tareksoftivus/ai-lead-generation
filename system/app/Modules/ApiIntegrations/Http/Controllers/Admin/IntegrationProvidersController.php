<?php

namespace App\Modules\ApiIntegrations\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\ApiIntegrations\Http\Requests\UpdateIntegrationProviderRequest;
use App\Modules\ApiIntegrations\Models\ApiIntegrationProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class IntegrationProvidersController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:api-integrations.view', only: ['index']),
            new Middleware('permission:api-integrations.edit', except: ['index']),
        ];
    }

    public function index(): View
    {
        return view('api-integrations::admin.index', [
            'providers' => ApiIntegrationProvider::query()
                ->withCount('connections')
                ->ordered()
                ->get(),
        ]);
    }

    public function update(UpdateIntegrationProviderRequest $request, ApiIntegrationProvider $provider): RedirectResponse
    {
        $provider->update([
            ...$request->safe()->only(['name', 'description', 'category', 'sort_order', 'docs_url']),
            'is_active' => $request->boolean('is_active'),
            'requires_configuration' => $request->boolean('requires_configuration'),
        ]);

        return redirect()
            ->route('admin.api-integrations.index')
            ->with('success', __('Integration provider updated.'));
    }
}

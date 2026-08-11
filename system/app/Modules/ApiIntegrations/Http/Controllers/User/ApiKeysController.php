<?php

namespace App\Modules\ApiIntegrations\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\ApiIntegrations\Http\Requests\StoreApiKeyRequest;
use App\Modules\ApiIntegrations\Services\ApiKeyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class ApiKeysController extends Controller
{
    public function __construct(
        protected ApiKeyService $apiKeys
    ) {}

    public function index(Request $request): View
    {
        return view('api-integrations::user.api.keys', [
            'keys' => $this->apiKeys->keysFor($request->user()),
            'apiKeys' => $this->apiKeys,
        ]);
    }

    public function store(StoreApiKeyRequest $request): RedirectResponse
    {
        $token = $this->apiKeys->create(
            $request->user(),
            $request->validated('key_name'),
            $request->validated('key_scope')
        );

        return redirect()
            ->route('user.api.keys')
            ->with('success', __('API key created. Copy it now; it will only be shown once.'))
            ->with('new_api_key', $token->plainTextToken)
            ->with('new_api_key_name', $request->validated('key_name'));
    }

    public function destroy(Request $request, PersonalAccessToken $token): RedirectResponse
    {
        abort_unless($token->tokenable_type === $request->user()::class && (int) $token->tokenable_id === (int) $request->user()->id, 404);
        abort_unless($this->apiKeys->isManagedToken($token), 404);

        $token->delete();

        return redirect()
            ->route('user.api.keys')
            ->with('success', __('API key revoked.'));
    }
}

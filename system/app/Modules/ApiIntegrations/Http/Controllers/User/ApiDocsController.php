<?php

namespace App\Modules\ApiIntegrations\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\ApiIntegrations\Services\ApiDocumentationService;
use App\Modules\ApiIntegrations\Services\ApiKeyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiDocsController extends Controller
{
    public function __invoke(Request $request, ApiDocumentationService $docs, ApiKeyService $apiKeys): View
    {
        $firstKey = $apiKeys->keysFor($request->user())->first();

        return view('api-integrations::user.api.docs', [
            'baseUrl' => $docs->baseUrl(),
            'rateLimit' => $docs->rateLimit(),
            'leadQueryParameters' => $docs->leadQueryParameters(),
            'errors' => $docs->errors(),
            'sampleToken' => $firstKey
                ? $apiKeys->preview($firstKey)
                : 'YOUR_API_KEY',
        ]);
    }
}

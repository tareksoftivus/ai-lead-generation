<?php

namespace App\Modules\ApiIntegrations\Providers;

use App\Modules\ApiIntegrations\Services\ApiDocumentationService;
use App\Modules\ApiIntegrations\Services\ApiKeyService;
use App\Modules\ApiIntegrations\Services\IntegrationProviderCatalog;
use App\Modules\Shared\Support\BasePanelModuleProvider;

class ApiIntegrationsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(ApiKeyService::class);
        $this->app->singleton(ApiDocumentationService::class);
        $this->app->singleton(IntegrationProviderCatalog::class);
    }
}

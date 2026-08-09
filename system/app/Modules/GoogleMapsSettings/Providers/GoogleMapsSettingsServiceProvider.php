<?php

namespace App\Modules\GoogleMapsSettings\Providers;

use App\Modules\GoogleMapsSettings\Services\GoogleMapsSettingsService;
use App\Modules\Shared\Support\BasePanelModuleProvider;

class GoogleMapsSettingsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(GoogleMapsSettingsService::class);
    }
}

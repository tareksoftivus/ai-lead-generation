<?php

namespace App\Modules\Analysis\Providers;

use App\Modules\Analysis\Services\BusinessAnalysisService;
use App\Modules\Shared\Support\BasePanelModuleProvider;

class AnalysisServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(BusinessAnalysisService::class);
    }
}

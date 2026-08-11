<?php

namespace App\Modules\AiTools\Providers;

use App\Modules\AiTools\Models\BusinessAnalysisRun;
use App\Modules\AiTools\Models\EmailDraft;
use App\Modules\AiTools\Services\AiToolsCreditCost;
use App\Modules\AiTools\Services\BusinessAnalysisService;
use App\Modules\AiTools\Services\EmailGeneratorService;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AiToolsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(AiToolsCreditCost::class);
        $this->app->singleton(BusinessAnalysisService::class);
        $this->app->singleton(EmailGeneratorService::class);
    }

    protected function bootModule(array $module): void
    {
        Relation::morphMap([
            'business_analysis_run' => BusinessAnalysisRun::class,
            'email_generator_draft' => EmailDraft::class,
            'App\\Modules\\Analysis\\Models\\BusinessAnalysisRun' => BusinessAnalysisRun::class,
        ], merge: true);
    }
}

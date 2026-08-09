<?php

namespace App\Modules\Leads\Providers;

use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Contracts\OutreachDraftGenerator;
use App\Modules\Leads\Services\GooglePlaces\GooglePlacesClient;
use App\Modules\Leads\Services\HeuristicLeadScorer;
use App\Modules\Leads\Services\TemplateOutreachDraftGenerator;
use App\Modules\Shared\Support\BasePanelModuleProvider;

class LeadsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(GooglePlacesClient::class);

        // Bound behind their contracts so a future AI module can rebind a real
        // implementation without touching any other Leads module code.
        $this->app->bind(LeadScorer::class, HeuristicLeadScorer::class);
        $this->app->bind(OutreachDraftGenerator::class, TemplateOutreachDraftGenerator::class);
    }
}

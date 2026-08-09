<?php

namespace App\Modules\Credits\Providers;

use App\Modules\Credits\Listeners\GrantPricingPlanCredits;
use App\Modules\Credits\Listeners\GrantStarterCredits;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class CreditsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(CreditLedger::class);
    }

    protected function bootModule(array $module): void
    {
        Event::listen(Login::class, GrantStarterCredits::class);
        Event::listen(PaymentSucceeded::class, GrantPricingPlanCredits::class);
    }
}

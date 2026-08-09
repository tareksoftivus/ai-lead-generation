<?php

use App\Models\Admin;
use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PricingPlan\Models\PricingPlan;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('loads the module descriptor', function (): void {
    expect(app(ModuleRegistry::class)->find('pricing-plans'))->not->toBeNull();
});

it('can access edit pricing plan page', function (): void {
    Permission::findOrCreate('pricing-plans.view', 'admin');
    Permission::findOrCreate('pricing-plans.edit', 'admin');

    $admin = Admin::factory()->create([
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $admin->givePermissionTo(['pricing-plans.view', 'pricing-plans.edit']);

    $pricingPlan = PricingPlan::create([
        'name' => 'Pro Plan',
        'slug' => 'pro-plan',
        'tagline' => 'Pro Plan Description',
        'price_monthly' => 1900,
        'price_yearly' => 19000,
        'credits_monthly' => 5000,
    ]);

    $this
        ->actingAs($admin, 'admin')
        ->get(route('admin.pricing-plans.edit', $pricingPlan))
        ->assertOk()
        ->assertViewHas('pricingPlan')
        ->assertSee('Pro Plan');
});

it('shows subscribers with paid/free stats and lets admins search by user', function (): void {
    Permission::findOrCreate('pricing-plans.view', 'admin');

    $admin = Admin::factory()->create([
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $admin->givePermissionTo(['pricing-plans.view']);

    $pricingPlan = PricingPlan::create([
        'name' => 'Pro Plan',
        'slug' => 'pro-plan',
        'tagline' => 'Pro Plan Description',
        'price_monthly' => 1900,
        'price_yearly' => 19000,
        'credits_monthly' => 5000,
    ]);

    $freePlan = PricingPlan::create([
        'name' => 'Free Plan',
        'slug' => 'free-plan',
        'price_monthly' => 0,
        'price_yearly' => 0,
        'credits_monthly' => 100,
    ]);

    $paidUser = User::factory()->create();
    $freeUser = User::factory()->create();

    $payment = Payment::create([
        'uuid' => (string) Str::uuid(),
        'user_type' => $paidUser->getMorphClass(),
        'user_id' => $paidUser->id,
        'gateway' => 'stripe',
        'amount' => 19.00,
        'currency' => 'USD',
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    CreditTransaction::create([
        'user_id' => $paidUser->id,
        'type' => 'grant',
        'amount' => 5000,
        'balance_after' => 5000,
        'reason' => 'pricing_plan_purchase',
        'reference_type' => $payment->getMorphClass(),
        'reference_id' => $payment->id,
        'metadata' => [
            'pricing_plan_id' => $pricingPlan->id,
            'pricing_plan_name' => $pricingPlan->name,
            'pricing_plan_slug' => $pricingPlan->slug,
        ],
    ]);

    CreditTransaction::create([
        'user_id' => $freeUser->id,
        'type' => 'grant',
        'amount' => 100,
        'balance_after' => 100,
        'reason' => 'pricing_plan_purchase',
        'metadata' => [
            'pricing_plan_id' => $freePlan->id,
            'pricing_plan_name' => $freePlan->name,
            'pricing_plan_slug' => $freePlan->slug,
        ],
    ]);

    $this
        ->actingAs($admin, 'admin')
        ->get(route('admin.pricing-plan-subscribers.index'))
        ->assertOk()
        ->assertViewHas('stats', function (array $stats): bool {
            return $stats['total'] === 2
                && $stats['paid'] === 1
                && $stats['free'] === 1
                && $stats['credits_granted'] === 5100;
        })
        ->assertSee('Pro Plan')
        ->assertSee('Free Plan');

    $searched = $this
        ->actingAs($admin, 'admin')
        ->get(route('admin.pricing-plan-subscribers.index', ['search' => $paidUser->name]))
        ->assertOk()
        ->assertViewHas('stats', function (array $stats): bool {
            return $stats['total'] === 1 && $stats['paid'] === 1 && $stats['free'] === 0;
        });
});

<?php

use App\Models\Admin;
use App\Modules\PricingPlan\Models\PricingPlan;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

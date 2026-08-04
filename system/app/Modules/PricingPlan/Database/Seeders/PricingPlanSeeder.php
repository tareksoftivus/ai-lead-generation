<?php

namespace App\Modules\PricingPlan\Database\Seeders;

use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        PricingPlan::query()->updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'tagline' => 'For solo sellers running their first searches.',
                'icon' => 'ph-map-trifold',
                'price_monthly' => 29,
                'price_yearly' => 290,
                'credits_monthly' => 1000,
                'features' => [
                    'Unlimited map searches',
                    'Contact enrichment',
                    'AI scoring on every lead',
                    'CSV export',
                ],
                'cta_label' => 'Start free',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ]
        );

        PricingPlan::query()->updateOrCreate(
            ['slug' => 'growth'],
            [
                'name' => 'Growth',
                'tagline' => 'For teams ready to turn scored leads into outreach.',
                'icon' => 'ph-sparkle',
                'price_monthly' => 89,
                'price_yearly' => 890,
                'credits_monthly' => 5000,
                'features' => [
                    'Everything in Starter',
                    'AI summaries and drafted emails',
                    'Built-in CRM pipeline',
                    'Email campaigns',
                ],
                'cta_label' => 'Start free',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        PricingPlan::query()->updateOrCreate(
            ['slug' => 'scale'],
            [
                'name' => 'Scale',
                'tagline' => 'For agencies and teams running searches at volume.',
                'icon' => 'ph-kanban',
                'price_monthly' => 249,
                'price_yearly' => 2490,
                'credits_monthly' => 20000,
                'features' => [
                    'Everything in Growth',
                    'REST API and webhooks',
                    'Bulk export and integrations',
                    'Priority support',
                ],
                'cta_label' => 'Start free',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ]
        );
    }
}

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
                'tagline' => 'For solo sellers generating local leads from Google Maps.',
                'icon' => 'ph-map-trifold',
                'price_monthly' => 29,
                'price_yearly' => 290,
                'credits_monthly' => 1000,
                'features' => [
                    '1,000 lead generation credits per month',
                    'Google Maps lead generation',
                    'Leads bank reuse before API calls',
                    'Search history and saved results',
                    'All leads and map view',
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
                'tagline' => 'For teams managing generated leads, lists, tags, and scoring.',
                'icon' => 'ph-sparkle',
                'price_monthly' => 89,
                'price_yearly' => 890,
                'credits_monthly' => 5000,
                'features' => [
                    'Everything in Starter',
                    '5,000 lead generation credits per month',
                    'Lead lists, tags, notes, and statuses',
                    'Dynamic lead scoring and re-scoring',
                    'Contact enrichment and email discovery',
                    'Bulk actions for saved leads',
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
                'tagline' => 'For agencies running high-volume local lead generation.',
                'icon' => 'ph-kanban',
                'price_monthly' => 249,
                'price_yearly' => 2490,
                'credits_monthly' => 20000,
                'features' => [
                    'Everything in Growth',
                    '20,000 lead generation credits per month',
                    'Higher-volume Google Maps discovery',
                    'Search-to-list workflows for campaigns',
                    'CSV export and API-ready lead data',
                    'Priority support and implementation help',
                ],
                'cta_label' => 'Start free',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ]
        );
    }
}

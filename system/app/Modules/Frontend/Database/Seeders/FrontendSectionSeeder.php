<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Services\FrontendSectionService;
use Illuminate\Database\Seeder;

class FrontendSectionSeeder extends Seeder
{
    public function run(): void
    {
        /** @var FrontendSectionService $service */
        $service = app(FrontendSectionService::class);

        $sections = [
            [
                'name' => 'Homepage Hero',
                'slug' => 'homepage-hero',
                'type' => 'hero',
                'status' => 'published',
                'description' => 'Primary hero section for the homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Features',
                'slug' => 'homepage-features',
                'type' => 'feature_grid',
                'status' => 'published',
                'description' => 'Feature highlights for the homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Testimonials',
                'slug' => 'homepage-testimonials',
                'type' => 'testimonial_grid',
                'status' => 'published',
                'description' => 'Social proof section for the homepage.',
                'data' => [],
            ],
            [
                'name' => 'Global Footer',
                'slug' => 'global-footer',
                'type' => 'footer',
                'status' => 'published',
                'description' => 'Reusable footer content.',
                'data' => [],
            ],
            [
                'name' => 'About Content',
                'slug' => 'about-rich-content',
                'type' => 'rich_content',
                'status' => 'published',
                'description' => 'Long-form content for the about page.',
                'data' => [
                    'title' => 'About this frontend stack',
                    'content' => '<p>This shared-content frontend stack is designed so you can add more themes later without redoing your content model.</p>',
                ],
            ],
            [
                'name' => 'Homepage Hero',
                'slug' => 'homepage-hero-leadatlas',
                'type' => 'homepage_hero',
                'status' => 'published',
                'description' => 'Map-driven hero for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Stages',
                'slug' => 'homepage-stages',
                'type' => 'homepage_stages',
                'status' => 'published',
                'description' => 'The funnel/run section for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Features',
                'slug' => 'homepage-features-leadatlas',
                'type' => 'homepage_features',
                'status' => 'published',
                'description' => 'Feature grid for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Voices',
                'slug' => 'homepage-voices',
                'type' => 'homepage_voices',
                'status' => 'published',
                'description' => 'Testimonial carousel for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Pricing',
                'slug' => 'homepage-pricing',
                'type' => 'homepage_pricing',
                'status' => 'published',
                'description' => 'Pricing plans for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage FAQ',
                'slug' => 'homepage-faq',
                'type' => 'homepage_faq',
                'status' => 'published',
                'description' => 'FAQ accordion for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Blog',
                'slug' => 'homepage-blog',
                'type' => 'homepage_blog',
                'status' => 'published',
                'description' => 'Featured blog posts for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage CTA',
                'slug' => 'homepage-cta',
                'type' => 'homepage_cta',
                'status' => 'published',
                'description' => 'Closing call-to-action with search form for the LeadAtlas homepage.',
                'data' => [],
            ],
            [
                'name' => 'Features Page Hero',
                'slug' => 'features-page-head',
                'type' => 'featurespage_hero',
                'status' => 'published',
                'description' => 'Breadcrumb header for the Features page.',
                'data' => [],
            ],
            [
                'name' => 'Features — Discover Stage',
                'slug' => 'features-stage-discover',
                'type' => 'featurespage_discover',
                'status' => 'published',
                'description' => 'Stage one: discovery features for the Features page.',
                'data' => [
                    'key_icon' => 'ph-fill ph-map-pin',
                    'key_label' => 'Stage one · Discover',
                    'title' => 'Everything Google Maps knows.',
                    'subtitle' => 'You give a keyword and a place. We pull every matching business in that radius, with the details the listing carries.',
                ],
            ],
            [
                'name' => 'Features — AI Layer Stage',
                'slug' => 'features-stage-ai',
                'type' => 'featurespage_layer',
                'status' => 'published',
                'description' => 'Stage two: the AI scoring layer, with a scoring showcase panel.',
                'data' => [
                    'key_icon' => 'ph-fill ph-sparkle',
                    'key_label' => 'Stage two · The AI layer',
                    'title' => 'How the scoring actually works.',
                    'subtitle' => 'A score is not a guess about whether a business is good. It is a judgement about whether your service is worth something to them, built from signals anyone could check by hand — if they had the time.',
                    'items' => [
                        ['icon' => 'ph-article', 'title' => 'Written summaries', 'description' => 'A paragraph on what each business is missing, so you know your angle before you pick up the phone.'],
                        ['icon' => 'ph-pen-nib', 'title' => 'Drafted opening emails', 'description' => 'A first message built around that specific gap. Editable, and never sent without you.'],
                        ['icon' => 'ph-sliders-horizontal', 'title' => 'Tune what counts', 'description' => 'Tell it what you sell and the weighting shifts. A web agency and a supplier do not want the same leads.'],
                    ],
                ],
            ],
            [
                'name' => 'Features — Act Stage',
                'slug' => 'features-stage-act',
                'type' => 'featurespage_act',
                'status' => 'published',
                'description' => 'Stage three: taking the ranked list out of the tool.',
                'data' => [
                    'key_icon' => 'ph ph-paper-plane-tilt',
                    'key_label' => 'Stage three · Act',
                    'title' => 'Then get it out of the tool.',
                    'subtitle' => 'A ranked list is only worth something once it is in front of someone. Take it wherever you already work.',
                    'items' => [
                        ['icon' => 'ph-kanban', 'title' => 'Built-in pipeline', 'description' => 'Move leads through stages, tag them, and log calls without a second tool or an export in between.'],
                        ['icon' => 'ph-envelope-open', 'title' => 'Email campaigns', 'description' => 'Send the drafted openers as a sequence, with replies landing back against the lead.'],
                        ['icon' => 'ph-download-simple', 'title' => 'Export, free', 'description' => 'CSV or XLSX, as many times as you like. Exporting a lead you already hold never costs a credit.'],
                        ['icon' => 'ph-plugs-connected', 'title' => 'REST API and webhooks', 'description' => 'Push new leads straight into your own CRM as they are scored. On the Scale plan.'],
                    ],
                ],
            ],
            [
                'name' => 'Features Page CTA',
                'slug' => 'features-cta',
                'type' => 'featurespage_cta',
                'status' => 'published',
                'description' => 'Closing call-to-action for the Features page.',
                'data' => [],
            ],
            [
                'name' => 'Pricing Page Hero',
                'slug' => 'pricing-page-head',
                'type' => 'featurespage_hero',
                'status' => 'published',
                'description' => 'Breadcrumb header for the Pricing page.',
                'data' => [
                    'title' => 'Pricing',
                    'lead' => 'Pay for the leads, not the seats.',
                ],
            ],
            [
                'name' => 'Pricing — Unit Economics',
                'slug' => 'pricing-unit',
                'type' => 'pricingpage_unit',
                'status' => 'published',
                'description' => 'Hero and "what one credit buys" breakdown for the Pricing page.',
                'data' => [],
            ],
            [
                'name' => 'Pricing — Plans',
                'slug' => 'pricing-plans',
                'type' => 'pricingpage_plans',
                'status' => 'published',
                'description' => 'Plan cards for the Pricing page.',
                'data' => [],
            ],
            [
                'name' => 'Pricing — If You Run Out',
                'slug' => 'pricing-rollover',
                'type' => 'pricingpage_rollover',
                'status' => 'published',
                'description' => 'Rollover, top-up, and data-ownership reassurance cards.',
                'data' => [],
            ],
            [
                'name' => 'Pricing — Compare Table',
                'slug' => 'pricing-compare',
                'type' => 'pricingpage_compare',
                'status' => 'published',
                'description' => 'Feature comparison table across the three plans.',
                'data' => [],
            ],
            [
                'name' => 'Pricing — FAQ',
                'slug' => 'pricing-faq',
                'type' => 'pricingpage_faq',
                'status' => 'published',
                'description' => 'Billing FAQ for the Pricing page.',
                'data' => [],
            ],
            [
                'name' => 'Pricing Page CTA',
                'slug' => 'pricing-cta',
                'type' => 'featurespage_cta',
                'status' => 'published',
                'description' => 'Closing call-to-action for the Pricing page.',
                'data' => [
                    'title' => 'Try it on 100 businesses first.',
                    'body' => 'No card, no trial countdown. Spend the free credits, see what comes back, then pick a plan.',
                    'button_text' => 'Create your account',
                    'button_link' => '/register',
                ],
            ],
            [
                'name' => 'Contact Page Hero',
                'slug' => 'contact-page-head',
                'type' => 'featurespage_hero',
                'status' => 'published',
                'description' => 'Breadcrumb header for the Contact page.',
                'data' => [
                    'title' => 'Contact',
                    'lead' => 'Sales questions, account problems, or a request to be removed from our index — this is the place for all of it.',
                ],
            ],
            [
                'name' => 'Contact — Form & Sidebar',
                'slug' => 'contact-form',
                'type' => 'contactpage_form',
                'status' => 'published',
                'description' => 'Contact form with topic options and the response-times/removal/existing-customer sidebar.',
                'data' => [],
            ],
            [
                'name' => 'Blog Page Hero',
                'slug' => 'blog-page-head',
                'type' => 'featurespage_hero',
                'status' => 'published',
                'description' => 'Breadcrumb header for the Blog page.',
                'data' => [
                    'title' => 'Blog',
                    'lead' => 'Notes on finding local businesses, scoring them, and getting a reply — written for people doing the work.',
                ],
            ],
            [
                'name' => 'Blog — Post Grid',
                'slug' => 'blog-grid',
                'type' => 'blogpage_grid',
                'status' => 'published',
                'description' => 'Category filters, featured article, post grid, and pagination for the Blog page.',
                'data' => [],
            ],
        ];

        foreach ($sections as $section) {
            FrontendSection::updateOrCreate(
                ['slug' => $section['slug']],
                [
                    'name' => $section['name'],
                    'type' => $section['type'],
                    'status' => $section['status'],
                    'description' => $section['description'],
                    'data' => $service->normalizeData($section['type'], $section['data']),
                    'theme_overrides' => [],
                    'preview_image_media_id' => null,
                ]
            );
        }
    }
}

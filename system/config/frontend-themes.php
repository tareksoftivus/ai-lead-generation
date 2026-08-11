<?php

return [
    'leadatlas' => [
        'key' => 'leadatlas',
        'label' => 'LeadAtlas',
        'description' => 'The default LeadAtlas marketing theme — map-driven lead discovery, AI scoring, and CRM pipeline messaging.',
        'preview_image' => null,
        'default_enabled' => true,
        'view_namespace' => 'frontend.themes.leadatlas',
        'supported_section_types' => [
            'hero', 'feature_grid', 'cta', 'faq', 'testimonial_grid', 'rich_content', 'footer',
            'homepage_hero', 'homepage_stages', 'homepage_features', 'homepage_voices', 'homepage_pricing', 'homepage_faq', 'homepage_blog', 'homepage_cta',
            'featurespage_hero', 'featurespage_discover', 'featurespage_layer', 'featurespage_act', 'featurespage_cta',
            'pricingpage_unit', 'pricingpage_plans', 'pricingpage_rollover', 'pricingpage_compare', 'pricingpage_faq',
            'contactpage_form',
            'blogpage_grid',
            'blog_details_hero', 'blog_details_article', 'blog_details_related',
            'termspage_hero', 'termspage_content',
            'privacypage_hero', 'privacypage_content',
        ],
        'page_layouts' => [
            'default' => [
                'label' => 'Default',
                'view' => 'layouts.page',
                'is_default' => true,
            ],
            'landing' => [
                'label' => 'Landing',
                'view' => 'layouts.landing',
            ],
        ],
        'fallback_renderer' => 'frontend.themes.leadatlas.sections.unsupported',
        'theme_settings_schema' => [
            'branding' => [
                'label' => 'Branding',
                'icon' => 'ph ph-map-trifold',
                'description' => 'Theme-specific branding and hero presentation.',
                'settings' => [
                    'logo_text' => [
                        'type' => 'text',
                        'label' => 'Logo Text',
                        'default' => 'LeadAtlas',
                        'rules' => 'nullable|string|max:100',
                    ],
                    'primary_color' => [
                        'type' => 'color',
                        'label' => 'Primary Color',
                        'default' => '#4F39F6',
                        'rules' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ],
                    'accent_color' => [
                        'type' => 'color',
                        'label' => 'Accent Color',
                        'default' => '#0F172A',
                        'rules' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ],
                    'show_hero_kicker' => [
                        'type' => 'feature',
                        'label' => 'Show Hero Kicker',
                        'default' => true,
                    ],
                ],
            ],
        ],
    ],
];

<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Models\Page;
use App\Modules\Frontend\Services\PageComposerService;
use Illuminate\Database\Seeder;

class FrontendPageSeeder extends Seeder
{
    public function run(): void
    {
        /** @var PageComposerService $composer */
        $composer = app(PageComposerService::class);

        $home = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'status' => 'published',
                'excerpt' => 'Homepage powered by the frontend management stack.',
                'default_layout' => 'landing',
                'theme_overrides' => [],
                'is_system' => true,
                'is_home' => true,
                'meta_title' => 'Home',
                'meta_description' => 'Homepage for the admin-panel frontend stack.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $about = Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About',
                'status' => 'published',
                'excerpt' => 'About page powered by shared theme-aware content.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'About',
                'meta_description' => 'About the multi-theme frontend stack.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $features = Page::updateOrCreate(
            ['slug' => 'features'],
            [
                'title' => 'Features',
                'status' => 'published',
                'excerpt' => 'What happens between typing a search and having a list worth calling.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Features',
                'meta_description' => 'What happens between typing a search and having a list worth calling — stage by stage.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $pricing = Page::updateOrCreate(
            ['slug' => 'pricing'],
            [
                'title' => 'Pricing',
                'status' => 'published',
                'excerpt' => 'Pay for the leads, not the seats.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Pricing',
                'meta_description' => 'One credit finds and enriches one business. Invite your whole team on any plan — you are billed for what you pull, not for who pulls it.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $contact = Page::updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact',
                'status' => 'published',
                'excerpt' => 'Sales questions, account problems, or a request to be removed from our index.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Contact',
                'meta_description' => 'Sales questions, account problems, or a request to be removed from our index — this is the place for all of it.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $blog = Page::updateOrCreate(
            ['slug' => 'blog'],
            [
                'title' => 'Blog',
                'status' => 'published',
                'excerpt' => 'Notes on finding local businesses, scoring them, and getting a reply.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Blog',
                'meta_description' => 'Notes on finding local businesses, scoring them, and getting a reply — written for people doing the work.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $terms = Page::updateOrCreate(
            ['slug' => 'terms'],
            [
                'title' => 'Terms & Conditions',
                'status' => 'published',
                'excerpt' => 'What a credit buys, how billing works, and who is responsible for the outreach you send.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Terms & Conditions',
                'meta_description' => 'What a credit buys, how billing works, and who is responsible for the outreach you send.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $privacy = Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'status' => 'published',
                'excerpt' => 'What we collect, why we are allowed to, and what a business can do about appearing in our index.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'What we collect, why we are allowed to, and what a business can do about appearing in our index.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $homeSectionSlugs = [
            'homepage-hero-leadatlas',
            'homepage-stages',
            'homepage-features-leadatlas',
            'homepage-voices',
            'homepage-pricing',
            'homepage-faq',
            'homepage-blog',
            'homepage-cta',
        ];

        $homeSectionIds = FrontendSection::whereIn('slug', $homeSectionSlugs)
            ->get(['id', 'slug'])
            ->sortBy(fn (FrontendSection $section) => array_search($section->slug, $homeSectionSlugs, true))
            ->pluck('id')
            ->all();

        $aboutSectionIds = FrontendSection::whereIn('slug', [
            'about-rich-content',
            'global-footer',
        ])->pluck('id')->all();

        $featuresSectionSlugs = [
            'features-page-head',
            'features-stage-discover',
            'features-stage-ai',
            'features-stage-act',
            'features-cta',
        ];

        $featuresSectionIds = FrontendSection::whereIn('slug', $featuresSectionSlugs)
            ->get(['id', 'slug'])
            ->sortBy(fn (FrontendSection $section) => array_search($section->slug, $featuresSectionSlugs, true))
            ->pluck('id')
            ->all();

        $pricingSectionSlugs = [
            'pricing-page-head',
            'pricing-unit',
            'pricing-plans',
            'pricing-rollover',
            'pricing-compare',
            'pricing-faq',
            'pricing-cta',
        ];

        $pricingSectionIds = FrontendSection::whereIn('slug', $pricingSectionSlugs)
            ->get(['id', 'slug'])
            ->sortBy(fn (FrontendSection $section) => array_search($section->slug, $pricingSectionSlugs, true))
            ->pluck('id')
            ->all();

        $contactSectionSlugs = [
            'contact-page-head',
            'contact-form',
        ];

        $contactSectionIds = FrontendSection::whereIn('slug', $contactSectionSlugs)
            ->get(['id', 'slug'])
            ->sortBy(fn (FrontendSection $section) => array_search($section->slug, $contactSectionSlugs, true))
            ->pluck('id')
            ->all();

        $blogSectionSlugs = [
            'blog-page-head',
            'blog-grid',
        ];

        $blogSectionIds = FrontendSection::whereIn('slug', $blogSectionSlugs)
            ->get(['id', 'slug'])
            ->sortBy(fn (FrontendSection $section) => array_search($section->slug, $blogSectionSlugs, true))
            ->pluck('id')
            ->all();

        $termsSectionSlugs = [
            'terms-page-head',
            'terms-rich-content',
        ];

        $termsSectionIds = FrontendSection::whereIn('slug', $termsSectionSlugs)
            ->get(['id', 'slug'])
            ->sortBy(fn (FrontendSection $section) => array_search($section->slug, $termsSectionSlugs, true))
            ->pluck('id')
            ->all();

        $privacySectionSlugs = [
            'privacy-page-head',
            'privacy-rich-content',
        ];

        $privacySectionIds = FrontendSection::whereIn('slug', $privacySectionSlugs)
            ->get(['id', 'slug'])
            ->sortBy(fn (FrontendSection $section) => array_search($section->slug, $privacySectionSlugs, true))
            ->pluck('id')
            ->all();

        $composer->syncSections($home, $homeSectionIds);
        $composer->syncSections($about, $aboutSectionIds);
        $composer->syncSections($features, $featuresSectionIds);
        $composer->syncSections($pricing, $pricingSectionIds);
        $composer->syncSections($contact, $contactSectionIds);
        $composer->syncSections($blog, $blogSectionIds);
        $composer->syncSections($terms, $termsSectionIds);
        $composer->syncSections($privacy, $privacySectionIds);
    }
}

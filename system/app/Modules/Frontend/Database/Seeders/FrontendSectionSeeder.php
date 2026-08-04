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
                'data' => [
                    'proof_count' => '128M',
                    'proof_countries' => '190',
                    'proof_text' => 'businesses indexed across :countries countries',
                    'title' => 'Every business on the map.',
                    'title_secondary' => 'Ranked before you call.',
                    'subtitle' => 'Search Google Maps by keyword and location. LeadAtlas pulls every matching business, finds who to contact, and scores how worth your time each one is.',
                    'primary_button_text' => 'Start finding leads',
                    'primary_button_link' => '/register',
                    'secondary_button_text' => 'See how scoring works',
                    'secondary_button_link' => '/features',
                    'screenshot_caption' => 'app.leadatlas.com/dashboard',
                    'screenshot_image' => null,
                    'screenshot_alt' => 'The LeadAtlas dashboard: two searches running, 1,284 leads found, and the highest-scoring businesses ranked by AI score.',
                    'lead_card_label' => 'AI analysis',
                    'lead_card_name' => 'Barton Springs Dental',
                    'lead_card_summary' => 'Busy practice, no online booking, ageing site. Strong fit for scheduling software.',
                    'lead_card_score' => 92,
                ],
            ],
            [
                'name' => 'Homepage Stages',
                'slug' => 'homepage-stages',
                'type' => 'homepage_stages',
                'status' => 'published',
                'description' => 'The funnel/run section for the LeadAtlas homepage.',
                'data' => [
                    'eyebrow' => 'The run',
                    'title' => 'You search once. The list gets shorter four times.',
                    'subtitle' => 'Every stage throws work away — that is the point. What reaches you is the remainder worth a phone call.',
                    'stages' => [
                        ['count' => '128', 'label' => 'plotted', 'variant' => 'find', 'description' => 'Every dentist Google Maps knows about within your radius — pulled with address, hours, and rating.'],
                        ['count' => '94', 'label' => 'contactable', 'variant' => 'find', 'description' => 'We follow each listing to its site and find an inbox and a direct line. Thirty-four had neither.'],
                        ['count' => '31', 'label' => 'scored 70+', 'variant' => 'ai', 'description' => 'The model reads reviews, booking flow, and site age, then rates how much your service is worth to them.'],
                        ['count' => '12', 'label' => 'worth calling', 'variant' => 'act', 'description' => 'Ranked, deduped against anyone you have already contacted, and ready to export.'],
                    ],
                    'callout_label' => 'Top of the twelve',
                    'callout_name' => 'Barton Springs Dental',
                    'callout_score' => 92,
                    'callout_text' => 'no way to book online',
                ],
            ],
            [
                'name' => 'Homepage Features',
                'slug' => 'homepage-features-leadatlas',
                'type' => 'homepage_features',
                'status' => 'published',
                'description' => 'Feature grid for the LeadAtlas homepage.',
                'data' => [
                    'eyebrow' => 'What you get',
                    'title' => 'Everything between a map pin and a signed deal.',
                    'subtitle' => 'Discovery, enrichment, scoring, and outreach in one place — so the list you export is already ranked and already contactable.',
                    'items' => [
                        ['icon' => 'ph-map-trifold', 'variant' => 'find', 'title' => 'Google Maps discovery', 'description' => 'Search any keyword and location and pull every matching business — not the first page, the whole result set.', 'meta' => '128M businesses indexed'],
                        ['icon' => 'ph-address-book', 'variant' => 'find', 'title' => 'Contact enrichment', 'description' => 'We follow each listing to its website and pull the email, phone, and social profiles the listing itself never shows.', 'meta' => 'Email · phone · socials'],
                        ['icon' => 'ph-gauge', 'variant' => 'ai', 'title' => 'AI lead scoring', 'description' => 'Reviews, site quality, and booking setup become one number from 0–100, with the reasoning written out.', 'meta' => 'Scored on every search'],
                        ['icon' => 'ph-sparkle', 'variant' => 'ai', 'title' => 'AI analysis & outreach', 'description' => 'A written summary of what each business is missing, and a first email drafted around exactly that gap.', 'meta' => 'Summary · draft email'],
                        ['icon' => 'ph-kanban', 'variant' => 'act', 'title' => 'Built-in CRM pipeline', 'description' => 'Move leads through stages, tag them, log calls and notes. No second tool and no exporting just to stay organised.', 'meta' => 'Stages · tags · activity'],
                        ['icon' => 'ph-download-simple', 'variant' => 'act', 'title' => 'Export & API', 'description' => 'Send the ranked list to CSV, your own CRM, or an email campaign — or pull it straight out over the API.', 'meta' => 'CSV · webhook · REST'],
                    ],
                ],
            ],
            [
                'name' => 'Homepage Voices',
                'slug' => 'homepage-voices',
                'type' => 'homepage_voices',
                'status' => 'published',
                'description' => 'Testimonial carousel for the LeadAtlas homepage.',
                'data' => [
                    'eyebrow' => 'Who uses it',
                    'title' => 'Built for people who sell to local business.',
                    'subtitle' => 'Agencies, freelancers, and sales teams who were doing this by hand in a spreadsheet.',
                    'items' => [],
                ],
            ],
            [
                'name' => 'Homepage Pricing',
                'slug' => 'homepage-pricing',
                'type' => 'homepage_pricing',
                'status' => 'published',
                'description' => 'Pricing plans for the LeadAtlas homepage.',
                'data' => [
                    'eyebrow' => 'Pricing',
                    'title' => 'Pay for the leads, not the seats.',
                    'subtitle' => 'One credit finds and enriches one business. Invite your whole team on any plan — you are billed for what you pull, not for who pulls it.',
                    'plans' => [],
                    'note' => 'Every plan starts with 100 free credits — no card required.',
                    'note_link_text' => 'Compare plans in full',
                    'note_link' => '/pricing',
                ],
            ],
            [
                'name' => 'Homepage FAQ',
                'slug' => 'homepage-faq',
                'type' => 'homepage_faq',
                'status' => 'published',
                'description' => 'FAQ accordion for the LeadAtlas homepage.',
                'data' => [
                    'eyebrow' => 'Questions',
                    'title' => 'Before you spend a credit.',
                    'subtitle' => 'The things people ask before signing up. If yours is not here, ask us directly.',
                    'items' => [
                        ['question' => 'What exactly does one credit pay for?', 'answer' => 'One credit covers one business, end to end — pulling it off the map, following its website for contact details, and running the AI scoring and summary. You are not charged separately for each step, and a search that returns nothing costs nothing.'],
                        ['question' => 'What happens when I run out of credits?', 'answer' => 'Searching stops until the next monthly refill, and nothing is deleted — every lead you have already pulled stays in your account and stays exportable. You can top up at any point without changing plan.'],
                        ['question' => 'How accurate is the AI scoring?', 'answer' => 'Treat it as a ranking, not a verdict. The score is a judgement built from real signals — review volume, site quality, whether online booking exists — and every score shows the reasons behind it, so you can disagree with it on a specific lead. It is there to put the best prospects at the top of a long list, not to decide who you call.'],
                        ['question' => 'Where does the data come from, and is that allowed?', 'answer' => "Business listings come from Google Maps, and contact details come from each business's own public website. It is all publicly listed business information — not personal data and not anything behind a login. You are still responsible for following the outreach rules that apply where you and your prospects are, which is why exports include the source for every field."],
                        ['question' => 'Do I have to use the AI-written emails?', 'answer' => 'No. Every generated line is a draft you can edit, rewrite, or ignore entirely — the leads and their contact details are yours regardless of whether you touch the outreach tools.'],
                        ['question' => 'Can I cancel, and what happens to my leads?', 'answer' => 'Cancel whenever you like, from your account, without emailing anyone. Export everything before your billing period ends and the data is yours to keep — we do not hold your leads hostage to a subscription.'],
                    ],
                ],
            ],
            [
                'name' => 'Homepage Blog',
                'slug' => 'homepage-blog',
                'type' => 'homepage_blog',
                'status' => 'published',
                'description' => 'Featured blog posts for the LeadAtlas homepage.',
                'data' => [
                    'eyebrow' => 'From the blog',
                    'title' => 'How this actually gets done.',
                    'subtitle' => 'Notes on finding local businesses, scoring them, and getting a reply — written for people doing the work.',
                    'posts' => [],
                    'all_articles_text' => 'All articles',
                    'all_articles_link' => '/blog',
                ],
            ],
            [
                'name' => 'Homepage CTA',
                'slug' => 'homepage-cta',
                'type' => 'homepage_cta',
                'status' => 'published',
                'description' => 'Closing call-to-action with search form for the LeadAtlas homepage.',
                'data' => [
                    'eyebrow' => 'Run one now',
                    'title' => 'Every business you want is already on the map.',
                    'keyword_label' => 'What you sell to',
                    'keyword_placeholder' => 'dentists',
                    'location_label' => 'Where',
                    'location_placeholder' => 'Austin, TX',
                    'button_text' => 'Search the map',
                    'form_action' => '/register',
                    'note' => '100 free credits · No card required · Keep every lead you export',
                ],
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
            [
                'name' => 'Terms Page Hero',
                'slug' => 'terms-page-head',
                'type' => 'featurespage_hero',
                'status' => 'published',
                'description' => 'Breadcrumb header for the Terms & Conditions page.',
                'data' => [
                    'breadcrumb_group' => 'Legal',
                    'title' => 'Terms & conditions',
                    'lead' => 'What a credit buys, how billing works, and who is responsible for the outreach you send.',
                ],
            ],
            [
                'name' => 'Terms — Content',
                'slug' => 'terms-rich-content',
                'type' => 'rich_content',
                'status' => 'published',
                'description' => 'Long-form legal content for the Terms & Conditions page.',
                'data' => [
                    'title' => 'Terms & conditions',
                    'notice' => 'These terms are written to fit an AI lead-generation product, but they are not legal advice. Have a lawyer review and adapt them before you launch.',
                    'content' => <<<'HTML'
                        <section>
                            <h2>1. The agreement</h2>
                            <p>These terms cover your use of LeadAtlas. Creating an account means accepting them, alongside our <a href="/privacy">privacy policy</a>. If you are accepting on behalf of a company, you confirm you may bind that company.</p>
                        </section>
                        <section>
                            <h2>2. Your account</h2>
                            <ul>
                                <li>You must be at least 18 and give accurate details when signing up.</li>
                                <li>You are responsible for what happens under your account, including anything your team members do.</li>
                                <li>Invite as many team members as you like — plans are priced on credits, not seats.</li>
                            </ul>
                        </section>
                        <section>
                            <h2>3. Credits</h2>
                            <p>A credit is the unit of work. One credit covers one business being pulled from the map, its contact details found, and the AI score and reasoning written for it. Drafting an outreach email costs one further credit. Searching the map and exporting a lead you already hold are free.</p>
                            <ul>
                                <li><strong>Failed enrichment is not charged.</strong> If we cannot find an email or a phone number for a business, the credit is returned to your balance automatically.</li>
                                <li>Monthly credits arrive on renewal. Up to one month's allowance rolls over; anything beyond that expires.</li>
                                <li>Top-up credits do not expire and are spent only after your monthly allowance is used.</li>
                                <li>Credits have no cash value and are not transferable.</li>
                            </ul>
                        </section>
                        <section>
                            <h2>4. Payment and cancellation</h2>
                            <p>Paid plans bill monthly in advance. You can change plan at any time — upgrades take effect immediately with the difference charged pro rata, downgrades take effect at your next renewal.</p>
                            <p>Cancelling stops the renewal. It does not delete your account or the leads you have already pulled, and we do not refund part months except where the law requires it.</p>
                        </section>
                        <section>
                            <h2>5. How you may use the data</h2>
                            <p>This is the section that matters most. LeadAtlas gives you business records assembled from public sources. What you do with them is your responsibility, and you agree that:</p>
                            <ul>
                                <li>Your outreach complies with the marketing and anti-spam law that applies to you — GDPR and PECR in the UK and EU, CAN-SPAM in the US, and their equivalents elsewhere.</li>
                                <li>You honour opt-outs and unsubscribe requests promptly, and you do not contact anyone who has told you to stop.</li>
                                <li>You will not resell the raw data as a list product, or use it for anything unlawful, harassing, or discriminatory.</li>
                            </ul>
                            <p>We may suspend an account we reasonably believe is breaking this section.</p>
                        </section>
                        <section>
                            <h2>6. What we do not promise</h2>
                            <p>AI scores, summaries, and drafted emails are generated automatically and can be wrong. They are a ranking aid, not a verified statement of fact about a business, and you should check anything you rely on commercially.</p>
                            <p>We also do not promise that a search will return a particular number of businesses, that contact details are current, or that the service will be uninterrupted. Public sources change without telling us.</p>
                        </section>
                        <section>
                            <h2>7. Your data and ours</h2>
                            <p>You keep ownership of the leads you pull and can export them at any time. We keep ownership of the platform, its interface, and its models. Neither of us gets rights in the other's property by using this service.</p>
                        </section>
                        <section>
                            <h2>8. Liability</h2>
                            <p>To the extent the law allows, our total liability in any 12-month period is limited to what you paid us in that period. We are not liable for lost profits, lost business, or lost data.</p>
                            <p>Nothing here limits liability for death, personal injury, or fraud, because it cannot.</p>
                        </section>
                        <section>
                            <h2>9. Ending the agreement</h2>
                            <p>You can close your account at any time from your settings. We can suspend or close an account for a material breach of these terms, and we will tell you why unless we are legally prevented from doing so. Your leads stay exportable for 90 days after closure.</p>
                        </section>
                        <section>
                            <h2>10. Changes and contact</h2>
                            <p>We will email account holders before a material change takes effect. Continuing to use the service afterwards means accepting the revised terms. Questions go to <a href="/contact">our contact page</a>.</p>
                        </section>
                        HTML,
                ],
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

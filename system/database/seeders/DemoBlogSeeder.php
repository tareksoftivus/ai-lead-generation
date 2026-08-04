<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Seeds demo blog categories and published posts for presentation.
 *
 * Idempotent: categories key on slug, posts key on slug.
 */
class DemoBlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = Admin::query()->orderBy('id')->first();

        $categories = [];
        foreach ($this->categories() as $index => $name) {
            $categories[$name] = BlogCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true, 'sort_order' => $index + 1]
            );
        }

        foreach ($this->posts() as $index => $data) {
            $slug = Str::slug($data['title']);

            BlogPost::firstOrCreate(
                ['slug' => $slug],
                [
                    'blog_category_id' => $categories[$data['category']]->id ?? null,
                    'author_id' => $author?->id,
                    'title' => $data['title'],
                    'slug' => $slug,
                    'excerpt' => $data['excerpt'],
                    'body' => $data['body'],
                    'status' => 'published',
                    'published_at' => $data['published_at'],
                ]
            );
        }
    }

    /**
     * @return array<int, string>
     */
    protected function categories(): array
    {
        return ['Finding leads', 'Scoring', 'Outreach', 'Compliance'];
    }

    /**
     * @return array<int, array{title: string, category: string, excerpt: string, body: string, published_at: Carbon}>
     */
    protected function posts(): array
    {
        return [
            [
                'title' => 'A map search is not a lead list. Here is the gap.',
                'category' => 'Finding leads',
                'excerpt' => 'Pulling a hundred businesses off Google Maps takes a morning. Working out which twelve are worth a call takes the rest of the week — and that second part is where most prospecting quietly dies.',
                'body' => '<p>Pulling a hundred businesses off Google Maps takes a morning. Working out which twelve are worth a call takes the rest of the week — and that second part is where most prospecting quietly dies.</p><p>A raw export of listings is not a lead list. It is a starting point that still needs contact details, a read on whether the business actually needs what you sell, and some way to rank the result so you are not calling in a random order.</p><p>That gap between "businesses on a map" and "people worth talking to" is where LeadAtlas spends most of its effort: enrichment, scoring, and ranking, so the list you end up with is already sorted by who is worth your time.</p>',
                'published_at' => Carbon::parse('2026-07-14'),
            ],
            [
                'title' => 'What a lead score should and should not tell you',
                'category' => 'Scoring',
                'excerpt' => 'A score that cannot explain itself is a horoscope. What to demand of any tool that ranks your prospects.',
                'body' => '<p>A score that cannot explain itself is a horoscope. It feels informative right up until you have to defend a decision based on it.</p><p>A useful lead score is built from signals you could check yourself — review volume, site quality, whether online booking exists — and it shows its work. You should be able to see exactly why a business scored 92 and disagree with it on a specific lead if the reasoning does not hold up.</p><p>What to demand of any tool that ranks your prospects: transparency over precision. A score that is roughly right and fully explained beats one that is precisely wrong and opaque.</p>',
                'published_at' => Carbon::parse('2026-07-08'),
            ],
            [
                'title' => 'The first line decides whether the rest gets read',
                'category' => 'Outreach',
                'excerpt' => 'Cold email openers that name a specific gap outperform anything built from a template. Some examples, and why they work.',
                'body' => '<p>Cold email openers that name a specific gap outperform anything built from a template. The reason is simple: a generic opener signals a mass send, and mass sends get deleted before the second line.</p><p>An opener that references something true and specific about the business — no online booking, a review response nobody answered, a site that has not changed in years — signals that a person actually looked. That is worth more than any clever subject line.</p><p>A few examples: "Noticed you\'re still taking bookings by phone only" reads very differently from "Hope you\'re having a great week!" One names a gap. The other names nothing.</p>',
                'published_at' => Carbon::parse('2026-07-02'),
            ],
            [
                'title' => 'Choosing a radius: why bigger is usually worse',
                'category' => 'Finding leads',
                'excerpt' => 'A wider search returns more businesses and fewer customers. How to pick the boundary that actually matches your service area.',
                'body' => '<p>A wider search returns more businesses and fewer customers. It is tempting to widen the radius to pad the numbers, but a bigger list is not a better list if half of it is outside where you can realistically deliver.</p><p>The boundary that matters is not "how far could I technically drive" but "where do I actually want to work every week." Search that area first, exhaust it, and only widen once it is genuinely thin.</p><p>A tight radius searched well beats a wide radius searched sloppily — the close prospects convert faster and cost less to serve.</p>',
                'published_at' => Carbon::parse('2026-06-24'),
            ],
            [
                'title' => 'Cold outreach and the law: a working summary',
                'category' => 'Compliance',
                'excerpt' => 'GDPR, PECR, and CAN-SPAM in the terms a salesperson needs, not a lawyer. What you can send, and what you must honour.',
                'body' => '<p>GDPR, PECR, and CAN-SPAM in the terms a salesperson needs, not a lawyer.</p><p>The common thread across all three: use publicly available business information for a relevant, identifiable offer; always include a real way to opt out; and honour that opt-out immediately, not "eventually." None of them require you to stop prospecting — they require you to be straightforward about who you are and give people an easy exit.</p><p>What you must honour is simple in practice: a working unsubscribe link, a real sender identity, and no re-adding someone who already opted out. Get those three right and most of the compliance risk goes away.</p>',
                'published_at' => Carbon::parse('2026-06-17'),
            ],
            [
                'title' => 'Signals that predict a reply, and signals that do not',
                'category' => 'Scoring',
                'excerpt' => 'Review count matters. Star rating barely does. What we found looking at which scored leads actually converted.',
                'body' => '<p>Review count matters. Star rating barely does. That was the most surprising thing we found looking at which scored leads actually converted into replies.</p><p>A business with two hundred reviews and a 4.1 rating is doing real volume and is worth reaching out to. A business with three reviews and a 5.0 rating tells you almost nothing — it is too small a sample to mean anything.</p><p>The signals that actually predicted a reply were operational: no online booking, a website that had not been touched in years, review responses left unanswered. Star rating, on its own, was close to noise.</p>',
                'published_at' => Carbon::parse('2026-06-09'),
            ],
            [
                'title' => 'Following up without becoming the person they block',
                'category' => 'Outreach',
                'excerpt' => 'How many times to follow up, how far apart, and the one thing that makes a second email worth opening.',
                'body' => '<p>How many times to follow up, how far apart, and the one thing that makes a second email worth opening: it has to add something, not just repeat the ask.</p><p>Three follow-ups spaced a few days apart is a reasonable default. Fewer than that and you are giving up too early; more than that and you risk becoming the sender people block on sight.</p><p>The one thing that makes a second email worth opening is new information — a relevant example, a specific detail you noticed since the first email, anything that is not just "just following up." Restating the same pitch with more urgency is the fastest way to get ignored.</p>',
                'published_at' => Carbon::parse('2026-06-03'),
            ],
        ];
    }
}

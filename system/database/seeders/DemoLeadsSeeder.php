<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Crm\Models\LeadContact;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\LeadBank;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\LeadNote;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\Search;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Models\Tag;
use App\Modules\Outreach\Models\LeadCampaign;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Demo data for the Leads module user flow:
 * lead search, search history, all leads, map view, lists, and tags.
 */
class DemoLeadsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->orderBy('id')->get();

        if ($users->isEmpty()) {
            return;
        }

        $places = $this->seedPlaces();

        foreach ($users as $user) {
            $this->seedForUser($user, $places);
        }
    }

    /**
     * @param  array<string, Place>|null  $places
     */
    protected function seedForUser(User $user, ?array $places = null): void
    {
        Model::unguarded(function () use ($user, $places) {
            $places ??= $this->seedPlaces();

            DB::transaction(function () use ($user, $places) {
                $this->grantDemoCreditsOnce($user, 250);

                $tags = $this->seedTags($user);
                $lists = $this->seedLists($user);
                $searches = $this->seedSearches($user);
                $runs = $this->seedSearchRuns($user, $searches, $places);

                $this->seedSavedLeads($user, $places, $runs, $lists, $tags);
                $this->seedCampaigns($user, $places, $runs, $lists);
                $this->recordGenerationSpend($user, $runs['dhaka_dentists'], 10);
                $this->recordGenerationSpend($user, $runs['gulshan_orthodontists'], 4);
                $this->recordGenerationSpend($user, $runs['austin_med_spas'], 3);
            });
        });
    }

    /**
     * @return array<string, Tag>
     */
    protected function seedTags(User $user): array
    {
        return collect(['Warm lead', 'Call first', 'Has website', 'Needs email', 'Dhaka'])
            ->mapWithKeys(function (string $name) use ($user) {
                $tag = Tag::query()->firstOrCreate([
                    'user_id' => $user->id,
                    'name' => $name,
                ]);

                return [Str::slug($name, '_') => $tag];
            })
            ->all();
    }

    /**
     * @return array<string, LeadList>
     */
    protected function seedLists(User $user): array
    {
        $definitions = [
            'dhaka_dentists_august' => [
                'name' => 'Dhaka dentists - August',
                'source' => LeadList::SOURCE_SEARCH,
                'note' => 'Generated from the demo prompt: dentists in Dhaka with 15+ reviews.',
            ],
            'high_intent_clinics' => [
                'name' => 'High intent clinics',
                'source' => LeadList::SOURCE_MANUAL,
                'note' => 'Clinics with strong reviews and direct contact details.',
            ],
            'call_this_week' => [
                'name' => 'Call this week',
                'source' => LeadList::SOURCE_MANUAL,
                'note' => 'Shortlist for immediate outreach.',
            ],
        ];

        $lists = [];

        foreach ($definitions as $key => $data) {
            $list = LeadList::query()->firstOrNew([
                'user_id' => $user->id,
                'name' => $data['name'],
            ]);

            $list->fill([
                'source' => $data['source'],
                'note' => $data['note'],
            ]);
            $list->save();

            $lists[$key] = $list;
        }

        return $lists;
    }

    /**
     * @return array<string, Place>
     */
    protected function seedPlaces(): array
    {
        $places = [];

        foreach ($this->places() as $key => $data) {
            $place = Place::query()->firstOrNew(['google_place_id' => $data['google_place_id']]);
            $place->fill([
                'name' => $data['name'],
                'formatted_address' => $data['formatted_address'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'phone' => $data['phone'],
                'website' => $data['website'],
                'google_category' => $data['google_category'],
                'rating' => $data['rating'],
                'review_count' => $data['review_count'],
                'raw_response' => [
                    'demo' => true,
                    'source' => 'DemoLeadsSeeder',
                    'business_type' => $data['business_type'],
                    'location' => $data['location'],
                ],
                'details_fetched_at' => $data['website'] || $data['phone'] ? now()->subDays(2) : null,
            ]);
            $place->save();

            LeadBank::query()->updateOrCreate(
                ['google_place_id' => $place->google_place_id],
                [
                    'place_id' => $place->id,
                    'name' => $place->name,
                    'formatted_address' => $place->formatted_address,
                    'business_type' => $data['business_type'],
                    'business_type_normalized' => $this->normalize($data['business_type']),
                    'location' => $data['location'],
                    'location_normalized' => $this->normalize($data['location']),
                    'phone' => $place->phone,
                    'website' => $place->website,
                    'google_category' => $place->google_category,
                    'rating' => $place->rating,
                    'review_count' => $place->review_count,
                    'searchable_text_normalized' => $this->normalize(implode(' ', [
                        $place->name,
                        $data['business_type'],
                        $place->google_category,
                        $place->formatted_address,
                    ])),
                    'location_text_normalized' => $this->normalize(implode(' ', [
                        $data['location'],
                        $place->formatted_address,
                    ])),
                    'raw_response' => $place->raw_response,
                    'last_seen_at' => now()->subDay(),
                ]
            );

            $places[$key] = $place;
        }

        return $places;
    }

    /**
     * @return array<string, Search>
     */
    protected function seedSearches(User $user): array
    {
        $searches = [];

        foreach ($this->savedSearches() as $key => $data) {
            $search = Search::query()->firstOrNew([
                'user_id' => $user->id,
                'name' => $data['name'],
            ]);

            $search->filters = $data['filters'];
            $search->save();

            $searches[$key] = $search;
        }

        return $searches;
    }

    /**
     * @param  array<string, Search>  $searches
     * @param  array<string, Place>  $places
     * @return array<string, SearchRun>
     */
    protected function seedSearchRuns(User $user, array $searches, array $places): array
    {
        $runs = [];

        foreach ($this->searchRuns() as $key => $data) {
            $startedAt = CarbonImmutable::parse($data['started_at']);

            $run = SearchRun::query()->firstOrNew([
                'user_id' => $user->id,
                'search_id' => $data['search_key'] ? $searches[$data['search_key']]->id : null,
                'started_at' => $startedAt,
            ]);

            $run->fill([
                'filters' => $data['filters'],
                'status' => $data['status'],
                'results_count' => count($data['place_keys']),
                'credits_spent' => $data['credits_spent'],
                'error_message' => $data['error_message'],
                'finished_at' => $data['finished_at'] ? CarbonImmutable::parse($data['finished_at']) : null,
            ]);
            $run->created_at = $startedAt;
            $run->updated_at = $data['finished_at'] ? CarbonImmutable::parse($data['finished_at']) : $startedAt;
            $run->save();

            if ($key === 'dhaka_dentists') {
                LeadList::query()
                    ->where('user_id', $user->id)
                    ->where('name', 'Dhaka dentists - August')
                    ->update(['search_run_id' => $run->id]);
            }

            foreach ($data['place_keys'] as $placeKey) {
                DB::table('search_run_results')->updateOrInsert(
                    ['search_run_id' => $run->id, 'place_id' => $places[$placeKey]->id],
                    ['created_at' => $startedAt, 'updated_at' => $startedAt]
                );
            }

            $runs[$key] = $run;
        }

        return $runs;
    }

    /**
     * @param  array<string, Place>  $places
     * @param  array<string, SearchRun>  $runs
     * @param  array<string, LeadList>  $lists
     * @param  array<string, Tag>  $tags
     */
    protected function seedSavedLeads(User $user, array $places, array $runs, array $lists, array $tags): void
    {
        foreach ($this->savedLeads() as $placeKey => $data) {
            $place = $places[$placeKey];

            $lead = Lead::withTrashed()->firstOrNew([
                'user_id' => $user->id,
                'place_id' => $place->id,
            ]);

            $lead->fill([
                'search_run_id' => $runs[$data['run_key']]->id,
                'status' => $data['status'],
                'is_in_pipeline' => $data['status'] !== Lead::STATUS_LOST,
                'pipeline_entered_at' => now()->subDays($data['days_ago']),
                'email' => $data['email'],
                'enriched_at' => $data['email'] ? now()->subDays($data['days_ago']) : null,
                'enrichment_credit_spent' => false,
                'score' => $data['score'],
                'score_signals' => $data['signals'],
            ]);
            $lead->created_at = now()->subDays($data['days_ago']);
            $lead->updated_at = now()->subDays(max(0, $data['days_ago'] - 1));
            $lead->deleted_at = null;
            $lead->save();

            $lead->lists()->syncWithoutDetaching(
                collect($data['lists'])->map(fn(string $key) => $lists[$key]->id)->all()
            );

            $lead->tags()->syncWithoutDetaching(
                collect($data['tags'])->map(fn(string $key) => $tags[$key]->id)->all()
            );

            LeadNote::query()->firstOrCreate([
                'lead_id' => $lead->id,
                'user_id' => $user->id,
                'body' => $data['note'],
            ]);

            if ($data['email'] || $place->phone) {
                LeadContact::query()->updateOrCreate([
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'name' => $data['contact_name'],
                ], [
                    'role' => $data['contact_role'],
                    'email' => $data['email'],
                    'phone' => $place->phone,
                    'note' => $data['contact_note'],
                    'is_primary' => true,
                    'last_contacted_at' => in_array($data['status'], [Lead::STATUS_CONTACTED, Lead::STATUS_REPLIED, Lead::STATUS_QUALIFIED], true)
                        ? now()->subDays(max(1, $data['days_ago'] - 1))
                        : null,
                ]);
            }

            LeadActivity::query()->firstOrCreate([
                'lead_id' => $lead->id,
                'type' => LeadActivity::TYPE_FOUND_IN_SEARCH,
                'caused_by_user_id' => $user->id,
            ], [
                'payload' => ['search_run_id' => $runs[$data['run_key']]->id, 'demo' => true],
                'created_at' => $lead->created_at,
            ]);

            LeadActivity::query()->firstOrCreate([
                'lead_id' => $lead->id,
                'type' => LeadActivity::TYPE_SCORED,
                'caused_by_user_id' => null,
            ], [
                'payload' => ['score' => $lead->score, 'demo' => true],
                'created_at' => $lead->created_at,
            ]);

            LeadActivity::query()->firstOrCreate([
                'lead_id' => $lead->id,
                'type' => LeadActivity::TYPE_NOTE_ADDED,
                'caused_by_user_id' => $user->id,
            ], [
                'payload' => [
                    'kind' => $data['activity_kind'],
                    'body' => $data['activity_body'],
                    'demo' => true,
                ],
                'created_at' => now()->subDays(max(0, $data['days_ago'] - 1)),
            ]);
        }
    }

    /**
     * @param  array<string, Place>  $places
     * @param  array<string, SearchRun>  $runs
     * @param  array<string, LeadList>  $lists
     */
    protected function seedCampaigns(User $user, array $places, array $runs, array $lists): void
    {
        if (! Schema::hasTable('lead_campaigns') || ! Schema::hasTable('lead_campaign_recipients')) {
            return;
        }

        foreach ($this->campaigns() as $key => $data) {
            $createdAt = CarbonImmutable::parse($data['created_at']);
            $sourceId = match ($data['source_type']) {
                'list' => $lists[$data['source_key']]->id,
                'search' => $runs[$data['source_key']]->id,
                default => null,
            };

            $campaign = LeadCampaign::query()->firstOrNew([
                'user_id' => $user->id,
                'name' => $data['name'],
            ]);

            $campaign->fill([
                'status' => $data['status'],
                'source_type' => $data['source_type'],
                'source_id' => $sourceId,
                'daily_limit' => $data['daily_limit'],
                'recipients_count' => count($data['lead_keys']),
                'sent_count' => $data['sent_count'],
                'opened_count' => $data['opened_count'],
                'replied_count' => $data['replied_count'],
                'approved_at' => $data['approved_at'] ? CarbonImmutable::parse($data['approved_at']) : null,
                'started_at' => $data['started_at'] ? CarbonImmutable::parse($data['started_at']) : null,
                'finished_at' => $data['finished_at'] ? CarbonImmutable::parse($data['finished_at']) : null,
            ]);
            $campaign->created_at = $createdAt;
            $campaign->updated_at = $data['updated_at'] ? CarbonImmutable::parse($data['updated_at']) : $createdAt;
            $campaign->deleted_at = null;
            $campaign->save();

            $sync = [];
            $sentRemaining = $data['sent_count'];
            $openedRemaining = $data['opened_count'];
            $repliedRemaining = $data['replied_count'];

            foreach ($data['lead_keys'] as $leadKey) {
                $lead = Lead::query()
                    ->forUser($user->id)
                    ->where('place_id', $places[$leadKey]->id)
                    ->first();

                if (! $lead) {
                    continue;
                }

                $status = 'pending';
                $sentAt = null;
                $openedAt = null;
                $repliedAt = null;

                if ($sentRemaining > 0) {
                    $status = 'sent';
                    $sentAt = $data['started_at'] ? CarbonImmutable::parse($data['started_at'])->addHours(count($sync)) : $createdAt;
                    $sentRemaining--;
                }

                if ($openedRemaining > 0 && $sentAt) {
                    $status = 'opened';
                    $openedAt = $sentAt->addHours(4);
                    $openedRemaining--;
                }

                if ($repliedRemaining > 0 && $openedAt) {
                    $status = 'replied';
                    $repliedAt = $openedAt->addHours(8);
                    $repliedRemaining--;
                }

                $sync[$lead->id] = [
                    'status' => $status,
                    'sent_at' => $sentAt,
                    'opened_at' => $openedAt,
                    'replied_at' => $repliedAt,
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ];
            }

            $campaign->leads()->sync($sync);
            $campaign->updateQuietly(['recipients_count' => count($sync)]);
        }
    }

    protected function grantDemoCreditsOnce(User $user, int $amount): void
    {
        if (
            CreditTransaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'grant')
                ->where('reason', 'demo_leads_seed')
                ->exists()
        ) {
            return;
        }

        $user->increment('credits_balance', $amount);
        $user->refresh();

        CreditTransaction::query()->create([
            'user_id' => $user->id,
            'type' => 'grant',
            'amount' => $amount,
            'balance_after' => $user->credits_balance,
            'reason' => 'demo_leads_seed',
            'metadata' => ['demo' => true, 'label' => 'Demo leads workspace credit grant'],
        ]);
    }

    protected function recordGenerationSpend(User $user, SearchRun $run, int $amount): void
    {
        if (
            $amount <= 0 || CreditTransaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'spend')
                ->where('reason', 'lead_generation')
                ->where('reference_type', $run->getMorphClass())
                ->where('reference_id', $run->id)
                ->exists()
        ) {
            return;
        }

        $user->refresh();

        if ($user->credits_balance < $amount) {
            $user->increment('credits_balance', $amount - $user->credits_balance);
            $user->refresh();
        }

        $user->decrement('credits_balance', $amount);
        $user->refresh();

        CreditTransaction::query()->create([
            'user_id' => $user->id,
            'type' => 'spend',
            'amount' => -$amount,
            'balance_after' => $user->credits_balance,
            'reason' => 'lead_generation',
            'reference_type' => $run->getMorphClass(),
            'reference_id' => $run->id,
            'metadata' => [
                'demo' => true,
                'results_count' => $run->results_count,
                'filters' => $run->filters,
            ],
        ]);
    }

    protected function normalize(string $value): string
    {
        return Str::of($value)->lower()->squish()->toString();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function places(): array
    {
        return [
            'smile_craft' => ['google_place_id' => 'demo-dhaka-dentist-001', 'name' => 'Smile Craft Dental Care', 'formatted_address' => 'House 22, Road 11, Banani, Dhaka 1213', 'lat' => 23.7937, 'lng' => 90.4043, 'phone' => '+880 1711-000101', 'website' => 'https://smilecraft.example.com', 'google_category' => 'Dentist', 'rating' => 4.8, 'review_count' => 126, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'gulshan_dental' => ['google_place_id' => 'demo-dhaka-dentist-002', 'name' => 'Gulshan Dental Studio', 'formatted_address' => 'Gulshan Avenue, Dhaka 1212', 'lat' => 23.7806, 'lng' => 90.4160, 'phone' => '+880 1711-000102', 'website' => 'https://gulshandental.example.com', 'google_category' => 'Dental clinic', 'rating' => 4.6, 'review_count' => 84, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'dhanmondi_smile' => ['google_place_id' => 'demo-dhaka-dentist-003', 'name' => 'Dhanmondi Smile Center', 'formatted_address' => 'Road 8A, Dhanmondi, Dhaka 1209', 'lat' => 23.7465, 'lng' => 90.3760, 'phone' => '+880 1711-000103', 'website' => 'https://dhanmondismile.example.com', 'google_category' => 'Dentist', 'rating' => 4.7, 'review_count' => 61, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'uttara_family' => ['google_place_id' => 'demo-dhaka-dentist-004', 'name' => 'Uttara Family Dental', 'formatted_address' => 'Sector 7, Uttara, Dhaka 1230', 'lat' => 23.8759, 'lng' => 90.3795, 'phone' => '+880 1711-000104', 'website' => 'https://uttaradental.example.com', 'google_category' => 'Dental clinic', 'rating' => 4.5, 'review_count' => 45, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'mirpur_dental' => ['google_place_id' => 'demo-dhaka-dentist-005', 'name' => 'Mirpur Dental Clinic', 'formatted_address' => 'Mirpur 10, Dhaka 1216', 'lat' => 23.8069, 'lng' => 90.3686, 'phone' => '+880 1711-000105', 'website' => null, 'google_category' => 'Dentist', 'rating' => 4.2, 'review_count' => 32, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'bashundhara_point' => ['google_place_id' => 'demo-dhaka-dentist-006', 'name' => 'Bashundhara Dental Point', 'formatted_address' => 'Block C, Bashundhara R/A, Dhaka 1229', 'lat' => 23.8150, 'lng' => 90.4258, 'phone' => '+880 1711-000106', 'website' => 'https://bdentalpoint.example.com', 'google_category' => 'Dental clinic', 'rating' => 4.4, 'review_count' => 58, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'mohammadpur_care' => ['google_place_id' => 'demo-dhaka-dentist-007', 'name' => 'Mohammadpur Dental Care', 'formatted_address' => 'Tajmahal Road, Mohammadpur, Dhaka 1207', 'lat' => 23.7654, 'lng' => 90.3588, 'phone' => '+880 1711-000107', 'website' => 'https://mdentalcare.example.com', 'google_category' => 'Dentist', 'rating' => 4.1, 'review_count' => 24, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'banani_orthodontics' => ['google_place_id' => 'demo-dhaka-dentist-008', 'name' => 'Banani Orthodontics', 'formatted_address' => 'Road 17, Banani, Dhaka 1213', 'lat' => 23.7948, 'lng' => 90.4079, 'phone' => '+880 1711-000108', 'website' => 'https://bananiortho.example.com', 'google_category' => 'Orthodontist', 'rating' => 4.9, 'review_count' => 73, 'business_type' => 'orthodontists', 'location' => 'Dhaka'],
            'gulshan_ortho' => ['google_place_id' => 'demo-dhaka-dentist-009', 'name' => 'Gulshan Orthodontic Center', 'formatted_address' => 'Road 47, Gulshan 2, Dhaka 1212', 'lat' => 23.7930, 'lng' => 90.4146, 'phone' => '+880 1711-000109', 'website' => 'https://gulshanortho.example.com', 'google_category' => 'Orthodontist', 'rating' => 4.8, 'review_count' => 67, 'business_type' => 'orthodontists', 'location' => 'Dhaka'],
            'lalmatia_dental' => ['google_place_id' => 'demo-dhaka-dentist-010', 'name' => 'Lalmatia Dental House', 'formatted_address' => 'Block D, Lalmatia, Dhaka 1207', 'lat' => 23.7536, 'lng' => 90.3692, 'phone' => null, 'website' => null, 'google_category' => 'Dental clinic', 'rating' => 4.0, 'review_count' => 18, 'business_type' => 'dentists', 'location' => 'Dhaka'],
            'austin_medspa' => ['google_place_id' => 'demo-austin-medspa-001', 'name' => 'Lakeview Med Spa', 'formatted_address' => '2101 Lake Austin Blvd, Austin, TX', 'lat' => 30.2791, 'lng' => -97.7725, 'phone' => '+1 512 555 0101', 'website' => 'https://lakeviewmedspa.example.com', 'google_category' => 'Medical spa', 'rating' => 4.7, 'review_count' => 94, 'business_type' => 'med spas', 'location' => 'Austin, TX'],
            'austin_glow' => ['google_place_id' => 'demo-austin-medspa-002', 'name' => 'South Congress Glow Clinic', 'formatted_address' => '1400 S Congress Ave, Austin, TX', 'lat' => 30.2500, 'lng' => -97.7490, 'phone' => '+1 512 555 0102', 'website' => 'https://socoglow.example.com', 'google_category' => 'Medical spa', 'rating' => 4.5, 'review_count' => 71, 'business_type' => 'med spas', 'location' => 'Austin, TX'],
            'austin_skin' => ['google_place_id' => 'demo-austin-medspa-003', 'name' => 'Domain Skin Lab', 'formatted_address' => '11821 Rock Rose Ave, Austin, TX', 'lat' => 30.4014, 'lng' => -97.7221, 'phone' => '+1 512 555 0103', 'website' => null, 'google_category' => 'Skin care clinic', 'rating' => 4.1, 'review_count' => 36, 'business_type' => 'med spas', 'location' => 'Austin, TX'],
        ];
    }

    /**
     * @return array<string, array{name: string, filters: array<string, mixed>}>
     */
    protected function savedSearches(): array
    {
        return [
            'dhaka_dentists' => [
                'name' => 'Dhaka dentists with 15+ reviews',
                'filters' => ['keyword' => ['dentists'], 'location' => ['Dhaka'], 'min_reviews_from' => 15, 'requested_count' => 10, 'skip_owned' => true, 'prompt' => 'Find the dentists in Dhaka with 15 reviews and at least 10 leads.'],
            ],
            'gulshan_orthodontists' => [
                'name' => 'Gulshan orthodontists',
                'filters' => ['keyword' => ['orthodontists'], 'location' => ['Dhaka'], 'min_rating' => 4, 'requested_count' => 5, 'has_phone' => true, 'prompt' => 'Find 5 orthodontists in Gulshan with phone numbers.'],
            ],
            'austin_med_spas' => [
                'name' => 'Austin med spas',
                'filters' => ['keyword' => ['med spas'], 'location' => ['Austin, TX'], 'min_reviews_from' => 25, 'requested_count' => 5],
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function searchRuns(): array
    {
        return [
            'dhaka_dentists' => ['search_key' => 'dhaka_dentists', 'filters' => $this->savedSearches()['dhaka_dentists']['filters'], 'status' => SearchRun::STATUS_DONE, 'place_keys' => ['smile_craft', 'gulshan_dental', 'dhanmondi_smile', 'uttara_family', 'mirpur_dental', 'bashundhara_point', 'mohammadpur_care', 'banani_orthodontics', 'gulshan_ortho', 'lalmatia_dental'], 'credits_spent' => 10, 'error_message' => null, 'started_at' => '2026-08-09 10:05:00', 'finished_at' => '2026-08-09 10:05:18'],
            'gulshan_orthodontists' => ['search_key' => 'gulshan_orthodontists', 'filters' => $this->savedSearches()['gulshan_orthodontists']['filters'], 'status' => SearchRun::STATUS_DONE, 'place_keys' => ['banani_orthodontics', 'gulshan_ortho', 'smile_craft', 'gulshan_dental'], 'credits_spent' => 4, 'error_message' => null, 'started_at' => '2026-08-08 15:20:00', 'finished_at' => '2026-08-08 15:20:11'],
            'austin_med_spas' => ['search_key' => 'austin_med_spas', 'filters' => $this->savedSearches()['austin_med_spas']['filters'], 'status' => SearchRun::STATUS_DONE, 'place_keys' => ['austin_medspa', 'austin_glow', 'austin_skin'], 'credits_spent' => 3, 'error_message' => null, 'started_at' => '2026-08-06 09:40:00', 'finished_at' => '2026-08-06 09:40:09'],
            'running_dhaka_clinics' => ['search_key' => null, 'filters' => ['keyword' => ['dental clinics'], 'location' => ['Dhaka'], 'requested_count' => 5, 'prompt' => 'Find 5 dental clinics in Dhaka.'], 'status' => SearchRun::STATUS_RUNNING, 'place_keys' => [], 'credits_spent' => 0, 'error_message' => null, 'started_at' => '2026-08-10 09:30:00', 'finished_at' => null],
            'failed_law_firms' => ['search_key' => null, 'filters' => ['keyword' => ['law firms'], 'location' => ['Seattle, WA'], 'requested_count' => 5], 'status' => SearchRun::STATUS_FAILED, 'place_keys' => [], 'credits_spent' => 0, 'error_message' => 'Demo failure: Google Maps API quota was unavailable during this run.', 'started_at' => '2026-08-05 13:10:00', 'finished_at' => '2026-08-05 13:10:04'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function savedLeads(): array
    {
        return [
            'smile_craft' => ['run_key' => 'dhaka_dentists', 'status' => Lead::STATUS_QUALIFIED, 'email' => 'hello@smilecraft.example.com', 'score' => 96, 'signals' => ['review_volume' => 'Strong', 'booking_presence' => 'Strong', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['dhaka_dentists_august', 'high_intent_clinics', 'call_this_week'], 'tags' => ['warm_lead', 'call_first', 'has_website', 'dhaka'], 'days_ago' => 1, 'note' => 'Strong review count and a clear website. Call first for cosmetic dentistry offer.', 'contact_name' => 'Nadia Rahman', 'contact_role' => 'Practice manager', 'contact_note' => 'Primary clinic manager', 'activity_kind' => 'call', 'activity_body' => 'Called the front desk and asked for Nadia. Follow up tomorrow.'],
            'gulshan_dental' => ['run_key' => 'dhaka_dentists', 'status' => Lead::STATUS_CONTACTED, 'email' => 'contact@gulshandental.example.com', 'score' => 88, 'signals' => ['review_volume' => 'Strong', 'booking_presence' => 'Fair', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['dhaka_dentists_august', 'high_intent_clinics'], 'tags' => ['warm_lead', 'has_website', 'dhaka'], 'days_ago' => 2, 'note' => 'Sent intro email. Follow up with clinic manager this week.', 'contact_name' => 'Farhan Chowdhury', 'contact_role' => 'Clinic manager', 'contact_note' => 'Asked for email first', 'activity_kind' => 'note', 'activity_body' => 'Sent intro email with the booking-system angle.'],
            'dhanmondi_smile' => ['run_key' => 'dhaka_dentists', 'status' => Lead::STATUS_REPLIED, 'email' => 'info@dhanmondismile.example.com', 'score' => 86, 'signals' => ['review_volume' => 'Fair', 'booking_presence' => 'Strong', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['dhaka_dentists_august', 'call_this_week'], 'tags' => ['warm_lead', 'call_first', 'has_website', 'dhaka'], 'days_ago' => 3, 'note' => 'They replied asking for pricing. Prepare a short proposal.', 'contact_name' => 'Ayesha Karim', 'contact_role' => 'Owner', 'contact_note' => 'Interested in pricing', 'activity_kind' => 'note', 'activity_body' => 'They replied asking for package pricing.'],
            'uttara_family' => ['run_key' => 'dhaka_dentists', 'status' => Lead::STATUS_NEW, 'email' => 'appointments@uttaradental.example.com', 'score' => 80, 'signals' => ['review_volume' => 'Fair', 'booking_presence' => 'Fair', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['dhaka_dentists_august'], 'tags' => ['has_website', 'dhaka'], 'days_ago' => 4, 'note' => 'Good fit for family dentistry campaign.', 'contact_name' => 'Front desk', 'contact_role' => 'Reception', 'contact_note' => 'Appointments inbox', 'activity_kind' => 'note', 'activity_body' => 'Add to the next family dentistry outreach batch.'],
            'mirpur_dental' => ['run_key' => 'dhaka_dentists', 'status' => Lead::STATUS_NEW, 'email' => null, 'score' => 61, 'signals' => ['review_volume' => 'Fair', 'booking_presence' => 'Weak', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['dhaka_dentists_august'], 'tags' => ['needs_email', 'dhaka'], 'days_ago' => 4, 'note' => 'Phone only. Needs manual email lookup before campaign.', 'contact_name' => 'Phone desk', 'contact_role' => 'Reception', 'contact_note' => 'Phone only', 'activity_kind' => 'call', 'activity_body' => 'Phone listed, no email found. Call before adding to campaign.'],
            'bashundhara_point' => ['run_key' => 'dhaka_dentists', 'status' => Lead::STATUS_CONTACTED, 'email' => 'care@bdentalpoint.example.com', 'score' => 78, 'signals' => ['review_volume' => 'Fair', 'booking_presence' => 'Fair', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['dhaka_dentists_august', 'high_intent_clinics'], 'tags' => ['has_website', 'dhaka'], 'days_ago' => 5, 'note' => 'Mention Bashundhara residential area campaign angle.', 'contact_name' => 'Care team', 'contact_role' => 'Front desk', 'contact_note' => 'General care inbox', 'activity_kind' => 'note', 'activity_body' => 'Mention Bashundhara local appointment demand in follow up.'],
            'banani_orthodontics' => ['run_key' => 'gulshan_orthodontists', 'status' => Lead::STATUS_QUALIFIED, 'email' => 'team@bananiortho.example.com', 'score' => 95, 'signals' => ['review_volume' => 'Strong', 'booking_presence' => 'Strong', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['high_intent_clinics', 'call_this_week'], 'tags' => ['warm_lead', 'call_first', 'has_website', 'dhaka'], 'days_ago' => 2, 'note' => 'Premium orthodontics positioning. Ask about invisalign lead volume.', 'contact_name' => 'Samira Hossain', 'contact_role' => 'Treatment coordinator', 'contact_note' => 'Best contact for orthodontics offer', 'activity_kind' => 'call', 'activity_body' => 'Coordinator is available after 2 PM.'],
            'gulshan_ortho' => ['run_key' => 'gulshan_orthodontists', 'status' => Lead::STATUS_NEW, 'email' => 'hello@gulshanortho.example.com', 'score' => 93, 'signals' => ['review_volume' => 'Strong', 'booking_presence' => 'Strong', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['high_intent_clinics'], 'tags' => ['warm_lead', 'has_website', 'dhaka'], 'days_ago' => 2, 'note' => 'Strong score and niche service. Add to orthodontics campaign.', 'contact_name' => 'Ortho team', 'contact_role' => 'Office manager', 'contact_note' => 'Shared inbox', 'activity_kind' => 'note', 'activity_body' => 'Strong score and niche service. Add to orthodontics campaign.'],
            'austin_medspa' => ['run_key' => 'austin_med_spas', 'status' => Lead::STATUS_CONTACTED, 'email' => 'bookings@lakeviewmedspa.example.com', 'score' => 89, 'signals' => ['review_volume' => 'Strong', 'booking_presence' => 'Fair', 'website_age' => 'Fair', 'local_competition' => 'Fair'], 'lists' => ['high_intent_clinics'], 'tags' => ['warm_lead', 'has_website'], 'days_ago' => 6, 'note' => 'Useful cross-market example for map and list filters.', 'contact_name' => 'Booking team', 'contact_role' => 'Spa manager', 'contact_note' => 'Demo cross-market contact', 'activity_kind' => 'note', 'activity_body' => 'Useful cross-market example for map and list filters.'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function campaigns(): array
    {
        return [
            'dhaka_dentists_review' => [
                'name' => 'Dhaka dentists - booking follow-up',
                'status' => LeadCampaign::STATUS_REVIEW,
                'source_type' => 'list',
                'source_key' => 'dhaka_dentists_august',
                'lead_keys' => ['smile_craft', 'gulshan_dental', 'dhanmondi_smile', 'uttara_family', 'bashundhara_point'],
                'daily_limit' => 40,
                'sent_count' => 0,
                'opened_count' => 0,
                'replied_count' => 0,
                'approved_at' => null,
                'started_at' => null,
                'finished_at' => null,
                'created_at' => '2026-08-10 09:15:00',
                'updated_at' => '2026-08-10 09:15:00',
            ],
            'gulshan_orthodontists_active' => [
                'name' => 'Gulshan orthodontists - first touch',
                'status' => LeadCampaign::STATUS_ACTIVE,
                'source_type' => 'search',
                'source_key' => 'gulshan_orthodontists',
                'lead_keys' => ['banani_orthodontics', 'gulshan_ortho', 'smile_craft'],
                'daily_limit' => 25,
                'sent_count' => 2,
                'opened_count' => 1,
                'replied_count' => 0,
                'approved_at' => '2026-08-09 16:00:00',
                'started_at' => '2026-08-10 08:00:00',
                'finished_at' => null,
                'created_at' => '2026-08-09 15:35:00',
                'updated_at' => '2026-08-10 08:40:00',
            ],
            'high_intent_done' => [
                'name' => 'High intent clinics - August opener',
                'status' => LeadCampaign::STATUS_DONE,
                'source_type' => 'list',
                'source_key' => 'high_intent_clinics',
                'lead_keys' => ['smile_craft', 'gulshan_dental', 'dhanmondi_smile', 'bashundhara_point', 'banani_orthodontics', 'gulshan_ortho', 'austin_medspa'],
                'daily_limit' => 50,
                'sent_count' => 7,
                'opened_count' => 4,
                'replied_count' => 2,
                'approved_at' => '2026-08-06 11:00:00',
                'started_at' => '2026-08-06 12:00:00',
                'finished_at' => '2026-08-08 17:00:00',
                'created_at' => '2026-08-06 10:30:00',
                'updated_at' => '2026-08-08 17:00:00',
            ],
            'austin_paused' => [
                'name' => 'Austin med spas - cross-market test',
                'status' => LeadCampaign::STATUS_PAUSED,
                'source_type' => 'search',
                'source_key' => 'austin_med_spas',
                'lead_keys' => ['austin_medspa'],
                'daily_limit' => 10,
                'sent_count' => 1,
                'opened_count' => 1,
                'replied_count' => 0,
                'approved_at' => '2026-08-07 10:00:00',
                'started_at' => '2026-08-07 11:00:00',
                'finished_at' => null,
                'created_at' => '2026-08-07 09:45:00',
                'updated_at' => '2026-08-07 14:20:00',
            ],
        ];
    }
}

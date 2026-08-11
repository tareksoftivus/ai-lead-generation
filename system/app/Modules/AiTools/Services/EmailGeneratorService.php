<?php

namespace App\Modules\AiTools\Services;

use App\Models\User;
use App\Modules\AiTools\Models\BusinessAnalysisItem;
use App\Modules\AiTools\Models\EmailDraft;
use App\Modules\AiTools\Models\EmailTemplate;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Outreach\Models\LeadCampaign;
use App\Modules\Outreach\Services\CampaignService;
use App\Modules\Outreach\Services\LeadSourceService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmailGeneratorService
{
    public function __construct(
        protected CampaignService $campaigns,
        protected CreditLedger $ledger,
        protected AiToolsCreditCost $costs
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function dashboardData(User $user, ?int $leadId = null, ?int $draftId = null): array
    {
        $leads = Lead::query()
            ->forUser($user->id)
            ->with('place')
            ->latest()
            ->limit(200)
            ->get();

        $lists = LeadList::query()
            ->forUser($user->id)
            ->withCount('leads')
            ->orderBy('name')
            ->get();

        $selectedDraft = $draftId
            ? EmailDraft::query()->forUser($user->id)->with(['lead.place', 'leadList', 'campaign'])->find($draftId)
            : null;

        $selectedLead = $selectedDraft?->lead
            ?? ($leadId ? $leads->firstWhere('id', $leadId) : null)
            ?? $leads->first();

        $analysis = $selectedLead
            ? $this->latestAnalysis($user, $selectedLead)
            : null;

        $draft = $selectedDraft ?: $this->previewDraft($user, $selectedLead, $analysis);

        return [
            'leads' => $leads,
            'lists' => $lists,
            'selectedLead' => $selectedLead,
            'selectedDraft' => $selectedDraft,
            'analysis' => $analysis,
            'draft' => $draft,
            'templates' => EmailTemplate::query()->forUser($user->id)->latest()->limit(8)->get(),
            'recentDrafts' => EmailDraft::query()->forUser($user->id)->with('lead.place')->latest()->limit(6)->get(),
            'tones' => $this->tones(),
            'lengths' => $this->lengths(),
            'openings' => $this->openings(),
            'emailCreditCost' => $this->costs->perEmailGeneration(),
            'balance' => $this->ledger->balance($user),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function generate(User $user, array $data): EmailDraft
    {
        $lead = Lead::query()
            ->forUser($user->id)
            ->with('place')
            ->findOrFail((int) $data['lead_id']);

        return DB::transaction(function () use ($user, $data, $lead): EmailDraft {
            $analysis = $this->latestAnalysis($user, $lead);
            $content = $this->content($user, $lead, $analysis, $data);
            $creditsToSpend = $this->costs->perEmailGeneration();

            $draft = EmailDraft::query()->create([
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'lead_list_id' => $data['scope_type'] === 'list' ? (int) ($data['lead_list_id'] ?? 0) : null,
                'scope_type' => $data['scope_type'],
                'tone' => $data['tone'],
                'length' => $data['length'],
                'opening' => $data['opening'],
                'subject' => $content['subject'],
                'body' => $content['body'],
                'gap' => $content['gap'],
                'status' => EmailDraft::STATUS_DRAFT,
                'metadata' => $content['metadata'] + [
                    'credits_spent' => $creditsToSpend,
                    'cost_per_generation' => $this->costs->perEmailGeneration(),
                ],
                'generated_at' => now(),
            ]);

            if ($creditsToSpend > 0) {
                $this->ledger->spend($user, $creditsToSpend, 'email_generation', $draft, [
                    'lead_id' => $lead->id,
                    'lead_list_id' => $draft->lead_list_id,
                    'scope_type' => $draft->scope_type,
                    'cost_per_generation' => $this->costs->perEmailGeneration(),
                ]);
            }

            return $draft;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveTemplate(User $user, array $data): EmailTemplate
    {
        return EmailTemplate::query()->create([
            'user_id' => $user->id,
            'name' => $data['template_name'],
            'gap_key' => $data['template_gap'],
            'tone' => $data['tone'],
            'length' => $data['length'],
            'opening' => $data['opening'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'metadata' => [
                'source_draft_id' => Arr::get($data, 'draft_id'),
                'source_lead_id' => Arr::get($data, 'lead_id'),
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function queueCampaign(User $user, array $data): LeadCampaign
    {
        return DB::transaction(function () use ($user, $data): LeadCampaign {
            $lead = Lead::query()
                ->forUser($user->id)
                ->with('place')
                ->findOrFail((int) $data['lead_id']);

            $campaignData = [
                'name' => $this->campaignName($lead, $data),
                'daily_limit' => 40,
            ];

            if ($data['scope_type'] === 'list') {
                $campaignData['source_type'] = LeadSourceService::SOURCE_LIST;
                $campaignData['source_id'] = (int) $data['lead_list_id'];
                $selectedIds = [];
            } else {
                $campaignData['source_type'] = LeadSourceService::SOURCE_SELECTION;
                $campaignData['source_id'] = null;
                $selectedIds = [$lead->id];
            }

            $this->ensureContactableRecipients($user, $lead, $data);

            $campaign = $this->campaigns->create($user, $campaignData, $selectedIds);

            $draft = $this->persistEditedDraft($user, $lead, $data, $campaign);
            $draft->update([
                'lead_campaign_id' => $campaign->id,
                'status' => EmailDraft::STATUS_QUEUED,
            ]);

            return $campaign->fresh();
        });
    }

    public function latestAnalysis(User $user, Lead $lead): ?BusinessAnalysisItem
    {
        return BusinessAnalysisItem::query()
            ->forUser($user->id)
            ->where('lead_id', $lead->id)
            ->latest('analysed_at')
            ->first();
    }

    /**
     * @return array<string, string>
     */
    public function tones(): array
    {
        return [
            'direct' => __('Direct - get to the point'),
            'warm' => __('Warm - friendly, less formal'),
            'formal' => __('Formal - for a larger practice'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function lengths(): array
    {
        return [
            'short' => __('Short - under 80 words'),
            'medium' => __('Medium - around 120 words'),
            'long' => __('Long - the full case, around 200'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function openings(): array
    {
        return [
            'gap' => __('The gap we found'),
            'praise' => __('Something they do well'),
            'question' => __('A question'),
        ];
    }

    protected function previewDraft(User $user, ?Lead $lead, ?BusinessAnalysisItem $analysis): object
    {
        if (! $lead) {
            return (object) [
                'id' => null,
                'lead_id' => null,
                'lead_list_id' => null,
                'scope_type' => 'one',
                'tone' => 'direct',
                'length' => 'medium',
                'opening' => 'gap',
                'subject' => __('Choose a lead to generate an email'),
                'body' => __('Save or import leads first, then this page will write a draft from the business data you already have.'),
                'gap' => null,
                'status' => EmailDraft::STATUS_DRAFT,
            ];
        }

        $content = $this->content($user, $lead, $analysis, [
            'tone' => 'direct',
            'length' => 'medium',
            'opening' => 'gap',
        ]);

        return (object) [
            'id' => null,
            'lead_id' => $lead->id,
            'lead_list_id' => $lead->lists()->first()?->id,
            'scope_type' => 'one',
            'tone' => 'direct',
            'length' => 'medium',
            'opening' => 'gap',
            'subject' => $content['subject'],
            'body' => $content['body'],
            'gap' => $content['gap'],
            'status' => EmailDraft::STATUS_DRAFT,
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{subject: string, body: string, gap: string, metadata: array<string, mixed>}
     */
    protected function content(User $user, Lead $lead, ?BusinessAnalysisItem $analysis, array $settings): array
    {
        $place = $lead->place;
        $businessName = $place?->name ?? __('this business');
        $category = $place?->google_category ?: __('local business');
        $reviewCount = (int) ($place?->review_count ?? 0);
        $rating = $place?->rating ? number_format((float) $place->rating, 1) : null;
        $gap = $analysis?->gap ?? $this->fallbackGap($lead);
        $tone = (string) ($settings['tone'] ?? 'direct');
        $length = (string) ($settings['length'] ?? 'medium');
        $opening = (string) ($settings['opening'] ?? 'gap');

        $subject = $this->subject($businessName, $gap, $opening);
        $greeting = $tone === 'formal'
            ? "Hello {$businessName} team,"
            : "Hi {$businessName} team,";

        $opener = match ($opening) {
            'praise' => $reviewCount > 0
                ? "I noticed {$businessName} has {$reviewCount} public reviews".($rating ? " averaging {$rating} stars" : '').", which is a strong trust signal."
                : "I noticed {$businessName} has enough local presence to deserve a more specific opening than a generic sales email.",
            'question' => "Are you currently trying to turn more of the interest around {$businessName} into booked conversations?",
            default => "I was looking at {$businessName} and noticed a useful gap: {$gap}.",
        };

        $pitch = match ($tone) {
            'warm' => "The opportunity is not to change how your team works overnight. It is to make the next step feel easier for someone who is already interested.",
            'formal' => "A focused improvement here can reduce friction for prospective customers while keeping the existing front-desk or sales process intact.",
            default => "That is usually the kind of friction worth fixing first, because it sits right between interest and action.",
        };

        $proof = $analysis?->read
            ?? "You are visible as a {$category}, so the outreach should be specific to what buyers can already see before they contact you.";

        $close = match ($tone) {
            'formal' => "Would it be useful to compare what is visible now with a cleaner conversion path?",
            'warm' => "Worth a quick look together to see whether this would help?",
            default => "Worth a short call to see if it fits?",
        };

        $paragraphs = match ($length) {
            'short' => [$greeting, $opener, $close, __('Best,'), $user->name],
            'long' => [
                $greeting,
                $opener,
                $proof,
                $pitch,
                "If helpful, I can show a simple before-and-after path for {$businessName}: what a prospect sees now, where the drop-off probably happens, and what a lighter next step could look like.",
                $close,
                __('Best,'),
                $user->name,
            ],
            default => [$greeting, $opener, $proof, $pitch, $close, __('Best,'), $user->name],
        };

        return [
            'subject' => $subject,
            'body' => implode("\n\n", array_filter($paragraphs)),
            'gap' => $gap,
            'metadata' => [
                'business_name' => $businessName,
                'category' => $category,
                'review_count' => $reviewCount,
                'rating' => $rating,
                'analysis_item_id' => $analysis?->id,
                'has_contact_email' => $lead->hasContact(),
            ],
        ];
    }

    protected function subject(string $businessName, string $gap, string $opening): string
    {
        if ($opening === 'question') {
            return Str::limit("A quick question about {$businessName}", 190, '');
        }

        if ($opening === 'praise') {
            return Str::limit("A thought after looking at {$businessName}", 190, '');
        }

        $shortGap = Str::of($gap)->lower()->limit(48, '');

        return Str::limit(Str::headline((string) $shortGap).' at '.$businessName, 190, '');
    }

    protected function fallbackGap(Lead $lead): string
    {
        $place = $lead->place;

        if (! $place?->website) {
            return __('no clear website for buyers to inspect before contacting you');
        }

        if (! $place?->phone) {
            return __('no phone number visible for urgent enquiries');
        }

        if (! $lead->hasContact()) {
            return __('no direct email captured yet, so interested buyers have fewer contact paths');
        }

        return __('visible demand that could turn into more booked conversations');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function ensureContactableRecipients(User $user, Lead $lead, array $data): void
    {
        if ($data['scope_type'] !== 'list') {
            if (! $lead->hasContact()) {
                throw ValidationException::withMessages([
                    'lead_id' => __('This lead does not have an email address yet. Copy the draft manually or enrich the lead first.'),
                ]);
            }

            return;
        }

        $count = Lead::query()
            ->forUser($user->id)
            ->whereNotNull('email')
            ->whereHas('lists', fn ($query) => $query->where('lead_lists.id', (int) $data['lead_list_id']))
            ->count();

        if ($count < 1) {
            throw ValidationException::withMessages([
                'lead_list_id' => __('That list has no leads with email addresses yet.'),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function persistEditedDraft(User $user, Lead $lead, array $data, LeadCampaign $campaign): EmailDraft
    {
        $attributes = [
            'user_id' => $user->id,
            'lead_id' => $lead->id,
            'lead_list_id' => $data['scope_type'] === 'list' ? (int) $data['lead_list_id'] : null,
            'lead_campaign_id' => $campaign->id,
            'scope_type' => $data['scope_type'],
            'tone' => $data['tone'],
            'length' => $data['length'],
            'opening' => $data['opening'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'gap' => $this->latestAnalysis($user, $lead)?->gap ?? $this->fallbackGap($lead),
            'status' => EmailDraft::STATUS_QUEUED,
            'metadata' => ['edited_before_campaign' => true],
            'generated_at' => now(),
        ];

        if (! empty($data['draft_id'])) {
            $draft = EmailDraft::query()
                ->forUser($user->id)
                ->find((int) $data['draft_id']);

            if ($draft) {
                $draft->update($attributes);

                return $draft;
            }
        }

        return EmailDraft::query()->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function campaignName(Lead $lead, array $data): string
    {
        if ($data['scope_type'] === 'list') {
            $list = LeadList::query()
                ->forUser($lead->user_id)
                ->find((int) $data['lead_list_id']);

            return Str::limit(($list?->name ?? __('Lead list')).' - email review', 160, '');
        }

        return Str::limit(($lead->place?->name ?? __('Lead')).' - email review', 160, '');
    }
}

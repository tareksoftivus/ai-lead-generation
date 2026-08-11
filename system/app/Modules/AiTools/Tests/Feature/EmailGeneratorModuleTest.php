<?php

use App\Models\User;
use App\Modules\AiTools\Models\BusinessAnalysisItem;
use App\Modules\AiTools\Models\EmailDraft;
use App\Modules\AiTools\Models\EmailTemplate;
use App\Modules\AiTools\Services\AiToolsCreditCost;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use App\Modules\Outreach\Models\LeadCampaign;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    app(SettingsService::class)->clearCache();
});

function emailGeneratorUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
        'credits_balance' => 10,
    ], $attributes));
}

function emailGeneratorPlace(array $attributes = []): Place
{
    return Place::query()->create(array_merge([
        'google_place_id' => 'email-generator-place-'.uniqid(),
        'name' => 'Dhaka Dental Studio',
        'formatted_address' => 'Gulshan, Dhaka',
        'phone' => '+8801700000000',
        'website' => 'https://example.test',
        'google_category' => 'dentist',
        'rating' => 4.8,
        'review_count' => 84,
    ], $attributes));
}

function emailGeneratorLead(User $user, array $attributes = []): Lead
{
    return Lead::query()->create(array_merge([
        'user_id' => $user->id,
        'place_id' => emailGeneratorPlace()->id,
        'email' => 'hello@example.test',
        'score' => 88,
    ], $attributes));
}

function emailGeneratorList(User $user, array $leads = []): LeadList
{
    $list = LeadList::query()->create([
        'user_id' => $user->id,
        'name' => 'Clinic follow ups',
        'source' => LeadList::SOURCE_MANUAL,
    ]);

    if ($leads) {
        $list->leads()->attach(collect($leads)->pluck('id')->all());
    }

    return $list;
}

function setEmailAiToolCost(string $key, int $value): void
{
    app(SettingsService::class)->set($key, $value);
}

it('shows saved leads and lists instead of the static sample data', function () {
    $user = emailGeneratorUser();
    $lead = emailGeneratorLead($user, [
        'place_id' => emailGeneratorPlace(['name' => 'Gulshan Smile Care'])->id,
    ]);
    emailGeneratorList($user, [$lead]);

    $this->actingAs($user)
        ->get(route('user.email.index'))
        ->assertSuccessful()
        ->assertSee('Gulshan Smile Care')
        ->assertSee('Clinic follow ups')
        ->assertDontSee('Barton Springs Dental')
        ->assertDontSee('Austin dentists');
});

it('generates and stores a draft from the latest business analysis gap and spends the configured cost', function () {
    setEmailAiToolCost(AiToolsCreditCost::EMAIL_GENERATION_SETTING, 4);

    $user = emailGeneratorUser(['credits_balance' => 10]);
    $lead = emailGeneratorLead($user);
    $list = emailGeneratorList($user, [$lead]);

    BusinessAnalysisItem::query()->create([
        'user_id' => $user->id,
        'lead_id' => $lead->id,
        'score' => 91,
        'read' => 'Dhaka Dental Studio has strong demand signal.',
        'gap' => 'No online booking on a busy practice',
        'fit' => 'Strong fit',
        'fit_status' => BusinessAnalysisItem::FIT_YES,
        'analysed_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('user.email.generate'), [
            'lead_id' => $lead->id,
            'scope_type' => 'list',
            'lead_list_id' => $list->id,
            'tone' => 'warm',
            'length' => 'medium',
            'opening' => 'gap',
        ])
        ->assertRedirect();

    $draft = EmailDraft::first();

    expect($draft)->not->toBeNull()
        ->and($draft->lead_id)->toBe($lead->id)
        ->and($draft->lead_list_id)->toBe($list->id)
        ->and($draft->gap)->toBe('No online booking on a busy practice')
        ->and($draft->body)->toContain('No online booking on a busy practice')
        ->and($user->fresh()->credits_balance)->toBe(6)
        ->and(CreditTransaction::query()->where('reason', 'email_generation')->first()->amount)->toBe(-4);
});

it('blocks email generation before saving a draft when credits are insufficient', function () {
    setEmailAiToolCost(AiToolsCreditCost::EMAIL_GENERATION_SETTING, 4);

    $user = emailGeneratorUser(['credits_balance' => 3]);
    $lead = emailGeneratorLead($user);

    $this->actingAs($user)
        ->post(route('user.email.generate'), [
            'lead_id' => $lead->id,
            'scope_type' => 'one',
            'tone' => 'direct',
            'length' => 'medium',
            'opening' => 'gap',
        ])
        ->assertSessionHas('error', 'You dont have sufficient credits please upgrade your plan');

    expect(EmailDraft::query()->count())->toBe(0)
        ->and(CreditTransaction::query()->count())->toBe(0)
        ->and($user->fresh()->credits_balance)->toBe(3);
});

it('saves the edited draft as a reusable template', function () {
    $user = emailGeneratorUser();
    $lead = emailGeneratorLead($user);

    $this->actingAs($user)
        ->post(route('user.email.templates.store'), [
            'lead_id' => $lead->id,
            'template_name' => 'Booking gap opener',
            'template_gap' => 'booking',
            'tone' => 'direct',
            'length' => 'short',
            'opening' => 'gap',
            'subject' => 'Booking online',
            'body' => 'Edited body the user wants to keep.',
        ])
        ->assertRedirect(route('user.email.index', ['lead' => $lead->id]));

    expect(EmailTemplate::query()->count())->toBe(1)
        ->and(EmailTemplate::first()->name)->toBe('Booking gap opener')
        ->and(EmailTemplate::first()->body)->toBe('Edited body the user wants to keep.');
});

it('creates a review campaign from the edited draft without sending it', function () {
    $user = emailGeneratorUser();
    $lead = emailGeneratorLead($user);

    $this->actingAs($user)
        ->post(route('user.email.campaigns.store'), [
            'lead_id' => $lead->id,
            'scope_type' => 'one',
            'tone' => 'formal',
            'length' => 'long',
            'opening' => 'question',
            'subject' => 'A quick question',
            'body' => 'Edited campaign body.',
        ])
        ->assertRedirect(route('user.campaigns.index'));

    $campaign = LeadCampaign::first();
    $draft = EmailDraft::first();

    expect($campaign)->not->toBeNull()
        ->and($campaign->status)->toBe(LeadCampaign::STATUS_REVIEW)
        ->and($campaign->recipients_count)->toBe(1)
        ->and($draft->lead_campaign_id)->toBe($campaign->id)
        ->and($draft->status)->toBe(EmailDraft::STATUS_QUEUED)
        ->and($draft->body)->toBe('Edited campaign body.');
});

it('rejects another users lead', function () {
    $user = emailGeneratorUser();
    $other = emailGeneratorUser();
    $lead = emailGeneratorLead($other);

    $this->actingAs($user)
        ->post(route('user.email.generate'), [
            'lead_id' => $lead->id,
            'scope_type' => 'one',
            'tone' => 'direct',
            'length' => 'medium',
            'opening' => 'gap',
        ])
        ->assertSessionHasErrors('lead_id');
});

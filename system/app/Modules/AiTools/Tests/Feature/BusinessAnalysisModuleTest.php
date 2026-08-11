<?php

use App\Models\User;
use App\Modules\AiTools\Models\BusinessAnalysisItem;
use App\Modules\AiTools\Models\BusinessAnalysisRun;
use App\Modules\AiTools\Services\AiToolsCreditCost;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    app(SettingsService::class)->clearCache();
});

function analysisUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
        'credits_balance' => 10,
    ], $attributes));
}

function analysisPlace(array $attributes = []): Place
{
    return Place::query()->create(array_merge([
        'google_place_id' => 'analysis-place-'.uniqid(),
        'name' => 'Barton Springs Dental',
        'formatted_address' => '1401 S Lamar Blvd, Austin, TX',
        'phone' => '(512) 555-0143',
        'website' => 'https://example.com',
        'google_category' => 'dentist',
        'rating' => 4.7,
        'review_count' => 312,
    ], $attributes));
}

function analysisLead(User $user, array $attributes = []): Lead
{
    return Lead::query()->create(array_merge([
        'user_id' => $user->id,
        'place_id' => analysisPlace()->id,
        'score' => 92,
    ], $attributes));
}

function analysisList(User $user, array $leads = []): LeadList
{
    $list = LeadList::query()->create([
        'user_id' => $user->id,
        'name' => 'Austin dentists',
        'source' => LeadList::SOURCE_MANUAL,
    ]);

    if ($leads) {
        $list->leads()->attach(collect($leads)->pluck('id')->all());
    }

    return $list;
}

function setAnalysisAiToolCost(string $key, int $value): void
{
    app(SettingsService::class)->set($key, $value);
}

it('shows real lead lists instead of the static sample data', function () {
    $user = analysisUser();
    $lead = analysisLead($user, [
        'place_id' => analysisPlace(['name' => 'Dhaka Dental Studio'])->id,
    ]);
    analysisList($user, [$lead]);

    $this->actingAs($user)
        ->get(route('user.analysis.index'))
        ->assertSuccessful()
        ->assertSee('Austin dentists')
        ->assertSee('1 lead')
        ->assertDontSee('Chicago clinics');
});

it('runs analysis for a list and spends the configured cost per analysed business', function () {
    setAnalysisAiToolCost(AiToolsCreditCost::BUSINESS_ANALYSIS_SETTING, 3);

    $user = analysisUser(['credits_balance' => 10]);
    $leadA = analysisLead($user);
    $leadB = analysisLead($user, [
        'place_id' => analysisPlace(['name' => 'Lamar Family Dentistry', 'google_place_id' => 'lamar-family'])->id,
        'score' => 78,
    ]);
    $list = analysisList($user, [$leadA, $leadB]);

    $this->actingAs($user)
        ->post(route('user.analysis.run'), [
            'source' => $list->id,
            'skip_analysed' => '1',
            'focus' => 'gaps',
        ])
        ->assertRedirect(route('user.analysis.index', ['source' => $list->id]));

    $run = BusinessAnalysisRun::first();

    expect($run->businesses_count)->toBe(2)
        ->and($run->credits_spent)->toBe(6)
        ->and(BusinessAnalysisItem::query()->count())->toBe(2)
        ->and($user->fresh()->credits_balance)->toBe(4)
        ->and(CreditTransaction::query()->where('reason', 'business_analysis')->count())->toBe(1)
        ->and(CreditTransaction::query()->where('reason', 'business_analysis')->first()->amount)->toBe(-6);
});

it('skips and does not charge again for businesses excluded from a run', function () {
    $user = analysisUser(['credits_balance' => 5]);
    $leadA = analysisLead($user);
    $leadB = analysisLead($user, [
        'place_id' => analysisPlace(['name' => 'Lamar Family Dentistry', 'google_place_id' => 'lamar-family'])->id,
    ]);
    $list = analysisList($user, [$leadA, $leadB]);

    BusinessAnalysisItem::query()->create([
        'user_id' => $user->id,
        'lead_id' => $leadA->id,
        'score' => 88,
        'read' => 'Existing read',
        'gap' => 'Existing gap',
        'fit' => 'Existing fit',
        'fit_status' => BusinessAnalysisItem::FIT_YES,
        'analysed_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('user.analysis.run'), [
            'source' => $list->id,
            'skip_analysed' => '1',
            'focus' => 'fit',
        ])
        ->assertRedirect(route('user.analysis.index', ['source' => $list->id]));

    $run = BusinessAnalysisRun::first();

    expect($run->businesses_count)->toBe(1)
        ->and($run->credits_spent)->toBe(1)
        ->and(BusinessAnalysisItem::query()->count())->toBe(2)
        ->and($user->fresh()->credits_balance)->toBe(4);
});

it('blocks analysis before writing a run when credits are insufficient', function () {
    $user = analysisUser(['credits_balance' => 1]);
    $leadA = analysisLead($user);
    $leadB = analysisLead($user, [
        'place_id' => analysisPlace(['name' => 'Lamar Family Dentistry', 'google_place_id' => 'lamar-family'])->id,
    ]);
    $list = analysisList($user, [$leadA, $leadB]);

    $this->actingAs($user)
        ->post(route('user.analysis.run'), [
            'source' => $list->id,
            'skip_analysed' => '1',
            'focus' => 'summary',
        ])
        ->assertSessionHas('error', 'You dont have sufficient credits please upgrade your plan');

    expect(BusinessAnalysisRun::query()->count())->toBe(0)
        ->and(BusinessAnalysisItem::query()->count())->toBe(0)
        ->and($user->fresh()->credits_balance)->toBe(1);
});

it('handles empty lists without spending credits', function () {
    $user = analysisUser(['credits_balance' => 5]);
    $list = analysisList($user);

    $this->actingAs($user)
        ->post(route('user.analysis.run'), [
            'source' => $list->id,
            'skip_analysed' => '1',
            'focus' => 'gaps',
        ])
        ->assertRedirect(route('user.analysis.index', ['source' => $list->id]))
        ->assertSessionHas('success', 'That list has no businesses yet. Add leads to it before running analysis.');

    expect(BusinessAnalysisRun::first()->businesses_count)->toBe(0)
        ->and(BusinessAnalysisItem::query()->count())->toBe(0)
        ->and($user->fresh()->credits_balance)->toBe(5);
});

it('rejects a lead list owned by another user', function () {
    $user = analysisUser();
    $other = analysisUser();
    $list = analysisList($other, [analysisLead($other)]);

    $this->actingAs($user)
        ->post(route('user.analysis.run'), [
            'source' => $list->id,
            'skip_analysed' => '1',
            'focus' => 'gaps',
        ])
        ->assertSessionHasErrors('source');
});

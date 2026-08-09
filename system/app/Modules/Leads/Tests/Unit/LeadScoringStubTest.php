<?php

use App\Models\User;
use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Contracts\OutreachDraftGenerator;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Services\HeuristicLeadScorer;
use App\Modules\Leads\Services\TemplateOutreachDraftGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function scoringUser(): User
{
    return User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
}

function scoringPlace(array $attributes = []): Place
{
    return Place::query()->create(array_merge([
        'google_place_id' => 'place-'.uniqid(),
        'name' => 'Barton Springs Dental',
        'google_category' => 'Dentist',
        'website' => 'https://bartonspringsdental.com',
        'phone' => '(512) 555-0143',
        'rating' => 4.7,
        'review_count' => 312,
    ], $attributes));
}

it('resolves LeadScorer and OutreachDraftGenerator to their default stub implementations from the container', function () {
    expect(app(LeadScorer::class))->toBeInstanceOf(HeuristicLeadScorer::class)
        ->and(app(OutreachDraftGenerator::class))->toBeInstanceOf(TemplateOutreachDraftGenerator::class);
});

it('computes a heuristic score and signal breakdown within bounds for a well-reviewed business', function () {
    $user = scoringUser();
    $place = scoringPlace();
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $result = app(LeadScorer::class)->score($lead);

    expect($result['score'])->toBeGreaterThanOrEqual(0)
        ->and($result['score'])->toBeLessThanOrEqual(100)
        ->and($result['score'])->toBeGreaterThan(50) // strong reviews + rating + website + phone
        ->and($result['headline'])->toBeString()->not->toBeEmpty()
        ->and($result['explanation'])->toContain('312 reviews')
        ->and($result['signals'])->toHaveKeys(['review_volume', 'booking_presence', 'website_age', 'local_competition'])
        ->and($result['signals']['review_volume'])->toBe('Strong');
});

it('scores a business with no data at the floor', function () {
    $user = scoringUser();
    $place = scoringPlace([
        'google_place_id' => 'place-empty',
        'website' => null,
        'phone' => null,
        'rating' => null,
        'review_count' => null,
    ]);
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $result = app(LeadScorer::class)->score($lead);

    expect($result['score'])->toBe(0)
        ->and($result['signals']['review_volume'])->toBe('Weak')
        ->and($result['signals']['booking_presence'])->toBe('Weak');
});

it('clamps the score at 100 for an exceptionally strong business', function () {
    $user = scoringUser();
    $place = scoringPlace([
        'google_place_id' => 'place-strong',
        'review_count' => 5000,
        'rating' => 5.0,
    ]);
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $result = app(LeadScorer::class)->score($lead);

    expect($result['score'])->toBe(100);
});

it('generates a template outreach draft interpolating the business name and category', function () {
    $user = scoringUser();
    $place = scoringPlace();
    $lead = Lead::query()->create(['user_id' => $user->id, 'place_id' => $place->id]);

    $draft = app(OutreachDraftGenerator::class)->draft($lead);

    expect($draft)->toContain('Barton Springs Dental')
        ->and($draft)->toContain('Dentist');
});

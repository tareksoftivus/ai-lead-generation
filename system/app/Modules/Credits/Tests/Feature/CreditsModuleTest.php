<?php

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Listeners\GrantStarterCredits;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function creditsUser(array $attributes = []): User
{
    Permission::findOrCreate('credits.view', 'web');

    $user = User::factory()->create(array_merge([
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));

    $user->givePermissionTo('credits.view');

    return $user;
}

it('grants 100 starter credits on first login only', function () {
    $user = creditsUser();

    $this->actingAs($user)->post(route('login'), []);
    // Directly trigger the login event to avoid depending on the login controller flow.
    event(new Login('web', $user, false));

    expect($user->fresh()->credits_balance)->toBe(100)
        ->and(CreditTransaction::query()->forUser($user->id)->where('reason', GrantStarterCredits::STARTER_GRANT_REASON)->count())->toBe(1);

    // A second login must not grant again.
    event(new Login('web', $user, false));

    expect($user->fresh()->credits_balance)->toBe(100)
        ->and(CreditTransaction::query()->forUser($user->id)->where('reason', GrantStarterCredits::STARTER_GRANT_REASON)->count())->toBe(1);
});

it('atomically spends credits and prevents overspending', function () {
    $user = creditsUser(['credits_balance' => 5]);
    $ledger = app(CreditLedger::class);

    $ledger->spend($user, 3, 'enrichment');
    expect($user->fresh()->credits_balance)->toBe(2);

    $ledger->spend($user, 2, 'enrichment');
    expect($user->fresh()->credits_balance)->toBe(0);

    expect(fn () => $ledger->spend($user, 1, 'enrichment'))
        ->toThrow(InsufficientCreditsException::class);

    expect($user->fresh()->credits_balance)->toBe(0);
});

it('records a balance_after snapshot matching the running total', function () {
    $user = creditsUser(['credits_balance' => 10]);
    $ledger = app(CreditLedger::class);

    $first = $ledger->spend($user, 4, 'enrichment');
    $second = $ledger->spend($user, 1, 'enrichment');

    expect($first->balance_after)->toBe(6)
        ->and($second->balance_after)->toBe(5)
        ->and($first->amount)->toBe(-4);
});

it('throws InsufficientCreditsException carrying the user and amount needed', function () {
    $user = creditsUser(['credits_balance' => 0]);
    $ledger = app(CreditLedger::class);

    try {
        $ledger->spend($user, 1, 'enrichment');
        $this->fail('Expected InsufficientCreditsException to be thrown.');
    } catch (InsufficientCreditsException $exception) {
        expect($exception->user->id)->toBe($user->id)
            ->and($exception->amountNeeded)->toBe(1);
    }
});

it('grants credits and records a positive transaction', function () {
    $user = creditsUser(['credits_balance' => 0]);
    $ledger = app(CreditLedger::class);

    $transaction = $ledger->grant($user, 50, 'purchase');

    expect($user->fresh()->credits_balance)->toBe(50)
        ->and($transaction->amount)->toBe(50)
        ->and($transaction->type)->toBe('grant');
});

it('shows the credits index page with balance and transaction history', function () {
    $user = creditsUser(['credits_balance' => 42]);
    app(CreditLedger::class)->grant($user, 42, 'starter_grant');

    $this->actingAs($user)
        ->get(route('user.credits.index'))
        ->assertSuccessful()
        ->assertSee('42');
});

it('shows active pricing plans on the credits buy page', function () {
    $user = creditsUser();

    PricingPlan::query()->create([
        'name' => 'Growth',
        'slug' => 'growth',
        'tagline' => 'For teams ready to turn scored leads into outreach.',
        'icon' => 'ph-sparkle',
        'price_monthly' => 89,
        'price_yearly' => 890,
        'credits_monthly' => 5000,
        'features' => ['AI summaries and drafted emails'],
        'cta_label' => 'Start free',
        'is_active' => true,
        'is_featured' => true,
        'sort_order' => 1,
    ]);

    PricingPlan::query()->create([
        'name' => 'Archived',
        'slug' => 'archived',
        'price_monthly' => 9,
        'price_yearly' => 90,
        'credits_monthly' => 100,
        'is_active' => false,
        'sort_order' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('user.credits.buy'))
        ->assertSuccessful()
        ->assertSee('Growth')
        ->assertSee('5,000')
        ->assertSee('$89')
        ->assertSee('Checkout')
        ->assertSee('Most bought')
        ->assertSee('AI summaries and drafted emails')
        ->assertDontSee('Archived');
});

it('shows a checkout page for a selected pricing plan', function () {
    $user = creditsUser(['credits_balance' => 25]);

    $plan = PricingPlan::query()->create([
        'name' => 'Scale',
        'slug' => 'scale',
        'tagline' => 'For agencies and teams running searches at volume.',
        'price_monthly' => 249,
        'price_yearly' => 2490,
        'credits_monthly' => 20000,
        'features' => ['REST API and webhooks'],
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('user.credits.checkout.start'), ['plan' => $plan->slug])
        ->assertRedirect(route('user.credits.checkout', $plan->slug));

    $this->actingAs($user)
        ->get(route('user.credits.checkout', $plan->slug))
        ->assertSuccessful()
        ->assertSee('Checkout')
        ->assertSee('Scale')
        ->assertSee('20,000')
        ->assertSee('$249')
        ->assertSee('Payment method')
        ->assertSee('Development gateway')
        ->assertSee('Gateway charge')
        ->assertSee('Total payable')
        ->assertSee('Continue checkout');
});

it('completes a pricing plan purchase and grants plan credits once', function () {
    $user = creditsUser(['credits_balance' => 10]);

    $plan = PricingPlan::query()->create([
        'name' => 'Growth',
        'slug' => 'growth',
        'price_monthly' => 89,
        'price_yearly' => 890,
        'credits_monthly' => 5000,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('user.credits.checkout.complete', $plan->slug), ['gateway' => 'log'])
        ->assertRedirect(route('user.credits.index'));

    expect($user->fresh()->credits_balance)->toBe(5010)
        ->and(CreditTransaction::query()->forUser($user->id)->where('reason', 'pricing_plan_purchase')->count())->toBe(1);
});

<?php

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Listeners\GrantStarterCredits;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditLedger;
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

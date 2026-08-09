<?php

namespace App\Modules\Credits\Listeners;

use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditLedger;
use Illuminate\Auth\Events\Login;

class GrantStarterCredits
{
    public const STARTER_GRANT_AMOUNT = 100;

    public const STARTER_GRANT_REASON = 'starter_grant';

    public function __construct(
        protected CreditLedger $ledger
    ) {}

    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $user = $event->user;

        $alreadyGranted = CreditTransaction::query()
            ->forUser($user->id)
            ->where('reason', self::STARTER_GRANT_REASON)
            ->exists();

        if ($alreadyGranted) {
            return;
        }

        $this->ledger->grant($user, self::STARTER_GRANT_AMOUNT, self::STARTER_GRANT_REASON);
    }
}

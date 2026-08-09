<?php

namespace App\Modules\Credits\Services;

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Models\CreditTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreditLedger
{
    public function balance(User $user): int
    {
        return (int) ($user->credits_balance ?? 0);
    }

    public function canAfford(User $user, int $amount): bool
    {
        return $this->balance($user) >= $amount;
    }

    /**
     * Atomically spend $amount credits, locking the user row for the duration
     * of the check-and-decrement so concurrent spends can't overdraw the balance.
     *
     * @throws InsufficientCreditsException
     */
    public function spend(User $user, int $amount, string $reason, ?Model $reference = null, array $metadata = []): CreditTransaction
    {
        return DB::transaction(function () use ($user, $amount, $reason, $reference, $metadata) {
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);

            if ($locked->credits_balance < $amount) {
                throw new InsufficientCreditsException($locked, $amount);
            }

            $locked->decrement('credits_balance', $amount);

            return CreditTransaction::create([
                'user_id' => $locked->id,
                'type' => 'spend',
                'amount' => -$amount,
                'balance_after' => $locked->fresh()->credits_balance,
                'reason' => $reason,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Atomically grant $amount credits.
     */
    public function grant(User $user, int $amount, string $reason, ?Model $reference = null, array $metadata = []): CreditTransaction
    {
        return DB::transaction(function () use ($user, $amount, $reason, $reference, $metadata) {
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);

            $locked->increment('credits_balance', $amount);

            return CreditTransaction::create([
                'user_id' => $locked->id,
                'type' => 'grant',
                'amount' => $amount,
                'balance_after' => $locked->fresh()->credits_balance,
                'reason' => $reason,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'metadata' => $metadata,
            ]);
        });
    }
}

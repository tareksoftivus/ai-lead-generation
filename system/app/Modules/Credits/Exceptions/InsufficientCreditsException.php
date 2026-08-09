<?php

namespace App\Modules\Credits\Exceptions;

use App\Models\User;
use RuntimeException;

class InsufficientCreditsException extends RuntimeException
{
    public function __construct(
        public readonly User $user,
        public readonly int $amountNeeded
    ) {
        parent::__construct(sprintf(
            'User #%d has insufficient credits: needed %d, has %d.',
            $user->id,
            $amountNeeded,
            $user->credits_balance
        ));
    }
}

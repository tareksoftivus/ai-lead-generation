<?php

namespace App\Modules\Credits\Listeners;

use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;

class GrantPricingPlanCredits
{
    public const PURCHASE_REASON = 'pricing_plan_purchase';

    public function __construct(
        protected CreditLedger $ledger
    ) {}

    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment->fresh();
        $metadata = $payment->metadata ?? [];

        if (($metadata['context'] ?? null) !== 'credits_plan_purchase') {
            return;
        }

        if (! empty($metadata['credits_granted_at'])) {
            return;
        }

        $credits = (int) ($metadata['credits'] ?? 0);
        if ($credits <= 0 || ! $payment->user instanceof User) {
            return;
        }

        $alreadyGranted = CreditTransaction::query()
            ->where('reason', self::PURCHASE_REASON)
            ->where('reference_type', $payment->getMorphClass())
            ->where('reference_id', $payment->getKey())
            ->exists();

        if ($alreadyGranted) {
            return;
        }

        $transaction = $this->ledger->grant($payment->user, $credits, self::PURCHASE_REASON, $payment, [
            'payment_uuid' => $payment->uuid,
            'pricing_plan_id' => $metadata['pricing_plan_id'] ?? null,
            'pricing_plan_slug' => $metadata['pricing_plan_slug'] ?? null,
            'pricing_plan_name' => $metadata['pricing_plan_name'] ?? null,
            'billing_period' => $metadata['billing_period'] ?? 'monthly',
        ]);

        $payment->update([
            'metadata' => array_merge($metadata, [
                'credits_granted_at' => now()->toISOString(),
                'credit_transaction_id' => $transaction->id,
            ]),
        ]);
    }
}

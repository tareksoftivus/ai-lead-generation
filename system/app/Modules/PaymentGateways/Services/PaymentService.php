<?php

namespace App\Modules\PaymentGateways\Services;

use App\Modules\PaymentGateways\DataObjects\PaymentData;
use App\Modules\PaymentGateways\DataObjects\PaymentResponse;
use App\Modules\PaymentGateways\Events\PaymentCreated;
use App\Modules\PaymentGateways\Events\PaymentFailed;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\PaymentGateways\Events\RefundProcessed;
use App\Modules\PaymentGateways\Exceptions\PaymentException;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\Shared\Traits\HasCrudOperations;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentService
{
    use HasCrudOperations;

    protected string $model = Payment::class;

    /** @var array<string> */
    protected array $searchable = ['uuid', 'gateway_payment_id', 'description'];

    /** @var array<string> */
    protected array $filterable = ['status', 'gateway', 'currency'];

    protected string $defaultSortBy = 'created_at';

    protected string $defaultSortOrder = 'desc';

    public function __construct(
        protected PaymentGatewayManager $manager
    ) {}

    /**
     * Create a new payment through the active (or specified) gateway.
     *
     * Usage:
     *   $payment = app(PaymentService::class)->charge(29.99, 'USD', [
     *       'description' => 'Pro Plan',
     *       'gateway' => 'stripe',          // optional, defaults to active gateway
     *       'user_id' => auth()->id(),       // optional, auto-detected
     *       'return_url' => route('payment.return'),
     *       'cancel_url' => route('payment.cancel'),
     *       'metadata' => ['plan_id' => 5],
     *   ]);
     *
     * @return array{payment: Payment, response: PaymentResponse}
     */
    public function charge(float $amount, string $currency = '', array $options = []): array
    {
        $currency = $currency ?: payment_gateway_setting('payment_currency', 'USD');
        $paymentUuid = (string) Str::uuid();
        $metadata = array_merge($options['metadata'] ?? [], [
            'payment_uuid' => $paymentUuid,
        ]);

        $user = auth()->user();
        $data = new PaymentData(
            amount: $amount,
            currency: strtoupper($currency),
            description: $options['description'] ?? null,
            paymentMethod: $options['payment_method'] ?? null,
            userId: $options['user_id'] ?? $user?->getKey(),
            userType: $options['user_type'] ?? $user?->getMorphClass(),
            metadata: $metadata,
            returnUrl: $this->appendQueryParameters($options['return_url'] ?? null, ['payment_uuid' => $paymentUuid]),
            cancelUrl: $this->appendQueryParameters($options['cancel_url'] ?? null, ['payment_uuid' => $paymentUuid]),
        );

        $gateway = $this->manager->driver($options['gateway'] ?? null);

        $payment = Payment::create([
            'uuid' => $paymentUuid,
            'user_id' => $data->userId,
            'user_type' => $data->userType,
            'gateway' => $gateway->name(),
            'amount' => $data->amount,
            'currency' => $data->currency,
            'status' => 'pending',
            'payment_method' => $data->paymentMethod,
            'description' => $data->description,
            'metadata' => $data->metadata,
        ]);

        try {
            $response = $gateway->createPayment($data);

            $payment->update([
                'gateway_payment_id' => $response->gatewayPaymentId,
                'status' => $response->status,
                'paid_at' => $response->isComplete() ? now() : null,
                'metadata' => array_merge($payment->metadata ?? [], $response->metadata),
            ]);

            if ($response->isComplete()) {
                event(new PaymentSucceeded($payment));
            } else {
                event(new PaymentCreated($payment));
            }

            return ['payment' => $payment->fresh(), 'response' => $response];
        } catch (\Throwable $e) {
            $payment->update([
                'status' => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], ['error' => $e->getMessage()]),
            ]);

            event(new PaymentFailed($payment));

            throw new PaymentException(
                message: $e->getMessage(),
                gatewayName: $gateway->name(),
                previous: $e,
            );
        }
    }

    /**
     * Verify a payment after user returns from redirect or JS callback.
     */
    public function verify(Request $request, ?string $gateway = null): Payment
    {
        $driver = $this->manager->driver($gateway);
        $payment = $this->paymentFromRequest($request);

        if ($payment?->gateway_payment_id && ! $request->filled('gateway_payment_id')) {
            $request->merge(['gateway_payment_id' => $payment->gateway_payment_id]);
        }

        $response = $driver->verifyPayment($request);

        if (! $response->gatewayPaymentId) {
            throw new PaymentException('No gateway payment ID in verification response.', $driver->name());
        }

        $payment ??= Payment::where('gateway_payment_id', $response->gatewayPaymentId)->firstOrFail();
        $metadata = array_merge($payment->metadata ?? [], $response->metadata);

        if ($response->gatewayPaymentId !== $payment->gateway_payment_id) {
            $metadata['initial_gateway_payment_id'] = $payment->gateway_payment_id;
        }

        $payment->update([
            'gateway_payment_id' => $response->gatewayPaymentId,
            'status' => $response->status,
            'paid_at' => $response->isComplete() ? now() : $payment->paid_at,
            'metadata' => $metadata,
        ]);

        if ($response->isComplete()) {
            event(new PaymentSucceeded($payment));
        } elseif ($response->isFailed()) {
            event(new PaymentFailed($payment));
        }

        return $payment->fresh();
    }

    protected function appendQueryParameters(?string $url, array $parameters): ?string
    {
        if (! $url) {
            return $url;
        }

        $query = http_build_query($parameters);
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.$query;
    }

    protected function paymentFromRequest(Request $request): ?Payment
    {
        $uuid = $request->get('payment_uuid');

        if (! $uuid) {
            return null;
        }

        return Payment::where('uuid', $uuid)->first();
    }

    /**
     * Refund a payment (full or partial).
     */
    public function refund(int|string $paymentId, ?float $amount = null, string $reason = ''): Refund
    {
        $payment = Payment::findOrFail($paymentId);
        $amount ??= $payment->amount;

        $driver = $this->manager->driver($payment->gateway);

        $refund = Refund::create([
            'payment_id' => $payment->id,
            'amount' => $amount,
            'status' => 'pending',
            'reason' => $reason,
        ]);

        try {
            $result = $driver->refund($payment->gateway_payment_id, $amount, $reason);

            $refund->update([
                'gateway_refund_id' => $result->gatewayRefundId,
                'status' => $result->status,
                'metadata' => $result->metadata,
            ]);

            if ($result->success) {
                $payment->update(['status' => 'refunded']);
                event(new RefundProcessed($refund));
            }

            return $refund->fresh();
        } catch (\Throwable $e) {
            $refund->update([
                'status' => 'failed',
                'metadata' => ['error' => $e->getMessage()],
            ]);

            throw new PaymentException(
                message: $e->getMessage(),
                gatewayName: $payment->gateway,
                previous: $e,
            );
        }
    }
}

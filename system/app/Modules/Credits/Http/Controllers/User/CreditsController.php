<?php

namespace App\Modules\Credits\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\PaymentGateways\Exceptions\PaymentException;
use App\Modules\PaymentGateways\Services\PaymentGatewayManager;
use App\Modules\PaymentGateways\Services\PaymentService;
use App\Modules\PricingPlan\Models\PricingPlan;
use App\Modules\PricingPlan\Services\PricingPlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CreditsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:credits.view'),
        ];
    }

    public function __construct(
        protected CreditLedger $ledger,
        protected PricingPlanService $pricingPlanService,
        protected PaymentService $paymentService,
        protected PaymentGatewayManager $paymentGatewayManager
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $creditAccount = $this->ledger->accountFor($user);

        $transactions = CreditTransaction::query()
            ->forUser($creditAccount->id)
            ->latest()
            ->paginate(20);

        return view('credits::user.index', [
            'balance' => $this->ledger->balance($user),
            'transactions' => $transactions,
            'plans' => $this->pricingPlanService->activePlans(),
            'featuredPlan' => $this->pricingPlanService->featuredPlan(),
        ]);
    }

    public function buy(Request $request): View
    {
        return view('credits::user.buy', [
            'balance' => $this->ledger->balance($request->user()),
            'plans' => $this->pricingPlanService->activePlans(),
            'featuredPlan' => $this->pricingPlanService->featuredPlan(),
        ]);
    }

    public function startCheckout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'string', 'exists:pricing_plans,slug'],
        ]);

        $pricingPlan = PricingPlan::query()
            ->active()
            ->where('slug', $validated['plan'])
            ->firstOrFail();

        return redirect()->route('user.credits.checkout', $pricingPlan->slug);
    }

    public function checkout(Request $request, PricingPlan $pricingPlan): View
    {
        $this->ensurePlanIsPurchasable($pricingPlan);

        return view('credits::user.checkout', [
            'balance' => $this->ledger->balance($request->user()),
            'plan' => $pricingPlan,
            'currency' => payment_gateway_setting('payment_currency', 'USD'),
            'gateways' => $this->gatewayOptions((float) $pricingPlan->price_monthly),
        ]);
    }

    public function completeCheckout(Request $request, PricingPlan $pricingPlan): RedirectResponse
    {
        $this->ensurePlanIsPurchasable($pricingPlan);

        if ((int) $pricingPlan->price_monthly === 0) {
            $alreadyActivated = CreditTransaction::query()
                ->forUser($request->user()->id)
                ->where('reason', 'pricing_plan_purchase')
                ->get()
                ->contains(fn (CreditTransaction $transaction) => ($transaction->metadata['checkout_type'] ?? null) === 'free_plan'
                    && (int) ($transaction->metadata['pricing_plan_id'] ?? 0) === $pricingPlan->id);

            if ($alreadyActivated) {
                return redirect()
                    ->route('user.credits.index')
                    ->with('success', __('This free plan is already active on your account.'));
            }

            $this->ledger->grant($request->user(), (int) $pricingPlan->credits_monthly, 'pricing_plan_purchase', metadata: [
                'pricing_plan_id' => $pricingPlan->id,
                'pricing_plan_slug' => $pricingPlan->slug,
                'pricing_plan_name' => $pricingPlan->name,
                'billing_period' => 'monthly',
                'checkout_type' => 'free_plan',
            ]);

            return redirect()
                ->route('user.credits.index')
                ->with('success', __('Plan activated. :credits credits were added to your balance.', [
                    'credits' => number_format($pricingPlan->credits_monthly),
                ]));
        }

        $gatewayOptions = $this->gatewayOptions((float) $pricingPlan->price_monthly);
        $gatewayNames = array_column($gatewayOptions, 'name');

        $validated = $request->validate([
            'gateway' => ['required', 'string', Rule::in($gatewayNames)],
        ]);

        $selectedGateway = collect($gatewayOptions)->firstWhere('name', $validated['gateway']);
        $manualPayload = [];

        if (($selectedGateway['type'] ?? null) === 'manual') {
            $manualPayload = $request->validate([
                'manual_reference' => ['required', 'string', 'max:255'],
            ]);
        }

        try {
            $gatewayName = $validated['gateway'];
            $gatewayCharge = (float) ($selectedGateway['charge'] ?? 0);
            $payableAmount = (float) ($selectedGateway['total'] ?? $pricingPlan->price_monthly);

            $result = $this->paymentService->charge($payableAmount, payment_gateway_setting('payment_currency', 'USD'), [
                'gateway' => $gatewayName,
                'description' => __(':plan credit plan', ['plan' => $pricingPlan->name]),
                'user_id' => $request->user()->getKey(),
                'user_type' => $request->user()->getMorphClass(),
                'return_url' => route('user.credits.checkout.return', $gatewayName),
                'cancel_url' => route('user.credits.checkout.cancel', $pricingPlan->slug),
                'metadata' => [
                    'context' => 'credits_plan_purchase',
                    'pricing_plan_id' => $pricingPlan->id,
                    'pricing_plan_slug' => $pricingPlan->slug,
                    'pricing_plan_name' => $pricingPlan->name,
                    'credits' => (int) $pricingPlan->credits_monthly,
                    'billing_period' => 'monthly',
                    'base_amount' => (float) $pricingPlan->price_monthly,
                    'gateway_charge' => $gatewayCharge,
                    'total_amount' => $payableAmount,
                    'user_fields' => $manualPayload ? ['reference' => $manualPayload['manual_reference']] : [],
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('user.credits.checkout', $pricingPlan->slug)
                ->with('error', __('Payment could not be started. Please check the gateway settings and try again.'));
        }

        $response = $result['response'];

        if ($response->isFailed()) {
            return redirect()
                ->route('user.credits.checkout', $pricingPlan->slug)
                ->with('error', $response->message ?: __('Payment failed. Please try again or choose another payment method.'));
        }

        if ($response->isRedirect()) {
            return redirect()->away($response->redirectUrl);
        }

        if ($response->requiresClientAction()) {
            $payment = $result['payment'];
            $clientAction = array_merge($response->clientData ?? [], [
                'gateway' => $gatewayName,
                'return_url' => route('user.credits.checkout.return', [
                    'gateway' => $gatewayName,
                    'payment_uuid' => $payment->uuid,
                ]),
            ]);

            return redirect()
                ->route('user.credits.checkout', $pricingPlan->slug)
                ->with('payment_client_action', $clientAction)
                ->with('success', __('Payment created. Complete the gateway action to finish your purchase.'));
        }

        if ($response->isComplete()) {
            return redirect()
                ->route('user.credits.index')
                ->with('success', __('Plan purchased. :credits credits were added to your balance.', [
                    'credits' => number_format($pricingPlan->credits_monthly),
                ]));
        }

        return redirect()
            ->route('user.credits.index')
            ->with('success', __('Payment created. Your credits will be added when the payment is approved.'));
    }

    public function paymentReturn(Request $request, ?string $gateway = null): RedirectResponse
    {
        try {
            $payment = $this->paymentService->verify($request, $gateway);
        } catch (PaymentException $exception) {
            report($exception);

            return redirect()
                ->route('user.credits.index')
                ->with('error', __('We could not verify that payment. If you were charged, contact support with your payment reference.'));
        }

        if ($payment->status === 'completed') {
            return redirect()
                ->route('user.credits.index')
                ->with('success', __('Payment confirmed. Your credits have been added to your balance.'));
        }

        if ($payment->status === 'failed') {
            return redirect()
                ->route('user.credits.index')
                ->with('error', __('Payment failed. No credits were added.'));
        }

        return redirect()
            ->route('user.credits.index')
            ->with('success', __('Payment is still processing. Your credits will be added when it is confirmed.'));
    }

    public function paymentCancel(PricingPlan $pricingPlan): RedirectResponse
    {
        return redirect()
            ->route('user.credits.checkout', $pricingPlan->slug)
            ->with('error', __('Checkout was cancelled. No credits were added.'));
    }

    protected function ensurePlanIsPurchasable(PricingPlan $pricingPlan): void
    {
        abort_unless($pricingPlan->is_active, 404);
    }

    /**
     * @return array<int, array{name: string, label: string, icon: string, type: string, mode: ?string, instructions: ?string, fixed_charge: float, percent_charge: float, charge: float, total: float}>
     */
    protected function gatewayOptions(float $baseAmount = 0): array
    {
        $names = $this->paymentGatewayManager->getEnabledGatewayNames();

        if ($names === []) {
            $names = ['log'];
        }

        return collect($names)
            ->unique()
            ->map(function (string $name) use ($baseAmount): array {
                $driver = $this->paymentGatewayManager->driver($name);
                $config = $driver->getClientConfig();
                $type = $config['type'] ?? ($name === 'log' ? 'development' : 'gateway');
                $fee = $this->gatewayFee($driver->name(), $baseAmount);

                return [
                    'name' => $driver->name(),
                    'label' => $this->gatewayLabel($driver->name()),
                    'icon' => $this->gatewayIcon($driver->name()),
                    'type' => $type,
                    'mode' => $config['mode'] ?? (isset($config['sandbox']) ? ((bool) $config['sandbox'] ? 'sandbox' : 'live') : null),
                    'instructions' => $config['instructions'] ?? null,
                    'fixed_charge' => $fee['fixed'],
                    'percent_charge' => $fee['percent'],
                    'charge' => $fee['charge'],
                    'total' => round($baseAmount + $fee['charge'], 2),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{fixed: float, percent: float, charge: float}
     */
    protected function gatewayFee(string $gateway, float $baseAmount): array
    {
        $fixed = (float) payment_gateway_setting("{$gateway}_fixed_charge", 0);
        $percent = (float) payment_gateway_setting("{$gateway}_percent_charge", 0);
        $percentageAmount = $baseAmount * ($percent / 100);

        return [
            'fixed' => round($fixed, 2),
            'percent' => round($percent, 2),
            'charge' => round(max(0, $fixed + $percentageAmount), 2),
        ];
    }

    protected function gatewayLabel(string $name): string
    {
        return [
            'log' => 'Development gateway',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'razorpay' => 'Razorpay',
            'paystack' => 'Paystack',
            'flutterwave' => 'Flutterwave',
            'mercadopago' => 'Mercado Pago',
            'izipay' => 'Izipay',
            'mollie' => 'Mollie',
            'xendit' => 'Xendit',
            'nowpayments' => 'NOWPayments',
            'coinbasecommerce' => 'Coinbase Commerce',
            'bitpay' => 'BitPay',
        ][$name] ?? Str::headline(str_replace(['-', '_'], ' ', $name));
    }

    protected function gatewayIcon(string $name): string
    {
        return [
            'log' => 'ph-terminal-window',
            'stripe' => 'ph-stripe-logo',
            'paypal' => 'ph-paypal-logo',
            'razorpay' => 'ph-credit-card',
            'paystack' => 'ph-credit-card',
            'flutterwave' => 'ph-waves',
            'mercadopago' => 'ph-wallet',
            'izipay' => 'ph-credit-card',
            'mollie' => 'ph-credit-card',
            'xendit' => 'ph-bank',
            'nowpayments' => 'ph-currency-btc',
            'coinbasecommerce' => 'ph-currency-btc',
            'bitpay' => 'ph-currency-btc',
        ][$name] ?? 'ph-credit-card';
    }
}

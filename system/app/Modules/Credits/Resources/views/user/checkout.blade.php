<x-layouts.user :title="__('Checkout')">
    @php
        $formatMoney = fn (float|int $price) => $price > 0 ? '$'.number_format((float) $price, fmod((float) $price, 1.0) === 0.0 ? 0 : 2) : __('Free');
        $monthlyPrice = (int) $plan->price_monthly;
        $yearlyPrice = (int) $plan->price_yearly;
        $credits = (int) $plan->credits_monthly;
        $perCredit = $monthlyPrice > 0 && $credits > 0 ? '$'.number_format($monthlyPrice / $credits, 3) : null;
        $selectedGateway = $gateways[0] ?? ['charge' => 0, 'total' => $monthlyPrice, 'fixed_charge' => 0, 'percent_charge' => 0];
        $paymentClientAction = session('payment_client_action');
        $clientActionGateway = is_array($paymentClientAction ?? null) ? ($paymentClientAction['gateway'] ?? null) : null;
        $stripeAction = is_array($paymentClientAction ?? null)
            && $clientActionGateway === 'stripe'
            && filled($paymentClientAction['client_secret'] ?? null)
            && filled($paymentClientAction['publishable_key'] ?? null)
                ? $paymentClientAction
                : null;
        $razorpayAction = is_array($paymentClientAction ?? null)
            && $clientActionGateway === 'razorpay'
            && filled($paymentClientAction['order_id'] ?? null)
            && filled($paymentClientAction['key_id'] ?? null)
                ? $paymentClientAction
                : null;
        $izipayAction = is_array($paymentClientAction ?? null)
            && $clientActionGateway === 'izipay'
            && filled($paymentClientAction['form_token'] ?? null)
            && filled($paymentClientAction['public_key'] ?? null)
                ? $paymentClientAction
                : null;
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('user.credits.buy') }}" class="back-link">
                <i class="ph ph-arrow-left" aria-hidden="true"></i>
                {{ __('Back to plans') }}
            </a>
            <h2 class="heading-3 mt-3">{{ __('Checkout') }}</h2>
            <p class="m-text mt-1">
                {{ __('Review your plan and complete the purchase. Credits are added as soon as the payment succeeds.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
        <section class="panel h-fit xl:sticky xl:top-6">
            <div class="panel__head">
                <h3 class="panel__title">{{ __('Plan details') }}</h3>
                @if ($plan->is_featured)
                    <span class="badge badge-accent text-[0.6875rem]">{{ __('Most bought') }}</span>
                @endif
            </div>

            <div class="panel__body">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <article class="kpi">
                        <p class="kpi__label">{{ __('Plan') }}</p>
                        <p class="kpi__value">{{ $plan->name }}</p>
                        @if ($plan->tagline)
                            <p class="kpi__foot">
                                <span class="kpi__note">{{ $plan->tagline }}</span>
                            </p>
                        @endif
                    </article>

                    <article class="kpi">
                        <p class="kpi__label">{{ __('Monthly credits') }}</p>
                        <p class="kpi__value numeric">{{ number_format($credits) }}</p>
                        <p class="kpi__foot">
                            <span class="kpi__note">{{ __('added after payment') }}</span>
                        </p>
                    </article>

                    <article class="kpi">
                        <p class="kpi__label">{{ __('Price') }}</p>
                        <p class="kpi__value numeric">{{ $formatMoney($monthlyPrice) }}</p>
                        <p class="kpi__foot">
                            <span class="kpi__note">{{ __('per month') }}</span>
                        </p>
                    </article>
                </div>

                @if (! empty($plan->features))
                    <div class="mt-6">
                        <h4 class="font-title text-[1rem] font-bold text-title">{{ __('Included') }}</h4>
                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($plan->features as $feature)
                                <div class="flex items-start gap-2 text-[0.875rem] text-body">
                                    <i class="ph ph-check-circle text-success" aria-hidden="true"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-6 border-t border-neutral-100 pt-5">
                    <h4 class="font-title text-[1rem] font-bold text-title">{{ __('Next steps') }}</h4>
                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="flex items-start gap-2 text-[0.875rem] text-body">
                            <span class="f-center h-6 w-6 shrink-0 rounded-full bg-primary text-[0.75rem] font-bold text-neutral-0 numeric">1</span>
                            <span>{{ __('Choose a payment gateway.') }}</span>
                        </div>
                        <div class="flex items-start gap-2 text-[0.875rem] text-body">
                            <span class="f-center h-6 w-6 shrink-0 rounded-full bg-primary text-[0.75rem] font-bold text-neutral-0 numeric">2</span>
                            <span>{{ __('Complete the gateway payment step.') }}</span>
                        </div>
                        <div class="flex items-start gap-2 text-[0.875rem] text-body">
                            <span class="f-center h-6 w-6 shrink-0 rounded-full bg-primary text-[0.75rem] font-bold text-neutral-0 numeric">3</span>
                            <span>{{ __('Credits appear in your wallet after confirmation.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <aside class="panel">
            <div class="panel__head">
                <h3 class="panel__title">{{ __('Order summary') }}</h3>
                <span class="panel__meta">{{ $currency }}</span>
            </div>

            <div class="panel__body">
                <div class="space-y-2.5 text-[0.8125rem]">
                    <div class="f-between">
                        <span class="text-body">{{ __('Current balance') }}</span>
                        <span class="font-medium text-title numeric">{{ number_format($balance) }}</span>
                    </div>
                    <div class="f-between">
                        <span class="text-body">{{ __('Credits to add') }}</span>
                        <span class="font-medium text-success numeric">+{{ number_format($credits) }}</span>
                    </div>
                </div>

                <div class="f-between mt-4 rounded-lg bg-primary/5 px-3 py-3">
                    <span class="text-[0.8125rem] font-semibold text-title">{{ __('New balance after payment') }}</span>
                    <span class="font-title text-[1.25rem] leading-none font-bold text-primary numeric">{{ number_format($balance + $credits) }}</span>
                </div>

                <div class="mt-4 space-y-2.5 border-t border-neutral-100 pt-4 text-[0.8125rem]">
                    <div class="f-between">
                        <span class="text-body">{{ __('Monthly price') }}</span>
                        <span class="font-medium text-title numeric">{{ $formatMoney($monthlyPrice) }}</span>
                    </div>
                    <div class="f-between">
                        <span class="text-body">{{ __('Gateway charge') }}</span>
                        <span class="font-medium text-title numeric" data-gateway-charge>{{ $formatMoney($selectedGateway['charge']) }}</span>
                    </div>
                    @if ($yearlyPrice > 0)
                        <div class="f-between">
                            <span class="text-body">{{ __('Yearly option') }}</span>
                            <span class="font-medium text-title numeric">{{ $formatMoney($yearlyPrice) }}</span>
                        </div>
                    @endif
                    @if ($perCredit)
                        <div class="f-between">
                            <span class="text-body">{{ __('Effective rate') }}</span>
                            <span class="font-medium text-title numeric">{{ $perCredit }} {{ __('per credit') }}</span>
                        </div>
                    @endif
                </div>

                <div class="f-between mt-4 rounded-lg bg-accent/10 px-3 py-3">
                    <span class="text-[0.8125rem] font-semibold text-title">{{ __('Total payable') }}</span>
                    <span class="font-title text-[1.35rem] leading-none font-bold text-title numeric" data-gateway-total>{{ $formatMoney($selectedGateway['total']) }}</span>
                </div>

                @if ($stripeAction)
                    <div class="mt-6 rounded-lg border border-primary/20 bg-primary/5 p-4" data-stripe-payment>
                        <div class="flex items-start gap-3">
                            <span class="f-center h-9 w-9 shrink-0 rounded-lg bg-primary text-neutral-0">
                                <i class="ph ph-stripe-logo text-[1.35rem]" aria-hidden="true"></i>
                            </span>
                            <div>
                                <h4 class="font-title text-[1rem] font-bold text-title">{{ __('Complete Stripe payment') }}</h4>
                                <p class="mt-1 text-[0.8125rem] leading-relaxed text-body">
                                    {{ __('Enter a card to confirm the secure Stripe payment intent already created for this order.') }}
                                </p>
                            </div>
                        </div>

                        <div id="stripe-card-element" class="mt-4 rounded-lg border border-neutral-200 bg-neutral-0 px-3 py-3"></div>
                        <p id="stripe-card-error" class="mt-2 hidden text-[0.8125rem] font-medium text-error"></p>

                        <button type="button" id="stripe-confirm-button" class="btn btn-primary mt-4 w-full">
                            <span class="btn__label">
                                <span>{{ __('Pay with Stripe') }}</span>
                                <span aria-hidden="true">{{ __('Pay with Stripe') }}</span>
                            </span>
                            <i class="ph ph-lock-key"></i>
                        </button>
                    </div>
                @endif

                @if ($razorpayAction)
                    <div class="mt-6 rounded-lg border border-primary/20 bg-primary/5 p-4" data-razorpay-payment>
                        <div class="flex items-start gap-3">
                            <span class="f-center h-9 w-9 shrink-0 rounded-lg bg-primary text-neutral-0">
                                <i class="ph ph-credit-card text-[1.35rem]" aria-hidden="true"></i>
                            </span>
                            <div>
                                <h4 class="font-title text-[1rem] font-bold text-title">{{ __('Complete Razorpay payment') }}</h4>
                                <p class="mt-1 text-[0.8125rem] leading-relaxed text-body">
                                    {{ __('Open the secure Razorpay checkout and finish the authorization to confirm this order.') }}
                                </p>
                            </div>
                        </div>

                        <p id="razorpay-error" class="mt-3 hidden text-[0.8125rem] font-medium text-error"></p>

                        <button type="button" id="razorpay-confirm-button" class="btn btn-primary mt-4 w-full">
                            <span class="btn__label">
                                <span>{{ __('Pay with Razorpay') }}</span>
                                <span aria-hidden="true">{{ __('Pay with Razorpay') }}</span>
                            </span>
                            <i class="ph ph-lock-key"></i>
                        </button>
                    </div>
                @endif

                @if ($izipayAction)
                    <div class="mt-6 rounded-lg border border-primary/20 bg-primary/5 p-4" data-izipay-payment>
                        <div class="flex items-start gap-3">
                            <span class="f-center h-9 w-9 shrink-0 rounded-lg bg-primary text-neutral-0">
                                <i class="ph ph-credit-card text-[1.35rem]" aria-hidden="true"></i>
                            </span>
                            <div>
                                <h4 class="font-title text-[1rem] font-bold text-title">{{ __('Complete Izipay payment') }}</h4>
                                <p class="mt-1 text-[0.8125rem] leading-relaxed text-body">
                                    {{ __('Use the secure Izipay form below to finish this order.') }}
                                </p>
                            </div>
                        </div>

                        <div id="izipay-payment-form" class="kr-embedded mt-4" kr-form-token="{{ $izipayAction['form_token'] }}"></div>
                        <p id="izipay-error" class="mt-2 hidden text-[0.8125rem] font-medium text-error"></p>
                    </div>
                @endif

                <form action="{{ route('user.credits.checkout.complete', $plan->slug) }}" method="post" class="mt-6">
                    @csrf

                    <div class="mb-5">
                        <h4 class="font-title text-[1rem] font-bold text-title">{{ __('Payment method') }}</h4>
                        <div class="mt-3 grid gap-2.5">
                            @foreach ($gateways as $gateway)
                                <label class="gateway">
                                    <input
                                        type="radio"
                                        name="gateway"
                                        value="{{ $gateway['name'] }}"
                                        class="gateway__radio"
                                        data-charge="{{ number_format($gateway['charge'], 2, '.', '') }}"
                                        data-total="{{ number_format($gateway['total'], 2, '.', '') }}"
                                        data-fixed-charge="{{ number_format($gateway['fixed_charge'], 2, '.', '') }}"
                                        data-percent-charge="{{ number_format($gateway['percent_charge'], 2, '.', '') }}"
                                        @checked($loop->first)
                                    />
                                    <span class="gateway__body">
                                        <span class="flex items-start justify-between gap-3">
                                            <span class="flex min-w-0 items-center gap-2">
                                                <i class="ph {{ $gateway['icon'] }} text-[1.25rem] text-primary" aria-hidden="true"></i>
                                                <span class="font-title text-[0.9375rem] font-bold text-title">{{ $gateway['label'] }}</span>
                                            </span>
                                            @if ($gateway['mode'])
                                                <span class="badge text-[0.6875rem]">{{ __(ucfirst($gateway['mode'])) }}</span>
                                            @endif
                                        </span>

                                        <span class="mt-2 block text-[0.8125rem] font-semibold text-title numeric">
                                            {{ __('Charge') }}: {{ $formatMoney($gateway['charge']) }}
                                            @if ($gateway['fixed_charge'] > 0 || $gateway['percent_charge'] > 0)
                                                <span class="font-normal text-neutral-500">
                                                    ({{ $formatMoney($gateway['fixed_charge']) }} + {{ number_format($gateway['percent_charge'], 2) }}%)
                                                </span>
                                            @endif
                                        </span>

                                        <span class="mt-1.5 block text-[0.8125rem] text-body">
                                            @if ($gateway['type'] === 'manual')
                                                {{ __('Manual approval after you submit the payment reference.') }}
                                            @elseif ($gateway['type'] === 'development')
                                                {{ __('Completes immediately for local testing.') }}
                                            @else
                                                {{ __('You may be redirected or asked to complete a secure gateway step.') }}
                                            @endif
                                        </span>

                                        @if ($gateway['instructions'])
                                            <span class="mt-3 block rounded-md bg-neutral-50 p-3 text-[0.8125rem] leading-relaxed text-body">
                                                {{ $gateway['instructions'] }}
                                            </span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error('gateway')
                            <p class="mt-2 text-[0.8125rem] font-medium text-error">{{ $message }}</p>
                        @enderror

                        <div class="mt-4">
                            <label for="manual_reference" class="mb-1 block text-[0.8125rem] font-semibold text-title">
                                {{ __('Manual payment reference') }}
                            </label>
                            <input
                                id="manual_reference"
                                name="manual_reference"
                                type="text"
                                value="{{ old('manual_reference') }}"
                                class="form-input"
                                placeholder="{{ __('Transaction ID, receipt number, or sender account') }}"
                            />
                            <p class="mt-1 text-[0.75rem] text-neutral-500">
                                {{ __('Required only when you choose a manual gateway.') }}
                            </p>
                            @error('manual_reference')
                                <p class="mt-2 text-[0.8125rem] font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-accent w-full">
                        <span class="btn__label">
                            <span>{{ __('Continue checkout') }}</span>
                            <span aria-hidden="true">{{ __('Continue checkout') }}</span>
                        </span>
                        <i class="ph ph-credit-card"></i>
                    </button>
                </form>

                <p class="packs__note mt-4">
                    <i class="ph ph-lock-key" aria-hidden="true"></i>
                    <span>{{ __('Payment is processed by the gateway you choose above.') }}</span>
                </p>
            </div>
        </aside>
    </div>

    @if ($izipayAction)
        <link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.css">
    @endif

    @if ($stripeAction)
        @push('scripts')
            <script src="https://js.stripe.com/v3/"></script>
        @endpush
    @endif

    @if ($razorpayAction)
        @push('scripts')
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        @endpush
    @endif

    @if ($izipayAction)
        @push('scripts')
            <script src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"></script>
        @endpush
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chargeEl = document.querySelector('[data-gateway-charge]');
            const totalEl = document.querySelector('[data-gateway-total]');
            const inputs = document.querySelectorAll('input[name="gateway"][data-charge][data-total]');

            const money = function (value) {
                const amount = Number.parseFloat(value || '0');

                if (!amount) {
                    return '{{ __('Free') }}';
                }

                return '$' + amount.toLocaleString(undefined, {
                    minimumFractionDigits: Number.isInteger(amount) ? 0 : 2,
                    maximumFractionDigits: 2,
                });
            };

            const syncSummary = function () {
                const selected = document.querySelector('input[name="gateway"]:checked[data-charge][data-total]');

                if (!selected) {
                    return;
                }

                if (chargeEl) {
                    chargeEl.textContent = money(selected.dataset.charge);
                }

                if (totalEl) {
                    totalEl.textContent = money(selected.dataset.total);
                }
            };

            inputs.forEach(function (input) {
                input.addEventListener('change', syncSummary);
            });

            syncSummary();

            @if ($stripeAction)
                const stripe = window.Stripe ? window.Stripe(@js($stripeAction['publishable_key'])) : null;
                const cardTarget = document.getElementById('stripe-card-element');
                const confirmButton = document.getElementById('stripe-confirm-button');
                const errorEl = document.getElementById('stripe-card-error');
                const returnUrl = @js($stripeAction['return_url'] ?? route('user.credits.checkout.return', 'stripe'));
                const clientSecret = @js($stripeAction['client_secret']);

                if (stripe && cardTarget && confirmButton) {
                    const elements = stripe.elements();
                    const card = elements.create('card', {
                        hidePostalCode: true,
                        style: {
                            base: {
                                color: '#111827',
                                fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif',
                                fontSize: '15px',
                                '::placeholder': {
                                    color: '#9ca3af',
                                },
                            },
                            invalid: {
                                color: '#dc2626',
                            },
                        },
                    });

                    card.mount(cardTarget);

                    const showError = function (message) {
                        if (!errorEl) {
                            return;
                        }

                        errorEl.textContent = message || '';
                        errorEl.classList.toggle('hidden', !message);
                    };

                    card.on('change', function (event) {
                        showError(event.error ? event.error.message : '');
                    });

                    confirmButton.addEventListener('click', async function () {
                        confirmButton.disabled = true;
                        confirmButton.classList.add('opacity-75');
                        showError('');

                        const result = await stripe.confirmCardPayment(clientSecret, {
                            payment_method: {
                                card: card,
                            },
                        });

                        if (result.error) {
                            showError(result.error.message || '{{ __('Stripe could not confirm the payment.') }}');
                            confirmButton.disabled = false;
                            confirmButton.classList.remove('opacity-75');

                            return;
                        }

                        const paymentIntent = result.paymentIntent;

                        if (paymentIntent && paymentIntent.id) {
                            const verifiedUrl = new URL(returnUrl, window.location.origin);
                            verifiedUrl.searchParams.set('payment_intent', paymentIntent.id);
                            window.location.href = verifiedUrl.toString();

                            return;
                        }

                        showError('{{ __('Stripe did not return a payment reference. Please try again.') }}');
                        confirmButton.disabled = false;
                        confirmButton.classList.remove('opacity-75');
                    });
                } else if (errorEl) {
                    errorEl.textContent = '{{ __('Stripe.js could not be loaded. Please refresh the page and try again.') }}';
                    errorEl.classList.remove('hidden');
                }
            @endif

            @if ($razorpayAction)
                const razorpayButton = document.getElementById('razorpay-confirm-button');
                const razorpayError = document.getElementById('razorpay-error');
                const razorpayReturnUrl = @js($razorpayAction['return_url'] ?? route('user.credits.checkout.return', 'razorpay'));

                const showRazorpayError = function (message) {
                    if (!razorpayError) {
                        return;
                    }

                    razorpayError.textContent = message || '';
                    razorpayError.classList.toggle('hidden', !message);
                };

                if (window.Razorpay && razorpayButton) {
                    const razorpayCheckout = new window.Razorpay({
                        key: @js($razorpayAction['key_id']),
                        amount: @js($razorpayAction['amount'] ?? null),
                        currency: @js($razorpayAction['currency'] ?? $currency),
                        name: @js($razorpayAction['name'] ?? config('app.name')),
                        description: @js($razorpayAction['description'] ?? __('Credit plan purchase')),
                        order_id: @js($razorpayAction['order_id']),
                        handler: function (response) {
                            const verifiedUrl = new URL(razorpayReturnUrl, window.location.origin);
                            verifiedUrl.searchParams.set('razorpay_payment_id', response.razorpay_payment_id || '');
                            verifiedUrl.searchParams.set('razorpay_order_id', response.razorpay_order_id || '');
                            verifiedUrl.searchParams.set('razorpay_signature', response.razorpay_signature || '');
                            window.location.href = verifiedUrl.toString();
                        },
                        modal: {
                            ondismiss: function () {
                                showRazorpayError('{{ __('Razorpay checkout was closed before payment confirmation.') }}');
                            },
                        },
                    });

                    razorpayButton.addEventListener('click', function (event) {
                        event.preventDefault();
                        showRazorpayError('');
                        razorpayCheckout.open();
                    });
                } else {
                    showRazorpayError('{{ __('Razorpay checkout could not be loaded. Please refresh the page and try again.') }}');
                }
            @endif

            @if ($izipayAction)
                const izipayError = document.getElementById('izipay-error');
                const showIzipayError = function (message) {
                    if (!izipayError) {
                        return;
                    }

                    izipayError.textContent = message || '';
                    izipayError.classList.toggle('hidden', !message);
                };

                if (window.KR) {
                    window.KR.setFormConfig({
                        formToken: @js($izipayAction['form_token']),
                        'kr-public-key': @js($izipayAction['public_key']),
                        'kr-post-url-success': @js($izipayAction['return_url'] ?? route('user.credits.checkout.return', 'izipay')),
                        'kr-post-url-refused': @js($izipayAction['return_url'] ?? route('user.credits.checkout.return', 'izipay')),
                        'kr-language': @js(str_replace('_', '-', app()->getLocale())),
                    }).then(function () {
                        return window.KR.renderElements('#izipay-payment-form');
                    }).catch(function (error) {
                        showIzipayError(error && error.message ? error.message : '{{ __('Izipay payment form could not be loaded.') }}');
                    });
                } else {
                    showIzipayError('{{ __('Izipay payment form could not be loaded. Please refresh the page and try again.') }}');
                }
            @endif
        });
    </script>
</x-layouts.user>

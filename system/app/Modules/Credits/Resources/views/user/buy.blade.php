<x-layouts.user :title="__('Buy credits')">
    @php
        $selectedPlanId = $featuredPlan?->id ?? $plans->first()?->id;
        $formatPrice = fn (int $price) => $price > 0 ? '$'.number_format($price) : __('Free');
    @endphp

    <div class="mb-6">
        <h2 class="heading-3">{{ __('Choose a credit plan') }}</h2>
        <p class="m-text mt-1">
            {{ __('Plans are managed from Pricing Plans and reflect the current monthly credit allowances.') }}
        </p>
    </div>

    <section class="bal">
        <div class="min-w-0">
            <p class="text-[0.75rem] font-semibold tracking-[0.08em] text-body uppercase">
                {{ __('Credits remaining') }}
            </p>
            <p class="mt-1 font-title text-[1.75rem] leading-none font-bold text-title numeric">{{ number_format($balance) }}</p>
        </div>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Available plans') }}</h3>
            <span class="panel__meta">
                {{ __('Monthly credits from active pricing plans') }}
            </span>
        </div>

        <form action="{{ route('user.credits.checkout.start') }}" method="post">
            @csrf

            <div class="packs">
                @forelse ($plans as $plan)
                    @php
                        $monthlyPrice = (int) $plan->price_monthly;
                        $credits = (int) $plan->credits_monthly;
                        $perCredit = $monthlyPrice > 0 && $credits > 0
                            ? '$'.number_format($monthlyPrice / $credits, 3)
                            : null;
                    @endphp

                    <label class="pack">
                        <input type="radio" name="plan" value="{{ $plan->slug }}" class="pack__radio" @checked($plan->id === $selectedPlanId) />
                        <span class="pack__body">
                            @if ($plan->is_featured)
                                <span class="badge badge-accent mb-2 self-start text-[0.6875rem]">{{ __('Most bought') }}</span>
                            @endif

                            @if ($plan->icon)
                                <i class="ph {{ $plan->icon }} mb-3 text-[1.5rem] text-primary" aria-hidden="true"></i>
                            @endif

                            <span class="font-title text-[1rem] font-bold text-title">{{ $plan->name }}</span>

                            @if ($plan->tagline)
                                <span class="mt-1 text-[0.8125rem] leading-snug text-body">{{ $plan->tagline }}</span>
                            @endif

                            <span class="mt-3 font-title text-[1.5rem] leading-none font-bold text-title numeric">{{ number_format($credits) }}</span>
                            <span class="mt-1 text-[0.875rem] text-body">{{ __('credits / month') }}</span>
                            <span class="mt-3 font-title text-[1.125rem] font-bold text-title numeric">{{ $formatPrice($monthlyPrice) }}<span class="text-[0.8125rem] font-medium text-body">{{ __(' / mo') }}</span></span>

                            @if ($plan->price_yearly > 0)
                                <span class="mt-0.5 text-[0.8125rem] text-neutral-500 numeric">{{ $formatPrice((int) $plan->price_yearly) }} {{ __('yearly') }}</span>
                            @endif

                            @if ($perCredit)
                                <span class="mt-0.5 text-[0.8125rem] text-neutral-500 numeric">{{ $perCredit }} {{ __('per credit') }}</span>
                            @endif

                            @if (! empty($plan->features))
                                <span class="mt-4 grid gap-1.5 text-[0.8125rem] text-body">
                                    @foreach (array_slice($plan->features, 0, 3) as $feature)
                                        <span class="flex items-start gap-2">
                                            <i class="ph ph-check text-success" aria-hidden="true"></i>
                                            <span>{{ $feature }}</span>
                                        </span>
                                    @endforeach
                                </span>
                            @endif
                        </span>
                    </label>
                @empty
                    <div class="no-results">
                        <p class="no-results__title">{{ __('No active pricing plans') }}</p>
                        <p class="m-text mt-1">{{ __('Create or activate a pricing plan in the admin panel to show billing options here.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="packs__foot">
                <p class="packs__note">
                    <i class="ph ph-info" aria-hidden="true"></i>
                    <span>
                        {{ __('A credit is spent when a business is enriched. Checkout uses the active payment gateway configured in the admin panel.') }}
                    </span>
                </p>

                <button type="submit" class="btn btn-accent btn-sm shrink-0" @disabled($plans->isEmpty())>
                    <span class="btn__label">
                        <span>{{ __('Checkout') }}</span>
                        <span aria-hidden="true">{{ __('Checkout') }}</span>
                    </span>
                    <i class="ph ph-arrow-right"></i>
                </button>
            </div>
        </form>
    </section>
</x-layouts.user>

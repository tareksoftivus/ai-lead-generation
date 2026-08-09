<x-layouts.user :title="__('Buy credits')">
    <div class="mb-6">
        <h2 class="heading-3">{{ __('Buy credits') }}</h2>
        <p class="m-text mt-1">
            {{ __('Top up your balance to keep enriching businesses.') }}
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
            <h3 class="panel__title">{{ __('Top up now') }}</h3>
            <span class="panel__meta">
                {{ __('One-off · added to your balance immediately') }}
            </span>
        </div>

        <form action="#" method="post">
            <div class="packs">
                <label class="pack">
                    <input type="radio" name="pack" value="500" class="pack__radio" />
                    <span class="pack__body">
                        <span class="font-title text-[1.5rem] leading-none font-bold text-title numeric">500</span>
                        <span class="mt-1 text-[0.875rem] text-body">{{ __('credits') }}</span>
                        <span class="mt-3 font-title text-[1.125rem] font-bold text-title numeric">$29</span>
                        <span class="mt-0.5 text-[0.8125rem] text-neutral-500 numeric">$0.058 {{ __('per credit') }}</span>
                    </span>
                </label>

                <label class="pack">
                    <input type="radio" name="pack" value="2000" class="pack__radio" checked />
                    <span class="pack__body">
                        <span class="badge badge-accent mb-2 self-start text-[0.6875rem]">{{ __('Most bought') }}</span>
                        <span class="font-title text-[1.5rem] leading-none font-bold text-title numeric">2,000</span>
                        <span class="mt-1 text-[0.875rem] text-body">{{ __('credits') }}</span>
                        <span class="mt-3 font-title text-[1.125rem] font-bold text-title numeric">$99</span>
                        <span class="mt-0.5 text-[0.8125rem] text-neutral-500 numeric">$0.050 {{ __('per credit') }}</span>
                    </span>
                </label>

                <label class="pack">
                    <input type="radio" name="pack" value="10000" class="pack__radio" />
                    <span class="pack__body">
                        <span class="font-title text-[1.5rem] leading-none font-bold text-title numeric">10,000</span>
                        <span class="mt-1 text-[0.875rem] text-body">{{ __('credits') }}</span>
                        <span class="mt-3 font-title text-[1.125rem] font-bold text-title numeric">$449</span>
                        <span class="mt-0.5 text-[0.8125rem] text-neutral-500 numeric">$0.045 {{ __('per credit') }}</span>
                    </span>
                </label>
            </div>

            <div class="packs__foot">
                <p class="packs__note">
                    <i class="ph ph-info" aria-hidden="true"></i>
                    <span>
                        {{ __('A credit is spent when a business is enriched. Payment processing is not yet available — this is a preview of the top-up flow.') }}
                    </span>
                </p>

                <button type="submit" class="btn btn-accent btn-sm shrink-0" disabled>
                    <span class="btn__label">
                        <span>{{ __('Coming soon') }}</span>
                        <span aria-hidden="true">{{ __('Coming soon') }}</span>
                    </span>
                </button>
            </div>
        </form>
    </section>
</x-layouts.user>

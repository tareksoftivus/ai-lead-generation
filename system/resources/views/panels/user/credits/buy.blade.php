<x-layouts.user :title="__('Buy credits')">
    <div class="mb-6">
        <h2 class="heading-3">{{ __('Buy credits') }}</h2>
        <p class="m-text mt-1">
            {{ __('Top up now, or change the plan your monthly allowance comes from.') }}
        </p>
    </div>

    <section class="bal">
        <div class="min-w-0">
            <p class="text-[0.75rem] font-semibold tracking-[0.08em] text-body uppercase">
                {{ __('Credits remaining') }}
            </p>
            <p class="mt-1 font-title text-[1.75rem] leading-none font-bold text-title numeric">2,480</p>
            <p class="mt-1.5 text-[0.875rem] text-body">
                {{ __('of') }} <span class="numeric">5,000</span> {{ __('this month') }}
            </p>
        </div>

        <div class="min-w-0">
            <p class="text-[0.75rem] font-semibold tracking-[0.08em] text-body uppercase">
                {{ __('Your plan') }}
            </p>
            <p class="mt-1 font-title text-[1.75rem] leading-none font-bold text-title bal__val--plan">
                {{ __('Growth') }}
                <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Current') }}</span>
            </p>
            <p class="mt-1.5 text-[0.875rem] text-body">
                <span class="numeric">$89</span>/month · {{ __('renews') }}
                <time datetime="2026-08-01">1 {{ __('Aug') }}</time>
            </p>
        </div>

        <p class="bal__roll">
            <i class="ph ph-arrow-clockwise" aria-hidden="true"></i>
            <span>
                <strong class="text-title">{{ __('Credits roll over.') }}</strong>
                {{ __('Anything you do not spend this month is still there next month, on top of your new allowance.') }}
            </span>
        </p>
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
                        {{ __('A credit is spent when a business is enriched, and returned if no contact is found. This is a card charge, not a search.') }}
                    </span>
                </p>

                <button type="submit" class="btn btn-accent btn-sm shrink-0">
                    <span class="btn__label">
                        <span>{{ __('Continue to payment') }}</span>
                        <span aria-hidden="true">{{ __('Continue to payment') }}</span>
                    </span>
                    <i class="ph ph-arrow-right"></i>
                </button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Or change your plan') }}</h3>
            <a href="#" class="panel__link">
                {{ __('Full comparison') }}
                <i class="ph ph-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="tiers">
            <article class="tier">
                <p class="tier__name">{{ __('Starter') }}</p>
                <p class="tier__price">
                    <span class="font-title text-[1.5rem] leading-none font-bold text-title numeric">$29</span>
                    <span class="text-[0.8125rem] text-body">/month</span>
                </p>
                <p class="mt-1.5 text-[0.875rem] text-body">
                    <span class="numeric">1,000</span> {{ __('credits a month') }}
                </p>
                <ul class="tier__list">
                    <li>
                        <i class="ph ph-check" aria-hidden="true"></i>
                        {{ __('Everything in the app') }}
                    </li>
                    <li>
                        <i class="ph ph-check" aria-hidden="true"></i>
                        {{ __('Unlimited team members') }}
                    </li>
                    <li class="tier__item--off">
                        <i class="ph ph-minus" aria-hidden="true"></i>
                        {{ __('No REST API') }}
                    </li>
                </ul>
                <button type="button" class="btn btn-outline btn-sm w-full" data-confirm
                        data-confirm-title="{{ __('Move to Starter?') }}"
                        data-confirm-body="{{ __('From 1 Aug your monthly allowance drops from 5,000 to 1,000 credits, and API access ends. Your current 2,480 credits are not affected — they roll over.') }}"
                        data-confirm-label="{{ __('Move to Starter') }}" data-id="starter">
                    <span class="btn__label">
                        <span>{{ __('Downgrade') }}</span>
                        <span aria-hidden="true">{{ __('Downgrade') }}</span>
                    </span>
                </button>
            </article>

            <article class="tier tier--current">
                <p class="tier__name">
                    {{ __('Growth') }}
                    <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('Current') }}</span>
                </p>
                <p class="tier__price">
                    <span class="font-title text-[1.5rem] leading-none font-bold text-title numeric">$89</span>
                    <span class="text-[0.8125rem] text-body">/month</span>
                </p>
                <p class="mt-1.5 text-[0.875rem] text-body">
                    <span class="numeric">5,000</span> {{ __('credits a month') }}
                </p>
                <ul class="tier__list">
                    <li>
                        <i class="ph ph-check" aria-hidden="true"></i>
                        {{ __('Everything in the app') }}
                    </li>
                    <li>
                        <i class="ph ph-check" aria-hidden="true"></i>
                        {{ __('Unlimited team members') }}
                    </li>
                    <li class="tier__item--off">
                        <i class="ph ph-minus" aria-hidden="true"></i>
                        {{ __('No REST API') }}
                    </li>
                </ul>
                <p class="tier__on">
                    <i class="ph ph-check-circle" aria-hidden="true"></i>
                    {{ __('You are on this plan') }}
                </p>
            </article>

            <article class="tier">
                <p class="tier__name">{{ __('Scale') }}</p>
                <p class="tier__price">
                    <span class="font-title text-[1.5rem] leading-none font-bold text-title numeric">$249</span>
                    <span class="text-[0.8125rem] text-body">/month</span>
                </p>
                <p class="mt-1.5 text-[0.875rem] text-body">
                    <span class="numeric">20,000</span> {{ __('credits a month') }}
                </p>
                <ul class="tier__list">
                    <li>
                        <i class="ph ph-check" aria-hidden="true"></i>
                        {{ __('Everything in the app') }}
                    </li>
                    <li>
                        <i class="ph ph-check" aria-hidden="true"></i>
                        {{ __('Unlimited team members') }}
                    </li>
                    <li>
                        <i class="ph ph-check" aria-hidden="true"></i>
                        {{ __('REST API and webhooks') }}
                    </li>
                </ul>
                <a href="{{ route('user.api.keys') }}" class="btn btn-accent btn-sm w-full">
                    <span class="btn__label">
                        <span>{{ __('Upgrade') }}</span>
                        <span aria-hidden="true">{{ __('Upgrade') }}</span>
                    </span>
                    <i class="ph ph-arrow-up-right"></i>
                </a>
            </article>
        </div>

        <p class="tiers__note">
            <i class="ph ph-info" aria-hidden="true"></i>
            <span>
                {{ __('Upgrades start immediately and are charged pro rata. Downgrades take effect at your next renewal, so you keep the allowance you have already paid for.') }}
            </span>
        </p>
    </section>

    @push('modals')
        <div id="confirmDialog" class="modal" role="dialog" aria-modal="true" aria-hidden="true">
            <div class="modal__backdrop"></div>
            <div class="modal__panel max-w-md p-6">
                <h2 class="heading-3" data-confirm-title-target>{{ __('Are you sure?') }}</h2>
                <p class="m-text mt-2" data-confirm-body-target>{{ __('This action cannot be undone.') }}</p>
                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button" class="btn btn-outline" data-confirm-cancel>
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn confirm-accept" data-confirm-accept>
                        {{ __('Confirm') }}
                    </button>
                </div>
            </div>
        </div>
    @endpush
</x-layouts.user>
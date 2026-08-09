<x-layouts.user :title="__('Credits & billing')">
    @php
        $formatPrice = fn (int $price) => $price > 0 ? '$'.number_format($price) : __('Free');
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Credit usage') }}</h2>
            <p class="m-text mt-1">
                {{ __('Every credit this account has spent, and every one granted. Searching is free — credits are spent when a business is enriched.') }}
            </p>
        </div>

        <a href="{{ route('user.credits.buy') }}" class="btn btn-accent btn-sm shrink-0">
            <span class="btn__label">
                <span>{{ __('Buy credits') }}</span>
                <span aria-hidden="true">{{ __('Buy credits') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </a>
    </div>

    <div class="kpis">
        <article class="kpi">
            <p class="kpi__label">{{ __('Credits remaining') }}</p>
            <p class="kpi__value numeric">{{ number_format($balance) }}</p>
            <p class="kpi__foot">
                <a href="{{ route('user.credits.buy') }}" class="kpi__link">{{ __('Buy more') }}</a>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Spent') }}</p>
            <p class="kpi__value numeric">{{ number_format(abs($transactions->where('type', 'spend')->sum('amount'))) }}</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    {{ __('across') }} <span class="numeric">{{ $transactions->where('type', 'spend')->count() }}</span> {{ __('transactions') }}
                </span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Granted') }}</p>
            <p class="kpi__value numeric">{{ number_format($transactions->whereIn('type', ['grant', 'refund', 'purchase'])->sum('amount')) }}</p>
            <p class="kpi__foot">
                <span class="kpi__note">{{ __('starter credits + top-ups') }}</span>
            </p>
        </article>
    </div>

    @if ($plans->isNotEmpty())
        <section class="panel">
            <div class="panel__head">
                <h3 class="panel__title">{{ __('Current credit plans') }}</h3>
                <span class="panel__meta">
                    <span class="numeric">{{ $plans->count() }}</span> {{ __('active') }}
                </span>
            </div>

            <div class="packs">
                @foreach ($plans as $plan)
                    <article class="pack">
                        <div class="pack__body">
                            @if ($plan->is_featured)
                                <span class="badge badge-accent mb-2 self-start text-[0.6875rem]">{{ __('Most bought') }}</span>
                            @endif

                            <span class="font-title text-[1rem] font-bold text-title">{{ $plan->name }}</span>

                            @if ($plan->tagline)
                                <span class="mt-1 text-[0.8125rem] leading-snug text-body">{{ $plan->tagline }}</span>
                            @endif

                            <span class="mt-3 font-title text-[1.375rem] leading-none font-bold text-title numeric">{{ number_format($plan->credits_monthly) }}</span>
                            <span class="mt-1 text-[0.875rem] text-body">{{ __('credits / month') }}</span>
                            <span class="mt-3 font-title text-[1.125rem] font-bold text-title numeric">{{ $formatPrice((int) $plan->price_monthly) }}<span class="text-[0.8125rem] font-medium text-body">{{ __(' / mo') }}</span></span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="panel" data-list>
        <div class="panel__head">
            <h3 class="panel__title">{{ __('Itemised usage') }}</h3>
            <span class="panel__meta">
                <span class="numeric">{{ $transactions->total() }}</span> {{ __('entries') }}
            </span>
        </div>

        <div class="tbl-wrap">
            <table class="d-table d-table--cards" data-list-table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('Action') }}</th>
                        <th scope="col">{{ __('When') }}</th>
                        <th scope="col" class="text-right">{{ __('Credits') }}</th>
                        <th scope="col" class="text-right">{{ __('Balance after') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td data-card-title>
                                <span class="ledger__act {{ $transaction->amount >= 0 ? 'ledger__act--return' : '' }}">
                                    <i class="ph {{ $transaction->amount >= 0 ? 'ph-arrow-u-down-left' : 'ph-users-three' }}" aria-hidden="true"></i>
                                    {{ __(ucfirst(str_replace('_', ' ', $transaction->reason ?? $transaction->type))) }}
                                </span>
                            </td>
                            <td data-label="{{ __('When') }}" class="d-table__muted whitespace-nowrap">
                                <time datetime="{{ $transaction->created_at->toDateString() }}">{{ $transaction->created_at->format('d M Y') }}</time>
                            </td>
                            <td data-label="{{ __('Credits') }}" class="numeric text-right {{ $transaction->amount >= 0 ? 'font-semibold text-success' : '' }}">
                                {{ $transaction->amount >= 0 ? '+' : '' }}{{ $transaction->amount }}
                            </td>
                            <td data-label="{{ __('Balance after') }}" class="numeric text-right">{{ number_format($transaction->balance_after) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <p class="no-results__title">{{ __('No credit activity yet') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ledger__total">
            <p class="font-title text-[0.9375rem] font-bold text-title">{{ __('Remaining') }}</p>
            <p class="font-title text-[1.125rem] font-bold text-title numeric">{{ number_format($balance) }}</p>
        </div>
    </div>

    <p class="ledger__note">
        <i class="ph ph-info" aria-hidden="true"></i>
        <span>
            {{ __('Running a search and viewing results costs nothing — a credit is spent only when a business is enriched. Re-opening a lead and exporting are always free.') }}
        </span>
    </p>
</x-layouts.user>

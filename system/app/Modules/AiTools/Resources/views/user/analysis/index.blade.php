<x-layouts.user :title="__('Business analysis')">
    @php
        $activeFocus = old('focus', $lastRun?->focus ?? $focus ?? 'gaps');
        $selectedId = (int) old('source', $selectedList?->id ?? 0);
        $analysisCreditCost = (int) ($analysisCreditCost ?? 1);
    @endphp

    <div class="mb-4">
        <h2 class="heading-3">{{ __('Business analysis') }}</h2>
    </div>

    <div class="@container">
        <form
            action="{{ route('user.analysis.run') }}"
            method="post"
            class="anz__form"
            data-estimate-form
            data-estimate-mode="selection"
            data-balance="{{ $balance }}"
            data-cost-per-item="{{ $analysisCreditCost }}"
        >
            @csrf

            <section class="form-card">
                <h3 class="form-card__title">{{ __('What to analyse') }}</h3>

                <div class="mt-4">
                    <label for="anz-source" class="form-label">{{ __('List') }}</label>
                    <select id="anz-source" name="source" class="form-input" required>
                        @forelse ($lists as $list)
                            @php
                                $analysedCount = (int) ($analysisCounts[$list->id] ?? 0);
                            @endphp
                            <option
                                value="{{ $list->id }}"
                                data-count="{{ $list->leads_count }}"
                                data-analysed="{{ $analysedCount }}"
                                @selected($selectedId === $list->id)
                            >
                                {{ $list->name }} ({{ trans_choice(':count lead|:count leads', $list->leads_count, ['count' => number_format($list->leads_count)]) }})
                            </option>
                        @empty
                            <option value="" data-count="0" data-analysed="0">
                                {{ __('No lead lists yet') }}
                            </option>
                        @endforelse
                    </select>
                    @error('source')
                        <p class="form-hint text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">
                        <a href="{{ route('user.leads.lists') }}" class="est__link">
                            {{ __('Manage your lists') }}
                        </a>
                    </p>
                </div>

                <div class="anz__opt">
                    <input type="hidden" name="skip_analysed" value="0" />
                    <input type="checkbox" class="form-check" id="anz-skip" name="skip_analysed" value="1" @checked(old('skip_analysed', true)) />
                    <label for="anz-skip" class="cursor-pointer text-[0.9375rem] font-medium text-title">
                        {{ __('Skip businesses already analysed') }}
                    </label>
                </div>

                <div class="mt-5">
                    <label for="anz-focus" class="form-label">{{ __('What you want to know') }}</label>
                    <select id="anz-focus" name="focus" class="form-input" data-anz-focus>
                        <option value="gaps" @selected($activeFocus === 'gaps')>
                            {{ __('Where they are weak — the gap to open with') }}
                        </option>
                        <option value="fit" @selected($activeFocus === 'fit')>{{ __('Whether they fit what I sell') }}</option>
                        <option value="summary" @selected($activeFocus === 'summary')>{{ __('A plain summary of the business') }}</option>
                    </select>
                    @error('focus')
                        <p class="form-hint text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <aside class="@3xl:sticky @3xl:top-24">
                <section class="form-card est">
                    <h3 class="form-card__title">{{ __('Before you run it') }}</h3>

                    <dl class="est__rows mt-4">
                        <div class="est__row">
                            <dt class="est__key">{{ __('Businesses to analyse') }}</dt>
                            <dd class="est__val numeric" data-estimate-count>0</dd>
                        </div>

                        <div class="est__row est__row--cost">
                            <dt class="est__key">{{ __('Credits it costs') }}</dt>
                            <dd class="est__val numeric" data-estimate-cost>0</dd>
                        </div>

                        <div class="est__row est__row--after">
                            <dt class="est__key">{{ __('Balance afterwards') }}</dt>
                            <dd class="est__val numeric" data-estimate-left>{{ number_format($balance) }}</dd>
                        </div>
                    </dl>

                    <p class="est__warn" data-estimate-warning role="status">
                        <i class="ph-fill ph-warning" aria-hidden="true"></i>
                        <span>
                            {{ __('This run costs more credits than you have.') }}
                            <a href="{{ route('user.credits.buy') }}" class="est__link">{{ __('Buy more') }}</a>
                        </span>
                    </p>

                    <p class="est__note">
                        <i class="ph ph-info" aria-hidden="true"></i>
                        {{ trans_choice(':count credit per business analysed. Skipped businesses do not spend credits.|:count credits per business analysed. Skipped businesses do not spend credits.', $analysisCreditCost, ['count' => number_format($analysisCreditCost)]) }}
                    </p>

                    <button type="submit" class="btn btn-primary mt-5 w-full" data-estimate-submit>
                        <span class="btn__label">
                            <span>{{ __('Run the analysis') }}</span>
                            <span aria-hidden="true">{{ __('Run the analysis') }}</span>
                        </span>
                        <i class="ph ph-sparkle"></i>
                    </button>
                </section>
            </aside>
        </form>
    </div>

    <div class="panel mt-6">
        <div class="panel__head">
            <h3 class="panel__title">
                <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                {{ __('Last run') }}
            </h3>
            @if ($lastRun)
                <p class="panel__meta">
                    <span class="numeric">{{ number_format($lastRun->businesses_count) }}</span>
                    {{ trans_choice('business|businesses', $lastRun->businesses_count) }}
                    · {{ $lastRun->created_at->diffForHumans() }}
                    · {{ $lastRun->leadList?->name }}
                </p>
            @endif
        </div>

        <div class="anz__results is-{{ $activeFocus }}" data-anz-results>
            @forelse ($lastItems as $item)
                <article class="anzr">
                    <div class="anzr__head">
                        <a href="{{ route('user.leads.show', $item->lead) }}" class="font-title text-[0.9375rem] font-bold text-title transition-colors duration-200 hover:text-primary">
                            {{ $item->lead?->place?->name ?? __('Untitled business') }}
                        </a>
                        <span class="score score--{{ $item->scoreBucket() }} numeric">{{ $item->score }}</span>
                    </div>

                    <p class="anzr__read">{{ $item->read }}</p>

                    <p class="anzr__gap">
                        <i class="ph ph-lightbulb" aria-hidden="true"></i>
                        <span>{{ $item->gap }}</span>
                    </p>

                    <p class="anzr__fit anzr__fit--{{ $item->fit_status }}">
                        <i class="ph {{ $item->fit_status === 'yes' ? 'ph-check-circle' : 'ph-minus-circle' }}" aria-hidden="true"></i>
                        <span>{{ $item->fit }}</span>
                    </p>
                </article>
            @empty
                <div class="px-4 py-8 text-center">
                    <p class="font-title text-[1rem] font-bold text-title">{{ __('No analysis runs yet') }}</p>
                </div>
            @endforelse
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-neutral-200 px-4 py-3">
            <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm">
                <span class="btn__label">
                    <span>{{ __('See them in the table') }}</span>
                    <span aria-hidden="true">{{ __('See them in the table') }}</span>
                </span>
                <i class="ph ph-arrow-right"></i>
            </a>
        </div>
    </div>
</x-layouts.user>

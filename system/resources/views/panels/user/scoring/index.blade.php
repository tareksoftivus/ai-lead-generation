@php
    $weights = $weights ?? [
        'reviews' => 30,
        'booking' => 40,
        'age' => 20,
        'competition' => 10,
    ];
    $leadCount = $leadCount ?? 0;
    $sampleLeads = $sampleLeads ?? collect();
    $lists = $lists ?? collect();
@endphp

<x-layouts.user :title="__('Lead scoring')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Lead scoring') }}</h2>
        <p class="m-text mt-1">
            {{ __('What makes a lead good depends on what you sell. Tell us that, and the ordering changes to match.') }}
        </p>
    </div>

    <form action="{{ route('user.scoring.apply') }}" method="post" class="scr" data-scoring>
        @csrf
        <div class="scr__grid">
            {{-- Left: what you sell, and the weights --}}
            <div class="scr__main">
                <section class="form-card">
                    <h3 class="form-card__title">{{ __('What you sell') }}</h3>
                    <p class="form-card__hint">
                        {{ __('In your own words. A web agency and a supplier want opposite leads — this is what the weighting keys off.') }}
                    </p>

                    <div class="mt-4">
                        <label for="scr-sell" class="form-label">
                            {{ __('Describe it in a sentence or two') }}
                        </label>
                        <textarea
                            id="scr-sell"
                            name="offer"
                            class="form-input min-h-[9.5rem] @lg:min-h-[7rem]"
                            rows="3"
                            placeholder="{{ __('We build booking systems for dental practices — they usually come to us when the phone is the only way to book.') }}"
                        >{{ old('offer', __('We build booking systems for dental practices — they usually come to us when the phone is the only way to book.')) }}</textarea>
                        <p class="form-hint">
                            {{ __('The clearer the gap you close, the better the ordering.') }}
                        </p>
                    </div>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('What counts, and how much') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Drag a signal up and the sample re-scores as you go. Shares matter, not the total — these do not need to add up to') }}
                        <span class="numeric">100</span>.
                    </p>

                    <div class="mt-5">
                        <div class="wrow">
                            <label for="w-reviews" class="form-label">
                                {{ __('Review volume') }}
                                <span class="field__val">
                                    <span class="numeric" data-weight-out="reviews">{{ old('w_reviews', $weights['reviews']) }}</span>
                                </span>
                            </label>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                {{ __('How much demand the business already has.') }}
                            </p>
                            <input
                                type="range"
                                id="w-reviews"
                                name="w_reviews"
                                class="range mt-2"
                                min="0"
                                max="100"
                                step="5"
                                value="{{ old('w_reviews', $weights['reviews']) }}"
                                data-weight="reviews"
                            />
                        </div>

                        <div class="wrow">
                            <label for="w-booking" class="form-label">
                                {{ __('Online booking') }}
                                <span class="field__val">
                                    <span class="numeric" data-weight-out="booking">{{ old('w_booking', $weights['booking']) }}</span>
                                </span>
                            </label>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                {{ __('Whether the gap you sell into is actually there.') }}
                            </p>
                            <input
                                type="range"
                                id="w-booking"
                                name="w_booking"
                                class="range mt-2"
                                min="0"
                                max="100"
                                step="5"
                                value="{{ old('w_booking', $weights['booking']) }}"
                                data-weight="booking"
                            />
                        </div>

                        <div class="wrow">
                            <label for="w-age" class="form-label">
                                {{ __('Website age') }}
                                <span class="field__val">
                                    <span class="numeric" data-weight-out="age">{{ old('w_age', $weights['age']) }}</span>
                                </span>
                            </label>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                {{ __('How long since anyone invested in the site.') }}
                            </p>
                            <input
                                type="range"
                                id="w-age"
                                name="w_age"
                                class="range mt-2"
                                min="0"
                                max="100"
                                step="5"
                                value="{{ old('w_age', $weights['age']) }}"
                                data-weight="age"
                            />
                        </div>

                        <div class="wrow">
                            <label for="w-competition" class="form-label">
                                {{ __('Local competition') }}
                                <span class="field__val">
                                    <span class="numeric" data-weight-out="competition">{{ old('w_competition', $weights['competition']) }}</span>
                                </span>
                            </label>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                {{ __('How crowded their market already is.') }}
                            </p>
                            <input
                                type="range"
                                id="w-competition"
                                name="w_competition"
                                class="range mt-2"
                                min="0"
                                max="100"
                                step="5"
                                value="{{ old('w_competition', $weights['competition']) }}"
                                data-weight="competition"
                            />
                        </div>
                    </div>

                    <p class="scr__total">
                        {{ __('Weights total') }}
                        <span class="numeric" data-weight-total>{{ array_sum([
                            (int) old('w_reviews', $weights['reviews']),
                            (int) old('w_booking', $weights['booking']),
                            (int) old('w_age', $weights['age']),
                            (int) old('w_competition', $weights['competition']),
                        ]) }}</span>
                    </p>
                </section>
            </div>

            {{-- Right: the preview --}}
            <aside class="min-w-0">
                <section class="form-card">
                    <h3 class="form-card__title">
                        <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                        {{ __('What it does to your leads') }}
                    </h3>
                    <p class="form-card__hint">
                        {{ __('A sample of businesses you already have, scored both ways. Nothing is saved until you apply it.') }}
                    </p>

                    <div class="tbl-wrap mt-4">
                        <table class="d-table prev d-table--cards">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Business') }}</th>
                                    <th scope="col" class="text-right">{{ __('Now') }}</th>
                                    <th scope="col" class="text-right">{{ __('New') }}</th>
                                    <th scope="col" class="text-right">{{ __('Change') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sampleLeads as $lead)
                                    @php
                                        $now = (int) ($lead->score ?? 0);
                                        $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($now);
                                        $signals = collect($scoring->signalScores($lead))
                                            ->map(fn ($value, $key) => $key.':'.$value)
                                            ->implode(',');
                                    @endphp
                                    <tr data-sample data-now="{{ $now }}" data-signals="{{ $signals }}">
                                        <td data-card-title>
                                            {{ $lead->place?->name ?? __('Unknown business') }}
                                            @if ($lead->place?->formatted_address)
                                                <p class="d-table__muted mt-1">{{ $lead->place->formatted_address }}</p>
                                            @endif
                                        </td>
                                        <td class="text-right" data-label="{{ __('Now') }}">
                                            <span class="score score--{{ $bucket }} numeric">{{ $now }}</span>
                                        </td>
                                        <td data-label="{{ __('New') }}" class="text-right">
                                            <span class="score score--{{ $bucket }} numeric" data-sample-new>{{ $now }}</span>
                                        </td>
                                        <td data-label="{{ __('Change') }}" class="text-right">
                                            <span class="delta" data-sample-delta>{{ __('no change') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <p class="no-results m-0">
                                                <i class="ph ph-chart-line-up" aria-hidden="true"></i>
                                                {{ __('No saved leads yet. Generate leads first, then come back to tune their scoring.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-4 text-[0.8125rem] text-body" @if ($sampleLeads->isEmpty()) hidden @endif>
                        <span class="numeric" data-preview-moved>0</span>
                        {{ __('of') }} <span class="numeric">{{ $sampleLeads->count() }}</span> {{ __('sampled leads move under this weighting.') }}
                    </p>
                </section>

                {{-- Committing it --}}
                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('Apply it') }}</h3>

                    <div class="mt-4">
                        <label for="scr-scope" class="form-label">
                            {{ __('Re-score which leads') }}
                        </label>
                        <select id="scr-scope" name="scope" class="form-input">
                            <option value="all" @selected(old('scope', 'all') === 'all')>
                                {{ __('Every lead I have (:count)', ['count' => number_format($leadCount)]) }}
                            </option>
                            @foreach ($lists as $list)
                                <option value="{{ $list->id }}" @selected((string) old('scope') === (string) $list->id)>
                                    {{ __(':name (:count)', ['name' => $list->name, 'count' => number_format($list->leads_count)]) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <p class="est__note mt-4">
                        <i class="ph ph-info" aria-hidden="true"></i>
                        {{ __('Re-scoring is free. It re-reads analysis you have already paid for — no credits are spent here.') }}
                    </p>

                    <div class="form-actions mt-5">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            data-scoring-apply
                            @if ($leadCount === 0) data-scoring-empty="1" @endif
                            @disabled($leadCount === 0)
                        >
                            <span class="btn__label">
                                <span>{{ __('Apply and re-score') }}</span>
                                <span aria-hidden="true">{{ __('Apply and re-score') }}</span>
                            </span>
                            <i class="ph ph-check"></i>
                        </button>
                        <button
                            type="reset"
                            class="btn btn-outline"
                            data-confirm
                            data-confirm-title="{{ __('Reset the weighting?') }}"
                            data-confirm-body="{{ __('The sliders go back to the defaults. Your existing scores stay as they are until you apply a change.') }}"
                            data-confirm-label="{{ __('Reset') }}"
                        >
                            <span class="btn__label">
                                <span>{{ __('Reset') }}</span>
                                <span aria-hidden="true">{{ __('Reset') }}</span>
                            </span>
                        </button>
                    </div>
                </section>
            </aside>
        </div>
    </form>

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

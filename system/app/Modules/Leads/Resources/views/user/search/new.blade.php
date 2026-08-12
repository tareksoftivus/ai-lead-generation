<x-layouts.user :title="__('New search')">
    @php
        $activeFilters = $activeFilters ?? [];
        $filterOptions = $filterOptions ?? [
            'keywords' => ['dentists', 'orthodontists', 'dental clinics', 'cosmetic dentists'],
            'locations' => ['Austin, TX', 'Dallas, TX', 'Houston, TX', 'San Antonio, TX'],
            'excludes' => ['franchise', 'permanently closed', 'hospital'],
            'categories' => ['Dentist', 'Orthodontist', 'Dental clinic', 'Cosmetic dentist'],
        ];
        $hasSearchContext = isset($searchRun) || isset($results);
        $searchFailed = isset($searchRun) && $searchRun->status === \App\Modules\Leads\Models\SearchRun::STATUS_FAILED;
        $resultCount = isset($results) ? count($results) : 0;
        $creditBalance = app(\App\Modules\Credits\Services\CreditLedger::class)->balance(auth()->user());

        $selectedArray = fn (string $key) => collect((array) old($key, $activeFilters[$key] ?? []))
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->values();
        $selectedValue = fn (string $key, mixed $default = null) => old($key, $activeFilters[$key] ?? $default);
        $selectedBool = fn (string $key, bool $default = false) => (bool) old($key, $activeFilters[$key] ?? $default);
        $searchLabel = fn (array $filters) => trim(implode(' ', array_filter([
            implode(', ', (array) ($filters['keyword'] ?? [])),
            ! empty($filters['location']) ? 'in' : null,
            implode(', ', (array) ($filters['location'] ?? [])),
        ]))) ?: __('Untitled search');
        $mapsUrl = fn ($place) => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(trim($place->name.' '.$place->formatted_address)).'&query_place_id='.rawurlencode($place->google_place_id);
    @endphp

    <form id="lead-search-form" class="srch-page" action="{{ route('user.search.run') }}" method="post"
          data-estimate-form data-estimate-url="{{ route('user.search.estimate') }}"
          data-show-results-on-query="false"
          data-server-estimate-locked="{{ $hasSearchContext ? 'true' : 'false' }}"
          data-balance="{{ $creditBalance }}">
        @csrf
        <div class="srch">
            <aside class="srch__rail" aria-label="{{ __('Search filters') }}">
                <div class="srch__rail-head">
                    <p class="srch__rail-title text-xl!">
                        {{ __('Filters') }}
                        <button type="button" class="srch__rail-badge" data-filter-clear data-filter-count-bar>
                            <span class="numeric" data-filter-count>0</span>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span class="sr-only">{{ __('Clear all filters') }}</span>
                        </button>
                    </p>

                    <span class="srch__rail-tools">
                        <button type="button" class="srch__tool" aria-label="{{ __('Save this search') }}" data-modal-open="saveSearchModal">
                            <i class="ph ph-bookmark-simple text-xl" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('user.search.history') }}" class="srch__tool" aria-label="{{ __('Open saved searches') }}">
                            <i class="ph ph-arrow-square-out text-xl" aria-hidden="true"></i>
                        </a>
                    </span>
                </div>

                <div class="srch__pref">
                    <label class="srch__pref-label" for="s-skip-owned">
                        <i class="ph ph-skip-forward" aria-hidden="true"></i>
                        {{ __('Skip leads I already have') }}
                    </label>
                    <input type="checkbox" id="s-skip-owned" name="skip_owned" class="switch" value="1" @checked($selectedBool('skip_owned', true))>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-type" data-accordion-toggle>
                            <i class="ph ph-magnifying-glass fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Business type') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-type" data-accordion-panel>
                        <div class="ffield{{ $selectedArray('keyword')->isNotEmpty() ? ' is-filled' : '' }}" data-filter-field data-filter-name="keyword[]">
                            <span data-filter-chips class="contents">
                                @foreach ($selectedArray('keyword') as $keyword)
                                    <span class="ffield__chip" data-value="{{ $keyword }}">
                                        <span>{{ $keyword }}</span>
                                        <input type="hidden" name="keyword[]" value="{{ $keyword }}">
                                        <button type="button" class="ffield__x" aria-label="{{ __('Remove :value', ['value' => $keyword]) }}">
                                            <i class="ph ph-x" aria-hidden="true"></i>
                                        </button>
                                    </span>
                                @endforeach
                            </span>
                            <input type="text" id="s-keyword" class="ffield__input" placeholder="{{ __('Add a type…') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-keyword-panel" data-filter-input>
                            <div class="ffield__panel" id="s-keyword-panel" role="listbox" data-filter-panel>
                                @foreach ($filterOptions['keywords'] as $keyword)
                                    <button type="button" class="ffield__opt" data-filter-option="{{ $keyword }}">
                                        <i class="ph ph-magnifying-glass" aria-hidden="true"></i>{{ $keyword }}
                                    </button>
                                @endforeach
                                <p class="ffield__none is-hidden" data-filter-empty>
                                    {{ __('No matches — press Enter to use what you typed.') }}
                                </p>
                                <button type="button" class="ffield__opt ffield__new is-hidden" data-filter-custom></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-location" data-accordion-toggle>
                            <i class="ph ph-map-pin fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Location') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-location" data-accordion-panel>
                        <div class="ffield{{ $selectedArray('location')->isNotEmpty() ? ' is-filled' : '' }}" data-filter-field data-filter-name="location[]">
                            <span data-filter-chips class="contents">
                                @foreach ($selectedArray('location') as $location)
                                    <span class="ffield__chip" data-value="{{ $location }}">
                                        <span>{{ $location }}</span>
                                        <input type="hidden" name="location[]" value="{{ $location }}">
                                        <button type="button" class="ffield__x" aria-label="{{ __('Remove :value', ['value' => $location]) }}">
                                            <i class="ph ph-x" aria-hidden="true"></i>
                                        </button>
                                    </span>
                                @endforeach
                            </span>
                            <input type="text" id="s-location" class="ffield__input" placeholder="{{ __('Add a place…') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-location-panel" data-filter-input>
                            <div class="ffield__panel" id="s-location-panel" role="listbox" data-filter-panel>
                                @foreach ($filterOptions['locations'] as $location)
                                    <button type="button" class="ffield__opt" data-filter-option="{{ $location }}">
                                        <i class="ph ph-map-pin" aria-hidden="true"></i>{{ $location }}
                                    </button>
                                @endforeach
                                <p class="ffield__none is-hidden" data-filter-empty>
                                    {{ __('No matches — press Enter to use what you typed.') }}
                                </p>
                                <button type="button" class="ffield__opt ffield__new is-hidden" data-filter-custom></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-radius" data-accordion-toggle>
                            <i class="ph ph-circle-dashed fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Radius') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-radius" data-accordion-panel>
                        <input type="range" id="s-radius" name="radius" class="range mt-2" min="1" max="50" step="1" value="{{ $selectedValue('radius', 10) }}">
                        <p class="range__scale" aria-hidden="true">
                            <span class="numeric">1</span>
                            <span class="numeric">50 mi</span>
                        </p>
                    </div>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-exclude" data-accordion-toggle>
                            <i class="ph ph-prohibit fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Exclude') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-exclude" data-accordion-panel>
                        <div class="ffield ffield--exclude{{ $selectedArray('exclude_keyword')->isNotEmpty() ? ' is-filled' : '' }}" data-filter-field data-filter-name="exclude_keyword[]" data-filter-counts>
                            <span data-filter-chips class="contents">
                                @foreach ($selectedArray('exclude_keyword') as $exclude)
                                    <span class="ffield__chip" data-value="{{ $exclude }}">
                                        <span>{{ $exclude }}</span>
                                        <input type="hidden" name="exclude_keyword[]" value="{{ $exclude }}">
                                        <button type="button" class="ffield__x" aria-label="{{ __('Remove :value', ['value' => $exclude]) }}">
                                            <i class="ph ph-x" aria-hidden="true"></i>
                                        </button>
                                    </span>
                                @endforeach
                            </span>
                            <input type="text" id="s-exclude" class="ffield__input" placeholder="{{ __('e.g. franchise') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-exclude-panel" data-filter-input>
                            <div class="ffield__panel" id="s-exclude-panel" role="listbox" data-filter-panel>
                                @foreach ($filterOptions['excludes'] as $exclude)
                                    <button type="button" class="ffield__opt" data-filter-option="{{ $exclude }}">
                                        <i class="ph ph-prohibit" aria-hidden="true"></i>{{ $exclude }}
                                    </button>
                                @endforeach
                                <p class="ffield__none is-hidden" data-filter-empty>
                                    {{ __('No matches — press Enter to use what you typed.') }}
                                </p>
                                <button type="button" class="ffield__opt ffield__new is-hidden" data-filter-custom></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-rating" data-accordion-toggle>
                            <i class="ph ph-star fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Minimum rating') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-rating" data-accordion-panel>
                        <fieldset class="mt-4" data-range-group data-filter-counts>
                            <div class="bkt">
                                <label class="bkt__opt">
                                    <input type="radio" name="min_rating" value="" @checked($selectedValue('min_rating', '') === null || $selectedValue('min_rating', '') === '')>
                                    {{ __('Any rating') }}
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_rating" value="3" @checked((string) $selectedValue('min_rating') === '3')>
                                    <span class="numeric">3.0</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_rating" value="4" @checked((string) $selectedValue('min_rating') === '4')>
                                    <span class="numeric">4.0</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_rating" value="4.5" @checked((string) $selectedValue('min_rating') === '4.5')>
                                    <span class="numeric">4.5</span>+
                                </label>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-reviews" data-accordion-toggle>
                            <i class="ph ph-chat-circle-text fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Minimum reviews') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-reviews" data-accordion-panel>
                        <fieldset class="mt-4" data-range-group data-filter-counts>
                            <div class="bkt">
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="" @checked($selectedValue('min_reviews', '') === null || $selectedValue('min_reviews', '') === '')>
                                    {{ __('Any number') }}
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="10" @checked((string) $selectedValue('min_reviews') === '10')>
                                    <span class="numeric">10</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="50" @checked((string) $selectedValue('min_reviews') === '50')>
                                    <span class="numeric">50</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="200" @checked((string) $selectedValue('min_reviews') === '200')>
                                    <span class="numeric">200</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="custom" @checked((string) $selectedValue('min_reviews') === 'custom')>
                                    {{ __('Custom') }}
                                </label>
                            </div>

                            <div class="bkt__custom" data-range-custom>
                                <div>
                                    <label for="s-reviews-min" class="form-label">{{ __('From') }}</label>
                                    <input type="number" id="s-reviews-min" name="min_reviews_from" class="form-input numeric" min="0" placeholder="25" value="{{ $selectedValue('min_reviews_from') }}" @disabled((string) $selectedValue('min_reviews') !== 'custom')>
                                </div>
                                <div>
                                    <label for="s-reviews-max" class="form-label">{{ __('To') }}</label>
                                    <input type="number" id="s-reviews-max" name="min_reviews_to" class="form-input numeric" min="0" placeholder="400" value="{{ $selectedValue('min_reviews_to') }}" @disabled((string) $selectedValue('min_reviews') !== 'custom')>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-website" data-accordion-toggle>
                            <i class="ph ph-address-book fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Contact details') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-website" data-accordion-panel>
                        <label class="check mt-4" for="s-website">
                            <input type="checkbox" id="s-website" name="has_website" class="form-check" value="1" data-filter-counts @checked($selectedBool('has_website'))>
                            <span class="flex flex-col">
                                <span class="text-[0.9375rem] font-medium text-title">{{ __('Only with a website') }}</span>
                                <span class="mt-1 text-[0.8125rem] text-body">{{ __('We follow the site to find an inbox, so this raises the odds of getting a contact.') }}</span>
                            </span>
                        </label>

                        <label class="check mt-4" for="s-phone">
                            <input type="checkbox" id="s-phone" name="has_phone" class="form-check" value="1" data-filter-counts @checked($selectedBool('has_phone'))>
                            <span class="flex flex-col">
                                <span class="text-[0.9375rem] font-medium text-title">{{ __('Only with a phone number') }}</span>
                                <span class="mt-1 text-[0.8125rem] text-body">{{ __('Maps lists it directly, so this costs no extra enrichment.') }}</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="fac" data-accordion>
                    <h3 class="fac__h">
                        <button type="button" class="fac__head" aria-expanded="false" aria-controls="f-category" data-accordion-toggle>
                            <i class="ph ph-tag fac__icon" aria-hidden="true"></i>
                            <span class="fac__label">{{ __('Category') }}</span>
                            <span class="fac__count" data-accordion-count hidden></span>
                            <i class="ph ph-caret-down fac__caret" aria-hidden="true"></i>
                        </button>
                    </h3>
                    <div class="fac__panel" id="f-category" data-accordion-panel>
                        <div class="ffield{{ $selectedArray('category')->isNotEmpty() ? ' is-filled' : '' }}" data-filter-field data-filter-name="category[]" data-filter-counts>
                            <span data-filter-chips class="contents">
                                @foreach ($selectedArray('category') as $category)
                                    <span class="ffield__chip" data-value="{{ $category }}">
                                        <span>{{ $category }}</span>
                                        <input type="hidden" name="category[]" value="{{ $category }}">
                                        <button type="button" class="ffield__x" aria-label="{{ __('Remove :value', ['value' => $category]) }}">
                                            <i class="ph ph-x" aria-hidden="true"></i>
                                        </button>
                                    </span>
                                @endforeach
                            </span>
                            <input type="text" id="s-category" class="ffield__input" placeholder="{{ __('Add a category…') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-category-panel" data-filter-input>
                            <div class="ffield__panel" id="s-category-panel" role="listbox" data-filter-panel>
                                @foreach ($filterOptions['categories'] as $category)
                                    <button type="button" class="ffield__opt" data-filter-option="{{ $category }}">
                                        <i class="ph ph-tag" aria-hidden="true"></i>{{ $category }}
                                    </button>
                                @endforeach
                                <p class="ffield__none is-hidden" data-filter-empty>
                                    {{ __('No matches — press Enter to use what you typed.') }}
                                </p>
                                <button type="button" class="ffield__opt ffield__new is-hidden" data-filter-custom></button>
                            </div>
                        </div>
                        <p class="form-hint">
                            {{ __('The category Google itself assigns, which is not always what you searched for.') }}
                        </p>
                    </div>
                </div>

                <div class="srch__pref srch__pref--foot">
                    <label class="srch__pref-label" for="s-one-per">
                        <i class="ph ph-stack-simple" aria-hidden="true"></i>
                        {{ __('One lead per business') }}
                    </label>
                    <input type="checkbox" id="s-one-per" name="one_per_business" class="switch" value="1" data-filter-counts @checked($selectedBool('one_per_business'))>
                </div>
            </aside>

            <div class="srch__work">
                <div class="srch__hero">
                    <h3 class="srch__hero-title">{{ __('Start your search') }}</h3>
                    <p class="srch__hero-lead">
                        {{ __('Describe what you are after and we fill the filters in, or set them on the left yourself. Credits are spent when leads are generated, before they are saved to your lists.') }}
                    </p>

                    <div class="srch__ask">
                        <span class="srch__ask-field">
                            <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                            <input type="text" name="prompt" class="srch__ask-input" placeholder="{{ __('e.g. dentists in Austin with at least 50 reviews') }}" aria-label="{{ __('Describe the businesses you want') }}" value="{{ $selectedValue('prompt') }}" data-ai-prompt>
                            <input type="hidden" name="requested_count" value="{{ $selectedValue('requested_count') }}" data-ai-requested-count>
                        </span>
                        <button type="submit" class="srch__ask-btn" data-ai-fill>
                            <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                            {{ __('AI search') }}
                        </button>
                    </div>

                    <div class="srch__recall">
                        <div class="srch__recall-col">
                            <p class="srch__recall-title">
                                <i class="ph ph-bookmark-simple" aria-hidden="true"></i>
                                {{ __('Saved searches') }}
                            </p>
                            <div class="srch__recall-list">
                                @forelse ($savedSearches ?? [] as $savedSearch)
                                    @php
                                        $savedPreset = json_encode($savedSearch->filters ?? [], JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_QUOT) ?: '{}';
                                    @endphp
                                    <button type="button" class="srch__recall-item text-left" data-search-preset="{{ $savedPreset }}">
                                        <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                                        {{ $savedSearch->name ?: $searchLabel($savedSearch->filters ?? []) }}
                                    </button>
                                @empty
                                    <span class="srch__recall-item">
                                        <i class="ph ph-bookmark-simple" aria-hidden="true"></i>
                                        {{ __('No saved searches yet') }}
                                    </span>
                                @endforelse
                            </div>
                        </div>

                        <div class="srch__recall-col">
                            <p class="srch__recall-title">
                                <i class="ph ph-clock-counter-clockwise" aria-hidden="true"></i>
                                {{ __('Recent searches') }}
                            </p>
                            <div class="srch__recall-list">
                                @forelse ($recentSearches ?? [] as $recentSearch)
                                    @php
                                        $recentPreset = json_encode($recentSearch->filters ?? [], JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_QUOT) ?: '{}';
                                    @endphp
                                    <button type="button" class="srch__recall-item text-left" data-search-preset="{{ $recentPreset }}">
                                        <i class="ph ph-arrow-counter-clockwise" aria-hidden="true"></i>
                                        {{ $searchLabel($recentSearch->filters ?? []) }}
                                    </button>
                                @empty
                                    <span class="srch__recall-item">
                                        <i class="ph ph-clock-counter-clockwise" aria-hidden="true"></i>
                                        {{ __('No recent searches yet') }}
                                    </span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <section class="srch__results{{ $hasSearchContext ? '' : ' is-hidden' }}" data-search-results data-has-server-results="{{ $hasSearchContext ? 'true' : 'false' }}">
                    @if ($searchFailed)
                        <x-ui.alert type="error" class="mb-4">
                            {{ __('We could not complete this search right now. Please try again in a moment, or contact support if it keeps happening.') }}
                        </x-ui.alert>

                        <div class="srch__results-act">
                            <button type="button" class="btn btn-sm btn-outline" data-results-hide>
                                <span class="btn__label">
                                    <span>{{ __('Clear') }}</span>
                                    <span aria-hidden="true">{{ __('Clear') }}</span>
                                </span>
                                <i class="ph ph-x"></i>
                            </button>

                            <button type="submit" class="btn btn-sm btn-primary" data-estimate-submit>
                                <span class="btn__label">
                                    <span>{{ __('Run search') }}</span>
                                    <span aria-hidden="true">{{ __('Run search') }}</span>
                                </span>
                                <i class="ph ph-arrow-right"></i>
                            </button>
                        </div>
                    @else
                        <div class="srch__results-head">
                            <div class="min-w-0">
                                <p class="srch__results-title">
                                    <span class="numeric" data-estimate-count>{{ $resultCount }}</span>
                                    {{ __('businesses found') }}
                                </p>
                                <p class="srch__cost" data-estimate-summary>
                                    <span class="srch__cost-val">
                                        <span class="numeric" data-estimate-cost>{{ $resultCount }}</span>
                                        {{ __('credits to generate them') }}
                                    </span>
                                    <span class="srch__cost-sep" aria-hidden="true">·</span>
                                    <span class="srch__cost-after">
                                        {{ __('leaves') }}
                                        <span class="numeric" data-estimate-left>{{ $creditBalance - $resultCount }}</span>
                                    </span>
                                </p>
                            </div>

                            <div class="srch__results-act">
                                <button type="button" class="btn btn-sm btn-outline" data-results-hide>
                                    <span class="btn__label">
                                        <span>{{ __('Clear') }}</span>
                                        <span aria-hidden="true">{{ __('Clear') }}</span>
                                    </span>
                                    <i class="ph ph-x"></i>
                                </button>

                                @if (isset($searchRun) && isset($results) && count($results) > 0)
                                    <button type="submit" form="save-to-leads-form" name="save_all" value="1" class="btn btn-sm btn-outline">
                                        <span class="btn__label">
                                            <span>{{ __('Save all leads') }}</span>
                                            <span aria-hidden="true">{{ __('Save all leads') }}</span>
                                        </span>
                                        <i class="ph ph-download-simple"></i>
                                    </button>
                                @endif

                                <button type="submit" class="btn btn-sm btn-primary" data-estimate-submit>
                                    <span class="btn__label">
                                        <span>{{ __('Run search') }}</span>
                                        <span aria-hidden="true">{{ __('Run search') }}</span>
                                    </span>
                                    <i class="ph ph-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="srch__applied is-hidden" data-applied-filters aria-label="{{ __('Applied filters') }}"></div>

                        <div class="panel mt-0" data-list data-bulk>
                            <div class="list-toolbar">
                                <div class="search-field">
                                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                                    <input type="search" class="form-input" placeholder="{{ __('Search these results') }}" aria-label="{{ __('Search these results') }}" data-list-search>
                                </div>

                                <p class="list-count">
                                    <span class="numeric" data-list-count>{{ isset($results) ? count($results) : 0 }}</span> {{ __('found') }}
                                </p>
                            </div>

                            <div class="bulk-bar is-hidden" data-bulk-bar>
                                <p class="bulk-bar__count">
                                    <span class="numeric" data-bulk-count>0</span> {{ __('selected') }}
                                </p>
                                <div class="bulk-bar__actions">
                                    <button type="submit" form="save-to-leads-form" class="btn btn-sm btn-primary" data-save-to-leads-submit>
                                        <span class="btn__label">
                                            <span>{{ __('Save to leads') }}</span>
                                            <span aria-hidden="true">{{ __('Save to leads') }}</span>
                                        </span>
                                        <i class="ph ph-check"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="table-scroll">
                                <table class="d-table d-table--cards whitespace-nowrap" data-list-table>
                                    <thead>
                                        <tr>
                                            <th scope="col" class="d-table__check">
                                                <input type="checkbox" class="form-check" data-bulk-all aria-label="{{ __('Select all visible results') }}">
                                            </th>
                                            <th scope="col">{{ __('Business') }}</th>
                                            <th scope="col">{{ __('Contact') }}</th>
                                            <th scope="col">{{ __('Phone') }}</th>
                                            <th scope="col" class="text-right">{{ __('Score') }}</th>
                                            <th scope="col">
                                                <span class="sr-only">{{ __('Actions') }}</span>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($results ?? [] as $result)
                                            @php
                                                $place = $result['place'];
                                                $score = $result['score'];
                                                $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($score);
                                            @endphp
                                            <tr data-list-key="found" data-score="{{ $bucket }}" data-contact="{{ $place->website || $place->phone ? 'yes' : 'no' }}" data-place-id="{{ $place->id }}">
                                                <td class="d-table__check">
                                                    <input type="checkbox" name="place_id[]" value="{{ $place->id }}" form="save-to-leads-form" class="form-check" data-bulk-item aria-label="{{ __('Select :name', ['name' => $place->name]) }}">
                                                </td>
                                                <td data-card-title>
                                                    <span class="d-table__id">{{ $place->name }}</span>
                                                    <p class="d-table__place">
                                                        <i class="ph ph-map-pin" aria-hidden="true"></i>
                                                        {{ $place->formatted_address }}
                                                    </p>
                                                </td>
                                                <td data-label="Contact">
                                                    <span class="d-table__muted">{{ __('Not yet enriched') }}</span>
                                                </td>
                                                <td data-label="Phone">
                                                    @if ($place->phone)
                                                        <a href="tel:{{ $place->phone }}" class="d-table__tel numeric">{{ $place->phone }}</a>
                                                    @else
                                                        <span class="d-table__muted">{{ __('Not listed') }}</span>
                                                    @endif
                                                </td>
                                                <td data-label="Score" class="text-right">
                                                    <span class="score score--{{ $bucket }} numeric">{{ $score }}</span>
                                                </td>
                                                <td data-card-actions class="text-right">
                                                    <div class="row-actions">
                                                        <a href="{{ $mapsUrl($place) }}" target="_blank" rel="noreferrer" class="btn btn-sm btn-outline">
                                                            <span class="btn__label">
                                                                <span>{{ __('Open') }}</span>
                                                                <span aria-hidden="true">{{ __('Open') }}</span>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <p class="no-results is-hidden" data-list-empty>
                                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                                {{ __('Nothing matches that search.') }}
                            </p>
                        </div>
                    @endif
                </section>
            </div>
        </div>

    </form>

    @if (isset($searchRun) && \Illuminate\Support\Facades\Route::has('user.leads.save-from-search'))
        <form id="save-to-leads-form" action="{{ route('user.leads.save-from-search') }}" method="post">
            @csrf
            <input type="hidden" name="search_run_id" value="{{ $searchRun->id }}">
        </form>
    @endif

    @push('modals')
        <div class="modal" id="saveSearchModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="saveSearchTitle">
                <h2 class="heading-3" id="saveSearchTitle">{{ __('Save this search') }}</h2>
                <p class="m-text mt-2 mb-5">
                    {{ __('Saved searches can be re-run later, and we flag businesses that have opened since last time.') }}
                </p>

                <div>
                    <label for="save-name" class="form-label">{{ __('Name it') }}</label>
                    <input type="text" id="save-name" name="name" form="lead-search-form" class="form-input" placeholder="{{ __('Austin dentists') }}">
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button" class="btn btn-outline" data-modal-close>
                        <span class="btn__label">
                            <span>{{ __('Cancel') }}</span>
                            <span aria-hidden="true">{{ __('Cancel') }}</span>
                        </span>
                    </button>
                    <button
                        type="submit"
                        form="lead-search-form"
                        formaction="{{ route('user.search.save') }}"
                        formmethod="post"
                        class="btn btn-primary"
                        data-save-search-submit
                    >
                        <span class="btn__label">
                            <span>{{ __('Save search') }}</span>
                            <span aria-hidden="true">{{ __('Save search') }}</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>

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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                'use strict';

                const form = document.getElementById('lead-search-form');
                if (!form) return;

                function notify() {
                    form.dispatchEvent(new Event('change', { bubbles: true }));
                    form.dispatchEvent(new Event('input', { bubbles: true }));
                }

                function clearChips(field) {
                    field.querySelectorAll('.ffield__chip').forEach((chip) => chip.remove());
                    field.classList.remove('is-filled');
                }

                function addChip(name, value) {
                    const field = form.querySelector(`[data-filter-name="${name}"]`);
                    const holder = field?.querySelector('[data-filter-chips]');
                    if (!field || !holder || !value) return;

                    const chip = document.createElement('span');
                    chip.className = 'ffield__chip';
                    chip.dataset.value = value;

                    const label = document.createElement('span');
                    label.textContent = value;
                    chip.appendChild(label);

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = name;
                    hidden.value = value;
                    chip.appendChild(hidden);

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'ffield__x';
                    remove.setAttribute('aria-label', `Remove ${value}`);
                    remove.innerHTML = '<i class="ph ph-x" aria-hidden="true"></i>';
                    chip.appendChild(remove);

                    holder.appendChild(chip);
                    field.classList.add('is-filled');
                }

                function setRadio(name, value) {
                    const radio = form.querySelector(`input[name="${name}"][value="${value ?? ''}"]`);
                    if (radio) radio.checked = true;
                }

                function setCheckbox(name, checked) {
                    const input = form.querySelector(`input[name="${name}"]`);
                    if (input) input.checked = Boolean(checked);
                }

                function setValue(name, value) {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input) input.value = value ?? '';
                }

                function applyPreset(filters) {
                    form.querySelectorAll('[data-filter-field]').forEach(clearChips);

                    (filters.keyword || []).forEach((value) => addChip('keyword[]', value));
                    (filters.location || []).forEach((value) => addChip('location[]', value));
                    (filters.exclude_keyword || []).forEach((value) => addChip('exclude_keyword[]', value));
                    (filters.category || []).forEach((value) => addChip('category[]', value));

                    setValue('radius', filters.radius || 10);
                    setValue('prompt', filters.prompt || '');
                    setValue('requested_count', filters.requested_count || '');
                    setRadio('min_rating', filters.min_rating || '');
                    setRadio('min_reviews', filters.min_reviews || '');
                    setValue('min_reviews_from', filters.min_reviews_from || '');
                    setValue('min_reviews_to', filters.min_reviews_to || '');
                    setCheckbox('has_website', filters.has_website);
                    setCheckbox('has_phone', filters.has_phone);
                    setCheckbox('skip_owned', filters.skip_owned ?? true);
                    setCheckbox('one_per_business', filters.one_per_business);

                    const customReviews = String(filters.min_reviews || '') === 'custom';
                    form.querySelectorAll('[name="min_reviews_from"], [name="min_reviews_to"]').forEach((input) => {
                        input.disabled = !customReviews;
                    });

                    notify();
                }

                function chipValues(name) {
                    return Array.from(form.querySelectorAll(`[data-filter-name="${name}"] .ffield__chip`))
                        .map((chip) => chip.dataset.value || '')
                        .filter(Boolean);
                }

                function checkedText(name) {
                    const checked = form.querySelector(`input[name="${name}"]:checked`);
                    if (!checked || checked.value === '') return '';

                    return checked.closest('label')?.textContent.trim() || checked.value;
                }

                function selectedHelpersPrompt() {
                    const keywords = chipValues('keyword[]');
                    const locations = chipValues('location[]');
                    const excludes = chipValues('exclude_keyword[]');
                    const categories = chipValues('category[]');
                    const parts = [];

                    if (keywords.length) {
                        parts.push(keywords.join(', '));
                    }

                    if (locations.length) {
                        parts.push(`in ${locations.join(', ')}`);
                    }

                    const rating = checkedText('min_rating');
                    if (rating) {
                        parts.push(`with ${rating.toLowerCase()} rating`);
                    }

                    const reviews = checkedText('min_reviews');
                    if (reviews) {
                        if (reviews.toLowerCase() === 'custom') {
                            const from = form.querySelector('[name="min_reviews_from"]')?.value;
                            const to = form.querySelector('[name="min_reviews_to"]')?.value;
                            if (from && to) {
                                parts.push(`with ${from} to ${to} reviews`);
                            } else if (from) {
                                parts.push(`with at least ${from} reviews`);
                            }
                        } else {
                            parts.push(`with ${reviews.toLowerCase()} reviews`);
                        }
                    }

                    if (categories.length) {
                        parts.push(`categorized as ${categories.join(', ')}`);
                    }

                    if (excludes.length) {
                        parts.push(`excluding ${excludes.join(', ')}`);
                    }

                    if (form.querySelector('[name="has_website"]')?.checked) {
                        parts.push('with a website');
                    }

                    if (form.querySelector('[name="has_phone"]')?.checked) {
                        parts.push('with a phone number');
                    }

                    return parts.join(' ');
                }

                function syncPromptFromHelpers() {
                    const prompt = form.querySelector('[data-ai-prompt]');
                    if (!(prompt instanceof HTMLInputElement)) return;

                    const composed = selectedHelpersPrompt();
                    const ownsPrompt = prompt.dataset.helperComposed === 'true';

                    if (!composed) {
                        if (ownsPrompt) {
                            prompt.value = '';
                            delete prompt.dataset.helperComposed;
                        }

                        return;
                    }

                    if (prompt.value.trim() !== '' && !ownsPrompt) {
                        return;
                    }

                    prompt.value = composed;
                    prompt.dataset.helperComposed = 'true';
                }

                document.querySelectorAll('[data-search-preset]').forEach((button) => {
                    button.addEventListener('click', () => {
                        try {
                            applyPreset(JSON.parse(button.dataset.searchPreset || '{}'));
                        } catch {
                            applyPreset({});
                        }
                    });
                });

                form.addEventListener('input', (event) => {
                    if (event.target === form.querySelector('[data-ai-prompt]')) {
                        delete event.target.dataset.helperComposed;
                        return;
                    }

                    syncPromptFromHelpers();
                });

                form.addEventListener('change', syncPromptFromHelpers);

                document.getElementById('save-name')?.addEventListener('keydown', (event) => {
                    if (event.key !== 'Enter') return;
                    event.preventDefault();
                    const submit = document.querySelector('[data-save-search-submit]');
                    if (submit instanceof HTMLElement && form instanceof HTMLFormElement) {
                        form.requestSubmit(submit);
                    }
                });
            });
        </script>
    @endpush
</x-layouts.user>

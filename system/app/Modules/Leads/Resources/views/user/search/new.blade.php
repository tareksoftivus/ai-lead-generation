<x-layouts.user :title="__('New search')">
    <form class="srch-page" action="{{ route('user.search.run') }}" method="post"
          data-estimate-form data-estimate-url="{{ route('user.search.estimate') }}"
          data-balance="{{ app(\App\Modules\Credits\Services\CreditLedger::class)->balance(auth()->user()) }}">
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
                        <a href="#" class="srch__tool" aria-label="{{ __('Open saved searches') }}">
                            <i class="ph ph-arrow-square-out text-xl" aria-hidden="true"></i>
                        </a>
                    </span>
                </div>

                <div class="srch__pref">
                    <label class="srch__pref-label" for="s-skip-owned">
                        <i class="ph ph-skip-forward" aria-hidden="true"></i>
                        {{ __('Skip leads I already have') }}
                    </label>
                    <input type="checkbox" id="s-skip-owned" name="skip_owned" class="switch" checked />
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
                        <div class="ffield" data-filter-field data-filter-name="keyword[]">
                            <span data-filter-chips class="contents"></span>
                            <input type="text" id="s-keyword" class="ffield__input" placeholder="{{ __('Add a type…') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-keyword-panel" data-filter-input />
                            <div class="ffield__panel" id="s-keyword-panel" role="listbox" data-filter-panel>
                                <button type="button" class="ffield__opt" data-filter-option="dentists">
                                    <i class="ph ph-tooth" aria-hidden="true"></i>{{ __('dentists') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="orthodontists">
                                    <i class="ph ph-tooth" aria-hidden="true"></i>{{ __('orthodontists') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="dental clinics">
                                    <i class="ph ph-first-aid-kit" aria-hidden="true"></i>{{ __('dental clinics') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="cosmetic dentists">
                                    <i class="ph ph-sparkle" aria-hidden="true"></i>{{ __('cosmetic dentists') }}
                                </button>
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
                        <div class="ffield" data-filter-field data-filter-name="location[]">
                            <span data-filter-chips class="contents"></span>
                            <input type="text" id="s-location" class="ffield__input" placeholder="{{ __('Add a place…') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-location-panel" data-filter-input />
                            <div class="ffield__panel" id="s-location-panel" role="listbox" data-filter-panel>
                                <button type="button" class="ffield__opt" data-filter-option="Austin, TX">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>{{ __('Austin, TX') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="Dallas, TX">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>{{ __('Dallas, TX') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="Houston, TX">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>{{ __('Houston, TX') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="San Antonio, TX">
                                    <i class="ph ph-map-pin" aria-hidden="true"></i>{{ __('San Antonio, TX') }}
                                </button>
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
                        <input type="range" id="s-radius" name="radius" class="range mt-2" min="1" max="50" step="1" value="10" />
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
                        <div class="ffield ffield--exclude" data-filter-field data-filter-name="exclude_keyword[]" data-filter-counts>
                            <span data-filter-chips class="contents"></span>
                            <input type="text" id="s-exclude" class="ffield__input" placeholder="{{ __('e.g. franchise') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-exclude-panel" data-filter-input />
                            <div class="ffield__panel" id="s-exclude-panel" role="listbox" data-filter-panel>
                                <button type="button" class="ffield__opt" data-filter-option="franchise">
                                    <i class="ph ph-buildings" aria-hidden="true"></i>{{ __('franchise') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="permanently closed">
                                    <i class="ph ph-prohibit" aria-hidden="true"></i>{{ __('permanently closed') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="hospital">
                                    <i class="ph ph-first-aid" aria-hidden="true"></i>{{ __('hospital') }}
                                </button>
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
                                    <input type="radio" name="min_rating" value="" checked />
                                    {{ __('Any rating') }}
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_rating" value="3" />
                                    <span class="numeric">3.0</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_rating" value="4" />
                                    <span class="numeric">4.0</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_rating" value="4.5" />
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
                                    <input type="radio" name="min_reviews" value="" checked />
                                    {{ __('Any number') }}
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="10" />
                                    <span class="numeric">10</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="50" />
                                    <span class="numeric">50</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="200" />
                                    <span class="numeric">200</span>+
                                </label>
                                <label class="bkt__opt">
                                    <input type="radio" name="min_reviews" value="custom" />
                                    {{ __('Custom') }}
                                </label>
                            </div>

                            <div class="bkt__custom" data-range-custom>
                                <div>
                                    <label for="s-reviews-min" class="form-label">{{ __('From') }}</label>
                                    <input type="number" id="s-reviews-min" name="min_reviews_from" class="form-input numeric" min="0" placeholder="25" disabled />
                                </div>
                                <div>
                                    <label for="s-reviews-max" class="form-label">{{ __('To') }}</label>
                                    <input type="number" id="s-reviews-max" name="min_reviews_to" class="form-input numeric" min="0" placeholder="400" disabled />
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
                            <input type="checkbox" id="s-website" name="has_website" class="form-check" value="1" data-filter-counts />
                            <span class="flex flex-col">
                                <span class="text-[0.9375rem] font-medium text-title">{{ __('Only with a website') }}</span>
                                <span class="mt-1 text-[0.8125rem] text-body">{{ __('We follow the site to find an inbox, so this raises the odds of getting a contact.') }}</span>
                            </span>
                        </label>

                        <label class="check mt-4" for="s-phone">
                            <input type="checkbox" id="s-phone" name="has_phone" class="form-check" value="1" data-filter-counts />
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
                        <div class="ffield" data-filter-field data-filter-name="category[]" data-filter-counts>
                            <span data-filter-chips class="contents"></span>
                            <input type="text" id="s-category" class="ffield__input" placeholder="{{ __('Add a category…') }}" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="s-category-panel" data-filter-input />
                            <div class="ffield__panel" id="s-category-panel" role="listbox" data-filter-panel>
                                <button type="button" class="ffield__opt" data-filter-option="Dentist">
                                    <i class="ph ph-tooth" aria-hidden="true"></i>{{ __('Dentist') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="Orthodontist">
                                    <i class="ph ph-tooth" aria-hidden="true"></i>{{ __('Orthodontist') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="Dental clinic">
                                    <i class="ph ph-first-aid-kit" aria-hidden="true"></i>{{ __('Dental clinic') }}
                                </button>
                                <button type="button" class="ffield__opt" data-filter-option="Cosmetic dentist">
                                    <i class="ph ph-sparkle" aria-hidden="true"></i>{{ __('Cosmetic dentist') }}
                                </button>
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
                    <input type="checkbox" id="s-one-per" name="one_per_business" class="switch" value="1" data-filter-counts />
                </div>
            </aside>

            <div class="srch__work">
                <div class="srch__hero">
                    <h3 class="srch__hero-title">{{ __('Start your search') }}</h3>
                    <p class="srch__hero-lead">
                        {{ __('Describe what you are after and we fill the filters in, or set them on the left yourself. Searching is free — you only spend a credit when we find a business\'s contact details.') }}
                    </p>

                    <div class="srch__ask">
                        <span class="srch__ask-field">
                            <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                            <input type="text" name="prompt" class="srch__ask-input" placeholder="{{ __('e.g. dentists in Austin with at least 50 reviews') }}" aria-label="{{ __('Describe the businesses you want') }}" data-ai-prompt />
                        </span>
                        <button type="button" class="srch__ask-btn" data-ai-fill>
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
                                <a href="#" class="srch__recall-item">
                                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                                    {{ __('Dentists · Austin, TX') }}
                                </a>
                                <a href="#" class="srch__recall-item">
                                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                                    {{ __('Med spas · Phoenix, AZ') }}
                                </a>
                            </div>
                        </div>

                        <div class="srch__recall-col">
                            <p class="srch__recall-title">
                                <i class="ph ph-clock-counter-clockwise" aria-hidden="true"></i>
                                {{ __('Recent searches') }}
                            </p>
                            <div class="srch__recall-list">
                                <a href="#" class="srch__recall-item">
                                    <i class="ph ph-arrow-counter-clockwise" aria-hidden="true"></i>
                                    {{ __('Orthodontists · Dallas, TX') }}
                                </a>
                                <a href="#" class="srch__recall-item">
                                    <i class="ph ph-arrow-counter-clockwise" aria-hidden="true"></i>
                                    {{ __('Law firms · Seattle, WA') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="srch__results{{ isset($results) ? '' : ' is-hidden' }}" data-search-results>
                    <div class="srch__results-head">
                        <div class="min-w-0">
                            <p class="srch__results-title">
                                <span class="numeric" data-estimate-count>{{ isset($results) ? count($results) : 0 }}</span>
                                {{ __('businesses found') }}
                            </p>
                            <p class="srch__cost" data-estimate-summary>
                                <span class="srch__cost-val">
                                    <span class="numeric" data-estimate-cost>{{ isset($results) ? count($results) : 0 }}</span>
                                    {{ __('credits to enrich them') }}
                                </span>
                                <span class="srch__cost-sep" aria-hidden="true">·</span>
                                <span class="srch__cost-after">
                                    {{ __('leaves') }}
                                    <span class="numeric" data-estimate-left>{{ app(\App\Modules\Credits\Services\CreditLedger::class)->balance(auth()->user()) - (isset($results) ? count($results) : 0) }}</span>
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
                                <input type="search" class="form-input" placeholder="{{ __('Search these results') }}" aria-label="{{ __('Search these results') }}" data-list-search />
                            </div>

                            <p class="list-count">
                                <span class="numeric" data-list-count>5</span> {{ __('found') }}
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
                                            <input type="checkbox" class="form-check" data-bulk-all aria-label="{{ __('Select all visible results') }}" />
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
                                        <tr data-list-key="found" data-score="{{ $bucket }}" data-contact="no" data-place-id="{{ $place->id }}">
                                            <td class="d-table__check">
                                                <input type="checkbox" name="place_id[]" value="{{ $place->id }}" form="save-to-leads-form" class="form-check" data-bulk-item aria-label="{{ __('Select :name', ['name' => $place->name]) }}" />
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
                                                    <button type="button" class="btn btn-sm btn-outline" data-result-open>
                                                        <span class="btn__label">
                                                            <span>{{ __('Open') }}</span>
                                                            <span aria-hidden="true">{{ __('Open') }}</span>
                                                        </span>
                                                    </button>
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
                </section>
            </div>
        </div>

        @if (isset($searchRun) && \Illuminate\Support\Facades\Route::has('user.leads.save-from-search'))
            <form id="save-to-leads-form" action="{{ route('user.leads.save-from-search') }}" method="post">
                @csrf
                <input type="hidden" name="search_run_id" value="{{ $searchRun->id }}" />
            </form>
        @endif
    </form>

    @push('modals')
        <div class="modal" id="saveSearchModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="saveSearchTitle">
                <form action="{{ route('user.search.new') }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="saveSearchTitle">{{ __('Save this search') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Saved searches can be re-run later, and we flag businesses that have opened since last time.') }}
                    </p>

                    <div>
                        <label for="save-name" class="form-label">{{ __('Name it') }}</label>
                        <input type="text" id="save-name" name="name" class="form-input" placeholder="{{ __('Austin dentists') }}" required />
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            <span class="btn__label">
                                <span>{{ __('Cancel') }}</span>
                                <span aria-hidden="true">{{ __('Cancel') }}</span>
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn__label">
                                <span>{{ __('Save search') }}</span>
                                <span aria-hidden="true">{{ __('Save search') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
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
</x-layouts.user>

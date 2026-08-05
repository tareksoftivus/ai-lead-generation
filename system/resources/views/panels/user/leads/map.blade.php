<x-layouts.user :title="__('Map view')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Map view') }}</h2>
        <p class="m-text mt-1">
            {{ __('Your leads by location. Pick a pin to see who is worth a visit while you are in the area.') }}
        </p>
    </div>

    <div class="mapview" data-map-view data-list>
        <div class="panel">
            <div class="list-toolbar">
                <label for="m-search" class="sr-only">{{ __('Search leads') }}</label>
                <div class="search-field">
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" id="m-search" class="form-input" placeholder="{{ __('Search by business name') }}" data-list-search />
                </div>

                <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="score" data-value="all">
                    <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                        <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any score') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">
                            {{ __('Any score') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="hi">
                            {{ __('80 and above') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="mid">
                            {{ __('60 to 79') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="lo">
                            {{ __('Below 60') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <p class="list-count">
                    <span class="numeric" data-map-count>6</span> {{ __('on the map') }}
                </p>
            </div>
        </div>

        <div class="mapview__grid">
            <section class="mapview__map">
                <div class="mapcard">
                    <div class="mapcard__head">
                        <h3 class="panel__title">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Austin, TX') }}
                        </h3>

                        <div class="mapcard__legend">
                            <span class="mapcard__key">
                                <span class="mapcard__swatch mapcard__swatch--hi" aria-hidden="true"></span>
                                <span class="numeric">80</span>+
                            </span>
                            <span class="mapcard__key">
                                <span class="mapcard__swatch mapcard__swatch--mid" aria-hidden="true"></span>
                                <span class="numeric">60</span>–<span class="numeric">79</span>
                            </span>
                            <span class="mapcard__key">
                                <span class="mapcard__swatch mapcard__swatch--raw" aria-hidden="true"></span>
                                {{ __('Below') }} <span class="numeric">60</span>
                            </span>
                        </div>
                    </div>

                    <div
                        class="map"
                        data-map
                        data-map-center="30.2672,-97.7431"
                        data-map-zoom="12"
                        data-map-detail="{{ route('user.leads.show', ['lead' => 'barton-springs-dental']) }}"
                        data-map-pins="30.2540,-97.7660,92,{{ __('Barton Springs Dental') }};30.2711,-97.7437,88,{{ __('Lamar Family Dentistry') }};30.2849,-97.7341,84,{{ __('Hyde Park Dental Care') }};30.2461,-97.7523,79,{{ __('Zilker Smile Studio') }};30.2601,-97.7130,64,{{ __('Congress Ave Dental') }};30.2280,-97.7690,52,{{ __('Sunset Valley Dental') }}"
                        aria-label="{{ __('Map of your leads') }}"
                    ></div>
                </div>
            </section>

            <aside class="mapview__rail">
                <div class="panel">
                    <div class="panel__head">
                        <h3 class="panel__title">{{ __('Results') }}</h3>
                        <a href="{{ route('user.leads.index') }}" class="panel__link">
                            {{ __('Open as table') }}
                            <i class="ph ph-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="mapview__rail-list" data-list-table>
                        <article class="mrow" data-list-key="all" data-score="hi" data-lead-name="{{ __('Barton Springs Dental') }}">
                            <span class="score score--hi numeric">92</span>
                            <span class="min-w-0 flex-1">
                                <a href="#" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ __('Barton Springs Dental') }}
                                </a>
                                <span class="mrow__meta">
                                    <span class="status status--new">{{ __('New') }}</span>
                                    <span class="mrow__where">
                                        <i class="ph ph-envelope-simple" aria-hidden="true"></i>
                                        {{ __('Email on file') }}
                                    </span>
                                </span>
                            </span>
                        </article>

                        <article class="mrow" data-list-key="all" data-score="hi" data-lead-name="{{ __('Lamar Family Dentistry') }}">
                            <span class="score score--hi numeric">88</span>
                            <span class="min-w-0 flex-1">
                                <a href="#" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ __('Lamar Family Dentistry') }}
                                </a>
                                <span class="mrow__meta">
                                    <span class="status status--contacted">{{ __('Contacted') }}</span>
                                    <span class="mrow__where">
                                        <i class="ph ph-envelope-simple" aria-hidden="true"></i>
                                        {{ __('Email on file') }}
                                    </span>
                                </span>
                            </span>
                        </article>

                        <article class="mrow" data-list-key="all" data-score="hi" data-lead-name="{{ __('Hyde Park Dental Care') }}">
                            <span class="score score--hi numeric">84</span>
                            <span class="min-w-0 flex-1">
                                <a href="#" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ __('Hyde Park Dental Care') }}
                                </a>
                                <span class="mrow__meta">
                                    <span class="status status--new">{{ __('New') }}</span>
                                    <span class="mrow__where">
                                        <i class="ph ph-phone" aria-hidden="true"></i>
                                        {{ __('Phone only') }}
                                    </span>
                                </span>
                            </span>
                        </article>

                        <article class="mrow" data-list-key="all" data-score="mid" data-lead-name="{{ __('Zilker Smile Studio') }}">
                            <span class="score score--mid numeric">79</span>
                            <span class="min-w-0 flex-1">
                                <a href="#" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ __('Zilker Smile Studio') }}
                                </a>
                                <span class="mrow__meta">
                                    <span class="status status--contacted">{{ __('Contacted') }}</span>
                                    <span class="mrow__where">
                                        <i class="ph ph-envelope-simple" aria-hidden="true"></i>
                                        {{ __('Email on file') }}
                                    </span>
                                </span>
                            </span>
                        </article>

                        <article class="mrow" data-list-key="all" data-score="mid" data-lead-name="{{ __('Congress Ave Dental') }}">
                            <span class="score score--mid numeric">64</span>
                            <span class="min-w-0 flex-1">
                                <a href="#" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ __('Congress Ave Dental') }}
                                </a>
                                <span class="mrow__meta">
                                    <span class="status status--new">{{ __('New') }}</span>
                                    <span class="mrow__where">
                                        <i class="ph ph-phone" aria-hidden="true"></i>
                                        {{ __('Phone only') }}
                                    </span>
                                </span>
                            </span>
                        </article>

                        <article class="mrow" data-list-key="all" data-score="lo" data-lead-name="{{ __('Sunset Valley Dental') }}">
                            <span class="score score--lo numeric">52</span>
                            <span class="min-w-0 flex-1">
                                <a href="#" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ __('Sunset Valley Dental') }}
                                </a>
                                <span class="mrow__meta">
                                    <span class="status status--new">{{ __('New') }}</span>
                                    <span class="mrow__where">
                                        <i class="ph ph-phone" aria-hidden="true"></i>
                                        {{ __('Phone only') }}
                                    </span>
                                </span>
                            </span>
                        </article>
                    </div>

                    <div class="mrail-foot">
                        <p class="text-[0.8125rem] text-body">
                            <span class="numeric">4</span> {{ __('new') }} ·
                            <span class="numeric">2</span> {{ __('contacted') }}
                        </p>
                        <a href="#" class="btn btn-primary btn-sm">
                            <span class="btn__label">
                                <span>{{ __('Export these leads') }}</span>
                                <span aria-hidden="true">{{ __('Export these leads') }}</span>
                            </span>
                            <i class="ph ph-download-simple"></i>
                        </a>
                    </div>

                    <div class="no-results is-hidden" data-list-empty>
                        <span class="no-results__icon" aria-hidden="true">
                            <i class="ph ph-funnel"></i>
                        </span>
                        <p class="no-results__title">{{ __('Nothing on the map') }}</p>
                        <p class="no-results__body">
                            {{ __('No lead matches those filters. Widen the score range or clear the search.') }}
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.user>
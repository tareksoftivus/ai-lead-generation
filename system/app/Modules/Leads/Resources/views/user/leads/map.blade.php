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
                    <span class="numeric" data-map-count>{{ $leads->count() }}</span> {{ __('on the map') }}
                </p>
            </div>
        </div>

        <div class="mapview__grid">
            <section class="mapview__map">
                <div class="mapcard">
                    <div class="mapcard__head">
                        <h3 class="panel__title">
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ __('Your leads') }}
                        </h3>

                        <div class="mapcard__legend">
                            <span class="mapcard__key">
                                <span class="mapcard__swatch mapcard__swatch--hi" aria-hidden="true"></span>
                                <span class="numeric">80</span>+
                            </span>
                            <span class="mapcard__key">
                                <span class="mapcard__swatch mapcard__swatch--mid" aria-hidden="true"></span>
                                <span class="numeric">50</span>–<span class="numeric">79</span>
                            </span>
                            <span class="mapcard__key">
                                <span class="mapcard__swatch mapcard__swatch--raw" aria-hidden="true"></span>
                                {{ __('Below') }} <span class="numeric">50</span>
                            </span>
                        </div>
                    </div>

                    @php
                        $pinned = $leads->filter(fn ($lead) => $lead->place?->lat && $lead->place?->lng);
                        $center = $pinned->first()?->place;
                        $pins = $pinned->map(fn ($lead) => "{$lead->place->lat},{$lead->place->lng},{$lead->score},{$lead->place->name}")->implode(';');
                    @endphp
                    <div
                        class="map"
                        data-map
                        data-map-center="{{ $center ? "{$center->lat},{$center->lng}" : '30.2672,-97.7431' }}"
                        data-map-zoom="12"
                        data-map-pins="{{ $pins }}"
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
                        @foreach ($leads as $lead)
                            @php $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($lead->score); @endphp
                            <article class="mrow" data-list-key="all" data-score="{{ $bucket }}" data-lead-name="{{ $lead->place?->name }}">
                                <span class="score score--{{ $bucket }} numeric">{{ $lead->score }}</span>
                                <span class="min-w-0 flex-1">
                                    <a href="{{ route('user.leads.show', $lead) }}" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                        {{ $lead->place?->name }}
                                    </a>
                                    <span class="mrow__meta">
                                        <span class="status status--{{ $lead->status }}">{{ \App\Modules\Leads\Models\Lead::statuses()[$lead->status]['label'] ?? $lead->status }}</span>
                                        <span class="mrow__where">
                                            <i class="ph {{ $lead->hasContact() ? 'ph-envelope-simple' : 'ph-phone' }}" aria-hidden="true"></i>
                                            {{ $lead->hasContact() ? __('Email on file') : __('Phone only') }}
                                        </span>
                                    </span>
                                </span>
                            </article>
                        @endforeach
                    </div>

                    <div class="mrail-foot">
                        <p class="text-[0.8125rem] text-body">
                            <span class="numeric">{{ $leads->where('status', 'new')->count() }}</span> {{ __('new') }} ·
                            <span class="numeric">{{ $leads->where('status', 'contacted')->count() }}</span> {{ __('contacted') }}
                        </p>
                    </div>

                    <div class="no-results{{ $leads->isEmpty() ? '' : ' is-hidden' }}" data-list-empty>
                        <span class="no-results__icon" aria-hidden="true">
                            <i class="ph ph-funnel"></i>
                        </span>
                        <p class="no-results__title">{{ __('Nothing on the map') }}</p>
                        <p class="no-results__body">
                            {{ __('No leads yet. Run a search to find some.') }}
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.user>
<x-layouts.user :title="__('Map view')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Map view') }}</h2>
    </div>

    <div class="mapview" data-map-view data-list>
        <div class="panel{{ $leads->isEmpty() ? ' is-hidden' : '' }}">
            <div class="list-toolbar">
                <label for="m-search" class="sr-only">{{ __('Search leads') }}</label>
                <div class="search-field">
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" id="m-search" class="form-input" placeholder="{{ __('Search by business, address, tag, or list') }}" data-list-search />
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
                            {{ __('50 to 79') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="lo">
                            {{ __('Below 50') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="status" data-value="all">
                    <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                        <i class="ph ph-flag" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any status') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">
                            {{ __('Any status') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        @foreach ($statuses as $status => $meta)
                            <button type="button" class="menu__item" data-value="{{ $status }}">
                                {{ __($meta['label']) }}
                                <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="contact" data-value="all">
                    <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                        <i class="ph ph-envelope-simple" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any contact') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">
                            {{ __('Any contact') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="yes">
                            {{ __('Has an email') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="no">
                            {{ __('No email found') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                @if ($lists->isNotEmpty())
                    <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="list" data-value="all">
                        <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                            <i class="ph ph-list-bullets" aria-hidden="true"></i>
                            <span data-dropdown-label>{{ __('Any list') }}</span>
                            <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                        </button>

                        <div class="menu__panel" data-dropdown-panel>
                            <button type="button" class="menu__item is-selected" data-value="all">
                                {{ __('Any list') }}
                                <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                            </button>
                            @foreach ($lists as $list)
                                <button type="button" class="menu__item" data-value="list-{{ $list->id }}">
                                    {{ $list->name }}
                                    <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mapview__grid{{ $leads->isEmpty() ? ' is-hidden' : '' }}">
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

                    <div
                        class="map"
                        data-map
                        data-map-leads='@json($mapLeads)'
                        data-map-center="{{ $mapCenter['lat'] }},{{ $mapCenter['lng'] }}"
                        data-map-zoom="12"
                        aria-label="{{ __('Map of your leads') }}"
                    >
                        <div class="map__empty{{ $mapLeads->isEmpty() ? '' : ' is-hidden' }}" data-map-empty>
                            <span class="no-results__icon" aria-hidden="true">
                                <i class="ph ph-map-pin"></i>
                            </span>
                            <p class="no-results__title">{{ __('No mapped leads') }}</p>
                            <p class="no-results__body">{{ __('These leads do not have coordinates yet. Generate or import leads with map locations to plot them here.') }}</p>
                        </div>
                    </div>
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

                    <div class="mapview__rail-list{{ $mapLeads->isEmpty() ? ' is-hidden' : '' }}" data-list-table>
                        @foreach ($leads as $lead)
                            @php
                                $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($lead->score);
                                $hasCoordinates = $lead->place && $lead->place->lat !== null && $lead->place->lng !== null;
                                $listKeys = $lead->lists->map(fn ($list) => 'list-'.$list->id)->implode(' ');
                            @endphp
                            @continue(! $hasCoordinates)
                            <article
                                class="mrow"
                                data-list-key="all"
                                data-score="{{ $bucket }}"
                                data-status="{{ $lead->status }}"
                                data-contact="{{ $lead->hasContact() ? 'yes' : 'no' }}"
                                data-list="{{ $listKeys }}"
                                data-lead-id="{{ $lead->id }}"
                            >
                                <span class="score score--{{ $bucket }} numeric">{{ $lead->score }}</span>
                                <span class="min-w-0 flex-1">
                                    <a href="{{ route('user.leads.show', $lead) }}" class="block truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                        {{ $lead->place?->name }}
                                    </a>
                                    <span class="mrow__address">{{ $lead->place?->formatted_address }}</span>
                                    <span class="mrow__meta">
                                        <span class="status status--{{ $lead->status }}">{{ $statuses[$lead->status]['label'] ?? $lead->status }}</span>
                                        <span class="mrow__where">
                                            <i class="ph {{ $lead->hasContact() ? 'ph-envelope-simple' : 'ph-phone' }}" aria-hidden="true"></i>
                                            {{ $lead->hasContact() ? __('Email on file') : __('Phone only') }}
                                        </span>
                                        @foreach ($lead->tags->take(2) as $tag)
                                            <span class="tag-pill">{{ $tag->name }}</span>
                                        @endforeach
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

                    <div class="no-results{{ $mapLeads->isEmpty() ? '' : ' is-hidden' }}" data-list-empty>
                        <span class="no-results__icon" aria-hidden="true">
                            <i class="ph ph-funnel"></i>
                        </span>
                        <p class="no-results__title">{{ __('Nothing on the map') }}</p>
                        <p class="no-results__body">
                            {{ $leads->isEmpty() ? __('No leads yet. Run a search to find some.') : __('Try a different search term, status, score, contact, or list filter.') }}
                        </p>
                    </div>
                </div>
            </aside>
        </div>

        <section class="panel empty" @if (! $leads->isEmpty()) hidden @endif>
            <span class="empty__icon" aria-hidden="true">
                <i class="ph ph-map-trifold"></i>
            </span>
            <h2 class="empty__title">{{ __('No leads yet') }}</h2>
            <p class="empty__body">
                {{ __('Run a search and saved businesses with coordinates will appear on this map.') }}
            </p>
            <a href="{{ route('user.search.new') }}" class="btn btn-primary">
                <span class="btn__label">
                    <span>{{ __('Find leads') }}</span>
                    <span aria-hidden="true">{{ __('Find leads') }}</span>
                </span>
                <i class="ph ph-arrow-right"></i>
            </a>
        </section>
    </div>
</x-layouts.user>

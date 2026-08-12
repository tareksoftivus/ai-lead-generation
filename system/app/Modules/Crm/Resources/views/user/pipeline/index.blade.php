<x-layouts.user :title="__('Sales pipeline')">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Sales pipeline') }}</h2>
        </div>

        <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm shrink-0">
            <span class="btn__label">
                <span>{{ __('Add from leads') }}</span>
                <span aria-hidden="true">{{ __('Add from leads') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </a>
    </div>

    <div class="{{ $leads->isEmpty() ? 'is-hidden' : '' }}" data-list>
        <div class="panel">
            <div class="list-toolbar">
                <label for="pl-search" class="sr-only">{{ __('Search the pipeline') }}</label>
                <div class="search-field">
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" id="pl-search" class="form-input" placeholder="{{ __('Search by business name') }}" data-list-search />
                </div>

                <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="score" data-value="all">
                    <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                        <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any score') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">{{ __('Any score') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                        <button type="button" class="menu__item" data-value="hi">{{ __('80 and above') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                        <button type="button" class="menu__item" data-value="mid">{{ __('50 to 79') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                        <button type="button" class="menu__item" data-value="lo">{{ __('Below 50') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                    </div>
                </div>

                <div class="menu shrink-0" data-dropdown data-dropdown-select data-list-filter="tag" data-value="all">
                    <button type="button" class="filter-btn" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                        <i class="ph ph-tag" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any tag') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">{{ __('Any tag') }}<i class="ph ph-check menu__tick" aria-hidden="true"></i></button>
                        @foreach ($tags as $tag)
                            <button type="button" class="menu__item" data-value="{{ \Illuminate\Support\Str::slug($tag->name) }}">
                                {{ $tag->name }}
                                <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div class="pipe" data-pipeline>
            @foreach ($stages as $stage)
                <section class="pipe__col" data-stage="{{ $stage['key'] }}">
                    <div class="pipe__head">
                        <h3 class="pipe__name">
                            <span class="h-2 w-2 shrink-0 rounded-full pipe__dot--{{ $stage['variant'] }}" aria-hidden="true"></span>
                            <span data-stage-name>{{ __($stage['label']) }}</span>
                        </h3>
                        <span class="rounded-full bg-neutral-0 px-2 py-0.5 text-[0.75rem] font-semibold text-body numeric" data-stage-count>{{ $stage['leads']->count() }}</span>
                    </div>

                    <div class="pipe__drop" data-stage-drop>
                        @foreach ($stage['leads'] as $lead)
                            @php
                                $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($lead->score);
                                $tagKeys = $lead->tags->map(fn ($tag) => \Illuminate\Support\Str::slug($tag->name))->implode(' ');
                            @endphp
                            <article
                                class="pcard"
                                draggable="true"
                                data-card
                                data-lead="{{ $lead->place?->name }}"
                                data-list-key="{{ $lead->status }}"
                                data-score="{{ $bucket }}"
                                data-tag="{{ $tagKeys }}"
                                data-update-url="{{ route('user.pipeline.update-status', $lead) }}"
                                data-remove-url="{{ route('user.pipeline.remove', $lead) }}"
                            >
                                <div class="pcard__top">
                                    <a href="{{ route('user.leads.show', $lead) }}" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                        {{ $lead->place?->name ?? __('Untitled lead') }}
                                    </a>
                                    <span class="score score--{{ $bucket }} numeric">{{ $lead->score ?? 0 }}</span>
                                </div>

                                <p class="pcard__when">
                                    <i class="ph ph-clock-counter-clockwise" aria-hidden="true"></i>
                                    {{ __('Updated :time', ['time' => $lead->updated_at?->diffForHumans()]) }}
                                </p>

                                <div class="pcard__foot">
                                    <span class="status status--{{ $lead->status }}" data-card-status>{{ __($stage['label']) }}</span>

                                    <div class="menu" data-dropdown data-dropdown-align="end">
                                        <button type="button" class="pcard__more" data-dropdown-toggle aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Actions for :name', ['name' => $lead->place?->name]) }}">
                                            <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                        </button>

                                        <div class="menu__panel" data-dropdown-panel>
                                            <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                                {{ __('Move to') }}
                                            </p>
                                            @foreach ($stages as $target)
                                                @continue($target['key'] === $stage['key'])
                                                <button type="button" class="menu__item" data-move-to="{{ $target['key'] }}">
                                                    {{ __($target['label']) }}
                                                </button>
                                            @endforeach

                                            <p class="menu__sep" role="separator"></p>

                                            <button
                                                type="button"
                                                class="menu__item menu__item--danger"
                                                data-confirm
                                                data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                                data-confirm-body="{{ __('This lead goes back to All leads. Nothing is deleted and no credits are affected.') }}"
                                                data-confirm-label="{{ __('Remove card') }}"
                                                data-confirm-variant="error"
                                                data-remove-card
                                            >
                                                <i class="ph ph-trash" aria-hidden="true"></i>
                                                {{ __('Remove from pipeline') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <p class="pipe__empty">{{ __('Nothing here yet.') }}</p>
                </section>
            @endforeach
        </div>

        <div class="no-results is-hidden" data-list-empty>
            <span class="no-results__icon" aria-hidden="true">
                <i class="ph ph-funnel"></i>
            </span>
            <p class="no-results__title">{{ __('No pipeline cards match') }}</p>
            <p class="no-results__body">{{ __('Try another score, tag, or business name.') }}</p>
        </div>
    </div>

    <section class="panel empty" @if (! $leads->isEmpty()) hidden @endif>
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-kanban"></i>
        </span>
        <h2 class="empty__title">{{ __('No pipeline cards yet') }}</h2>
        <p class="empty__body">
            {{ __('Save generated leads and they appear here ready to work.') }}
        </p>
        <a href="{{ route('user.search.new') }}" class="btn btn-primary btn-sm">
            <span class="btn__label">
                <span>{{ __('Generate leads') }}</span>
                <span aria-hidden="true">{{ __('Generate leads') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </section>

    @push('modals')
        <div id="confirmDialog" class="modal" role="dialog" aria-modal="true" aria-hidden="true">
            <div class="modal__backdrop"></div>
            <div class="modal__panel max-w-md p-6">
                <h2 class="heading-3" data-confirm-title-target>{{ __('Are you sure?') }}</h2>
                <p class="m-text mt-2" data-confirm-body-target>{{ __('This action cannot be undone.') }}</p>
                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button" class="btn btn-outline" data-confirm-cancel>{{ __('Cancel') }}</button>
                    <button type="button" class="btn confirm-accept" data-confirm-accept>{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    @endpush
</x-layouts.user>

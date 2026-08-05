<x-layouts.user :title="__('Sales pipeline')">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="heading-3">{{ __('Sales pipeline') }}</h2>
            <p class="m-text mt-1">
                {{ __('The leads you are actually working. Move a card as the conversation moves.') }}
            </p>
        </div>

        <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm shrink-0">
            <span class="btn__label">
                <span>{{ __('Add from leads') }}</span>
                <span aria-hidden="true">{{ __('Add from leads') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </a>
    </div>

    <div data-list>
        {{-- Filters. Same component and the same meaning as screen 18. --}}
        <div class="panel">
            <div class="list-toolbar">
                <label for="pl-search" class="sr-only">{{ __('Search the pipeline') }}</label>
                <div class="search-field">
                    <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                    <input
                        type="search"
                        id="pl-search"
                        class="form-input"
                        placeholder="{{ __('Search by business name') }}"
                        data-list-search
                    />
                </div>

                <div
                    class="menu shrink-0"
                    data-dropdown
                    data-dropdown-select
                    data-list-filter="score"
                    data-value="all"
                >
                    <button
                        type="button"
                        class="filter-btn"
                        data-dropdown-toggle
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
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

                <div
                    class="menu shrink-0"
                    data-dropdown
                    data-dropdown-select
                    data-list-filter="tag"
                    data-value="all"
                >
                    <button
                        type="button"
                        class="filter-btn"
                        data-dropdown-toggle
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="ph ph-tag" aria-hidden="true"></i>
                        <span data-dropdown-label>{{ __('Any tag') }}</span>
                        <i class="ph ph-caret-down filter-btn__caret" aria-hidden="true"></i>
                    </button>

                    <div class="menu__panel" data-dropdown-panel>
                        <button type="button" class="menu__item is-selected" data-value="all">
                            {{ __('Any tag') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="high-value">
                            {{ __('High value') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="follow-up">
                            {{ __('Follow up') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="menu__item" data-value="hot">
                            {{ __('Hot') }}
                            <i class="ph ph-check menu__tick" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <p class="list-count">
                    <span class="numeric" data-list-count>9</span> {{ __('in the pipeline') }}
                </p>
            </div>
        </div>

        {{-- The board --}}
        <div class="pipe" data-pipeline>
            {{-- NEW --}}
            <section class="pipe__col" data-stage="new">
                <div class="pipe__head">
                    <h3 class="pipe__name">
                        <span class="h-2 w-2 shrink-0 rounded-full pipe__dot--new" aria-hidden="true"></span>
                        <span data-stage-name>{{ __('New') }}</span>
                    </h3>
                    <span class="rounded-full bg-neutral-0 px-2 py-0.5 text-[0.75rem] font-semibold text-body numeric" data-stage-count>2</span>
                </div>

                <div class="pipe__drop" data-stage-drop>
                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Sunset Valley Dental"
                        data-list-key="all"
                        data-score="lo"
                        data-tag="follow-up"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Sunset Valley Dental') }}
                            </a>
                            <span class="score score--lo numeric">52</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-clock-counter-clockwise" aria-hidden="true"></i>
                            {{ __('Added 2 days ago') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--new" data-card-status>{{ __('New') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Sunset Valley Dental') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="contacted">
                                        {{ __('Contacted') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="replied">
                                        {{ __('Replied') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Sunset Valley Dental')) }}"
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

                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Congress Ave Dental"
                        data-list-key="all"
                        data-score="mid"
                        data-tag="follow-up"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Congress Ave Dental') }}
                            </a>
                            <span class="score score--mid numeric">64</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-clock-counter-clockwise" aria-hidden="true"></i>
                            {{ __('Added 3 days ago') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--new" data-card-status>{{ __('New') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Congress Ave Dental') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="contacted">
                                        {{ __('Contacted') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="replied">
                                        {{ __('Replied') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Congress Ave Dental')) }}"
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
                </div>

                <p class="pipe__empty">{{ __('Nothing here yet.') }}</p>
            </section>

            {{-- CONTACTED --}}
            <section class="pipe__col" data-stage="contacted">
                <div class="pipe__head">
                    <h3 class="pipe__name">
                        <span class="h-2 w-2 shrink-0 rounded-full pipe__dot--contacted" aria-hidden="true"></span>
                        <span data-stage-name>{{ __('Contacted') }}</span>
                    </h3>
                    <span class="rounded-full bg-neutral-0 px-2 py-0.5 text-[0.75rem] font-semibold text-body numeric" data-stage-count>3</span>
                </div>

                <div class="pipe__drop" data-stage-drop>
                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Barton Springs Dental"
                        data-list-key="all"
                        data-score="hi"
                        data-tag="high-value"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Barton Springs Dental') }}
                            </a>
                            <span class="score score--hi numeric">92</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-envelope-simple" aria-hidden="true"></i>
                            {{ __('Emailed 2 days ago') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--contacted" data-card-status>{{ __('Contacted') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Barton Springs Dental') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="new">
                                        {{ __('New') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="replied">
                                        {{ __('Replied') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Barton Springs Dental')) }}"
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

                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Hyde Park Dental Care"
                        data-list-key="all"
                        data-score="hi"
                        data-tag="hot"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Hyde Park Dental Care') }}
                            </a>
                            <span class="score score--hi numeric">84</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-envelope-simple" aria-hidden="true"></i>
                            {{ __('Emailed 4 days ago') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--contacted" data-card-status>{{ __('Contacted') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Hyde Park Dental Care') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="new">
                                        {{ __('New') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="replied">
                                        {{ __('Replied') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Hyde Park Dental Care')) }}"
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

                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Round Rock Family Dental"
                        data-list-key="all"
                        data-score="mid"
                        data-tag="follow-up"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Round Rock Family Dental') }}
                            </a>
                            <span class="score score--mid numeric">71</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-phone" aria-hidden="true"></i>
                            {{ __('Called 1 week ago') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--contacted" data-card-status>{{ __('Contacted') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Round Rock Family Dental') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="new">
                                        {{ __('New') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="replied">
                                        {{ __('Replied') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Round Rock Family Dental')) }}"
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
                </div>

                <p class="pipe__empty">{{ __('Nothing here yet.') }}</p>
            </section>

            {{-- REPLIED --}}
            <section class="pipe__col" data-stage="replied">
                <div class="pipe__head">
                    <h3 class="pipe__name">
                        <span class="h-2 w-2 shrink-0 rounded-full pipe__dot--replied" aria-hidden="true"></span>
                        <span data-stage-name>{{ __('Replied') }}</span>
                    </h3>
                    <span class="rounded-full bg-neutral-0 px-2 py-0.5 text-[0.75rem] font-semibold text-body numeric" data-stage-count>2</span>
                </div>

                <div class="pipe__drop" data-stage-drop>
                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Lamar Family Dentistry"
                        data-list-key="all"
                        data-score="hi"
                        data-tag="high-value"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Lamar Family Dentistry') }}
                            </a>
                            <span class="score score--hi numeric">88</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-arrow-bend-up-left" aria-hidden="true"></i>
                            {{ __('Replied yesterday') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--replied" data-card-status>{{ __('Replied') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Lamar Family Dentistry') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="new">
                                        {{ __('New') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="contacted">
                                        {{ __('Contacted') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Lamar Family Dentistry')) }}"
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

                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Zilker Smile Studio"
                        data-list-key="all"
                        data-score="mid"
                        data-tag="hot"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Zilker Smile Studio') }}
                            </a>
                            <span class="score score--mid numeric">79</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-arrow-bend-up-left" aria-hidden="true"></i>
                            {{ __('Replied 3 days ago') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--replied" data-card-status>{{ __('Replied') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Zilker Smile Studio') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="new">
                                        {{ __('New') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="contacted">
                                        {{ __('Contacted') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Zilker Smile Studio')) }}"
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
                </div>

                <p class="pipe__empty">{{ __('Nothing here yet.') }}</p>
            </section>

            {{-- QUALIFIED --}}
            <section class="pipe__col" data-stage="qualified">
                <div class="pipe__head">
                    <h3 class="pipe__name">
                        <span class="h-2 w-2 shrink-0 rounded-full pipe__dot--qualified" aria-hidden="true"></span>
                        <span data-stage-name>{{ __('Qualified') }}</span>
                    </h3>
                    <span class="rounded-full bg-neutral-0 px-2 py-0.5 text-[0.75rem] font-semibold text-body numeric" data-stage-count>1</span>
                </div>

                <div class="pipe__drop" data-stage-drop>
                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Mueller Dental Studio"
                        data-list-key="all"
                        data-score="hi"
                        data-tag="high-value"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Mueller Dental Studio') }}
                            </a>
                            <span class="score score--hi numeric">90</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-calendar-check" aria-hidden="true"></i>
                            {{ __('Call booked for Friday') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--qualified" data-card-status>{{ __('Qualified') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Mueller Dental Studio') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="contacted">
                                        {{ __('Contacted') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="replied">
                                        {{ __('Replied') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="won">
                                        {{ __('Won') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Mueller Dental Studio')) }}"
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
                </div>

                <p class="pipe__empty">{{ __('Nothing here yet.') }}</p>
            </section>

            {{-- WON --}}
            <section class="pipe__col" data-stage="won">
                <div class="pipe__head">
                    <h3 class="pipe__name">
                        <span class="h-2 w-2 shrink-0 rounded-full pipe__dot--won" aria-hidden="true"></span>
                        <span data-stage-name>{{ __('Won') }}</span>
                    </h3>
                    <span class="rounded-full bg-neutral-0 px-2 py-0.5 text-[0.75rem] font-semibold text-body numeric" data-stage-count>1</span>
                </div>

                <div class="pipe__drop" data-stage-drop>
                    <article
                        class="pcard"
                        draggable="true"
                        data-card
                        data-lead="Clarksville Dental Co"
                        data-list-key="all"
                        data-score="hi"
                        data-tag="high-value"
                    >
                        <div class="pcard__top">
                            <a href="#" class="min-w-0 text-[0.875rem] font-semibold text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Clarksville Dental Co') }}
                            </a>
                            <span class="score score--hi numeric">86</span>
                        </div>

                        <p class="pcard__when">
                            <i class="ph ph-check-circle" aria-hidden="true"></i>
                            {{ __('Signed 2 weeks ago') }}
                        </p>

                        <div class="pcard__foot">
                            <span class="status status--won" data-card-status>{{ __('Won') }}</span>

                            <div class="menu" data-dropdown data-dropdown-align="end">
                                <button
                                    type="button"
                                    class="pcard__more"
                                    data-dropdown-toggle
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="{{ __('Actions for') }} {{ __('Clarksville Dental Co') }}"
                                >
                                    <i class="ph ph-dots-three-vertical" aria-hidden="true"></i>
                                </button>

                                <div class="menu__panel" data-dropdown-panel>
                                    <p class="px-3 pt-1.5 pb-1 text-[0.6875rem] font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ __('Move to') }}
                                    </p>
                                    <button type="button" class="menu__item" data-move-to="qualified">
                                        {{ __('Qualified') }}
                                    </button>
                                    <button type="button" class="menu__item" data-move-to="lost">
                                        {{ __('Lost') }}
                                    </button>

                                    <p class="menu__sep" role="separator"></p>

                                    <button
                                        type="button"
                                        class="menu__item menu__item--danger"
                                        data-confirm
                                        data-confirm-title="{{ __('Remove from the pipeline?') }}"
                                        data-confirm-body="{{ sprintf(__('"%s" goes back to your leads. Nothing is deleted and no credits are affected — only the pipeline card is removed.'), __('Clarksville Dental Co')) }}"
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
                </div>

                <p class="pipe__empty">{{ __('Nothing here yet.') }}</p>
            </section>

            {{-- LOST --}}
            <section class="pipe__col" data-stage="lost">
                <div class="pipe__head">
                    <h3 class="pipe__name">
                        <span class="h-2 w-2 shrink-0 rounded-full pipe__dot--lost" aria-hidden="true"></span>
                        <span data-stage-name>{{ __('Lost') }}</span>
                    </h3>
                    <span class="rounded-full bg-neutral-0 px-2 py-0.5 text-[0.75rem] font-semibold text-body numeric" data-stage-count>0</span>
                </div>

                <div class="pipe__drop" data-stage-drop></div>

                <p class="pipe__empty">{{ __('Nothing here yet.') }}</p>
            </section>
        </div>
    </div>

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

<x-layouts.user :title="__('Lead details')">
    <a href="{{ route('user.leads.index') }}" class="back-link">
        <i class="ph ph-arrow-left" aria-hidden="true"></i>
        {{ __('All leads') }}
    </a>

    <div class="lead-head">
        <div class="lead-head__id">
            <span class="lead-head__mark" aria-hidden="true">
                <i class="ph ph-tooth"></i>
            </span>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="heading-3">{{ __('Barton Springs Dental') }}</h2>
                    <span class="status status--new">{{ __('New') }}</span>
                </div>

                <p class="lead-head__meta">
                    <span>
                        <i class="ph ph-map-pin" aria-hidden="true"></i>
                        {{ __('1401 S Lamar Blvd, Austin, TX') }}
                    </span>
                    <a href="tel:+15125550143">
                        <span class="numeric">(512) 555-0143</span>
                    </a>
                    <a href="#" rel="noreferrer">bartonspringsdental.com</a>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-1.5 md:justify-end">
            <button type="button" class="btn btn-sm btn-outline" data-modal-open="noteModal">
                <span class="btn__label">
                    <span>{{ __('Add note') }}</span>
                    <span aria-hidden="true">{{ __('Add note') }}</span>
                </span>
                <i class="ph ph-note-pencil"></i>
            </button>

            <button type="button" class="btn btn-sm btn-outline" data-modal-open="statusModal">
                <span class="btn__label">
                    <span>{{ __('Set status') }}</span>
                    <span aria-hidden="true">{{ __('Set status') }}</span>
                </span>
                <i class="ph ph-flag"></i>
            </button>

            <a href="{{ route('user.export.index') }}" class="btn btn-sm btn-primary">
                <span class="btn__label">
                    <span>{{ __('Export') }}</span>
                    <span aria-hidden="true">{{ __('Export') }}</span>
                </span>
                <i class="ph ph-download-simple"></i>
            </a>

            <button type="button" class="row-icon row-icon--danger" aria-label="{{ __('Delete this lead') }}"
                    data-confirm data-confirm-title="{{ __('Delete this lead?') }}"
                    data-confirm-body="{{ __('It is removed from your account and from any list it belongs to. The credit already spent enriching it is not returned.') }}"
                    data-confirm-label="{{ __('Delete lead') }}" data-confirm-variant="error">
                <i class="ph ph-trash" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="lead-grid">
        <div class="lead-grid__main">
            <section class="@container rounded-2xl border border-ai/25 bg-ai/4 p-5 md:p-6">
                <div class="verdict-card__top">
                    <span class="verdict-card__score numeric">92</span>

                    <div class="min-w-0">
                        <p class="verdict-card__key">
                            <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                            {{ __('Scored by AI') }}
                        </p>
                        <p class="mt-0.5 font-title text-[1.125rem] font-bold text-title">
                            {{ __('Busy practice with no way to book online.') }}
                        </p>
                    </div>
                </div>

                <p class="mt-4 border-t border-ai/15 pt-4 text-[0.9375rem] leading-[1.7]">
                    {{ __('Barton Springs Dental has') }}
                    <span class="numeric">312</span> {{ __('reviews at') }}
                    <span class="numeric">4.7</span> {{ __('stars, which puts it in the top tenth of Austin practices by volume — this is a business with more demand than it can comfortably schedule. Its website has no booking flow, no patient portal, and a copyright notice reading') }}
                    <span class="numeric">2019</span>.
                    {{ __('The gap between how busy they are and how little their site does for them is the widest we found in this search.') }}
                </p>
            </section>

            <section class="form-card">
                <h3 class="form-card__title">{{ __('What the score is made of') }}</h3>
                <p class="form-card__hint">
                    {{ __('Four signals, weighted for what you told us you sell.') }}
                    <a href="{{ route('user.scoring.index') }}"
                       class="font-medium text-primary underline decoration-neutral-300 underline-offset-2 transition-colors duration-200 hover:decoration-primary">
                        {{ __('Change the weighting') }}
                    </a>
                </p>

                <div class="mt-4">
                    <div class="signal-row">
                        <p class="min-w-0 flex-1 basis-40 text-[0.875rem] font-medium text-title">
                            {{ __('Review volume') }}
                            <span class="mt-0.5 block text-[0.8125rem] font-normal text-body">
                                <span class="numeric">312</span> {{ __('reviews') }} ·
                                <span class="numeric">4.7</span> {{ __('stars') }}
                            </span>
                        </p>
                        <span class="meter meter--strong" aria-hidden="true">
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                        </span>
                        <span class="w-14 shrink-0 text-right text-[0.8125rem] text-body">{{ __('Strong') }}</span>
                    </div>

                    <div class="signal-row">
                        <p class="min-w-0 flex-1 basis-40 text-[0.875rem] font-medium text-title">
                            {{ __('Online booking') }}
                            <span class="mt-0.5 block text-[0.8125rem] font-normal text-body">
                                {{ __('None found — the gap you sell into') }}
                            </span>
                        </p>
                        <span class="meter meter--strong" aria-hidden="true">
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                        </span>
                        <span class="w-14 shrink-0 text-right text-[0.8125rem] text-body">{{ __('Strong') }}</span>
                    </div>

                    <div class="signal-row">
                        <p class="min-w-0 flex-1 basis-40 text-[0.875rem] font-medium text-title">
                            {{ __('Website age') }}
                            <span class="mt-0.5 block text-[0.8125rem] font-normal text-body">
                                {{ __('Last touched') }} <span class="numeric">2019</span>
                            </span>
                        </p>
                        <span class="meter meter--fair" aria-hidden="true">
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                        </span>
                        <span class="w-14 shrink-0 text-right text-[0.8125rem] text-body">{{ __('Fair') }}</span>
                    </div>

                    <div class="signal-row">
                        <p class="min-w-0 flex-1 basis-40 text-[0.875rem] font-medium text-title">
                            {{ __('Local competition') }}
                            <span class="mt-0.5 block text-[0.8125rem] font-normal text-body">
                                <span class="numeric">14</span> {{ __('practices within') }}
                                <span class="numeric">2</span> {{ __('miles') }}
                            </span>
                        </p>
                        <span class="meter meter--weak" aria-hidden="true">
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                            <span class="meter__seg"></span>
                        </span>
                        <span class="w-14 shrink-0 text-right text-[0.8125rem] text-body">{{ __('Weak') }}</span>
                    </div>
                </div>
            </section>

            <section class="form-card">
                <h3 class="form-card__title">{{ __('A way in') }}</h3>
                <p class="form-card__hint">
                    {{ __('Written around the gap above. Edit it, copy it, send it yourself — nothing goes out from this page.') }}
                </p>

                <div class="draft mt-4">
                    <div class="draft__head">
                        <p class="draft__key">
                            <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                            {{ __('Drafted by AI') }}
                        </p>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#draft-body">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>

                    <label for="draft-body" class="sr-only">{{ __('Drafted opening email') }}</label>
                    <textarea id="draft-body" name="draft"
                              class="w-full resize-y border-0 px-4 py-3 text-[0.9375rem] leading-[1.7] text-title focus:outline-none"
                              rows="7">
{{ __('Hi there,') }}

{{ __('I noticed Barton Springs Dental has over 300 reviews — clearly a busy practice — but there is no way for a patient to book online. Right now every appointment has to come through the phone.') }}

{{ __('We set up booking that works alongside how your front desk already runs. Most practices your size see the phone quieten within a fortnight.') }}

{{ __('Worth a short call to see if it fits?') }}</textarea>

                    <div class="draft__foot">
                        <p class="text-[0.8125rem] text-body">
                            {{ __('You read it before it goes anywhere.') }}
                        </p>
                        <a href="{{ route('user.campaigns.index') }}" class="btn btn-sm btn-outline">
                            <span class="btn__label">
                                <span>{{ __('Add to campaign') }}</span>
                                <span aria-hidden="true">{{ __('Add to campaign') }}</span>
                            </span>
                            <i class="ph ph-paper-plane-tilt"></i>
                        </a>
                    </div>
                </div>
            </section>

            <section class="form-card">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="form-card__title">{{ __('Activity') }}</h3>
                    <button type="button" class="btn btn-sm btn-outline" data-modal-open="noteModal">
                        <span class="btn__label">
                            <span>{{ __('Add note') }}</span>
                            <span aria-hidden="true">{{ __('Add note') }}</span>
                        </span>
                        <i class="ph ph-plus"></i>
                    </button>
                </div>

                <div class="timeline mt-4">
                    <article class="timeline__item">
                        <span class="timeline__dot timeline__dot--act" aria-hidden="true">
                            <i class="ph ph-tag"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[0.9375rem] font-medium text-title">
                                {{ __('Tagged “High value”') }}
                            </p>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                {{ __('by Amara Rivera') }} ·
                                <time datetime="2026-07-19">{{ __('19 July') }}</time>
                            </p>
                        </div>
                    </article>

                    <article class="timeline__item">
                        <span class="timeline__dot timeline__dot--ai" aria-hidden="true">
                            <i class="ph-fill ph-sparkle"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[0.9375rem] font-medium text-title">
                                {{ __('Scored') }} <span class="numeric">92</span>
                                {{ __('and an opening email drafted') }}
                            </p>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                {{ __('by LeadAtlas') }} ·
                                <time datetime="2026-07-19">{{ __('19 July') }}</time>
                            </p>
                        </div>
                    </article>

                    <article class="timeline__item">
                        <span class="timeline__dot" aria-hidden="true">
                            <i class="ph ph-envelope-simple"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[0.9375rem] font-medium text-title">
                                {{ __('Contact found — hello@bartonsprings.com') }}
                            </p>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                <span class="numeric">1</span> {{ __('credit') }} ·
                                <time datetime="2026-07-19">{{ __('19 July') }}</time>
                            </p>
                        </div>
                    </article>

                    <article class="timeline__item">
                        <span class="timeline__dot" aria-hidden="true">
                            <i class="ph ph-map-pin"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[0.9375rem] font-medium text-title">
                                {{ __('Found in “dentists in Austin, TX”') }}
                            </p>
                            <p class="mt-0.5 text-[0.8125rem] text-body">
                                <a href="{{ route('user.search.history') }}"
                                   class="font-medium text-primary underline decoration-neutral-300 underline-offset-2 transition-colors duration-200 hover:decoration-primary">
                                    {{ __('View that search') }}
                                </a>
                                · <time datetime="2026-07-19">{{ __('19 July') }}</time>
                            </p>
                        </div>
                    </article>
                </div>
            </section>
        </div>

        <aside class="lead-grid__side">
            <section class="mapcard">
                <div class="mapcard__head">
                    <h3 class="form-card__title">{{ __('Where it is') }}</h3>
                </div>
                <div class="map" data-map data-map-center="30.2540,-97.7660" data-map-zoom="14"
                     data-map-pins="30.2540,-97.7660,92,{{ __('Barton Springs Dental') }}"
                     aria-label="{{ __('Map showing Barton Springs Dental') }}"></div>
            </section>

            <section class="form-card">
                <h3 class="form-card__title">{{ __('Details') }}</h3>

                <div class="mt-3">
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Category') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">{{ __('Dentist') }}</p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Email') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">
                            <a href="#" class="d-table__mail">hello@bartonsprings.com</a>
                        </p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Phone') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title numeric">(512) 555-0143</p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Rating') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">
                            <span class="numeric">4.7</span> {{ __('from') }}
                            <span class="numeric">312</span> {{ __('reviews') }}
                        </p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Added') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">
                            <time datetime="2026-07-19">{{ __('19 July 2026') }}</time>
                        </p>
                    </div>
                </div>
            </section>

            <section class="form-card">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="form-card__title">{{ __('Tags') }}</h3>
                    <button type="button" class="row-icon" aria-label="{{ __('Add a tag') }}" data-modal-open="tagModal">
                        <i class="ph ph-plus" aria-hidden="true"></i>
                    </button>
                </div>
                <p class="form-card__hint">
                    {{ __('Your own labels. They do not affect the score.') }}
                </p>

                <div class="mt-3 flex flex-wrap gap-1.5">
                    <span class="tag-pill">{{ __('High value') }}</span>
                    <span class="tag-pill">{{ __('Austin') }}</span>
                    <span class="tag-pill">{{ __('No booking') }}</span>
                </div>
            </section>
        </aside>
    </div>

    @push('modals')
        <div class="modal" id="noteModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="noteModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="noteModalTitle">{{ __('Add a note') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Notes appear in the activity timeline for this lead.') }}
                    </p>

                    <div>
                        <label for="note-body" class="form-label">{{ __('Note') }}</label>
                        <textarea id="note-body" name="note" class="form-input" rows="4"
                                  placeholder="{{ __('Called the front desk — practice manager is Dana, back Thursday.') }}"
                                  required></textarea>
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
                                <span>{{ __('Save note') }}</span>
                                <span aria-hidden="true">{{ __('Save note') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="statusModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="statusModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="statusModalTitle">{{ __('Set status') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Where this lead sits in your pipeline.') }}
                    </p>

                    <div>
                        <label for="status-value" class="form-label">{{ __('Status') }}</label>
                        <select id="status-value" name="status" class="form-input" required>
                            <option value="new" selected>{{ __('New') }}</option>
                            <option value="contacted">{{ __('Contacted') }}</option>
                            <option value="replied">{{ __('Replied') }}</option>
                            <option value="qualified">{{ __('Qualified') }}</option>
                            <option value="lost">{{ __('Lost') }}</option>
                        </select>
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
                                <span>{{ __('Set status') }}</span>
                                <span aria-hidden="true">{{ __('Set status') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="tagModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="tagModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="tagModalTitle">{{ __('Add a tag') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Your own label — it does not affect the AI score.') }}
                    </p>

                    <div>
                        <label for="tag-name" class="form-label">{{ __('Tag') }}</label>
                        <input type="text" id="tag-name" name="tag" class="form-input"
                               placeholder="{{ __('Follow up in Q3') }}" list="tag-suggestions" required />
                        <datalist id="tag-suggestions">
                            <option value="{{ __('High value') }}"></option>
                            <option value="{{ __('Follow up') }}"></option>
                            <option value="{{ __('Hot') }}"></option>
                        </datalist>
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
                                <span>{{ __('Add tag') }}</span>
                                <span aria-hidden="true">{{ __('Add tag') }}</span>
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

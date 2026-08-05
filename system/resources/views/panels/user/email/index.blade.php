<x-layouts.user :title="__('Email generator')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Email generator') }}</h2>
        <p class="m-text mt-1">
            {{ __('An opening message built around the gap found for each business. You read it, you edit it, you decide where it goes.') }}
        </p>
    </div>

    <form action="#" method="post" class="@container">
        <div class="gen__grid">
            {{-- Controls --}}
            <div class="@4xl:sticky @4xl:top-24">
                <section class="form-card">
                    <h3 class="form-card__title">{{ __('Who it is for') }}</h3>

                    <div class="mt-4">
                        <label for="gen-lead" class="form-label">{{ __('Lead') }}</label>
                        <select id="gen-lead" name="lead" class="form-input">
                            <option value="1" selected>{{ __('Barton Springs Dental') }}</option>
                            <option value="2">{{ __('Lamar Family Dentistry') }}</option>
                            <option value="3">{{ __('Hyde Park Dental Care') }}</option>
                            <option value="4">{{ __('Zilker Smile Studio') }}</option>
                        </select>
                        <p class="form-hint">
                            {{ __('Or write one draft for a whole list and personalise per business.') }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <label for="gen-scope" class="form-label">
                            {{ __('Write it for') }}
                        </label>
                        <select id="gen-scope" name="scope" class="form-input">
                            <option value="one" selected>{{ __('Just this lead') }}</option>
                            <option value="list-1">
                                {{ __('Austin dentists — Q3 (142 leads)') }}
                            </option>
                            <option value="list-2">{{ __('Warm — follow up (38 leads)') }}</option>
                        </select>
                    </div>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('How it should read') }}</h3>
                    <p class="form-card__hint">
                        {{ __('The gap it argues stays the same — this changes the voice, not the facts.') }}
                    </p>

                    <div class="mt-4">
                        <label for="gen-tone" class="form-label">{{ __('Tone') }}</label>
                        <select id="gen-tone" name="tone" class="form-input">
                            <option value="direct" selected>
                                {{ __('Direct — get to the point') }}
                            </option>
                            <option value="warm">{{ __('Warm — friendly, less formal') }}</option>
                            <option value="formal">
                                {{ __('Formal — for a larger practice') }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="gen-length" class="form-label">{{ __('Length') }}</label>
                        <select id="gen-length" name="length" class="form-input">
                            <option value="short">{{ __('Short — under 80 words') }}</option>
                            <option value="medium" selected>
                                {{ __('Medium — around 120 words') }}
                            </option>
                            <option value="long">
                                {{ __('Long — the full case, around 200') }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="gen-open" class="form-label">{{ __('Open on') }}</label>
                        <select id="gen-open" name="opening" class="form-input">
                            <option value="gap" selected>{{ __('The gap we found') }}</option>
                            <option value="praise">{{ __('Something they do well') }}</option>
                            <option value="question">{{ __('A question') }}</option>
                        </select>
                    </div>

                    <p class="est__note mt-5">
                        <i class="ph ph-info" aria-hidden="true"></i>
                        {{ __('Re-writing is free. It uses analysis you have already paid for — no credits are spent here.') }}
                    </p>

                    <button type="submit" class="btn btn-ai mt-4 w-full">
                        <span class="btn__label">
                            <span>{{ __('Write it again') }}</span>
                            <span aria-hidden="true">{{ __('Write it again') }}</span>
                        </span>
                        <i class="ph ph-arrows-clockwise"></i>
                    </button>
                </section>
            </div>

            {{-- The draft --}}
            <div class="min-w-0">
                <section class="form-card">
                    <h3 class="form-card__title">{{ __('The draft') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Written for Barton Springs Dental, around the gap the analysis found: no online booking on a busy practice.') }}
                    </p>

                    <div class="mt-4">
                        <label for="gen-subject" class="form-label">{{ __('Subject') }}</label>
                        <input
                            type="text"
                            id="gen-subject"
                            name="subject"
                            class="form-input"
                            value="{{ __('Booking online at Barton Springs') }}"
                        />
                    </div>

                    <div class="draft mt-4">
                        <div class="draft__head">
                            <p class="draft__key">
                                <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                                {{ __('Drafted by AI') }}
                            </p>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline copy-btn"
                                data-copy-target="#gen-body"
                            >
                                <span class="btn__label">
                                    <span>{{ __('Copy') }}</span>
                                    <span aria-hidden="true">{{ __('Copy') }}</span>
                                </span>
                                <i class="ph ph-copy copy-btn__idle"></i>
                                <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                            </button>
                        </div>

                        <label for="gen-body" class="sr-only">{{ __('Drafted email') }}</label>
                        <textarea
                            id="gen-body"
                            name="body"
                            class="w-full resize-y border-0 px-4 py-3 text-[0.9375rem] leading-[1.7] text-title focus:outline-none"
                            rows="12"
                        >{{ __('Hi there,') }}

{{ __('I noticed Barton Springs Dental has over 300 reviews — clearly a busy practice — but there is no way for a patient to book online. Right now every appointment has to come through the phone.') }}

{{ __('We set up booking that works alongside how your front desk already runs. Most practices your size see the phone quieten within a fortnight.') }}

{{ __('Worth a short call to see if it fits?') }}

{{ __('Best,') }}
{{ __('Amara') }}</textarea>

                        <div class="draft__foot">
                            <p class="text-[0.8125rem] text-body">
                                <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                {{ __('Edit it freely — this is a starting point, not a finished email.') }}
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Where it can go. NO send control — rule 4. --}}
                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('What happens next') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Nothing leaves LeadAtlas from this screen. Take it with you, keep it for later, or queue it where you still approve each one.') }}
                    </p>

                    <div class="gen__routes mt-4">
                        <button
                            type="button"
                            class="gen__route copy-btn"
                            data-copy-target="#gen-body"
                        >
                            <span class="gen__route-icon" aria-hidden="true">
                                <i class="ph ph-copy copy-btn__idle"></i>
                                <i class="ph ph-check copy-btn__done"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[0.875rem] font-semibold text-title">{{ __('Copy it') }}</span>
                                <span class="mt-0.5 block text-[0.8125rem] leading-snug text-body">
                                    {{ __('Paste it into whatever you send from.') }}
                                </span>
                            </span>
                        </button>

                        <button
                            type="button"
                            class="gen__route"
                            data-modal-open="templateModal"
                        >
                            <span class="gen__route-icon" aria-hidden="true">
                                <i class="ph ph-bookmark-simple"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[0.875rem] font-semibold text-title">{{ __('Save as a template') }}</span>
                                <span class="mt-0.5 block text-[0.8125rem] leading-snug text-body">
                                    {{ __('Reuse this wording on similar businesses.') }}
                                </span>
                            </span>
                        </button>

                        <a href="#" class="gen__route">
                            <span class="gen__route-icon" aria-hidden="true">
                                <i class="ph ph-paper-plane-tilt"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[0.875rem] font-semibold text-title">{{ __('Add to a campaign') }}</span>
                                <span class="mt-0.5 block text-[0.8125rem] leading-snug text-body">
                                    {{ __('Queued for review — you approve every message before it goes.') }}
                                </span>
                            </span>
                        </a>
                    </div>

                    <p class="gen__rule">
                        <i class="ph-fill ph-shield-check" aria-hidden="true"></i>
                        <span>
                            {{ __('LeadAtlas never sends on your behalf. Even in a campaign, each message waits for you.') }}
                        </span>
                    </p>
                </section>
            </div>
        </div>
    </form>

    @push('modals')
        <div class="modal" id="templateModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="templateModalTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="templateModalTitle">{{ __('Save as a template') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Keep this wording to reuse on businesses with the same gap.') }}
                    </p>

                    <div>
                        <label for="tpl-name" class="form-label">{{ __('Name') }}</label>
                        <input
                            type="text"
                            id="tpl-name"
                            name="name"
                            class="form-input"
                            placeholder="{{ __('No online booking — direct') }}"
                            required
                        />
                        <p class="form-hint">{{ __('Only you see this.') }}</p>
                    </div>

                    <div class="mt-4">
                        <label for="tpl-when" class="form-label">{{ __('Use it when') }}</label>
                        <select id="tpl-when" name="gap" class="form-input">
                            <option value="booking" selected>
                                {{ __('The business has no online booking') }}
                            </option>
                            <option value="site">{{ __('Their site is out of date') }}</option>
                            <option value="contact">{{ __('They are phone only') }}</option>
                            <option value="any">{{ __('Any business') }}</option>
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
                                <span>{{ __('Save template') }}</span>
                                <span aria-hidden="true">{{ __('Save template') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endpush
</x-layouts.user>

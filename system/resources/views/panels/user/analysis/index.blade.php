<x-layouts.user :title="__('Business analysis')">
    <div class="mb-4">
        <h2 class="heading-3">{{ __('Business analysis') }}</h2>
        <p class="m-text mt-1">
            {{ __('A written read of what each business does, what it is missing, and what that gives you to open with.') }}
        </p>
    </div>

    <div class="@container">
        <form
            action="#"
            method="post"
            class="anz__form"
            data-estimate-form
            data-estimate-mode="selection"
            data-balance="2480"
        >
            <section class="form-card">
                <h3 class="form-card__title">{{ __('What to analyse') }}</h3>
                <p class="form-card__hint">
                    {{ __('Pick a list you have already built. Analysis reads the business, not the map, so it can run any time.') }}
                </p>

                <div class="mt-4">
                    <label for="anz-source" class="form-label">{{ __('List') }}</label>
                    <select id="anz-source" name="source" class="form-input" required>
                        <option value="1" data-count="142" data-analysed="96" selected>
                            {{ __('Austin dentists — Q3 (142 leads)') }}
                        </option>
                        <option value="2" data-count="38" data-analysed="38">
                            {{ __('Warm — follow up (38 leads)') }}
                        </option>
                        <option value="3" data-count="96" data-analysed="0">
                            {{ __('Chicago clinics (96 leads)') }}
                        </option>
                        <option value="4" data-count="12" data-analysed="4">
                            {{ __('Do not contact (12 leads)') }}
                        </option>
                    </select>
                    <p class="form-hint">
                        <a href="{{ route('user.leads.lists') }}" class="est__link">
                            {{ __('Manage your lists') }}
                        </a>
                    </p>
                </div>

                <div class="anz__opt">
                    <input type="checkbox" class="form-check" id="anz-skip" name="skip_analysed" value="1" checked />
                    <label for="anz-skip" class="cursor-pointer text-[0.9375rem] font-medium text-title">
                        {{ __('Skip businesses already analysed') }}
                        <span class="mt-0.5 block text-[0.8125rem] font-normal text-body">
                            {{ __('Re-reading an analysis you already have is always free.') }}
                        </span>
                    </label>
                </div>

                <div class="mt-5">
                    <label for="anz-focus" class="form-label">{{ __('What you want to know') }}</label>
                    <select id="anz-focus" name="focus" class="form-input" data-anz-focus>
                        <option value="gaps" selected>
                            {{ __('Where they are weak — the gap to open with') }}
                        </option>
                        <option value="fit">{{ __('Whether they fit what I sell') }}</option>
                        <option value="summary">{{ __('A plain summary of the business') }}</option>
                    </select>
                    <p class="form-hint">
                        {{ __('This steers what the analysis argues, not how much it costs.') }}
                    </p>
                </div>
            </section>

            <aside class="@3xl:sticky @3xl:top-24">
                <section class="form-card est">
                    <h3 class="form-card__title">{{ __('Before you run it') }}</h3>

                    <dl class="est__rows mt-4">
                        <div class="est__row">
                            <dt class="est__key">{{ __('Businesses to analyse') }}</dt>
                            <dd class="est__val numeric" data-estimate-count>46</dd>
                        </div>

                        <div class="est__row est__row--cost">
                            <dt class="est__key">{{ __('Credits it costs') }}</dt>
                            <dd class="est__val numeric" data-estimate-cost>46</dd>
                        </div>

                        <div class="est__row est__row--after">
                            <dt class="est__key">{{ __('Balance afterwards') }}</dt>
                            <dd class="est__val numeric" data-estimate-left>2,434</dd>
                        </div>
                    </dl>

                    <p class="est__warn" data-estimate-warning role="status">
                        <i class="ph-fill ph-warning" aria-hidden="true"></i>
                        <span>
                            {{ __('This run costs more credits than you have.') }}
                            <a href="#" class="est__link">{{ __('Buy more') }}</a>
                        </span>
                    </p>

                    <p class="est__note">
                        <i class="ph ph-info" aria-hidden="true"></i>
                        {{ __('One credit per business. Businesses you have analysed before are free to re-read, however often.') }}
                    </p>

                    <button type="submit" class="btn btn-primary mt-5 w-full" data-estimate-submit>
                        <span class="btn__label">
                            <span>{{ __('Run the analysis') }}</span>
                            <span aria-hidden="true">{{ __('Run the analysis') }}</span>
                        </span>
                        <i class="ph ph-sparkle"></i>
                    </button>
                </section>
            </aside>
        </form>
    </div>

    <div class="panel mt-6">
        <div class="panel__head">
            <h3 class="panel__title">
                <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                {{ __('Last run') }}
            </h3>
            <p class="panel__meta">
                <span class="numeric">96</span> {{ __('businesses') }} · {{ __('2 days ago') }}
            </p>
        </div>

        <div class="anz__results is-gaps" data-anz-results>
            <article class="anzr">
                <div class="anzr__head">
                    <a href="#" class="font-title text-[0.9375rem] font-bold text-title transition-colors duration-200 hover:text-primary">
                        {{ __('Barton Springs Dental') }}
                    </a>
                    <span class="score score--hi numeric">92</span>
                </div>

                <p class="anzr__read">
                    {{ __('Busy practice with') }} <span class="numeric">312</span>
                    {{ __('reviews and a strong local reputation, but no online booking and a site that has not changed in two years. They are turning demand away at the door.') }}
                </p>

                <p class="anzr__gap">
                    <i class="ph ph-lightbulb" aria-hidden="true"></i>
                    <span>{{ __('No online booking on a practice this busy') }}</span>
                </p>

                <p class="anzr__fit anzr__fit--yes">
                    <i class="ph ph-check-circle" aria-hidden="true"></i>
                    <span>{{ __('Strong fit — busy enough to feel the phone load') }}</span>
                </p>
            </article>

            <article class="anzr">
                <div class="anzr__head">
                    <a href="#" class="font-title text-[0.9375rem] font-bold text-title transition-colors duration-200 hover:text-primary">
                        {{ __('Lamar Family Dentistry') }}
                    </a>
                    <span class="score score--hi numeric">88</span>
                </div>

                <p class="anzr__read">
                    {{ __('Well reviewed and clearly established, with') }} <span class="numeric">204</span>
                    {{ __('reviews. The site loads slowly on a phone and the contact page asks for more than most people will fill in.') }}
                </p>

                <p class="anzr__gap">
                    <i class="ph ph-lightbulb" aria-hidden="true"></i>
                    <span>{{ __('A contact form long enough to lose people') }}</span>
                </p>

                <p class="anzr__fit anzr__fit--yes">
                    <i class="ph ph-check-circle" aria-hidden="true"></i>
                    <span>{{ __('Good fit — already invested in the site once') }}</span>
                </p>
            </article>

            <article class="anzr">
                <div class="anzr__head">
                    <a href="#" class="font-title text-[0.9375rem] font-bold text-title transition-colors duration-200 hover:text-primary">
                        {{ __('Hyde Park Dental Care') }}
                    </a>
                    <span class="score score--hi numeric">84</span>
                </div>

                <p class="anzr__read">
                    {{ __('Growing fast —') }} <span class="numeric">87</span>
                    {{ __('reviews, most of them this year. No email anywhere on the site, only a phone number, so every enquiry has to happen in office hours.') }}
                </p>

                <p class="anzr__gap">
                    <i class="ph ph-lightbulb" aria-hidden="true"></i>
                    <span>{{ __('Phone only, so out-of-hours enquiries are lost') }}</span>
                </p>

                <p class="anzr__fit anzr__fit--yes">
                    <i class="ph ph-check-circle" aria-hidden="true"></i>
                    <span>{{ __('Strong fit — growing fast with no way to book') }}</span>
                </p>
            </article>

            <article class="anzr">
                <div class="anzr__head">
                    <a href="#" class="font-title text-[0.9375rem] font-bold text-title transition-colors duration-200 hover:text-primary">
                        {{ __('Zilker Smile Studio') }}
                    </a>
                    <span class="score score--mid numeric">79</span>
                </div>

                <p class="anzr__read">
                    {{ __('Newer practice with') }} <span class="numeric">41</span>
                    {{ __('reviews and a well-built site. Little to fix technically; the gap is reach rather than setup.') }}
                </p>

                <p class="anzr__gap">
                    <i class="ph ph-lightbulb" aria-hidden="true"></i>
                    <span>{{ __('Set up well, but almost invisible in local search') }}</span>
                </p>

                <p class="anzr__fit anzr__fit--maybe">
                    <i class="ph ph-minus-circle" aria-hidden="true"></i>
                    <span>{{ __('Weaker fit — booking is already handled') }}</span>
                </p>
            </article>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-neutral-200 px-4 py-3">
            <p class="m-text">{{ __('Re-reading any of these costs nothing.') }}</p>
            <a href="{{ route('user.leads.index') }}" class="btn btn-outline btn-sm">
                <span class="btn__label">
                    <span>{{ __('See them in the table') }}</span>
                    <span aria-hidden="true">{{ __('See them in the table') }}</span>
                </span>
                <i class="ph ph-arrow-right"></i>
            </a>
        </div>
    </div>
</x-layouts.user>
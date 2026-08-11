<x-layouts.user :title="__('Email generator')">
    @php
        $selectedLeadId = (int) old('lead_id', $draft->lead_id ?? $selectedLead?->id ?? 0);
        $activeScope = old('scope_type', $draft->scope_type ?? 'one');
        $activeTone = old('tone', $draft->tone ?? 'direct');
        $activeLength = old('length', $draft->length ?? 'medium');
        $activeOpening = old('opening', $draft->opening ?? 'gap');
        $activeListId = (int) old('lead_list_id', $draft->lead_list_id ?? $lists->first()?->id ?? 0);
        $businessName = $selectedLead?->place?->name ?? __('No lead selected');
        $draftSubject = old('subject', $draft->subject ?? '');
        $draftBody = old('body', $draft->body ?? '');
        $emailCreditCost = (int) ($emailCreditCost ?? 1);
        $balance = (int) ($balance ?? 0);
    @endphp

    <div class="mb-4">
        <h2 class="heading-3">{{ __('Email generator') }}</h2>
        <p class="m-text mt-1">
            {{ __('Draft outreach from the lead data and business analysis you already have. Nothing is sent from this screen.') }}
        </p>
    </div>

    <form id="email-generator-form" action="{{ route('user.email.generate') }}" method="post" class="@container">
        @csrf
        <input type="hidden" name="draft_id" value="{{ $selectedDraft?->id }}" />

        <div class="gen__grid">
            <div class="@4xl:sticky @4xl:top-24">
                <section class="form-card">
                    <h3 class="form-card__title">{{ __('Who it is for') }}</h3>

                    <div class="mt-4">
                        <label for="gen-lead" class="form-label">{{ __('Lead') }}</label>
                        <select id="gen-lead" name="lead_id" class="form-input" required>
                            @forelse ($leads as $lead)
                                <option value="{{ $lead->id }}" @selected($selectedLeadId === $lead->id)>
                                    {{ $lead->place?->name ?? __('Untitled lead') }}
                                    @if ($lead->email)
                                        - {{ $lead->email }}
                                    @endif
                                </option>
                            @empty
                                <option value="">{{ __('No saved leads yet') }}</option>
                            @endforelse
                        </select>
                        @error('lead_id')
                            <p class="form-hint text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">
                            {{ __('Pick a saved lead. Analysed leads produce more specific copy.') }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <label for="gen-scope" class="form-label">{{ __('Write it for') }}</label>
                        <select id="gen-scope" name="scope_type" class="form-input">
                            <option value="one" @selected($activeScope === 'one')>{{ __('Just this lead') }}</option>
                            <option value="list" @selected($activeScope === 'list')>{{ __('A whole list, using this lead as the sample') }}</option>
                        </select>
                        @error('scope_type')
                            <p class="form-hint text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="gen-list" class="form-label">{{ __('List') }}</label>
                        <select id="gen-list" name="lead_list_id" class="form-input">
                            @forelse ($lists as $list)
                                <option value="{{ $list->id }}" @selected($activeListId === $list->id)>
                                    {{ $list->name }} ({{ trans_choice(':count lead|:count leads', $list->leads_count, ['count' => number_format($list->leads_count)]) }})
                                </option>
                            @empty
                                <option value="">{{ __('No lists yet') }}</option>
                            @endforelse
                        </select>
                        @error('lead_list_id')
                            <p class="form-hint text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">
                            {{ __('Only used when you queue a list campaign.') }}
                        </p>
                    </div>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('How it should read') }}</h3>
                    <p class="form-card__hint">
                        {{ __('The business facts stay the same. These controls change the voice and shape.') }}
                    </p>

                    <div class="mt-4">
                        <label for="gen-tone" class="form-label">{{ __('Tone') }}</label>
                        <select id="gen-tone" name="tone" class="form-input">
                            @foreach ($tones as $value => $label)
                                <option value="{{ $value }}" @selected($activeTone === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tone')
                            <p class="form-hint text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="gen-length" class="form-label">{{ __('Length') }}</label>
                        <select id="gen-length" name="length" class="form-input">
                            @foreach ($lengths as $value => $label)
                                <option value="{{ $value }}" @selected($activeLength === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('length')
                            <p class="form-hint text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="gen-open" class="form-label">{{ __('Open on') }}</label>
                        <select id="gen-open" name="opening" class="form-input">
                            @foreach ($openings as $value => $label)
                                <option value="{{ $value }}" @selected($activeOpening === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('opening')
                            <p class="form-hint text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <p class="est__note mt-5">
                        <i class="ph ph-info" aria-hidden="true"></i>
                        {{ trans_choice(':count credit is spent each time you write a new draft.|:count credits are spent each time you write a new draft.', $emailCreditCost, ['count' => number_format($emailCreditCost)]) }}
                        {{ __('Balance: :balance credits.', ['balance' => number_format($balance)]) }}
                    </p>

                    @if ($emailCreditCost > $balance)
                        <p class="est__warn is-shown mt-3" role="status">
                            <i class="ph-fill ph-warning" aria-hidden="true"></i>
                            <span>
                                {{ __('You need more credits to generate a new draft.') }}
                                <a href="{{ route('user.credits.buy') }}" class="est__link">{{ __('Buy more') }}</a>
                            </span>
                        </p>
                    @endif

                    <button type="submit" class="btn btn-ai mt-4 w-full" @disabled($leads->isEmpty() || $emailCreditCost > $balance)>
                        <span class="btn__label">
                            <span>{{ __('Write it again') }}</span>
                            <span aria-hidden="true">{{ __('Write it again') }}</span>
                        </span>
                        <i class="ph ph-arrows-clockwise"></i>
                    </button>
                </section>
            </div>

            <div class="min-w-0">
                <section class="form-card">
                    <h3 class="form-card__title">{{ __('The draft') }}</h3>
                    <p class="form-card__hint">
                        @if ($analysis)
                            {{ __('Written for :business around this gap: :gap', ['business' => $businessName, 'gap' => $analysis->gap]) }}
                        @else
                            {{ __('Written for :business from available lead data. Run business analysis for a sharper gap.', ['business' => $businessName]) }}
                        @endif
                    </p>

                    <div class="mt-4">
                        <label for="gen-subject" class="form-label">{{ __('Subject') }}</label>
                        <input
                            type="text"
                            id="gen-subject"
                            name="subject"
                            class="form-input"
                            value="{{ $draftSubject }}"
                            maxlength="190"
                            required
                        />
                        @error('subject')
                            <p class="form-hint text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="draft mt-4">
                        <div class="draft__head">
                            <p class="draft__key">
                                <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                                {{ $selectedDraft ? __('Saved draft') : __('Generated preview') }}
                            </p>
                            <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#gen-body">
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
                            required
                        >{{ $draftBody }}</textarea>
                        @error('body')
                            <p class="form-hint px-4 pb-3 text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="draft__foot">
                            <p class="text-[0.8125rem] text-body">
                                <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                {{ __('Edit it freely before copying, saving, or queueing for review.') }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('What happens next') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Nothing leaves LeadAtlas from this screen. Campaigns created here still wait for review.') }}
                    </p>

                    <div class="gen__routes mt-4">
                        <button type="button" class="gen__route copy-btn" data-copy-target="#gen-body">
                            <span class="gen__route-icon" aria-hidden="true">
                                <i class="ph ph-copy copy-btn__idle"></i>
                                <i class="ph ph-check copy-btn__done"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[0.875rem] font-semibold text-title">{{ __('Copy it') }}</span>
                                <span class="mt-0.5 block text-[0.8125rem] leading-snug text-body">
                                    {{ __('Paste it into wherever you send from.') }}
                                </span>
                            </span>
                        </button>

                        <button type="button" class="gen__route" data-modal-open="templateModal" @disabled($leads->isEmpty())>
                            <span class="gen__route-icon" aria-hidden="true">
                                <i class="ph ph-bookmark-simple"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[0.875rem] font-semibold text-title">{{ __('Save as a template') }}</span>
                                <span class="mt-0.5 block text-[0.8125rem] leading-snug text-body">
                                    {{ __('Reuse this edited wording on similar businesses.') }}
                                </span>
                            </span>
                        </button>

                        <button
                            type="submit"
                            class="gen__route"
                            formaction="{{ route('user.email.campaigns.store') }}"
                            @disabled($leads->isEmpty())
                        >
                            <span class="gen__route-icon" aria-hidden="true">
                                <i class="ph ph-paper-plane-tilt"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[0.875rem] font-semibold text-title">{{ __('Add to a campaign') }}</span>
                                <span class="mt-0.5 block text-[0.8125rem] leading-snug text-body">
                                    {{ __('Create a review campaign from this lead or list.') }}
                                </span>
                            </span>
                        </button>
                    </div>

                    <p class="gen__rule">
                        <i class="ph-fill ph-shield-check" aria-hidden="true"></i>
                        <span>{{ __('LeadAtlas never sends on your behalf. Copying, saving templates, and campaign review do not spend generation credits.') }}</span>
                    </p>
                </section>

                @if ($templates->isNotEmpty() || $recentDrafts->isNotEmpty())
                    <section class="form-card mt-4">
                        <h3 class="form-card__title">{{ __('Saved work') }}</h3>

                        @if ($recentDrafts->isNotEmpty())
                            <div class="mt-4">
                                <p class="form-label">{{ __('Recent drafts') }}</p>
                                <div class="grid gap-2">
                                    @foreach ($recentDrafts as $recent)
                                        <a href="{{ route('user.email.index', ['draft' => $recent->id, 'lead' => $recent->lead_id]) }}" class="gen__route">
                                            <span class="gen__route-icon" aria-hidden="true">
                                                <i class="ph ph-file-text"></i>
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block text-[0.875rem] font-semibold text-title">{{ $recent->lead?->place?->name ?? __('Draft') }}</span>
                                                <span class="mt-0.5 block text-[0.8125rem] leading-snug text-body">{{ $recent->created_at->diffForHumans() }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($templates->isNotEmpty())
                            <div class="mt-4">
                                <p class="form-label">{{ __('Templates') }}</p>
                                <div class="grid gap-2">
                                    @foreach ($templates as $template)
                                        <article class="rounded-md border border-neutral-200 px-3 py-2">
                                            <p class="text-[0.875rem] font-semibold text-title">{{ $template->name }}</p>
                                            <p class="mt-0.5 text-[0.8125rem] text-body">{{ $template->subject }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>
                @endif
            </div>
        </div>
    </form>

    @push('modals')
        <div class="modal" id="templateModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="templateModalTitle">
                <h2 class="heading-3" id="templateModalTitle">{{ __('Save as a template') }}</h2>
                <p class="m-text mt-2 mb-5">
                    {{ __('Keep this wording to reuse on businesses with the same kind of gap.') }}
                </p>

                <div>
                    <label for="tpl-name" class="form-label">{{ __('Name') }}</label>
                    <input
                        type="text"
                        id="tpl-name"
                        name="template_name"
                        form="email-generator-form"
                        class="form-input"
                        placeholder="{{ __('No online booking - direct') }}"
                    />
                    @error('template_name')
                        <p class="form-hint text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">{{ __('Only you see this.') }}</p>
                </div>

                <div class="mt-4">
                    <label for="tpl-when" class="form-label">{{ __('Use it when') }}</label>
                    <select id="tpl-when" name="template_gap" form="email-generator-form" class="form-input">
                        <option value="booking">{{ __('The business has no online booking') }}</option>
                        <option value="site">{{ __('Their site is missing or out of date') }}</option>
                        <option value="contact">{{ __('They are hard to contact directly') }}</option>
                        <option value="reputation">{{ __('Reputation proof is thin') }}</option>
                        <option value="conversion">{{ __('They have demand but conversion friction') }}</option>
                        <option value="any" selected>{{ __('Any business') }}</option>
                    </select>
                    @error('template_gap')
                        <p class="form-hint text-red-600">{{ $message }}</p>
                    @enderror
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
                        class="btn btn-primary"
                        form="email-generator-form"
                        formaction="{{ route('user.email.templates.store') }}"
                    >
                        <span class="btn__label">
                            <span>{{ __('Save template') }}</span>
                            <span aria-hidden="true">{{ __('Save template') }}</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endpush
</x-layouts.user>

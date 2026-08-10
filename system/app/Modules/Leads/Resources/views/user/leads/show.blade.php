<x-layouts.user :title="$lead->place?->name ?? __('Lead details')">
    @php
        $statuses = \App\Modules\Leads\Models\Lead::statuses();
        $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($lead->score);
        $place = $lead->place;
    @endphp

    <a href="{{ route('user.leads.index') }}" class="back-link">
        <i class="ph ph-arrow-left" aria-hidden="true"></i>
        {{ __('All leads') }}
    </a>

    <div class="lead-head">
        <div class="lead-head__id">
            <span class="lead-head__mark" aria-hidden="true">
                <i class="ph ph-storefront"></i>
            </span>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="heading-3">{{ $place?->name }}</h2>
                    <span class="status status--{{ $lead->status }}">{{ $statuses[$lead->status]['label'] ?? $lead->status }}</span>
                </div>

                <p class="lead-head__meta">
                    @if ($place?->formatted_address)
                        <span>
                            <i class="ph ph-map-pin" aria-hidden="true"></i>
                            {{ $place->formatted_address }}
                        </span>
                    @endif
                    @if ($place?->phone)
                        <a href="tel:{{ $place->phone }}">
                            <span class="numeric">{{ $place->phone }}</span>
                        </a>
                    @endif
                    @if ($place?->website)
                        <a href="{{ $place->website }}" rel="noreferrer" target="_blank">{{ parse_url($place->website, PHP_URL_HOST) }}</a>
                    @endif
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

            <form action="{{ route('user.leads.destroy', $lead) }}" method="post" class="contents">
                @csrf
                @method('delete')
                <button type="submit" class="row-icon row-icon--danger" aria-label="{{ __('Delete this lead') }}"
                        data-confirm data-confirm-title="{{ __('Delete this lead?') }}"
                        data-confirm-body="{{ __('It is removed from your account and from any list it belongs to. Credits already spent generating it are not returned.') }}"
                        data-confirm-label="{{ __('Delete lead') }}" data-confirm-variant="error">
                    <i class="ph ph-trash" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="lead-grid">
        <div class="lead-grid__main">
            <section class="@container rounded-2xl border border-ai/25 bg-ai/4 p-5 md:p-6">
                <div class="verdict-card__top">
                    <span class="verdict-card__score numeric">{{ $lead->score }}</span>

                    <div class="min-w-0">
                        <p class="verdict-card__key">
                            <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                            {{ __('Scored by AI') }}
                        </p>
                        <p class="mt-0.5 font-title text-[1.125rem] font-bold text-title">
                            {{ $verdict['headline'] }}
                        </p>
                    </div>
                </div>

                <p class="mt-4 border-t border-ai/15 pt-4 text-[0.9375rem] leading-[1.7]">
                    {{ $verdict['explanation'] }}
                </p>
            </section>

            <section class="form-card">
                <h3 class="form-card__title">{{ __('What the score is made of') }}</h3>
                <p class="form-card__hint">
                    {{ __('Four signals behind the score above.') }}
                </p>

                <div class="mt-4">
                    @foreach ([
                        'review_volume' => __('Review volume'),
                        'booking_presence' => __('Online booking'),
                        'website_age' => __('Website age'),
                        'local_competition' => __('Local competition'),
                    ] as $key => $label)
                        @php
                            $value = $verdict['signals'][$key] ?? 'Fair';
                            $meterClass = match ($value) {
                                'Strong' => 'meter--strong',
                                'Weak' => 'meter--weak',
                                default => 'meter--fair',
                            };
                        @endphp
                        <div class="signal-row">
                            <p class="min-w-0 flex-1 basis-40 text-[0.875rem] font-medium text-title">
                                {{ $label }}
                            </p>
                            <span class="meter {{ $meterClass }}" aria-hidden="true">
                                <span class="meter__seg"></span>
                                <span class="meter__seg"></span>
                                <span class="meter__seg"></span>
                            </span>
                            <span class="w-14 shrink-0 text-right text-[0.8125rem] text-body">{{ __($value) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="form-card">
                <h3 class="form-card__title">{{ __('A way in') }}</h3>
                <p class="form-card__hint">
                    {{ __('Edit it, copy it, send it yourself — nothing goes out from this page.') }}
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
                              rows="7">{{ $draft }}</textarea>

                    <div class="draft__foot">
                        <p class="text-[0.8125rem] text-body">
                            {{ __('You read it before it goes anywhere.') }}
                        </p>
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
                    @foreach ($lead->activities as $activity)
                        <article class="timeline__item">
                            <span class="timeline__dot{{ $activity->type === 'scored' ? ' timeline__dot--ai' : '' }}{{ in_array($activity->type, ['tag_added', 'tag_removed', 'status_changed']) ? ' timeline__dot--act' : '' }}" aria-hidden="true">
                                <i class="ph {{ match ($activity->type) {
                                    'found_in_search' => 'ph-map-pin',
                                    'contact_found', 'enrichment_failed' => 'ph-envelope-simple',
                                    'status_changed' => 'ph-flag',
                                    'tag_added', 'tag_removed' => 'ph-tag',
                                    'note_added' => 'ph-note-pencil',
                                    'scored' => 'ph-fill ph-sparkle',
                                    'list_added', 'list_removed' => 'ph-folders',
                                    default => 'ph-circle',
                                } }}"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[0.9375rem] font-medium text-title">
                                    @switch($activity->type)
                                        @case('found_in_search')
                                            {{ __('Found in a search') }}
                                            @break
                                        @case('contact_found')
                                            {{ ($activity->payload['found'] ?? false) ? __('Contact found') : __('Contact search attempted — no email found') }}
                                            @break
                                        @case('status_changed')
                                            {{ __('Status changed from :from to :to', ['from' => $activity->payload['from'] ?? '', 'to' => $activity->payload['to'] ?? '']) }}
                                            @break
                                        @case('tag_added')
                                            {{ __('Tagged ":tag"', ['tag' => $activity->payload['tag'] ?? '']) }}
                                            @break
                                        @case('tag_removed')
                                            {{ __('Untagged ":tag"', ['tag' => $activity->payload['tag'] ?? '']) }}
                                            @break
                                        @case('note_added')
                                            {{ __('Note added') }}
                                            @break
                                        @case('scored')
                                            {{ __('Scored :score', ['score' => $activity->payload['score'] ?? '']) }}
                                            @break
                                        @default
                                            {{ $activity->type }}
                                    @endswitch
                                </p>
                                <p class="mt-0.5 text-[0.8125rem] text-body">
                                    {{ $activity->causedBy?->name ?? __('LeadAtlas') }} ·
                                    <time datetime="{{ $activity->created_at->toDateString() }}">{{ $activity->created_at->format('d F') }}</time>
                                </p>
                            </div>
                        </article>
                    @endforeach

                    @foreach ($lead->notes as $note)
                        <article class="timeline__item">
                            <span class="timeline__dot" aria-hidden="true">
                                <i class="ph ph-note-pencil"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[0.9375rem] font-medium text-title">{{ $note->body }}</p>
                                <p class="mt-0.5 text-[0.8125rem] text-body">
                                    {{ $note->author?->name }} ·
                                    <time datetime="{{ $note->created_at->toDateString() }}">{{ $note->created_at->format('d F') }}</time>
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="lead-grid__side">
            @if ($place?->lat && $place?->lng)
                <section class="mapcard">
                    <div class="mapcard__head">
                        <h3 class="form-card__title">{{ __('Where it is') }}</h3>
                    </div>
                    <div class="map" data-map data-map-center="{{ $place->lat }},{{ $place->lng }}" data-map-zoom="14"
                         data-map-pins="{{ $place->lat }},{{ $place->lng }},{{ $lead->score }},{{ $place->name }}"
                         aria-label="{{ __('Map showing :name', ['name' => $place->name]) }}"></div>
                </section>
            @endif

            <section class="form-card">
                <h3 class="form-card__title">{{ __('Details') }}</h3>

                <div class="mt-3">
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Category') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">{{ $place?->google_category ?? __('Unknown') }}</p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Email') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">
                            @if ($lead->email)
                                <a href="mailto:{{ $lead->email }}" class="d-table__mail">{{ $lead->email }}</a>
                            @else
                                <span class="d-table__muted">{{ __('Not found') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Phone') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title numeric">{{ $place?->phone ?? __('Not listed') }}</p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Rating') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">
                            @if ($place?->rating)
                                <span class="numeric">{{ $place->rating }}</span> {{ __('from') }}
                                <span class="numeric">{{ $place->review_count }}</span> {{ __('reviews') }}
                            @else
                                {{ __('No rating yet') }}
                            @endif
                        </p>
                    </div>
                    <div class="fact">
                        <p class="text-[0.75rem] font-medium text-body">{{ __('Added') }}</p>
                        <p class="mt-0.5 text-[0.9375rem] font-medium text-title">
                            <time datetime="{{ $lead->created_at->toDateString() }}">{{ $lead->created_at->format('d F Y') }}</time>
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
                    @foreach ($lead->tags as $tag)
                        <form action="{{ route('user.leads.detach-tag', [$lead, $tag]) }}" method="post" class="contents">
                            @csrf
                            @method('delete')
                            <button type="submit" class="tag-pill">
                                {{ $tag->name }}
                                <i class="ph ph-x" aria-hidden="true"></i>
                            </button>
                        </form>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.addEventListener('confirm:accepted', (event) => {
                    const trigger = event.target;
                    if (trigger.tagName === 'BUTTON' && trigger.form) {
                        trigger.form.submit();
                    }
                });
            });
        </script>
    @endpush

    @push('modals')
        <div class="modal" id="noteModal" aria-hidden="true">
            <div class="modal__backdrop" data-modal-close></div>

            <div class="modal__panel max-w-md p-6" role="dialog" aria-modal="true" aria-labelledby="noteModalTitle">
                <form action="{{ route('user.leads.add-note', $lead) }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="noteModalTitle">{{ __('Add a note') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Notes appear in the activity timeline for this lead.') }}
                    </p>

                    <div>
                        <label for="note-body" class="form-label">{{ __('Note') }}</label>
                        <textarea id="note-body" name="body" class="form-input" rows="4"
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
                <form action="{{ route('user.leads.update-status', $lead) }}" method="post">
                    @csrf
                    @method('patch')
                    <h2 class="heading-3" id="statusModalTitle">{{ __('Set status') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Where this lead sits in your pipeline.') }}
                    </p>

                    <div>
                        <label for="status-value" class="form-label">{{ __('Status') }}</label>
                        <select id="status-value" name="status" class="form-input" required>
                            @foreach ($statuses as $key => $meta)
                                <option value="{{ $key }}" @selected($lead->status === $key)>{{ $meta['label'] }}</option>
                            @endforeach
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
                <form action="{{ route('user.leads.attach-tag', $lead) }}" method="post">
                    @csrf
                    <h2 class="heading-3" id="tagModalTitle">{{ __('Add a tag') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('Your own label — it does not affect the AI score.') }}
                    </p>

                    <div>
                        <label for="tag-name" class="form-label">{{ __('Tag') }}</label>
                        <input type="text" id="tag-name" name="tag" class="form-input"
                               placeholder="{{ __('Follow up in Q3') }}" maxlength="30" required />
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

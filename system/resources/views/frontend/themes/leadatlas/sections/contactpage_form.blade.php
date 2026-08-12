@php
    $data = $section->data;
    $topics = $data['topics'] ?? [];
    $responseTimes = $data['response_times'] ?? [];
@endphp
<section class="spb-section pt-12 md:pt-14" data-anim>
    <div class="container">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,22rem)] lg:gap-14">
            <div class="rounded-2xl border border-neutral-200 bg-neutral-0 p-6 md:p-8" data-anim-item>
                <form class="flex flex-col gap-5" action="{{ route('contacts.store') }}" method="post" novalidate>
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="field">
                            <label for="c-name" class="form-label">{{ __('Your name') }}</label>
                            <input type="text" id="c-name" name="name" class="form-input" autocomplete="name"
                                placeholder="{{ __('Alex Morgan') }}" value="{{ old('name') }}" required>
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="c-email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" id="c-email" name="email" class="form-input" autocomplete="email"
                                placeholder="alex@agency.com" value="{{ old('email') }}" required>
                            @error('email')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="c-topic" class="form-label">{{ __('What is this about?') }}</label>
                        <select id="c-topic" name="subject" class="form-input" required>
                            <option value="">{{ __('Choose a topic') }}</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic['label'] ?? '' }}" @selected(old('subject') === ($topic['label'] ?? ''))>{{ $topic['label'] ?? '' }}</option>
                            @endforeach
                        </select>
                        @error('subject')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="c-message" class="form-label">{{ __('Message') }}</label>
                        <textarea id="c-message" name="message" class="form-input contact__textarea" rows="6"
                            placeholder="{{ $data['message_placeholder'] ?? '' }}"
                            required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-plugins.recaptcha />

                    <label class="flex items-start gap-2 text-[0.8125rem] leading-[1.6]">
                        <input type="checkbox" id="c-terms" name="terms_accepted" value="1" class="mt-0.5" required>
                        <span>{{ __('I agree to be contacted about this request.') }}</span>
                    </label>
                    @error('terms_accepted')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="btn btn-primary mt-1 w-full sm:w-auto sm:self-start">
                        <span class="btn__label">
                            <span>{{ __('Send message') }}</span>
                            <span aria-hidden="true">{{ __('Send message') }}</span>
                        </span>
                        <i class="ph ph-arrow-right" aria-hidden="true"></i>
                    </button>

                    @if(!empty($data['consent_text']))
                        <p class="text-[0.8125rem] leading-[1.6]">
                            {{ $data['consent_text'] }}
                            <a href="{{ $data['consent_link'] ?: '#' }}"
                                class="consent__link">{{ $data['consent_link_text'] ?? __('privacy policy') }}</a>.
                        </p>
                    @endif
                </form>
            </div>

            <aside class="flex flex-col gap-5" data-anim-item>
                <div class="rounded-xl border border-neutral-200 bg-neutral-0 p-5">
                    @if(!empty($data['response_label']))
                        <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">
                            {{ $data['response_label'] }}
                        </p>
                    @endif
                    <ul class="mt-4 flex flex-col">
                        @foreach($responseTimes as $item)
                            @php
                                $responseUnit = (string) ($item['unit'] ?? '');
                                $responseValue = (string) ($item['value'] ?? '');
                                $responseUnitParts = explode(':n', $responseUnit, 2);
                            @endphp
                            <li
                                class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 border-b border-neutral-100 py-2.5 text-[0.875rem] last:border-b-0 last:pb-0">
                                <span class="text-title">{{ $item['label'] ?? '' }}</span>
                                <span class="text-[0.8125rem] @if(!empty($item['fast'])) contact__time-when--fast @endif">
                                    @if(count($responseUnitParts) === 2)
                                        {{ $responseUnitParts[0] }}<span class="numeric">{{ $responseValue }}</span>{{ $responseUnitParts[1] }}
                                    @else
                                        {{ $responseUnit }}
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if(!empty($data['removal_label']) || !empty($data['removal_body']))
                    <div class="rounded-xl border border-neutral-200 bg-neutral-0 p-5 contact__card--removal">
                        @if(!empty($data['removal_label']))
                            <p
                                class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase contact__card-key--removal">
                                <i class="ph ph-shield-check" aria-hidden="true"></i>
                                {{ $data['removal_label'] }}
                            </p>
                        @endif
                        @if(!empty($data['removal_body']))
                            <p class="contact__card-body">{{ $data['removal_body'] }}</p>
                        @endif
                        @if(!empty($data['removal_body_secondary']))
                            <p class="contact__card-body">{{ $data['removal_body_secondary'] }}</p>
                        @endif
                    </div>
                @endif

                @if(!empty($data['customer_label']) || !empty($data['customer_body']))
                    <div class="rounded-xl border border-neutral-200 bg-neutral-0 p-5">
                        @if(!empty($data['customer_label']))
                            <p class="font-mono text-[0.6875rem] font-medium tracking-[0.16em] text-body uppercase">
                                {{ $data['customer_label'] }}
                            </p>
                        @endif
                        @if(!empty($data['customer_body']))
                            <p class="contact__card-body">{{ $data['customer_body'] }}</p>
                        @endif
                        @if(!empty($data['customer_link_text']))
                            <a href="{{ $data['customer_link'] ?: '#' }}" class="contact__link">
                                {{ $data['customer_link_text'] }}
                                <i class="ph ph-arrow-right" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>

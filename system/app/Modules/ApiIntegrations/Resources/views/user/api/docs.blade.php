<x-layouts.user :title="__('API documentation')">
    @include('api-integrations::user.api.nav', ['apiActive' => 'docs'])

    <div class="mb-4">
        <h2 class="heading-3">{{ __('API documentation') }}</h2>
    </div>

    <div class="doc">
        <div class="doc__main">
            <section class="form-card" id="start">
                <h3 class="form-card__title">{{ __('Getting started') }}</h3>

                <dl class="doc__facts">
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Base URL') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">{{ $baseUrl }}</dd>
                    </div>
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Auth') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">{{ __('Bearer token') }}</dd>
                    </div>
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Format') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">{{ __('JSON') }}</dd>
                    </div>
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Rate limit') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">
                            <span class="numeric">{{ $rateLimit }}</span> {{ __('requests a minute') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('First request') }}</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-first">
                            <span class="btn__label"><span>{{ __('Copy') }}</span><span aria-hidden="true">{{ __('Copy') }}</span></span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-first"><code>curl "{{ $baseUrl }}/leads?limit=25" \
  -H "Authorization: Bearer {{ $sampleToken }}"</code></pre>
                </div>
            </section>

            <section class="form-card mt-4" id="leads">
                <div class="doc__ep">
                    <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                    <code class="font-mono text-[0.9375rem] font-semibold break-all text-title">/v1/leads</code>
                </div>

                <h4 class="mt-5 font-title text-[0.875rem] font-bold text-title">{{ __('Query parameters') }}</h4>
                <div class="table-scroll">
                    <table class="d-table d-table--cards">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Parameter') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leadQueryParameters as $parameter)
                                <tr>
                                    <td data-card-title><code class="doc__inline">{{ $parameter['name'] }}</code></td>
                                    <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __($parameter['type']) }}</td>
                                    <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __($parameter['description']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Response') }}</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-res">
                            <span class="btn__label"><span>{{ __('Copy') }}</span><span aria-hidden="true">{{ __('Copy') }}</span></span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-res"><code>{
  "data": [
    {
      "id": 42,
      "name": "Barton Springs Dental",
      "category": "Dentist",
      "address": "2110 Barton Springs Rd, Austin, TX 78704",
      "phone": "+1 512 555 0142",
      "email": "dana@example.com",
      "website": "https://example.com",
      "score": 92,
      "score_signals": ["High review count", "No online booking"],
      "status": "new",
      "tags": ["high-reviews"],
      "lists": [{"id": 7, "name": "Austin pipeline"}],
      "created_at": "2026-08-11T09:14:22.000000Z"
    }
  ],
  "next_cursor": 42,
  "has_more": true
}</code></pre>
                </div>
            </section>

            <section class="form-card mt-4" id="lead">
                <div class="doc__ep">
                    <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                    <code class="font-mono text-[0.9375rem] font-semibold break-all text-title">/v1/leads/{id}</code>
                </div>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Request') }}</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-one">
                            <span class="btn__label"><span>{{ __('Copy') }}</span><span aria-hidden="true">{{ __('Copy') }}</span></span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-one"><code>curl "{{ $baseUrl }}/leads/42" \
  -H "Authorization: Bearer {{ $sampleToken }}"</code></pre>
                </div>
            </section>

            <section class="form-card mt-4" id="errors">
                <h3 class="form-card__title">{{ __('Errors') }}</h3>
                <div class="table-scroll">
                    <table class="d-table d-table--cards">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Code') }}</th>
                                <th scope="col">{{ __('Means') }}</th>
                                <th scope="col">{{ __('Do this') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($errors as $error)
                                <tr>
                                    <td data-card-title><code class="doc__inline">{{ $error['code'] }}</code></td>
                                    <td data-label="{{ __('Means') }}" class="d-table__muted">{{ __($error['means']) }}</td>
                                    <td data-label="{{ __('Do this') }}" class="d-table__muted">{{ __($error['action']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts.user>

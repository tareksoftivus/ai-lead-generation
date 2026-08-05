<x-layouts.user :title="__('API documentation')">
    @include('panels.user.api.nav', ['apiActive' => 'docs'])

    <div class="mb-4">
        <h2 class="heading-3">{{ __('API documentation') }}</h2>
        <p class="m-text mt-1">
            {{ __('Push leads into your own systems. Everything the app does, the API does — same endpoints, same credits, same rules.') }}
        </p>
    </div>

    <div class="doc">
        <div class="doc__main">
            <section class="form-card" id="start">
                <h3 class="form-card__title">{{ __('Getting started') }}</h3>
                <p class="form-card__hint">
                    {{ __('Every request needs a key and returns JSON. There is no SDK to install.') }}
                </p>

                <dl class="doc__facts">
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Base URL') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">
                            https://api.leadatlas.com/v1
                        </dd>
                    </div>
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Auth') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">
                            {{ __('Bearer token, in the header') }}
                        </dd>
                    </div>
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Format') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">
                            {{ __('JSON in, JSON out') }}
                        </dd>
                    </div>
                    <div class="doc__fact">
                        <dt class="text-[0.8125rem] text-body">{{ __('Rate limit') }}</dt>
                        <dd class="font-mono text-[0.8125rem] font-semibold break-all text-title">
                            <span class="numeric">120</span> {{ __('requests a minute') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">
                            {{ __('Your first request') }}
                        </span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-first">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-first"><code>curl https://api.leadatlas.com/v1/leads \
  -H "Authorization: Bearer sk_live_7f2a4c9e"</code></pre>
                </div>

                <p class="doc__note doc__note--free">
                    <i class="ph ph-coins" aria-hidden="true"></i>
                    <span>
                        {{ __('API calls spend credits at the same rate as the app — one per enriched business, returned if no contact is found. Reading leads you already own is free.') }}
                    </span>
                </p>
            </section>

            <section class="form-card mt-4" id="auth">
                <h3 class="form-card__title">{{ __('Authentication') }}</h3>
                <p class="form-card__hint">
                    {{ __('Create keys on the') }}
                    <a href="{{ route('user.api.keys') }}" class="cnew__link">{{ __('API keys') }}</a>
                    {{ __('screen. A full secret is shown once, when it is made.') }}
                </p>
                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">
                            {{ __('Header') }}
                        </span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-auth">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-auth"><code>Authorization: Bearer sk_live_7f2a4c9e</code></pre>
                </div>

                <div class="doc__scopes">
                    <div class="rounded-xl border border-neutral-200 px-4 py-3">
                        <span class="doc__scope-name">
                            <code class="doc__inline">sk_live_…</code>
                            {{ __('Full access') }}
                        </span>
                        <p class="mt-1 text-[0.8125rem] leading-[1.5] text-body">
                            {{ __('Reads leads and starts searches. Can spend credits.') }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-neutral-200 px-4 py-3">
                        <span class="doc__scope-name">
                            <code class="doc__inline">sk_test_…</code>
                            {{ __('Read-only') }}
                        </span>
                        <p class="mt-1 text-[0.8125rem] leading-[1.5] text-body">
                            {{ __('Fetches leads you already hold. Cannot start a search, so it cannot spend credits.') }}
                        </p>
                    </div>
                </div>

                <p class="doc__note doc__note--warn">
                    <i class="ph ph-warning" aria-hidden="true"></i>
                    <span>
                        {{ __('Keep keys server-side. A key in browser JavaScript is public, and anyone holding it can spend your credits.') }}
                    </span>
                </p>
            </section>

            <section class="form-card mt-4" id="leads">
                <div class="doc__ep">
                    <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                    <code class="font-mono text-[0.9375rem] font-semibold break-all text-title">/v1/leads</code>
                </div>
                <p class="form-card__hint mt-3">
                    {{ __('Every lead in your account, newest first. Free — you already paid for these.') }}
                </p>

                <h4 class="mt-5 font-title text-[0.875rem] font-bold text-title">
                    {{ __('Query parameters') }}
                </h4>
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
                            <tr>
                                <td data-card-title><code class="doc__inline">list_id</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('string') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('Only leads on one list.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">status</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('string') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('new, contacted, qualified, won, or lost.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">min_score</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('integer') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('0–100. Leads scoring at least this.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">limit</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('integer') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('1–100, default 25.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">cursor</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('string') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('The next_cursor from the last response.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4" data-tabs>
                    <div class="doc__block-head">
                        <div class="app-tablist doc__tablist">
                            <button type="button" class="app-tab is-active" data-tab="curl" aria-selected="true">cURL</button>
                            <button type="button" class="app-tab" data-tab="php" aria-selected="false">PHP</button>
                            <button type="button" class="app-tab" data-tab="node" aria-selected="false">Node</button>
                        </div>
                    </div>

                    <div data-tab-panel="curl">
                        <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                            <div class="doc__block-head">
                                <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Request') }}</span>
                                <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-curl">
                                    <span class="btn__label">
                                        <span>{{ __('Copy') }}</span>
                                        <span aria-hidden="true">{{ __('Copy') }}</span>
                                    </span>
                                    <i class="ph ph-copy copy-btn__idle"></i>
                                    <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                                </button>
                            </div>
                            <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-curl"><code>curl "https://api.leadatlas.com/v1/leads?min_score=70&amp;limit=25" \
  -H "Authorization: Bearer sk_live_7f2a4c9e"</code></pre>
                        </div>
                    </div>

                    <div data-tab-panel="php" class="is-hidden">
                        <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                            <div class="doc__block-head">
                                <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Request') }}</span>
                                <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-php">
                                    <span class="btn__label">
                                        <span>{{ __('Copy') }}</span>
                                        <span aria-hidden="true">{{ __('Copy') }}</span>
                                    </span>
                                    <i class="ph ph-copy copy-btn__idle"></i>
                                    <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                                </button>
                            </div>
                            <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-php"><code>$response = Http::withToken(config('leadatlas.key'))
    -&gt;get('https://api.leadatlas.com/v1/leads', [
        'min_score' =&gt; 70,
        'limit' =&gt; 25,
    ]);

$leads = $response-&gt;json()['data'];</code></pre>
                        </div>
                    </div>

                    <div data-tab-panel="node" class="is-hidden">
                        <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                            <div class="doc__block-head">
                                <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Request') }}</span>
                                <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-node">
                                    <span class="btn__label">
                                        <span>{{ __('Copy') }}</span>
                                        <span aria-hidden="true">{{ __('Copy') }}</span>
                                    </span>
                                    <i class="ph ph-copy copy-btn__idle"></i>
                                    <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                                </button>
                            </div>
                            <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-node"><code>const res = await fetch(
  "https://api.leadatlas.com/v1/leads?min_score=70&amp;limit=25",
  { headers: { Authorization: `Bearer ${process.env.LEADATLAS_KEY}` } },
);

const { data } = await res.json();</code></pre>
                        </div>
                    </div>
                </div>

                <h4 class="mt-5 font-title text-[0.875rem] font-bold text-title">
                    {{ __('Response') }}
                </h4>
                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">200 OK</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-res">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-res"><code>{
  "data": [
    {
      "id": "ld_8f31c2",
      "name": "Barton Springs Dental",
      "category": "Dentist",
      "address": "2110 Barton Springs Rd, Austin, TX 78704",
      "phone": "+1 512 555 0142",
      "email": "dana@bartonsprings.com",
      "website": "https://bartonsprings.com",
      "score": 92,
      "score_reasoning": "312 reviews at 4.8, but no online booking and
                          the site has not changed since 2019.",
      "status": "new",
      "tags": ["no-booking", "high-reviews"],
      "created_at": "2026-07-19T09:14:22Z"
    }
  ],
  "next_cursor": "ld_8f31c2",
  "has_more": true
}</code></pre>
                </div>

                <p class="doc__note doc__note--ai">
                    <i class="ph ph-sparkle" aria-hidden="true"></i>
                    <span>
                        <code class="doc__inline">score</code> {{ __('never travels without') }}
                        <code class="doc__inline">score_reasoning</code>. {{ __('If you show the number in your own system, show the sentence with it — a bare score tells your team nothing they can act on.') }}
                    </span>
                </p>
            </section>

            <section class="form-card mt-4" id="lead">
                <div class="doc__ep">
                    <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                    <code class="font-mono text-[0.9375rem] font-semibold break-all text-title">/v1/leads/{id}</code>
                </div>
                <p class="form-card__hint mt-3">
                    {{ __('One lead in full, including the signal breakdown behind its score. Free.') }}
                </p>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Request') }}</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-one">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-one"><code>curl https://api.leadatlas.com/v1/leads/ld_8f31c2 \
  -H "Authorization: Bearer sk_live_7f2a4c9e"</code></pre>
                </div>
            </section>

            <section class="form-card mt-4" id="search">
                <div class="doc__ep">
                    <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--post">POST</span>
                    <code class="font-mono text-[0.9375rem] font-semibold break-all text-title">/v1/searches</code>
                    <span class="shrink-0 rounded-full bg-accent/10 px-2 py-0.5 text-[0.6875rem] font-semibold text-accent-dark">
                        {{ __('Spends credits') }}
                    </span>
                </div>
                <p class="form-card__hint mt-3">
                    {{ __('Starts a search. Returns immediately with a job — searches run in the background, so poll the job or take the webhook.') }}
                </p>

                <h4 class="mt-5 font-title text-[0.875rem] font-bold text-title">
                    {{ __('Body parameters') }}
                </h4>
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
                            <tr>
                                <td data-card-title>
                                    <code class="doc__inline">keyword</code>
                                    <span class="ml-1.5 align-middle text-[0.6875rem] font-semibold text-error">required</span>
                                </td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('string') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('What to look for — "dentists".') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title>
                                    <code class="doc__inline">location</code>
                                    <span class="ml-1.5 align-middle text-[0.6875rem] font-semibold text-error">required</span>
                                </td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('string') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('Where — "Austin, TX".') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">radius_miles</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('integer') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('1–50, default 10.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">limit</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('integer') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('Most businesses to enrich. Caps what you can spend.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">webhook_url</code></td>
                                <td data-label="{{ __('Type') }}" class="d-table__muted">{{ __('string') }}</td>
                                <td data-label="{{ __('Description') }}" class="d-table__muted">{{ __('Called once when the search finishes.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Request') }}</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-search">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-search"><code>curl -X POST https://api.leadatlas.com/v1/searches \
  -H "Authorization: Bearer sk_live_7f2a4c9e" \
  -H "Content-Type: application/json" \
  -d '{
    "keyword": "dentists",
    "location": "Austin, TX",
    "radius_miles": 10,
    "limit": 200,
    "webhook_url": "https://yours.com/hooks/leadatlas"
  }'</code></pre>
                </div>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">202 {{ __('Accepted') }}</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-searchres">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-searchres"><code>{
  "id": "srch_4820",
  "status": "running",
  "found": 0,
  "credits_estimated": 200,
  "credits_spent": 0
}</code></pre>
                </div>

                <p class="doc__note doc__note--free">
                    <i class="ph ph-arrow-u-down-left" aria-hidden="true"></i>
                    <span>
                        <code class="doc__inline">credits_estimated</code> {{ __('is the ceiling, not the bill. You are charged one credit per business we actually enrich, and any credit spent on a business with no contact details is returned —') }}
                        <code class="doc__inline">credits_spent</code> {{ __('on the finished job is what you paid.') }}
                    </span>
                </p>
            </section>

            <section class="form-card mt-4" id="jobs">
                <div class="doc__ep">
                    <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                    <code class="font-mono text-[0.9375rem] font-semibold break-all text-title">/v1/searches/{id}</code>
                </div>
                <p class="form-card__hint mt-3">
                    {{ __('Where a search has got to. Free to poll — every ten seconds is a good rhythm until') }}
                    <code class="doc__inline">status</code>
                    {{ __('returns') }}
                    <code class="doc__inline">done</code>.
                </p>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">200 OK</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-job">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-job"><code>{
  "id": "srch_4820",
  "status": "done",
  "keyword": "dentists",
  "location": "Austin, TX",
  "found": 184,
  "credits_estimated": 200,
  "credits_spent": 172,
  "credits_returned": 12,
  "finished_at": "2026-07-19T09:31:04Z"
}</code></pre>
                </div>
            </section>

            <section class="form-card mt-4" id="errors">
                <h3 class="form-card__title">{{ __('Errors') }}</h3>
                <p class="form-card__hint">
                    {{ __('Standard HTTP codes. The body always says what to do about it.') }}
                </p>

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
                            <tr>
                                <td data-card-title><code class="doc__inline">401</code></td>
                                <td data-label="{{ __('Means') }}" class="d-table__muted">{{ __('Key missing, wrong, or revoked.') }}</td>
                                <td data-label="{{ __('Do this') }}" class="d-table__muted">{{ __('Check the header, then the key on screen 35.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">403</code></td>
                                <td data-label="{{ __('Means') }}" class="d-table__muted">{{ __('A read-only key tried to start a search.') }}</td>
                                <td data-label="{{ __('Do this') }}" class="d-table__muted">{{ __('Use a full-access key.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">402</code></td>
                                <td data-label="{{ __('Means') }}" class="d-table__muted">{{ __('Not enough credits for the search.') }}</td>
                                <td data-label="{{ __('Do this') }}" class="d-table__muted">{{ __('Lower limit, or top up.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">422</code></td>
                                <td data-label="{{ __('Means') }}" class="d-table__muted">{{ __('A parameter is missing or out of range.') }}</td>
                                <td data-label="{{ __('Do this') }}" class="d-table__muted">{{ __('Read errors — it names the field.') }}</td>
                            </tr>
                            <tr>
                                <td data-card-title><code class="doc__inline">429</code></td>
                                <td data-label="{{ __('Means') }}" class="d-table__muted">{{ __('Over 120 requests a minute.') }}</td>
                                <td data-label="{{ __('Do this') }}" class="d-table__muted">{{ __('Back off, then retry after Retry-After.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 overflow-hidden rounded-xl border border-neutral-200">
                    <div class="doc__block-head">
                        <span class="font-mono text-[0.6875rem] font-semibold tracking-wider text-body uppercase">{{ __('Error body') }}</span>
                        <button type="button" class="btn btn-sm btn-outline copy-btn" data-copy-target="#code-err">
                            <span class="btn__label">
                                <span>{{ __('Copy') }}</span>
                                <span aria-hidden="true">{{ __('Copy') }}</span>
                            </span>
                            <i class="ph ph-copy copy-btn__idle"></i>
                            <i class="ph ph-check copy-btn__done" aria-hidden="true"></i>
                        </button>
                    </div>
                    <pre class="overflow-x-auto bg-primary p-4 font-mono text-[0.75rem] leading-[1.7] text-neutral-0" id="code-err"><code>{
  "error": {
    "code": "insufficient_credits",
    "message": "This search needs 200 credits and you have 148.",
    "credits_available": 148
  }
}</code></pre>
                </div>
            </section>
        </div>

        <aside class="min-w-0">
            <nav class="form-card doc__nav" aria-label="{{ __('On this page') }}">
                <h3 class="form-card__title">{{ __('On this page') }}</h3>
                <ul class="doc__links">
                    <li><a href="#start" class="doc__link">{{ __('Getting started') }}</a></li>
                    <li><a href="#auth" class="doc__link">{{ __('Authentication') }}</a></li>
                    <li>
                        <a href="#leads" class="doc__link">
                            <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                            /v1/leads
                        </a>
                    </li>
                    <li>
                        <a href="#lead" class="doc__link">
                            <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                            /v1/leads/{id}
                        </a>
                    </li>
                    <li>
                        <a href="#search" class="doc__link">
                            <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--post">POST</span>
                            /v1/searches
                        </a>
                    </li>
                    <li>
                        <a href="#jobs" class="doc__link">
                            <span class="shrink-0 rounded px-1.5 py-0.5 font-mono text-[0.625rem] font-bold tracking-wider uppercase doc__verb--get">GET</span>
                            /v1/searches/{id}
                        </a>
                    </li>
                    <li><a href="#errors" class="doc__link">{{ __('Errors') }}</a></li>
                </ul>

                <a href="{{ route('user.api.keys') }}" class="btn btn-primary btn-sm mt-4 w-full">
                    <span class="btn__label">
                        <span>{{ __('Your API keys') }}</span>
                        <span aria-hidden="true">{{ __('Your API keys') }}</span>
                    </span>
                    <i class="ph ph-key"></i>
                </a>
            </nav>
        </aside>
    </div>
</x-layouts.user>

<x-layouts.user :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning, :name' : ($hour < 18 ? 'Good afternoon, :name' : 'Good evening, :name');
    @endphp

    <div class="page-top">
        <div>
            <h2 class="font-title text-[1.25rem] leading-tight font-bold tracking-[-0.02em] text-title md:text-[1.5rem]">
                {{ __($greeting, ['name' => $user->name ?? '']) }}
            </h2>
            <p class="mt-1 text-[0.875rem] text-body">
                <span class="numeric">{{ number_format($runningSearchCount ?? 0) }}</span> {{ __('searches running') }} ·
                <span class="numeric">{{ number_format($newLeadsSinceYesterday ?? 0) }}</span> {{ __('new leads since yesterday') }}
            </p>
        </div>

        <a href="{{ route('user.search.new') }}" class="btn btn-primary btn-sm">
            <span class="btn__label">
                <span>{{ __('New search') }}</span>
                <span aria-hidden="true">{{ __('New search') }}</span>
            </span>
            <i class="ph ph-plus"></i>
        </a>
    </div>

    <div class="kpis">
        <article class="kpi">
            <p class="kpi__label">{{ __('Credits remaining') }}</p>
            <p class="kpi__value numeric">{{ number_format($creditsRemaining ?? 0) }}</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    {{ __('of') }} <span class="numeric">{{ number_format($monthlyCredits ?? ($creditsRemaining ?? 0)) }}</span> {{ __('this month') }}
                </span>
                <a href="{{ route('user.credits.buy') }}" class="kpi__link">{{ __('Buy more') }}</a>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Searches this month') }}</p>
            <p class="kpi__value numeric">{{ number_format($searchesThisMonth ?? 0) }}</p>
            <p class="kpi__foot">
                <span class="kpi__delta {{ ($searchesDelta ?? 0) >= 0 ? 'kpi__delta--up' : 'kpi__delta--down' }}">
                    <i class="ph {{ ($searchesDelta ?? 0) >= 0 ? 'ph-trend-up' : 'ph-trend-down' }}" aria-hidden="true"></i>
                    <span class="numeric">{{ number_format(abs($searchesDelta ?? 0)) }}</span> {{ __('vs last month') }}
                </span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Leads found') }}</p>
            <p class="kpi__value numeric">{{ number_format($leadsFound ?? 0) }}</p>
            <p class="kpi__foot">
                <span class="kpi__delta {{ ($leadsDeltaPercent ?? 0) >= 0 ? 'kpi__delta--up' : 'kpi__delta--down' }}">
                    <i class="ph {{ ($leadsDeltaPercent ?? 0) >= 0 ? 'ph-trend-up' : 'ph-trend-down' }}" aria-hidden="true"></i>
                    <span class="numeric">{{ abs($leadsDeltaPercent ?? 0) }}%</span> {{ __('vs last month') }}
                </span>
            </p>
        </article>

        <article class="kpi kpi--ai">
            <p class="kpi__label">{{ __('Average lead score') }}</p>
            <p class="kpi__value numeric">{{ number_format($averageLeadScore ?? 0) }}</p>
            <p class="kpi__foot">
                <span class="kpi__note kpi__note--ai">
                    <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                    {{ __('Scored by AI') }}
                </span>
            </p>
        </article>
    </div>

    <section class="panel panel--live">
        <div class="panel__head">
            <h2 class="panel__title">
                <span class="live-dot" aria-hidden="true"></span>
                {{ __('Running now') }}
            </h2>
            <a href="{{ route('user.search.history') }}" class="panel__link">
                {{ __('All searches') }}
                <i class="ph ph-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="panel__body">
            @forelse ($runningSearches as $run)
                <article class="job">
                    <div class="job__head">
                        <p class="job__what">
                            <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                            {{ $run['label'] }}
                        </p>
                        <p class="shrink-0 text-[0.8125rem] text-body">
                            <span class="numeric">{{ number_format($run['found']) }}</span> {{ __('of') }}
                            <span class="numeric">{{ number_format($run['target']) }}</span> {{ __('found') }}
                        </p>
                    </div>
                    <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-neutral-100"
                         role="progressbar" aria-valuenow="{{ $run['progress'] }}" aria-valuemin="0" aria-valuemax="100"
                         aria-label="{{ __('Search progress') }}">
                        <span class="job__fill" style="--progress: {{ $run['progress'] }}%;"></span>
                    </div>
                    <div class="job__foot">
                        <span class="text-body">
                            {{ __('Started') }} {{ $run['started_at']?->diffForHumans() ?? __('recently') }}
                        </span>
                        <a href="{{ $run['url'] }}" class="font-medium text-primary transition-colors duration-200 hover:text-primary-dark">
                            {{ __('Watch progress') }}
                        </a>
                    </div>
                </article>
            @empty
                <div class="empty py-8">
                    <span class="empty__icon" aria-hidden="true">
                        <i class="ph ph-check-circle"></i>
                    </span>
                    <h2 class="empty__title">{{ __('No searches running') }}</h2>
                    <p class="empty__body">{{ __('Start a new search when you are ready to find more businesses.') }}</p>
                </div>
            @endforelse
        </div>
    </section>

    <div class="dash-split">
        <section class="panel">
            <div class="panel__head">
                <h2 class="panel__title">{{ __('Leads found') }}</h2>
                <span class="panel__meta">{{ __('Last 30 days') }}</span>
            </div>

            <div class="panel__body">
                <div class="h-[240px] w-full @3xl:h-[280px]">
                    <canvas data-chart="line" data-chart-color="discover"
                            data-chart-labels="{{ $chartLabels }}"
                            data-chart-values="{{ $chartValues }}"
                            aria-label="{{ __('Leads found per day over the last 30 days') }}"
                            role="img"></canvas>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <h2 class="panel__title">{{ __('Top scoring') }}</h2>
                <a href="{{ route('user.leads.index') }}" class="panel__link">
                    {{ __('All leads') }}
                    <i class="ph ph-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="panel__body">
                <ul class="tops">
                    @forelse ($topLeads as $lead)
                        @php
                            $bucket = \App\Modules\Leads\Models\Lead::scoreBucket($lead->score);
                        @endphp
                        <li class="top">
                            <span class="top__score top__score--{{ $bucket === 'hi' ? 'hi' : 'mid' }} numeric">{{ $lead->score ?? 0 }}</span>
                            <span class="top__who">
                                <a href="{{ route('user.leads.show', $lead) }}" class="truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ $lead->place?->name ?? __('Untitled lead') }}
                                </a>
                                <span class="truncate text-[0.75rem] text-body">{{ $lead->place?->formatted_address ?? __('No address') }}</span>
                            </span>
                        </li>
                    @empty
                        <li class="top">
                            <span class="top__score top__score--mid numeric">0</span>
                            <span class="top__who">
                                <a href="{{ route('user.search.new') }}" class="truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                    {{ __('Run a search to score leads') }}
                                </a>
                                <span class="truncate text-[0.75rem] text-body">{{ __('AI scores appear here automatically.') }}</span>
                            </span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </section>
    </div>

    <section class="panel">
        <div class="panel__head">
            <h2 class="panel__title">{{ __('Recent searches') }}</h2>
            <a href="{{ route('user.search.history') }}" class="panel__link">
                {{ __('View all') }}
                <i class="ph ph-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Search') }}</th>
                        <th scope="col">{{ __('Location') }}</th>
                        <th scope="col" class="tbl__num">{{ __('Found') }}</th>
                        <th scope="col" class="tbl__num">{{ __('Credits') }}</th>
                        <th scope="col">{{ __('Date') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('Actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentSearches as $run)
                        <tr>
                            <td class="tbl__key">{{ $run['keyword'] }}</td>
                            <td>{{ $run['location'] }}</td>
                            <td class="tbl__num numeric">{{ number_format($run['found']) }}</td>
                            <td class="tbl__num numeric">{{ number_format($run['credits']) }}</td>
                            <td class="text-neutral-500">
                                <time datetime="{{ $run['created_at']->toDateString() }}">{{ $run['created_at']->format('j M') }}</time>
                            </td>
                            <td class="tbl__act">
                                <a href="{{ $run['url'] }}" class="tbl__link">{{ __('Results') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-body">
                                {{ __('No searches yet. Run your first search to build this history.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel empty" @if ($hasDashboardActivity) hidden @endif>
        <span class="empty__icon" aria-hidden="true">
            <i class="ph ph-magnifying-glass"></i>
        </span>
        <h2 class="empty__title">{{ __('No searches yet') }}</h2>
        <p class="empty__body">
            {{ __('Pick a business type and a place, and LeadAtlas pulls every matching business off the map.') }}
            {{ __('You have') }} <span class="numeric">100</span> {{ __('free credits to start — searching itself costs nothing.') }}
        </p>
        <a href="{{ route('user.search.new') }}" class="btn btn-primary">
            <span class="btn__label">
                <span>{{ __('Run your first search') }}</span>
                <span aria-hidden="true">{{ __('Run your first search') }}</span>
            </span>
            <i class="ph ph-arrow-right"></i>
        </a>
    </section>
</x-layouts.user>

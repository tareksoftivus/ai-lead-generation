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
                <span class="numeric">2</span> {{ __('searches running') }} ·
                <span class="numeric">312</span> {{ __('new leads since yesterday') }}
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
            <p class="kpi__value numeric">2,480</p>
            <p class="kpi__foot">
                <span class="kpi__note">
                    {{ __('of') }} <span class="numeric">5,000</span> {{ __('this month') }}
                </span>
                <a href="#" class="kpi__link">{{ __('Buy more') }}</a>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Searches this month') }}</p>
            <p class="kpi__value numeric">18</p>
            <p class="kpi__foot">
                <span class="kpi__delta kpi__delta--up">
                    <i class="ph ph-trend-up" aria-hidden="true"></i>
                    <span class="numeric">4</span> {{ __('vs last month') }}
                </span>
            </p>
        </article>

        <article class="kpi">
            <p class="kpi__label">{{ __('Leads found') }}</p>
            <p class="kpi__value numeric">1,284</p>
            <p class="kpi__foot">
                <span class="kpi__delta kpi__delta--up">
                    <i class="ph ph-trend-up" aria-hidden="true"></i>
                    <span class="numeric">12%</span> {{ __('vs last month') }}
                </span>
            </p>
        </article>

        <article class="kpi kpi--ai">
            <p class="kpi__label">{{ __('Average lead score') }}</p>
            <p class="kpi__value numeric">74</p>
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
            <article class="job">
                <div class="job__head">
                    <p class="job__what">
                        <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                        {{ __('orthodontists in Dallas, TX') }}
                    </p>
                    <p class="shrink-0 text-[0.8125rem] text-body">
                        <span class="numeric">312</span> {{ __('of') }}
                        <span class="numeric">480</span> {{ __('enriched') }}
                    </p>
                </div>
                <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-neutral-100"
                     role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"
                     aria-label="{{ __('Search progress') }}">
                    <span class="job__fill job__fill--65"></span>
                </div>
                <div class="job__foot">
                    <span class="text-body">
                        {{ __('Started') }} <span class="numeric">6</span> {{ __('minutes ago') }}
                    </span>
                    <a href="#" class="font-medium text-primary transition-colors duration-200 hover:text-primary-dark">
                        {{ __('Watch progress') }}
                    </a>
                </div>
            </article>

            <article class="job">
                <div class="job__head">
                    <p class="job__what">
                        <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                        {{ __('med spas in Phoenix, AZ') }}
                    </p>
                    <p class="shrink-0 text-[0.8125rem] text-body">
                        <span class="numeric">88</span> {{ __('of') }}
                        <span class="numeric">240</span> {{ __('enriched') }}
                    </p>
                </div>
                <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-neutral-100"
                     role="progressbar" aria-valuenow="37" aria-valuemin="0" aria-valuemax="100"
                     aria-label="{{ __('Search progress') }}">
                    <span class="job__fill job__fill--37"></span>
                </div>
                <div class="job__foot">
                    <span class="text-body">
                        {{ __('Started') }} <span class="numeric">2</span> {{ __('minutes ago') }}
                    </span>
                    <a href="#" class="font-medium text-primary transition-colors duration-200 hover:text-primary-dark">
                        {{ __('Watch progress') }}
                    </a>
                </div>
            </article>
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
                            data-chart-labels="1 Jul,4 Jul,7 Jul,10 Jul,13 Jul,16 Jul,19 Jul,22 Jul,25 Jul,28 Jul"
                            data-chart-values="24,38,31,52,44,68,57,84,72,96"
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
                    <li class="top">
                        <span class="top__score top__score--hi numeric">92</span>
                        <span class="top__who">
                            <a href="#" class="truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Barton Springs Dental') }}
                            </a>
                            <span class="truncate text-[0.75rem] text-body">{{ __('Austin, TX') }}</span>
                        </span>
                    </li>
                    <li class="top">
                        <span class="top__score top__score--hi numeric">88</span>
                        <span class="top__who">
                            <a href="#" class="truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Lamar Family Dentistry') }}
                            </a>
                            <span class="truncate text-[0.75rem] text-body">{{ __('Austin, TX') }}</span>
                        </span>
                    </li>
                    <li class="top">
                        <span class="top__score top__score--hi numeric">84</span>
                        <span class="top__who">
                            <a href="#" class="truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Hill Country Orthodontics') }}
                            </a>
                            <span class="truncate text-[0.75rem] text-body">{{ __('Dallas, TX') }}</span>
                        </span>
                    </li>
                    <li class="top">
                        <span class="top__score top__score--mid numeric">79</span>
                        <span class="top__who">
                            <a href="#" class="truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Desert Bloom Med Spa') }}
                            </a>
                            <span class="truncate text-[0.75rem] text-body">{{ __('Phoenix, AZ') }}</span>
                        </span>
                    </li>
                    <li class="top">
                        <span class="top__score top__score--mid numeric">76</span>
                        <span class="top__who">
                            <a href="#" class="truncate text-[0.875rem] font-medium text-title transition-colors duration-200 hover:text-primary">
                                {{ __('Zilker Smile Studio') }}
                            </a>
                            <span class="truncate text-[0.75rem] text-body">{{ __('Austin, TX') }}</span>
                        </span>
                    </li>
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
                    <tr>
                        <td class="tbl__key">{{ __('dentists') }}</td>
                        <td>{{ __('Austin, TX') }}</td>
                        <td class="tbl__num numeric">184</td>
                        <td class="tbl__num numeric">172</td>
                        <td class="text-neutral-500">
                            <time datetime="2026-07-19">{{ __('19 Jul') }}</time>
                        </td>
                        <td class="tbl__act">
                            <a href="{{ route('user.leads.index') }}" class="tbl__link">{{ __('Results') }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbl__key">{{ __('orthodontists') }}</td>
                        <td>{{ __('Dallas, TX') }}</td>
                        <td class="tbl__num numeric">480</td>
                        <td class="tbl__num numeric">312</td>
                        <td class="text-neutral-500">
                            <time datetime="2026-07-19">{{ __('19 Jul') }}</time>
                        </td>
                        <td class="tbl__act">
                            <a href="{{ route('user.leads.index') }}" class="tbl__link">{{ __('Results') }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbl__key">{{ __('med spas') }}</td>
                        <td>{{ __('Phoenix, AZ') }}</td>
                        <td class="tbl__num numeric">240</td>
                        <td class="tbl__num numeric">88</td>
                        <td class="text-neutral-500">
                            <time datetime="2026-07-18">{{ __('18 Jul') }}</time>
                        </td>
                        <td class="tbl__act">
                            <a href="{{ route('user.leads.index') }}" class="tbl__link">{{ __('Results') }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbl__key">{{ __('chiropractors') }}</td>
                        <td>{{ __('Denver, CO') }}</td>
                        <td class="tbl__num numeric">156</td>
                        <td class="tbl__num numeric">141</td>
                        <td class="text-neutral-500">
                            <time datetime="2026-07-16">{{ __('16 Jul') }}</time>
                        </td>
                        <td class="tbl__act">
                            <a href="{{ route('user.leads.index') }}" class="tbl__link">{{ __('Results') }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="tbl__key">{{ __('law firms') }}</td>
                        <td>{{ __('Seattle, WA') }}</td>
                        <td class="tbl__num numeric">312</td>
                        <td class="tbl__num numeric">298</td>
                        <td class="text-neutral-500">
                            <time datetime="2026-07-14">{{ __('14 Jul') }}</time>
                        </td>
                        <td class="tbl__act">
                            <a href="{{ route('user.leads.index') }}" class="tbl__link">{{ __('Results') }}</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel empty" hidden>
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

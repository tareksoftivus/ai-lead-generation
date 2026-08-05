<x-layouts.user :title="__('Account settings')">
    <div class="mb-6">
        <h2 class="heading-3">{{ __('Account settings') }}</h2>
        <p class="m-text mt-1">
            {{ __('Your team, your search defaults, and what LeadAtlas emails you about.') }}
        </p>
    </div>

    <div class="panel" data-tabs>
        <nav class="app-tablist" aria-label="{{ __('Settings sections') }}">
            <button type="button" class="app-tab is-active" data-tab="general" aria-current="page">
                {{ __('General') }}
            </button>
            <button type="button" class="app-tab" data-tab="team">
                {{ __('Team') }}
                <span class="app-tab__count">3</span>
            </button>
            <button type="button" class="app-tab" data-tab="defaults">
                {{ __('Search defaults') }}
            </button>
            <button type="button" class="app-tab" data-tab="email">
                {{ __('Email') }}
            </button>
            <button type="button" class="app-tab" data-tab="danger">
                {{ __('Danger zone') }}
            </button>
        </nav>

        {{-- General --}}
        <div data-tab-panel="general">
            <form class="p-4 sm:p-5" action="#" method="post">
                <div class="form-card">
                    <h3 class="form-card__title">{{ __('Workspace') }}</h3>
                    <p class="form-card__hint">
                        {{ __('The name your team sees, and the timezone every date on your screens is shown in.') }}
                    </p>

                    <div class="set__grid">
                        <div>
                            <label for="w-name" class="form-label">{{ __('Workspace name') }}</label>
                            <input
                                type="text"
                                id="w-name"
                                name="workspace_name"
                                class="form-input"
                                value="Rivera Growth Studio"
                                required
                            />
                        </div>

                        <div>
                            <label for="w-tz" class="form-label">{{ __('Timezone') }}</label>
                            <select id="w-tz" name="timezone" class="form-input">
                                <option value="America/Chicago" selected>{{ __('Central Time — Chicago') }}</option>
                                <option value="America/New_York">{{ __('Eastern Time — New York') }}</option>
                                <option value="America/Denver">{{ __('Mountain Time — Denver') }}</option>
                                <option value="America/Los_Angeles">{{ __('Pacific Time — Los Angeles') }}</option>
                                <option value="Europe/London">{{ __('London') }}</option>
                                <option value="Asia/Dhaka">{{ __('Dhaka') }}</option>
                            </select>
                            <p class="form-hint">
                                {{ __('Search timestamps and credit entries use this.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="btn__label">
                            <span>{{ __('Save changes') }}</span>
                            <span aria-hidden="true">{{ __('Save changes') }}</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Team --}}
        <div data-tab-panel="team" class="is-hidden">
            <div class="p-4 sm:p-5">
                <div class="form-card">
                    <div class="set__head">
                        <div>
                            <h3 class="form-card__title">{{ __('Members') }}</h3>
                            <p class="form-card__hint">
                                {{ __('Everyone here shares the same credit balance. Seats are free — you are billed for credits, not people.') }}
                            </p>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm shrink-0" data-modal-open="inviteModal">
                            <span class="btn__label">
                                <span>{{ __('Invite member') }}</span>
                                <span aria-hidden="true">{{ __('Invite member') }}</span>
                            </span>
                            <i class="ph ph-plus"></i>
                        </button>
                    </div>

                    <div class="tbl-wrap mt-4">
                        <table class="d-table d-table--cards">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Member') }}</th>
                                    <th scope="col">{{ __('Role') }}</th>
                                    <th scope="col">{{ __('Last active') }}</th>
                                    <th scope="col">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td data-card-title>
                                        <span class="set__who">
                                            <span class="set__avatar" aria-hidden="true">AR</span>
                                            <span class="min-w-0">
                                                <span class="block truncate font-medium text-title">Amara Rivera</span>
                                                <span class="block truncate text-[0.8125rem] text-body">amara@riveragrowth.co</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td data-label="Role">
                                        <span class="status status--qualified">{{ __('Owner') }}</span>
                                    </td>
                                    <td data-label="Last active" class="d-table__muted whitespace-nowrap">
                                        <time datetime="2026-07-21">{{ __('Today') }}</time>
                                    </td>
                                    <td data-card-actions class="text-right">
                                        <span class="d-table__muted text-[0.8125rem]">
                                            {{ __('That is you') }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td data-card-title>
                                        <span class="set__who">
                                            <span class="set__avatar" aria-hidden="true">LF</span>
                                            <span class="min-w-0">
                                                <span class="block truncate font-medium text-title">Luis Ferrer</span>
                                                <span class="block truncate text-[0.8125rem] text-body">luis@riveragrowth.co</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td data-label="Role">
                                        <label for="r-luis" class="sr-only">{{ __('Role for Luis Ferrer') }}</label>
                                        <select id="r-luis" name="role[2]" class="form-input h-9 w-auto min-w-[7.5rem] py-0 text-[0.8125rem]">
                                            <option value="admin" selected>{{ __('Admin') }}</option>
                                            <option value="member">{{ __('Member') }}</option>
                                            <option value="viewer">{{ __('Viewer') }}</option>
                                        </select>
                                    </td>
                                    <td data-label="Last active" class="d-table__muted whitespace-nowrap">
                                        <time datetime="2026-07-20">{{ __('Yesterday') }}</time>
                                    </td>
                                    <td data-card-actions class="text-right">
                                        <button
                                            type="button"
                                            class="row-icon"
                                            data-confirm
                                            data-confirm-title="{{ __('Remove Luis Ferrer?') }}"
                                            data-confirm-body="{{ __('They lose access straight away. Leads, searches, and notes they created stay in the workspace.') }}"
                                            data-confirm-label="{{ __('Remove member') }}"
                                            data-confirm-variant="error"
                                            data-id="2"
                                            aria-label="{{ __('Remove Luis Ferrer') }}"
                                        >
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td data-card-title>
                                        <span class="set__who">
                                            <span class="set__avatar" aria-hidden="true">PR</span>
                                            <span class="min-w-0">
                                                <span class="block truncate font-medium text-title">Priya Raman</span>
                                                <span class="block truncate text-[0.8125rem] text-body">priya@riveragrowth.co</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td data-label="Role">
                                        <label for="r-priya" class="sr-only">{{ __('Role for Priya Raman') }}</label>
                                        <select id="r-priya" name="role[3]" class="form-input h-9 w-auto min-w-[7.5rem] py-0 text-[0.8125rem]">
                                            <option value="admin">{{ __('Admin') }}</option>
                                            <option value="member" selected>{{ __('Member') }}</option>
                                            <option value="viewer">{{ __('Viewer') }}</option>
                                        </select>
                                    </td>
                                    <td data-label="Last active" class="d-table__muted whitespace-nowrap">
                                        <time datetime="2026-07-17">17 {{ __('Jul') }}</time>
                                    </td>
                                    <td data-card-actions class="text-right">
                                        <button
                                            type="button"
                                            class="row-icon"
                                            data-confirm
                                            data-confirm-title="{{ __('Remove Priya Raman?') }}"
                                            data-confirm-body="{{ __('They lose access straight away. Leads, searches, and notes they created stay in the workspace.') }}"
                                            data-confirm-label="{{ __('Remove member') }}"
                                            data-confirm-variant="error"
                                            data-id="3"
                                            aria-label="{{ __('Remove Priya Raman') }}"
                                        >
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <dl class="set__roles">
                        <div class="set__role-row">
                            <dt class="w-20 shrink-0 text-[0.8125rem] font-semibold text-title">{{ __('Admin') }}</dt>
                            <dd class="min-w-0 flex-1 text-[0.8125rem] leading-[1.55] text-body">
                                {{ __('Everything except billing and deleting the workspace.') }}
                            </dd>
                        </div>
                        <div class="set__role-row">
                            <dt class="w-20 shrink-0 text-[0.8125rem] font-semibold text-title">{{ __('Member') }}</dt>
                            <dd class="min-w-0 flex-1 text-[0.8125rem] leading-[1.55] text-body">
                                {{ __('Runs searches and works leads. Spends credits.') }}
                            </dd>
                        </div>
                        <div class="set__role-row">
                            <dt class="w-20 shrink-0 text-[0.8125rem] font-semibold text-title">{{ __('Viewer') }}</dt>
                            <dd class="min-w-0 flex-1 text-[0.8125rem] leading-[1.55] text-body">
                                {{ __('Reads leads and exports. Cannot spend credits.') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Search defaults --}}
        <div data-tab-panel="defaults" class="is-hidden">
            <form class="p-4 sm:p-5" action="#" method="post">
                <div class="form-card">
                    <h3 class="form-card__title">{{ __('New search defaults') }}</h3>
                    <p class="form-card__hint">
                        {{ __('What a new search starts with. You can change any of it before running — and the credit cost is always shown first.') }}
                    </p>

                    <div class="set__grid">
                        <div>
                            <label for="d-location" class="form-label">{{ __('Default location') }}</label>
                            <input
                                type="text"
                                id="d-location"
                                name="default_location"
                                class="form-input"
                                value="Austin, TX"
                                placeholder="{{ __('City, state') }}"
                            />
                        </div>

                        <div>
                            <label for="d-radius" class="form-label">{{ __('Default radius') }}</label>
                            <select id="d-radius" name="default_radius" class="form-input">
                                <option value="5">{{ __('5 miles') }}</option>
                                <option value="10" selected>{{ __('10 miles') }}</option>
                                <option value="15">{{ __('15 miles') }}</option>
                                <option value="25">{{ __('25 miles') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="d-rating" class="form-label">{{ __('Minimum rating') }}</label>
                            <select id="d-rating" name="min_rating" class="form-input">
                                <option value="0" selected>{{ __('Any rating') }}</option>
                                <option value="3">{{ __('3.0 and up') }}</option>
                                <option value="4">{{ __('4.0 and up') }}</option>
                                <option value="4.5">{{ __('4.5 and up') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="d-reviews" class="form-label">{{ __('Minimum reviews') }}</label>
                            <input
                                type="number"
                                id="d-reviews"
                                name="min_reviews"
                                class="form-input"
                                value="10"
                                min="0"
                                step="1"
                            />
                        </div>
                    </div>
                </div>

                <div class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('What to skip') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Businesses matching these are left out of results, so you never spend a credit enriching them.') }}
                    </p>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="sk-nophone" class="setting-row__label">{{ __('Skip businesses with no phone number') }}</label>
                            <p class="setting-row__hint">{{ __('A listing with no phone rarely has a reachable contact.') }}</p>
                        </div>
                        <input type="checkbox" id="sk-nophone" name="skip_no_phone" class="switch" checked />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="sk-closed" class="setting-row__label">{{ __('Skip permanently closed') }}</label>
                            <p class="setting-row__hint">{{ __('Google still lists them; they are never worth calling.') }}</p>
                        </div>
                        <input type="checkbox" id="sk-closed" name="skip_closed" class="switch" checked />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="sk-seen" class="setting-row__label">{{ __('Skip businesses already in my leads') }}</label>
                            <p class="setting-row__hint">
                                {{ __('You are never charged twice for the same business, but this keeps them out of the results too.') }}
                            </p>
                        </div>
                        <input type="checkbox" id="sk-seen" name="skip_seen" class="switch" checked />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="btn__label">
                            <span>{{ __('Save defaults') }}</span>
                            <span aria-hidden="true">{{ __('Save defaults') }}</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Email --}}
        <div data-tab-panel="email" class="is-hidden">
            <form class="p-4 sm:p-5" action="#" method="post">
                <div class="form-card">
                    <h3 class="form-card__title">{{ __('What we email you') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Sent to') }}
                        <strong class="text-title">amara@riveragrowth.co</strong>.
                    </p>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-done" class="setting-row__label">{{ __('When a search finishes') }}</label>
                            <p class="setting-row__hint">
                                {{ __('Large searches run in the background — this tells you the results are ready.') }}
                            </p>
                        </div>
                        <input type="checkbox" id="e-done" name="email_search_done" class="switch" checked />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-low" class="setting-row__label">{{ __('When credits run low') }}</label>
                            <p class="setting-row__hint">
                                {{ __('At 10% of your monthly allowance, so a search does not stop halfway.') }}
                            </p>
                        </div>
                        <input type="checkbox" id="e-low" name="email_low_credits" class="switch" checked />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-weekly" class="setting-row__label">{{ __('Weekly summary') }}</label>
                            <p class="setting-row__hint">
                                {{ __('Searches run, leads found, and credits spent over the past seven days.') }}
                            </p>
                        </div>
                        <input type="checkbox" id="e-weekly" name="email_weekly" class="switch" />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-product" class="setting-row__label">{{ __('Product news') }}</label>
                            <p class="setting-row__hint">
                                {{ __('New features and changes. Rarely — a few times a year.') }}
                            </p>
                        </div>
                        <input type="checkbox" id="e-product" name="email_product" class="switch" />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="btn__label">
                            <span>{{ __('Save preferences') }}</span>
                            <span aria-hidden="true">{{ __('Save preferences') }}</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger zone --}}
        <div data-tab-panel="danger" class="is-hidden">
            <div class="p-4 sm:p-5">
                <div class="rounded-2xl border border-error/30 bg-error/5 p-5">
                    <h3 class="form-card__title">{{ __('Delete this workspace') }}</h3>
                    <p class="form-card__hint">
                        {{ __('This cannot be undone, and it is immediate.') }}
                    </p>

                    <ul class="set__danger-list">
                        <li>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span>
                                <span class="numeric">1,284</span> {{ __('leads and every AI analysis written about them') }}
                            </span>
                        </li>
                        <li>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span>
                                <span class="numeric">18</span> {{ __('saved searches and their history') }}
                            </span>
                        </li>
                        <li>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span>
                                {{ __('Access for all') }} <span class="numeric">3</span> {{ __('members') }}
                            </span>
                        </li>
                        <li>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span>
                                <span class="numeric">2,480</span> {{ __('unused credits — these are not refunded') }}
                            </span>
                        </li>
                    </ul>

                    <p class="set__danger-note">
                        <i class="ph ph-download-simple" aria-hidden="true"></i>
                        <span>
                            {{ __('Export your leads first if you want to keep them — exporting is free and does not spend credits.') }}
                        </span>
                    </p>

                    <div class="set__danger-act">
                        <a href="{{ route('user.export.index') }}" class="btn btn-outline btn-sm">
                            <span class="btn__label">
                                <span>{{ __('Export leads first') }}</span>
                                <span aria-hidden="true">{{ __('Export leads first') }}</span>
                            </span>
                            <i class="ph ph-download-simple"></i>
                        </a>

                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            data-confirm
                            data-confirm-title="{{ __('Delete this workspace?') }}"
                            data-confirm-body="{{ __('1,284 leads, 18 searches, and 2,480 unused credits are deleted immediately. All 3 members lose access. This cannot be undone.') }}"
                            data-confirm-label="{{ __('Delete everything') }}"
                            data-confirm-variant="error"
                        >
                            <span class="btn__label">
                                <span>{{ __('Delete workspace') }}</span>
                                <span aria-hidden="true">{{ __('Delete workspace') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
        {{-- Invite member --}}
        <div class="modal" id="inviteModal" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="modal__backdrop" data-modal-close></div>
            <div class="modal__panel max-w-md p-6" aria-labelledby="inviteTitle">
                <form action="#" method="post">
                    <h2 class="heading-3" id="inviteTitle">{{ __('Invite a member') }}</h2>
                    <p class="m-text mt-2 mb-5">
                        {{ __('They join this workspace and share its credit balance. Seats are free — you are billed for credits, not people.') }}
                    </p>

                    <div>
                        <label for="i-email" class="form-label">{{ __('Email address') }}</label>
                        <input
                            type="email"
                            id="i-email"
                            name="invite_email"
                            class="form-input"
                            placeholder="{{ __('name@company.com') }}"
                            required
                        />
                        <p class="form-hint">
                            {{ __('They get an invite link that expires in 7 days.') }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <label for="i-role" class="form-label">{{ __('Role') }}</label>
                        <select id="i-role" name="invite_role" class="form-input">
                            <option value="member" selected>{{ __('Member') }}</option>
                            <option value="admin">{{ __('Admin') }}</option>
                            <option value="viewer">{{ __('Viewer') }}</option>
                        </select>
                        <p class="form-hint">{{ __('A viewer cannot spend credits.') }}</p>
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
                                <span>{{ __('Send invite') }}</span>
                                <span aria-hidden="true">{{ __('Send invite') }}</span>
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

<x-layouts.user :title="__('Account settings')">
    @php
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('') ?: 'U';
        $searchDefaults = $searchDefaults ?? [];
        $emailPreferences = $emailPreferences ?? [];
    @endphp

    <div class="mb-6">
        <h2 class="heading-3">{{ __('Account settings') }}</h2>
    </div>

    <div class="panel" data-tabs>
        <nav class="app-tablist" aria-label="{{ __('Settings sections') }}">
            <button type="button" class="app-tab is-active" data-tab="general" aria-current="page">
                {{ __('General') }}
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
            <form class="p-4 sm:p-5" action="{{ route('user.settings.general.update') }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-card">
                    <h3 class="form-card__title">{{ __('Workspace') }}</h3>

                    <div class="set__grid">
                        <div>
                            <label for="w-name" class="form-label">{{ __('Workspace name') }}</label>
                            <input
                                type="text"
                                id="w-name"
                                name="workspace_name"
                                class="form-input"
                                value="{{ old('workspace_name', $settings->workspace_name) }}"
                                required
                            />
                            @error('workspace_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="w-tz" class="form-label">{{ __('Timezone') }}</label>
                            <select id="w-tz" name="timezone" class="form-input">
                                @foreach ($timezones as $timezone)
                                    <option value="{{ $timezone }}" @selected(old('timezone', $settings->timezone) === $timezone)>
                                        {{ str_replace('_', ' ', $timezone) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
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

        {{-- Search defaults --}}
        <div data-tab-panel="defaults" class="is-hidden">
            <form class="p-4 sm:p-5" action="{{ route('user.settings.search-defaults.update') }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-card">
                    <h3 class="form-card__title">{{ __('New search defaults') }}</h3>

                    <div class="set__grid">
                        <div>
                            <label for="d-location" class="form-label">{{ __('Default location') }}</label>
                            <input
                                type="text"
                                id="d-location"
                                name="default_location"
                                class="form-input"
                                value="{{ old('default_location', $searchDefaults['default_location'] ?? '') }}"
                                placeholder="{{ __('City, state') }}"
                            />
                            @error('default_location')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="d-radius" class="form-label">{{ __('Default radius') }}</label>
                            <select id="d-radius" name="default_radius" class="form-input">
                                @foreach ([5, 10, 15, 25, 50] as $radius)
                                    <option value="{{ $radius }}" @selected((int) old('default_radius', $searchDefaults['default_radius'] ?? 10) === $radius)>
                                        {{ trans_choice(':count mile|:count miles', $radius, ['count' => $radius]) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('default_radius')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="d-rating" class="form-label">{{ __('Minimum rating') }}</label>
                            <select id="d-rating" name="min_rating" class="form-input">
                                <option value="" @selected(old('min_rating', $searchDefaults['min_rating'] ?? '') === null || old('min_rating', $searchDefaults['min_rating'] ?? '') === '')>{{ __('Any rating') }}</option>
                                <option value="3" @selected((string) old('min_rating', $searchDefaults['min_rating'] ?? '') === '3')>{{ __('3.0 and up') }}</option>
                                <option value="4" @selected((string) old('min_rating', $searchDefaults['min_rating'] ?? '') === '4')>{{ __('4.0 and up') }}</option>
                                <option value="4.5" @selected((string) old('min_rating', $searchDefaults['min_rating'] ?? '') === '4.5')>{{ __('4.5 and up') }}</option>
                            </select>
                            @error('min_rating')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="d-reviews" class="form-label">{{ __('Minimum reviews') }}</label>
                            <input
                                type="number"
                                id="d-reviews"
                                name="min_reviews"
                                class="form-input"
                                value="{{ old('min_reviews', $searchDefaults['min_reviews'] ?? 10) }}"
                                min="0"
                                step="1"
                            />
                            @error('min_reviews')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-card mt-4">
                    <h3 class="form-card__title">{{ __('What to skip') }}</h3>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="sk-nophone" class="setting-row__label">{{ __('Skip businesses with no phone number') }}</label>
                        </div>
                        <input type="checkbox" id="sk-nophone" name="skip_no_phone" class="switch" value="1" @checked(old('skip_no_phone', $searchDefaults['skip_no_phone'] ?? true)) />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="sk-closed" class="setting-row__label">{{ __('Skip permanently closed') }}</label>
                        </div>
                        <input type="checkbox" id="sk-closed" name="skip_closed" class="switch" value="1" @checked(old('skip_closed', $searchDefaults['skip_closed'] ?? true)) />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="sk-seen" class="setting-row__label">{{ __('Skip businesses already in my leads') }}</label>

                        </div>
                        <input type="checkbox" id="sk-seen" name="skip_seen" class="switch" value="1" @checked(old('skip_seen', $searchDefaults['skip_seen'] ?? true)) />
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
            <form class="p-4 sm:p-5" action="{{ route('user.settings.email-preferences.update') }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-card">
                    <h3 class="form-card__title">{{ __('What we email you') }}</h3>
                    <p class="form-card__hint">
                        {{ __('Sent to') }}
                        <strong class="text-title">{{ $user->email }}</strong>.
                    </p>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-done" class="setting-row__label">{{ __('When a search finishes') }}</label>
                        </div>
                        <input type="checkbox" id="e-done" name="email_search_done" class="switch" value="1" @checked(old('email_search_done', $emailPreferences['email_search_done'] ?? true)) />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-low" class="setting-row__label">{{ __('When credits run low') }}</label>
                        </div>
                        <input type="checkbox" id="e-low" name="email_low_credits" class="switch" value="1" @checked(old('email_low_credits', $emailPreferences['email_low_credits'] ?? true)) />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-weekly" class="setting-row__label">{{ __('Weekly summary') }}</label>
                        </div>
                        <input type="checkbox" id="e-weekly" name="email_weekly" class="switch" value="1" @checked(old('email_weekly', $emailPreferences['email_weekly'] ?? false)) />
                    </div>

                    <div class="setting-row">
                        <div class="setting-row__text">
                            <label for="e-product" class="setting-row__label">{{ __('Product news') }}</label>
                        </div>
                        <input type="checkbox" id="e-product" name="email_product" class="switch" value="1" @checked(old('email_product', $emailPreferences['email_product'] ?? false)) />
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
                        {{ __('This permanently deletes your account and records owned by it. This cannot be undone.') }}
                    </p>

                    <ul class="set__danger-list">
                        <li>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span>
                                <span class="numeric">{{ number_format($workspaceStats['leads']) }}</span> {{ __('leads') }}
                                @if ($workspaceStats['analyses'] > 0)
                                    {{ __('and') }} <span class="numeric">{{ number_format($workspaceStats['analyses']) }}</span> {{ __('AI analyses') }}
                                @endif
                            </span>
                        </li>
                        <li>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span>
                                <span class="numeric">{{ number_format($workspaceStats['searches']) }}</span> {{ __('saved searches and search history entries') }}
                            </span>
                        </li>
                        <li>
                            <i class="ph ph-x" aria-hidden="true"></i>
                            <span>
                                <span class="numeric">{{ number_format($workspaceStats['credits']) }}</span> {{ __('unused credits — these are not refunded') }}
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

                        <form id="deleteWorkspaceForm" action="{{ route('user.settings.workspace.destroy') }}" method="post" class="grid w-full gap-3 sm:max-w-md">
                            @csrf
                            @method('DELETE')

                            <div>
                                <label for="confirm-workspace" class="form-label">{{ __('Type workspace name to confirm') }}</label>
                                <input
                                    type="text"
                                    id="confirm-workspace"
                                    name="confirm_workspace"
                                    class="form-input"
                                    placeholder="{{ $settings->workspace_name }}"
                                    autocomplete="off"
                                    required
                                />
                                @error('confirm_workspace')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="delete-current-password" class="form-label">{{ __('Current password') }}</label>
                                <input
                                    type="password"
                                    id="delete-current-password"
                                    name="current_password"
                                    class="form-input"
                                    autocomplete="current-password"
                                    required
                                />
                                @error('current_password')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                data-confirm
                                data-submit-form="deleteWorkspaceForm"
                                data-confirm-title="{{ __('Delete :workspace?', ['workspace' => $settings->workspace_name]) }}"
                                data-confirm-body="{{ __('This permanently deletes :leads leads, :searches search records, and :credits unused credits for this account. This cannot be undone.', ['leads' => number_format($workspaceStats['leads']), 'searches' => number_format($workspaceStats['searches']), 'credits' => number_format($workspaceStats['credits'])]) }}"
                                data-confirm-label="{{ __('Delete everything') }}"
                                data-confirm-variant="error"
                            >
                                <span class="btn__label">
                                    <span>{{ __('Delete workspace') }}</span>
                                    <span aria-hidden="true">{{ __('Delete workspace') }}</span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
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

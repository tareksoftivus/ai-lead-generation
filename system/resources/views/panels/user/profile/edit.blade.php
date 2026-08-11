<x-layouts.user :title="__('Profile')">
    <div class="mb-6">
        <h2 class="heading-3">{{ __('Profile') }}</h2>
        <p class="m-text mt-1">
            {{ __('Your identity details. Workspace display, timezone, and search defaults live in') }}
            <a href="{{ route('user.settings.index') }}" class="font-medium text-primary underline underline-offset-2 hover:no-underline">
                {{ __('account settings') }}
            </a>.
        </p>
    </div>

    <div class="pro">
        {{-- LEFT: the details --}}
        <div class="pro__main">
            <form class="form-card" id="profile-form" method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h3 class="form-card__title">{{ __('Your details') }}</h3>
                <p class="form-card__hint">
                    {{ __('The name shown on your notes, activities, and saved lists.') }}
                </p>

                <div class="pro__grid">
                    <div>
                        <label for="p-name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" id="p-name" name="name" class="form-input" value="{{ old('name', $user->name) }}" required />
                    </div>

                    <div>
                        <label for="p-phone" class="form-label">{{ __('Phone number') }}</label>
                        <input type="tel" id="p-phone" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="{{ __('Optional') }}" />
                    </div>
                </div>

                <div class="mt-4">
                    <label for="p-email" class="form-label">{{ __('Email address') }}</label>
                    <input type="email" id="p-email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required />
                    <p class="form-hint">
                        {{ __('You sign in with this, and it is where alerts go.') }}
                    </p>
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

            <form class="form-card mt-4" method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PUT')

                <h3 class="form-card__title">{{ __('Change password') }}</h3>
                <p class="form-card__hint">
                    {{ __('You stay signed in here; other devices are signed out.') }}
                </p>

                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="phone" value="{{ $user->phone }}">

                <div class="mt-4">
                    <label for="p-current" class="form-label">{{ __('Current password') }}</label>
                    <div class="password-field">
                        <input type="password" id="p-current" name="current_password" class="form-input" autocomplete="current-password" required />
                        <button type="button" class="password-field__toggle" data-password-toggle aria-label="{{ __('Show password') }}">
                            <i class="ph ph-eye" aria-hidden="true"></i>
                            <i class="ph ph-eye-slash" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="pro__grid mt-4">
                    <div>
                        <label for="p-new" class="form-label">{{ __('New password') }}</label>
                        <div class="password-field">
                            <input type="password" id="p-new" name="password" class="form-input" autocomplete="new-password" minlength="8" required />
                            <button type="button" class="password-field__toggle" data-password-toggle aria-label="{{ __('Show password') }}">
                                <i class="ph ph-eye" aria-hidden="true"></i>
                                <i class="ph ph-eye-slash" aria-hidden="true"></i>
                            </button>
                        </div>
                        <p class="form-hint">
                            {{ __('At least') }} <span class="numeric">8</span> {{ __('characters') }}.
                        </p>
                    </div>

                    <div>
                        <label for="p-confirm" class="form-label">{{ __('Confirm new password') }}</label>
                        <div class="password-field">
                            <input type="password" id="p-confirm" name="password_confirmation" class="form-input" autocomplete="new-password" minlength="8" required />
                            <button type="button" class="password-field__toggle" data-password-toggle aria-label="{{ __('Show password') }}">
                                <i class="ph ph-eye" aria-hidden="true"></i>
                                <i class="ph ph-eye-slash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="btn__label">
                            <span>{{ __('Update password') }}</span>
                            <span aria-hidden="true">{{ __('Update password') }}</span>
                        </span>
                    </button>
                </div>
            </form>

            @include('panels.user.profile._two-factor-setup')
        </div>

        {{-- RIGHT: photo + connections + sessions --}}
        <aside class="min-w-0">
            <div class="form-card">
                <h3 class="form-card__title">{{ __('Photo') }}</h3>
                <p class="form-card__hint">
                    {{ __('Shown on your notes and account menu.') }}
                </p>

                <div class="pro__photo">
                    <span class="pro__avatar">
                        <img
                            id="p-avatar-img"
                            src="{{ $user->avatar ? Storage::disk('public')->url($user->avatar) : asset('assets/images/avatars/avatar-1.jpg') }}"
                            alt="{{ __('Your profile photo') }}"
                            width="80"
                            height="80"
                        />
                    </span>

                    <div class="min-w-0 flex-1 basis-40">
                        <label for="p-avatar" class="btn btn-outline btn-sm">
                            <span class="btn__label">
                                <span>{{ __('Change photo') }}</span>
                                <span aria-hidden="true">{{ __('Change photo') }}</span>
                            </span>
                        </label>
                        <input
                            type="file"
                            id="p-avatar"
                            name="avatar"
                            class="sr-only"
                            accept="image/png, image/jpeg, image/webp"
                            data-preview-swap="#p-avatar-img"
                            form="profile-form"
                        />
                        <p class="form-hint">{{ __('JPG, PNG, or WebP. Up to 2 MB.') }}</p>
                    </div>
                </div>
            </div>

            <div class="form-card mt-4">
                <h3 class="form-card__title">{{ __('Connected accounts') }}</h3>
                <p class="form-card__hint">
                    {{ __('Other ways to sign in to this account.') }}
                </p>

                <ul class="pro__conns">
                    <li class="pro__conn">
                        <span class="pro__conn-mark" aria-hidden="true">
                            <i class="ph ph-google-logo"></i>
                        </span>
                        <span class="pro__conn-body">
                            <span class="pro__conn-key">{{ __('Google') }}</span>
                            <span class="pro__conn-val">{{ $user->email }}</span>
                        </span>
                        <button type="button" class="btn btn-outline btn-sm shrink-0" data-confirm data-confirm-title="{{ __('Disconnect Google?') }}" data-confirm-body="{{ __('You will only be able to sign in with your email and password. Make sure you know it before disconnecting.') }}" data-confirm-label="{{ __('Disconnect') }}" data-confirm-variant="error" data-id="google">
                            <span class="btn__label">
                                <span>{{ __('Disconnect') }}</span>
                                <span aria-hidden="true">{{ __('Disconnect') }}</span>
                            </span>
                        </button>
                    </li>

                    <li class="pro__conn">
                        <span class="pro__conn-mark" aria-hidden="true">
                            <i class="ph ph-microsoft-outlook-logo"></i>
                        </span>
                        <span class="pro__conn-body">
                            <span class="pro__conn-key">{{ __('Microsoft') }}</span>
                            <span class="pro__conn-val pro__conn-val--off">{{ __('Not connected') }}</span>
                        </span>
                        <button type="button" class="btn btn-outline btn-sm shrink-0">
                            <span class="btn__label">
                                <span>{{ __('Connect') }}</span>
                                <span aria-hidden="true">{{ __('Connect') }}</span>
                            </span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="form-card mt-4">
                <h3 class="form-card__title">{{ __('Signed in on') }}</h3>
                <p class="form-card__hint">
                    {{ __('Sign out everywhere if you have lost a device.') }}
                </p>

                <ul class="pro__sessions">
                    @forelse($sessions as $session)
                        <li class="pro__session">
                            <span class="pro__session-body">
                                <span class="pro__session-key">
                                    {{ $session->browser }} {{ __('on') }} {{ $session->platform }} — {{ $session->ip_address }}
                                    @if($session->is_current)
                                        <span class="badge badge-soft shrink-0 text-[0.6875rem]">{{ __('This device') }}</span>
                                    @endif
                                </span>
                                <span class="mt-0.5 truncate text-[0.8125rem] text-body">
                                    @if($session->is_current)
                                        {{ __('active now') }}
                                    @else
                                        {{ __('last active') }} {{ $session->last_activity->diffForHumans() }}
                                    @endif
                                </span>
                            </span>

                            @unless($session->is_current)
                                <button type="button" class="btn btn-outline btn-sm shrink-0" data-modal-trigger="confirmRevokeSession-{{ $loop->index }}">
                                    {{ __('Revoke') }}
                                </button>
                            @endunless
                        </li>

                        @unless($session->is_current)
                            <x-ui.confirm
                                :id="'confirmRevokeSession-' . $loop->index"
                                :title="__('Revoke Session?')"
                                :message="__('Are you sure you want to revoke this session? The device will be signed out immediately.')"
                                :confirmText="__('Yes, Revoke')"
                                :formId="'revoke-session-' . $loop->index"
                            />

                            <form id="revoke-session-{{ $loop->index }}" method="POST" action="{{ route('user.profile.sessions.revoke', $session->id) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endunless
                    @empty
                        <li class="pro__session">
                            <span class="pro__session-body">
                                <span class="pro__session-key">{{ __('No active sessions found.') }}</span>
                            </span>
                        </li>
                    @endforelse
                </ul>

                @if($sessions->where('is_current', false)->count() > 0)
                    <button type="button" class="btn btn-outline btn-sm mt-4 w-full" data-modal-trigger="confirmRevokeAllSessions">
                        <span class="btn__label">
                            <span>{{ __('Sign out everywhere') }}</span>
                            <span aria-hidden="true">{{ __('Sign out everywhere') }}</span>
                        </span>
                    </button>

                    <x-ui.confirm
                        id="confirmRevokeAllSessions"
                        :title="__('Sign out of every device?')"
                        :message="__('You will be signed out here too and will need to sign in again. Nothing in your workspace is deleted.')"
                        :confirmText="__('Sign out everywhere')"
                        formId="revoke-all-sessions"
                    />

                    <form id="revoke-all-sessions" method="POST" action="{{ route('user.profile.sessions.revoke-all') }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        </aside>
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

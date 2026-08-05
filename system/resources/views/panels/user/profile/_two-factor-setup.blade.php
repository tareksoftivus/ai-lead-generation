@if(setting('enable_2fa_for_users', true))
<div class="form-card mt-4">
    <h3 class="form-card__title">{{ __('Two-factor authentication') }}</h3>
    <p class="form-card__hint">
        {{ __('Add an extra layer of security to your account.') }}
    </p>

    @if($user->hasConfirmedTwoFactor())
        <div class="rounded-xl border border-success/30 bg-success/10 p-4 mb-4">
            <div class="flex items-center gap-2">
                <i class="ph ph-check-circle text-success text-lg"></i>
                <span class="text-sm font-medium text-success">{{ __('Two-factor authentication is enabled.') }}</span>
            </div>
        </div>

        @if(session('recovery_codes'))
            <div class="rounded-xl border border-warning/30 bg-warning/10 p-4 mb-4">
                <div class="flex items-center gap-2 mb-3">
                    <i class="ph ph-warning text-warning text-lg"></i>
                    <span class="text-sm font-medium text-warning">{{ __('Save these recovery codes in a secure location.') }}</span>
                </div>
                <p class="text-xs text-neutral-500 mb-3">{{ __('These codes can be used to access your account if you lose your authenticator device. Each code can only be used once.') }}</p>
                <div class="grid grid-cols-2 gap-2 rounded-lg bg-neutral-50 p-3 font-mono text-sm">
                    @foreach(session('recovery_codes') as $code)
                        <div class="text-neutral-700">{{ $code }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('user.two-factor.disable') }}" class="space-y-4">
            @csrf
            <x-forms.input :label="__('Confirm Password')" name="password" type="password" required :placeholder="__('Enter your password to disable 2FA')" />
            <x-ui.button type="submit" variant="danger">
                <i class="ph ph-shield-slash mr-1.5"></i>
                {{ __('Disable Two-Factor Authentication') }}
            </x-ui.button>
        </form>
    @else
        <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-4 mb-4">
            <div class="flex items-center gap-2">
                <i class="ph ph-shield-warning text-neutral-400 text-lg"></i>
                <span class="text-sm text-neutral-500">{{ __('Two-factor authentication is not enabled.') }}</span>
            </div>
        </div>

        <a href="{{ route('user.two-factor.setup') }}" class="btn btn-primary">
            <i class="ph ph-shield-plus mr-1.5"></i>
            {{ __('Enable Two-Factor Authentication') }}
        </a>
    @endif
</div>
@endif

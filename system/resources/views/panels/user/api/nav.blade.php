@php
    $apiActive = $apiActive ?? 'keys';
@endphp

<nav class="app-tablist mb-4" aria-label="{{ __('API and integrations') }}">
    <a
        href="{{ route('user.api.keys') }}"
        class="app-tab{{ $apiActive === 'keys' ? ' is-active' : '' }}"
        @if ($apiActive === 'keys') aria-current="page" @endif
    >
        <i class="ph ph-key"></i>{{ __('API keys') }}
    </a>
    <a
        href="{{ route('user.api.docs') }}"
        class="app-tab{{ $apiActive === 'docs' ? ' is-active' : '' }}"
        @if ($apiActive === 'docs') aria-current="page" @endif
    >
        <i class="ph ph-book-open"></i>{{ __('Documentation') }}
    </a>
    <a
        href="{{ route('user.api.integrations') }}"
        class="app-tab{{ $apiActive === 'integrations' ? ' is-active' : '' }}"
        @if ($apiActive === 'integrations') aria-current="page" @endif
    >
        <i class="ph ph-plugs-connected"></i>{{ __('Integrations') }}
    </a>
</nav>

<x-layouts.admin :title="$contact->subject ?: __('Contact Request')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ $contact->subject ?: __('Contact Request') }}</h1>
                <p class="mt-1 text-sm text-neutral-500">
                    {{ $contact->name }} &middot; {{ $contact->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ $contact->isRead() ? route('admin.contacts.mark-unread', $contact) : route('admin.contacts.mark-read', $contact) }}">
                    @csrf
                    <x-ui.button variant="outline" type="submit">
                        <i class="ph ph-{{ $contact->isRead() ? 'circle' : 'check-circle' }}"></i>
                        {{ $contact->isRead() ? __('Mark unread') : __('Mark read') }}
                    </x-ui.button>
                </form>
                <x-ui.button variant="outline" href="{{ route('admin.contacts.index') }}">
                    <i class="ph ph-arrow-left"></i> {{ __('Back') }}
                </x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="section-card lg:col-span-2">
                <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Message') }}</h2>
                <div class="whitespace-pre-line text-sm leading-6 text-neutral-800">{{ $contact->message }}</div>
            </div>

            <div class="space-y-6">
                <div class="section-card">
                    <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Requester') }}</h2>
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-neutral-400">{{ __('Name') }}</span>
                            <p class="font-medium text-neutral-950">{{ $contact->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-neutral-400">{{ __('Email') }}</span>
                            <p>
                                <a href="mailto:{{ $contact->email }}" class="font-medium text-primary hover:underline">{{ $contact->email }}</a>
                            </p>
                        </div>
                        @if ($contact->phone)
                            <div>
                                <span class="text-sm text-neutral-400">{{ __('Phone') }}</span>
                                <p>
                                    <a href="tel:{{ $contact->phone }}" class="font-medium text-primary hover:underline">{{ $contact->phone }}</a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="section-card">
                    <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Details') }}</h2>
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-neutral-400">{{ __('Status') }}</span>
                            <p class="font-medium text-neutral-950">{{ $contact->isRead() ? __('Read') : __('Unread') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-neutral-400">{{ __('Received') }}</span>
                            <p class="font-medium text-neutral-950">{{ format_date($contact->created_at, true) }}</p>
                        </div>
                        @if ($contact->read_at)
                            <div>
                                <span class="text-sm text-neutral-400">{{ __('Read At') }}</span>
                                <p class="font-medium text-neutral-950">{{ format_date($contact->read_at, true) }}</p>
                            </div>
                        @endif
                        <div>
                            <span class="text-sm text-neutral-400">{{ __('Terms Accepted') }}</span>
                            <p class="font-medium text-neutral-950">{{ $contact->terms_accepted ? __('Yes') : __('No') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

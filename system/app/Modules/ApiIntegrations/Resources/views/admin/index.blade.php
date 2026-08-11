<x-layouts.admin :title="__('API & integrations')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('API & integrations') }}</h1>
                <p class="s-body mt-1 text-neutral-500">
                    {{ __('Manage which third-party destinations users can connect from their dashboard.') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary transition-transform group-hover:scale-110">
                        <i class="ph ph-plugs-connected text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium tracking-wider text-neutral-400 uppercase">{{ __('Providers') }}</p>
                    <p class="mt-1 text-lg font-bold text-neutral-950 numeric">{{ $providers->count() }}</p>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 transition-transform group-hover:scale-110">
                        <i class="ph ph-check-circle text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium tracking-wider text-neutral-400 uppercase">{{ __('Active') }}</p>
                    <p class="mt-1 text-lg font-bold text-neutral-950 numeric">{{ $providers->where('is_active', true)->count() }}</p>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-neutral-100 text-neutral-500 transition-transform group-hover:scale-110">
                        <i class="ph ph-eye-slash text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium tracking-wider text-neutral-400 uppercase">{{ __('Hidden') }}</p>
                    <p class="mt-1 text-lg font-bold text-neutral-950 numeric">{{ $providers->where('is_active', false)->count() }}</p>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 transition-transform group-hover:scale-110">
                        <i class="ph ph-link text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium tracking-wider text-neutral-400 uppercase">{{ __('Connections') }}</p>
                    <p class="mt-1 text-lg font-bold text-neutral-950 numeric">{{ number_format($providers->sum('connections_count')) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-4 xl:col-span-2">
                @forelse ($providers as $provider)
                    <details class="section-card group/provider" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                            <i class="{{ $provider->icon ?: 'ph ph-plugs-connected' }} text-lg"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="truncate text-sm font-semibold text-neutral-900">{{ $provider->name }}</h4>
                                        @if ($provider->category)
                                            <span class="badge badge-neutral">{{ $provider->category }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-neutral-500">
                                        <span class="numeric">{{ $provider->connections_count }}</span> {{ __('connections') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                @if ($provider->is_active)
                                    <x-ui.badge variant="success">{{ __('Active') }}</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">{{ __('Hidden') }}</x-ui.badge>
                                @endif
                                <i class="ph ph-caret-down text-neutral-400 transition-transform duration-200 group-open/provider:rotate-180"></i>
                            </div>
                        </summary>

                        <form action="{{ route('admin.api-integrations.update', $provider) }}" method="post" class="mt-5 border-t border-neutral-100 pt-5">
                            @csrf
                            @method('put')

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <x-forms.input :label="__('Name')" name="name" :value="$provider->name" />
                                <x-forms.input :label="__('Category')" name="category" :value="$provider->category" />
                                <x-forms.input :label="__('Sort order')" name="sort_order" type="number" :value="$provider->sort_order" />
                                <x-forms.input :label="__('Docs URL')" name="docs_url" type="url" :value="$provider->docs_url" />
                            </div>

                            <div class="mt-4">
                                <x-forms.textarea :label="__('Description')" name="description" :value="$provider->description" :rows="3" />
                            </div>

                            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-forms.toggle
                                    :label="__('Available to users')"
                                    name="is_active"
                                    :checked="$provider->is_active"
                                />
                                <x-forms.toggle
                                    :label="__('Requires user configuration')"
                                    name="requires_configuration"
                                    :checked="$provider->requires_configuration"
                                />
                            </div>

                            <div class="mt-5 flex justify-end border-t border-neutral-100 pt-4">
                                <x-forms.submit :label="__('Save provider')" />
                            </div>
                        </form>
                    </details>
                @empty
                    <div class="section-card flex flex-col items-center justify-center gap-3 py-12 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 text-neutral-400">
                            <i class="ph ph-plugs-connected text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">{{ __('No providers yet') }}</p>
                            <p class="mt-1 text-xs text-neutral-500">{{ __('Integration providers will appear here once configured.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="space-y-6">
                <div class="section-card border-info/20 bg-info/5">
                    <div class="flex gap-3">
                        <i class="ph ph-info text-lg text-info"></i>
                        <p class="text-xs text-neutral-600">
                            {{ __('Disabling a provider hides it from new connections but does not delete existing user connection records.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

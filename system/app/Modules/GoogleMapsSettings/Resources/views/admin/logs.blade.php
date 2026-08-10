<x-layouts.admin :title="__('Google Maps API Logs')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Google Maps API Logs') }}</h1>
                <p class="s-body mt-1 text-neutral-500">
                    {{ __('Monitor server-side Google Places requests and responses without exposing the API key.') }}
                </p>
            </div>

            <x-ui.button variant="outline" href="{{ route('admin.google-maps-settings.index') }}">
                <i class="ph ph-gear"></i> {{ __('Settings') }}
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.kpi-card :title="__('Total requests')" :value="number_format($stats['total'])" icon="ph-list-magnifying-glass" color="primary" />
            <x-ui.kpi-card :title="__('Requests today')" :value="number_format($stats['today'])" icon="ph-calendar-check" color="info" />
            <x-ui.kpi-card :title="__('Success rate (today)')" :value="$stats['today'] > 0 ? $stats['success_rate'] . '%' : __('N/A')" icon="ph-check-circle" :color="$stats['success_rate'] >= 95 ? 'success' : 'warning'" />
            <x-ui.kpi-card :title="__('Avg. response time')" :value="$stats['avg_duration'] > 0 ? $stats['avg_duration'] . 'ms' : __('N/A')" icon="ph-timer" color="secondary" />
        </div>

        <div class="section-card">
            <form method="GET" action="{{ route('admin.google-maps-settings.logs') }}" class="mb-6">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-36 flex-1">
                        <label for="gml-status" class="form-label">{{ __('Status') }}</label>
                        <x-forms.select id="gml-status" name="status">
                            <option value="">{{ __('All') }}</option>
                            <option value="successful" @selected(request('status') === 'successful')>{{ __('Successful') }}</option>
                            <option value="failed" @selected(request('status') === 'failed')>{{ __('Failed') }}</option>
                        </x-forms.select>
                    </div>

                    <div class="min-w-36 flex-1">
                        <label for="gml-action" class="form-label">{{ __('Action') }}</label>
                        <x-forms.select id="gml-action" name="action">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn btn-primary py-3">
                            <i class="ph ph-funnel"></i> {{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.google-maps-settings.logs') }}" class="btn btn-ghost py-3">
                            <i class="ph ph-x"></i> {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="border-t border-neutral-100 pt-4 pb-2">
                <x-tables.table>
                    <thead>
                        <tr>
                            <th>{{ __('Action') }}</th>
                            <th>{{ __('Request') }}</th>
                            <th>{{ __('Response') }}</th>
                            <th class="text-right">{{ __('Time') }}</th>
                            <th>{{ __('Created') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td data-th="{{ __('Action') }}">
                                    <p class="text-sm font-medium text-neutral-900">{{ $log->action }}</p>
                                    <p class="mt-0.5 text-xs text-neutral-400">{{ $log->method }}</p>
                                </td>
                                <td data-th="{{ __('Request') }}">
                                    <p class="max-w-xs truncate font-mono text-xs text-neutral-600" title="{{ $log->url }}">{{ $log->url }}</p>
                                    @if ($log->request_payload)
                                        <details class="mt-2">
                                            <summary class="cursor-pointer text-xs font-medium text-primary">{{ __('View payload') }}</summary>
                                            <pre class="mt-2 max-w-xl overflow-x-auto rounded-lg bg-neutral-50 p-3 text-xs text-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">{{ json_encode($log->request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @endif
                                </td>
                                <td data-th="{{ __('Response') }}">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-ui.badge :variant="$log->successful ? 'success' : 'danger'">
                                            {{ $log->status_code ?: __('No response') }}
                                        </x-ui.badge>
                                        @if ($log->attempts > 1)
                                            <span class="text-xs text-neutral-400">{{ $log->attempts }}x {{ __('attempts') }}</span>
                                        @endif
                                    </div>
                                    @if ($log->error_message)
                                        <p class="mt-2 text-xs text-danger">{{ $log->error_message }}</p>
                                    @endif
                                    @if ($log->response_body)
                                        <details class="mt-2">
                                            <summary class="cursor-pointer text-xs font-medium text-primary">{{ __('View response') }}</summary>
                                            <pre class="mt-2 max-w-xl overflow-x-auto rounded-lg bg-neutral-50 p-3 text-xs text-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">{{ json_encode($log->response_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @endif
                                </td>
                                <td data-th="{{ __('Time') }}" class="text-right text-sm text-neutral-600">{{ $log->duration_ms }}ms</td>
                                <td data-th="{{ __('Created') }}" class="text-sm whitespace-nowrap text-neutral-400">
                                    {{ format_date($log->created_at, true) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-neutral-400">{{ __('No Google Maps API logs found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-tables.table>

                <x-tables.pagination :paginator="$logs" />
            </div>
        </div>
    </div>
</x-layouts.admin>

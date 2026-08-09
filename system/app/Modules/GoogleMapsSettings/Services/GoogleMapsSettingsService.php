<?php

namespace App\Modules\GoogleMapsSettings\Services;

use App\Modules\GoogleMapsSettings\Models\GoogleMapsSetting;
use Illuminate\Support\Facades\Cache;

class GoogleMapsSettingsService
{
    protected string $cacheKey = 'google_maps_settings_cache';

    protected int $cacheTtl = 86400;

    /**
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'google_maps_api_key' => null,
        'google_maps_enrichment_enabled' => true,
        'google_maps_daily_search_cap' => null,
    ];

    /**
     * @var array<string>
     */
    protected array $booleanKeys = ['google_maps_enrichment_enabled'];

    /**
     * @var array<string>
     */
    protected array $integerKeys = ['google_maps_daily_search_cap'];

    public function get(string $key, mixed $default = null): mixed
    {
        $values = $this->getAllFromDb();

        if (! array_key_exists($key, $values)) {
            return $default ?? ($this->defaults[$key] ?? null);
        }

        return $this->castValue($key, $values[$key]);
    }

    public function set(string $key, mixed $value): void
    {
        GoogleMapsSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $this->formatForStorage($key, $value)]
        );

        $this->clearCache();
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return collect(array_keys($this->defaults))
            ->mapWithKeys(fn (string $key) => [$key => $this->get($key)])
            ->all();
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * @return array<string, string|null>
     */
    protected function getAllFromDb(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return GoogleMapsSetting::query()->pluck('value', 'key')->all();
        });
    }

    protected function castValue(string $key, ?string $value): mixed
    {
        if ($value === null) {
            return $this->defaults[$key] ?? null;
        }

        if (in_array($key, $this->booleanKeys, true)) {
            return (bool) $value;
        }

        if (in_array($key, $this->integerKeys, true)) {
            return $value === '' ? null : (int) $value;
        }

        return $value;
    }

    protected function formatForStorage(string $key, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (in_array($key, $this->booleanKeys, true)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }
}

<?php

namespace App\Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $fillable = [
        'google_place_id',
        'name',
        'formatted_address',
        'lat',
        'lng',
        'phone',
        'website',
        'google_category',
        'rating',
        'review_count',
        'raw_response',
        'details_fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'rating' => 'float',
            'review_count' => 'integer',
            'raw_response' => 'array',
            'details_fetched_at' => 'datetime',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function hasWebsite(): bool
    {
        return ! empty($this->website);
    }

    public function hasPhone(): bool
    {
        return ! empty($this->phone);
    }

    /**
     * Find the cached Place for a Google Places result, or create it from the
     * mapped attribute array. Existing rows are refreshed with the latest data
     * so the global cache stays current across repeated searches.
     *
     * @param  array<string, mixed>  $attributes  Output of PlacesResultMapper.
     */
    public static function findOrCreateFromPlacesResult(array $attributes): self
    {
        $place = static::query()->firstOrNew(['google_place_id' => $attributes['google_place_id']]);
        $place->fill($attributes);
        $place->save();

        return $place;
    }
}

<?php

namespace App\Modules\ApiIntegrations\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiIntegrationProvider extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'logo_class',
        'category',
        'is_active',
        'requires_configuration',
        'config_schema',
        'docs_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'requires_configuration' => 'boolean',
            'config_schema' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function connections(): HasMany
    {
        return $this->hasMany(UserIntegrationConnection::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

<?php

namespace App\Modules\Outreach\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadExport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'filename',
        'format',
        'source_type',
        'source_id',
        'source_label',
        'columns',
        'selected_lead_ids',
        'require_email',
        'rows_count',
        'columns_count',
        'downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'columns' => 'array',
            'selected_lead_ids' => 'array',
            'require_email' => 'boolean',
            'rows_count' => 'integer',
            'columns_count' => 'integer',
            'downloaded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}

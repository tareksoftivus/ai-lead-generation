<?php

namespace App\Modules\Crm\Models;

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadContact extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'name',
        'role',
        'email',
        'phone',
        'note',
        'is_primary',
        'last_contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'last_contacted_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
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

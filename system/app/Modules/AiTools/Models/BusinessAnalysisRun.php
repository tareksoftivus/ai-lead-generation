<?php

namespace App\Modules\AiTools\Models;

use App\Models\User;
use App\Modules\Leads\Models\LeadList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessAnalysisRun extends Model
{
    public const FOCUS_GAPS = 'gaps';

    public const FOCUS_FIT = 'fit';

    public const FOCUS_SUMMARY = 'summary';

    public const STATUS_DONE = 'done';

    protected $fillable = [
        'user_id',
        'lead_list_id',
        'focus',
        'skip_analysed',
        'status',
        'businesses_count',
        'credits_spent',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'skip_analysed' => 'boolean',
            'businesses_count' => 'integer',
            'credits_spent' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leadList(): BelongsTo
    {
        return $this->belongsTo(LeadList::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BusinessAnalysisItem::class, 'business_analysis_run_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @return array<int, string>
     */
    public static function focuses(): array
    {
        return [
            self::FOCUS_GAPS,
            self::FOCUS_FIT,
            self::FOCUS_SUMMARY,
        ];
    }
}

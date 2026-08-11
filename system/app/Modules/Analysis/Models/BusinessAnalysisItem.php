<?php

namespace App\Modules\Analysis\Models;

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAnalysisItem extends Model
{
    public const FIT_YES = 'yes';

    public const FIT_MAYBE = 'maybe';

    protected $fillable = [
        'user_id',
        'lead_id',
        'business_analysis_run_id',
        'score',
        'read',
        'gap',
        'fit',
        'fit_status',
        'signals',
        'analysed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'signals' => 'array',
            'analysed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(BusinessAnalysisRun::class, 'business_analysis_run_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scoreBucket(): string
    {
        return match (true) {
            $this->score >= 80 => 'hi',
            $this->score >= 50 => 'mid',
            default => 'lo',
        };
    }
}

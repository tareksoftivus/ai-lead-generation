<?php

namespace App\Modules\Outreach\Models;

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadCampaign extends Model
{
    use SoftDeletes;

    public const STATUS_REVIEW = 'review';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_DONE = 'done';

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'source_type',
        'source_id',
        'daily_limit',
        'recipients_count',
        'sent_count',
        'opened_count',
        'replied_count',
        'approved_at',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'daily_limit' => 'integer',
            'recipients_count' => 'integer',
            'sent_count' => 'integer',
            'opened_count' => 'integer',
            'replied_count' => 'integer',
            'approved_at' => 'datetime',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'lead_campaign_recipients')
            ->withPivot(['status', 'sent_at', 'opened_at', 'replied_at'])
            ->withTimestamps();
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @return array<string, array{label: string, variant: string}>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_REVIEW => ['label' => 'Needs your review', 'variant' => 'review'],
            self::STATUS_ACTIVE => ['label' => 'Sending', 'variant' => 'running'],
            self::STATUS_PAUSED => ['label' => 'Paused', 'variant' => 'neutral'],
            self::STATUS_DONE => ['label' => 'Finished', 'variant' => 'done'],
        ];
    }

    public function statusLabel(): string
    {
        return static::statuses()[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function statusVariant(): string
    {
        return static::statuses()[$this->status]['variant'] ?? 'neutral';
    }
}

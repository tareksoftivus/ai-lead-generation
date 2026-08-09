<?php

namespace App\Modules\Leads\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_REPLIED = 'replied';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_LOST = 'lost';

    protected $fillable = [
        'user_id',
        'place_id',
        'search_run_id',
        'status',
        'email',
        'enriched_at',
        'enrichment_credit_spent',
        'score',
        'score_signals',
    ];

    protected function casts(): array
    {
        return [
            'enriched_at' => 'datetime',
            'enrichment_credit_spent' => 'boolean',
            'score' => 'integer',
            'score_signals' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function searchRun(): BelongsTo
    {
        return $this->belongsTo(SearchRun::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'lead_tag');
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(LeadList::class, 'lead_list_lead');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHasContact(Builder $query): Builder
    {
        return $query->whereNotNull('email');
    }

    public function scopeScoreBucket(Builder $query, string $bucket): Builder
    {
        return match ($bucket) {
            'hi' => $query->where('score', '>=', 80),
            'mid' => $query->whereBetween('score', [50, 79]),
            'lo' => $query->where('score', '<', 50),
            default => $query,
        };
    }

    /**
     * Bucket a numeric score into the hi/mid/lo classification used across
     * the leads UI (badges, filters, map pin colors). Single source of truth
     * so scopeScoreBucket() and Blade rendering never drift apart.
     */
    public static function scoreBucket(?int $score): string
    {
        if ($score === null) {
            return 'lo';
        }

        return match (true) {
            $score >= 80 => 'hi',
            $score >= 50 => 'mid',
            default => 'lo',
        };
    }

    /**
     * @return array<string, array{label: string, variant: string}>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => ['label' => 'New', 'variant' => 'new'],
            self::STATUS_CONTACTED => ['label' => 'Contacted', 'variant' => 'contacted'],
            self::STATUS_REPLIED => ['label' => 'Replied', 'variant' => 'replied'],
            self::STATUS_QUALIFIED => ['label' => 'Qualified', 'variant' => 'qualified'],
            self::STATUS_LOST => ['label' => 'Lost', 'variant' => 'lost'],
        ];
    }

    public function hasContact(): bool
    {
        return ! empty($this->email);
    }
}

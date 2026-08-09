<?php

namespace App\Modules\Leads\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchRun extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_RUNNING = 'running';

    public const STATUS_DONE = 'done';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'search_id',
        'filters',
        'status',
        'results_count',
        'credits_spent',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'results_count' => 'integer',
            'credits_spent' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function search(): BelongsTo
    {
        return $this->belongsTo(Search::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'search_run_results');
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
            self::STATUS_PENDING => ['label' => 'Pending', 'variant' => 'neutral'],
            self::STATUS_RUNNING => ['label' => 'Running', 'variant' => 'running'],
            self::STATUS_DONE => ['label' => 'Finished', 'variant' => 'done'],
            self::STATUS_FAILED => ['label' => 'Failed', 'variant' => 'error'],
        ];
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_DONE, self::STATUS_FAILED], true);
    }
}

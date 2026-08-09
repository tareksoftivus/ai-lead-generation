<?php

namespace App\Modules\Leads\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LeadList extends Model
{
    protected $table = 'lead_lists';

    public const SOURCE_SEARCH = 'search';

    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'user_id',
        'name',
        'source',
        'note',
        'search_run_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function searchRun(): BelongsTo
    {
        return $this->belongsTo(SearchRun::class);
    }

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'lead_list_lead');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}

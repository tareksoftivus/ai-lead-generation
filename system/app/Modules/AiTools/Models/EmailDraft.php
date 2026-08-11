<?php

namespace App\Modules\AiTools\Models;

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Outreach\Models\LeadCampaign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailDraft extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_QUEUED = 'queued';

    protected $table = 'email_generator_drafts';

    protected $fillable = [
        'user_id',
        'lead_id',
        'lead_list_id',
        'lead_campaign_id',
        'scope_type',
        'tone',
        'length',
        'opening',
        'subject',
        'body',
        'gap',
        'status',
        'metadata',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'lead_id' => 'integer',
            'lead_list_id' => 'integer',
            'lead_campaign_id' => 'integer',
            'metadata' => 'array',
            'generated_at' => 'datetime',
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

    public function leadList(): BelongsTo
    {
        return $this->belongsTo(LeadList::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(LeadCampaign::class, 'lead_campaign_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}

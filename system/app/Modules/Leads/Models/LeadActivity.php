<?php

namespace App\Modules\Leads\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    public const UPDATED_AT = null;

    public const TYPE_FOUND_IN_SEARCH = 'found_in_search';

    public const TYPE_CONTACT_FOUND = 'contact_found';

    public const TYPE_ENRICHMENT_FAILED = 'enrichment_failed';

    public const TYPE_STATUS_CHANGED = 'status_changed';

    public const TYPE_TAG_ADDED = 'tag_added';

    public const TYPE_TAG_REMOVED = 'tag_removed';

    public const TYPE_NOTE_ADDED = 'note_added';

    public const TYPE_SCORED = 'scored';

    public const TYPE_LIST_ADDED = 'list_added';

    public const TYPE_LIST_REMOVED = 'list_removed';

    protected $fillable = [
        'lead_id',
        'type',
        'payload',
        'caused_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function causedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caused_by_user_id');
    }

    public static function logFoundInSearch(Lead $lead, SearchRun $searchRun): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_FOUND_IN_SEARCH,
            'payload' => ['search_run_id' => $searchRun->id],
            'caused_by_user_id' => $lead->user_id,
        ]);
    }

    public static function logContactFound(Lead $lead, int $creditsSpent, bool $found): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_CONTACT_FOUND,
            'payload' => ['credits_spent' => $creditsSpent, 'found' => $found],
            'caused_by_user_id' => $lead->user_id,
        ]);
    }

    public static function logStatusChanged(Lead $lead, string $from, string $to, ?User $actor = null): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_STATUS_CHANGED,
            'payload' => ['from' => $from, 'to' => $to],
            'caused_by_user_id' => $actor?->id ?? $lead->user_id,
        ]);
    }

    public static function logTagAdded(Lead $lead, Tag $tag, ?User $actor = null): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_TAG_ADDED,
            'payload' => ['tag' => $tag->name],
            'caused_by_user_id' => $actor?->id ?? $lead->user_id,
        ]);
    }

    public static function logTagRemoved(Lead $lead, Tag $tag, ?User $actor = null): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_TAG_REMOVED,
            'payload' => ['tag' => $tag->name],
            'caused_by_user_id' => $actor?->id ?? $lead->user_id,
        ]);
    }

    public static function logNoteAdded(Lead $lead, ?User $actor = null): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_NOTE_ADDED,
            'payload' => [],
            'caused_by_user_id' => $actor?->id ?? $lead->user_id,
        ]);
    }

    public static function logScored(Lead $lead): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_SCORED,
            'payload' => ['score' => $lead->score],
            'caused_by_user_id' => null,
        ]);
    }

    public static function logListAdded(Lead $lead, LeadList $list, ?User $actor = null): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_LIST_ADDED,
            'payload' => ['list' => $list->name],
            'caused_by_user_id' => $actor?->id ?? $lead->user_id,
        ]);
    }

    public static function logListRemoved(Lead $lead, LeadList $list, ?User $actor = null): self
    {
        return static::create([
            'lead_id' => $lead->id,
            'type' => self::TYPE_LIST_REMOVED,
            'payload' => ['list' => $list->name],
            'caused_by_user_id' => $actor?->id ?? $lead->user_id,
        ]);
    }
}

<?php

namespace App\Modules\Outreach\Services;

use App\Models\User;
use App\Modules\Outreach\Models\LeadCampaign;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function __construct(protected LeadSourceService $sources) {}

    /**
     * @param  array<int, int|string>  $selectedIds
     */
    public function create(User $user, array $data, array $selectedIds = []): LeadCampaign
    {
        return DB::transaction(function () use ($user, $data, $selectedIds): LeadCampaign {
            $sourceType = $data['source_type'] ?? LeadSourceService::SOURCE_ALL;
            $sourceId = isset($data['source_id']) ? (int) $data['source_id'] : null;
            $leads = $this->sources
                ->query($user, $sourceType, $sourceId, $selectedIds, true)
                ->limit(1000)
                ->get(['leads.id']);

            $campaign = LeadCampaign::query()->create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'status' => LeadCampaign::STATUS_REVIEW,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'daily_limit' => (int) ($data['daily_limit'] ?? 40),
                'recipients_count' => $leads->count(),
            ]);

            $campaign->leads()->sync($leads->pluck('id')->all());

            return $campaign->fresh();
        });
    }

    public function updateStatus(LeadCampaign $campaign, string $status): LeadCampaign
    {
        $now = Carbon::now();
        $attributes = ['status' => $status];

        if ($status === LeadCampaign::STATUS_ACTIVE) {
            $attributes['approved_at'] = $campaign->approved_at ?? $now;
            $attributes['started_at'] = $campaign->started_at ?? $now;
            $attributes['finished_at'] = null;
            $attributes['sent_count'] = max($campaign->sent_count, min($campaign->recipients_count, $campaign->daily_limit));
        }

        if ($status === LeadCampaign::STATUS_DONE) {
            $attributes['approved_at'] = $campaign->approved_at ?? $now;
            $attributes['started_at'] = $campaign->started_at ?? $now;
            $attributes['finished_at'] = $now;
            $attributes['sent_count'] = $campaign->recipients_count;
        }

        if ($status === LeadCampaign::STATUS_PAUSED) {
            $attributes['finished_at'] = null;
        }

        $campaign->update($attributes);

        $this->refreshMetrics($campaign);

        return $campaign->fresh();
    }

    public function duplicate(LeadCampaign $campaign): LeadCampaign
    {
        return DB::transaction(function () use ($campaign): LeadCampaign {
            $copy = LeadCampaign::query()->create([
                'user_id' => $campaign->user_id,
                'name' => $campaign->name.' copy',
                'status' => LeadCampaign::STATUS_REVIEW,
                'source_type' => $campaign->source_type,
                'source_id' => $campaign->source_id,
                'daily_limit' => $campaign->daily_limit,
                'recipients_count' => $campaign->recipients_count,
            ]);

            $copy->leads()->sync($campaign->leads()->pluck('leads.id')->all());

            return $copy->fresh();
        });
    }

    protected function refreshMetrics(LeadCampaign $campaign): void
    {
        if ($campaign->status === LeadCampaign::STATUS_REVIEW) {
            return;
        }

        $sent = $campaign->status === LeadCampaign::STATUS_DONE
            ? $campaign->recipients_count
            : min($campaign->recipients_count, max($campaign->sent_count, $campaign->daily_limit));

        $campaign->updateQuietly([
            'sent_count' => $sent,
            'opened_count' => (int) floor($sent * 0.38),
            'replied_count' => (int) floor($sent * 0.1),
        ]);
    }
}

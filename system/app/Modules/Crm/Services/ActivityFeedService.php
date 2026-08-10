<?php

namespace App\Modules\Crm\Services;

use App\Modules\Leads\Models\LeadActivity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ActivityFeedService
{
    /**
     * @param  Collection<int, LeadActivity>  $activities
     * @return Collection<string, Collection<int, array<string, mixed>>>
     */
    public function grouped(Collection $activities): Collection
    {
        return $activities
            ->map(fn (LeadActivity $activity): array => $this->present($activity))
            ->groupBy('day_label');
    }

    /**
     * @return array<string, mixed>
     */
    public function present(LeadActivity $activity): array
    {
        $payload = $activity->payload ?? [];
        $lead = $activity->lead;
        $actor = $activity->causedBy;
        $isAuto = $actor === null;
        $kind = $this->kind($activity, $payload);

        return [
            'id' => $activity->id,
            'title' => $this->title($activity, $payload),
            'kind' => $kind,
            'list_key' => $isAuto ? 'auto' : 'mine',
            'icon' => $this->icon($kind, $activity->type),
            'is_auto' => $isAuto,
            'actor' => $isAuto ? __('LeadAtlas') : ($actor?->name ?? __('You')),
            'lead' => $lead,
            'lead_name' => $lead?->place?->name ?? __('Lead'),
            'created_at' => $activity->created_at,
            'day_label' => $this->dayLabel($activity->created_at),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function kind(LeadActivity $activity, array $payload): string
    {
        if ($activity->type === LeadActivity::TYPE_NOTE_ADDED) {
            return ($payload['kind'] ?? 'note') === 'call' ? 'call' : 'note';
        }

        return match ($activity->type) {
            LeadActivity::TYPE_STATUS_CHANGED => 'stage',
            LeadActivity::TYPE_CONTACT_FOUND => 'email',
            LeadActivity::TYPE_SCORED, LeadActivity::TYPE_FOUND_IN_SEARCH => 'ai',
            default => 'stage',
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function title(LeadActivity $activity, array $payload): string
    {
        return match ($activity->type) {
            LeadActivity::TYPE_FOUND_IN_SEARCH => __('Saved from search results'),
            LeadActivity::TYPE_CONTACT_FOUND => ! empty($payload['found'])
                ? __('Contact details found')
                : __('No email found during enrichment'),
            LeadActivity::TYPE_STATUS_CHANGED => __('Moved from :from to :to', [
                'from' => ucfirst((string) ($payload['from'] ?? __('previous'))),
                'to' => ucfirst((string) ($payload['to'] ?? __('next'))),
            ]),
            LeadActivity::TYPE_TAG_ADDED => __('Tagged ":tag"', ['tag' => (string) ($payload['tag'] ?? '')]),
            LeadActivity::TYPE_TAG_REMOVED => __('Removed tag ":tag"', ['tag' => (string) ($payload['tag'] ?? '')]),
            LeadActivity::TYPE_NOTE_ADDED => (string) ($payload['body'] ?? __('Added a note')),
            LeadActivity::TYPE_SCORED => __('Scored :score', ['score' => (string) ($payload['score'] ?? $activity->lead?->score ?? '')]),
            LeadActivity::TYPE_LIST_ADDED => __('Added to list ":list"', ['list' => (string) ($payload['list'] ?? '')]),
            LeadActivity::TYPE_LIST_REMOVED => __('Removed from list ":list"', ['list' => (string) ($payload['list'] ?? '')]),
            LeadActivity::TYPE_PIPELINE_REMOVED => __('Removed from the pipeline'),
            default => __(str_replace('_', ' ', $activity->type)),
        };
    }

    protected function icon(string $kind, string $type): string
    {
        if ($type === LeadActivity::TYPE_FOUND_IN_SEARCH) {
            return 'ph-list-magnifying-glass';
        }

        return match ($kind) {
            'call' => 'ph-phone',
            'email' => 'ph-envelope-simple',
            'stage' => 'ph-kanban',
            'ai' => 'ph-sparkle',
            default => 'ph-note',
        };
    }

    protected function dayLabel(?Carbon $date): string
    {
        if (! $date) {
            return __('Earlier');
        }

        if ($date->isToday()) {
            return __('Today');
        }

        if ($date->isYesterday()) {
            return __('Yesterday');
        }

        return $date->format('j F');
    }
}

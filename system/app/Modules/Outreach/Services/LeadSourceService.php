<?php

namespace App\Modules\Outreach\Services;

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\SearchRun;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LeadSourceService
{
    public const SOURCE_SELECTION = 'selection';

    public const SOURCE_ALL = 'all';

    public const SOURCE_LIST = 'list';

    public const SOURCE_SEARCH = 'search';

    /**
     * @param  array<int, int|string>  $selectedIds
     */
    public function query(User $user, string $sourceType, ?int $sourceId = null, array $selectedIds = [], bool $requireEmail = false): Builder
    {
        $query = Lead::query()
            ->forUser($user->id)
            ->with(['place', 'tags', 'lists']);

        if ($sourceType === self::SOURCE_SELECTION) {
            if ($selectedIds === []) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereKey(array_map('intval', $selectedIds));
            }
        } elseif ($sourceType === self::SOURCE_LIST && $sourceId) {
            $query->whereHas('lists', fn (Builder $listQuery) => $listQuery->where('lead_lists.id', $sourceId));
        } elseif ($sourceType === self::SOURCE_SEARCH && $sourceId) {
            $query->where('search_run_id', $sourceId);
        }

        if ($requireEmail) {
            $query->whereNotNull('email');
        }

        return $query->latest();
    }

    /**
     * @param  array<int, int|string>  $selectedIds
     */
    public function count(User $user, string $sourceType, ?int $sourceId = null, array $selectedIds = [], bool $requireEmail = false): int
    {
        return (clone $this->query($user, $sourceType, $sourceId, $selectedIds, $requireEmail))->count();
    }

    /**
     * @return Collection<int, LeadList|SearchRun>
     */
    public function options(User $user): Collection
    {
        return LeadList::query()
            ->forUser($user->id)
            ->withCount('leads')
            ->orderBy('name')
            ->get()
            ->concat(
                SearchRun::query()
                    ->forUser($user->id)
                    ->where('results_count', '>', 0)
                    ->latest()
                    ->limit(20)
                    ->get()
            );
    }

    public function label(User $user, string $sourceType, ?int $sourceId = null, int $selectionCount = 0): string
    {
        return match ($sourceType) {
            self::SOURCE_SELECTION => $selectionCount > 0
                ? __('Selected leads')
                : __('Every lead in the account'),
            self::SOURCE_LIST => $this->listLabel($user, $sourceId),
            self::SOURCE_SEARCH => $this->searchLabel($user, $sourceId),
            default => __('Every lead in the account'),
        };
    }

    protected function listLabel(User $user, ?int $sourceId): string
    {
        $list = $sourceId
            ? LeadList::query()->forUser($user->id)->find($sourceId)
            : null;

        return $list?->name ?? __('A list');
    }

    protected function searchLabel(User $user, ?int $sourceId): string
    {
        $run = $sourceId
            ? SearchRun::query()->forUser($user->id)->with('search')->find($sourceId)
            : null;

        if (! $run) {
            return __('One search');
        }

        $prompt = $run->search?->prompt;

        return $prompt
            ? __('Search · :prompt', ['prompt' => $prompt])
            : __('Search #:id', ['id' => $run->id]);
    }
}

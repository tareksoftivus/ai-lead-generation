<?php

namespace App\Modules\Leads\Services;

use App\Models\User;
use App\Modules\Crm\Services\LeadContactService;
use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\SearchRun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadService
{
    public function __construct(
        protected LeadEnrichmentService $enrichmentService,
        protected LeadScorer $scorer
    ) {}

    /**
     * Save a batch of generated search results as leads. Generation already
     * spent credits, so this action persists/list-attaches without charging.
     *
     * @param  array<int, int>  $placeIds
     * @return array{saved: Collection<int, Lead>, already_saved: int, insufficient_credits: bool, list: LeadList|null}
     */
    public function saveFromSearch(User $user, ?SearchRun $searchRun, array $placeIds): array
    {
        $saved = collect();
        $alreadySaved = 0;
        $insufficientCredits = false;
        $list = null;
        $placeIds = collect($placeIds)
            ->map(fn ($placeId) => (int) $placeId)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $list = $searchRun ? $this->findOrCreateSearchList($user, $searchRun) : null;

        $places = Place::query()->whereIn('id', $placeIds)->get()->keyBy('id');

        foreach ($placeIds as $placeId) {
            $place = $places->get($placeId);

            if (! $place) {
                continue;
            }

            [$lead, $isNew] = $this->findOrRestoreForPlace($user, $place, $searchRun);

            if (! $isNew) {
                $alreadySaved++;
                $this->attachToList($lead, $list, $user);

                continue;
            }

            $lead = $this->enrichAndSave($user, $lead, $place, $searchRun);
            $saved->push($lead);
            $this->attachToList($lead, $list, $user);
        }

        return [
            'saved' => $saved,
            'already_saved' => $alreadySaved,
            'insufficient_credits' => $insufficientCredits,
            'list' => $list,
        ];
    }

    /**
     * @param  array<int, int>  $placeIds
     */
    public function canSaveFromSearch(User $user, array $placeIds): bool
    {
        // Saving generated leads is free; credits were checked/spent when the
        // search generated results. Kept for callers/tests that still ask.
        return true;
    }

    /**
     * Saving generated leads no longer spends credits. Kept as a stable API
     * for older call sites while the credit gate lives in SearchService.
     *
     * @param  array<int, int>  $placeIds
     */
    public function requiredCreditsForSave(User $user, array $placeIds): int
    {
        return 0;
    }

    /**
     * Find a live lead for (user, place), restore a soft-deleted one, or
     * build (but not yet persist beyond a bare create) a new one. Returns
     * [Lead, isNewOrRestored:bool].
     *
     * @return array{0: Lead, 1: bool}
     */
    public function findOrRestoreForPlace(User $user, Place $place, ?SearchRun $searchRun = null): array
    {
        $existing = Lead::withTrashed()
            ->where('user_id', $user->id)
            ->where('place_id', $place->id)
            ->first();

        if ($existing && ! $existing->trashed()) {
            return [$existing, false];
        }

        if ($existing && $existing->trashed()) {
            $existing->restore();
            $existing->update([
                'search_run_id' => $searchRun?->id ?? $existing->search_run_id,
                'is_in_pipeline' => true,
                'pipeline_entered_at' => $existing->pipeline_entered_at ?? now(),
            ]);

            return [$existing, true];
        }

        $lead = Lead::query()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
            'search_run_id' => $searchRun?->id,
            'status' => Lead::STATUS_NEW,
            'is_in_pipeline' => true,
            'pipeline_entered_at' => now(),
        ]);

        return [$lead, true];
    }

    /**
     * Enrich the place and persist the lead's contact + score. Credits are
     * charged during generation, not during this save/list action.
     */
    protected function enrichAndSave(User $user, Lead $lead, Place $place, ?SearchRun $searchRun): Lead
    {
        return DB::transaction(function () use ($user, $lead, $place, $searchRun) {
            $enrichment = $this->enrichmentService->enrich($place);
            $lead->refresh(); // enrich() may have updated $place; keep $lead's place relation fresh too.
            $place->refresh();

            $scoreResult = $this->scorer->score($lead->setRelation('place', $place));

            $lead->update([
                'email' => $enrichment['email'],
                'is_in_pipeline' => true,
                'pipeline_entered_at' => $lead->pipeline_entered_at ?? now(),
                'enriched_at' => now(),
                'enrichment_credit_spent' => false,
                'score' => $scoreResult['score'],
                'score_signals' => $scoreResult['signals'],
            ]);

            app(LeadContactService::class)->syncPrimaryFromLead($lead->setRelation('place', $place));

            if ($searchRun) {
                LeadActivity::logFoundInSearch($lead, $searchRun);
            }

            LeadActivity::logContactFound($lead, 0, found: (bool) $enrichment['email']);
            LeadActivity::logScored($lead);

            return $lead;
        });
    }

    protected function findOrCreateSearchList(User $user, SearchRun $searchRun): LeadList
    {
        return LeadList::query()->firstOrCreate([
            'user_id' => $user->id,
            'search_run_id' => $searchRun->id,
        ], [
            'name' => $this->searchListName($searchRun),
            'source' => LeadList::SOURCE_SEARCH,
            'note' => __('Generated from search results.'),
        ]);
    }

    protected function attachToList(Lead $lead, ?LeadList $list, User $user): void
    {
        if (! $list) {
            return;
        }

        if ($lead->lists()->whereKey($list->id)->exists()) {
            return;
        }

        $lead->lists()->attach($list);
        $list->touch();
        LeadActivity::logListAdded($lead, $list, $user);
    }

    protected function searchListName(SearchRun $searchRun): string
    {
        $filters = $searchRun->filters ?? [];
        $prompt = trim((string) ($filters['prompt'] ?? ''));

        if ($prompt !== '') {
            return Str::limit($prompt, 80);
        }

        $keyword = collect((array) ($filters['keyword'] ?? []))->filter()->first();
        $location = collect((array) ($filters['location'] ?? []))->filter()->first();
        $parts = array_filter([$keyword, $location]);

        return $parts ? Str::limit(implode(' · ', $parts), 80) : __('Search #:id', ['id' => $searchRun->id]);
    }
}

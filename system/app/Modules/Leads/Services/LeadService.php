<?php

namespace App\Modules\Leads\Services;

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\SearchRun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadService
{
    protected const ENRICHMENT_CREDIT_COST = 1;

    public function __construct(
        protected CreditLedger $ledger,
        protected LeadEnrichmentService $enrichmentService,
        protected LeadScorer $scorer
    ) {}

    /**
     * Save a batch of search results as leads, enriching (and charging) each
     * genuinely new one. Charges on enrichment *attempt*, not success — see
     * the module's design notes for why. Insufficient balance skips that
     * one place rather than failing the whole batch.
     *
     * @param  array<int, int>  $placeIds
     * @return array{saved: Collection<int, Lead>, already_saved: int, insufficient_credits: bool}
     */
    public function saveFromSearch(User $user, ?SearchRun $searchRun, array $placeIds): array
    {
        $saved = collect();
        $alreadySaved = 0;
        $insufficientCredits = false;

        $places = Place::query()->whereIn('id', $placeIds)->get()->keyBy('id');

        foreach ($placeIds as $placeId) {
            $place = $places->get($placeId);

            if (! $place) {
                continue;
            }

            [$lead, $isNew] = $this->findOrRestoreForPlace($user, $place, $searchRun);

            if (! $isNew) {
                $alreadySaved++;

                continue;
            }

            try {
                $lead = $this->enrichAndSave($user, $lead, $place, $searchRun);
                $saved->push($lead);
            } catch (InsufficientCreditsException) {
                $lead->delete();
                $lead->forceDelete();
                $insufficientCredits = true;
            }
        }

        return [
            'saved' => $saved,
            'already_saved' => $alreadySaved,
            'insufficient_credits' => $insufficientCredits,
        ];
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
            $existing->update(['search_run_id' => $searchRun?->id ?? $existing->search_run_id]);

            return [$existing, true];
        }

        $lead = Lead::query()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
            'search_run_id' => $searchRun?->id,
            'status' => Lead::STATUS_NEW,
        ]);

        return [$lead, true];
    }

    /**
     * Charge one credit, enrich the place, and persist the lead's contact +
     * score in a single transaction. Throws InsufficientCreditsException
     * (uncaught) if the user can't afford it — the caller decides what to
     * do with that place.
     */
    protected function enrichAndSave(User $user, Lead $lead, Place $place, ?SearchRun $searchRun): Lead
    {
        return DB::transaction(function () use ($user, $lead, $place, $searchRun) {
            $this->ledger->spend(
                $user,
                self::ENRICHMENT_CREDIT_COST,
                reason: 'enrichment',
                reference: $lead
            );

            $enrichment = $this->enrichmentService->enrich($place);
            $lead->refresh(); // enrich() may have updated $place; keep $lead's place relation fresh too.
            $place->refresh();

            $scoreResult = $this->scorer->score($lead->setRelation('place', $place));

            $lead->update([
                'email' => $enrichment['email'],
                'enriched_at' => now(),
                'enrichment_credit_spent' => true,
                'score' => $scoreResult['score'],
                'score_signals' => $scoreResult['signals'],
            ]);

            if ($searchRun) {
                LeadActivity::logFoundInSearch($lead, $searchRun);
                $searchRun->increment('credits_spent', self::ENRICHMENT_CREDIT_COST);
            }

            LeadActivity::logContactFound($lead, self::ENRICHMENT_CREDIT_COST, found: (bool) $enrichment['email']);
            LeadActivity::logScored($lead);

            return $lead;
        });
    }
}

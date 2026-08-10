<?php

namespace App\Modules\Leads\Services;

use App\Models\User;
use App\Modules\Credits\Services\CreditLedger;

/**
 * A free, instant, purely-local projection of the lead target and enrichment
 * cost. It deliberately never calls the Places API; filters without an
 * explicit requested count default to five leads.
 */
class SearchEstimateService
{
    protected const DEFAULT_RESULTS_COUNT = 5;

    protected const MAX_RESULTS_COUNT = 30;

    public function __construct(
        protected CreditLedger $ledger
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array{count: int, cost: int, credits_left: int}
     */
    public function estimate(User $user, array $filters): array
    {
        if (! empty($filters['requested_count'])) {
            $count = min(self::MAX_RESULTS_COUNT, max(1, (int) $filters['requested_count']));

            return [
                'count' => $count,
                'cost' => $count,
                'credits_left' => $this->ledger->balance($user) - $count,
            ];
        }

        $hasKeyword = count(array_filter((array) ($filters['keyword'] ?? []))) > 0;
        $hasLocation = count(array_filter((array) ($filters['location'] ?? []))) > 0;
        $count = $hasKeyword && $hasLocation ? self::DEFAULT_RESULTS_COUNT : 0;

        // Cost mirrors the UI's worst-case framing: "if every result gets
        // enriched, this is what it costs" — not a claim about this exact run.
        $cost = $count;

        return [
            'count' => $count,
            'cost' => $cost,
            'credits_left' => $this->ledger->balance($user) - $cost,
        ];
    }
}

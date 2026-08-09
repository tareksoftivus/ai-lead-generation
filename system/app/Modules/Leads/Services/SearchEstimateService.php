<?php

namespace App\Modules\Leads\Services;

use App\Models\User;
use App\Modules\Credits\Services\CreditLedger;

/**
 * A free, instant, purely-local projection of "if you ran this search and
 * enriched everything it found, roughly what would it cost" — deliberately
 * never calls the Places API (that would burn Google quota on every filter
 * keystroke and contradict "searching is free"). Constants below are a
 * heuristic v1 and can be tuned freely without touching the real search flow.
 */
class SearchEstimateService
{
    protected const BASE_RESULTS_PER_COMBINATION = 20;

    protected const MAX_RESULTS_PER_COMBINATION = 60;

    public function __construct(
        protected CreditLedger $ledger
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array{count: int, cost: int, credits_left: int}
     */
    public function estimate(User $user, array $filters): array
    {
        $keywordCount = max(1, count(array_filter((array) ($filters['keyword'] ?? []))));
        $locationCount = max(1, count(array_filter((array) ($filters['location'] ?? []))));

        $count = $keywordCount * $locationCount * self::BASE_RESULTS_PER_COMBINATION;
        $count = (int) round($count * $this->restrictiveness($filters));
        $count = min($count, $keywordCount * $locationCount * self::MAX_RESULTS_PER_COMBINATION);

        // Cost mirrors the UI's worst-case framing: "if every result gets
        // enriched, this is what it costs" — not a claim about this exact run.
        $cost = $count;

        return [
            'count' => $count,
            'cost' => $cost,
            'credits_left' => $this->ledger->balance($user) - $cost,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function restrictiveness(array $filters): float
    {
        $multiplier = 1.0;

        if (! empty($filters['has_website'])) {
            $multiplier *= 0.7;
        }

        if (! empty($filters['has_phone'])) {
            $multiplier *= 0.85;
        }

        $minRating = (float) ($filters['min_rating'] ?? 0);
        if ($minRating >= 4.5) {
            $multiplier *= 0.5;
        } elseif ($minRating >= 4) {
            $multiplier *= 0.7;
        } elseif ($minRating >= 3) {
            $multiplier *= 0.85;
        }

        if (! empty($filters['min_reviews']) && $filters['min_reviews'] !== 'custom') {
            $minReviews = (int) $filters['min_reviews'];
            $multiplier *= match (true) {
                $minReviews >= 200 => 0.5,
                $minReviews >= 50 => 0.7,
                $minReviews >= 10 => 0.85,
                default => 1.0,
            };
        } elseif (! empty($filters['min_reviews_from'])) {
            $multiplier *= 0.7;
        }

        if (! empty($filters['exclude_keyword'])) {
            $multiplier *= 0.9;
        }

        return $multiplier;
    }
}

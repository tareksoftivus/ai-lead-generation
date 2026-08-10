<?php

namespace App\Modules\Leads\Services;

use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\Place;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class LeadScoringService
{
    public const DEFAULT_WEIGHTS = [
        'reviews' => 30,
        'booking' => 40,
        'age' => 20,
        'competition' => 10,
    ];

    /**
     * @param  array<string, mixed>  $weights
     * @return array<string, int>
     */
    public function normalizeWeights(array $weights): array
    {
        return collect(self::DEFAULT_WEIGHTS)
            ->mapWithKeys(fn (int $default, string $key) => [
                $key => max(0, min(100, (int) ($weights[$key] ?? $default))),
            ])
            ->all();
    }

    /**
     * @return array<string, int>
     */
    public function signalScores(Lead $lead): array
    {
        $place = $lead->place;

        return [
            'reviews' => $this->reviewScore($place),
            'booking' => $this->bookingScore($place),
            'age' => $this->websiteAgeScore($place),
            'competition' => $this->competitionScore($place),
        ];
    }

    /**
     * @param  array<string, int>  $weights
     */
    public function score(Lead $lead, array $weights): int
    {
        $weights = $this->normalizeWeights($weights);
        $total = array_sum($weights);

        if ($total <= 0) {
            return (int) ($lead->score ?? 0);
        }

        $signals = $this->signalScores($lead);
        $score = collect($weights)->reduce(
            fn (int|float $sum, int $weight, string $key) => $sum + (($signals[$key] ?? 0) * $weight),
            0
        );

        return max(0, min(100, (int) round($score / $total)));
    }

    /**
     * @param  EloquentCollection<int, Lead>  $leads
     * @param  array<string, int>  $weights
     * @return array{count: int, changed: int}
     */
    public function rescore(EloquentCollection $leads, array $weights): array
    {
        $changed = 0;

        foreach ($leads as $lead) {
            $lead->loadMissing('place');

            $oldScore = (int) ($lead->score ?? 0);
            $newScore = $this->score($lead, $weights);

            $lead->forceFill([
                'score' => $newScore,
                'score_signals' => $this->signalLabels($lead),
            ])->save();

            if ($newScore !== $oldScore) {
                $changed++;
                LeadActivity::logScored($lead);
            }
        }

        return [
            'count' => $leads->count(),
            'changed' => $changed,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function signalLabels(Lead $lead): array
    {
        $signals = $this->signalScores($lead);

        return [
            'review_volume' => $this->label($signals['reviews']),
            'booking_presence' => $this->label($signals['booking']),
            'website_age' => $this->label($signals['age']),
            'local_competition' => $this->label($signals['competition']),
        ];
    }

    protected function reviewScore(?Place $place): int
    {
        if (! $place || ! $place->review_count) {
            return 0;
        }

        $volume = min(80, (int) round($place->review_count / 2));
        $rating = $place->rating ? (int) round(max(0, ($place->rating - 3) / 2) * 20) : 0;

        return max(0, min(100, $volume + $rating));
    }

    protected function bookingScore(?Place $place): int
    {
        if (! $place) {
            return 0;
        }

        $score = 0;

        if ($place->hasWebsite()) {
            $score += 70;
        }

        if ($place->hasPhone()) {
            $score += 20;
        }

        return max(0, min(100, $score));
    }

    protected function websiteAgeScore(?Place $place): int
    {
        if (! $place || ! $place->hasWebsite()) {
            return 25;
        }

        return $place->details_fetched_at ? 70 : 55;
    }

    protected function competitionScore(?Place $place): int
    {
        if (! $place) {
            return 0;
        }

        $score = 50;

        if (($place->review_count ?? 0) >= 50) {
            $score += 20;
        }

        if ($place->rating && $place->rating >= 4.5) {
            $score += 15;
        }

        if ($place->google_category) {
            $score += 5;
        }

        return max(0, min(100, $score));
    }

    protected function label(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Strong',
            $score >= 50 => 'Fair',
            default => 'Weak',
        };
    }
}

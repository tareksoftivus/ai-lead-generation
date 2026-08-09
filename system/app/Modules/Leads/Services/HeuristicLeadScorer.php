<?php

namespace App\Modules\Leads\Services;

use App\Modules\Leads\Contracts\LeadScorer;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\Place;

/**
 * A deterministic, non-AI stand-in for lead scoring. Every constant here is
 * "heuristic v1, tune freely" — this class exists purely so the rest of the
 * Leads module has a score/verdict/signal breakdown to render today, behind
 * the LeadScorer contract, so a future AI module can rebind a real
 * implementation without touching any other Leads code.
 */
class HeuristicLeadScorer implements LeadScorer
{
    public function score(Lead $lead): array
    {
        $place = $lead->place;

        $score = $this->computeScore($place);
        $signals = $this->computeSignals($place);

        return [
            'score' => $score,
            'headline' => $this->headline($score, $place),
            'explanation' => $this->explanation($score, $place),
            'signals' => $signals,
        ];
    }

    protected function computeScore(?Place $place): int
    {
        if (! $place) {
            return 0;
        }

        $reviewVolume = min(40, (int) round(($place->review_count ?? 0) / 5));
        $ratingContribution = $place->rating ? max(0, ($place->rating - 3) * 15) : 0;
        $websiteBonus = $place->hasWebsite() ? 15 : 0;
        $phoneBonus = $place->hasPhone() ? 15 : 0;

        $score = $reviewVolume + $ratingContribution + $websiteBonus + $phoneBonus;

        return (int) max(0, min(100, round($score)));
    }

    /**
     * @return array<string, string>
     */
    protected function computeSignals(?Place $place): array
    {
        $reviewCount = $place?->review_count ?? 0;

        return [
            'review_volume' => match (true) {
                $reviewCount >= 100 => 'Strong',
                $reviewCount >= 20 => 'Fair',
                default => 'Weak',
            },
            // Places data has no real "accepts online bookings" field — this is
            // a crude proxy off website presence, explicitly a placeholder.
            'booking_presence' => $place?->hasWebsite() ? 'Fair' : 'Weak',
            // TODO: replace with a real WHOIS/domain-age lookup when an AI module lands.
            'website_age' => 'Fair',
            // TODO: replace with a real nearby-competitor count when an AI module lands.
            'local_competition' => 'Fair',
        ];
    }

    protected function headline(int $score, ?Place $place): string
    {
        $name = $place?->name ?? 'This business';

        return match (true) {
            $score >= 80 => "{$name} looks like a strong opportunity.",
            $score >= 50 => "{$name} is worth a closer look.",
            default => "{$name} has limited signal to work with.",
        };
    }

    protected function explanation(int $score, ?Place $place): string
    {
        if (! $place) {
            return 'No business data was available to score this lead.';
        }

        $bits = [];

        if ($place->review_count) {
            $bits[] = "{$place->review_count} reviews".($place->rating ? " at {$place->rating} stars" : '');
        }

        $bits[] = $place->hasWebsite() ? 'an active website' : 'no website found';
        $bits[] = $place->hasPhone() ? 'a listed phone number' : 'no phone number listed';

        return 'Based on '.implode(', ', $bits).'.';
    }
}

<?php

namespace App\Modules\Leads\Contracts;

use App\Modules\Leads\Models\Lead;

interface LeadScorer
{
    /**
     * Score a lead's underlying business and explain the result.
     *
     * @return array{score: int, headline: string, explanation: string, signals: array<string, string>}
     */
    public function score(Lead $lead): array;
}

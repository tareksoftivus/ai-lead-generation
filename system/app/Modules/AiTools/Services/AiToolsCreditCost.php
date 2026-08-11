<?php

namespace App\Modules\AiTools\Services;

class AiToolsCreditCost
{
    public const BUSINESS_ANALYSIS_SETTING = 'ai_tools_business_analysis_credit_cost';

    public const EMAIL_GENERATION_SETTING = 'ai_tools_email_generation_credit_cost';

    public function perBusinessAnalysis(): int
    {
        return max(0, (int) setting(self::BUSINESS_ANALYSIS_SETTING, 1));
    }

    public function perEmailGeneration(): int
    {
        return max(0, (int) setting(self::EMAIL_GENERATION_SETTING, 1));
    }

    public function businessAnalysisCost(int $businesses): int
    {
        return max(0, $businesses) * $this->perBusinessAnalysis();
    }
}

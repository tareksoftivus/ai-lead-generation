<?php

namespace App\Modules\Leads\Services;

use App\Modules\Leads\Contracts\OutreachDraftGenerator;
use App\Modules\Leads\Models\Lead;
use Illuminate\Support\Facades\Auth;

/**
 * A canned-template stand-in for AI-drafted outreach copy. Pure string
 * interpolation, no LLM call — bound behind the OutreachDraftGenerator
 * contract so a future AI module can replace it without touching the rest
 * of the Leads module.
 */
class TemplateOutreachDraftGenerator implements OutreachDraftGenerator
{
    public function draft(Lead $lead): string
    {
        $businessName = $lead->place?->name ?? 'there';
        $category = $lead->place?->google_category;
        $senderName = Auth::user()?->name ?? 'the team';

        $categoryLine = $category
            ? "I came across {$businessName} while researching {$category} businesses in the area"
            : "I came across {$businessName} while researching local businesses";

        return <<<TEXT
        Hi {$businessName} team,

        {$categoryLine} and wanted to reach out.

        Worth a short call to see if it's a fit?

        Best,
        {$senderName}
        TEXT;
    }
}

<?php

namespace App\Modules\Leads\Contracts;

use App\Modules\Leads\Models\Lead;

interface OutreachDraftGenerator
{
    /**
     * Draft an opening outreach email for a lead. Never sent automatically —
     * always presented to the user to review, edit, and send themselves.
     */
    public function draft(Lead $lead): string;
}

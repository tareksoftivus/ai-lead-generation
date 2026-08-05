<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LeadsController extends Controller
{
    /**
     * The "All leads" screen — every found/enriched business.
     */
    public function index(): View
    {
        return view('panels.user.leads.index');
    }

    /**
     * The "Map view" screen — leads plotted by location.
     */
    public function map(): View
    {
        return view('panels.user.leads.map');
    }

    /**
     * The "Lists & tags" screen — the account's groupings of leads.
     */
    public function lists(): View
    {
        return view('panels.user.leads.lists');
    }

    /**
     * The "Lead details" screen — one business with its score, draft, and activity.
     */
    public function show(string $lead): View
    {
        return view('panels.user.leads.show');
    }
}

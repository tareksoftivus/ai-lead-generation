<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * The "New search" screen — filters rail + cost estimate + results.
     */
    public function new(): View
    {
        return view('panels.user.search.new');
    }

    /**
     * The "Search history" screen — past searches, re-run + delete actions.
     */
    public function history(): View
    {
        return view('panels.user.search.history');
    }
}

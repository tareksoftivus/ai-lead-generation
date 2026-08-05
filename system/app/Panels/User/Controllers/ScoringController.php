<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ScoringController extends Controller
{
    /**
     * The "Lead scoring" screen — weight the signals that order your leads.
     */
    public function index(): View
    {
        return view('panels.user.scoring.index');
    }
}

<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    /**
     * The "Business analysis" screen — AI-written reads of a list of leads.
     */
    public function index(): View
    {
        return view('panels.user.analysis.index');
    }
}

<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ActivitiesController extends Controller
{
    /**
     * The "Notes & activities" screen — everything that has happened across your leads.
     */
    public function index(): View
    {
        return view('panels.user.activities.index');
    }
}

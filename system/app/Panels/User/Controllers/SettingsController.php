<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * The "Account settings" screen — workspace, team, search defaults,
     * email preferences, and the danger zone.
     */
    public function index(): View
    {
        return view('panels.user.settings.index');
    }
}

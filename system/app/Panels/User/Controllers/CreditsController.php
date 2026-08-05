<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CreditsController extends Controller
{
    /**
     * The "Credits & billing" screen — every credit spent and returned.
     */
    public function index(): View
    {
        return view('panels.user.credits.index');
    }

    /**
     * The "Buy credits" screen — one-off top-up packs and plan changes.
     */
    public function buy(): View
    {
        return view('panels.user.credits.buy');
    }
}

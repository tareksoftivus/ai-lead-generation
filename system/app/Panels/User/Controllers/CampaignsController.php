<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CampaignsController extends Controller
{
    /**
     * The "Email campaigns" screen — sequences built from approved drafts.
     */
    public function index(): View
    {
        return view('panels.user.campaigns.index');
    }
}

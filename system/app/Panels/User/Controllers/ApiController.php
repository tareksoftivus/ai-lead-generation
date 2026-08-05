<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApiController extends Controller
{
    /**
     * The "API keys" screen — keys that authenticate every request.
     */
    public function keys(): View
    {
        return view('panels.user.api.keys');
    }

    /**
     * The "API documentation" screen — the endpoint reference.
     */
    public function docs(): View
    {
        return view('panels.user.api.docs');
    }

    /**
     * The "Integrations" screen — connected tools and webhooks.
     */
    public function integrations(): View
    {
        return view('panels.user.api.integrations');
    }
}

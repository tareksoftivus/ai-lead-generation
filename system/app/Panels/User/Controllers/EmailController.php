<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class EmailController extends Controller
{
    /**
     * The "Email generator" screen — a draft built around the gap found per business.
     */
    public function index(): View
    {
        return view('panels.user.email.index');
    }
}

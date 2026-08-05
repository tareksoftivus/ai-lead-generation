<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('panels.user.dashboard');
    }
}

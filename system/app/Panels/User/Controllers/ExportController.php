<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ExportController extends Controller
{
    /**
     * The "Export center" screen — your leads in a file you own.
     */
    public function index(): View
    {
        return view('panels.user.export.index');
    }
}

<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PipelineController extends Controller
{
    /**
     * The "Sales pipeline" screen — the leads you are actually working.
     */
    public function index(): View
    {
        return view('panels.user.pipeline.index');
    }
}

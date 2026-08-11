<?php

use App\Modules\Analysis\Http\Controllers\User\BusinessAnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('analysis', [BusinessAnalysisController::class, 'index'])->name('analysis.index');
Route::post('analysis', [BusinessAnalysisController::class, 'run'])->name('analysis.run');

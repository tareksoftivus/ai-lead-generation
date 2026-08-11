<?php

use App\Modules\AiTools\Http\Controllers\User\BusinessAnalysisController;
use App\Modules\AiTools\Http\Controllers\User\EmailGeneratorController;
use Illuminate\Support\Facades\Route;

Route::get('analysis', [BusinessAnalysisController::class, 'index'])->name('analysis.index');
Route::post('analysis', [BusinessAnalysisController::class, 'run'])->name('analysis.run');

Route::get('email', [EmailGeneratorController::class, 'index'])->name('email.index');
Route::post('email', [EmailGeneratorController::class, 'generate'])->name('email.generate');
Route::post('email/templates', [EmailGeneratorController::class, 'storeTemplate'])->name('email.templates.store');
Route::post('email/campaigns', [EmailGeneratorController::class, 'queueCampaign'])->name('email.campaigns.store');

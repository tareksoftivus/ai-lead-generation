<?php

use App\Modules\ApiIntegrations\Http\Controllers\Api\V1\LeadsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function (): void {
    Route::get('leads', [LeadsController::class, 'index'])->name('leads.index');
    Route::get('leads/{lead}', [LeadsController::class, 'show'])->name('leads.show');
});

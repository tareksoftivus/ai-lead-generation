<?php

use App\Modules\ApiIntegrations\Http\Controllers\User\ApiDocsController;
use App\Modules\ApiIntegrations\Http\Controllers\User\ApiKeysController;
use App\Modules\ApiIntegrations\Http\Controllers\User\IntegrationsController;
use Illuminate\Support\Facades\Route;

Route::get('api', fn () => redirect()->route('user.api.keys'))->name('api.index');

Route::prefix('api')->name('api.')->group(function (): void {
    Route::get('keys', [ApiKeysController::class, 'index'])->name('keys');
    Route::post('keys', [ApiKeysController::class, 'store'])->name('keys.store');
    Route::delete('keys/{token}', [ApiKeysController::class, 'destroy'])->name('keys.destroy');

    Route::get('docs', ApiDocsController::class)->name('docs');
});

Route::get('integrations', [IntegrationsController::class, 'index'])->name('api.integrations');
Route::post('integrations/{provider}', [IntegrationsController::class, 'store'])->name('api.integrations.store');
Route::put('integrations/connections/{connection}', [IntegrationsController::class, 'update'])->name('api.integrations.update');
Route::delete('integrations/connections/{connection}', [IntegrationsController::class, 'destroy'])->name('api.integrations.destroy');

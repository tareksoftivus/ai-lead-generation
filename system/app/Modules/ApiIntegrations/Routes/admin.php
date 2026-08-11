<?php

use App\Modules\ApiIntegrations\Http\Controllers\Admin\IntegrationProvidersController;
use Illuminate\Support\Facades\Route;

Route::get('api-integrations', [IntegrationProvidersController::class, 'index'])->name('api-integrations.index');
Route::put('api-integrations/{provider}', [IntegrationProvidersController::class, 'update'])->name('api-integrations.update');

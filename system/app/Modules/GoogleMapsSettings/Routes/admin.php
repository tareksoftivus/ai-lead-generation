<?php

use App\Modules\GoogleMapsSettings\Http\Controllers\Admin\GoogleMapsSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('google-maps-settings', [GoogleMapsSettingsController::class, 'edit'])->name('google-maps-settings.index');
Route::put('google-maps-settings', [GoogleMapsSettingsController::class, 'update'])->name('google-maps-settings.update');

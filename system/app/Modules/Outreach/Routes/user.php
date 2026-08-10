<?php

use App\Modules\Outreach\Http\Controllers\User\CampaignsController;
use App\Modules\Outreach\Http\Controllers\User\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('campaigns', [CampaignsController::class, 'index'])->name('campaigns.index');
Route::post('campaigns', [CampaignsController::class, 'store'])->name('campaigns.store');
Route::patch('campaigns/{campaign}', [CampaignsController::class, 'update'])->name('campaigns.update');
Route::post('campaigns/{campaign}/duplicate', [CampaignsController::class, 'duplicate'])->name('campaigns.duplicate');
Route::delete('campaigns/{campaign}', [CampaignsController::class, 'destroy'])->name('campaigns.destroy');

Route::get('export', [ExportController::class, 'index'])->name('export.index');
Route::post('export', [ExportController::class, 'store'])->name('export.store');
Route::get('export/{leadExport}/download', [ExportController::class, 'download'])->name('export.download');
Route::delete('export/{leadExport}', [ExportController::class, 'destroy'])->name('export.destroy');

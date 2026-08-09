<?php

use App\Modules\Credits\Http\Controllers\User\CreditsController;
use Illuminate\Support\Facades\Route;

Route::get('credits', [CreditsController::class, 'index'])->name('credits.index');
Route::get('credits/buy', [CreditsController::class, 'buy'])->name('credits.buy');

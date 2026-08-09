<?php

use App\Modules\Credits\Http\Controllers\User\CreditsController;
use Illuminate\Support\Facades\Route;

Route::get('credits', [CreditsController::class, 'index'])->name('credits.index');
Route::get('credits/buy', [CreditsController::class, 'buy'])->name('credits.buy');
Route::post('credits/checkout', [CreditsController::class, 'startCheckout'])->name('credits.checkout.start');
Route::get('credits/checkout/return/{gateway?}', [CreditsController::class, 'paymentReturn'])->name('credits.checkout.return');
Route::get('credits/checkout/cancel/{pricingPlan:slug}', [CreditsController::class, 'paymentCancel'])->name('credits.checkout.cancel');
Route::get('credits/checkout/{pricingPlan:slug}', [CreditsController::class, 'checkout'])->name('credits.checkout');
Route::post('credits/checkout/{pricingPlan:slug}', [CreditsController::class, 'completeCheckout'])->name('credits.checkout.complete');

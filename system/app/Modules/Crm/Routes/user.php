<?php

use App\Modules\Crm\Http\Controllers\User\ActivitiesController;
use App\Modules\Crm\Http\Controllers\User\ContactsController;
use App\Modules\Crm\Http\Controllers\User\PipelineController;
use Illuminate\Support\Facades\Route;

Route::get('pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
Route::patch('pipeline/{lead}/status', [PipelineController::class, 'updateStatus'])->name('pipeline.update-status');
Route::delete('pipeline/{lead}', [PipelineController::class, 'remove'])->name('pipeline.remove');

Route::get('contacts', [ContactsController::class, 'index'])->name('contacts.index');
Route::post('contacts', [ContactsController::class, 'store'])->name('contacts.store');
Route::patch('contacts/{contact}', [ContactsController::class, 'update'])->name('contacts.update');
Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])->name('contacts.destroy');

Route::get('activities', [ActivitiesController::class, 'index'])->name('activities.index');
Route::post('activities', [ActivitiesController::class, 'store'])->name('activities.store');

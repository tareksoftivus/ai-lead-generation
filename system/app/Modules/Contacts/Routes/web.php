<?php

use App\Modules\Contacts\Http\Controllers\ContactSubmissionController;
use Illuminate\Support\Facades\Route;

Route::post('contact', [ContactSubmissionController::class, 'store'])->name('contacts.store');

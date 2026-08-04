<?php

use App\Modules\Contacts\Http\Controllers\Admin\ContactsController;
use Illuminate\Support\Facades\Route;

Route::get('contacts/export/csv', [ContactsController::class, 'export'])->name('contacts.export');
Route::post('contacts/{contact}/mark-read', [ContactsController::class, 'markRead'])->name('contacts.mark-read');
Route::post('contacts/{contact}/mark-unread', [ContactsController::class, 'markUnread'])->name('contacts.mark-unread');
Route::resource('contacts', ContactsController::class)->only(['index', 'show', 'destroy']);

<?php

use App\Modules\Leads\Http\Controllers\User\LeadListsController;
use App\Modules\Leads\Http\Controllers\User\LeadsController;
use App\Modules\Leads\Http\Controllers\User\SearchController;
use App\Modules\Leads\Http\Controllers\User\TagsController;
use Illuminate\Support\Facades\Route;

// Search
Route::get('search/new', [SearchController::class, 'new'])->name('search.new');
Route::post('search/new', [SearchController::class, 'run'])->name('search.run');
Route::post('search/new/estimate', [SearchController::class, 'estimate'])->name('search.estimate');
Route::post('search/new/save', [SearchController::class, 'saveSearch'])->name('search.save');
Route::get('search/history', [SearchController::class, 'history'])->name('search.history');
Route::get('search/history/{searchRun}', [SearchController::class, 'results'])->name('search.results');
Route::post('search/history/{searchRun}/rerun', [SearchController::class, 'rerun'])->name('search.rerun');
Route::delete('search/history/{searchRun}', [SearchController::class, 'destroy'])->name('search.destroy');

// Leads
Route::get('leads', [LeadsController::class, 'index'])->name('leads.index');
Route::get('leads/map', [LeadsController::class, 'map'])->name('leads.map');
Route::get('leads/lists', [LeadListsController::class, 'index'])->name('leads.lists');
Route::post('leads/save-from-search', [LeadsController::class, 'saveFromSearch'])->name('leads.save-from-search');
Route::post('leads/bulk/tag', [LeadsController::class, 'bulkTag'])->name('leads.bulk-tag');
Route::post('leads/bulk/status', [LeadsController::class, 'bulkStatus'])->name('leads.bulk-status');
Route::delete('leads/bulk', [LeadsController::class, 'bulkDelete'])->name('leads.bulk-delete');

// Lists & tags — registered before the leads/{lead} wildcard routes below so
// "leads/lists/..." isn't swallowed by {lead} matching the literal "lists".
Route::post('leads/lists/lists', [LeadListsController::class, 'store'])->name('leads.lists.store');
Route::patch('leads/lists/lists/{list}', [LeadListsController::class, 'update'])->name('leads.lists.update');
Route::delete('leads/lists/lists/{list}', [LeadListsController::class, 'destroy'])->name('leads.lists.destroy');
Route::post('leads/lists/tags', [TagsController::class, 'store'])->name('leads.tags.store');
Route::patch('leads/lists/tags/{tag}', [TagsController::class, 'update'])->name('leads.tags.update');
Route::delete('leads/lists/tags/{tag}', [TagsController::class, 'destroy'])->name('leads.tags.destroy');

Route::get('leads/{lead}', [LeadsController::class, 'show'])->name('leads.show');
Route::patch('leads/{lead}/status', [LeadsController::class, 'updateStatus'])->name('leads.update-status');
Route::post('leads/{lead}/tags', [LeadsController::class, 'attachTag'])->name('leads.attach-tag');
Route::delete('leads/{lead}/tags/{tag}', [LeadsController::class, 'detachTag'])->name('leads.detach-tag');
Route::post('leads/{lead}/notes', [LeadsController::class, 'addNote'])->name('leads.add-note');
Route::delete('leads/{lead}', [LeadsController::class, 'destroy'])->name('leads.destroy');

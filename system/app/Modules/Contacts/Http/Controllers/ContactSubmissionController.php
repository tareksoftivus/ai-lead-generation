<?php

namespace App\Modules\Contacts\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Contacts\Http\Requests\StoreContactRequest;
use App\Modules\Contacts\Services\ContactsService;
use Illuminate\Http\RedirectResponse;

class ContactSubmissionController extends Controller
{
    public function __construct(
        protected ContactsService $service
    ) {}

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return back()->with('success', __('Thank you. Your message has been sent successfully.'));
    }
}

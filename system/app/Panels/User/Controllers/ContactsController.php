<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ContactsController extends Controller
{
    /**
     * The "Contacts" screen — the people behind your leads.
     */
    public function index(): View
    {
        return view('panels.user.contacts.index');
    }
}

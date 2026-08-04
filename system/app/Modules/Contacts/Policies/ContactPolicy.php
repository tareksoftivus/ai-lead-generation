<?php

namespace App\Modules\Contacts\Policies;

use App\Modules\Contacts\Models\Contact;
use Illuminate\Contracts\Auth\Authenticatable;

class ContactPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('contacts.view');
    }

    public function view(Authenticatable $user, Contact $contact): bool
    {
        return $user->can('contacts.view');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('contacts.create');
    }

    public function update(Authenticatable $user, Contact $contact): bool
    {
        return $user->can('contacts.edit');
    }

    public function delete(Authenticatable $user, Contact $contact): bool
    {
        return $user->can('contacts.delete');
    }
}

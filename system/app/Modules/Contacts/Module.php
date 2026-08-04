<?php

namespace App\Modules\Contacts;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Policies\ContactPolicy;
use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'contacts';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'contacts.view' => 'View Contacts',
                'contacts.edit' => 'Update Contacts',
                'contacts.delete' => 'Delete Contacts',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            Contact::class => ContactPolicy::class,
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Management')
            ->item(label: 'Contacts', route: 'admin.contacts.*')
            ->icon('ph-envelope-simple')
            ->permission('contacts.view')
            ->order(100);
    }
}

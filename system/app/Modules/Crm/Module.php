<?php

namespace App\Modules\Crm;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'crm';
    }

    public function permissions(): array
    {
        return [
            'web' => [
                'crm.view' => 'View CRM pipeline, contacts, and activities',
                'crm.manage' => 'Manage CRM pipeline, contacts, notes, and activities',
            ],
        ];
    }

    public function userNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('CRM')
            ->item(label: 'Sales pipeline', route: 'user.pipeline.index')
            ->icon('ph-kanban')
            ->order(40);

        $navigation
            ->group('CRM')
            ->item(label: 'Contacts', route: 'user.contacts.index')
            ->icon('ph-address-book')
            ->order(41);

        $navigation
            ->group('CRM')
            ->item(label: 'Notes & activities', route: 'user.activities.index')
            ->icon('ph-note-pencil')
            ->order(42);
    }
}

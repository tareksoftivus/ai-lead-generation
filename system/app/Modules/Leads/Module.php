<?php

namespace App\Modules\Leads;

use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Policies\LeadPolicy;
use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'leads';
    }

    public function permissions(): array
    {
        return [
            'web' => [
                'leads.search' => 'Search Google Places',
                'leads.view' => 'View own leads',
                'leads.manage' => 'Manage own leads (status, tags, notes, lists)',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            Lead::class => LeadPolicy::class,
        ];
    }

    public function userNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Lead Generation')
            ->item(label: 'New search', route: 'user.search.new')
            ->icon('ph-magnifying-glass')
            ->permission('leads.search')
            ->order(10);

        $navigation
            ->group('Lead Generation')
            ->item(label: 'Search history', route: 'user.search.history')
            ->icon('ph-clock-counter-clockwise')
            ->permission('leads.search')
            ->order(11);

        $navigation
            ->group('Leads')
            ->item(label: 'All leads', route: 'user.leads.index')
            ->icon('ph-users-three')
            ->permission('leads.view')
            ->order(20);

        $navigation
            ->group('Leads')
            ->item(label: 'Map view', route: 'user.leads.map')
            ->icon('ph-map-pin')
            ->permission('leads.view')
            ->order(21);

        $navigation
            ->group('Leads')
            ->item(label: 'Lists & tags', route: 'user.leads.lists')
            ->icon('ph-folders')
            ->permission('leads.manage')
            ->order(22);
    }
}

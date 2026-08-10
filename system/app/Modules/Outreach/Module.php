<?php

namespace App\Modules\Outreach;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'outreach';
    }

    public function permissions(): array
    {
        return [
            'web' => [
                'outreach.view' => 'View email campaigns and exports',
                'outreach.manage' => 'Manage campaigns and export leads',
            ],
        ];
    }

    public function userNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Outreach')
            ->item(label: 'Email campaigns', route: 'user.campaigns.index')
            ->icon('ph-paper-plane-tilt')
            ->permission('outreach.view')
            ->order(50);

        $navigation
            ->group('Outreach')
            ->item(label: 'Export center', route: 'user.export.index')
            ->icon('ph-download-simple')
            ->permission('outreach.view')
            ->order(51);
    }
}

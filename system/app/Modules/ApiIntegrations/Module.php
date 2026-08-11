<?php

namespace App\Modules\ApiIntegrations;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'api-integrations';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'api-integrations.view' => 'View API and integration settings',
                'api-integrations.edit' => 'Edit API and integration settings',
            ],
            'web' => [
                'api-integrations.manage' => 'Manage own API keys and integrations',
            ],
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Settings')
            ->item(label: 'API & integrations', route: 'admin.api-integrations.index')
            ->icon('ph-plugs-connected')
            ->permission('api-integrations.view')
            ->order(92);
    }
}

<?php

namespace App\Modules\GoogleMapsSettings;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'google-maps-settings';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'google-maps-settings.manage' => 'Manage Google Maps API settings',
            ],
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('System')
            ->item(label: 'Google Maps API', route: 'admin.google-maps-settings.index')
            ->icon('ph-map-trifold')
            ->permission('google-maps-settings.manage')
            ->order(135);

        $navigation
            ->group('System')
            ->item(label: 'Google Maps Logs', route: 'admin.google-maps-settings.logs')
            ->icon('ph-list-magnifying-glass')
            ->permission('google-maps-settings.manage')
            ->order(136);
    }
}

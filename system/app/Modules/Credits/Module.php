<?php

namespace App\Modules\Credits;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'credits';
    }

    public function permissions(): array
    {
        return [
            'web' => [
                'credits.view' => 'View own credit balance and history',
            ],
        ];
    }

    public function userNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Account')
            ->item(label: 'Credits & billing', route: 'user.credits.index')
            ->icon('ph-coins')
            ->permission('credits.view')
            ->order(60);
    }
}

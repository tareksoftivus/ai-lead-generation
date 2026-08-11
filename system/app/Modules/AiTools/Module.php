<?php

namespace App\Modules\AiTools;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'ai-tools';
    }

    public function userNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('AI Tools')
            ->item(label: 'Business analysis', route: 'user.analysis.index')
            ->icon('ph-sparkle')
            ->order(30);

        $navigation
            ->group('AI Tools')
            ->item(label: 'Email generator', route: 'user.email.index')
            ->icon('ph-envelope-simple')
            ->order(32);
    }
}

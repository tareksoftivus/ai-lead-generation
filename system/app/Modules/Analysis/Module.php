<?php

namespace App\Modules\Analysis;

use App\Modules\Shared\Support\BasePanelModule;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'analysis';
    }
}

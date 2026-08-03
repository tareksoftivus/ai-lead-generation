<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendThemeSetting;
use Illuminate\Database\Seeder;

class FrontendThemeSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'active_theme' => 'leadatlas',
            'theme.leadatlas.enabled' => '1',
            'theme.leadatlas.logo_text' => 'LeadAtlas',
            'theme.leadatlas.primary_color' => '#4F39F6',
            'theme.leadatlas.accent_color' => '#0F172A',
            'theme.leadatlas.show_hero_kicker' => '1',
        ];

        foreach ($defaults as $key => $value) {
            FrontendThemeSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}

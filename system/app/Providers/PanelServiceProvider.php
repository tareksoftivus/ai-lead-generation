<?php

namespace App\Providers;

use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PanelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach (config('panels', []) as $key => $panel) {
            if (empty($panel['active'])) {
                continue;
            }

            $panelPath = app_path('Panels/'.ucfirst($key));
            $routesFile = $panelPath.'/routes.php';
            $viewsPath = resource_path("views/panels/{$key}");

            if (file_exists($routesFile)) {
                Route::middleware($panel['middleware'] ?? ['web', 'auth'])
                    ->prefix($panel['prefix'])
                    ->name("{$key}.")
                    ->group($routesFile);
            }

            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, $key);
            }
        }
    }

    public function register(): void
    {
        $this->app->singleton('current.panel', function () {
            $prefix = request()->segment(1);

            foreach (config('panels', []) as $key => $panel) {
                if (($panel['prefix'] ?? '') !== $prefix || empty($panel['active'])) {
                    continue;
                }

                $navigation = array_merge(
                    $panel['navigation'] ?? [],
                    app(ModuleRegistry::class)->buildNavigation($key)
                );

                usort($navigation, function (array $left, array $right): int {
                    $order = ($left['order'] ?? 9999) <=> ($right['order'] ?? 9999);

                    return $order !== 0 ? $order : strcmp($left['label'] ?? '', $right['label'] ?? '');
                });

                return array_merge($panel, [
                    'key' => $key,
                    'navigation' => $navigation,
                ]);
            }

            return null;
        });
    }
}

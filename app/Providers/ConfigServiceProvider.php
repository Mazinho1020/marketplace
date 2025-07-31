<?php

namespace App\Providers;

use App\Services\Config\ConfigManager;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para o sistema de configurações
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar ConfigManager como singleton
        $this->app->singleton(ConfigManager::class, function () {
            return ConfigManager::getInstance();
        });

        // Registrar alias para facilitar injeção de dependência
        $this->app->alias(ConfigManager::class, 'config.manager');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar helper functions globalmente se necessário
        if (!function_exists('config_marketplace')) {
            require_once app_path('Services/Config/ConfigManager.php');
        }
    }
}

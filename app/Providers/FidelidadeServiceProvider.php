<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class FidelidadeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar serviços específicos da Fidelidade
        $this->app->singleton(\App\Services\Fidelidade\FidelidadeService::class);
        $this->app->singleton(\App\Services\Fidelidade\PontosService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Carregar rotas da Fidelidade
        $this->loadRoutes();

        // Carregar views da Fidelidade
        $this->loadViewsFrom(resource_path('views/fidelidade'), 'fidelidade');
    }

    /**
     * Carregar as rotas do módulo Fidelidade
     */
    protected function loadRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/fidelidade/web.php'));
    }
}

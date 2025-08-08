<?php

namespace App\Providers;

use Illuminate\Auth\AuthManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Customizar o redirecionamento do middleware Authenticate
        Authenticate::redirectUsing(function ($request) {
            // Se a URL é de comerciantes, redirecionar para login de comerciantes
            if ($request->is('comerciantes/*')) {
                return route('comerciantes.login');
            }

            // Caso contrário, usar o login padrão (admin)
            return route('login');
        });
    }
}

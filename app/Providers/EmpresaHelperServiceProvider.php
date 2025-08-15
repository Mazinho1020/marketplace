<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EmpresaHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/EmpresaHelpers.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

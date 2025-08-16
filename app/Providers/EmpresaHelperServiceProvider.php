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
        // Helper file temporarily disabled until created
        // require_once app_path('Helpers/EmpresaHelpers.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

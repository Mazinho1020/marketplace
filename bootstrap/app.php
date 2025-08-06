<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

// Define timezone globalmente para toda a aplicação
date_default_timezone_set('America/Cuiaba');

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\PermissionServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        then: function () {
            Route::middleware('web')->group(base_path('routes/admin.php'));
            Route::middleware('web')->group(base_path('routes/comerciante.php'));
        },
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'empresa' => \App\Http\Middleware\EmpresaMiddleware::class,
            'auth.simple' => \App\Http\Middleware\AuthMiddleware::class,
            'auth.comerciante' => \App\Http\Middleware\ComercianteAuthMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'auto.permission' => \App\Http\Middleware\AutoPermissionCheck::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

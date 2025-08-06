<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Permission\PermissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Models\User\EmpresaUsuario;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PermissionService::class, function ($app) {
            return new PermissionService();
        });
    }

    public function boot()
    {
        // Registrar Gates dinamicamente
        Gate::before(function (EmpresaUsuario $user, string $ability) {
            // Super admin bypass
            if ($user->hasRole('super_admin')) {
                return true;
            }

            // Verificar permissão específica
            if ($user->hasPermission($ability)) {
                return true;
            }

            return null; // Continuar verificação normal
        });

        // Blade directives
        Blade::if('permission', function (string $permission) {
            return Auth::check() && Auth::user()->hasPermission($permission);
        });

        Blade::if('role', function (string $role) {
            return Auth::check() && Auth::user()->hasRole($role);
        });

        Blade::if('anypermission', function (...$permissions) {
            return Auth::check() && Auth::user()->hasAnyPermission($permissions);
        });

        Blade::if('allpermissions', function (...$permissions) {
            return Auth::check() && Auth::user()->hasAllPermissions($permissions);
        });
    }
}

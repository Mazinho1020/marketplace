<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Services\Permission\PermissionService;
use App\Http\Middleware\AutoPermissionCheck;
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

    public function boot(Router $router)
    {
        // Registrar middlewares
        $router->aliasMiddleware('auto.permission', AutoPermissionCheck::class);
        $router->aliasMiddleware('permission', \App\Http\Middleware\CheckPermission::class);

        // Aplicar middleware automático para grupos específicos
        $this->applyAutoPermissions($router);

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

        Blade::if('empresaPermission', function (string $permission, int $empresaId) {
            return Auth::guard('comerciante')->check() &&
                Auth::guard('comerciante')->user()->temPermissaoEmpresa($empresaId, $permission);
        });
    }

    /**
     * Aplica permissões automáticas para grupos de rotas
     */
    protected function applyAutoPermissions(Router $router): void
    {
        // Aplicar para todas as rotas de comerciantes (exceto login/logout)
        $router->middlewareGroup('comerciantes.protected', [
            'auth:comerciante',
            'auto.permission:comerciante'
        ]);

        // Aplicar para todas as rotas de admin (exceto login/logout)
        $router->middlewareGroup('admin.protected', [
            'auth:admin',
            'auto.permission:admin'
        ]);
    }
}

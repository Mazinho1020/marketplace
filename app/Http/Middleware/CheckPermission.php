<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Permission\PermissionService;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission, string $guard = 'web')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard($guard)->user();

        if (!$this->permissionService->hasPermission($user, $permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Acesso negado',
                    'message' => 'Você não tem permissão para acessar este recurso',
                    'required_permission' => $permission
                ], 403);
            }

            abort(403, 'Acesso negado. Permissão necessária: ' . $permission);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Permission\PermissionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Middleware automático de permissões
 * Verifica permissões baseado nas rotas automaticamente
 */
class AutoPermissionCheck
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     * Verifica permissões automaticamente baseado na rota e método HTTP
     */
    public function handle(Request $request, Closure $next, string $guard = 'comerciante')
    {
        // Verificar se o usuário está autenticado
        if (!Auth::guard($guard)->check()) {
            return $this->redirectToLogin($guard);
        }

        $user = Auth::guard($guard)->user();
        $route = Route::current();

        // Obter informações da rota
        $routeName = $route->getName();
        $controller = $route->getController();
        $action = $route->getActionMethod();
        $httpMethod = $request->getMethod();

        // Determinar a permissão necessária automaticamente
        $permission = $this->determinePermission($routeName, $action, $httpMethod, $request);

        // Pular verificação para rotas públicas
        if ($this->isPublicRoute($routeName)) {
            return $next($request);
        }

        // Verificar se o usuário tem a permissão necessária
        if (!$this->permissionService->hasPermission($user, $permission)) {
            return $this->handleUnauthorized($request, $permission);
        }

        return $next($request);
    }

    /**
     * Determina a permissão necessária baseada na rota
     */
    protected function determinePermission(string $routeName, string $action, string $httpMethod, Request $request): string
    {
        // Mapear métodos HTTP para ações
        $actionMap = [
            'GET' => [
                'index' => 'visualizar',
                'show' => 'visualizar',
                'create' => 'criar',
                'edit' => 'editar',
            ],
            'POST' => [
                'store' => 'criar',
            ],
            'PUT' => [
                'update' => 'editar',
            ],
            'PATCH' => [
                'update' => 'editar',
            ],
            'DELETE' => [
                'destroy' => 'excluir',
            ]
        ];

        // Extrair o recurso da rota
        $resource = $this->extractResourceFromRoute($routeName);

        // Determinar a ação baseada no método HTTP e ação do controller
        $permissionAction = $actionMap[$httpMethod][$action] ?? $action;

        // Casos especiais baseados na rota
        if (str_contains($routeName, 'usuarios')) {
            $permissionAction = $this->determineUserPermission($action, $httpMethod);
        }

        return "{$resource}.{$permissionAction}";
    }

    /**
     * Extrai o recurso principal da rota
     */
    protected function extractResourceFromRoute(string $routeName): string
    {
        $parts = explode('.', $routeName);

        // Mapear rotas para recursos
        $resourceMap = [
            'empresas' => 'empresa',
            'usuarios' => 'usuario',
            'marcas' => 'marca',
            'dashboard' => 'dashboard',
            'relatorios' => 'relatorio',
            'configuracoes' => 'configuracao',
        ];

        foreach ($resourceMap as $route => $resource) {
            if (str_contains($routeName, $route)) {
                return $resource;
            }
        }

        // Default: usar a segunda parte da rota
        return $parts[1] ?? 'geral';
    }

    /**
     * Determina permissões específicas para usuários
     */
    protected function determineUserPermission(string $action, string $httpMethod): string
    {
        $userPermissions = [
            'index' => 'gerenciar',
            'show' => 'visualizar',
            'create' => 'gerenciar',
            'store' => 'gerenciar',
            'edit' => 'gerenciar',
            'update' => 'gerenciar',
            'destroy' => 'gerenciar',
            'adicionarUsuario' => 'gerenciar',
            'editarUsuario' => 'gerenciar',
            'removerUsuario' => 'gerenciar',
        ];

        return $userPermissions[$action] ?? 'visualizar';
    }

    /**
     * Rotas que não precisam de verificação de permissão
     */
    protected function isPublicRoute(string $routeName): bool
    {
        $publicRoutes = [
            'comerciantes.dashboard',
            'comerciantes.profile',
            'comerciantes.logout',
            'comerciantes.password',
        ];

        return in_array($routeName, $publicRoutes);
    }

    /**
     * Redireciona para o login baseado no guard
     */
    protected function redirectToLogin(string $guard)
    {
        $loginRoutes = [
            'web' => 'login',
            'admin' => 'admin.login',
            'comerciante' => 'comerciantes.login',
        ];

        $loginRoute = $loginRoutes[$guard] ?? 'login';

        return redirect()->route($loginRoute);
    }

    /**
     * Trata acesso não autorizado
     */
    protected function handleUnauthorized(Request $request, string $permission)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para acessar este recurso',
                'required_permission' => $permission
            ], 403);
        }

        // Redirecionar com mensagem de erro
        return redirect()->back()->with(
            'error',
            "Acesso negado. Você não tem permissão para: {$permission}"
        );
    }
}

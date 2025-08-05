<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware personalizado para autenticação de comerciantes
 * Resolve o problema de redirecionamento
 */
class ComercianteAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar se o usuário está logado no guard comerciante
        if (!Auth::guard('comerciante')->check()) {
            // Forçar redirecionamento para a URL correta
            return redirect('http://localhost:8000/comerciantes/login')
                ->withErrors(['error' => 'Faça login para acessar esta área.']);
        }

        return $next($request);
    }
}

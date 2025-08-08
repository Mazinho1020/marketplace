<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware de autenticação personalizado que resolve o redirecionamento correto
 * para diferentes guards (admin vs comerciante)
 */
class Authenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        // Determinar para onde redirecionar baseado na URL
        if ($request->is('comerciantes/*')) {
            return redirect()->route('comerciantes.login');
        }

        // Para admin e outras rotas, usar o login padrão
        return redirect()->route('login');
    }
}

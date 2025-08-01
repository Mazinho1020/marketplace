<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmpresaMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Por enquanto, sempre permite acesso
        // TODO: Implementar lógica de verificação de empresa quando o login estiver pronto

        // Simular empresa_id para desenvolvimento
        if (!session()->has('empresa_id')) {
            session(['empresa_id' => 1]);
        }

        return $next($request);
    }
}

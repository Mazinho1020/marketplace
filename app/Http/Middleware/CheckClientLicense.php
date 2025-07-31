<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Config\ConfigManager;

class CheckClientLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $config = ConfigManager::getInstance();

        // Pular verificação se for empresa desenvolvedora
        if ($config->isDeveloperCompany()) {
            return $next($request);
        }

        // Verificar se há usuário logado com empresa
        if (!Auth::check() || !Auth::user()->empresa_id) {
            return redirect()->route('login');
        }

        $clientId = Auth::user()->empresa_id;

        // Verificar se cliente está ativo
        if (!$config->isClientActive($clientId)) {
            return redirect()->route('subscription.expired')
                ->with('error', 'Sua licença expirou. Entre em contato conosco para renovar.');
        }

        return $next($request);
    }
}

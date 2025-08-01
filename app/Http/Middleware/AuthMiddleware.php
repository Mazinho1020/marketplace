<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Middleware de Autenticação Simplificado
 */
class AuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$params)
    {
        // Verificar se está autenticado
        if (!$this->isAuthenticated()) {
            // Salvar URL de destino
            Session::put('redirect_after_login', $request->fullUrl());
            return redirect('/login')->withErrors(['error' => 'Faça login para acessar esta área.']);
        }

        // Verificar nível de acesso se especificado
        if (!empty($params)) {
            $requiredLevel = (int) $params[0];
            if (!$this->hasAccessLevel($requiredLevel)) {
                return redirect('/admin/access-denied')->withErrors(['error' => 'Você não tem permissão suficiente para acessar esta área.']);
            }
        }

        return $next($request);
    }

    /**
     * Verificar se o usuário está autenticado
     */
    protected function isAuthenticated()
    {
        if (!Session::has('usuario_id')) {
            return false;
        }

        // Verificar timeout de sessão (30 minutos)
        if (Session::has('last_activity')) {
            $timeout = 30 * 60; // 30 minutos
            if (time() - Session::get('last_activity') > $timeout) {
                Session::flush();
                return false;
            }
            Session::put('last_activity', time());
        }

        return true;
    }

    /**
     * Verificar nível de acesso
     */
    protected function hasAccessLevel($requiredLevel)
    {
        $userLevel = Session::get('nivel_acesso', 0);
        return $userLevel >= $requiredLevel;
    }

    /**
     * Métodos estáticos para usar em outros locais
     */
    public static function check()
    {
        return Session::has('usuario_id');
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }

        return (object) [
            'id' => Session::get('usuario_id'),
            'nome' => Session::get('usuario_nome'),
            'email' => Session::get('usuario_email'),
            'empresa_id' => Session::get('empresa_id'),
            'tipo' => Session::get('usuario_tipo'),
            'tipo_nome' => Session::get('tipo_nome'),
            'nivel_acesso' => Session::get('nivel_acesso', 0)
        ];
    }

    public static function hasLevel($level)
    {
        return Session::get('nivel_acesso', 0) >= $level;
    }

    public static function isAdmin()
    {
        return Session::get('usuario_tipo') === 'admin';
    }

    public static function isGerente()
    {
        return in_array(Session::get('usuario_tipo'), ['admin', 'gerente']);
    }
}

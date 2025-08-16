<?php

/**
 * Helpers específicos para empresas
 */

if (!function_exists('empresa_atual')) {
    /**
     * Obtém a empresa atual do usuário logado
     */
    function empresa_atual()
    {
        return auth()->user()?->empresa_id ?? null;
    }
}

if (!function_exists('usuario_empresa')) {
    /**
     * Obtém a empresa do usuário atual
     */
    function usuario_empresa()
    {
        $user = auth()->user();
        
        if ($user && method_exists($user, 'empresa')) {
            return $user->empresa;
        }
        
        return null;
    }
}

if (!function_exists('pode_acessar_empresa')) {
    /**
     * Verifica se o usuário pode acessar uma determinada empresa
     */
    function pode_acessar_empresa($empresaId)
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Se for admin, pode acessar qualquer empresa
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Verifica se tem acesso à empresa específica
        return $user->empresa_id == $empresaId;
    }
}

if (!function_exists('empresas_usuario')) {
    /**
     * Obtém todas as empresas que o usuário pode acessar
     */
    function empresas_usuario()
    {
        $user = auth()->user();
        
        if (!$user) {
            return collect();
        }
        
        // Se for admin, retorna todas as empresas
        if ($user->hasRole('admin')) {
            return \App\Models\Empresa::all();
        }
        
        // Retorna apenas a empresa do usuário
        if ($user->empresa_id) {
            return \App\Models\Empresa::where('id', $user->empresa_id)->get();
        }
        
        return collect();
    }
}
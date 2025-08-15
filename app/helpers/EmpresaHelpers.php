<?php

if (!function_exists('route_financeiro')) {
    /**
     * Gera uma URL para rota financeira com a empresa atual da sessão
     * 
     * @param string $routeName Nome da rota (sem o prefixo comerciantes.empresas.financeiro.)
     * @param array $parameters Parâmetros adicionais para a rota
     * @return string
     */
    function route_financeiro(string $routeName, array $parameters = []): string
    {
        $empresaId = session('empresa_atual_id');
        
        if (!$empresaId) {
            // Se não há empresa selecionada, redireciona para seleção
            return route('comerciantes.empresas.index');
        }

        // Monta o nome completo da rota
        $fullRouteName = 'comerciantes.empresas.financeiro.' . $routeName;
        
        // Adiciona a empresa aos parâmetros
        $parameters = array_merge(['empresa' => $empresaId], $parameters);
        
        return route($fullRouteName, $parameters);
    }
}

if (!function_exists('empresa_atual')) {
    /**
     * Retorna a empresa atual da sessão
     * 
     * @return \App\Comerciantes\Models\Empresa|null
     */
    function empresa_atual(): ?\App\Comerciantes\Models\Empresa
    {
        $empresaId = session('empresa_atual_id');
        
        if (!$empresaId) {
            return null;
        }

        return \App\Comerciantes\Models\Empresa::find($empresaId);
    }
}

if (!function_exists('tem_empresa_selecionada')) {
    /**
     * Verifica se há uma empresa selecionada na sessão
     * 
     * @return bool
     */
    function tem_empresa_selecionada(): bool
    {
        return session()->has('empresa_atual_id') && session('empresa_atual_id') !== null;
    }
}

if (!function_exists('empresas_usuario')) {
    /**
     * Retorna todas as empresas do usuário logado
     * 
     * @return \Illuminate\Support\Collection
     */
    function empresas_usuario(): \Illuminate\Support\Collection
    {
        $user = auth('comerciante')->user();
        
        if (!$user) {
            return collect();
        }

        return $user->todas_empresas ?? collect();
    }
}

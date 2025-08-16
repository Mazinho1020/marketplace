<?php

/**
 * Helpers de configuração para o sistema
 */

if (!function_exists('config_get')) {
    /**
     * Obtém valor de configuração com fallback
     */
    function config_get($key, $default = null)
    {
        return config($key, $default);
    }
}

if (!function_exists('tenant_config')) {
    /**
     * Obtém configuração específica do tenant
     */
    function tenant_config($key, $default = null)
    {
        $tenantId = auth()->user()?->empresa_id ?? null;
        
        if ($tenantId) {
            return config("tenant.{$tenantId}.{$key}", $default);
        }
        
        return config($key, $default);
    }
}

if (!function_exists('is_multitenancy_enabled')) {
    /**
     * Verifica se multitenancy está habilitado
     */
    function is_multitenancy_enabled()
    {
        return config('multitenancy.enabled', false);
    }
}
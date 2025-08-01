<?php

use App\Services\ConfigService;

if (!function_exists('config_get')) {
    /**
     * Obter valor de configuração personalizada
     */
    function config_get(string $key, $default = null, ?int $empresaId = null, ?string $siteId = null, ?string $ambienteId = null)
    {
        return ConfigService::get($key, $default, $empresaId, $siteId, $ambienteId);
    }
}

if (!function_exists('config_set')) {
    /**
     * Definir valor de configuração personalizada
     */
    function config_set(string $key, $value, ?int $empresaId = null, ?string $siteId = null, ?string $ambienteId = null): bool
    {
        return ConfigService::set($key, $value, $empresaId, $siteId, $ambienteId);
    }
}

if (!function_exists('config_group')) {
    /**
     * Obter todas as configurações de um grupo
     */
    function config_group(string $groupCode, ?int $empresaId = null): array
    {
        return ConfigService::getGroup($groupCode, $empresaId);
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Verificar se o sistema está em modo de manutenção
     */
    function is_maintenance_mode(?string $siteId = null): bool
    {
        return config_get('maintenance_mode', false, null, $siteId) === true;
    }
}

if (!function_exists('app_setting')) {
    /**
     * Obter configuração de aplicação
     */
    function app_setting(string $key, $default = null)
    {
        return config_get("app_{$key}", $default);
    }
}

if (!function_exists('empresa_setting')) {
    /**
     * Obter configuração de empresa
     */
    function empresa_setting(string $key, $default = null, ?int $empresaId = null)
    {
        return config_get("empresa_{$key}", $default, $empresaId);
    }
}

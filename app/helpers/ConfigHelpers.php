<?php

/**
 * Helpers de configuração para sistema multi-empresa
 * 
 * Data: 2025-07-31 17:10:28
 * Usuário: Mazinho1020
 */

use App\Services\Config\ConfigManager;

if (!function_exists('empresa_config')) {
    /**
     * Obtém valor de configuração usando o ConfigManager
     * 
     * @param string|null $key Chave da configuração (pode ser aninhada com '.')
     * @param mixed $default Valor padrão se a configuração não existir
     * @return mixed
     */
    function empresa_config($key = null, $default = null)
    {
        return ConfigManager::getInstance()->get($key, $default);
    }
}

if (!function_exists('set_empresa_config')) {
    /**
     * Define valor de configuração
     * 
     * @param string $key Chave da configuração
     * @param mixed $value Valor da configuração
     * @param bool $save_db Se deve salvar no banco de dados
     * @return bool
     */
    function set_empresa_config($key, $value, $save_db = false)
    {
        $config = ConfigManager::getInstance();
        $config->set($key, $value);

        if ($save_db) {
            return $config->saveToDatabase($key, $value);
        }

        return true;
    }
}

if (!function_exists('is_online_mode')) {
    /**
     * Verifica se está em modo online (acessando base finanp06_*)
     * 
     * @return bool
     */
    function is_online_mode()
    {
        return ConfigManager::getInstance()->isOnlineMode();
    }
}

if (!function_exists('is_offline_mode')) {
    /**
     * Verifica se está em modo offline (base local)
     * 
     * @return bool
     */
    function is_offline_mode()
    {
        return !ConfigManager::getInstance()->isOnlineMode();
    }
}

if (!function_exists('current_empresa_id')) {
    /**
     * Obtém ID da empresa atual
     * 
     * @return int|null
     */
    function current_empresa_id()
    {
        return ConfigManager::getInstance()->getCurrentEmpresaId();
    }
}

if (!function_exists('switch_empresa')) {
    /**
     * Altera empresa atual
     * 
     * @param int $empresaId
     * @return ConfigManager
     */
    function switch_empresa($empresaId)
    {
        return ConfigManager::getInstance()->setCurrentEmpresaId($empresaId);
    }
}

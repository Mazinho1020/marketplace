<?php

/**
 * Função helper para acessar configurações do sistema (empresa desenvolvedora)
 * 
 * @param string|null $key Chave de configuração
 * @param mixed $default Valor padrão
 * @return mixed ConfigManager ou valor da configuração
 */
function system_config($key = null, $default = null)
{
    $manager = \App\Services\Config\ConfigManager::getInstance();

    // Salvar contexto atual
    $currentContext = $manager->getCurrentContext();

    // Alternar para contexto da desenvolvedora
    $manager->useDeveloperContext();

    // Obter valor
    $value = ($key === null) ? $manager : $manager->get($key, $default);

    // Restaurar contexto anterior
    $manager->setContext(
        $currentContext['empresa_id'],
        $currentContext['site_id'],
        $currentContext['environment_id']
    );

    return $value;
}

/**
 * Função helper para acessar configurações de um cliente específico
 * 
 * @param int $clientEmpresaId ID da empresa cliente
 * @param string|null $key Chave específica (opcional)
 * @return mixed Array de configurações ou valor específico
 */
function client_config(int $clientEmpresaId, string $key = null)
{
    $clientConfig = \App\Services\Config\ConfigManager::getInstance()->getClientConfig($clientEmpresaId);

    if ($key !== null) {
        return $clientConfig[$key] ?? null;
    }

    return $clientConfig;
}

/**
 * Verifica se o usuário atual é da empresa desenvolvedora
 * 
 * @return bool
 */
function is_developer_company(): bool
{
    return \App\Services\Config\ConfigManager::getInstance()->isDeveloperCompany();
}

/**
 * Verifica se um cliente específico tem licença ativa
 * 
 * @param int $clientEmpresaId ID da empresa cliente
 * @return bool
 */
function client_is_active(int $clientEmpresaId): bool
{
    return \App\Services\Config\ConfigManager::getInstance()->isClientActive($clientEmpresaId);
}

<?php

use App\Services\Config\ConfigManager;

// Inicializar ConfigManager
$config = ConfigManager::getInstance();

// Detectar automaticamente o contexto (empresa, site, ambiente)
// A detecção é feita no construtor, então não precisa fazer nada aqui

// Definir constantes baseadas nas configurações
if (!defined('APP_NAME')) {
    define('APP_NAME', $config->get('app_name', 'Sistema'));
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', (bool)$config->get('app_debug', false));
}

// Configurações específicas para cliente ou desenvolvedor
if ($config->isDeveloperCompany()) {
    // Configurações específicas para empresa desenvolvedora
    define('IS_DEVELOPER', true);
    define('MAX_CLIENTES', (int)$config->get('max_clientes', 100));
} else {
    // Configurações específicas para clientes
    define('IS_DEVELOPER', false);
    define('CLIENTE_TIPO', $config->get('cliente_tipo', 'Cliente'));
}

return [
    'initialized' => true,
    'context' => $config->getCurrentContext()
];

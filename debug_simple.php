<?php

echo "Iniciando teste...\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "Autoload carregado\n";

    $service = \App\Services\Database\DatabaseEnvironmentService::getInstance();
    echo "Service criado com sucesso\n";

    echo "Ambiente detectado: " . $service->getCurrentEnvironment() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

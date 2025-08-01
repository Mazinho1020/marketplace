<?php

echo "=== DEBUG PASSO A PASSO ===\n";

try {
    echo "1. Carregando autoload...\n";
    require_once __DIR__ . '/vendor/autoload.php';
    echo "   ✓ Autoload OK\n";

    echo "2. Testando classe exists...\n";
    if (class_exists('App\Services\Database\DatabaseEnvironmentService')) {
        echo "   ✓ Classe existe\n";
    } else {
        echo "   ✗ Classe não encontrada\n";
        exit(1);
    }

    echo "3. Tentando criar instância...\n";
    $service = \App\Services\Database\DatabaseEnvironmentService::getInstance();
    echo "   ✓ Instância criada\n";

    echo "4. Testando método...\n";
    $env = $service->getCurrentEnvironment();
    echo "   ✓ Ambiente: $env\n";

    echo "5. Testando config...\n";
    $config = $service->getConfig();
    if ($config) {
        echo "   ✓ Config carregada: {$config['database']} @ {$config['host']}\n";
    } else {
        echo "   - Config padrão\n";
    }

    echo "=== SUCESSO ===\n";
} catch (Error $e) {
    echo "ERRO PHP: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} catch (Exception $e) {
    echo "EXCEÇÃO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

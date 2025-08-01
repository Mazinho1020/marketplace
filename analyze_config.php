<?php

echo "=== ANÁLISE DE CONFIGURAÇÃO ===\n";

// Simular valores de ambiente
$appEnvValues = ['local', 'development', 'production'];

foreach ($appEnvValues as $appEnv) {
    echo "\n--- APP_ENV = {$appEnv} ---\n";

    if (in_array($appEnv, ['local', 'development', 'dev'])) {
        echo "✅ Detectado como DESENVOLVIMENTO\n";
        echo "  Host: 127.0.0.1\n";
        echo "  Database: meufinanceiro\n";
        echo "  Username: root\n";
        echo "  Password: (vazio)\n";
    } else {
        echo "⚠️  Detectado como PRODUÇÃO/OUTROS\n";
        echo "  Usará valores do .env\n";
    }
}

echo "\n=== VERIFICAÇÃO ATUAL ===\n";

// Ler .env manualmente
$envContent = file_get_contents(__DIR__ . '/.env');
$lines = explode("\n", $envContent);
$envVars = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (!empty($line) && !str_starts_with($line, '#') && str_contains($line, '=')) {
        [$key, $value] = explode('=', $line, 2);
        $envVars[trim($key)] = trim($value, '"');
    }
}

$currentAppEnv = $envVars['APP_ENV'] ?? 'local';
echo "APP_ENV atual no .env: {$currentAppEnv}\n";

if (in_array($currentAppEnv, ['local', 'development', 'dev'])) {
    echo "✅ CONFIGURAÇÃO ESPERADA: Banco Local (meufinanceiro)\n";
} else {
    echo "⚠️  CONFIGURAÇÃO ESPERADA: Banco do .env\n";
}

echo "\nVarıáveis .env relacionadas ao banco:\n";
foreach (['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME'] as $key) {
    $value = $envVars[$key] ?? 'não definido';
    echo "  {$key}: {$value}\n";
}

echo "\n=== FIM DA ANÁLISE ===\n";

<?php

/**
 * Teste final das páginas administrativas de fidelidade
 * Verifica se todas as páginas carregam sem erros
 */

require_once 'vendor/autoload.php';

$urls = [
    'http://127.0.0.1:8000/admin/fidelidade/',
    'http://127.0.0.1:8000/admin/fidelidade/clientes',
    'http://127.0.0.1:8000/admin/fidelidade/transacoes',
    'http://127.0.0.1:8000/admin/fidelidade/cupons',
    'http://127.0.0.1:8000/admin/fidelidade/cashback',
    'http://127.0.0.1:8000/admin/fidelidade/relatorios'
];

echo "🧪 Testando páginas administrativas de fidelidade...\n\n";

foreach ($urls as $url) {
    echo "Testando: $url\n";

    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        echo "❌ ERRO: Não foi possível acessar a URL\n";
    } else {
        // Verificar se há erros PHP na resposta
        if (
            strpos($response, 'ErrorException') !== false ||
            strpos($response, 'Undefined array key') !== false ||
            strpos($response, 'Call to undefined') !== false ||
            strpos($response, 'Route [admin.fidelidade.dashboard] not defined') !== false
        ) {
            echo "❌ ERRO: Encontrados erros PHP na página\n";
        } else {
            echo "✅ OK: Página carregou sem erros\n";
        }
    }
    echo "\n";
}

echo "🎯 Teste administrativo concluído!\n";

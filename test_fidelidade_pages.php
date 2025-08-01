<?php

/**
 * Teste para verificar se todas as páginas de fidelidade estão funcionando
 * sem erros de chaves indefinidas ou outros problemas
 */

require_once 'vendor/autoload.php';

$urls = [
    'http://127.0.0.1:8000/admin/fidelidade/cupons',
    'http://127.0.0.1:8000/admin/fidelidade/transacoes',
    'http://127.0.0.1:8000/admin/fidelidade/cartoes',
    'http://127.0.0.1:8000/admin/fidelidade/cashback',
    'http://127.0.0.1:8000/admin/fidelidade/clientes'
];

echo "🧪 Testando páginas de fidelidade...\n\n";

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
            strpos($response, 'Call to undefined') !== false
        ) {
            echo "❌ ERRO: Encontrados erros PHP na página\n";
        } else {
            echo "✅ OK: Página carregou sem erros\n";
        }
    }
    echo "\n";
}

echo "🎯 Teste concluído!\n";

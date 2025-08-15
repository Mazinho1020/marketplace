<?php

// Teste da API de formas de pagamento
echo "=== TESTE DIRETO DA API ===\n";

$url = 'http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/api/formas-pagamento';
echo "URL: {$url}\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Accept: application/json',
            'User-Agent: PHP-Test-Script'
        ],
        'timeout' => 10
    ]
]);

$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo "❌ Erro: Não foi possível acessar a URL\n";
    echo "Headers de resposta:\n";
    print_r($http_response_header ?? []);
} else {
    echo "✅ Resposta recebida:\n";
    echo "Headers:\n";
    foreach ($http_response_header as $header) {
        echo "  {$header}\n";
    }
    echo "\nConteúdo:\n";

    $data = json_decode($result, true);
    if ($data !== null) {
        echo "📋 JSON válido recebido:\n";
        foreach ($data as $index => $forma) {
            echo sprintf(
                "  %d. ID: %s | Nome: %s | Gateway: %s\n",
                $index + 1,
                $forma['id'],
                $forma['nome'],
                $forma['gateway_method'] ?? 'N/A'
            );
            if ($index >= 9) { // Mostrar apenas os primeiros 10
                echo "  ... e mais " . (count($data) - 10) . " itens\n";
                break;
            }
        }
        echo "\n✅ Total: " . count($data) . " formas de pagamento\n";
    } else {
        echo "❌ Resposta não é JSON válido:\n";
        echo $result . "\n";
    }
}

echo "\n=== TESTE CONCLUÍDO ===\n";

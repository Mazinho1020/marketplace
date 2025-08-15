<?php

// Teste da API de formas de pagamento
$url = 'http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/api/formas-pagamento';

echo "=== TESTANDO API DE FORMAS DE PAGAMENTO ===\n";
echo "URL: {$url}\n\n";

// Inicializar cURL
$ch = curl_init();

// Configurar opções
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

// Executar requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// Mostrar resultado
echo "Status HTTP: {$httpCode}\n";

if ($error) {
    echo "❌ Erro cURL: {$error}\n";
} else {
    echo "✅ Resposta recebida\n";
    echo "Conteúdo:\n";

    // Tentar decodificar JSON
    $data = json_decode($response, true);
    if ($data !== null) {
        echo "📋 Formas de pagamento disponíveis:\n";
        foreach ($data as $forma) {
            echo "  • ID: {$forma['id']} | Nome: {$forma['nome']} | Gateway: " . ($forma['gateway_method'] ?? 'N/A') . "\n";
        }
        echo "\n✅ Total: " . count($data) . " formas de pagamento\n";
    } else {
        echo "Response (raw):\n{$response}\n";
    }
}

echo "\n=== TESTE CONCLUÍDO ===\n";

<?php

// Teste direto da API de bandeiras via cURL

echo "🌐 TESTE DIRETO DA API DE BANDEIRAS\n";
echo "===================================\n\n";

function testApi($url, $description)
{
    echo "🧪 Testando: {$description}\n";
    echo "URL: {$url}\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "Status: {$httpCode}\n";

    if ($error) {
        echo "❌ Erro cURL: {$error}\n";
        return;
    }

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (is_array($data)) {
            echo "✅ Sucesso! " . count($data) . " items retornados\n";

            // Mostrar primeiros 3 items
            foreach (array_slice($data, 0, 3) as $item) {
                if (isset($item['nome'])) {
                    $extra = isset($item['taxa']) ? " (Taxa: {$item['taxa']}%)" : "";
                    echo "   - {$item['nome']}{$extra}\n";
                }
            }

            if (count($data) > 3) {
                echo "   ... e mais " . (count($data) - 3) . " items\n";
            }
        } else {
            echo "❌ Resposta não é um array válido\n";
            echo "Response: " . substr($response, 0, 200) . "\n";
        }
    } else {
        echo "❌ Erro HTTP {$httpCode}\n";
        echo "Response: " . substr($response, 0, 200) . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

$baseUrl = "http://127.0.0.1:8000";
$empresaId = 1;

// 1. Testar API de formas de pagamento
testApi(
    "{$baseUrl}/comerciantes/empresas/{$empresaId}/financeiro/api/formas-pagamento",
    "API de Formas de Pagamento"
);

// 2. Testar API de bandeiras para Boleto Bancário (ID: 26)
testApi(
    "{$baseUrl}/comerciantes/empresas/{$empresaId}/financeiro/api/formas-pagamento/26/bandeiras",
    "API de Bandeiras - Boleto Bancário (ID: 26)"
);

// 3. Testar API de bandeiras para PIX (ID: 21)
testApi(
    "{$baseUrl}/comerciantes/empresas/{$empresaId}/financeiro/api/formas-pagamento/21/bandeiras",
    "API de Bandeiras - PIX (ID: 21)"
);

// 4. Testar API de bandeiras para Cartão de Crédito (ID: 24)
testApi(
    "{$baseUrl}/comerciantes/empresas/{$empresaId}/financeiro/api/formas-pagamento/24/bandeiras",
    "API de Bandeiras - Cartão de Crédito (ID: 24)"
);

echo "🎯 TESTE CONCLUÍDO!\n";

<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE CURL PAGAMENTO ===\n";

// Dados do pagamento
$postData = [
    'forma_pagamento_id' => 1,
    'bandeira_id' => null,
    'valor' => 300.00,
    'data_pagamento' => date('Y-m-d'),
    'data_compensacao' => null,
    'valor_principal' => 300.00,
    'valor_juros' => 0,
    'valor_multa' => 0,
    'valor_desconto' => 0,
    'conta_bancaria_id' => 1,
    'taxa' => 0,
    'valor_taxa' => 0,
    'observacao' => 'Teste de pagamento via script',
    'referencia_externa' => null
];

// URL do endpoint
$url = 'http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-pagar/377/pagamentos';

// Configurar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

// Executar requisição
echo "📡 Enviando requisição para: $url\n";
echo "📤 Dados enviados:\n";
foreach ($postData as $key => $value) {
    echo "   $key: $value\n";
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "\n=== RESPOSTA ===\n";
echo "🔗 HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ Erro cURL: $error\n";
} else {
    echo "📥 Resposta:\n";
    echo $response . "\n";

    // Tentar decodificar JSON
    $jsonResponse = json_decode($response, true);
    if ($jsonResponse) {
        echo "\n📊 JSON decodificado:\n";
        foreach ($jsonResponse as $key => $value) {
            if (is_array($value)) {
                echo "   $key: " . json_encode($value) . "\n";
            } else {
                echo "   $key: $value\n";
            }
        }
    }
}

echo "\n=== FIM TESTE ===\n";

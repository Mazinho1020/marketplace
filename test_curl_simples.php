<?php

echo "🌐 Teste CURL simples para pagamento\n\n";

// Dados do pagamento
$dados = [
    '_token' => 'test-token', // Vamos usar um token fake primeiro
    'forma_pagamento_id' => 6,
    'conta_bancaria_id' => 1,
    'valor' => 300.00,
    'data_pagamento' => '2025-08-14',
    'valor_principal' => 300.00,
    'valor_juros' => 0,
    'valor_multa' => 0,
    'valor_desconto' => 0,
    'taxa' => 0,
    'valor_taxa' => 0,
];

$url = 'http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-pagar/377/pagamentos';

echo "📤 Enviando para: {$url}\n";
echo "📋 Dados:\n";
foreach ($dados as $key => $value) {
    echo "   {$key}: {$value}\n";
}
echo "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dados));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

if (curl_error($ch)) {
    echo "❌ Erro CURL: " . curl_error($ch) . "\n";
    exit;
}

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "📡 Status HTTP: {$httpCode}\n\n";
echo "📋 Headers:\n";
echo $headers . "\n";
echo "📄 Body:\n";
echo $body . "\n\n";

// Tentar decodificar como JSON
if ($httpCode == 200) {
    $data = json_decode($body, true);
    if ($data) {
        echo "✅ Resposta JSON válida\n";
        if (isset($data['success'])) {
            echo "   Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        }
        if (isset($data['message'])) {
            echo "   Message: {$data['message']}\n";
        }
    } else {
        echo "❌ Resposta não é JSON válido\n";
    }
} else {
    echo "❌ Código de erro HTTP: {$httpCode}\n";
    if (strpos($body, 'TokenMismatchException') !== false) {
        echo "🔐 Erro de CSRF Token detectado\n";
    }
    if (strpos($body, 'MethodNotAllowedHttpException') !== false) {
        echo "🚫 Método não permitido\n";
    }
    if (strpos($body, 'RouteNotFoundException') !== false) {
        echo "🗺️ Rota não encontrada\n";
    }
}

echo "\n✅ Teste CURL concluído\n";

<?php

echo "🧪 Teste de Recebimento via Interface Web\n";
echo "==========================================\n\n";

// Simular requisição POST via cURL
$url = 'http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-receber/392/recebimentos';

$dados = [
    '_token' => 'test-token', // Token fictício para teste
    'valor' => '100',
    'data_recebimento' => '2025-08-14',
    'forma_pagamento_id' => '25',
    'bandeira_id' => '35',
    'conta_bancaria_id' => '2',
    'valor_principal' => '100',
    'valor_juros' => '0',
    'valor_multa' => '0',
    'valor_desconto' => '0',
    'observacao' => 'Teste via cURL'
];

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query($dados),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ],
]);

echo "🚀 Enviando requisição para: $url\n";
echo "📝 Dados: " . json_encode($dados, JSON_PRETTY_PRINT) . "\n\n";

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

echo "📥 Código HTTP: $httpCode\n";
echo "📄 Resposta:\n";

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if ($responseData) {
        echo "✅ Sucesso!\n";
        echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "⚠️ Resposta não é JSON válido:\n";
        echo $response . "\n";
    }
} else {
    echo "❌ Erro HTTP $httpCode:\n";
    echo $response . "\n";
}

echo "\n🏁 Teste finalizado!\n";

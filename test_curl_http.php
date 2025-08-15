<?php

echo "🌐 Teste de requisição HTTP direta ao endpoint\n\n";

$url = 'http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-pagar/377/pagamentos';

$dados = [
    'forma_pagamento_id' => 6,
    'bandeira_id' => null,
    'conta_bancaria_id' => 1,
    'valor' => 300.00,
    'valor_principal' => 300.00,
    'valor_juros' => 0,
    'valor_multa' => 0,
    'valor_desconto' => 0,
    'data_pagamento' => '2025-08-14',
    'data_compensacao' => null,
    'observacao' => 'Teste via CURL HTTP',
    'comprovante_pagamento' => null,
    'taxa' => 0,
    'valor_taxa' => 0,
    'referencia_externa' => null,
];

// Primeiro, vamos obter um token CSRF válido fazendo uma requisição GET
echo "🔐 Obtendo token CSRF...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-pagar/377');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "❌ Erro CURL: " . curl_error($ch) . "\n";
    exit;
}

echo "📡 Status da página principal: {$httpCode}\n";

// Extrair cookies e token CSRF
preg_match_all('/Set-Cookie:\s*([^;]+)/i', $response, $matches);
$cookies = [];
foreach ($matches[1] as $cookie) {
    $parts = explode('=', $cookie, 2);
    if (count($parts) == 2) {
        $cookies[$parts[0]] = $parts[1];
    }
}

// Tentar extrair token CSRF do HTML
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $csrfMatches);
$csrfToken = isset($csrfMatches[1]) ? $csrfMatches[1] : 'fake-token';

echo "🍪 Cookies encontrados: " . count($cookies) . "\n";
echo "🔑 CSRF Token: " . substr($csrfToken, 0, 20) . "...\n\n";

curl_close($ch);

// Agora fazer a requisição POST
echo "📤 Enviando requisição POST...\n";

$dados['_token'] = $csrfToken;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dados));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Adicionar cookies
$cookieString = '';
foreach ($cookies as $name => $value) {
    $cookieString .= "{$name}={$value}; ";
}
if ($cookieString) {
    curl_setopt($ch, CURLOPT_COOKIE, rtrim($cookieString, '; '));
}

// Headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-CSRF-TOKEN: ' . $csrfToken,
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

if (curl_error($ch)) {
    echo "❌ Erro CURL: " . curl_error($ch) . "\n";
    exit;
}

echo "📡 Status da resposta: {$httpCode}\n";
echo "📄 Content-Type: {$contentType}\n\n";

// Separar headers e body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "📋 Headers da resposta:\n";
echo $headers . "\n";

echo "📄 Body da resposta:\n";
echo $body . "\n\n";

// Tentar decodificar JSON
$data = json_decode($body, true);
if ($data) {
    echo "✅ Resposta JSON válida:\n";
    echo "   Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    if (isset($data['message'])) {
        echo "   Message: {$data['message']}\n";
    }
    if (isset($data['pagamento']['id'])) {
        echo "   Pagamento ID: {$data['pagamento']['id']}\n";
    }
} else {
    echo "❌ Resposta não é JSON válido\n";
    if ($httpCode >= 400) {
        echo "🚨 Possível erro do servidor\n";
    }
}

echo "\n✅ Teste HTTP concluído\n";

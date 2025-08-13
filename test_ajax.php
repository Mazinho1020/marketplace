<?php

echo "Testando AJAX...";

// Simular uma requisição GET
$data = [
    'produto_id' => 8,
    'ajax' => 1
];

$url = 'http://127.0.0.1:8000/comerciantes/produtos/precos-quantidade?' . http_build_query($data);

echo "\nURL: " . $url . "\n";

// Usar cURL para fazer a requisição
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

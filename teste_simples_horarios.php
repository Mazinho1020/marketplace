<?php
// Teste simples e direto
$url = 'http://localhost:8000/comerciantes/horarios';

echo "Testando: $url\n\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'GET'
    ]
]);

$result = @file_get_contents($url, false, $context);

if ($result === false) {
    echo "❌ Erro ao acessar a URL\n";

    if (isset($http_response_header)) {
        echo "Headers de resposta:\n";
        foreach ($http_response_header as $header) {
            echo "  $header\n";
        }
    }
} else {
    echo "✅ Sucesso! Resposta recebida:\n";
    echo "─────────────────────────\n";
    echo substr($result, 0, 500); // Primeiros 500 caracteres
    echo "\n─────────────────────────\n";
}

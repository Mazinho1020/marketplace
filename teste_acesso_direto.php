<?php

echo "TESTE SIMPLES DE ACESSO À ROTA\n";
echo "================================\n\n";

// Verificar se conseguimos fazer uma requisição para a rota
$url = 'http://localhost:8000/comerciantes/empresas/1/horarios';

echo "Fazendo requisição para: $url\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'follow_location' => 0, // Não seguir redirects
    ]
]);

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERRO: Não foi possível acessar a URL\n";
    echo "Detalhes do erro:\n";
    print_r(error_get_last());
} else {
    echo "Resposta recebida!\n";
    echo "Headers de resposta:\n";
    print_r($http_response_header);
    echo "\nPrimeiros 300 caracteres da resposta:\n";
    echo substr($response, 0, 300) . "...\n";
}

echo "\n=== FIM TESTE ===\n";

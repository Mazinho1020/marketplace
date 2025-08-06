<?php

echo "TESTE DE AUTENTICAÇÃO E ACESSO\n";
echo "===============================\n\n";

$loginUrl = 'http://localhost:8000/comerciantes/login';
$horarios = 'http://localhost:8000/comerciantes/empresas/1/horarios';

// Primeiro, verificar se podemos acessar a página de login
echo "1. Testando acesso à página de login...\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'follow_location' => 0,
    ]
]);

$loginPage = @file_get_contents($loginUrl, false, $context);

if ($loginPage === false) {
    echo "ERRO: Não foi possível acessar a página de login\n";
    exit;
}

echo "✓ Página de login acessível\n\n";

// Verificar se há redirecionamento quando tentamos acessar horarios sem login
echo "2. Testando acesso aos horários sem login...\n";

$response = @file_get_contents($horarios, false, $context);

if ($response === false) {
    echo "ERRO: Não foi possível acessar a página\n";
    echo "Headers de resposta:\n";
    if (isset($http_response_header)) {
        print_r($http_response_header);
    }
} else {
    echo "Resposta recebida!\n";
    echo "Headers de resposta:\n";
    print_r($http_response_header);

    // Verificar se é um redirect
    foreach ($http_response_header as $header) {
        if (stripos($header, 'HTTP/') === 0) {
            $parts = explode(' ', $header);
            $statusCode = $parts[1];
            echo "Status Code: $statusCode\n";

            if ($statusCode >= 300 && $statusCode < 400) {
                echo "REDIRECT DETECTADO!\n";
            }
        }
        if (stripos($header, 'Location:') === 0) {
            echo "Redirect para: " . substr($header, 10) . "\n";
        }
    }
}

echo "\n=== FIM TESTE ===\n";

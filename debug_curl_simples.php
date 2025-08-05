<?php

echo "=== DEBUG SIMPLES DO REDIRECIONAMENTO ===\n\n";

// Teste usando cURL para simular o browser
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/comerciantes/horarios');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Não seguir redirecionamentos
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

curl_close($ch);

echo "🌐 TESTE DIRETO DA URL:\n";
echo "   URL testada: http://localhost:8000/comerciantes/horarios\n";
echo "   Status HTTP: $httpCode\n";
echo "   URL efetiva: $effectiveUrl\n\n";

if ($httpCode === 302) {
    echo "❌ REDIRECIONAMENTO 302 CONFIRMADO!\n\n";

    // Extrair cabeçalho Location
    $headers = [];
    $headerLines = explode("\n", $response);
    foreach ($headerLines as $line) {
        if (strpos($line, ':') !== false) {
            [$key, $value] = explode(':', $line, 2);
            $headers[trim($key)] = trim($value);
        }
    }

    if (isset($headers['Location'])) {
        echo "🔍 REDIRECIONANDO PARA: " . $headers['Location'] . "\n\n";

        if (str_contains($headers['Location'], 'login')) {
            echo "💡 CAUSA: Middleware de autenticação\n";
            echo "   O usuário não está logado ou a sessão expirou\n";
            echo "   SOLUÇÃO: Fazer login primeiro\n\n";
        } elseif (str_contains($headers['Location'], 'empresas')) {
            echo "💡 CAUSA: Sistema de seleção de empresa\n";
            echo "   O sistema está forçando seleção de empresa\n";
            echo "   SOLUÇÃO: Modificar lógica do controller\n\n";
        } else {
            echo "💡 CAUSA: Redirecionamento personalizado\n";
            echo "   Verificar lógica específica\n\n";
        }
    }

    echo "🔧 AÇÕES PARA RESOLVER:\n";
    echo "1. Verificar se middleware auth:comerciante está funcionando\n";
    echo "2. Verificar se há redirecionamento no controller\n";
    echo "3. Verificar se há JavaScript causando redirecionamento\n";
    echo "4. Verificar configuração de rotas\n\n";
} elseif ($httpCode === 200) {
    echo "✅ PÁGINA CARREGOU COM SUCESSO!\n";
    echo "   O problema pode estar no browser/cache\n\n";
} else {
    echo "❌ ERRO DIFERENTE: HTTP $httpCode\n";
    echo "   Investigar erro específico\n\n";
}

echo "🎯 PRÓXIMOS PASSOS:\n";
echo "1. Testar login manual no browser\n";
echo "2. Verificar se session está funcionando\n";
echo "3. Verificar logs do Laravel\n";
echo "4. Testar em aba privada\n";

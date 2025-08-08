<?php

echo "=== TESTE FINAL DE REDIRECIONAMENTO ===\n\n";

$url = "http://localhost:8000/comerciantes/empresas/1/usuarios";

echo "🔍 Testando URL: $url\n\n";

// Teste usando cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

curl_close($ch);

echo "📊 RESULTADO:\n";
echo "   Status HTTP: $httpCode\n";

if ($httpCode === 302) {
    echo "   ✅ Redirecionamento detectado!\n";

    // Extrair Location do cabeçalho
    if (preg_match('/Location:\s*(.+)/i', $response, $matches)) {
        $location = trim($matches[1]);
        echo "   📍 Redirecionando para: $location\n";

        if (strpos($location, 'comerciantes/login') !== false) {
            echo "   🎉 SUCESSO! Redirecionando para login de comerciantes!\n";
        } elseif (strpos($location, '/login') !== false) {
            echo "   ❌ PROBLEMA! Ainda redirecionando para login de admin!\n";
        } else {
            echo "   ⚠️ Redirecionamento inesperado\n";
        }
    } else {
        echo "   ⚠️ Não foi possível detectar o destino do redirecionamento\n";
    }
} elseif ($httpCode === 200) {
    echo "   ⚠️ Página carregou sem redirecionamento (usuário pode estar logado)\n";
} else {
    echo "   ❌ Status inesperado: $httpCode\n";
}

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "   1. Se aparecer 'SUCESSO', o problema foi resolvido!\n";
echo "   2. Se aparecer 'PROBLEMA', precisamos investigar mais\n";
echo "   3. Teste manualmente abrindo a URL no navegador\n";

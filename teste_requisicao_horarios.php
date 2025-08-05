<?php
echo "=== TESTE DE REQUISIÇÃO HTTP ===\n\n";

// Inicializar cURL
$ch = curl_init();

// Configurar cURL
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/comerciantes/horarios');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // NÃO seguir redirecionamentos
curl_setopt($ch, CURLOPT_HEADER, true); // Incluir headers na resposta
curl_setopt($ch, CURLOPT_NOBODY, false); // Incluir corpo da resposta
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Executar requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

// Verificar se houve erro
if (curl_error($ch)) {
    echo "❌ Erro cURL: " . curl_error($ch) . "\n";
} else {
    echo "✅ Requisição executada com sucesso!\n\n";

    echo "📊 STATUS HTTP: $httpCode\n";

    if ($httpCode == 302) {
        echo "🔄 REDIRECIONAMENTO DETECTADO!\n";
        echo "   Para: $redirectUrl\n\n";
    }

    echo "📋 RESPOSTA COMPLETA:\n";
    echo "─────────────────────────────────\n";
    echo $response;
    echo "\n─────────────────────────────────\n";
}

curl_close($ch);

echo "\n💡 ANÁLISE:\n";
if ($httpCode == 302) {
    echo "   • Há um redirecionamento HTTP 302\n";
    echo "   • O problema NÃO está no controller\n";
    echo "   • Provavelmente há middleware interceptando\n";
} elseif ($httpCode == 200) {
    echo "   • A requisição funcionou!\n";
    echo "   • O controller está respondendo corretamente\n";
} else {
    echo "   • Status HTTP inesperado: $httpCode\n";
}

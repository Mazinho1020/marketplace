<?php
echo "\n🔍 TESTE DA ROTA HEADER NOTIFICAÇÕES 🔍\n\n";

$url = 'http://127.0.0.1:8000/comerciantes/notificacoes/header';

// Fazer request com cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📡 Testando URL: $url\n";
echo "📊 HTTP Code: $httpCode\n\n";

if ($httpCode == 200) {
    echo "✅ Status OK!\n";

    // Verificar se é JSON válido
    $json = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ JSON válido!\n";
        echo "📋 Estrutura:\n";

        if (isset($json['success'])) {
            echo "   - success: " . ($json['success'] ? 'true' : 'false') . "\n";
        }

        if (isset($json['notificacoes'])) {
            echo "   - notificacoes: " . count($json['notificacoes']) . " itens\n";
        }

        if (isset($json['total_nao_lidas'])) {
            echo "   - total_nao_lidas: " . $json['total_nao_lidas'] . "\n";
        }

        echo "\n📝 Primeira notificação (se existe):\n";
        if (!empty($json['notificacoes'])) {
            $primeira = $json['notificacoes'][0];
            foreach ($primeira as $key => $value) {
                echo "   - $key: " . (is_string($value) ? substr($value, 0, 50) . '...' : $value) . "\n";
            }
        }
    } else {
        echo "❌ JSON inválido!\n";
        echo "Erro: " . json_last_error_msg() . "\n";
        echo "Resposta: " . substr($response, 0, 200) . "...\n";
    }
} else {
    echo "❌ Erro HTTP: $httpCode\n";
    echo "Resposta: " . substr($response, 0, 500) . "...\n";
}

echo "\n🎯 RESULTADO FINAL:\n";
if ($httpCode == 200 && json_last_error() === JSON_ERROR_NONE) {
    echo "✅ Rota funcionando perfeitamente!\n";
} else {
    echo "❌ Rota com problemas - verificar logs do Laravel\n";
}

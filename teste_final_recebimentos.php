<?php

// Teste final do sistema de recebimentos via HTTP

echo "🧪 TESTE FINAL DO SISTEMA DE RECEBIMENTOS\n";
echo "=========================================\n\n";

$baseUrl = "http://127.0.0.1:8000";
$empresaId = 1;

// Função para fazer requisições HTTP
function makeRequest($url, $method = 'GET', $data = null)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'body' => $response
    ];
}

try {

    // 1. Testar API de formas de pagamento para recebimento
    echo "📋 1. Testando API de formas de pagamento:\n";

    $url1 = "{$baseUrl}/comerciantes/empresas/{$empresaId}/financeiro/api/formas-pagamento";
    $response1 = makeRequest($url1);

    echo "   URL: {$url1}\n";
    echo "   Status: {$response1['status']}\n";

    if ($response1['status'] === 200) {
        $formas = json_decode($response1['body'], true);
        if (is_array($formas)) {
            echo "   ✅ {$count} formas de pagamento carregadas:\n";
            $count = count($formas);

            foreach (array_slice($formas, 0, 5) as $forma) {
                echo "      - {$forma['nome']} (ID: {$forma['id']})\n";
            }

            if ($count > 5) {
                echo "      ... e mais " . ($count - 5) . " formas\n";
            }

            // 2. Testar API de bandeiras para uma forma específica
            if ($count > 0) {
                $primeiraForma = $formas[0];
                echo "\n🏷️ 2. Testando API de bandeiras para '{$primeiraForma['nome']}':\n";

                $url2 = "{$baseUrl}/comerciantes/empresas/{$empresaId}/financeiro/api/formas-pagamento/{$primeiraForma['id']}/bandeiras";
                $response2 = makeRequest($url2);

                echo "   URL: {$url2}\n";
                echo "   Status: {$response2['status']}\n";

                if ($response2['status'] === 200) {
                    $bandeiras = json_decode($response2['body'], true);
                    if (is_array($bandeiras)) {
                        $bandeirasCount = count($bandeiras);
                        echo "   ✅ {$bandeirasCount} bandeiras carregadas:\n";

                        foreach ($bandeiras as $bandeira) {
                            echo "      🏷️ {$bandeira['nome']} - Taxa: {$bandeira['taxa']}% - {$bandeira['dias_para_receber']} dias\n";
                        }
                    } else {
                        echo "   ❌ Resposta inválida para bandeiras\n";
                    }
                } else {
                    echo "   ❌ Erro na API de bandeiras: {$response2['body']}\n";
                }
            }
        } else {
            echo "   ❌ Resposta inválida: não é um array\n";
        }
    } else {
        echo "   ❌ Erro na API: {$response1['body']}\n";
    }

    // 3. Resumo do teste
    echo "\n📊 3. Resumo do teste:\n";

    $statusApis = [
        'API Formas de Pagamento' => $response1['status'] === 200,
        'API Bandeiras' => isset($response2) && $response2['status'] === 200,
        'JSON Válido' => isset($formas) && is_array($formas) && count($formas) > 0,
        'Bandeiras Disponíveis' => isset($bandeiras) && is_array($bandeiras) && count($bandeiras) > 0
    ];

    foreach ($statusApis as $teste => $ok) {
        $icon = $ok ? '✅' : '❌';
        echo "   {$icon} {$teste}\n";
    }

    if (array_filter($statusApis) === $statusApis) {
        echo "\n🎉 SISTEMA TOTALMENTE FUNCIONAL!\n";
        echo "   O modal de recebimento está pronto para uso.\n";
        echo "   As APIs estão respondendo corretamente.\n";
        echo "   Bandeiras estão sendo carregadas dinamicamente.\n";

        echo "\n🚀 Próximos passos:\n";
        echo "   1. Acessar http://127.0.0.1:8000 no navegador\n";
        echo "   2. Navegar para uma conta a receber\n";
        echo "   3. Clicar em 'Registrar Recebimento'\n";
        echo "   4. Testar o modal completo\n";
    } else {
        echo "\n⚠️ Sistema precisa de ajustes.\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

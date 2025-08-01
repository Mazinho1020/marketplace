<?php

/**
 * Teste rápido dos links do dashboard
 * Verifica se as rotas estão respondendo corretamente
 */

echo "=== TESTE DOS LINKS DO DASHBOARD ===\n\n";

// Lista de URLs para testar
$urls = [
    '/admin/dashboard' => 'Dashboard Principal',
    '/admin/usuarios' => 'Gerenciar Usuários',
    '/admin/config' => 'Configurações',
    '/admin/perfil' => 'Perfil do Usuário',
    '/admin/relatorios' => 'Relatórios',
    '/admin/access-denied' => 'Acesso Negado',
    '/fidelidade' => 'Sistema Fidelidade'
];

$baseUrl = 'http://127.0.0.1:8000';

foreach ($urls as $url => $nome) {
    $fullUrl = $baseUrl . $url;

    echo "🔗 Testando: $nome ($url)\n";

    // Fazer requisição simples
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($fullUrl, false, $context);

    if ($response !== false) {
        // Verificar se não é redirecionamento para dashboard
        if (strpos($response, 'Dashboard Administrativo') !== false && $url !== '/admin/dashboard') {
            echo "❌ PROBLEMA: Redirecionando para dashboard em vez da página específica\n";
        } else {
            echo "✅ OK: Página carregando corretamente\n";
        }
    } else {
        echo "❌ ERRO: Não foi possível acessar a URL\n";
    }

    echo "\n";
}

echo "=== RESUMO ===\n";
echo "Teste concluído. Verifique se todos os links estão com ✅ OK\n";
echo "Se algum link mostrar 'PROBLEMA', significa que está redirecionando incorretamente.\n\n";

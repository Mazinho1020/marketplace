<?php
echo "=== DIAGNÓSTICO DE AUTENTICAÇÃO ===\n";

// Simular sessão para testar autenticação
session_start();

echo "🔍 Verificando URLs e autenticação...\n";

// Testar URL sem autenticação
$urls = [
    'http://localhost:8000/',
    'http://localhost:8000/login',
    'http://localhost:8000/comerciantes/empresas/1/usuarios'
];

foreach ($urls as $url) {
    echo "\n🌐 Testando: $url\n";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
            ]
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        echo "❌ Erro ao acessar\n";
        continue;
    }

    $length = strlen($response);
    echo "📊 Tamanho: {$length} bytes\n";

    if (strpos($response, 'login') !== false) {
        echo "🔒 Redirecionando para login\n";
    }

    if (strpos($response, 'usuarios') !== false) {
        echo "✅ Página de usuários carregada\n";
    }

    if (strpos($response, 'error') !== false || strpos($response, 'erro') !== false) {
        echo "⚠️ Possível erro na página\n";
    }
}

echo "\n🎯 Análise:\n";
echo "• Se a página redireciona para login = usuário não autenticado\n";
echo "• Se página retorna 0 bytes = erro de renderização ou timeout\n";
echo "• Solução: fazer login primeiro em /login\n";

echo "\n📋 Próximos passos:\n";
echo "1. Acessar http://localhost:8000/login\n";
echo "2. Fazer login com usuário válido\n";
echo "3. Tentar acessar http://localhost:8000/comerciantes/empresas/1/usuarios\n";
echo "4. Verificar se o formulário carrega com as 85 permissões\n";

<?php

/**
 * Verificação Final do Sistema de Login Simplificado
 * Este script verifica se todos os componentes estão funcionando corretamente
 */

echo "=== VERIFICAÇÃO FINAL DO SISTEMA ===\n\n";

// Verificar se os arquivos principais existem
$arquivos = [
    'Controller' => 'app/Http/Controllers/Auth/LoginControllerSimplified.php',
    'Middleware' => 'app/Http/Middleware/AuthMiddleware.php',
    'View Login' => 'resources/views/admin/login-simplified.blade.php',
    'View Dashboard' => 'resources/views/admin/dashboard-simplified.blade.php',
    'View Acesso Negado' => 'resources/views/admin/access-denied.blade.php',
    'DashboardController' => 'app/Http/Controllers/DashboardController.php'
];

echo "📁 VERIFICANDO ARQUIVOS:\n";
foreach ($arquivos as $nome => $arquivo) {
    if (file_exists($arquivo)) {
        echo "✅ $nome: $arquivo\n";
    } else {
        echo "❌ $nome: $arquivo (FALTANDO)\n";
    }
}

echo "\n🔗 VERIFICANDO ROTAS:\n";

// Verificar se as rotas estão configuradas
$webRoutes = file_get_contents('routes/web.php');
$rotasVerificar = [
    '/login' => 'LoginControllerSimplified@showLoginForm',
    'POST /login' => 'LoginControllerSimplified@authenticate',
    '/logout' => 'LoginControllerSimplified@logout',
    '/admin/dashboard' => 'DashboardController@adminDashboard',
    '/admin/access-denied' => 'access-denied'
];

foreach ($rotasVerificar as $rota => $verificacao) {
    if (strpos($webRoutes, $verificacao) !== false) {
        echo "✅ Rota $rota configurada\n";
    } else {
        echo "❌ Rota $rota não encontrada\n";
    }
}

echo "\n🔧 VERIFICANDO MIDDLEWARE:\n";
$appBootstrap = file_get_contents('bootstrap/app.php');
if (strpos($appBootstrap, 'auth.simple') !== false) {
    echo "✅ Middleware 'auth.simple' registrado\n";
} else {
    echo "❌ Middleware 'auth.simple' não registrado\n";
}

echo "\n💾 VERIFICANDO BANCO DE DADOS:\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão com banco estabelecida\n";

    // Verificar tabelas
    $tabelas = [
        'empresa_usuarios' => 'Usuários principais',
        'empresa_usuario_tipos' => 'Tipos de usuário',
        'empresa_usuarios_login_attempts' => 'Tentativas de login',
        'empresa_usuarios_password_resets' => 'Reset de senhas',
        'empresa_usuarios_activity_log' => 'Log de atividades'
    ];

    foreach ($tabelas as $tabela => $desc) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM $tabela")->fetchColumn();
            echo "✅ $desc ($tabela): $count registros\n";
        } else {
            echo "❌ $desc ($tabela): Tabela não existe\n";
        }
    }

    echo "\n👥 USUÁRIOS DE TESTE:\n";
    $usuarios = $pdo->query("SELECT nome, email, tipo, nivel_acesso FROM empresa_usuarios WHERE email LIKE '%@teste.com' ORDER BY nivel_acesso DESC")->fetchAll();

    if (count($usuarios) > 0) {
        foreach ($usuarios as $user) {
            echo "✅ {$user['nome']} ({$user['email']}) - {$user['tipo']} (nível {$user['nivel_acesso']})\n";
        }
    } else {
        echo "❌ Nenhum usuário de teste encontrado\n";
    }
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "\n";
}

echo "\n🧪 LINKS PARA TESTE:\n";
echo "🔐 Login: http://127.0.0.1:8000/login\n";
echo "📊 Dashboard: http://127.0.0.1:8000/admin/dashboard\n";
echo "🚫 Acesso Negado: http://127.0.0.1:8000/admin/access-denied\n";
echo "👥 Criar Usuários: http://127.0.0.1:8000/criar-usuarios-teste.php\n";

echo "\n📋 INSTRUÇÕES DE TESTE:\n";
echo "1. Acesse http://127.0.0.1:8000/criar-usuarios-teste.php e clique em 'Criar Usuários de Teste'\n";
echo "2. Vá para http://127.0.0.1:8000/login\n";
echo "3. Teste os seguintes usuários:\n";
echo "   • admin@teste.com (nível 100) - DEVE acessar dashboard\n";
echo "   • supervisor@teste.com (nível 60) - DEVE acessar dashboard\n";
echo "   • operador@teste.com (nível 40) - deve ver 'Acesso Negado'\n";
echo "   • consulta@teste.com (nível 20) - deve ver 'Acesso Negado'\n";
echo "4. Senha para todos: 123456\n";

echo "\n✅ VERIFICAÇÃO CONCLUÍDA!\n";
echo "Sistema de Login Simplificado implementado com controle de acesso por níveis.\n\n";

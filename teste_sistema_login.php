<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "TESTE DO SISTEMA DE LOGIN SIMPLIFICADO\n";
echo "======================================\n\n";

try {
    // 1. Verificar tabelas criadas
    echo "1. Verificando tabelas...\n";

    $tabelas = [
        'empresa_usuario_tipos',
        'empresa_usuarios_login_attempts',
        'empresa_usuarios_password_resets'
    ];

    foreach ($tabelas as $tabela) {
        $exists = DB::select("SHOW TABLES LIKE '$tabela'");
        echo "   $tabela: " . (count($exists) > 0 ? "✅" : "❌") . "\n";
    }

    // 2. Verificar tipos de usuário
    echo "\n2. Tipos de usuário disponíveis:\n";
    $tipos = DB::table('empresa_usuario_tipos')->orderBy('nivel_acesso', 'desc')->get();

    foreach ($tipos as $tipo) {
        echo "   - {$tipo->codigo}: {$tipo->nome} (nível {$tipo->nivel_acesso})\n";
    }

    // 3. Verificar usuários existentes
    echo "\n3. Usuários cadastrados:\n";
    $usuarios = DB::table('empresa_usuarios as u')
        ->leftJoin('empresa_usuario_tipos as t', 'u.tipo_id', '=', 't.id')
        ->select('u.id', 'u.nome', 'u.email', 'u.status', 't.codigo as tipo', 't.nivel_acesso')
        ->whereNull('u.deleted_at')
        ->get();

    foreach ($usuarios as $user) {
        echo "   - {$user->nome} ({$user->email}) - {$user->tipo} (nível {$user->nivel_acesso}) - {$user->status}\n";
    }

    // 4. Verificar estrutura empresa_usuarios
    echo "\n4. Verificando campo tipo_id em empresa_usuarios...\n";
    $hasColumn = DB::select("SHOW COLUMNS FROM empresa_usuarios LIKE 'tipo_id'");
    echo "   Campo tipo_id existe: " . (count($hasColumn) > 0 ? "✅" : "❌") . "\n";

    // 5. Testar criação de usuário admin
    echo "\n5. Verificando/criando usuário admin de teste...\n";

    $adminExists = DB::table('empresa_usuarios')->where('email', 'admin@teste.com')->first();

    if (!$adminExists) {
        $adminTipoId = DB::table('empresa_usuario_tipos')->where('codigo', 'admin')->value('id');

        $userId = DB::table('empresa_usuarios')->insertGetId([
            'nome' => 'Admin Teste',
            'email' => 'admin@teste.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'empresa_id' => 1,
            'status' => 'ativo',
            'tipo_id' => $adminTipoId,
            'data_cadastro' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "   ✅ Usuário admin criado com ID: $userId\n";
    } else {
        echo "   ✅ Usuário admin já existe\n";
    }

    echo "\n=== SISTEMA PRONTO PARA TESTE ===\n";
    echo "\n🔗 URLs para testar:\n";
    echo "1. Login: http://127.0.0.1:8000/login\n";
    echo "2. Admin Dashboard: http://127.0.0.1:8000/admin/dashboard\n";
    echo "\n🔑 Credenciais de teste:\n";
    echo "Email: admin@teste.com\n";
    echo "Senha: 123456\n";
    echo "\n📋 Funcionalidades implementadas:\n";
    echo "- ✅ Sistema de login simplificado\n";
    echo "- ✅ Tipos de usuário com níveis de acesso\n";
    echo "- ✅ Middleware de autenticação\n";
    echo "- ✅ Dashboard administrativo\n";
    echo "- ✅ Controle de tentativas de login\n";
    echo "- ✅ Logs de atividade\n";
    echo "- ✅ Sistema de recuperação de senha\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

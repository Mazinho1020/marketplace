<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== DIAGNÓSTICO ADMIN ACCESS ===\n\n";

// 1. Verificar se existem usuários
try {
    $hasUsersTable = DB::getSchemaBuilder()->hasTable('empresa_usuarios');
    echo "1. Tabela empresa_usuarios existe: " . ($hasUsersTable ? "✅ SIM" : "❌ NÃO") . "\n";

    if ($hasUsersTable) {
        $userCount = DB::table('empresa_usuarios')->count();
        echo "   Total de usuários: $userCount\n";

        if ($userCount > 0) {
            $users = DB::table('empresa_usuarios')->select('id', 'email', 'nome', 'tipo')->get();
            echo "   Usuários disponíveis:\n";
            foreach ($users as $user) {
                echo "   - ID: {$user->id} | Email: {$user->email} | Nome: {$user->nome} | Tipo: {$user->tipo}\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO ao verificar usuários: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Verificar configuração de autenticação
echo "2. Configuração de autenticação:\n";
$authGuard = config('auth.defaults.guard');
$authProvider = config('auth.defaults.provider');
echo "   Guard padrão: $authGuard\n";
echo "   Provider padrão: $authProvider\n";

// 3. Verificar rotas admin
echo "\n3. Rotas admin disponíveis:\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = [];

    foreach ($routes as $route) {
        $uri = $route->uri();
        if (str_starts_with($uri, 'admin')) {
            $adminRoutes[] = [
                'uri' => $uri,
                'name' => $route->getName(),
                'methods' => implode('|', $route->methods()),
                'middleware' => implode(',', $route->middleware())
            ];
        }
    }

    foreach ($adminRoutes as $route) {
        echo "   {$route['methods']} /{$route['uri']} -> {$route['name']} (middleware: {$route['middleware']})\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO ao listar rotas: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Criar usuário admin temporário se não existir
echo "4. Criação de usuário admin:\n";
try {
    if ($hasUsersTable) {
        $adminExists = DB::table('empresa_usuarios')
            ->where('email', 'admin@teste.com')
            ->exists();

        if (!$adminExists) {
            echo "   Criando usuário admin temporário...\n";

            $adminId = DB::table('empresa_usuarios')->insertGetId([
                'nome' => 'Admin Teste',
                'email' => 'admin@teste.com',
                'password' => Hash::make('123456'),
                'tipo' => 'admin',
                'ativo' => 1,
                'empresa_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo "   ✅ Usuário admin criado com ID: $adminId\n";
            echo "   📧 Email: admin@teste.com\n";
            echo "   🔒 Senha: 123456\n";
        } else {
            echo "   ✅ Usuário admin já existe\n";
            echo "   📧 Email: admin@teste.com\n";
            echo "   🔒 Senha: 123456\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO ao criar usuário: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Instruções para acesso
echo "5. INSTRUÇÕES PARA ACESSAR ADMIN:\n";
echo "   1. Abra: http://127.0.0.1:8000/login\n";
echo "   2. Digite:\n";
echo "      Email: admin@teste.com\n";
echo "      Senha: 123456\n";
echo "   3. Após login, acesse: http://127.0.0.1:8000/admin\n";
echo "\n";

echo "=== FIM DO DIAGNÓSTICO ===\n";

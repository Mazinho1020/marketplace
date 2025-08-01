<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "DIAGNÓSTICO ADMIN\n";
echo "================\n\n";

// Verificar usuários
try {
    $users = DB::table('empresa_usuarios')->get();
    echo "Usuários encontrados: " . count($users) . "\n";

    foreach ($users as $user) {
        echo "- {$user->email} ({$user->tipo})\n";
    }

    // Verificar se admin existe
    $admin = DB::table('empresa_usuarios')->where('email', 'admin@teste.com')->first();

    if (!$admin) {
        echo "\nCriando usuário admin...\n";
        DB::table('empresa_usuarios')->insert([
            'nome' => 'Admin Teste',
            'email' => 'admin@teste.com',
            'password' => Hash::make('123456'),
            'tipo' => 'admin',
            'ativo' => 1,
            'empresa_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Admin criado!\n";
    }

    echo "\nPARA ACESSAR ADMIN:\n";
    echo "1. Vá para: http://127.0.0.1:8000/login\n";
    echo "2. Email: admin@teste.com\n";
    echo "3. Senha: 123456\n";
    echo "4. Depois acesse: http://127.0.0.1:8000/admin\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}

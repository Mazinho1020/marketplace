<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar se há usuários comerciantes
$usuarios = DB::table('empresa_usuarios')->get();

echo "=== USUÁRIOS COMERCIANTES ===\n";
foreach ($usuarios as $usuario) {
    echo "ID: {$usuario->id} | Email: {$usuario->email} | Empresa: {$usuario->empresa_id}\n";
}

if ($usuarios->isEmpty()) {
    echo "\nCriando usuário de teste...\n";

    // Criar usuário teste
    $userId = DB::table('empresa_usuarios')->insertGetId([
        'nome' => 'Teste Comerciante',
        'email' => 'teste@teste.com',
        'password' => Hash::make('123456'),
        'empresa_id' => 1,
        'ativo' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "Usuário criado com ID: {$userId}\n";
    echo "Email: teste@teste.com\n";
    echo "Senha: 123456\n";
}

echo "\n=== PRÓXIMOS PASSOS ===\n";
echo "1. Acesse: http://127.0.0.1:8000/comerciantes/login\n";
echo "2. Faça login com as credenciais acima\n";
echo "3. Depois acesse: http://127.0.0.1:8000/comerciantes/produtos/categorias\n";

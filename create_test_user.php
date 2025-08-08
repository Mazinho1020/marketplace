<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Support\Facades\Hash;

try {
    echo "=== CRIANDO USUÁRIO DE TESTE ===\n";

    // Verificar se já existe
    $existingUser = EmpresaUsuario::where('email', 'admin@teste.com')->first();
    if ($existingUser) {
        echo "Usuário admin@teste.com já existe! Atualizando...\n";
        $existingUser->update([
            'senha' => Hash::make('123456'),
            'status' => 'ativo',
            'nome' => 'Admin Teste'
        ]);
        echo "Usuário atualizado!\n";
    } else {
        // Criar novo usuário
        $user = EmpresaUsuario::create([
            'nome' => 'Admin Teste',
            'email' => 'admin@teste.com',
            'senha' => Hash::make('123456'),
            'status' => 'ativo',
            'empresa_id' => 1, // Primeira empresa
            'perfil_id' => 1,
            'username' => 'admin.teste',
            'uuid' => \Illuminate\Support\Str::uuid(),
            'data_cadastro' => now(),
        ]);

        echo "Usuário criado com sucesso!\n";
        echo "Email: admin@teste.com\n";
        echo "Senha: 123456\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

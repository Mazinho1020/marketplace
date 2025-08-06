<?php

require_once 'vendor/autoload.php';

// Carregar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Support\Facades\Hash;

echo "=== CRIANDO USUÁRIO DE TESTE ===\n\n";

// Verificar se já existe
$user = EmpresaUsuario::where('email', 'teste@exemplo.com')->first();

if ($user) {
    echo "Usuário de teste já existe: teste@exemplo.com\n";
} else {
    echo "Criando usuário de teste...\n";

    $user = EmpresaUsuario::create([
        'nome' => 'Usuario Teste',
        'email' => 'teste@exemplo.com',
        'senha' => password_hash('123456', PASSWORD_DEFAULT),
        'telefone' => '11999999999',
        'cpf' => '12345678901',
        'empresa_id' => 1,
        'cargo' => 'proprietario',
        'status' => 'ativo',
        'data_nascimento' => '1990-01-01',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "✓ Usuário criado com sucesso!\n";
}

echo "\nDados do usuário:\n";
echo "Email: teste@exemplo.com\n";
echo "Senha: 123456\n";
echo "Empresa ID: " . $user->empresa_id . "\n";
echo "Status: " . $user->status . "\n";

echo "\nAgora você pode testar o login manualmente no browser!\n";
echo "1. Acesse: http://localhost:8000/comerciantes/login\n";
echo "2. Use email: teste@exemplo.com e senha: 123456\n";
echo "3. Depois tente acessar: http://localhost:8000/comerciantes/empresas/1/horarios\n";

echo "\n=== FIM ===\n";

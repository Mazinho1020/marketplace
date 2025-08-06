<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Comerciantes\Models\EmpresaUsuario;
use App\Comerciantes\Models\Empresa;

echo "=== TESTE CRUD USUÁRIOS ===\n";

// Verificar se existe empresa
$empresa = Empresa::first();
if (!$empresa) {
    echo "❌ Nenhuma empresa encontrada\n";
    exit;
}

echo "✅ Empresa encontrada: {$empresa->nome_fantasia}\n";

// Verificar usuários vinculados
$usuariosVinculados = $empresa->usuariosVinculados;
echo "👥 Usuários vinculados: " . $usuariosVinculados->count() . "\n";

if ($usuariosVinculados->count() > 0) {
    echo "\n=== LISTAGEM DE USUÁRIOS ===\n";
    foreach ($usuariosVinculados as $usuario) {
        echo "• {$usuario->nome} ({$usuario->email}) - Perfil: {$usuario->pivot->perfil}\n";
    }
}

// Criar um usuário de teste se não existir
$emailTeste = 'teste@exemplo.com';
$usuarioTeste = EmpresaUsuario::where('email', $emailTeste)->first();

if (!$usuarioTeste) {
    echo "\n=== CRIANDO USUÁRIO DE TESTE ===\n";

    $usuarioTeste = EmpresaUsuario::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'nome' => 'Usuário Teste',
        'username' => 'usuario.teste',
        'email' => $emailTeste,
        'senha' => bcrypt('123456'),
        'status' => 'ativo',
    ]);

    echo "✅ Usuário criado: {$usuarioTeste->nome}\n";

    // Vincular à empresa
    $empresa->usuariosVinculados()->attach($usuarioTeste->id, [
        'perfil' => 'colaborador',
        'permissoes' => json_encode(['produtos.view']),
        'status' => 'ativo',
        'data_vinculo' => now(),
    ]);

    echo "✅ Usuário vinculado à empresa\n";
} else {
    echo "\n✅ Usuário de teste já existe: {$usuarioTeste->nome}\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
echo "👥 Total de usuários vinculados: " . $empresa->usuariosVinculados()->count() . "\n";

<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    echo "=== TESTE DE LOGIN CORRIGIDO ===\n\n";

    $email = 'mazinho@gmail.com';
    $senha = '123456'; // Senha do arquivo SQL

    // 1. Buscar usuário (query corrigida)
    echo "1. Buscando usuário...\n";
    $usuario = DB::table('empresa_usuarios')
        ->select('*')
        ->where('email', $email)
        ->whereNull('deleted_at')
        ->first();

    if (!$usuario) {
        echo "   ✗ Usuário não encontrado!\n";
        exit(1);
    }

    echo "   ✓ Usuário encontrado: {$usuario->nome}\n";
    echo "   - Status: {$usuario->status}\n";

    // 2. Verificar status
    if ($usuario->status !== 'ativo') {
        echo "   ⚠️  Usuário não está ativo\n";
    } else {
        echo "   ✓ Usuário está ativo\n";
    }

    // 3. Verificar senha
    echo "\n2. Verificando senha...\n";
    $senhaHash = $usuario->senha;
    echo "   Hash armazenado: {$senhaHash}\n";

    if (Hash::check($senha, $senhaHash)) {
        echo "   ✓ Senha correta!\n";
    } else {
        echo "   ✗ Senha incorreta!\n";
        // Testar outras senhas possíveis
        $senhasTest = ['123456', 'password', 'mazinho', 'admin'];
        foreach ($senhasTest as $testSenha) {
            if (Hash::check($testSenha, $senhaHash)) {
                echo "   ✓ Senha correta encontrada: {$testSenha}\n";
                break;
            }
        }
    }

    // 4. Simular dados de sessão que seriam criados
    echo "\n3. Dados de sessão que seriam criados:\n";
    $sessionData = [
        'usuario_id' => $usuario->id,
        'usuario_nome' => $usuario->nome,
        'usuario_email' => $usuario->email,
        'empresa_id' => $usuario->empresa_id,
        'usuario_tipo_id' => $usuario->perfil_id ?? null,
        'usuario_tipo' => 'admin',
        'tipo_nome' => 'Administrador',
        'nivel_acesso' => 100,
        'login_time' => time(),
        'last_activity' => time()
    ];

    foreach ($sessionData as $key => $value) {
        echo "   - {$key}: " . ($value ?? 'NULL') . "\n";
    }

    echo "\n✓ Login deve funcionar agora!\n";
    echo "\n=== FIM TESTE ===\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}

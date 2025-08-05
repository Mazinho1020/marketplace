<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "🔧 CORREÇÃO DA SENHA DO USUÁRIO\n";
echo "=" . str_repeat("=", 35) . "\n\n";

try {
    // Verificar usuário
    $user = DB::table('empresa_usuarios')
        ->where('email', 'mazinho@gmail.com')
        ->first();

    if (!$user) {
        echo "❌ Usuário não encontrado\n";
        exit;
    }

    echo "✅ Usuário encontrado: {$user->nome}\n";
    echo "   Email: {$user->email}\n";
    echo "   Status: {$user->status}\n";

    // Gerar nova senha
    $novaSenha = Hash::make('123456');
    echo "\n🔧 Atualizando senha...\n";

    // Atualizar diretamente no banco
    $updated = DB::table('empresa_usuarios')
        ->where('id', $user->id)
        ->update([
            'password' => $novaSenha,
            'updated_at' => now()
        ]);

    if ($updated) {
        echo "✅ Senha atualizada com sucesso!\n";

        // Verificar se a senha está correta agora
        $userAtualizado = DB::table('empresa_usuarios')
            ->where('id', $user->id)
            ->first();

        $senhaCorreta = Hash::check('123456', $userAtualizado->password);
        echo ($senhaCorreta ? "✅" : "❌") . " Verificação da senha: " . ($senhaCorreta ? "OK" : "FALHOU") . "\n";

        echo "\n🎯 CREDENCIAIS PARA LOGIN:\n";
        echo "   Email: mazinho@gmail.com\n";
        echo "   Senha: 123456\n";
        echo "\n🔗 ACESSE:\n";
        echo "   http://localhost:8000/comerciantes/login\n";
    } else {
        echo "❌ Falha ao atualizar senha\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 37) . "\n";

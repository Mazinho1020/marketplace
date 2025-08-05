<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "🔧 ATUALIZAR SENHA - CAMPO CORRETO\n";
echo "=" . str_repeat("=", 38) . "\n\n";

try {
    // Gerar hash da senha
    $novaSenha = Hash::make('123456');
    echo "🔑 Nova senha gerada: " . substr($novaSenha, 0, 20) . "...\n";

    // Atualizar usando o campo correto 'senha'
    $updated = DB::table('empresa_usuarios')
        ->where('email', 'mazinho@gmail.com')
        ->update([
            'senha' => $novaSenha,
            'updated_at' => now()
        ]);

    if ($updated) {
        echo "✅ Senha atualizada com sucesso!\n";

        // Verificar se está funcionando
        $user = DB::table('empresa_usuarios')
            ->where('email', 'mazinho@gmail.com')
            ->first();

        $senhaCorreta = Hash::check('123456', $user->senha);
        echo ($senhaCorreta ? "✅" : "❌") . " Verificação: " . ($senhaCorreta ? "OK" : "FALHOU") . "\n";

        echo "\n🎯 CREDENCIAIS PRONTAS:\n";
        echo "   Email: mazinho@gmail.com\n";
        echo "   Senha: 123456\n";
        echo "   Status: {$user->status}\n";

        echo "\n🔗 FAÇA LOGIN EM:\n";
        echo "   http://localhost:8000/comerciantes/login\n";
    } else {
        echo "❌ Nenhuma linha foi atualizada\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 40) . "\n";

<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 ESTRUTURA TABELA EMPRESA_USUARIOS\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    $columns = DB::select('DESCRIBE empresa_usuarios');

    echo "📋 COLUNAS:\n";
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }

    echo "\n🔍 VERIFICANDO SENHA:\n";
    $user = DB::table('empresa_usuarios')
        ->where('email', 'mazinho@gmail.com')
        ->first();

    if ($user) {
        echo "   Usuário: {$user->nome}\n";
        $userArray = (array) $user;

        // Verificar se tem campo de senha
        $senhaFields = ['password', 'senha', 'pass'];
        $senhaField = null;

        foreach ($senhaFields as $field) {
            if (isset($userArray[$field])) {
                $senhaField = $field;
                break;
            }
        }

        if ($senhaField) {
            echo "   Campo de senha: {$senhaField}\n";
            echo "   Valor atual: " . substr($userArray[$senhaField], 0, 20) . "...\n";
        } else {
            echo "   ❌ Nenhum campo de senha encontrado!\n";
            echo "   Campos disponíveis: " . implode(', ', array_keys($userArray)) . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 42) . "\n";

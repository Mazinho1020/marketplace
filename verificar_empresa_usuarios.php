<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando tabela empresa_usuarios...\n";
echo "=========================================\n\n";

try {
    // Verificar estrutura da tabela
    $columns = DB::select('DESCRIBE empresa_usuarios');

    echo "📋 Estrutura da tabela empresa_usuarios:\n";
    foreach ($columns as $col) {
        $nullable = $col->Null === 'YES' ? ' (NULL)' : ' (NOT NULL)';
        echo "   - {$col->Field}: {$col->Type}{$nullable}\n";
    }

    echo "\n";

    // Verificar quantidade de usuários
    $count = DB::table('empresa_usuarios')->count();
    echo "👥 Total de usuários: $count\n\n";

    if ($count > 0) {
        echo "📋 Primeiros 3 usuários:\n";
        $usuarios = DB::table('empresa_usuarios')
            ->select('id', 'nome', 'email', 'empresa_id')
            ->limit(3)
            ->get();

        foreach ($usuarios as $user) {
            echo "   - ID: {$user->id}, Nome: {$user->nome}, Email: {$user->email}, Empresa: " . ($user->empresa_id ?? 'NULL') . "\n";
        }
        echo "\n";
    } else {
        echo "⚠️ Nenhum usuário encontrado na tabela empresa_usuarios\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

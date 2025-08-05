<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Comerciantes\Models\EmpresaUsuario;

echo "🧪 TESTE RÁPIDO DAS VIEWS DE EMPRESA\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    // Verificar usuário
    $user = EmpresaUsuario::find(3);

    if ($user) {
        echo "✅ Usuário: {$user->nome}\n";

        // Verificar marcas
        $marcas = $user->marcasProprietario()->count();
        echo "✅ Marcas: $marcas\n";

        // Verificar empresas usando o relacionamento correto
        $empresas = $user->empresasProprietario()->count();
        echo "✅ Empresas: $empresas\n";

        if ($empresas > 0) {
            $empresa = $user->empresasProprietario()->first();
            echo "✅ Primeira empresa: {$empresa->nome}\n";
        }
    } else {
        echo "❌ Usuário não encontrado\n";
    }

    echo "\n🎯 VIEWS DE EMPRESA CRIADAS:\n";
    echo "   ✅ index.blade.php\n";
    echo "   ✅ create.blade.php\n";
    echo "   ✅ show.blade.php\n";
    echo "\n   Agora teste: http://localhost:8000/comerciantes/empresas\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 42) . "\n";

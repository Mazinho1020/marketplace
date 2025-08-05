<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Comerciantes\Models\Empresa;
use Illuminate\Support\Facades\DB;

echo "🔍 DEBUG DETALHADO - RELACIONAMENTO\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    $empresa = Empresa::find(1);
    echo "✅ Empresa: {$empresa->nome}\n";

    // Verificar vínculos diretamente na tabela
    echo "\n📊 VÍNCULOS RAW:\n";
    $vinculos = DB::table('empresa_user_vinculos')
        ->where('empresa_id', 1)
        ->get();

    foreach ($vinculos as $v) {
        echo "   - Empresa: {$v->empresa_id}, User: {$v->user_id}, Perfil: {$v->perfil}\n";
    }

    // Carregar relacionamento
    echo "\n🔗 RELACIONAMENTO ELOQUENT:\n";
    $empresa->load('usuariosVinculados');

    echo "   Count: " . $empresa->usuariosVinculados->count() . "\n";

    foreach ($empresa->usuariosVinculados as $index => $vinculo) {
        echo "   [{$index}] Vinculo object: " . get_class($vinculo) . "\n";
        echo "        ID: " . ($vinculo->id ?? 'NULL') . "\n";
        echo "        Nome: " . ($vinculo->nome ?? 'NULL') . "\n";
        echo "        Email: " . ($vinculo->email ?? 'NULL') . "\n";
        echo "        Pivot perfil: " . ($vinculo->pivot->perfil ?? 'NULL') . "\n";
        echo "        Pivot status: " . ($vinculo->pivot->status ?? 'NULL') . "\n";

        // Verificar se existe user property
        if (isset($vinculo->user)) {
            echo "        USER property existe\n";
        } else {
            echo "        USER property NÃO EXISTE\n";
        }
    }

    // Verificar se o problema é que o relacionamento não está usando a propriedade 'user'
    echo "\n🔍 VERIFICANDO MODELO EMPRESA:\n";
    $reflection = new ReflectionClass($empresa);
    $methods = $reflection->getMethods();

    foreach ($methods as $method) {
        if (strpos($method->getName(), 'usuarios') !== false) {
            echo "   Método: " . $method->getName() . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 42) . "\n";

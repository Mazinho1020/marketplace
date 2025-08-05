<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Comerciantes\Models\Empresa;
use Illuminate\Support\Facades\DB;

echo "🧪 DEBUG - USUÁRIOS VINCULADOS\n";
echo "=" . str_repeat("=", 35) . "\n\n";

try {
    // Verificar primeira empresa
    $empresa = Empresa::find(1);

    if (!$empresa) {
        echo "❌ Empresa ID 1 não encontrada\n";
        exit;
    }

    echo "✅ Empresa encontrada: {$empresa->nome}\n";

    // Verificar relacionamentos carregados
    echo "\n🔍 VERIFICANDO RELACIONAMENTOS:\n";

    // Marca
    if ($empresa->marca) {
        echo "   ✅ Marca: {$empresa->marca->nome}\n";
    } else {
        echo "   ❌ Marca: null\n";
    }

    // Proprietário
    if ($empresa->proprietario) {
        echo "   ✅ Proprietário: {$empresa->proprietario->nome}\n";
    } else {
        echo "   ❌ Proprietário: null\n";
    }

    // Verificar usuários vinculados sem carregar relacionamento
    $vinculosRaw = DB::table('empresa_user_vinculos')
        ->where('empresa_id', $empresa->id)
        ->get();

    echo "\n📊 VÍNCULOS NA TABELA:\n";
    echo "   Total de vínculos: " . $vinculosRaw->count() . "\n";

    foreach ($vinculosRaw as $vinculo) {
        $user = DB::table('empresa_usuarios')->find($vinculo->user_id);
        echo "   - User ID {$vinculo->user_id}: " . ($user ? $user->nome : 'NÃO ENCONTRADO') . " ({$vinculo->perfil})\n";
    }

    // Agora carregar relacionamento
    echo "\n🔗 CARREGANDO RELACIONAMENTO:\n";
    $empresa->load(['usuariosVinculados', 'proprietario', 'marca']);

    echo "   Usuários vinculados carregados: " . $empresa->usuariosVinculados->count() . "\n";

    foreach ($empresa->usuariosVinculados as $vinculo) {
        if ($vinculo) {
            echo "   ✅ {$vinculo->nome} ({$vinculo->pivot->perfil})\n";
        } else {
            echo "   ❌ Usuário NULL encontrado\n";
        }
    }

    // Verificar se há problemas na tabela empresa_usuarios
    echo "\n🔍 VERIFICANDO TABELA EMPRESA_USUARIOS:\n";
    $totalUsers = DB::table('empresa_usuarios')->count();
    echo "   Total de usuários: $totalUsers\n";

    $usersComNome = DB::table('empresa_usuarios')->whereNotNull('nome')->count();
    echo "   Usuários com nome: $usersComNome\n";

    $usersSemNome = DB::table('empresa_usuarios')->whereNull('nome')->count();
    echo "   Usuários sem nome: $usersSemNome\n";

    if ($usersSemNome > 0) {
        echo "\n❌ PROBLEMA ENCONTRADO: Há usuários sem nome na tabela!\n";
        $problemUsers = DB::table('empresa_usuarios')->whereNull('nome')->limit(5)->get();
        foreach ($problemUsers as $user) {
            echo "   - ID {$user->id}: nome=NULL, email={$user->email}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
}

echo "\n" . str_repeat("=", 37) . "\n";

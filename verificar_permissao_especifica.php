<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 VERIFICAÇÃO DE PERMISSÕES ESPECÍFICAS\n";
echo "=" . str_repeat("=", 42) . "\n\n";

try {
    // Buscar usuário
    $usuario = DB::table('empresa_usuarios')->where('email', 'mazinho@gmail.com')->first();

    if (!$usuario) {
        echo "❌ Usuário mazinho@gmail.com não encontrado!\n";

        echo "📋 Usuários disponíveis:\n";
        $usuarios = DB::table('empresa_usuarios')->select('id', 'nome', 'email')->get();
        foreach ($usuarios as $u) {
            echo "   - ID: {$u->id} | Nome: {$u->nome} | Email: {$u->email}\n";
        }
        exit;
    }

    echo "✅ Usuário encontrado: {$usuario->nome} (ID: {$usuario->id})\n\n";

    // Verificar empresa 1
    $empresa1 = DB::table('empresas')->where('id', 1)->first();
    if (!$empresa1) {
        echo "❌ Empresa 1 não existe!\n";

        echo "📋 Empresas disponíveis:\n";
        $empresas = DB::table('empresas')->select('id', 'nome_fantasia')->get();
        foreach ($empresas as $e) {
            echo "   - ID: {$e->id} | Nome: {$e->nome_fantasia}\n";
        }
        exit;
    }

    echo "✅ Empresa 1 encontrada: {$empresa1->nome_fantasia}\n\n";

    // Verificar se é proprietário da empresa
    $isProprietario = DB::table('empresas')
        ->where('id', 1)
        ->where('user_id', $usuario->id)
        ->exists();

    echo "🏢 PROPRIETÁRIO da empresa 1: " . ($isProprietario ? "✅ SIM" : "❌ NÃO") . "\n";

    // Verificar vínculos na tabela de relacionamento
    $vinculo = DB::table('empresa_user_vinculos')
        ->where('user_id', $usuario->id)
        ->where('empresa_id', 1)
        ->first();

    echo "🔗 VÍNCULO com empresa 1: " . ($vinculo ? "✅ SIM" : "❌ NÃO") . "\n";

    if ($vinculo) {
        echo "   - Perfil: {$vinculo->perfil}\n";
        echo "   - Status: {$vinculo->status}\n";
    }

    // Verificar na tabela empresas_marketplace se existe
    $vinculoMarketplace = DB::table('empresas_marketplace')
        ->where('user_id', $usuario->id)
        ->where('empresa_id', 1)
        ->first();

    echo "🛒 VÍNCULO MARKETPLACE com empresa 1: " . ($vinculoMarketplace ? "✅ SIM" : "❌ NÃO") . "\n";

    // Conclusão
    $temPermissao = $isProprietario || ($vinculo && $vinculo->status === 'ativo') || $vinculoMarketplace;

    echo "\n🎯 RESULTADO FINAL:\n";
    echo "   Tem permissão para empresa 1: " . ($temPermissao ? "✅ SIM" : "❌ NÃO") . "\n\n";

    if (!$temPermissao) {
        echo "🔧 SOLUÇÕES:\n";
        echo "   1. Tornar o usuário proprietário da empresa:\n";
        echo "      UPDATE empresas SET user_id = {$usuario->id} WHERE id = 1;\n\n";
        echo "   2. Ou criar vínculo:\n";
        echo "      INSERT INTO empresa_user_vinculos (user_id, empresa_id, perfil, status) VALUES ({$usuario->id}, 1, 'proprietario', 'ativo');\n\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo str_repeat("=", 44) . "\n";

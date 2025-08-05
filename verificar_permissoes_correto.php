<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 VERIFICAÇÃO DE PERMISSÕES CORRIGIDA\n";
echo "=" . str_repeat("=", 39) . "\n\n";

try {
    // Buscar usuário
    $usuario = DB::table('empresa_usuarios')->where('email', 'mazinho@gmail.com')->first();

    if (!$usuario) {
        echo "❌ Usuário mazinho@gmail.com não encontrado!\n";

        echo "📋 Usuários disponíveis:\n";
        $usuarios = DB::table('empresa_usuarios')->select('id', 'nome', 'email', 'empresa_id')->get();
        foreach ($usuarios as $u) {
            echo "   - ID: {$u->id} | Nome: {$u->nome} | Email: {$u->email} | Empresa: {$u->empresa_id}\n";
        }
        exit;
    }

    echo "✅ Usuário encontrado: {$usuario->nome} (ID: {$usuario->id})\n";
    echo "   - Email: {$usuario->email}\n";
    echo "   - Empresa padrão: {$usuario->empresa_id}\n";
    echo "   - Status: {$usuario->status}\n\n";

    // Verificar empresa 1
    $empresa1 = DB::table('empresas')->where('id', 1)->first();
    if (!$empresa1) {
        echo "❌ Empresa 1 não existe!\n";

        echo "📋 Empresas disponíveis:\n";
        $empresas = DB::table('empresas')->select('id', 'nome_fantasia', 'status')->get();
        foreach ($empresas as $e) {
            echo "   - ID: {$e->id} | Nome: {$e->nome_fantasia} | Status: {$e->status}\n";
        }
        exit;
    }

    echo "✅ Empresa 1 encontrada: {$empresa1->nome_fantasia}\n";
    echo "   - Status: {$empresa1->status}\n\n";

    // Verificar permissões
    echo "🔑 VERIFICAÇÃO DE PERMISSÕES:\n\n";

    // 1. Empresa padrão do usuário
    $empresaPadrao = ($usuario->empresa_id == 1);
    echo "1. 🏢 Empresa padrão do usuário: " . ($empresaPadrao ? "✅ SIM (empresa 1)" : "❌ NÃO (empresa {$usuario->empresa_id})") . "\n";

    // 2. Vínculos na tabela empresa_user_vinculos
    $vinculo = DB::table('empresa_user_vinculos')
        ->where('user_id', $usuario->id)
        ->where('empresa_id', 1)
        ->first();

    echo "2. 🔗 Vínculo direto: " . ($vinculo ? "✅ SIM" : "❌ NÃO") . "\n";

    if ($vinculo) {
        echo "   - Perfil: {$vinculo->perfil}\n";
        echo "   - Status: {$vinculo->status}\n";
        echo "   - Data vínculo: {$vinculo->data_vinculo}\n";
    }

    // 3. Proprietário na tabela empresas_marketplace
    $proprietarioMarketplace = DB::table('empresas_marketplace')
        ->where('proprietario_id', $usuario->id)
        ->where('id', 1)
        ->first();

    echo "3. 🛒 Proprietário marketplace: " . ($proprietarioMarketplace ? "✅ SIM" : "❌ NÃO") . "\n";

    // Conclusão final
    $temPermissao = $empresaPadrao ||
        ($vinculo && $vinculo->status === 'ativo') ||
        $proprietarioMarketplace;

    echo "\n🎯 RESULTADO FINAL:\n";
    echo "   Tem permissão para empresa 1: " . ($temPermissao ? "✅ SIM" : "❌ NÃO") . "\n\n";

    if (!$temPermissao) {
        echo "🔧 SOLUÇÕES PARA DAR PERMISSÃO:\n\n";
        echo "   Opção 1 - Alterar empresa padrão:\n";
        echo "   UPDATE empresa_usuarios SET empresa_id = 1 WHERE id = {$usuario->id};\n\n";
        echo "   Opção 2 - Criar vínculo:\n";
        echo "   INSERT INTO empresa_user_vinculos (user_id, empresa_id, perfil, status, data_vinculo, created_at, updated_at) \n";
        echo "   VALUES ({$usuario->id}, 1, 'proprietario', 'ativo', NOW(), NOW(), NOW());\n\n";
    } else {
        echo "✅ Usuário tem permissão! O problema pode ser outro.\n";
        echo "   - Verifique se está logado\n";
        echo "   - Verifique se o middleware está funcionando\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 41) . "\n";

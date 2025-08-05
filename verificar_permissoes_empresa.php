<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Comerciantes\Models\EmpresaUsuario;
use App\Comerciantes\Models\Empresa;

echo "🔐 VERIFICAÇÃO DE PERMISSÕES DE EMPRESA\n";
echo "=" . str_repeat("=", 45) . "\n\n";

try {
    // Simular login (você pode ajustar o email conforme necessário)
    $usuario = EmpresaUsuario::where('email', 'mazinho@gmail.com')->first();

    if (!$usuario) {
        echo "❌ Usuário mazinho@gmail.com não encontrado!\n";
        echo "📋 Usuários disponíveis:\n";
        $usuarios = EmpresaUsuario::select('id', 'nome', 'email')->get();
        foreach ($usuarios as $u) {
            echo "   - ID: {$u->id} | Nome: {$u->nome} | Email: {$u->email}\n";
        }
        exit;
    }

    echo "✅ Usuário encontrado: {$usuario->nome} ({$usuario->email})\n\n";

    // Verificar empresas disponíveis
    echo "🏢 EMPRESAS NO SISTEMA:\n";
    $empresas = Empresa::select('id', 'nome_fantasia', 'razao_social')->get();
    foreach ($empresas as $empresa) {
        echo "   - ID: {$empresa->id} | Nome: {$empresa->nome_fantasia}\n";
    }
    echo "\n";

    // Verificar permissões para cada empresa
    echo "🔑 PERMISSÕES DO USUÁRIO:\n";
    foreach ($empresas as $empresa) {
        $temPermissao = $usuario->temPermissaoEmpresa($empresa->id);
        $icon = $temPermissao ? "✅" : "❌";
        echo "   {$icon} Empresa {$empresa->id} ({$empresa->nome_fantasia}): " . ($temPermissao ? "PERMITIDO" : "NEGADO") . "\n";

        if ($temPermissao) {
            // Verificar detalhes da permissão
            echo "      📋 Detalhes da permissão:\n";

            // É proprietário?
            $isProprietario = $usuario->empresasProprietario()->where('id', $empresa->id)->exists();
            echo "      - Proprietário: " . ($isProprietario ? "SIM" : "NÃO") . "\n";

            // Tem vínculo?
            $vinculo = $usuario->empresasVinculadas()->where('empresas.id', $empresa->id)->first();
            if ($vinculo) {
                echo "      - Vínculo: {$vinculo->pivot->perfil}\n";
                echo "      - Status: {$vinculo->pivot->status}\n";
            } else {
                echo "      - Vínculo: NENHUM\n";
            }
            echo "\n";
        }
    }

    // Teste específico para empresa 1
    echo "🎯 TESTE ESPECÍFICO PARA EMPRESA 1:\n";
    if ($empresas->where('id', 1)->first()) {
        $permissaoEmpresa1 = $usuario->temPermissaoEmpresa(1);
        echo "   Permissão para empresa 1: " . ($permissaoEmpresa1 ? "✅ PERMITIDO" : "❌ NEGADO") . "\n";

        if (!$permissaoEmpresa1) {
            echo "\n🔧 SOLUÇÕES POSSÍVEIS:\n";
            echo "   1. Tornar o usuário proprietário da empresa 1\n";
            echo "   2. Criar vínculo do usuário com a empresa 1\n";
            echo "   3. Verificar se a empresa 1 existe\n";
        }
    } else {
        echo "   ❌ Empresa 1 não existe no sistema!\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 47) . "\n";

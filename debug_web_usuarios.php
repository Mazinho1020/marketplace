<?php
// Script para executar via web e resolver o problema definitivamente

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Comerciantes\Models\Empresa;
use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Support\Facades\DB;

try {
    echo "<h1>DEBUG COMPLETO - Usuários Vinculados</h1>";

    // 1. Verificar estrutura das tabelas
    echo "<h3>1. Estrutura das Tabelas</h3>";

    $tabelas = ['empresas', 'empresa_usuarios', 'empresa_user_vinculos'];
    foreach ($tabelas as $tabela) {
        try {
            $count = DB::table($tabela)->count();
            echo "✅ {$tabela}: {$count} registros<br>";
        } catch (Exception $e) {
            echo "❌ {$tabela}: {$e->getMessage()}<br>";
        }
    }

    // 2. Verificar dados da empresa 2
    echo "<h3>2. Dados da Empresa ID 2</h3>";
    $empresa = Empresa::find(2);

    if ($empresa) {
        echo "✅ Empresa encontrada: {$empresa->nome_fantasia}<br>";

        // Query direta na tabela pivot
        $vinculosRaw = DB::table('empresa_user_vinculos')
            ->where('empresa_id', 2)
            ->get();
        echo "Vínculos na tabela pivot: {$vinculosRaw->count()}<br>";

        foreach ($vinculosRaw as $vinculo) {
            $usuario = DB::table('empresa_usuarios')->where('id', $vinculo->user_id)->first();
            $nomeUsuario = $usuario ? ($usuario->nome ?: 'SEM NOME') : 'USUÁRIO NÃO ENCONTRADO';
            echo "- Vínculo: User {$vinculo->user_id} ({$nomeUsuario}) como {$vinculo->perfil}<br>";
        }

        // Testar relacionamento Laravel
        echo "<h4>Relacionamento Laravel:</h4>";
        try {
            $usuariosVinculados = $empresa->usuariosVinculados;
            echo "Count via relacionamento: {$usuariosVinculados->count()}<br>";

            if ($usuariosVinculados->count() > 0) {
                foreach ($usuariosVinculados as $vinculo) {
                    echo "- {$vinculo->nome} (ID: {$vinculo->id})<br>";
                }
            } else {
                echo "❌ Relacionamento retornou coleção vazia<br>";

                // Debug do SQL
                $sql = $empresa->usuariosVinculados()->toSql();
                $bindings = $empresa->usuariosVinculados()->getBindings();
                echo "SQL: {$sql}<br>";
                echo "Bindings: " . json_encode($bindings) . "<br>";
            }
        } catch (Exception $e) {
            echo "❌ Erro no relacionamento: {$e->getMessage()}<br>";
        }
    } else {
        echo "❌ Empresa ID 2 não encontrada<br>";

        // Listar empresas disponíveis
        $empresas = Empresa::all();
        echo "Empresas disponíveis:<br>";
        foreach ($empresas as $emp) {
            echo "- ID {$emp->id}: {$emp->nome_fantasia}<br>";
        }
    }

    // 3. Testar criação de vínculo se necessário
    echo "<h3>3. Criar Vínculo de Teste</h3>";

    $empresaTeste = Empresa::first();
    $usuarioTeste = EmpresaUsuario::first();

    if ($empresaTeste && $usuarioTeste) {
        echo "Empresa: {$empresaTeste->nome_fantasia} (ID: {$empresaTeste->id})<br>";
        echo "Usuário: {$usuarioTeste->nome} (ID: {$usuarioTeste->id})<br>";

        // Verificar se já existe
        $vinculoExiste = DB::table('empresa_user_vinculos')
            ->where('empresa_id', $empresaTeste->id)
            ->where('user_id', $usuarioTeste->id)
            ->exists();

        if (!$vinculoExiste) {
            DB::table('empresa_user_vinculos')->insert([
                'empresa_id' => $empresaTeste->id,
                'user_id' => $usuarioTeste->id,
                'perfil' => 'proprietario',
                'status' => 'ativo',
                'permissoes' => json_encode(['*']),
                'data_vinculo' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Vínculo criado!<br>";
        } else {
            echo "✅ Vínculo já existe<br>";
        }

        // Testar após criação
        $empresaTeste = Empresa::find($empresaTeste->id); // Recarregar
        $usuariosVinculados = $empresaTeste->usuariosVinculados;
        echo "Usuários vinculados após criação: {$usuariosVinculados->count()}<br>";
    }
} catch (Exception $e) {
    echo "<h3 style='color:red'>ERRO FATAL</h3>";
    echo "Mensagem: {$e->getMessage()}<br>";
    echo "Arquivo: {$e->getFile()}:{$e->getLine()}<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br><a href='/comerciantes/empresas/2/usuarios'>🔄 Testar página de usuários</a>";
echo " | <a href='/debug/empresa/2/usuarios'>🔍 Debug sem auth</a>";

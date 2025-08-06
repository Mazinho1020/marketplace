<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Configurar conexão com o banco
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'marketplace',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔍 Verificando permissão 'empresas.gerenciar'...\n\n";

try {
    // 1. Verificar se a permissão existe
    $permissao = Capsule::table('empresa_permissoes')
        ->where('nome', 'empresas.gerenciar')
        ->first();

    if ($permissao) {
        echo "✅ Permissão encontrada:\n";
        echo "   ID: {$permissao->id}\n";
        echo "   Nome: {$permissao->nome}\n";
        echo "   Descrição: {$permissao->descricao}\n\n";
    } else {
        echo "❌ Permissão 'empresas.gerenciar' NÃO encontrada!\n\n";

        // Vamos ver que permissões de empresas existem
        echo "📋 Permissões de empresas existentes:\n";
        $empresasPermissoes = Capsule::table('empresa_permissoes')
            ->where('nome', 'like', 'empresas.%')
            ->get();

        foreach ($empresasPermissoes as $perm) {
            echo "   - {$perm->nome} (ID: {$perm->id})\n";
        }
        echo "\n";
    }

    // 2. Verificar o usuário mazinho1@gmail.com
    $usuario = Capsule::table('empresa_usuarios')
        ->where('email', 'mazinho1@gmail.com')
        ->first();

    if ($usuario) {
        echo "👤 Usuário encontrado:\n";
        echo "   ID: {$usuario->id}\n";
        echo "   Nome: {$usuario->nome}\n";
        echo "   Email: {$usuario->email}\n";
        echo "   Empresa ID: {$usuario->empresa_id}\n\n";

        // 3. Verificar permissões do usuário
        $permissoesUsuario = Capsule::table('empresa_usuario_permissoes as eup')
            ->join('empresa_permissoes as ep', 'eup.permissao_id', '=', 'ep.id')
            ->where('eup.usuario_id', $usuario->id)
            ->where('ep.nome', 'like', 'empresas.%')
            ->select('ep.nome', 'ep.id as permissao_id')
            ->get();

        echo "🔑 Permissões de empresas do usuário:\n";
        if ($permissoesUsuario->count() > 0) {
            foreach ($permissoesUsuario as $perm) {
                echo "   ✅ {$perm->nome} (ID: {$perm->permissao_id})\n";
            }
        } else {
            echo "   ❌ Nenhuma permissão de empresas encontrada!\n";
        }
        echo "\n";
    } else {
        echo "❌ Usuário 'mazinho1@gmail.com' não encontrado!\n\n";
    }

    // 4. Se a permissão não existe, vamos criá-la
    if (!$permissao) {
        echo "🔧 Criando permissão 'empresas.gerenciar'...\n";

        $permissaoId = Capsule::table('empresa_permissoes')->insertGetId([
            'nome' => 'empresas.gerenciar',
            'descricao' => 'Gerenciar configurações da empresa',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        echo "✅ Permissão criada com ID: $permissaoId\n\n";

        // 5. Atribuir a permissão ao usuário
        if ($usuario) {
            $existe = Capsule::table('empresa_usuario_permissoes')
                ->where('usuario_id', $usuario->id)
                ->where('permissao_id', $permissaoId)
                ->exists();

            if (!$existe) {
                Capsule::table('empresa_usuario_permissoes')->insert([
                    'usuario_id' => $usuario->id,
                    'permissao_id' => $permissaoId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                echo "✅ Permissão atribuída ao usuário!\n";
            } else {
                echo "ℹ️ Usuário já possui esta permissão.\n";
            }
        }
    }

    echo "\n🎯 Verificação completa!\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

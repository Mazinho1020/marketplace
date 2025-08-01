<?php
require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== DEBUG LOGIN ISSUE ===\n\n";

    // 1. Testar conexão básica
    echo "1. Testando conexão com banco...\n";
    $connection = DB::connection();
    $dbName = $connection->getDatabaseName();
    echo "   ✓ Conectado ao banco: {$dbName}\n\n";

    // 2. Verificar se tabela empresa_usuarios existe
    echo "2. Verificando tabela empresa_usuarios...\n";
    $tables = DB::select("SHOW TABLES LIKE 'empresa_usuarios'");
    if (empty($tables)) {
        echo "   ✗ Tabela empresa_usuarios NÃO EXISTE!\n";
        exit(1);
    }
    echo "   ✓ Tabela empresa_usuarios existe\n\n";

    // 3. Verificar estrutura da tabela
    echo "3. Estrutura da tabela empresa_usuarios:\n";
    $columns = DB::select("DESCRIBE empresa_usuarios");
    $hasTipoId = false;
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
        if ($column->Field === 'tipo_id') {
            $hasTipoId = true;
        }
    }

    if (!$hasTipoId) {
        echo "\n   ⚠️  PROBLEMA: Campo 'tipo_id' NÃO EXISTE na tabela!\n";
    }

    // 4. Verificar se tabela empresa_usuario_tipos existe
    echo "\n4. Verificando tabela empresa_usuario_tipos...\n";
    $typesTables = DB::select("SHOW TABLES LIKE 'empresa_usuario_tipos'");
    if (empty($typesTables)) {
        echo "   ✗ Tabela empresa_usuario_tipos NÃO EXISTE!\n";
    } else {
        echo "   ✓ Tabela empresa_usuario_tipos existe\n";
    }

    // 5. Testar query do login (sem o JOIN problemático)
    echo "\n5. Testando query de login simplificada...\n";
    $email = 'mazinho@gmail.com'; // Email do arquivo SQL

    $usuario = DB::table('empresa_usuarios')
        ->where('email', $email)
        ->whereNull('deleted_at')
        ->first();

    if ($usuario) {
        echo "   ✓ Usuário encontrado: {$usuario->nome}\n";
        echo "   - Email: {$usuario->email}\n";
        echo "   - Status: {$usuario->status}\n";
        echo "   - Empresa ID: {$usuario->empresa_id}\n";

        // Verificar se tem campo tipo_id
        if (property_exists($usuario, 'tipo_id')) {
            echo "   - Tipo ID: " . ($usuario->tipo_id ?? 'NULL') . "\n";
        } else {
            echo "   ⚠️  Campo tipo_id não existe no resultado\n";
        }
    } else {
        echo "   ✗ Usuário não encontrado\n";
    }

    // 6. Testar query com LEFT JOIN (como no código original)
    echo "\n6. Testando query com LEFT JOIN...\n";

    try {
        $usuarioComTipo = DB::table('empresa_usuarios as u')
            ->leftJoin('empresa_usuario_tipos as t', 'u.tipo_id', '=', 't.id')
            ->select('u.*', 't.codigo as tipo_codigo', 't.nome as tipo_nome', 't.nivel_acesso')
            ->where('u.email', $email)
            ->whereNull('u.deleted_at')
            ->first();

        if ($usuarioComTipo) {
            echo "   ✓ Query com JOIN executada com sucesso\n";
        } else {
            echo "   ⚠️  Query executada mas não retornou resultado\n";
        }
    } catch (Exception $e) {
        echo "   ✗ ERRO na query com JOIN: " . $e->getMessage() . "\n";
        echo "   📋 Esse é provavelmente o erro que está causando o problema no login!\n";
    }

    echo "\n=== FIM DEBUG ===\n";
} catch (Exception $e) {
    echo "ERRO GERAL: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

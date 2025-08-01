<?php
// Teste de conexão de banco
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== TESTE DE CONEXÃO ===\n";

    // Verificar conexão padrão
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();

    echo "Banco conectado: {$databaseName}\n";

    // Verificar se consegue executar uma query simples
    $result = DB::select('SELECT DATABASE() as db');
    echo "Query SELECT DATABASE(): " . $result[0]->db . "\n";

    // Verificar tabelas empresas
    if (DB::getSchemaBuilder()->hasTable('empresas')) {
        $count = DB::table('empresas')->count();
        echo "Tabela 'empresas' encontrada: {$count} registros\n";
    } else {
        echo "Tabela 'empresas' NÃO encontrada\n";
    }

    // Verificar tabelas empresa_usuarios
    if (DB::getSchemaBuilder()->hasTable('empresa_usuarios')) {
        $count = DB::table('empresa_usuarios')->count();
        echo "Tabela 'empresa_usuarios' encontrada: {$count} registros\n";
    } else {
        echo "Tabela 'empresa_usuarios' NÃO encontrada\n";
    }

    echo "\n=== TESTE DE LOGIN ===\n";

    // Testar busca de usuário (simulando login)
    $testEmail = 'admin@teste.com';
    $user = DB::table('empresa_usuarios')
        ->where('email', $testEmail)
        ->first();

    if ($user) {
        echo "Usuário encontrado: {$user->email} (Empresa: {$user->empresa_id})\n";
    } else {
        echo "Usuário '{$testEmail}' não encontrado\n";
    }

    echo "\n=== CONFIGURAÇÕES ===\n";
    echo "DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
    echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
    echo "DB_HOST: " . env('DB_HOST') . "\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

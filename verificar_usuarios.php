<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USUARIOS COMERCIANTES ===\n";

$comerciantes = App\Comerciantes\Models\EmpresaUsuario::all();

foreach ($comerciantes as $comerciante) {
    echo "ID: {$comerciante->id}\n";
    echo "Email: {$comerciante->email}\n";
    echo "Nome: {$comerciante->nome}\n";
    echo "Empresa ID: {$comerciante->empresa_id}\n";
    echo "---\n";
}

echo "\n=== VERIFICAR TABELAS DE PERMISSAO ===\n";

// Verificar quais tabelas de permissão existem
$tables = ['empresa_permissoes', 'empresa_usuario_permissoes', 'empresa_papeis', 'empresa_papel_permissoes'];

foreach ($tables as $table) {
    try {
        $count = Illuminate\Support\Facades\DB::table($table)->count();
        echo "Tabela '{$table}': {$count} registros\n";
    } catch (Exception $e) {
        echo "Tabela '{$table}': NÃO EXISTE\n";
    }
}

// Pegar o primeiro usuário para dar todas as permissões
$primeiroUsuario = $comerciantes->first();
if ($primeiroUsuario) {
    echo "\n=== DANDO TODAS AS PERMISSOES PARA: {$primeiroUsuario->nome} ===\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Estrutura da tabela funforcli:\n";
$columns = DB::select('SHOW COLUMNS FROM funforcli');

echo "\nVerificando campos importantes:\n";
$hasAtivo = collect($columns)->contains('Field', 'ativo');
$hasDeletedAt = collect($columns)->contains('Field', 'deleted_at');

echo $hasAtivo ? "✓ Campo 'ativo' encontrado" : "✗ Campo 'ativo' NÃO encontrado";
echo "\n";
echo $hasDeletedAt ? "✓ Campo 'deleted_at' encontrado (soft deletes)" : "✗ Campo 'deleted_at' NÃO encontrado";
echo "\n";

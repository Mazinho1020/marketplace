<?php
require_once __DIR__ . '/vendor/autoload.php';

echo "=== LIMPEZA COMPLETA DE ARQUIVOS OBSOLETOS ===\n\n";

// 1. Modelos obsoletos a serem removidos
$models_to_remove = [
    'app/Models/Fidelidade/ProgramaFidelidade.php',
    'app/Models/Fidelidade/CartaoFidelidade.php',
    'app/Models/Fidelidade/TransacaoPontos.php',
    'app/Models/Fidelidade/FichaTecnicaCategoria.php'
];

echo "1. REMOVENDO MODELOS OBSOLETOS...\n";
foreach ($models_to_remove as $model) {
    $full_path = __DIR__ . '/' . $model;
    if (file_exists($full_path)) {
        echo "Removendo: $model\n";
        unlink($full_path);
        echo "✅ Removido\n";
    } else {
        echo "⚠️  Não encontrado: $model\n";
    }
}

// 2. Arquivos de migração remanescentes
echo "\n2. PROCURANDO MIGRAÇÕES REMANESCENTES...\n";
$migration_dirs = [
    'database/migrations',
    'database/migrations/fidelidade'
];

$migration_patterns = [
    'programas_fidelidade',
    'cartoes_fidelidade',
    'transacoes_pontos',
    'ficha_tecnica_categorias'
];

foreach ($migration_dirs as $dir) {
    $full_dir = __DIR__ . '/' . $dir;
    if (is_dir($full_dir)) {
        $files = glob($full_dir . '/*.php');
        foreach ($files as $file) {
            $filename = basename($file);
            foreach ($migration_patterns as $pattern) {
                if (strpos($filename, $pattern) !== false) {
                    echo "Removendo migração: " . str_replace(__DIR__ . '/', '', $file) . "\n";
                    unlink($file);
                    echo "✅ Removido\n";
                }
            }
        }
    }
}

// 3. Arquivos temporários de verificação
echo "\n3. REMOVENDO ARQUIVOS TEMPORÁRIOS...\n";
$temp_files = [
    'check_all_fidelidade_tables.php',
    'check_columns.php',
    'check_programas_structure.php',
    'final_verification.php',
    'remove_unused_tables.php',
    'remove_migration_files.php',
    'final_system_verification.php'
];

foreach ($temp_files as $temp_file) {
    $full_path = __DIR__ . '/' . $temp_file;
    if (file_exists($full_path)) {
        echo "Removendo: $temp_file\n";
        unlink($full_path);
        echo "✅ Removido\n";
    }
}

echo "\n=== VERIFICANDO CONTROLLER ===\n";

// 4. Verificar se o FidelidadeController precisa ser atualizado
$controller_path = __DIR__ . '/app/Http/Controllers/FidelidadeController.php';
if (file_exists($controller_path)) {
    $content = file_get_contents($controller_path);

    // Verificar se ainda tem referências às tabelas removidas
    $obsolete_references = [
        'programas_fidelidade',
        'cartoes_fidelidade',
        'transacoes_pontos'
    ];

    $needs_update = false;
    foreach ($obsolete_references as $ref) {
        if (strpos($content, $ref) !== false) {
            $needs_update = true;
            echo "⚠️  Encontrada referência obsoleta: $ref\n";
        }
    }

    if ($needs_update) {
        echo "❌ FidelidadeController precisa ser atualizado\n";
    } else {
        echo "✅ FidelidadeController está limpo\n";
    }
}

echo "\n=== LIMPEZA CONCLUÍDA ===\n";

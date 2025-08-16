<?php

echo "=== ANÁLISE DA ESTRUTURA ATUAL ===\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=meufinanceiro;charset=utf8mb4", 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Conectado ao banco: meufinanceiro\n\n";
    
    // 1. Verificar tabelas existentes
    echo "1. TABELAS EXISTENTES:\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        if (str_contains($table, 'lancament') || str_contains($table, 'pagament')) {
            echo "   - $table\n";
        }
    }
    
    // 2. Estrutura da tabela pagamentos
    echo "\n2. ESTRUTURA DA TABELA PAGAMENTOS:\n";
    $columns = $pdo->query("DESCRIBE pagamentos")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "   {$col['Field']} - {$col['Type']} - {$col['Key']} - {$col['Extra']}\n";
    }
    
    // 3. Foreign keys da tabela pagamentos
    echo "\n3. FOREIGN KEYS DA TABELA PAGAMENTOS:\n";
    $fks = $pdo->query("
        SELECT 
            CONSTRAINT_NAME, 
            COLUMN_NAME, 
            REFERENCED_TABLE_NAME, 
            REFERENCED_COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'meufinanceiro' 
        AND TABLE_NAME = 'pagamentos' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($fks as $fk) {
        echo "   {$fk['CONSTRAINT_NAME']}: {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
    }
    
    // 4. Verificar se existe tabela lancamentos antiga
    echo "\n4. ESTRUTURA ATUAL DE LANÇAMENTOS (se existir):\n";
    try {
        $columns = $pdo->query("DESCRIBE lancamentos")->fetchAll(PDO::FETCH_ASSOC);
        echo "   Tabela lancamentos EXISTS:\n";
        foreach ($columns as $col) {
            echo "     {$col['Field']} - {$col['Type']}\n";
        }
    } catch (PDOException $e) {
        echo "   Tabela lancamentos NÃO EXISTE\n";
    }
    
    // 5. Verificar backup
    echo "\n5. VERIFICAR BACKUP:\n";
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM lancamentos_backup")->fetchColumn();
        echo "   lancamentos_backup: $count registros\n";
        
        $sample = $pdo->query("SELECT * FROM lancamentos_backup LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($sample) {
            echo "   Campos disponíveis: " . implode(', ', array_keys($sample)) . "\n";
        }
    } catch (PDOException $e) {
        echo "   lancamentos_backup NÃO EXISTE\n";
    }
    
    // 6. Dados da tabela pagamentos
    echo "\n6. AMOSTRA DA TABELA PAGAMENTOS:\n";
    $sample = $pdo->query("SELECT * FROM pagamentos LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($sample) {
        echo "   Campos: " . implode(', ', array_keys($sample)) . "\n";
        echo "   Exemplo lancamento_id: " . ($sample['lancamento_id'] ?? 'NÃO TEM') . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

?>

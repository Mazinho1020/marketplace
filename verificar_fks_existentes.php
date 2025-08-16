<?php

echo "🔍 VERIFICANDO ESTRUTURA COMPLETA DO BANCO\n\n";

// Configuração do banco
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE $database");
    
    echo "✅ Conectado ao banco $database\n\n";
    
    // 1. Verificar se lancamento_itens existe
    $tables = $pdo->query("SHOW TABLES LIKE 'lancamento_itens'")->fetchAll();
    
    if (count($tables) > 0) {
        echo "📋 TABELA LANCAMENTO_ITENS EXISTE\n";
        
        // Ver estrutura da tabela
        $structure = $pdo->query("DESCRIBE lancamento_itens")->fetchAll(PDO::FETCH_ASSOC);
        echo "\n📊 ESTRUTURA DA TABELA:\n";
        foreach ($structure as $col) {
            echo "   {$col['Field']}: {$col['Type']} {$col['Null']} {$col['Key']} {$col['Default']} {$col['Extra']}\n";
        }
        
        // Ver foreign keys
        $fks = $pdo->query("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = '$database' 
            AND TABLE_NAME = 'lancamento_itens'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\n🔗 FOREIGN KEYS EXISTENTES:\n";
        if (count($fks) > 0) {
            foreach ($fks as $fk) {
                echo "   {$fk['CONSTRAINT_NAME']}: {$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
            }
        } else {
            echo "   Nenhuma FK encontrada\n";
        }
        
        // Ver CREATE TABLE completo
        $createTable = $pdo->query("SHOW CREATE TABLE lancamento_itens")->fetch(PDO::FETCH_ASSOC);
        echo "\n📋 CREATE TABLE COMPLETO:\n";
        echo $createTable['Create Table'] . "\n\n";
        
    } else {
        echo "❌ TABELA LANCAMENTO_ITENS NÃO EXISTE\n";
    }
    
    // 2. Verificar todas as FKs que referenciam lancamentos
    echo "🔍 TODAS AS FKs QUE REFERENCIAM 'lancamentos':\n";
    $allFks = $pdo->query("
        SELECT 
            TABLE_NAME,
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = '$database' 
        AND REFERENCED_TABLE_NAME = 'lancamentos'
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($allFks) > 0) {
        foreach ($allFks as $fk) {
            echo "   {$fk['TABLE_NAME']}.{$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']} ({$fk['CONSTRAINT_NAME']})\n";
        }
    } else {
        echo "   Nenhuma FK referenciando 'lancamentos' encontrada\n";
    }
    
    echo "\n🔍 VERIFICANDO QUAIS TABELAS REFERENCIAM 'lancamento_id':\n";
    $lancamentoRefs = $pdo->query("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            DATA_TYPE,
            IS_NULLABLE,
            COLUMN_DEFAULT,
            EXTRA
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = '$database' 
        AND COLUMN_NAME LIKE '%lancamento%'
        ORDER BY TABLE_NAME
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($lancamentoRefs as $ref) {
        echo "   {$ref['TABLE_NAME']}.{$ref['COLUMN_NAME']}: {$ref['DATA_TYPE']} {$ref['IS_NULLABLE']} {$ref['EXTRA']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

?>

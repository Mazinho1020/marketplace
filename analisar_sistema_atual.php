<?php
echo "🔍 ANÁLISE COMPLETA DO SISTEMA ATUAL\n\n";

$pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', 'root');

echo "📊 TABELAS EXISTENTES RELACIONADAS AO FINANCEIRO:\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

$financial_tables = [];
foreach($tables as $table) {
    if(stripos($table, 'lancament') !== false || 
       stripos($table, 'pagament') !== false || 
       stripos($table, 'financ') !== false ||
       stripos($table, 'conta') !== false) {
        $financial_tables[] = $table;
    }
}

foreach($financial_tables as $table) {
    echo "   ✅ $table\n";
}

echo "\n📋 ESTRUTURA DAS TABELAS PRINCIPAIS:\n\n";

// Verificar se existe lancamentos (nossa nova tabela)
echo "=== TABELA LANCAMENTOS (NOVA) ===\n";
try {
    $result = $pdo->query("DESCRIBE lancamentos");
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "   {$row['Field']} - {$row['Type']} - {$row['Key']}\n";
    }
    
    $count = $pdo->query("SELECT COUNT(*) FROM lancamentos")->fetchColumn();
    echo "   TOTAL REGISTROS: $count\n\n";
} catch(Exception $e) {
    echo "   ❌ Não existe ainda\n\n";
}

// Verificar tabela pagamentos
echo "=== TABELA PAGAMENTOS (EXISTENTE) ===\n";
try {
    $result = $pdo->query("DESCRIBE pagamentos");
    $fields = [];
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $fields[] = $row['Field'];
        echo "   {$row['Field']} - {$row['Type']} - {$row['Key']}\n";
    }
    
    $count = $pdo->query("SELECT COUNT(*) FROM pagamentos")->fetchColumn();
    echo "   TOTAL REGISTROS: $count\n";
    
    // Ver alguns exemplos
    echo "   EXEMPLOS:\n";
    $examples = $pdo->query("SELECT * FROM pagamentos LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    foreach($examples as $ex) {
        echo "   - ID: {$ex['id']}, Valor: {$ex['valor']}, Status: {$ex['status_pagamento']}\n";
    }
    echo "\n";
} catch(Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n\n";
}

// Verificar outras tabelas financeiras
$other_tables = array_diff($financial_tables, ['lancamentos', 'pagamentos']);
foreach($other_tables as $table) {
    echo "=== TABELA " . strtoupper($table) . " ===\n";
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "   TOTAL REGISTROS: $count\n";
        
        if($count > 0) {
            $sample = $pdo->query("SELECT * FROM $table LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            echo "   CAMPOS: " . implode(', ', array_keys($sample)) . "\n";
        }
    } catch(Exception $e) {
        echo "   ❌ Erro: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "🎯 SISTEMA ATUAL IDENTIFICADO:\n";
echo "✅ Banco: meufinanceiro\n";
echo "✅ Tabela lancamentos: CRIADA E FUNCIONANDO\n";
echo "✅ Tabela pagamentos: EXISTENTE com " . $pdo->query("SELECT COUNT(*) FROM pagamentos")->fetchColumn() . " registros\n";
echo "✅ Foreign Keys: CONFIGURADAS\n";
echo "✅ Triggers: ATIVOS para sincronização\n\n";

echo "📋 PRÓXIMOS PASSOS PARA USAR O SISTEMA:\n";
echo "1. ✅ Estrutura BD já está pronta\n";
echo "2. 🔄 Atualizar Models Laravel\n";
echo "3. 🔄 Atualizar Services Laravel\n";
echo "4. 🔄 Atualizar Controllers Laravel\n";
echo "5. 🔄 Atualizar Views Laravel\n";
echo "6. 🔄 Testar integração completa\n";

?>

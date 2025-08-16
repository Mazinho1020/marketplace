<?php
echo "🧪 TESTE RÁPIDO DO SISTEMA\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', 'root');
    echo "✅ Conexão BD: OK\n";
    
    // Verificar se estrutura está ok
    $count_lancamentos = $pdo->query("SELECT COUNT(*) FROM lancamentos")->fetchColumn();
    $count_pagamentos = $pdo->query("SELECT COUNT(*) FROM pagamentos")->fetchColumn();
    
    echo "✅ Lançamentos: $count_lancamentos registros\n";
    echo "✅ Pagamentos: $count_pagamentos registros\n";
    
    // Testar um lançamento simples
    $sql = "INSERT INTO lancamentos (empresa_id, usuario_id, natureza_financeira, categoria, valor_bruto, descricao, data_emissao, data_competencia, data_vencimento, usuario_criacao) VALUES (1, 1, 'saida', 'compra', 100.00, 'Teste rápido', CURDATE(), CURDATE(), CURDATE(), 1)";
    
    $result = $pdo->exec($sql);
    if ($result) {
        $id = $pdo->lastInsertId();
        echo "✅ Lançamento teste criado: ID $id\n";
        
        // Verificar cálculos automáticos
        $lancamento = $pdo->query("SELECT valor_liquido, valor_saldo, situacao_financeira FROM lancamentos WHERE id = $id")->fetch();
        echo "✅ Valor líquido: {$lancamento['valor_liquido']}\n";
        echo "✅ Situação: {$lancamento['situacao_financeira']}\n";
        
        echo "\n🎉 SISTEMA BÁSICO FUNCIONANDO!\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
?>

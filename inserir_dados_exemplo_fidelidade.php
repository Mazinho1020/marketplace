<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Inserindo dados de exemplo no sistema de fidelidade...\n\n";

    // 1. Programa de Fidelidade
    $pdo->exec("
        INSERT INTO fidelidade_programas (id, empresa_id, nome, descricao, tipo, regra_pontos_real, regra_real_pontos, pontos_minimo_resgate, cashback_percentual, status, data_inicio) 
        VALUES (1, 1, 'Programa VIP', 'Programa de fidelidade premium', 'hibrido', 1.00, 100.00, 100, 2.50, 'ativo', CURDATE())
        ON DUPLICATE KEY UPDATE nome = VALUES(nome)
    ");
    echo "✅ Programa de fidelidade criado\n";

    // 2. Cartão de Fidelidade 
    $pdo->exec("
        INSERT INTO fidelidade_cartoes (id, programa_id, cliente_id, numero_cartao, tipo, nivel_atual, pontos_totais, xp_atual, status, data_ativacao) 
        VALUES (1, 1, 1, 'CARD001', 'virtual', 'ouro', 2500, 750, 'ativo', NOW())
        ON DUPLICATE KEY UPDATE numero_cartao = VALUES(numero_cartao)
    ");
    echo "✅ Cartão de fidelidade criado\n";

    // 3. Carteira
    $pdo->exec("
        INSERT INTO fidelidade_carteiras (id, cartao_id, saldo_pontos, saldo_cashback, saldo_creditos, xp_total, nivel_atual, xp_proximo_nivel, data_ultima_movimentacao) 
        VALUES (1, 1, 2500, 45.75, 25.00, 750, 'ouro', 1000, NOW())
        ON DUPLICATE KEY UPDATE saldo_pontos = VALUES(saldo_pontos)
    ");
    echo "✅ Carteira criada\n";

    // 4. Regra de Cashback
    $pdo->exec("
        INSERT INTO fidelidade_cashback_regras (id, programa_id, nome, descricao, tipo, valor, valor_minimo_compra, status, data_inicio) 
        VALUES (1, 1, 'Cashback Padrão', 'Cashback de 2.5% em todas as compras', 'percentual', 2.50, 10.00, 'ativo', CURDATE())
        ON DUPLICATE KEY UPDATE nome = VALUES(nome)
    ");
    echo "✅ Regra de cashback criada\n";

    // 5. Transações de Cashback
    $pdo->exec("
        INSERT INTO fidelidade_cashback_transacoes (carteira_id, regra_id, tipo, valor_transacao, valor_cashback, percentual_aplicado, saldo_anterior, saldo_posterior, status, motivo) 
        VALUES 
        (1, 1, 'credito', 150.00, 3.75, 2.50, 42.00, 45.75, 'confirmado', 'Compra realizada hoje'),
        (1, 1, 'credito', 80.00, 2.00, 2.50, 40.00, 42.00, 'confirmado', 'Compra realizada ontem')
    ");
    echo "✅ Transações de cashback criadas\n";

    // 6. Conquistas
    $pdo->exec("
        INSERT INTO fidelidade_conquistas (id, programa_id, nome, descricao, tipo_requisito, valor_requisito, unidade_requisito, recompensa_tipo, recompensa_valor, status) 
        VALUES 
        (1, 1, 'Primeira Compra', 'Realize sua primeira compra', 'compras_quantidade', 1, 'compras', 'pontos', 100, 'ativo'),
        (2, 1, 'Comprador Fiel', 'Realize 10 compras', 'compras_quantidade', 10, 'compras', 'credito', 50.00, 'ativo'),
        (3, 1, 'Grande Comprador', 'Gaste R$ 1000 no total', 'compras_valor', 1000.00, 'reais', 'cashback', 25.00, 'ativo')
        ON DUPLICATE KEY UPDATE nome = VALUES(nome)
    ");
    echo "✅ Conquistas criadas\n";

    // 7. Conquistas do Cliente
    $pdo->exec("
        INSERT INTO fidelidade_cliente_conquistas (cartao_id, conquista_id, progresso_atual, progresso_necessario, status, data_conquista) 
        VALUES 
        (1, 1, 1, 1, 'conquistado', NOW()),
        (1, 2, 7, 10, 'em_progresso', NOW()),
        (1, 3, 450.00, 1000.00, 'em_progresso', NOW())
    ");
    echo "✅ Conquistas do cliente criadas\n";

    // 8. Cupons
    $pdo->exec("
        INSERT INTO fidelidade_cupons (id, programa_id, codigo, nome, descricao, tipo_desconto, valor, valor_minimo_compra, quantidade_total, limite_uso_cliente, data_inicio, data_fim, status) 
        VALUES 
        (1, 1, 'BEMVINDO10', 'Cupom de Boas-vindas', 'Desconto de 10% na primeira compra', 'percentual', 10.00, 50.00, 100, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'ativo'),
        (2, 1, 'FIDELIDADE20', 'Cupom Fidelidade', 'Desconto de 20% para clientes fiéis', 'percentual', 20.00, 100.00, 50, 2, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 'ativo'),
        (3, 1, 'DESCONTO50', 'Desconto Fixo', 'R$ 50 de desconto em compras acima de R$ 200', 'fixo', 50.00, 200.00, 25, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'ativo')
        ON DUPLICATE KEY UPDATE nome = VALUES(nome)
    ");
    echo "✅ Cupons criados\n";

    // 9. Uso de Cupons
    $pdo->exec("
        INSERT INTO fidelidade_cupons_uso (cupom_id, cliente_id, empresa_id, pedido_id, valor_desconto_aplicado, data_uso) 
        VALUES (1, 1, 1, 1, 5.50, NOW())
        ON DUPLICATE KEY UPDATE valor_desconto_aplicado = VALUES(valor_desconto_aplicado)
    ");
    echo "✅ Uso de cupom registrado\n";

    // 10. Créditos
    $pdo->exec("
        INSERT INTO fidelidade_creditos (carteira_id, tipo, valor, descricao, origem, data_vencimento, status) 
        VALUES 
        (1, 'bonus', 25.00, 'Bônus de boas-vindas', 'Sistema', DATE_ADD(CURDATE(), INTERVAL 365 DAY), 'ativo'),
        (1, 'promocao', 10.00, 'Promoção especial de aniversário', 'Admin', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'ativo')
    ");
    echo "✅ Créditos adicionais criados\n";

    echo "\n=== DADOS DE EXEMPLO INSERIDOS COM SUCESSO! ===\n";
    echo "✅ 1 Programa de fidelidade\n";
    echo "✅ 1 Cartão virtual ativo\n";
    echo "✅ 1 Carteira com saldo\n";
    echo "✅ 1 Regra de cashback ativa\n";
    echo "✅ 2 Transações de cashback\n";
    echo "✅ 3 Conquistas disponíveis\n";
    echo "✅ 3 Progresso de conquistas\n";
    echo "✅ 3 Cupons ativos\n";
    echo "✅ 1 Uso de cupom\n";
    echo "✅ 2 Créditos adicionais\n";

    echo "\nO sistema está pronto para uso!\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}

<?php

echo "=== TESTE DAS FORMAS DE PAGAMENTO ===\n";

try {
    // Conectar usando PDO
    $pdo = new PDO(
        'mysql:host=localhost;dbname=meufinanceiro;charset=utf8mb4',
        'root',
        'root'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar formas de pagamento ativas
    $sql = "SELECT id, nome, gateway_method FROM formas_pagamento WHERE ativo = 1 ORDER BY nome";
    $stmt = $pdo->query($sql);
    $formas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "📋 Formas de pagamento no banco:\n";
    foreach ($formas as $forma) {
        echo "  • ID: {$forma['id']} | Nome: {$forma['nome']} | Gateway: " . ($forma['gateway_method'] ?? 'N/A') . "\n";
    }

    echo "\n✅ Total: " . count($formas) . " formas ativas\n";

    // Criar JSON para teste
    echo "\n📤 JSON que deveria ser retornado pela API:\n";
    echo json_encode($formas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";

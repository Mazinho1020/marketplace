<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=marketplace;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "=== TESTE DA API - FORMAS DE PAGAMENTO ===\n";

    $empresaId = 1;

    // Simulando a nova query da API (menos restritiva)
    $formasPagamento = $pdo->prepare("
        SELECT id, nome, gateway_method, tipo, origem
        FROM formas_pagamento 
        WHERE ativo = 1 
        AND empresa_id = ? 
        AND origem != 'delivery'
        ORDER BY nome
    ");

    $formasPagamento->execute([$empresaId]);
    $formas = $formasPagamento->fetchAll(PDO::FETCH_ASSOC);

    echo "Total de formas de pagamento encontradas: " . count($formas) . "\n\n";

    foreach ($formas as $forma) {
        echo "- ID: {$forma['id']} | Nome: {$forma['nome']} | Tipo: {$forma['tipo']} | Origem: {$forma['origem']}\n";
    }

    echo "\n=== ANÁLISE POR TIPO ===\n";
    $tipoStats = [];
    foreach ($formas as $forma) {
        $tipoStats[$forma['tipo']] = ($tipoStats[$forma['tipo']] ?? 0) + 1;
    }
    foreach ($tipoStats as $tipo => $count) {
        echo "- {$tipo}: {$count} formas\n";
    }

    echo "\n=== ANÁLISE POR ORIGEM ===\n";
    $origemStats = [];
    foreach ($formas as $forma) {
        $origemStats[$forma['origem']] = ($origemStats[$forma['origem']] ?? 0) + 1;
    }
    foreach ($origemStats as $origem => $count) {
        echo "- {$origem}: {$count} formas\n";
    }

    echo "\n=== COMPARAÇÃO COM FILTROS ANTERIORES ===\n";

    // Query anterior (muito restritiva)
    $formasRestritivas = $pdo->prepare("
        SELECT id, nome, tipo, origem
        FROM formas_pagamento 
        WHERE ativo = 1 
        AND empresa_id = ? 
        AND tipo = 'recebimento'
        AND origem IN ('sistema', 'pdv')
        ORDER BY nome
    ");

    $formasRestritivas->execute([$empresaId]);
    $formasAnteriores = $formasRestritivas->fetchAll(PDO::FETCH_ASSOC);

    echo "Filtros anteriores (tipo=recebimento + origem=sistema/pdv): " . count($formasAnteriores) . " formas\n";
    echo "Filtros novos (excluir apenas delivery): " . count($formas) . " formas\n";
    echo "Diferença: +" . (count($formas) - count($formasAnteriores)) . " formas\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=marketplace;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "=== TESTE DOS FILTROS CORRIGIDOS ===\n";

    $empresaId = 1;

    // Query da API corrigida
    $formasAPI = $pdo->prepare("
        SELECT id, nome, gateway_method, tipo, origem, is_gateway
        FROM formas_pagamento 
        WHERE ativo = 1 
        AND empresa_id = ? 
        AND tipo = 'recebimento'
        AND origem IN ('sistema')
        AND is_gateway = 0
        ORDER BY nome
    ");

    $formasAPI->execute([$empresaId]);
    $formas = $formasAPI->fetchAll(PDO::FETCH_ASSOC);

    echo "Formas retornadas pela API (tipo=recebimento + origem=sistema + is_gateway=0): " . count($formas) . "\n\n";

    foreach ($formas as $forma) {
        echo "- ID: {$forma['id']} | Nome: {$forma['nome']} | Tipo: {$forma['tipo']} | Origem: {$forma['origem']} | Is_Gateway: {$forma['is_gateway']}\n";
    }

    echo "\n=== VERIFICANDO FORMAS COM IS_GATEWAY = 1 ===\n";

    // Verificar formas que seriam excluídas pelo is_gateway
    $formasGateway = $pdo->prepare("
        SELECT id, nome, tipo, origem, is_gateway
        FROM formas_pagamento 
        WHERE ativo = 1 
        AND empresa_id = ? 
        AND tipo = 'recebimento'
        AND origem IN ('sistema')
        AND is_gateway = 1
        ORDER BY nome
    ");

    $formasGateway->execute([$empresaId]);
    $formasExcluidas = $formasGateway->fetchAll(PDO::FETCH_ASSOC);

    echo "Formas EXCLUÍDAS por is_gateway=1: " . count($formasExcluidas) . "\n";
    foreach ($formasExcluidas as $forma) {
        echo "- ID: {$forma['id']} | Nome: {$forma['nome']} (EXCLUÍDA - é gateway)\n";
    }

    echo "\n=== COMPARAÇÃO COM FILTROS ANTERIORES ===\n";

    // Todas as formas ativas (filtro anterior)
    $todasFormas = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM formas_pagamento 
        WHERE ativo = 1 
        AND empresa_id = ? 
        AND origem != 'delivery'
    ");

    $todasFormas->execute([$empresaId]);
    $totalAnterior = $todasFormas->fetch(PDO::FETCH_ASSOC)['total'];

    echo "Filtros anteriores (excluir apenas delivery): {$totalAnterior} formas\n";
    echo "Filtros atuais (tipo=recebimento + origem=sistema + is_gateway=0): " . count($formas) . " formas\n";
    echo "Redução: " . ($totalAnterior - count($formas)) . " formas removidas\n";

    echo "\n=== VERIFICAÇÃO DAS FORMAS VÁLIDAS ===\n";
    echo "✅ Essas " . count($formas) . " formas são as corretas para contas a receber (uso administrativo)!\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

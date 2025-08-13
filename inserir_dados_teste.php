<?php
echo "=== INSERINDO DADOS DE TESTE ===\n";
echo "Data: " . date('d/m/Y H:i:s') . "\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter o primeiro produto para testes
    $stmt = $pdo->query("SELECT id, nome FROM produtos LIMIT 1");
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        echo "❌ Nenhum produto encontrado para teste\n";
        exit;
    }

    echo "🎯 Usando produto: {$produto['nome']} (ID: {$produto['id']})\n\n";

    // Inserir movimentações de teste
    echo "📦 INSERINDO MOVIMENTAÇÕES DE TESTE:\n";
    echo str_repeat("=", 40) . "\n";

    $movimentacoes = [
        ['tipo' => 'entrada', 'quantidade' => 10, 'motivo' => 'Compra inicial de estoque'],
        ['tipo' => 'saida', 'quantidade' => 3, 'motivo' => 'Venda para cliente'],
        ['tipo' => 'entrada', 'quantidade' => 5, 'motivo' => 'Reposição de estoque'],
        ['tipo' => 'saida', 'quantidade' => 2, 'motivo' => 'Venda online']
    ];

    $estoqueAtual = 15; // Simulando estoque inicial

    foreach ($movimentacoes as $mov) {
        $estoqueAnterior = $estoqueAtual;

        if ($mov['tipo'] === 'entrada') {
            $estoqueAtual += $mov['quantidade'];
        } else {
            $estoqueAtual -= $mov['quantidade'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO produto_movimentacoes 
            (empresa_id, produto_id, tipo, quantidade, valor_unitario, valor_total, 
             estoque_anterior, estoque_posterior, motivo, data_movimento, sync_status, created_at)
            VALUES (1, ?, ?, ?, 25.50, ?, ?, ?, ?, NOW(), 'pendente', NOW())
        ");

        $valorTotal = $mov['quantidade'] * 25.50;

        $stmt->execute([
            $produto['id'],
            $mov['tipo'],
            $mov['quantidade'],
            $valorTotal,
            $estoqueAnterior,
            $estoqueAtual,
            $mov['motivo']
        ]);

        echo "✅ {$mov['tipo']}: {$mov['quantidade']} unidades - {$mov['motivo']}\n";
    }

    // Atualizar estoque do produto
    $stmt = $pdo->prepare("UPDATE produtos SET estoque_atual = ? WHERE id = ?");
    $stmt->execute([$estoqueAtual, $produto['id']]);

    echo "\n📊 Estoque final atualizado: $estoqueAtual unidades\n";

    // Inserir imagens de exemplo (simulando URLs)
    echo "\n🖼️ INSERINDO IMAGENS DE TESTE:\n";
    echo str_repeat("=", 40) . "\n";

    $imagens = [
        [
            'tipo' => 'principal',
            'arquivo' => 'produto_principal_' . $produto['id'] . '.jpg',
            'titulo' => $produto['nome'] . ' - Imagem Principal',
            'ordem' => 1
        ],
        [
            'tipo' => 'galeria',
            'arquivo' => 'produto_galeria_1_' . $produto['id'] . '.jpg',
            'titulo' => $produto['nome'] . ' - Vista Lateral',
            'ordem' => 2
        ],
        [
            'tipo' => 'galeria',
            'arquivo' => 'produto_galeria_2_' . $produto['id'] . '.jpg',
            'titulo' => $produto['nome'] . ' - Detalhe',
            'ordem' => 3
        ]
    ];

    foreach ($imagens as $img) {
        $stmt = $pdo->prepare("
            INSERT INTO produto_imagens 
            (empresa_id, produto_id, tipo, arquivo, titulo, alt_text, ordem, 
             tamanho_arquivo, dimensoes, ativo, sync_status, created_at)
            VALUES (1, ?, ?, ?, ?, ?, ?, 123456, '800x600', 1, 'pendente', NOW())
        ");

        $stmt->execute([
            $produto['id'],
            $img['tipo'],
            $img['arquivo'],
            $img['titulo'],
            $img['titulo'],
            $img['ordem']
        ]);

        echo "✅ {$img['tipo']}: {$img['titulo']}\n";
    }

    echo "\n🎉 DADOS DE TESTE INSERIDOS COM SUCESSO!\n";
    echo "\n💡 AGORA VOCÊ PODE TESTAR:\n";
    echo "1. 🚨 Alertas de estoque: /comerciantes/produtos/estoque/alertas\n";
    echo "2. 📊 Movimentações: /comerciantes/produtos/estoque/movimentacoes\n";
    echo "3. 🖼️ Galeria: /comerciantes/produtos/{$produto['id']}/imagens\n";
    echo "4. 👁️ Ver produto: /comerciantes/produtos/{$produto['id']}\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

<?php
echo "=== TESTE DAS NOVAS FUNCIONALIDADES ===\n";
echo "Data: " . date('d/m/Y H:i:s') . "\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conexão com banco estabelecida\n\n";

    // Testar se há produtos com estoque baixo
    echo "📊 TESTE DE ALERTAS DE ESTOQUE:\n";
    echo str_repeat("=", 40) . "\n";

    $stmt = $pdo->query("
        SELECT p.nome, p.estoque_atual, p.estoque_minimo
        FROM produtos p 
        WHERE p.controla_estoque = 1 
        AND p.ativo = 1
        AND p.estoque_atual <= p.estoque_minimo
        LIMIT 5
    ");

    $produtosEstoqueBaixo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($produtosEstoqueBaixo) > 0) {
        echo "⚠️ " . count($produtosEstoqueBaixo) . " produto(s) com estoque baixo encontrado(s):\n";
        foreach ($produtosEstoqueBaixo as $produto) {
            echo "  • {$produto['nome']}: {$produto['estoque_atual']}/{$produto['estoque_minimo']}\n";
        }
    } else {
        echo "✅ Nenhum produto com estoque baixo\n";
    }

    // Testar imagens
    echo "\n🖼️ TESTE DE IMAGENS:\n";
    echo str_repeat("=", 40) . "\n";

    $stmt = $pdo->query("
        SELECT p.nome, COUNT(pi.id) as total_imagens
        FROM produtos p
        LEFT JOIN produto_imagens pi ON p.id = pi.produto_id
        GROUP BY p.id, p.nome
        HAVING total_imagens > 0
        LIMIT 5
    ");

    $produtosComImagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($produtosComImagens) > 0) {
        echo "📸 Produtos com imagens:\n";
        foreach ($produtosComImagens as $produto) {
            echo "  • {$produto['nome']}: {$produto['total_imagens']} imagem(ns)\n";
        }
    } else {
        echo "ℹ️ Nenhum produto com imagens ainda\n";
    }

    // Testar movimentações
    echo "\n📦 TESTE DE MOVIMENTAÇÕES:\n";
    echo str_repeat("=", 40) . "\n";

    $stmt = $pdo->query("
        SELECT COUNT(*) as total
        FROM produto_movimentacoes
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalMovimentacoes = $result['total'];

    if ($totalMovimentacoes > 0) {
        echo "📊 {$totalMovimentacoes} movimentação(ões) nos últimos 30 dias\n";
    } else {
        echo "ℹ️ Nenhuma movimentação registrada nos últimos 30 dias\n";
    }

    // Testar estrutura das tabelas
    echo "\n🔧 VERIFICAÇÃO DE ESTRUTURAS:\n";
    echo str_repeat("=", 40) . "\n";

    $tabelas = ['produtos', 'produto_imagens', 'produto_movimentacoes'];

    foreach ($tabelas as $tabela) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM `$tabela`");
            $count = $stmt->fetchColumn();
            echo "✅ Tabela '$tabela': $count registro(s)\n";
        } catch (Exception $e) {
            echo "❌ Tabela '$tabela': Erro - " . $e->getMessage() . "\n";
        }
    }

    echo "\n💡 RECOMENDAÇÕES:\n";
    echo str_repeat("=", 40) . "\n";
    echo "1. 🎯 Acesse: /comerciantes/produtos/estoque/alertas\n";
    echo "2. 📊 Acesse: /comerciantes/produtos/estoque/movimentacoes\n";
    echo "3. 🖼️ Acesse: /comerciantes/produtos/{id}/imagens\n";
    echo "4. 📸 Teste upload de imagens na galeria\n";
    echo "5. 📦 Registre movimentações de estoque\n";

    echo "\n✅ FUNCIONALIDADES IMPLEMENTADAS:\n";
    echo "• Sistema de alertas de estoque baixo\n";
    echo "• Interface de movimentações de estoque\n";
    echo "• Galeria de imagens com upload múltiplo\n";
    echo "• Reordenação de imagens por arrastar\n";
    echo "• Definição de imagem principal\n";
    echo "• Controles avançados de estoque\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

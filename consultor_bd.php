<?php

/**
 * CONSULTOR INTERATIVO DE BANCO DE DADOS
 * Execute este script e me informe o resultado para análises
 */

echo "=== CONSULTOR INTERATIVO - MARKETPLACE ===\n";
echo "Data: " . date('d/m/Y H:i:s') . "\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Menu de opções
    echo "ESCOLHA UMA CONSULTA:\n";
    echo "1. Ver todos os produtos\n";
    echo "2. Produtos com estoque baixo\n";
    echo "3. Movimentações recentes\n";
    echo "4. Galeria de imagens\n";
    echo "5. Estatísticas gerais\n";
    echo "6. Estrutura das tabelas\n";
    echo "7. Consulta personalizada\n";
    echo "\nDigite o número da opção (1-7): ";

    // Se estiver executando via linha de comando
    if (php_sapi_name() === 'cli') {
        $opcao = trim(fgets(STDIN));
    } else {
        // Para execução via web (se necessário)
        $opcao = $_GET['opcao'] ?? '5';
    }

    switch ($opcao) {
        case '1':
            echo "\n📦 TODOS OS PRODUTOS:\n";
            echo str_repeat("=", 50) . "\n";
            $stmt = $pdo->query("
                SELECT p.id, p.nome, p.sku, p.preco_venda, p.estoque_atual,
                       pc.nome as categoria, pm.nome as marca
                FROM produtos p
                LEFT JOIN produto_categorias pc ON p.categoria_id = pc.id
                LEFT JOIN produto_marcas pm ON p.marca_id = pm.id
                ORDER BY p.nome
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "ID: {$row['id']} | {$row['nome']} | SKU: {$row['sku']}\n";
                echo "  Preço: R$ {$row['preco_venda']} | Estoque: {$row['estoque_atual']}\n";
                echo "  Categoria: {$row['categoria']} | Marca: {$row['marca']}\n\n";
            }
            break;

        case '2':
            echo "\n🚨 PRODUTOS COM ESTOQUE BAIXO:\n";
            echo str_repeat("=", 50) . "\n";
            $stmt = $pdo->query("
                SELECT nome, sku, estoque_atual, estoque_minimo
                FROM produtos 
                WHERE controla_estoque = 1 
                AND estoque_atual <= estoque_minimo
                ORDER BY estoque_atual ASC
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "⚠️  {$row['nome']} ({$row['sku']})\n";
                echo "    Atual: {$row['estoque_atual']} | Mínimo: {$row['estoque_minimo']}\n\n";
            }
            break;

        case '3':
            echo "\n📊 MOVIMENTAÇÕES RECENTES:\n";
            echo str_repeat("=", 50) . "\n";
            $stmt = $pdo->query("
                SELECT pm.*, p.nome as produto_nome
                FROM produto_movimentacoes pm
                JOIN produtos p ON pm.produto_id = p.id
                ORDER BY pm.created_at DESC
                LIMIT 10
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tipo = $row['tipo'] === 'entrada' ? '⬆️' : '⬇️';
                echo "{$tipo} {$row['produto_nome']}\n";
                echo "    Qtd: {$row['quantidade']} | Motivo: {$row['motivo']}\n";
                echo "    Data: {$row['created_at']}\n\n";
            }
            break;

        case '4':
            echo "\n🖼️ GALERIA DE IMAGENS:\n";
            echo str_repeat("=", 50) . "\n";
            $stmt = $pdo->query("
                SELECT p.nome, pi.tipo, pi.arquivo, pi.titulo
                FROM produtos p
                JOIN produto_imagens pi ON p.id = pi.produto_id
                ORDER BY p.nome, pi.ordem
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "📸 {$row['nome']} | {$row['tipo']}\n";
                echo "    Arquivo: {$row['arquivo']}\n";
                echo "    Título: {$row['titulo']}\n\n";
            }
            break;

        case '5':
            echo "\n📈 ESTATÍSTICAS GERAIS:\n";
            echo str_repeat("=", 50) . "\n";

            // Contadores
            $stats = [
                'produtos' => $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn(),
                'categorias' => $pdo->query("SELECT COUNT(*) FROM produto_categorias")->fetchColumn(),
                'marcas' => $pdo->query("SELECT COUNT(*) FROM produto_marcas")->fetchColumn(),
                'imagens' => $pdo->query("SELECT COUNT(*) FROM produto_imagens")->fetchColumn(),
                'movimentacoes' => $pdo->query("SELECT COUNT(*) FROM produto_movimentacoes")->fetchColumn(),
                'estoque_baixo' => $pdo->query("SELECT COUNT(*) FROM produtos WHERE controla_estoque=1 AND estoque_atual<=estoque_minimo")->fetchColumn(),
            ];

            foreach ($stats as $item => $count) {
                echo "📊 " . ucfirst($item) . ": $count\n";
            }
            break;

        case '6':
            echo "\n🔧 ESTRUTURA DAS TABELAS:\n";
            echo str_repeat("=", 50) . "\n";
            $tables = ['produtos', 'produto_categorias', 'produto_marcas', 'produto_imagens', 'produto_movimentacoes'];

            foreach ($tables as $table) {
                echo "\n📋 Tabela: $table\n";
                $stmt = $pdo->query("DESCRIBE $table");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "  {$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']}\n";
                }
            }
            break;

        case '7':
            echo "\n🔍 CONSULTA PERSONALIZADA:\n";
            echo "Cole sua consulta SQL aqui e execute o script novamente\n";
            echo "Exemplo: SELECT * FROM produtos WHERE ativo = 1;\n";
            break;

        default:
            echo "Opção inválida. Execute novamente e escolha 1-7.\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "💡 Para nova consulta, execute: php consultor_bd.php\n";
echo "📋 Para análise, copie e cole o resultado para mim!\n";

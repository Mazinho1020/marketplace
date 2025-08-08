<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CRIANDO DADOS BÁSICOS PARA PRODUTOS ===\n\n";

    // 1. Criar categorias básicas se não existirem
    echo "1. Verificando categorias...\n";
    $categorias = [
        ['nome' => 'Alimentação', 'descricao' => 'Produtos alimentícios', 'slug' => 'alimentacao', 'icone' => 'fas fa-utensils', 'cor' => '#28a745'],
        ['nome' => 'Bebidas', 'descricao' => 'Bebidas em geral', 'slug' => 'bebidas', 'icone' => 'fas fa-glass-cheers', 'cor' => '#007bff'],
        ['nome' => 'Serviços', 'descricao' => 'Serviços oferecidos', 'slug' => 'servicos', 'icone' => 'fas fa-tools', 'cor' => '#ffc107'],
        ['nome' => 'Produtos', 'descricao' => 'Produtos diversos', 'slug' => 'produtos', 'icone' => 'fas fa-box', 'cor' => '#6f42c1']
    ];

    foreach ($categorias as $cat) {
        $stmt = $pdo->prepare("SELECT id FROM produto_categorias WHERE slug = ? AND empresa_id = 1");
        $stmt->execute([$cat['slug']]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO produto_categorias (empresa_id, nome, descricao, slug, icone, cor, ordem, ativo, created_at, updated_at, sync_status)
                VALUES (1, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW(), 'pendente')
            ");
            $stmt->execute([$cat['nome'], $cat['descricao'], $cat['slug'], $cat['icone'], $cat['cor'], array_search($cat, $categorias) + 1]);
            echo "   - Categoria '{$cat['nome']}' criada\n";
        } else {
            echo "   - Categoria '{$cat['nome']}' já existe\n";
        }
    }

    // 2. Criar marcas básicas
    echo "\n2. Verificando marcas...\n";
    $marcas = [
        ['nome' => 'Marca Própria', 'descricao' => 'Produtos da casa'],
        ['nome' => 'Fornecedor Padrão', 'descricao' => 'Produtos de fornecedores diversos']
    ];

    foreach ($marcas as $marca) {
        $stmt = $pdo->prepare("SELECT id FROM produto_marcas WHERE nome = ? AND empresa_id = 1");
        $stmt->execute([$marca['nome']]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO produto_marcas (empresa_id, nome, descricao, ativo, created_at, updated_at, sync_status)
                VALUES (1, ?, ?, 1, NOW(), NOW(), 'pendente')
            ");
            $stmt->execute([$marca['nome'], $marca['descricao']]);
            echo "   - Marca '{$marca['nome']}' criada\n";
        } else {
            echo "   - Marca '{$marca['nome']}' já existe\n";
        }
    }

    // 3. Verificar se temos produtos de exemplo
    echo "\n3. Verificando produtos...\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produtos WHERE empresa_id = 1");
    $stmt->execute();
    $total = $stmt->fetch()['total'];

    if ($total == 0) {
        echo "   - Nenhum produto encontrado. Criando produtos de exemplo...\n";

        // Buscar IDs das categorias e marcas
        $stmt = $pdo->prepare("SELECT id FROM produto_categorias WHERE empresa_id = 1 ORDER BY id LIMIT 4");
        $stmt->execute();
        $categorias_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $pdo->prepare("SELECT id FROM produto_marcas WHERE empresa_id = 1 ORDER BY id LIMIT 1");
        $stmt->execute();
        $marca_id = $stmt->fetchColumn();

        $produtos_exemplo = [
            [
                'nome' => 'Pizza Margherita',
                'categoria_id' => $categorias_ids[0] ?? 1,
                'marca_id' => $marca_id,
                'preco_venda' => 25.90,
                'tipo' => 'produto',
                'descricao' => 'Pizza tradicional com molho de tomate, mussarela e manjericão',
                'controla_estoque' => 0
            ],
            [
                'nome' => 'Refrigerante Cola 350ml',
                'categoria_id' => $categorias_ids[1] ?? 1,
                'marca_id' => $marca_id,
                'preco_venda' => 4.50,
                'tipo' => 'produto',
                'descricao' => 'Refrigerante sabor cola 350ml',
                'controla_estoque' => 1,
                'estoque_atual' => 50,
                'estoque_minimo' => 10
            ],
            [
                'nome' => 'Consulta Técnica',
                'categoria_id' => $categorias_ids[2] ?? 1,
                'marca_id' => $marca_id,
                'preco_venda' => 100.00,
                'tipo' => 'servico',
                'descricao' => 'Consulta técnica especializada',
                'controla_estoque' => 0
            ]
        ];

        foreach ($produtos_exemplo as $produto) {
            $stmt = $pdo->prepare("
                INSERT INTO produtos (
                    empresa_id, categoria_id, marca_id, tipo, nome, descricao, 
                    preco_venda, controla_estoque, estoque_atual, estoque_minimo,
                    status, status_venda, ativo, created_at, updated_at, sync_status
                ) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'disponivel', 'disponivel', 1, NOW(), NOW(), 'pendente')
            ");
            $stmt->execute([
                $produto['categoria_id'],
                $produto['marca_id'],
                $produto['tipo'],
                $produto['nome'],
                $produto['descricao'],
                $produto['preco_venda'],
                $produto['controla_estoque'],
                $produto['estoque_atual'] ?? null,
                $produto['estoque_minimo'] ?? null
            ]);
            echo "   - Produto '{$produto['nome']}' criado\n";
        }
    } else {
        echo "   - {$total} produtos já existem\n";
    }

    echo "\n=== DADOS BÁSICOS CONFIGURADOS COM SUCESSO! ===\n";
} catch (Exception $e) {
    echo 'ERRO: ' . $e->getMessage() . "\n";
}

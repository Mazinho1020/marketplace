<?php
// Criar tabelas de notificações específicas para produtos
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CRIANDO TABELAS DE NOTIFICAÇÕES PARA PRODUTOS ===\n\n";

    // 16. ALERTAS DE ESTOQUE
    echo "16. Criando tabela produto_alertas_estoque...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_alertas_estoque;
        CREATE TABLE produto_alertas_estoque (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            tipo_alerta enum('estoque_baixo','estoque_zerado','estoque_negativo','vencimento_proximo') NOT NULL,
            estoque_atual decimal(10,3) NOT NULL,
            estoque_minimo decimal(10,3) DEFAULT NULL,
            dias_vencimento int DEFAULT NULL,
            data_vencimento date DEFAULT NULL,
            prioridade enum('baixa','media','alta','critica') DEFAULT 'media',
            notificado tinyint(1) DEFAULT 0,
            data_notificacao timestamp NULL DEFAULT NULL,
            resolvido tinyint(1) DEFAULT 0,
            data_resolucao timestamp NULL DEFAULT NULL,
            observacoes text,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_variacao (variacao_id),
            KEY idx_tipo (tipo_alerta),
            KEY idx_prioridade (prioridade),
            KEY idx_notificado (notificado),
            KEY idx_resolvido (resolvido),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_alertas_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_alertas_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 17. AVALIAÇÕES DE PRODUTOS
    echo "17. Criando tabela produto_avaliacoes...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_avaliacoes;
        CREATE TABLE produto_avaliacoes (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            cliente_pessoa_id bigint unsigned DEFAULT NULL,
            nome_cliente varchar(255) DEFAULT NULL,
            email_cliente varchar(255) DEFAULT NULL,
            nota int NOT NULL CHECK (nota >= 1 AND nota <= 5),
            titulo varchar(255) DEFAULT NULL,
            comentario text,
            recomenda tinyint(1) DEFAULT NULL,
            compra_verificada tinyint(1) DEFAULT 0,
            data_compra date DEFAULT NULL,
            aprovado tinyint(1) DEFAULT 0,
            data_aprovacao timestamp NULL DEFAULT NULL,
            resposta_empresa text,
            data_resposta timestamp NULL DEFAULT NULL,
            util_positivo int DEFAULT 0,
            util_negativo int DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_variacao (variacao_id),
            KEY idx_cliente (cliente_pessoa_id),
            KEY idx_nota (nota),
            KEY idx_aprovado (aprovado),
            KEY idx_data (created_at),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_avaliacoes_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_avaliacoes_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE,
            CONSTRAINT fk_avaliacoes_cliente FOREIGN KEY (cliente_pessoa_id) REFERENCES pessoas (id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 18. WISHLIST
    echo "18. Criando tabela produto_wishlist...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_wishlist;
        CREATE TABLE produto_wishlist (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            cliente_pessoa_id bigint unsigned NOT NULL,
            quantidade decimal(10,3) DEFAULT 1.000,
            preco_desejado decimal(10,2) DEFAULT NULL,
            notificar_preco tinyint(1) DEFAULT 0,
            notificar_disponibilidade tinyint(1) DEFAULT 1,
            observacoes text,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uk_cliente_produto (cliente_pessoa_id, produto_id, variacao_id),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_variacao (variacao_id),
            KEY idx_cliente (cliente_pessoa_id),
            KEY idx_notificar (notificar_preco, notificar_disponibilidade),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_wishlist_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_wishlist_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE,
            CONSTRAINT fk_wishlist_cliente FOREIGN KEY (cliente_pessoa_id) REFERENCES pessoas (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 19. ESTATÍSTICAS DE PRODUTOS
    echo "19. Criando tabela produto_estatisticas...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_estatisticas;
        CREATE TABLE produto_estatisticas (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            periodo_tipo enum('diario','semanal','mensal','anual') NOT NULL,
            data_periodo date NOT NULL,
            visualizacoes int DEFAULT 0,
            vendas_quantidade decimal(10,3) DEFAULT 0.000,
            vendas_valor decimal(10,2) DEFAULT 0.00,
            devolucoes_quantidade decimal(10,3) DEFAULT 0.000,
            devolucoes_valor decimal(10,2) DEFAULT 0.00,
            avaliacoes_total int DEFAULT 0,
            avaliacoes_media decimal(3,2) DEFAULT NULL,
            wishlist_adicoes int DEFAULT 0,
            carrinho_abandonos int DEFAULT 0,
            conversao_vendas decimal(5,2) DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uk_produto_periodo (produto_id, variacao_id, periodo_tipo, data_periodo),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_variacao (variacao_id),
            KEY idx_periodo (periodo_tipo, data_periodo),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_estatisticas_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_estatisticas_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 20. INVENTÁRIOS
    echo "20. Criando tabela produto_inventarios...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_inventarios;
        CREATE TABLE produto_inventarios (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            nome varchar(255) NOT NULL,
            descricao text,
            data_inicio date NOT NULL,
            data_fim date DEFAULT NULL,
            status enum('planejado','em_andamento','finalizado','cancelado') DEFAULT 'planejado',
            tipo enum('geral','parcial','ciclico','especial') DEFAULT 'geral',
            responsavel_id int DEFAULT NULL,
            observacoes text,
            total_produtos int DEFAULT 0,
            produtos_contados int DEFAULT 0,
            divergencias_encontradas int DEFAULT 0,
            valor_total_sistema decimal(12,2) DEFAULT 0.00,
            valor_total_contado decimal(12,2) DEFAULT 0.00,
            diferenca_valor decimal(12,2) DEFAULT 0.00,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_status (status),
            KEY idx_tipo (tipo),
            KEY idx_data_inicio (data_inicio),
            KEY idx_sync (sync_status, sync_data)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 21. ITENS DE INVENTÁRIO
    echo "21. Criando tabela produto_inventario_itens...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_inventario_itens;
        CREATE TABLE produto_inventario_itens (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            inventario_id int unsigned NOT NULL,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            estoque_sistema decimal(10,3) NOT NULL,
            estoque_contado decimal(10,3) DEFAULT NULL,
            diferenca decimal(10,3) DEFAULT NULL,
            valor_unitario decimal(10,2) DEFAULT NULL,
            valor_total_sistema decimal(10,2) DEFAULT NULL,
            valor_total_contado decimal(10,2) DEFAULT NULL,
            diferenca_valor decimal(10,2) DEFAULT NULL,
            observacoes text,
            usuario_contagem_id int DEFAULT NULL,
            data_contagem timestamp NULL DEFAULT NULL,
            ajuste_aplicado tinyint(1) DEFAULT 0,
            data_ajuste timestamp NULL DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uk_inventario_produto (inventario_id, produto_id, variacao_id),
            KEY idx_empresa (empresa_id),
            KEY idx_inventario (inventario_id),
            KEY idx_produto (produto_id),
            KEY idx_variacao (variacao_id),
            KEY idx_diferenca (diferenca),
            KEY idx_ajuste (ajuste_aplicado),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_inventario_itens_inventario FOREIGN KEY (inventario_id) REFERENCES produto_inventarios (id) ON DELETE CASCADE,
            CONSTRAINT fk_inventario_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_inventario_itens_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Tabelas de notificações e controle criadas com sucesso!\n\n";

    // Inserir algumas categorias padrão
    echo "22. Inserindo dados padrão...\n";

    $categoriaspadrao = [
        ['nome' => 'Alimentação e Bebidas', 'icone' => 'fas fa-utensils', 'cor' => '#FF6B35'],
        ['nome' => 'Roupas e Acessórios', 'icone' => 'fas fa-tshirt', 'cor' => '#4ECDC4'],
        ['nome' => 'Casa e Decoração', 'icone' => 'fas fa-home', 'cor' => '#45B7D1'],
        ['nome' => 'Eletrônicos', 'icone' => 'fas fa-laptop', 'cor' => '#9B59B6'],
        ['nome' => 'Saúde e Beleza', 'icone' => 'fas fa-heart', 'cor' => '#E74C3C'],
        ['nome' => 'Esporte e Lazer', 'icone' => 'fas fa-dumbbell', 'cor' => '#2ECC71'],
        ['nome' => 'Livros e Mídia', 'icone' => 'fas fa-book', 'cor' => '#F39C12'],
        ['nome' => 'Automotivo', 'icone' => 'fas fa-car', 'cor' => '#34495E'],
        ['nome' => 'Serviços', 'icone' => 'fas fa-cogs', 'cor' => '#95A5A6'],
        ['nome' => 'Outros', 'icone' => 'fas fa-boxes', 'cor' => '#BDC3C7']
    ];

    foreach ($categoriaspadrao as $cat) {
        $pdo->exec("
            INSERT IGNORE INTO produto_categorias (empresa_id, nome, slug, icone, cor, ordem, ativo)
            VALUES (0, '{$cat['nome']}', '" . strtolower(str_replace([' ', 'ã', 'õ'], ['-', 'a', 'o'], $cat['nome'])) . "', '{$cat['icone']}', '{$cat['cor']}', 0, 1)
        ");
    }

    // Inserir tipos de eventos de notificação para produtos
    $pdo->exec("
        INSERT IGNORE INTO notificacao_tipos_evento (nome, descricao, categoria, ativo) VALUES
        ('produto_estoque_baixo', 'Produto com estoque baixo', 'produtos', 1),
        ('produto_estoque_zerado', 'Produto com estoque zerado', 'produtos', 1),
        ('produto_vencimento_proximo', 'Produto próximo do vencimento', 'produtos', 1),
        ('produto_novo_cadastrado', 'Novo produto cadastrado', 'produtos', 1),
        ('produto_preco_alterado', 'Preço do produto alterado', 'produtos', 1),
        ('produto_nova_avaliacao', 'Nova avaliação de produto', 'produtos', 1),
        ('produto_inventario_divergencia', 'Divergência encontrada no inventário', 'produtos', 1)
    ");

    echo "Dados padrão inseridos com sucesso!\n\n";
    echo "=== SISTEMA DE PRODUTOS CRIADO COM SUCESSO! ===\n";
    echo "Total de tabelas criadas: 21\n";
    echo "- produtos\n";
    echo "- produto_categorias\n";
    echo "- produto_subcategorias\n";
    echo "- produto_marcas\n";
    echo "- produto_configuracoes\n";
    echo "- produto_configuracao_itens\n";
    echo "- produto_variacoes_combinacoes\n";
    echo "- produto_imagens\n";
    echo "- produto_movimentacoes\n";
    echo "- produto_historico_precos\n";
    echo "- produto_fornecedores\n";
    echo "- produto_precos_quantidade\n";
    echo "- produto_kits\n";
    echo "- produto_codigos_barras\n";
    echo "- produto_relacionados\n";
    echo "- produto_alertas_estoque\n";
    echo "- produto_avaliacoes\n";
    echo "- produto_wishlist\n";
    echo "- produto_estatisticas\n";
    echo "- produto_inventarios\n";
    echo "- produto_inventario_itens\n\n";
} catch (Exception $e) {
    echo 'ERRO: ' . $e->getMessage() . "\n";
    exit(1);
}

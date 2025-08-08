<?php
// Criar tabelas avançadas do sistema de produtos
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CRIANDO TABELAS AVANÇADAS DO SISTEMA DE PRODUTOS ===\n\n";

    // 7. VARIAÇÕES COMBINAÇÕES
    echo "7. Criando tabela produto_variacoes_combinacoes...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_variacoes_combinacoes;
        CREATE TABLE produto_variacoes_combinacoes (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            nome varchar(255) NOT NULL,
            sku varchar(100) DEFAULT NULL,
            codigo_barras varchar(255) DEFAULT NULL,
            configuracoes json NOT NULL,
            preco_adicional decimal(10,2) DEFAULT 0.00,
            preco_final decimal(10,2) NOT NULL,
            estoque_proprio decimal(10,3) DEFAULT NULL,
            imagem varchar(255) DEFAULT NULL,
            ativo tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_sku (sku),
            KEY idx_codigo_barras (codigo_barras),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_variacoes_combinacoes_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 8. IMAGENS DOS PRODUTOS
    echo "8. Criando tabela produto_imagens...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_imagens;
        CREATE TABLE produto_imagens (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            tipo enum('principal','galeria','miniatura','zoom') DEFAULT 'galeria',
            arquivo varchar(255) NOT NULL,
            titulo varchar(255) DEFAULT NULL,
            alt_text varchar(255) DEFAULT NULL,
            ordem int DEFAULT 0,
            tamanho_arquivo int DEFAULT NULL,
            dimensoes varchar(20) DEFAULT NULL,
            ativo tinyint(1) DEFAULT 1,
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
            KEY idx_tipo (tipo),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_imagens_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_imagens_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 9. MOVIMENTAÇÕES DE ESTOQUE
    echo "9. Criando tabela produto_movimentacoes...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_movimentacoes;
        CREATE TABLE produto_movimentacoes (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            tipo enum('entrada','saida','ajuste','venda','compra','devolucao','perda','transferencia') NOT NULL,
            quantidade decimal(10,3) NOT NULL,
            valor_unitario decimal(10,2) DEFAULT NULL,
            valor_total decimal(10,2) DEFAULT NULL,
            estoque_anterior decimal(10,3) NOT NULL,
            estoque_posterior decimal(10,3) NOT NULL,
            motivo varchar(255) DEFAULT NULL,
            observacoes text,
            documento varchar(100) DEFAULT NULL,
            fornecedor_id int DEFAULT NULL,
            cliente_id int DEFAULT NULL,
            usuario_id int DEFAULT NULL,
            data_movimento timestamp DEFAULT CURRENT_TIMESTAMP,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_variacao (variacao_id),
            KEY idx_tipo (tipo),
            KEY idx_data (data_movimento),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_movimentacoes_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_movimentacoes_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE,
            CONSTRAINT fk_movimentacoes_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES pessoas (id),
            CONSTRAINT fk_movimentacoes_cliente FOREIGN KEY (cliente_id) REFERENCES pessoas (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 10. HISTÓRICO DE PREÇOS
    echo "10. Criando tabela produto_historico_precos...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_historico_precos;
        CREATE TABLE produto_historico_precos (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            preco_compra_anterior decimal(10,2) DEFAULT NULL,
            preco_compra_novo decimal(10,2) DEFAULT NULL,
            preco_venda_anterior decimal(10,2) DEFAULT NULL,
            preco_venda_novo decimal(10,2) DEFAULT NULL,
            margem_anterior decimal(5,2) DEFAULT NULL,
            margem_nova decimal(5,2) DEFAULT NULL,
            motivo varchar(255) DEFAULT NULL,
            usuario_id int DEFAULT NULL,
            data_alteracao timestamp DEFAULT CURRENT_TIMESTAMP,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_variacao (variacao_id),
            KEY idx_data (data_alteracao),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_historico_precos_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_historico_precos_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 11. FORNECEDORES POR PRODUTO
    echo "11. Criando tabela produto_fornecedores...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_fornecedores;
        CREATE TABLE produto_fornecedores (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            fornecedor_pessoa_id int NOT NULL,
            codigo_fornecedor varchar(100) DEFAULT NULL,
            preco_compra decimal(10,2) DEFAULT NULL,
            prazo_entrega int DEFAULT NULL,
            quantidade_minima decimal(10,3) DEFAULT NULL,
            desconto_percentual decimal(5,2) DEFAULT NULL,
            principal tinyint(1) DEFAULT 0,
            ativo tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_produto (produto_id),
            KEY idx_fornecedor (fornecedor_pessoa_id),
            KEY idx_principal (principal),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_produto_fornecedores_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_produto_fornecedores_pessoa FOREIGN KEY (fornecedor_pessoa_id) REFERENCES pessoas (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 12. PREÇOS POR QUANTIDADE
    echo "12. Criando tabela produto_precos_quantidade...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_precos_quantidade;
        CREATE TABLE produto_precos_quantidade (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            variacao_id int unsigned DEFAULT NULL,
            quantidade_minima decimal(10,3) NOT NULL,
            quantidade_maxima decimal(10,3) DEFAULT NULL,
            preco decimal(10,2) NOT NULL,
            desconto_percentual decimal(5,2) DEFAULT NULL,
            ativo tinyint(1) DEFAULT 1,
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
            KEY idx_quantidade (quantidade_minima),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_precos_quantidade_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
            CONSTRAINT fk_precos_quantidade_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes_combinacoes (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Tabelas avançadas criadas com sucesso!\n\n";
} catch (Exception $e) {
    echo 'ERRO: ' . $e->getMessage() . "\n";
    exit(1);
}

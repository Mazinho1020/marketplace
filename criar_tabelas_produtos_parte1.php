<?php
// Criar todas as tabelas do sistema de produtos completo
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CRIANDO SISTEMA COMPLETO DE PRODUTOS ===\n\n";

    // 1. TABELA PRINCIPAL DE PRODUTOS
    echo "1. Criando tabela produtos...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produtos;
        CREATE TABLE produtos (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            categoria_id int unsigned DEFAULT NULL,
            subcategoria_id int unsigned DEFAULT NULL,
            marca_id int unsigned DEFAULT NULL,
            
            -- Identificação
            tipo enum('produto','insumo','complemento','servico','combo','kit') DEFAULT 'produto',
            possui_variacoes tinyint(1) DEFAULT 0,
            codigo_sistema varchar(50) DEFAULT NULL,
            nome varchar(255) NOT NULL,
            nome_reduzido varchar(100) DEFAULT NULL,
            slug varchar(255) DEFAULT NULL,
            sku varchar(100) DEFAULT NULL,
            codigo_fabricante varchar(100) DEFAULT NULL,
            
            -- Status
            status enum('disponivel','indisponivel','pausado','esgotado','novidade') DEFAULT 'disponivel',
            status_venda enum('disponivel','indisponivel','pausado','esgotado','novidade','promocao') DEFAULT 'disponivel',
            ativo tinyint(1) DEFAULT 1,
            
            -- Descrições
            descricao text,
            descricao_curta varchar(500) DEFAULT NULL,
            especificacoes_tecnicas text,
            ingredientes text,
            informacoes_nutricionais text,
            modo_uso text,
            cuidados text,
            
            -- Códigos fiscais
            codigo_barras varchar(255) DEFAULT NULL,
            gtin varchar(14) DEFAULT NULL,
            ncm varchar(10) DEFAULT NULL,
            cest varchar(10) DEFAULT NULL,
            origem varchar(1) DEFAULT '0',
            cfop varchar(4) DEFAULT NULL,
            
            -- Preços
            preco_compra decimal(10,2) DEFAULT 0.00,
            preco_venda decimal(10,2) NOT NULL DEFAULT 0.00,
            preco_promocional decimal(10,2) DEFAULT NULL,
            margem_lucro decimal(5,2) DEFAULT NULL,
            
            -- Estoque
            controla_estoque tinyint(1) DEFAULT 1,
            estoque_atual decimal(10,3) DEFAULT 0.000,
            estoque_minimo decimal(10,3) DEFAULT 0.000,
            estoque_maximo decimal(10,3) DEFAULT NULL,
            
            -- Medidas e peso
            unidade_medida varchar(50) DEFAULT 'UN',
            unidade_compra varchar(50) DEFAULT 'UN',
            fator_conversao decimal(10,4) DEFAULT 1.0000,
            peso_liquido decimal(10,3) DEFAULT NULL,
            peso_bruto decimal(10,3) DEFAULT NULL,
            altura decimal(10,2) DEFAULT NULL,
            largura decimal(10,2) DEFAULT NULL,
            profundidade decimal(10,2) DEFAULT NULL,
            volume decimal(10,3) DEFAULT NULL,
            
            -- Fiscais
            cst varchar(3) DEFAULT NULL,
            aliquota_icms decimal(5,2) DEFAULT NULL,
            aliquota_ipi decimal(5,2) DEFAULT NULL,
            aliquota_pis decimal(5,2) DEFAULT NULL,
            aliquota_cofins decimal(5,2) DEFAULT NULL,
            
            -- Meta-dados
            observacoes text,
            palavras_chave text,
            ordem_exibicao int DEFAULT 0,
            destaque tinyint(1) DEFAULT 0,
            
            -- Controle
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            
            -- Sincronização
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            
            PRIMARY KEY (id),
            KEY idx_empresa_status (empresa_id, status),
            KEY idx_categoria (categoria_id),
            KEY idx_codigo_barras (codigo_barras),
            KEY idx_sku (sku),
            KEY idx_nome (nome),
            KEY idx_sync (sync_status, sync_data)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 2. CATEGORIAS DE PRODUTOS
    echo "2. Criando tabela produto_categorias...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_categorias;
        CREATE TABLE produto_categorias (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            categoria_pai_id int unsigned DEFAULT NULL,
            nome varchar(255) NOT NULL,
            descricao text,
            slug varchar(255) DEFAULT NULL,
            icone varchar(100) DEFAULT NULL,
            cor varchar(7) DEFAULT NULL,
            imagem varchar(255) DEFAULT NULL,
            ordem int DEFAULT 0,
            ativo tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_categoria_pai (categoria_pai_id),
            KEY idx_nome (nome),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_categorias_pai FOREIGN KEY (categoria_pai_id) REFERENCES produto_categorias (id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 3. SUBCATEGORIAS
    echo "3. Criando tabela produto_subcategorias...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_subcategorias;
        CREATE TABLE produto_subcategorias (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            categoria_id int unsigned NOT NULL,
            nome varchar(255) NOT NULL,
            descricao text,
            slug varchar(255) DEFAULT NULL,
            icone varchar(100) DEFAULT NULL,
            ordem int DEFAULT 0,
            ativo tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_categoria (categoria_id),
            KEY idx_nome (nome),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_subcategorias_categoria FOREIGN KEY (categoria_id) REFERENCES produto_categorias (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 4. MARCAS
    echo "4. Criando tabela produto_marcas...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_marcas;
        CREATE TABLE produto_marcas (
            id int unsigned NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            nome varchar(255) NOT NULL,
            descricao text,
            logo varchar(255) DEFAULT NULL,
            site varchar(255) DEFAULT NULL,
            telefone varchar(20) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            ativo tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_nome (nome),
            KEY idx_sync (sync_status, sync_data)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 5. CONFIGURAÇÕES DE PRODUTOS
    echo "5. Criando tabela produto_configuracoes...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_configuracoes;
        CREATE TABLE produto_configuracoes (
            id int NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_id int unsigned NOT NULL,
            nome varchar(255) NOT NULL,
            descricao text,
            tipo_configuracao enum('tamanho','sabor','ingrediente','complemento','personalizado') DEFAULT 'personalizado',
            obrigatorio tinyint(1) DEFAULT 0,
            permite_multiplos tinyint(1) DEFAULT 0,
            qtd_minima int DEFAULT NULL,
            qtd_maxima int DEFAULT NULL,
            tipo_calculo enum('soma','media','maximo','substituicao') DEFAULT 'soma',
            ordem int DEFAULT 0,
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
            KEY idx_tipo (tipo_configuracao),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_configuracoes_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 6. ITENS DE CONFIGURAÇÃO
    echo "6. Criando tabela produto_configuracao_itens...\n";
    $pdo->exec("
        DROP TABLE IF EXISTS produto_configuracao_itens;
        CREATE TABLE produto_configuracao_itens (
            id int NOT NULL AUTO_INCREMENT,
            empresa_id int NOT NULL DEFAULT 0,
            produto_configuracao_id int NOT NULL,
            nome varchar(255) NOT NULL,
            descricao text,
            valor_adicional decimal(10,2) DEFAULT 0.00,
            imagem varchar(255) DEFAULT NULL,
            ordem int DEFAULT 0,
            disponivel tinyint(1) DEFAULT 1,
            padrao tinyint(1) DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            sync_data timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            sync_hash varchar(32) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_empresa (empresa_id),
            KEY idx_configuracao (produto_configuracao_id),
            KEY idx_disponivel (disponivel),
            KEY idx_sync (sync_status, sync_data),
            CONSTRAINT fk_itens_configuracao FOREIGN KEY (produto_configuracao_id) REFERENCES produto_configuracoes (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Tabelas básicas criadas com sucesso!\n\n";
    echo "Continuando...\n";
} catch (Exception $e) {
    echo 'ERRO: ' . $e->getMessage() . "\n";
    exit(1);
}

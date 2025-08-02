<?php
// Criar tabelas uma por uma
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Criando Tabelas Individualmente</h2>\n";

    // 1. Tabela configurações
    $sql1 = "CREATE TABLE IF NOT EXISTS afi_plan_configuracoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        empresa_id INT NOT NULL,
        chave VARCHAR(100) NOT NULL,
        valor TEXT,
        tipo ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
        descricao TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_empresa_chave (empresa_id, chave)
    )";
    $pdo->exec($sql1);
    echo "✅ afi_plan_configuracoes criada<br>\n";

    // 2. Tabela gateways
    $sql2 = "CREATE TABLE IF NOT EXISTS afi_plan_gateways (
        id INT AUTO_INCREMENT PRIMARY KEY,
        empresa_id INT NOT NULL,
        codigo VARCHAR(50) NOT NULL,
        nome VARCHAR(100) NOT NULL,
        provedor VARCHAR(50) NOT NULL,
        ambiente ENUM('sandbox', 'producao') DEFAULT 'sandbox',
        ativo TINYINT(1) DEFAULT 1,
        credenciais JSON,
        configuracoes JSON,
        url_webhook VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_empresa_codigo (empresa_id, codigo)
    )";
    $pdo->exec($sql2);
    echo "✅ afi_plan_gateways criada<br>\n";

    // 3. Tabela planos
    $sql3 = "CREATE TABLE IF NOT EXISTS afi_plan_planos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        empresa_id INT NOT NULL,
        codigo VARCHAR(50) NOT NULL,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        preco_mensal DECIMAL(10,2) DEFAULT 0,
        preco_anual DECIMAL(10,2) DEFAULT 0,
        preco_vitalicio DECIMAL(10,2) DEFAULT 0,
        dias_trial INT DEFAULT 0,
        recursos JSON,
        limites JSON,
        ativo TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_empresa_codigo (empresa_id, codigo)
    )";
    $pdo->exec($sql3);
    echo "✅ afi_plan_planos criada<br>\n";

    // 4. Tabela assinaturas
    $sql4 = "CREATE TABLE IF NOT EXISTS afi_plan_assinaturas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        empresa_id INT NOT NULL,
        funforcli_id INT NOT NULL,
        plano_id INT NOT NULL,
        ciclo_cobranca ENUM('mensal', 'anual', 'vitalicio') DEFAULT 'mensal',
        valor DECIMAL(10,2) NOT NULL,
        status ENUM('trial', 'ativo', 'suspenso', 'expirado', 'cancelado') DEFAULT 'trial',
        trial_expira_em TIMESTAMP NULL,
        iniciado_em TIMESTAMP NULL,
        expira_em TIMESTAMP NULL,
        proxima_cobranca_em TIMESTAMP NULL,
        ultima_cobranca_em TIMESTAMP NULL,
        cancelado_em TIMESTAMP NULL,
        renovacao_automatica TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_funforcli (funforcli_id),
        INDEX idx_plano (plano_id),
        INDEX idx_status (status)
    )";
    $pdo->exec($sql4);
    echo "✅ afi_plan_assinaturas criada<br>\n";

    // 5. Tabela transações
    $sql5 = "CREATE TABLE IF NOT EXISTS afi_plan_transacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) NOT NULL,
        empresa_id INT NOT NULL,
        codigo_transacao VARCHAR(100) NOT NULL,
        cliente_id INT,
        gateway_id INT,
        gateway_transacao_id VARCHAR(255),
        tipo_origem ENUM('nova_assinatura', 'renovacao_assinatura', 'comissao_afiliado', 'venda_avulsa') DEFAULT 'venda_avulsa',
        id_origem INT,
        valor_original DECIMAL(10,2) NOT NULL,
        valor_desconto DECIMAL(10,2) DEFAULT 0,
        valor_taxas DECIMAL(10,2) DEFAULT 0,
        valor_final DECIMAL(10,2) NOT NULL,
        moeda VARCHAR(3) DEFAULT 'BRL',
        forma_pagamento VARCHAR(50),
        status ENUM('rascunho', 'pendente', 'processando', 'aprovado', 'recusado', 'cancelado', 'estornado') DEFAULT 'rascunho',
        gateway_status VARCHAR(50),
        cliente_nome VARCHAR(255),
        cliente_email VARCHAR(255),
        descricao TEXT,
        metadados JSON,
        expira_em TIMESTAMP NULL,
        processado_em TIMESTAMP NULL,
        aprovado_em TIMESTAMP NULL,
        cancelado_em TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_codigo_transacao (codigo_transacao),
        INDEX idx_status (status),
        INDEX idx_cliente (cliente_id),
        INDEX idx_gateway (gateway_id)
    )";
    $pdo->exec($sql5);
    echo "✅ afi_plan_transacoes criada<br>\n";

    // 6. Tabela vendas
    $sql6 = "CREATE TABLE IF NOT EXISTS afi_plan_vendas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        empresa_id INT NOT NULL,
        afiliado_id INT NOT NULL,
        cliente_id INT NOT NULL,
        assinatura_id INT,
        transacao_id INT,
        valor_venda DECIMAL(10,2) NOT NULL,
        taxa_comissao DECIMAL(5,2) NOT NULL,
        valor_comissao DECIMAL(10,2) NOT NULL,
        status ENUM('pendente', 'confirmado', 'cancelado', 'estornado') DEFAULT 'pendente',
        confirmado_em TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_afiliado (afiliado_id),
        INDEX idx_cliente (cliente_id),
        INDEX idx_status (status)
    )";
    $pdo->exec($sql6);
    echo "✅ afi_plan_vendas criada<br>\n";

    echo "<br><h3>🎉 Todas as tabelas criadas com sucesso!</h3>\n";
    echo "<p>Agora vou inserir dados básicos...</p>\n";

    // Inserir dados básicos
    $pdo->exec("INSERT IGNORE INTO afi_plan_gateways (empresa_id, codigo, nome, provedor, ambiente, ativo, credenciais, configuracoes) VALUES
        (1, 'pix_interno', 'PIX Interno', 'pix', 'producao', 1, '{}', '{}'),
        (1, 'boleto_interno', 'Boleto Interno', 'boleto', 'producao', 1, '{}', '{}')");

    $pdo->exec("INSERT IGNORE INTO afi_plan_planos (empresa_id, codigo, nome, descricao, preco_mensal, preco_anual, ativo) VALUES
        (1, 'basico', 'Plano Básico', 'Plano básico para iniciantes', 50.00, 500.00, 1),
        (1, 'premium', 'Plano Premium', 'Plano completo com todas as funcionalidades', 100.00, 1000.00, 1),
        (1, 'enterprise', 'Plano Enterprise', 'Plano para grandes empresas', 200.00, 2000.00, 1)");

    echo "✅ Dados básicos inseridos<br>\n";

    echo "<br><p><strong>✅ Sistema pronto para criar as views!</strong></p>\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

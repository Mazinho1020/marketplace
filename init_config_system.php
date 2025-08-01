<?php

/**
 * Script para inicializar sistema de configuração
 * Execute apenas após restaurar o backup da database
 */

echo "=== INICIANDO SISTEMA DE CONFIGURAÇÃO ===\n\n";

try {
    // Verificar se as tabelas de configuração existem
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conexão com banco estabelecida\n";

    // Verificar se tabelas de configuração existem
    $configTables = [
        'config_environments',
        'config_sites',
        'config_groups',
        'config_definitions',
        'config_values'
    ];

    $allTablesExist = true;
    foreach ($configTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);

        if ($stmt->rowCount() === 0) {
            echo "❌ Tabela '$table' não existe\n";
            $allTablesExist = false;
        } else {
            echo "✅ Tabela '$table' existe\n";
        }
    }

    if (!$allTablesExist) {
        echo "\n⚠️ Execute as migrações primeiro: php artisan migrate\n";
        exit(1);
    }

    echo "\n=== POPULANDO DADOS PADRÃO ===\n";

    // 1. Inserir ambientes padrão
    echo "Inserindo ambientes...\n";
    $pdo->exec("
        INSERT INTO config_environments 
        (empresa_id, codigo, nome, descricao, is_producao, ativo, created_at, updated_at)
        VALUES 
        (1, 'local', 'Local', 'Ambiente de desenvolvimento local', 0, 1, NOW(), NOW()),
        (1, 'production', 'Produção', 'Ambiente de produção', 1, 1, NOW(), NOW())
        ON DUPLICATE KEY UPDATE updated_at = NOW()
    ");
    echo "✅ Ambientes inseridos\n";

    // 2. Inserir sites padrão
    echo "Inserindo sites...\n";
    $pdo->exec("
        INSERT INTO config_sites 
        (empresa_id, codigo, nome, descricao, base_url_padrao, ativo, created_at, updated_at)
        VALUES 
        (1, 'marketplace', 'Marketplace', 'Sistema principal do marketplace', 'http://localhost:8000', 1, NOW(), NOW()),
        (1, 'pdv', 'PDV', 'Sistema de ponto de venda', 'http://localhost:8001', 1, NOW(), NOW())
        ON DUPLICATE KEY UPDATE updated_at = NOW()
    ");
    echo "✅ Sites inseridos\n";

    // 3. Inserir grupos padrão
    echo "Inserindo grupos...\n";
    $pdo->exec("
        INSERT INTO config_groups 
        (empresa_id, codigo, nome, descricao, icone_class, ordem, ativo, created_at, updated_at)
        VALUES 
        (1, 'sistema', 'Sistema', 'Configurações gerais do sistema', 'fas fa-cogs', 1, 1, NOW(), NOW()),
        (1, 'empresa', 'Empresa', 'Dados da empresa', 'fas fa-building', 2, 1, NOW(), NOW()),
        (1, 'fidelidade', 'Fidelidade', 'Configurações do sistema de fidelidade', 'fas fa-heart', 3, 1, NOW(), NOW()),
        (1, 'notificacao', 'Notificações', 'Configurações de notificações', 'fas fa-bell', 4, 1, NOW(), NOW())
        ON DUPLICATE KEY UPDATE updated_at = NOW()
    ");
    echo "✅ Grupos inseridos\n";

    // 4. Buscar IDs dos grupos
    $sistemaGroupId = $pdo->query("SELECT id FROM config_groups WHERE empresa_id = 1 AND codigo = 'sistema'")->fetchColumn();
    $empresaGroupId = $pdo->query("SELECT id FROM config_groups WHERE empresa_id = 1 AND codigo = 'empresa'")->fetchColumn();

    // 5. Inserir definições de configuração
    echo "Inserindo definições de configuração...\n";
    $stmt = $pdo->prepare("
        INSERT INTO config_definitions 
        (empresa_id, chave, nome, descricao, tipo, grupo_id, valor_padrao, obrigatorio, editavel, ativo, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE updated_at = NOW()
    ");

    $definitions = [
        [1, 'app.name', 'Nome da Aplicação', 'Nome principal da aplicação', 'string', $sistemaGroupId, 'Marketplace Multi-Tenant', 1, 1, 1],
        [1, 'app.version', 'Versão do Sistema', 'Versão atual do sistema', 'string', $sistemaGroupId, '1.0.0', 1, 0, 1],
        [1, 'empresa.nome', 'Nome da Empresa', 'Nome/Razão social da empresa', 'string', $empresaGroupId, 'Minha Empresa', 1, 1, 1],
        [1, 'empresa.cnpj', 'CNPJ da Empresa', 'CNPJ da empresa', 'string', $empresaGroupId, '00.000.000/0001-00', 0, 1, 1],
        [1, 'sistema.manutencao', 'Modo Manutenção', 'Sistema em manutenção', 'boolean', $sistemaGroupId, 'false', 0, 1, 1],
        [1, 'sistema.debug', 'Modo Debug', 'Exibir erros detalhados', 'boolean', $sistemaGroupId, 'true', 0, 1, 1],
    ];

    foreach ($definitions as $def) {
        $stmt->execute($def);
    }
    echo "✅ Definições inseridas\n";

    // 6. Buscar ambiente local
    $localEnvId = $pdo->query("SELECT id FROM config_environments WHERE empresa_id = 1 AND codigo = 'local'")->fetchColumn();

    // 7. Inserir valores padrão
    echo "Inserindo valores padrão...\n";
    $stmt = $pdo->prepare("
        INSERT INTO config_values 
        (empresa_id, config_id, ambiente_id, valor, created_at, updated_at)
        SELECT ?, cd.id, ?, ?, NOW(), NOW()
        FROM config_definitions cd 
        WHERE cd.empresa_id = ? AND cd.chave = ?
        ON DUPLICATE KEY UPDATE updated_at = NOW()
    ");

    $values = [
        ['app.name', 'Marketplace Multi-Tenant'],
        ['app.version', '1.0.0'],
        ['empresa.nome', 'Minha Empresa'],
        ['empresa.cnpj', '00.000.000/0001-00'],
        ['sistema.manutencao', 'false'],
        ['sistema.debug', 'true'],
    ];

    foreach ($values as [$chave, $valor]) {
        $stmt->execute([1, $localEnvId, $valor, 1, $chave]);
    }
    echo "✅ Valores padrão inseridos\n";

    echo "\n🎉 SISTEMA DE CONFIGURAÇÃO INICIALIZADO COM SUCESSO!\n";
    echo "\n📋 PRÓXIMOS PASSOS:\n";
    echo "1. ✅ Restaurar backup do banco (se ainda não fez)\n";
    echo "2. ✅ Sistema de configuração criado\n";
    echo "3. 🔄 Testar login do sistema\n";
    echo "4. ⚙️ Acessar painel de configurações\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}

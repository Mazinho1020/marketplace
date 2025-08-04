<?php

/**
 * Inserir dados iniciais do sistema de notificações
 */

// Configuração da conexão
$host = '127.0.0.1';
$port = 3306;
$database = 'meufinanceiro';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conectado ao MySQL Docker\n";

    echo "📋 Inserindo dados iniciais...\n\n";

    // 1. APLICAÇÕES PADRÃO
    echo "1️⃣ Inserindo aplicações padrão...\n";
    $aplicacoes = [
        [1, 'cliente', 'Cliente', 'Aplicação para clientes do marketplace', 'fas fa-user', '#28a745', true, 1],
        [1, 'empresa', 'Empresa', 'Aplicação para empresas vendedoras', 'fas fa-building', '#007bff', true, 2],
        [1, 'admin', 'Administrador', 'Painel administrativo do marketplace', 'fas fa-user-shield', '#6c757d', true, 3],
        [1, 'entregador', 'Entregador', 'Aplicação para entregadores', 'fas fa-truck', '#ffc107', true, 4],
        [1, 'fidelidade', 'Programa de Fidelidade', 'Sistema de pontos e fidelidade', 'fas fa-heart', '#e83e8c', true, 5]
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO notificacao_aplicacoes (empresa_id, codigo, nome, descricao, icone_classe, cor_hex, ativo, ordem_exibicao, sync_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    foreach ($aplicacoes as $app) {
        $stmt->execute($app);
        echo "  ✅ {$app[2]} ({$app[1]})\n";
    }

    // 2. TIPOS DE EVENTOS PADRÃO
    echo "\n2️⃣ Inserindo tipos de eventos padrão...\n";
    $eventos = [
        [1, 'pedido_criado', 'Pedido Criado', 'Notificação quando um novo pedido é criado', 'pedido', false, null, '["pedido_id", "cliente_nome", "empresa_nome", "pedido_total", "quantidade_itens"]'],
        [1, 'pagamento_confirmado', 'Pagamento Confirmado', 'Notificação quando pagamento é aprovado', 'pagamento', false, null, '["pedido_id", "cliente_nome", "empresa_nome", "pedido_total", "metodo_pagamento"]'],
        [1, 'pedido_entregue', 'Pedido Entregue', 'Notificação quando pedido é entregue', 'pedido', false, null, '["pedido_id", "cliente_nome", "endereco_entrega", "horario_entrega"]'],
        [1, 'cliente_aniversario', 'Aniversário do Cliente', 'Notificação automática no aniversário', 'usuario', true, '0 9 * * *', '["cliente_nome", "pontos_bonus", "ofertas_especiais"]'],
        [1, 'cliente_inativo_15', 'Cliente Inativo 15 dias', 'Cliente sem pedidos há 15 dias', 'usuario', true, '0 10 * * *', '["cliente_nome", "dias_inativo", "data_ultimo_pedido", "ofertas_especiais"]'],
        [1, 'cliente_risco_30', 'Cliente de Risco 30 dias', 'Cliente de risco (30+ dias sem pedidos)', 'usuario', true, '0 14 * * *', '["cliente_nome", "dias_inativo", "data_ultimo_pedido", "ofertas_retencao"]']
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO notificacao_tipos_evento (empresa_id, codigo, nome, descricao, categoria, automatico, agendamento_cron, variaveis_disponiveis, sync_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    foreach ($eventos as $evento) {
        $stmt->execute($evento);
        echo "  ✅ {$evento[2]} ({$evento[1]})\n";
    }

    // 3. TEMPLATES PADRÃO (Mais complexo, precisa dos IDs das aplicações e eventos)
    echo "\n3️⃣ Inserindo templates padrão...\n";

    // Busca IDs das aplicações
    $apps = $pdo->query("SELECT id, codigo FROM notificacao_aplicacoes WHERE empresa_id = 1")->fetchAll(PDO::FETCH_KEY_PAIR);

    // Busca IDs dos eventos
    $eventosIds = $pdo->query("SELECT id, codigo FROM notificacao_tipos_evento WHERE empresa_id = 1")->fetchAll(PDO::FETCH_KEY_PAIR);

    $templates = [
        // PEDIDO CRIADO
        [1, $eventosIds['pedido_criado'], $apps['cliente'], 'Pedido Criado - Cliente', '🛒 Pedido confirmado!', 'Seu pedido #{{pedido_id}} foi confirmado e está sendo preparado pela {{empresa_nome}}.', '["websocket", "push", "email", "in_app"]', 'media', 'fas fa-shopping-cart', '#28a745', true, true],
        [1, $eventosIds['pedido_criado'], $apps['empresa'], 'Pedido Criado - Empresa', '🔔 Novo pedido recebido!', 'Novo pedido #{{pedido_id}} de {{cliente_nome}} - Total: R$ {{pedido_total}}', '["websocket", "push", "email", "in_app"]', 'alta', 'fas fa-bell', '#007bff', true, true],
        [1, $eventosIds['pedido_criado'], $apps['admin'], 'Pedido Criado - Admin', '📊 Novo pedido no sistema', 'Pedido #{{pedido_id}} - {{empresa_nome}} - R$ {{pedido_total}}', '["websocket", "in_app"]', 'baixa', 'fas fa-chart-line', '#6c757d', true, true],

        // PAGAMENTO CONFIRMADO
        [1, $eventosIds['pagamento_confirmado'], $apps['cliente'], 'Pagamento Confirmado - Cliente', '💳 Pagamento aprovado!', 'Pagamento do pedido #{{pedido_id}} foi confirmado. Seu pedido será processado em breve.', '["websocket", "push", "email", "in_app"]', 'alta', 'fas fa-credit-card', '#28a745', true, true],
        [1, $eventosIds['pagamento_confirmado'], $apps['empresa'], 'Pagamento Confirmado - Empresa', '💰 Pagamento recebido', 'Pagamento confirmado para o pedido #{{pedido_id}} - R$ {{pedido_total}}', '["websocket", "push", "in_app"]', 'alta', 'fas fa-money-bill-wave', '#28a745', true, true],
        [1, $eventosIds['pagamento_confirmado'], $apps['entregador'], 'Pagamento Confirmado - Entregador', '🚚 Pedido liberado para entrega', 'Pedido #{{pedido_id}} pago e liberado - {{endereco_entrega}}', '["websocket", "push", "in_app"]', 'media', 'fas fa-truck', '#ffc107', true, true],

        // ANIVERSÁRIO
        [1, $eventosIds['cliente_aniversario'], $apps['cliente'], 'Aniversário - Cliente', '🎉 Feliz Aniversário, {{cliente_nome}}!', 'Parabéns! Como presente, você ganhou {{pontos_bonus}} pontos especiais!', '["push", "email", "in_app"]', 'media', 'fas fa-birthday-cake', '#e83e8c', true, true],
        [1, $eventosIds['cliente_aniversario'], $apps['empresa'], 'Aniversário - Empresa', '🎂 Cliente aniversariante: {{cliente_nome}}', 'Oportunidade de engajamento - cliente faz aniversário hoje', '["in_app"]', 'baixa', 'fas fa-birthday-cake', '#e83e8c', true, true],
        [1, $eventosIds['cliente_aniversario'], $apps['fidelidade'], 'Aniversário - Fidelidade', '🎁 Bônus de aniversário aplicado', 'Cliente {{cliente_nome}} recebeu {{pontos_bonus}} pontos de aniversário', '["websocket", "in_app"]', 'baixa', 'fas fa-gift', '#e83e8c', true, true]
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO notificacao_templates (empresa_id, tipo_evento_id, aplicacao_id, nome, titulo, mensagem, canais, prioridade, icone_classe, cor_hex, ativo, padrao, sync_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    foreach ($templates as $template) {
        $stmt->execute($template);
        echo "  ✅ {$template[3]}\n";
    }

    echo "\n🎉 DADOS INICIAIS INSERIDOS COM SUCESSO!\n";

    // Verifica dados inseridos
    $countApps = $pdo->query("SELECT COUNT(*) FROM notificacao_aplicacoes WHERE empresa_id = 1")->fetchColumn();
    $countEventos = $pdo->query("SELECT COUNT(*) FROM notificacao_tipos_evento WHERE empresa_id = 1")->fetchColumn();
    $countTemplates = $pdo->query("SELECT COUNT(*) FROM notificacao_templates WHERE empresa_id = 1")->fetchColumn();

    echo "\n📊 RESUMO DOS DADOS:\n";
    echo "  📱 Aplicações: $countApps\n";
    echo "  📋 Tipos de eventos: $countEventos\n";
    echo "  📝 Templates: $countTemplates\n";
} catch (Exception $e) {
    echo "💥 ERRO: " . $e->getMessage() . "\n";
}

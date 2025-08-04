<?php

/**
 * Criador de tabelas de notificação completas
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

    // Desabilita verificação de chaves estrangeiras temporariamente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "🔓 Verificação de chaves estrangeiras desabilitada\n";

    // Array com todas as tabelas do sistema de notificações
    $tabelas = [
        // TABELA 1: APLICAÇÕES DO SISTEMA
        'notificacao_aplicacoes' => "
            CREATE TABLE IF NOT EXISTS notificacao_aplicacoes (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                codigo VARCHAR(50) NOT NULL COMMENT 'Código único (customer, company, admin, delivery, loyalty)',
                nome VARCHAR(100) NOT NULL COMMENT 'Nome da aplicação',
                descricao TEXT NULL COMMENT 'Descrição da aplicação',
                icone_classe VARCHAR(100) NULL COMMENT 'Classe do ícone (ex: fas fa-user)',
                cor_hex VARCHAR(7) NULL COMMENT 'Cor tema da aplicação (#28a745)',
                webhook_url VARCHAR(500) NULL COMMENT 'URL para receber webhooks (apps externas)',
                api_key VARCHAR(255) NULL COMMENT 'Chave de autenticação para webhooks',
                configuracoes JSON NULL COMMENT 'Configurações específicas da aplicação',
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Se a aplicação está ativa',
                ordem_exibicao INT DEFAULT 0 COMMENT 'Ordem de exibição',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_empresa_codigo (empresa_id, codigo),
                INDEX idx_empresa_ativo (empresa_id, ativo),
                INDEX idx_ordem_exibicao (ordem_exibicao),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data),
                
                -- Chave única
                UNIQUE KEY unique_empresa_codigo (empresa_id, codigo, deleted_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aplicações que podem receber notificações'
        ",

        // TABELA 2: TIPOS DE EVENTOS
        'notificacao_tipos_evento' => "
            CREATE TABLE IF NOT EXISTS notificacao_tipos_evento (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                codigo VARCHAR(100) NOT NULL COMMENT 'Código único (pedido_criado, pagamento_confirmado)',
                nome VARCHAR(150) NOT NULL COMMENT 'Nome amigável do evento',
                descricao TEXT NULL COMMENT 'Descrição detalhada do evento',
                categoria VARCHAR(50) DEFAULT 'geral' COMMENT 'Categoria do evento (pedido, pagamento, usuario, sistema)',
                automatico BOOLEAN DEFAULT FALSE COMMENT 'Se é um evento automático (cron)',
                agendamento_cron VARCHAR(100) NULL COMMENT 'Expressão cron (se automático)',
                aplicacoes_padrao JSON NULL COMMENT 'Aplicações padrão que recebem este evento',
                variaveis_disponiveis JSON NULL COMMENT 'Variáveis disponíveis para templates',
                condicoes JSON NULL COMMENT 'Condições para disparar o evento',
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Se o tipo de evento está ativo',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_empresa_codigo (empresa_id, codigo),
                INDEX idx_empresa_categoria (empresa_id, categoria),
                INDEX idx_empresa_automatico (empresa_id, automatico),
                INDEX idx_empresa_ativo (empresa_id, ativo),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data),
                
                -- Chave única
                UNIQUE KEY unique_empresa_codigo (empresa_id, codigo, deleted_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tipos de eventos que podem gerar notificações'
        ",

        // TABELA 3: TEMPLATES DE NOTIFICAÇÃO
        'notificacao_templates' => "
            CREATE TABLE IF NOT EXISTS notificacao_templates (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                tipo_evento_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do tipo de evento',
                aplicacao_id BIGINT UNSIGNED NOT NULL COMMENT 'ID da aplicação alvo',
                nome VARCHAR(100) NOT NULL COMMENT 'Nome do template',
                categoria VARCHAR(50) DEFAULT 'geral' COMMENT 'Categoria do template',
                
                -- Conteúdo da notificação
                titulo VARCHAR(255) NOT NULL COMMENT 'Título da notificação',
                mensagem TEXT NOT NULL COMMENT 'Corpo da mensagem',
                subtitulo VARCHAR(255) NULL COMMENT 'Subtítulo opcional',
                texto_acao VARCHAR(100) NULL COMMENT 'Texto do botão/ação (ex: Ver Pedido)',
                url_acao VARCHAR(500) NULL COMMENT 'URL da ação (pode ter variáveis {{pedido_id}})',
                
                -- Configurações específicas
                canais JSON NOT NULL COMMENT 'Canais permitidos [\"websocket\", \"push\", \"email\", \"sms\", \"in_app\"]',
                prioridade ENUM('baixa', 'media', 'alta', 'urgente') DEFAULT 'media' COMMENT 'Prioridade da notificação',
                expira_em_minutos INT NULL COMMENT 'Minutos para expirar (NULL = nunca expira)',
                
                -- Variáveis e condições
                variaveis JSON NULL COMMENT 'Variáveis específicas deste template',
                condicoes JSON NULL COMMENT 'Condições para usar este template',
                segmentos_usuario JSON NULL COMMENT 'Segmentos de usuário aplicáveis',
                
                -- Personalização visual
                icone_classe VARCHAR(100) NULL COMMENT 'Classe do ícone (fas fa-shopping-cart)',
                cor_hex VARCHAR(7) NULL COMMENT 'Cor tema (#28a745)',
                arquivo_som VARCHAR(100) NULL COMMENT 'Arquivo de som personalizado',
                url_imagem VARCHAR(500) NULL COMMENT 'URL da imagem da notificação',
                
                -- Controle e versionamento
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Se o template está ativo',
                padrao BOOLEAN DEFAULT FALSE COMMENT 'Se é o template padrão para o evento+app',
                versao INT DEFAULT 1 COMMENT 'Versão do template (para A/B testing)',
                percentual_ab_test DECIMAL(5,2) NULL COMMENT 'Percentual para A/B test (0.00-100.00)',
                
                -- Estatísticas
                total_uso INT DEFAULT 0 COMMENT 'Quantas vezes foi usado',
                taxa_conversao DECIMAL(5,2) NULL COMMENT 'Taxa de conversão (%)',
                ultimo_uso_em TIMESTAMP NULL COMMENT 'Última vez que foi usado',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_empresa_evento_app (empresa_id, tipo_evento_id, aplicacao_id),
                INDEX idx_empresa_ativo (empresa_id, ativo),
                INDEX idx_empresa_padrao (empresa_id, padrao),
                INDEX idx_ab_test (empresa_id, percentual_ab_test),
                INDEX idx_estatisticas_uso (total_uso, taxa_conversao),
                INDEX idx_ultimo_uso (ultimo_uso_em),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Templates de notificação por evento e aplicação'
        ",

        // TABELA 4: NOTIFICAÇÕES ENVIADAS (LOG)
        'notificacao_enviadas' => "
            CREATE TABLE IF NOT EXISTS notificacao_enviadas (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                template_id BIGINT UNSIGNED NULL COMMENT 'ID do template usado (pode ser NULL se enviado programaticamente)',
                tipo_evento_id BIGINT UNSIGNED NULL COMMENT 'ID do tipo de evento',
                aplicacao_id BIGINT UNSIGNED NOT NULL COMMENT 'ID da aplicação alvo',
                
                -- Destinatário
                usuario_id BIGINT UNSIGNED NULL COMMENT 'ID do usuário destinatário',
                empresa_relacionada_id INT UNSIGNED NULL COMMENT 'ID da empresa relacionada (no contexto)',
                usuario_externo_id VARCHAR(100) NULL COMMENT 'ID externo (para apps externas)',
                email_destinatario VARCHAR(255) NULL COMMENT 'Email do destinatário',
                telefone_destinatario VARCHAR(20) NULL COMMENT 'Telefone do destinatário',
                
                -- Conteúdo processado
                titulo VARCHAR(255) NOT NULL COMMENT 'Título processado',
                mensagem TEXT NOT NULL COMMENT 'Mensagem processada',
                dados_processados JSON NULL COMMENT 'Dados completos processados',
                
                -- Controle de envio
                canal VARCHAR(50) NOT NULL COMMENT 'Canal usado (websocket, push, email, sms, in_app)',
                prioridade ENUM('baixa', 'media', 'alta', 'urgente') DEFAULT 'media',
                agendado_para TIMESTAMP NULL COMMENT 'Quando foi agendado para envio',
                enviado_em TIMESTAMP NULL COMMENT 'Quando foi efetivamente enviado',
                entregue_em TIMESTAMP NULL COMMENT 'Quando foi entregue (se suportado pelo canal)',
                lido_em TIMESTAMP NULL COMMENT 'Quando foi lido pelo usuário',
                clicado_em TIMESTAMP NULL COMMENT 'Quando o usuário clicou na ação',
                
                -- Status e controle
                status ENUM('pendente', 'enviado', 'entregue', 'falhou', 'expirou') DEFAULT 'pendente',
                tentativas INT DEFAULT 0 COMMENT 'Número de tentativas de envio',
                mensagem_erro TEXT NULL COMMENT 'Mensagem de erro (se falhou)',
                id_externo VARCHAR(255) NULL COMMENT 'ID externo do provedor (ex: Firebase)',
                expira_em TIMESTAMP NULL COMMENT 'Quando a notificação expira',
                
                -- Dados contextuais
                dados_evento_origem JSON NULL COMMENT 'Dados originais do evento',
                user_agent TEXT NULL COMMENT 'User agent (para in-app)',
                endereco_ip VARCHAR(45) NULL COMMENT 'IP do usuário (para in-app)',
                info_dispositivo JSON NULL COMMENT 'Informações do dispositivo',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_empresa_usuario (empresa_id, usuario_id),
                INDEX idx_empresa_app (empresa_id, aplicacao_id),
                INDEX idx_empresa_canal (empresa_id, canal),
                INDEX idx_empresa_status (empresa_id, status),
                INDEX idx_empresa_enviado (empresa_id, enviado_em),
                INDEX idx_usuario_nao_lido (usuario_id, lido_em),
                INDEX idx_template_stats (template_id, status, enviado_em),
                INDEX idx_agendado (agendado_para, status),
                INDEX idx_expirado (expira_em, status),
                INDEX idx_tentativas (tentativas, status),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de todas as notificações enviadas'
        ",

        // TABELA 5: HISTÓRICO DE TEMPLATES (AUDITORIA)
        'notificacao_templates_historico' => "
            CREATE TABLE IF NOT EXISTS notificacao_templates_historico (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                template_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do template',
                acao ENUM('criado', 'atualizado', 'excluido', 'ativado', 'desativado', 'clonado') NOT NULL COMMENT 'Ação realizada',
                
                -- Dados da mudança
                alteracoes JSON NULL COMMENT 'Campos que foram alterados',
                dados_anteriores JSON NULL COMMENT 'Dados anteriores',
                dados_novos JSON NULL COMMENT 'Novos dados',
                motivo VARCHAR(255) NULL COMMENT 'Motivo da alteração',
                
                -- Contexto da alteração
                usuario_id BIGINT UNSIGNED NULL COMMENT 'Usuário que fez a alteração',
                endereco_ip VARCHAR(45) NULL COMMENT 'IP do usuário',
                user_agent TEXT NULL COMMENT 'User agent',
                sessao_id VARCHAR(255) NULL COMMENT 'ID da sessão',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_template (template_id),
                INDEX idx_empresa (empresa_id),
                INDEX idx_empresa_acao (empresa_id, acao),
                INDEX idx_usuario (usuario_id),
                INDEX idx_created (created_at),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de mudanças nos templates'
        ",

        // TABELA 6: AGENDAMENTOS E AUTOMAÇÃO
        'notificacao_agendamentos' => "
            CREATE TABLE IF NOT EXISTS notificacao_agendamentos (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                tipo_evento_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do tipo de evento',
                nome VARCHAR(100) NOT NULL COMMENT 'Nome do agendamento',
                descricao TEXT NULL COMMENT 'Descrição do agendamento',
                
                -- Configuração do agendamento
                tipo_agendamento ENUM('cron', 'intervalo', 'data', 'evento') NOT NULL DEFAULT 'cron' COMMENT 'Tipo de agendamento',
                expressao_cron VARCHAR(100) NULL COMMENT 'Expressão cron (ex: 0 9 * * *)',
                minutos_intervalo INT NULL COMMENT 'Intervalo em minutos (para tipo_agendamento=intervalo)',
                data_especifica TIMESTAMP NULL COMMENT 'Data específica (para tipo_agendamento=data)',
                
                -- Filtros e condições
                aplicacoes_alvo JSON NOT NULL COMMENT 'Aplicações alvo para este agendamento',
                filtros_usuario JSON NULL COMMENT 'Filtros de usuários (ex: apenas clientes ativos)',
                condicoes JSON NULL COMMENT 'Condições específicas para execução',
                
                -- Configurações de execução
                maximo_destinatarios INT NULL COMMENT 'Máximo de destinatários por execução',
                tamanho_lote INT DEFAULT 100 COMMENT 'Tamanho do lote para processamento',
                tentativas_retry INT DEFAULT 3 COMMENT 'Tentativas em caso de erro',
                timeout_minutos INT DEFAULT 60 COMMENT 'Timeout da execução',
                
                -- Status e controle
                ativo BOOLEAN DEFAULT TRUE COMMENT 'Se o agendamento está ativo',
                ultima_execucao_em TIMESTAMP NULL COMMENT 'Última execução',
                proxima_execucao_em TIMESTAMP NULL COMMENT 'Próxima execução prevista',
                status_ultima_execucao ENUM('sucesso', 'falhou', 'parcial') NULL COMMENT 'Status da última execução',
                destinatarios_ultima_execucao INT NULL COMMENT 'Destinatários da última execução',
                erros_ultima_execucao TEXT NULL COMMENT 'Erros da última execução',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_empresa_ativo (empresa_id, ativo),
                INDEX idx_empresa_tipo (empresa_id, tipo_agendamento),
                INDEX idx_proxima_execucao (proxima_execucao_em, ativo),
                INDEX idx_ultima_execucao (ultima_execucao_em),
                INDEX idx_tipo_evento (tipo_evento_id),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Agendamentos e automações de notificações'
        ",

        // TABELA 7: PREFERÊNCIAS DE USUÁRIO
        'notificacao_preferencias_usuario' => "
            CREATE TABLE IF NOT EXISTS notificacao_preferencias_usuario (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                usuario_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do usuário',
                aplicacao_id BIGINT UNSIGNED NOT NULL COMMENT 'ID da aplicação',
                
                -- Preferências por canal
                websocket_ativo BOOLEAN DEFAULT TRUE COMMENT 'WebSocket ativo',
                push_ativo BOOLEAN DEFAULT TRUE COMMENT 'Push notifications ativo',
                email_ativo BOOLEAN DEFAULT TRUE COMMENT 'Email ativo',
                sms_ativo BOOLEAN DEFAULT FALSE COMMENT 'SMS ativo',
                in_app_ativo BOOLEAN DEFAULT TRUE COMMENT 'In-app ativo',
                
                -- Preferências por tipo de evento
                preferencias_evento JSON NULL COMMENT 'Preferências específicas por tipo de evento',
                
                -- Configurações de horário
                horario_silencio_inicio TIME NULL COMMENT 'Início do período de silêncio',
                horario_silencio_fim TIME NULL COMMENT 'Fim do período de silêncio',
                fuso_horario VARCHAR(50) NULL COMMENT 'Fuso horário do usuário',
                
                -- Configurações de frequência
                frequencia_resumo ENUM('nunca', 'imediato', 'horario', 'diario', 'semanal') DEFAULT 'imediato' COMMENT 'Frequência do resumo',
                maximo_notificacoes_hora INT DEFAULT 10 COMMENT 'Máximo de notificações por hora',
                
                -- Dispositivos
                tokens_push JSON NULL COMMENT 'Tokens para push notifications',
                info_dispositivos JSON NULL COMMENT 'Informações dos dispositivos',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_empresa_usuario (empresa_id, usuario_id),
                INDEX idx_empresa_usuario_app (empresa_id, usuario_id, aplicacao_id),
                INDEX idx_push_ativo (push_ativo),
                INDEX idx_email_ativo (email_ativo),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Preferências de notificação por usuário e aplicação'
        ",

        // TABELA 8: ESTATÍSTICAS E ANALYTICS
        'notificacao_estatisticas' => "
            CREATE TABLE IF NOT EXISTS notificacao_estatisticas (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                empresa_id INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',
                data DATE NOT NULL COMMENT 'Data da estatística',
                hora TINYINT NULL COMMENT 'Hora (0-23, NULL para dados diários)',
                
                -- Dimensões
                aplicacao_id BIGINT UNSIGNED NULL COMMENT 'ID da aplicação (NULL para todas)',
                tipo_evento_id BIGINT UNSIGNED NULL COMMENT 'ID do tipo de evento (NULL para todos)',
                template_id BIGINT UNSIGNED NULL COMMENT 'ID do template (NULL para todos)',
                canal VARCHAR(50) NULL COMMENT 'Canal (NULL para todos)',
                
                -- Métricas de envio
                notificacoes_enviadas INT DEFAULT 0 COMMENT 'Total de notificações enviadas',
                notificacoes_entregues INT DEFAULT 0 COMMENT 'Total entregues',
                notificacoes_falharam INT DEFAULT 0 COMMENT 'Total falharam',
                notificacoes_expiraram INT DEFAULT 0 COMMENT 'Total expiraram',
                
                -- Métricas de engajamento
                notificacoes_lidas INT DEFAULT 0 COMMENT 'Total lidas',
                notificacoes_clicadas INT DEFAULT 0 COMMENT 'Total clicadas',
                destinatarios_unicos INT DEFAULT 0 COMMENT 'Destinatários únicos',
                
                -- Taxas calculadas
                taxa_entrega DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taxa de entrega (%)',
                taxa_abertura DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taxa de abertura (%)',
                taxa_clique DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taxa de clique (%)',
                
                -- Tempo médio
                tempo_medio_entrega_segundos INT DEFAULT 0 COMMENT 'Tempo médio de entrega (segundos)',
                tempo_medio_leitura_segundos INT DEFAULT 0 COMMENT 'Tempo médio para leitura (segundos)',
                
                -- Sincronização Multi-Sites (OBRIGATÓRIO)
                sync_hash VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
                sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
                sync_data TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
                
                -- Timestamps Laravel (SEMPRE usar estes nomes)
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,  -- Para SoftDeletes
                
                -- Índices
                INDEX idx_empresa_data (empresa_id, data),
                INDEX idx_empresa_data_hora (empresa_id, data, hora),
                INDEX idx_app_data (aplicacao_id, data),
                INDEX idx_evento_data (tipo_evento_id, data),
                INDEX idx_template_data (template_id, data),
                INDEX idx_canal_data (canal, data),
                INDEX idx_taxas (taxa_entrega, taxa_abertura, taxa_clique),
                INDEX idx_sync_status (sync_status),
                INDEX idx_sync_data (sync_data)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estatísticas e analytics das notificações'
        "
    ];

    $success = 0;
    $errors = 0;

    // Cria cada tabela
    foreach ($tabelas as $nome => $sql) {
        try {
            echo "📋 Criando tabela: $nome...";
            $pdo->exec($sql);
            echo " ✅\n";
            $success++;
        } catch (PDOException $e) {
            echo " ❌ - " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    echo "\n📊 RESULTADO DA CRIAÇÃO DAS TABELAS:\n";
    echo "✅ Sucessos: $success\n";
    echo "❌ Erros: $errors\n";

    // Reabilita verificação de chaves estrangeiras
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "🔒 Verificação de chaves estrangeiras reabilitada\n";

    // Verifica se as tabelas foram criadas
    echo "\n🔍 Verificando tabelas criadas:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'notificacao_%'");
    $tabelasNotificacao = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tabelasNotificacao as $tabela) {
        echo "  ✅ $tabela\n";
    }

    echo "\n📈 Total de tabelas de notificação: " . count($tabelasNotificacao) . "\n";

    if (count($tabelasNotificacao) === 8) {
        echo "🎉 TODAS AS TABELAS DE NOTIFICAÇÃO FORAM CRIADAS COM SUCESSO!\n";
    } else {
        echo "⚠️  Algumas tabelas podem não ter sido criadas corretamente.\n";
    }
} catch (Exception $e) {
    echo "💥 ERRO: " . $e->getMessage() . "\n";
}

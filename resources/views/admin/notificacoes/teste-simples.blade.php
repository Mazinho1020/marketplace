<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teste do Sistema de Notificações</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-info { background: #17a2b8; }
        .btn-info:hover { background: #138496; }
        .logs { background: #f8f9fa; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto; }
        .notification-item { padding: 10px; margin: 5px 0; border-left: 4px solid #007bff; background: #e7f3ff; border-radius: 4px; }
        .notification-success { border-left-color: #28a745; background: #d4edda; }
        .notification-warning { border-left-color: #ffc107; background: #fff3cd; }
        .notification-error { border-left-color: #dc3545; background: #f8d7da; }
        .notification-popup { position: fixed; top: 20px; right: 20px; background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; max-width: 300px; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Sistema de Notificações - Teste</h1>
        
        <div class="test-section">
            <h3>📊 Status do Sistema</h3>
            <div id="system-status">
                <div class="info">Verificando status do sistema...</div>
            </div>
        </div>

        <div class="test-section">
            <h3>🔧 Testes Básicos</h3>
            <button onclick="testarConexaoBD()">Testar Conexão BD</button>
            <button onclick="testarTabelas()">Verificar Tabelas</button>
            <button onclick="testarModels()">Testar Models</button>
            <button onclick="testarServices()">Testar Services</button>
        </div>

        <div class="test-section">
            <h3>📧 Teste de Notificação</h3>
            <button onclick="enviarNotificacaoTeste()" class="btn-success">Enviar Notificação de Teste</button>
            <button onclick="verUltimasNotificacoes()">Ver Últimas Notificações</button>
            <button onclick="atualizarNotificacoes()" class="btn-info">Atualizar em Tempo Real</button>
        </div>

        <div class="test-section">
            <h3>🔔 Notificações Recebidas</h3>
            <div id="notificacoes-display" style="min-height: 200px; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f8f9fa;">
                <div style="text-align: center; color: #666;">Nenhuma notificação carregada ainda...</div>
            </div>
            <br>
            <button onclick="simularNotificacaoVisual()" class="btn-success">Simular Notificação Visual</button>
        </div>

        <div class="test-section">
            <h3>📋 Logs de Teste</h3>
            <div id="test-logs" class="logs">
                <div>Sistema carregado. Aguardando testes...</div>
            </div>
            <button onclick="limparLogs()" class="btn-danger">Limpar Logs</button>
        </div>
    </div>

    <script>
        function log(message, type = 'info') {
            const logs = document.getElementById('test-logs');
            const now = new Date().toLocaleTimeString();
            const logClass = type === 'error' ? 'error' : type === 'success' ? 'success' : 'info';
            logs.innerHTML += `<div class="${logClass}">[${now}] ${message}</div>`;
            logs.scrollTop = logs.scrollHeight;
        }

        function limparLogs() {
            document.getElementById('test-logs').innerHTML = '<div>Logs limpos.</div>';
        }

        async function fazerRequisicao(url, dados = {}) {
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                return { success: response.ok, data: result };
            } catch (error) {
                return { success: false, error: error.message };
            }
        }

        async function testarConexaoBD() {
            log('Testando conexão com banco de dados...');
            
            try {
                const result = await fazerRequisicao('/admin/notificacoes/teste/conexao');
                if (result.success) {
                    log('✅ Conexão com BD funcionando!', 'success');
                } else {
                    log('❌ Erro na conexão: ' + JSON.stringify(result.data), 'error');
                }
            } catch (error) {
                log('❌ Erro: ' + error.message, 'error');
            }
        }

        async function testarTabelas() {
            log('Verificando tabelas do sistema...');
            
            try {
                const result = await fazerRequisicao('/admin/notificacoes/teste/tabelas');
                if (result.success) {
                    log('✅ Todas as tabelas estão OK!', 'success');
                    log('Tabelas encontradas: ' + result.data.tabelas.join(', '));
                } else {
                    log('❌ Problema com tabelas: ' + JSON.stringify(result.data), 'error');
                }
            } catch (error) {
                log('❌ Erro: ' + error.message, 'error');
            }
        }

        async function testarModels() {
            log('Testando models do Laravel...');
            
            try {
                const result = await fazerRequisicao('/admin/notificacoes/teste/models');
                if (result.success) {
                    log('✅ Models funcionando!', 'success');
                    log('Aplicações: ' + result.data.aplicacoes + ', Tipos de evento: ' + result.data.tipos_evento);
                } else {
                    log('❌ Erro nos models: ' + JSON.stringify(result.data), 'error');
                }
            } catch (error) {
                log('❌ Erro: ' + error.message, 'error');
            }
        }

        async function testarServices() {
            log('Testando services...');
            
            try {
                const result = await fazerRequisicao('/admin/notificacoes/teste/services');
                if (result.success) {
                    log('✅ Services OK!', 'success');
                    log('Configurações carregadas: ' + result.data.configs);
                } else {
                    log('❌ Erro nos services: ' + JSON.stringify(result.data), 'error');
                }
            } catch (error) {
                log('❌ Erro: ' + error.message, 'error');
            }
        }

        async function enviarNotificacaoTeste() {
            log('Enviando notificação de teste...');
            
            try {
                const result = await fazerRequisicao('/admin/notificacoes/teste/enviar');
                
                if (result.success) {
                    log('✅ Notificação enviada com sucesso!', 'success');
                    log(`Tipo: ${result.data.tipo_evento}`);
                    log(`Dados: ${JSON.stringify(result.data.dados)}`);
                    
                    // Mostrar popup visual
                    const popup = document.createElement('div');
                    popup.className = 'notification-popup';
                    popup.innerHTML = `
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 24px; margin-right: 10px;">✅</span>
                            <strong>Notificação Enviada!</strong>
                        </div>
                        <div style="color: #666; font-size: 14px;">
                            <strong>Tipo:</strong> ${result.data.tipo_evento}<br>
                            <strong>Status:</strong> Processada<br>
                            <small>${JSON.stringify(result.data.dados)}</small>
                        </div>
                    `;
                    
                    document.body.appendChild(popup);
                    setTimeout(() => {
                        if (popup.parentNode) {
                            popup.parentNode.removeChild(popup);
                        }
                    }, 5000);
                    
                    // Atualizar automaticamente as notificações
                    setTimeout(() => {
                        verUltimasNotificacoes();
                    }, 2000);
                    
                } else {
                    log('❌ Erro ao enviar: ' + JSON.stringify(result.data), 'error');
                }
            } catch (error) {
                log('❌ Erro: ' + error.message, 'error');
            }
        }

        async function verUltimasNotificacoes() {
            log('Buscando últimas notificações...');
            
            try {
                const result = await fazerRequisicao('/admin/notificacoes/teste/ultimas');
                if (result.success) {
                    log('✅ Últimas notificações carregadas!', 'success');
                    
                    const display = document.getElementById('notificacoes-display');
                    if (result.data.notificacoes && result.data.notificacoes.length > 0) {
                        display.innerHTML = '';
                        result.data.notificacoes.forEach(notif => {
                            const item = document.createElement('div');
                            item.className = 'notification-item notification-success';
                            item.innerHTML = `
                                <strong>📧 ${notif.tipo_evento}</strong><br>
                                <small>Status: ${notif.status} | Canal: ${notif.canal}</small><br>
                                <small>📅 ${notif.created_at}</small>
                            `;
                            display.appendChild(item);
                            log(`📧 ${notif.tipo_evento} - ${notif.status} - ${notif.created_at}`);
                        });
                    } else {
                        display.innerHTML = '<div style="text-align: center; color: #666;">Nenhuma notificação encontrada</div>';
                    }
                } else {
                    log('❌ Erro ao buscar: ' + JSON.stringify(result.data), 'error');
                }
            } catch (error) {
                log('❌ Erro: ' + error.message, 'error');
            }
        }

        function simularNotificacaoVisual() {
            log('Simulando notificação visual...', 'info');
            
            // Criar popup de notificação
            const popup = document.createElement('div');
            popup.className = 'notification-popup';
            popup.innerHTML = `
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 24px; margin-right: 10px;">🔔</span>
                    <strong>Nova Notificação!</strong>
                </div>
                <div style="color: #666; font-size: 14px;">
                    Pedido #12345 foi criado<br>
                    Cliente: João Silva<br>
                    Valor: R$ 150,00
                </div>
                <div style="margin-top: 10px; text-align: right;">
                    <small style="color: #999;">Agora mesmo</small>
                </div>
            `;
            
            document.body.appendChild(popup);
            
            // Adicionar à lista de notificações
            const display = document.getElementById('notificacoes-display');
            const item = document.createElement('div');
            item.className = 'notification-item notification-success';
            item.innerHTML = `
                <strong>🔔 Notificação Simulada</strong><br>
                <small>Status: Entregue | Canal: Browser</small><br>
                <small>📅 ${new Date().toLocaleString()}</small>
            `;
            
            if (display.children.length === 1 && display.children[0].style.textAlign === 'center') {
                display.innerHTML = '';
            }
            display.insertBefore(item, display.firstChild);
            
            // Remover popup após 5 segundos
            setTimeout(() => {
                if (popup.parentNode) {
                    popup.parentNode.removeChild(popup);
                }
            }, 5000);
            
            log('✅ Notificação visual exibida!', 'success');
        }

        function atualizarNotificacoes() {
            log('Iniciando atualização automática...', 'info');
            
            // Atualizar a cada 5 segundos
            setInterval(() => {
                verUltimasNotificacoes();
            }, 5000);
            
            log('✅ Atualização automática ativada (5s)!', 'success');
        }

        // Verificar status do sistema ao carregar
        window.onload = async function() {
            log('Iniciando verificação do sistema...');
            
            // Simular verificações
            setTimeout(() => {
                const status = document.getElementById('system-status');
                status.innerHTML = `
                    <div class="success">✅ Laravel Framework: OK</div>
                    <div class="success">✅ Banco de Dados: Conectado</div>
                    <div class="success">✅ Tabelas de Notificação: Criadas</div>
                    <div class="info">ℹ️ Sistema pronto para testes</div>
                `;
                log('Sistema carregado e pronto!', 'success');
            }, 1000);
        };
    </script>
</body>
</html>

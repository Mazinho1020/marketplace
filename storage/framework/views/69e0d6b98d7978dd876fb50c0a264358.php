<?php $__env->startSection('title', 'Teste de Notificações'); ?>

<?php $__env->startPush('head'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.notificacoes.index')); ?>">Notificações</a></li>
                        <li class="breadcrumb-item active">Teste</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-test-tube"></i> Teste de Notificações
                </h4>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="card-title mb-3">
                                <i class="mdi mdi-play-circle"></i> Testes Rápidos
                            </h5>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" onclick="testeNotificacao('pedido_criado')">
                                    <i class="mdi mdi-cart"></i> Pedido Criado
                                </button>
                                <button class="btn btn-success" onclick="testeNotificacao('pagamento_aprovado')">
                                    <i class="mdi mdi-check-circle"></i> Pagamento Aprovado
                                </button>
                                <button class="btn btn-warning" onclick="testeNotificacao('produto_baixo_estoque')">
                                    <i class="mdi mdi-alert"></i> Baixo Estoque
                                </button>
                                <button class="btn btn-info" onclick="testeNotificacao('cliente_novo')">
                                    <i class="mdi mdi-account-plus"></i> Cliente Novo
                                </button>
                                <button class="btn btn-secondary" onclick="testePushNotification()">
                                    <i class="mdi mdi-cellphone-message"></i> Teste Push
                                </button>
                                <button class="btn btn-dark" onclick="testeTodasNotificacoes()">
                                    <i class="mdi mdi-rocket"></i> Teste Completo
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            <button class="btn btn-outline-danger" onclick="limparLogs()">
                                <i class="mdi mdi-broom"></i> Limpar Logs
                            </button>
                            <button class="btn btn-outline-secondary" onclick="toggleAutoUpdate()">
                                <i class="mdi mdi-refresh"></i> <span id="auto-update-text">Auto Update: OFF</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Teste Personalizado -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cog"></i> Teste Personalizado
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-teste-personalizado">
                        <div class="mb-3">
                            <label class="form-label">Canal</label>
                            <select class="form-control" id="teste-canal">
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option value="push">Push Notification</option>
                                <option value="in_app">In-App</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Evento</label>
                            <select class="form-control" id="teste-tipo">
                                <option value="pedido_criado">Pedido Criado</option>
                                <option value="pagamento_aprovado">Pagamento Aprovado</option>
                                <option value="produto_baixo_estoque">Produto Baixo Estoque</option>
                                <option value="cliente_novo">Cliente Novo</option>
                                <option value="sistema_manutencao">Sistema em Manutenção</option>
                                <option value="promocao_ativa">Promoção Ativa</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Destinatário</label>
                            <input type="text" class="form-control" id="teste-destinatario" placeholder="email@exemplo.com">
                            <small class="form-text text-muted" id="destinatario-ajuda">
                                Para push notifications, use o token do dispositivo
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Título/Assunto</label>
                            <input type="text" class="form-control" id="teste-titulo" placeholder="Título da notificação">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mensagem</label>
                            <textarea class="form-control" id="teste-mensagem" rows="3" placeholder="Conteúdo da notificação"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prioridade</label>
                            <select class="form-control" id="teste-prioridade">
                                <option value="baixa">Baixa</option>
                                <option value="normal" selected>Normal</option>
                                <option value="alta">Alta</option>
                                <option value="critica">Crítica</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="teste-agendamento">
                                <label class="form-check-label" for="teste-agendamento">
                                    Agendar notificação
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="div-agendamento" style="display: none;">
                            <label class="form-label">Data/Hora para Envio</label>
                            <input type="datetime-local" class="form-control" id="teste-data-agendamento">
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="enviarTestePersonalizado()">
                            <i class="mdi mdi-send"></i> Enviar Teste
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-monitor"></i> Monitor de Testes
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Métricas dos Testes -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary" id="total-testes">0</h4>
                                <small class="text-muted">Total de Testes</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success" id="testes-sucesso">0</h4>
                                <small class="text-muted">Sucessos</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-danger" id="testes-erro">0</h4>
                                <small class="text-muted">Erros</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info" id="tempo-medio">0ms</h4>
                                <small class="text-muted">Tempo Médio</small>
                            </div>
                        </div>
                    </div>

                    <!-- Log de Atividades -->
                    <div class="bg-light p-3 rounded" style="height: 400px; overflow-y: auto;" id="log-atividades">
                        <div class="text-center text-muted">
                            <i class="mdi mdi-information"></i> Aguardando testes...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Notificações de Teste -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-history"></i> Histórico de Testes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Horário</th>
                                    <th>Canal</th>
                                    <th>Tipo</th>
                                    <th>Destinatário</th>
                                    <th>Status</th>
                                    <th>Tempo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="historico-testes">
                                <!-- Carregado via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-information"></i> Detalhes da Notificação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-detalhes-conteudo">
                <!-- Carregado via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="reenviarNotificacao()">
                    <i class="mdi mdi-send"></i> Reenviar
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let contadores = {
    total: 0,
    sucesso: 0,
    erro: 0,
    tempos: []
};

let autoUpdate = false;
let autoUpdateInterval;

document.addEventListener('DOMContentLoaded', function() {
    carregarHistorico();
    
    // Configurar checkbox de agendamento
    document.getElementById('teste-agendamento').addEventListener('change', function() {
        const divAgendamento = document.getElementById('div-agendamento');
        divAgendamento.style.display = this.checked ? 'block' : 'none';
    });
    
    // Configurar mudança de canal para ajustar placeholder
    document.getElementById('teste-canal').addEventListener('change', function() {
        const canal = this.value;
        const inputDestinatario = document.getElementById('teste-destinatario');
        const ajudaDestinatario = document.getElementById('destinatario-ajuda');
        
        switch(canal) {
            case 'email':
                inputDestinatario.placeholder = 'email@exemplo.com';
                ajudaDestinatario.textContent = 'Digite o endereço de email do destinatário';
                break;
            case 'sms':
                inputDestinatario.placeholder = '+5511999999999';
                ajudaDestinatario.textContent = 'Digite o número de telefone com código do país';
                break;
            case 'push':
                inputDestinatario.placeholder = 'device-token-123456789';
                ajudaDestinatario.textContent = 'Digite o token do dispositivo para push notification';
                break;
            case 'in_app':
                inputDestinatario.placeholder = 'user_id_123';
                ajudaDestinatario.textContent = 'Digite o ID do usuário para notificação in-app';
                break;
        }
    });
});

function testeNotificacao(tipo) {
    adicionarLog(`🚀 Iniciando teste: ${tipo}`, 'info');
    
    const dados = {
        tipo: tipo,
        canal: 'email',
        destinatario: 'teste@exemplo.com',
        titulo: `Teste: ${tipo.replace('_', ' ').toUpperCase()}`,
        mensagem: `Esta é uma notificação de teste do tipo ${tipo}`,
        prioridade: 'normal'
    };
    
    enviarNotificacao(dados);
}

function testePushNotification() {
    adicionarLog('📱 Iniciando teste específico de Push Notification', 'info');
    
    const dados = {
        tipo: 'teste_push',
        canal: 'push',
        destinatario: 'push-token-' + Math.random().toString(36).substr(2, 9),
        titulo: 'Teste Push Notification',
        mensagem: 'Esta é uma notificação push de teste enviada via interface',
        prioridade: 'alta'
    };
    
    enviarNotificacao(dados);
}

function testeTodasNotificacoes() {
    adicionarLog('🎯 Iniciando teste completo de todas as notificações...', 'info');
    
    const tipos = ['pedido_criado', 'pagamento_aprovado', 'produto_baixo_estoque', 'cliente_novo'];
    const canais = ['email', 'sms', 'push', 'in_app'];
    
    tipos.forEach((tipo, indexTipo) => {
        canais.forEach((canal, indexCanal) => {
            setTimeout(() => {
                let destinatario;
                switch(canal) {
                    case 'email':
                        destinatario = 'teste@exemplo.com';
                        break;
                    case 'sms':
                        destinatario = '+5511999999999';
                        break;
                    case 'push':
                        destinatario = 'push-token-' + Math.random().toString(36).substr(2, 9);
                        break;
                    case 'in_app':
                        destinatario = 'user_' + Math.floor(Math.random() * 1000);
                        break;
                    default:
                        destinatario = 'teste@exemplo.com';
                }
                
                const dados = {
                    tipo: tipo,
                    canal: canal,
                    destinatario: destinatario,
                    titulo: `Teste ${tipo} via ${canal}`,
                    mensagem: `Teste completo: ${tipo} enviado via ${canal}`,
                    prioridade: 'normal'
                };
                
                enviarNotificacao(dados);
            }, (indexTipo * canais.length + indexCanal) * 1000); // 1 segundo entre cada teste
        });
    });
}

function enviarTestePersonalizado() {
    const dados = {
        tipo: document.getElementById('teste-tipo').value,
        canal: document.getElementById('teste-canal').value,
        destinatario: document.getElementById('teste-destinatario').value,
        titulo: document.getElementById('teste-titulo').value,
        mensagem: document.getElementById('teste-mensagem').value,
        prioridade: document.getElementById('teste-prioridade').value,
        agendamento: document.getElementById('teste-agendamento').checked,
        data_agendamento: document.getElementById('teste-data-agendamento').value
    };
    
    if (!dados.destinatario) {
        mostrarAlerta('Por favor, informe o destinatário', 'warning');
        return;
    }
    
    adicionarLog(`📧 Enviando teste personalizado para ${dados.destinatario}`, 'info');
    enviarNotificacao(dados);
}

function enviarNotificacao(dados) {
    const inicio = Date.now();
    
    fetch('/admin/notificacoes/teste/enviar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        const tempo = Date.now() - inicio;
        
        if (data.success) {
            contadores.sucesso++;
            adicionarLog(`✅ Sucesso: ${dados.tipo} via ${dados.canal} (${tempo}ms)`, 'success');
            mostrarNotificacaoPopup(`Teste enviado com sucesso via ${dados.canal}`, 'success');
        } else {
            contadores.erro++;
            adicionarLog(`❌ Erro: ${data.message} (${tempo}ms)`, 'error');
            mostrarNotificacaoPopup(`Erro: ${data.message}`, 'error');
        }
        
        contadores.total++;
        contadores.tempos.push(tempo);
        atualizarContadores();
        carregarHistorico();
    })
    .catch(error => {
        const tempo = Date.now() - inicio;
        contadores.erro++;
        contadores.total++;
        contadores.tempos.push(tempo);
        
        adicionarLog(`❌ Erro de rede: ${error.message} (${tempo}ms)`, 'error');
        mostrarNotificacaoPopup(`Erro de rede: ${error.message}`, 'error');
        atualizarContadores();
    });
}

function carregarHistorico() {
    fetch('/admin/notificacoes/api/historico-testes')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('historico-testes');
            tbody.innerHTML = '';
            
            data.forEach(item => {
                const status = item.status === 'enviado' ? 'success' : 'danger';
                const statusTexto = item.status === 'enviado' ? 'Enviado' : 'Falhou';
                
                tbody.innerHTML += `
                    <tr>
                        <td>${formatarDataHora(item.created_at)}</td>
                        <td><span class="badge bg-${getCorCanal(item.canal)}">${item.canal.toUpperCase()}</span></td>
                        <td>${item.tipo_evento}</td>
                        <td>${item.destinatario}</td>
                        <td><span class="badge bg-${status}">${statusTexto}</span></td>
                        <td>${item.tempo_processamento || '-'}ms</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="verDetalhes(${item.id})">
                                <i class="mdi mdi-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error('Erro ao carregar histórico:', error);
        });
}

function verDetalhes(id) {
    fetch(`/admin/notificacoes/api/detalhes/${id}`)
        .then(response => response.json())
        .then(data => {
            const conteudo = document.getElementById('modal-detalhes-conteudo');
            conteudo.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações Básicas</h6>
                        <table class="table table-sm">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Canal:</strong></td><td>${data.canal}</td></tr>
                            <tr><td><strong>Tipo:</strong></td><td>${data.tipo_evento}</td></tr>
                            <tr><td><strong>Status:</strong></td><td>${data.status}</td></tr>
                            <tr><td><strong>Prioridade:</strong></td><td>${data.prioridade}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Destinatário</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Para:</strong></td><td>${data.destinatario}</td></tr>
                            <tr><td><strong>Tentativas:</strong></td><td>${data.tentativas || 1}</td></tr>
                            <tr><td><strong>Enviado em:</strong></td><td>${formatarDataHora(data.created_at)}</td></tr>
                            <tr><td><strong>Tempo:</strong></td><td>${data.tempo_processamento || '-'}ms</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Conteúdo</h6>
                        <div class="bg-light p-3 rounded">
                            <strong>Título:</strong> ${data.titulo}<br>
                            <strong>Mensagem:</strong> ${data.mensagem}
                        </div>
                    </div>
                </div>
                ${data.erro_mensagem ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Erro</h6>
                        <div class="alert alert-danger">
                            ${data.erro_mensagem}
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
            
            new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
        });
}

function atualizarContadores() {
    document.getElementById('total-testes').textContent = contadores.total;
    document.getElementById('testes-sucesso').textContent = contadores.sucesso;
    document.getElementById('testes-erro').textContent = contadores.erro;
    
    if (contadores.tempos.length > 0) {
        const tempoMedio = contadores.tempos.reduce((a, b) => a + b, 0) / contadores.tempos.length;
        document.getElementById('tempo-medio').textContent = Math.round(tempoMedio) + 'ms';
    }
}

function adicionarLog(mensagem, tipo = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const icones = {
        'info': 'ℹ️',
        'success': '✅',
        'error': '❌',
        'warning': '⚠️'
    };
    
    const icone = icones[tipo] || 'ℹ️';
    const logContainer = document.getElementById('log-atividades');
    
    logContainer.innerHTML += `
        <div class="mb-1">
            <span class="text-muted">[${timestamp}]</span> ${icone} ${mensagem}
        </div>
    `;
    
    // Scroll para o final
    logContainer.scrollTop = logContainer.scrollHeight;
}

function limparLogs() {
    document.getElementById('log-atividades').innerHTML = `
        <div class="text-center text-muted">
            <i class="mdi mdi-broom"></i> Logs limpos
        </div>
    `;
    
    // Resetar contadores
    contadores = { total: 0, sucesso: 0, erro: 0, tempos: [] };
    atualizarContadores();
}

function toggleAutoUpdate() {
    autoUpdate = !autoUpdate;
    const botao = document.getElementById('auto-update-text');
    
    if (autoUpdate) {
        botao.textContent = 'Auto Update: ON';
        autoUpdateInterval = setInterval(carregarHistorico, 5000);
        adicionarLog('🔄 Auto-update ativado', 'info');
    } else {
        botao.textContent = 'Auto Update: OFF';
        clearInterval(autoUpdateInterval);
        adicionarLog('⏸️ Auto-update desativado', 'info');
    }
}

function mostrarNotificacaoPopup(mensagem, tipo) {
    const cores = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'info'
    };
    
    const popup = document.createElement('div');
    popup.className = `alert alert-${cores[tipo]} alert-dismissible fade show position-fixed`;
    popup.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
    popup.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(popup);
    
    // Remover após 5 segundos
    setTimeout(() => {
        if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
        }
    }, 5000);
}

function mostrarAlerta(mensagem, tipo) {
    mostrarNotificacaoPopup(mensagem, tipo);
}

function formatarDataHora(data) {
    return new Date(data).toLocaleString('pt-BR');
}

function getCorCanal(canal) {
    const cores = {
        'email': 'primary',
        'sms': 'success',
        'push': 'warning',
        'in_app': 'info'
    };
    return cores[canal] || 'secondary';
}

function reenviarNotificacao() {
    // Implementar reenvio de notificação
    mostrarAlerta('Funcionalidade de reenvio em desenvolvimento', 'info');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/notificacoes/teste.blade.php ENDPATH**/ ?>
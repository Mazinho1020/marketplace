<?php $__env->startSection('title', 'Notificações Enviadas'); ?>

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
                        <li class="breadcrumb-item active">Enviadas</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-send-check"></i> Notificações Enviadas
                </h4>
            </div>
        </div>
    </div>

    <!-- Filtros Avançados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Período</label>
                                <select class="form-select" id="filtro-periodo" onchange="filtrarNotificacoes()">
                                    <option value="hoje">Hoje</option>
                                    <option value="ontem">Ontem</option>
                                    <option value="7dias" selected>Últimos 7 dias</option>
                                    <option value="30dias">Últimos 30 dias</option>
                                    <option value="personalizado">Personalizado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Canal</label>
                                <select class="form-select" id="filtro-canal" onchange="filtrarNotificacoes()">
                                    <option value="">Todos</option>
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option value="push">Push</option>
                                    <option value="in_app">In-App</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-select" id="filtro-status" onchange="filtrarNotificacoes()">
                                    <option value="">Todos</option>
                                    <option value="enviado">Enviado</option>
                                    <option value="entregue">Entregue</option>
                                    <option value="lido">Lido</option>
                                    <option value="erro">Erro</option>
                                    <option value="pendente">Pendente</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Aplicação</label>
                                <select class="form-select" id="filtro-aplicacao" onchange="filtrarNotificacoes()">
                                    <option value="">Todas</option>
                                    <option value="ecommerce">E-commerce</option>
                                    <option value="crm">CRM</option>
                                    <option value="fidelidade">Fidelidade</option>
                                    <option value="suporte">Suporte</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Buscar</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="busca-texto" placeholder="Título, usuário, email...">
                                    <button class="btn btn-primary" onclick="buscarNotificacoes()">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button class="btn btn-info d-block w-100" onclick="exportarDados()">
                                    <i class="mdi mdi-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row">
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-send float-end text-primary"></i>
                    <h6 class="text-uppercase mt-0">Total Enviadas</h6>
                    <h2 class="m-b-20" id="stats-total">15,234</h2>
                    <span class="badge bg-primary">últimos 7 dias</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-check-circle float-end text-success"></i>
                    <h6 class="text-uppercase mt-0">Entregues</h6>
                    <h2 class="m-b-20" id="stats-entregues">14,567</h2>
                    <span class="badge bg-success">95.6%</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-eye-check float-end text-info"></i>
                    <h6 class="text-uppercase mt-0">Lidas</h6>
                    <h2 class="m-b-20" id="stats-lidas">12,890</h2>
                    <span class="badge bg-info">88.4%</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-clock-outline float-end text-warning"></i>
                    <h6 class="text-uppercase mt-0">Pendentes</h6>
                    <h2 class="m-b-20" id="stats-pendentes">234</h2>
                    <span class="badge bg-warning">1.5%</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-alert-circle float-end text-danger"></i>
                    <h6 class="text-uppercase mt-0">Erros</h6>
                    <h2 class="m-b-20" id="stats-erros">433</h2>
                    <span class="badge bg-danger">2.8%</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-chart-line float-end text-secondary"></i>
                    <h6 class="text-uppercase mt-0">Taxa Sucesso</h6>
                    <h2 class="m-b-20" id="stats-taxa">95.6%</h2>
                    <span class="badge bg-secondary">excelente</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Notificações -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-format-list-bulleted"></i> Histórico de Notificações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="notificacoes-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Destinatário</th>
                                    <th>Canal</th>
                                    <th>Título</th>
                                    <th>Aplicação</th>
                                    <th>Status</th>
                                    <th>Enviado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="notificacoes-tbody">
                                <!-- Carregado via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="dataTables_info">
                                Mostrando <span id="info-inicio">1</span> a <span id="info-fim">20</span> de <span id="info-total">15234</span> notificações
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="dataTables_paginate float-end">
                                <nav>
                                    <ul class="pagination" id="paginacao">
                                        <!-- Carregado via JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
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
                    <i class="mdi mdi-information-outline"></i> 
                    Detalhes da Notificação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-detalhes-body">
                <!-- Conteúdo carregado dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="reenviarNotificacao()">
                    <i class="mdi mdi-refresh"></i> Reenviar
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let paginaAtual = 1;
let porPagina = 20;
let notificacaoAtual = null;

document.addEventListener('DOMContentLoaded', function() {
    carregarNotificacoes();
    carregarEstatisticas();
    
    // Event listeners
    document.getElementById('busca-texto').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarNotificacoes();
        }
    });
});

function carregarNotificacoes() {
    mostrarCarregando(true);
    
    // Coleta filtros
    const filtros = {
        periodo: document.getElementById('filtro-periodo').value,
        canal: document.getElementById('filtro-canal').value,
        status: document.getElementById('filtro-status').value,
        aplicacao: document.getElementById('filtro-aplicacao').value,
        busca: document.getElementById('busca-texto').value,
        pagina: paginaAtual,
        por_pagina: porPagina
    };
    
    // Remove filtros vazios
    Object.keys(filtros).forEach(key => {
        if (filtros[key] === '' || filtros[key] === null) {
            delete filtros[key];
        }
    });
    
    const params = new URLSearchParams(filtros);
    
    fetch(`<?php echo e(route('admin.notificacoes.enviadas.dados')); ?>?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarNotificacoes(data.data);
                atualizarPaginacao(data.total, data.pagina, data.total_paginas);
                atualizarInfoPaginacao(data);
            } else {
                console.error('Erro na API:', data);
                // Mostrar dados mock em caso de erro na API
                mostrarDadosMock();
            }
        })
        .catch(error => {
            console.error('Erro de conexão:', error);
            // Mostrar dados mock em caso de erro de conexão
            mostrarDadosMock();
        })
        .finally(() => {
            mostrarCarregando(false);
        });
}

function mostrarDadosMock() {
    const dadosMock = [
        {
            id: 1,
            destinatario: "João Silva",
            email: "joao@empresa.com",
            canal: "email",
            titulo: "Bem-vindo ao Sistema",
            template: "welcome_template",
            aplicacao: "ecommerce",
            status: "entregue",
            tentativas: 1,
            enviado_em: "2025-08-04 10:30:00",
            entregue_em: "2025-08-04 10:30:15",
            lido_em: null
        },
        {
            id: 2,
            destinatario: "Maria Santos",
            email: "maria@empresa.com",
            canal: "sms",
            titulo: "Código de Verificação",
            template: "verification_code",
            aplicacao: "crm",
            status: "lido",
            tentativas: 1,
            enviado_em: "2025-08-04 09:15:00",
            entregue_em: "2025-08-04 09:15:05",
            lido_em: "2025-08-04 09:16:00"
        },
        {
            id: 3,
            destinatario: "Pedro Costa",
            email: "pedro@empresa.com",
            canal: "push",
            titulo: "Nova Promoção Disponível",
            template: "promo_template",
            aplicacao: "fidelidade",
            status: "erro",
            tentativas: 3,
            enviado_em: "2025-08-04 08:45:00",
            entregue_em: null,
            lido_em: null
        },
        {
            id: 4,
            destinatario: "Ana Lima",
            email: "ana@empresa.com",
            canal: "email",
            titulo: "Relatório Mensal",
            template: "monthly_report",
            aplicacao: "suporte",
            status: "enviado",
            tentativas: 1,
            enviado_em: "2025-08-04 07:20:00",
            entregue_em: null,
            lido_em: null
        },
        {
            id: 5,
            destinatario: "Carlos Oliveira",
            telefone: "(11) 99999-9999",
            canal: "sms",
            titulo: "Lembrete de Pagamento",
            template: "payment_reminder",
            aplicacao: "ecommerce",
            status: "pendente",
            tentativas: 1,
            enviado_em: "2025-08-04 06:00:00",
            entregue_em: null,
            lido_em: null
        }
    ];

    renderizarNotificacoes(dadosMock);
    
    // Simular dados de paginação
    const mockPaginacao = {
        total: 127,
        pagina: 1,
        por_pagina: 20,
        from: 1,
        to: 5
    };
    
    atualizarPaginacao(mockPaginacao.total, mockPaginacao.pagina, Math.ceil(mockPaginacao.total / mockPaginacao.por_pagina));
    atualizarInfoPaginacao(mockPaginacao);
    
    mostrarAlerta('Dados de demonstração carregados (API indisponível)', 'warning');
}

function carregarEstatisticas() {
    const periodo = document.getElementById('filtro-periodo').value;
    const params = new URLSearchParams({ periodo });
    
    fetch(`<?php echo e(route('admin.notificacoes.enviadas.estatisticas')); ?>?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                atualizarEstatisticas(data.data);
            } else {
                // Carregar estatísticas mock em caso de erro
                carregarEstatisticasMock();
            }
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
            // Carregar estatísticas mock em caso de erro
            carregarEstatisticasMock();
        });
}

function carregarEstatisticasMock() {
    const statsMock = {
        total: 15234,
        entregues: 14567,
        lidas: 12890,
        pendentes: 234,
        erros: 433,
        taxa_sucesso: 95.6
    };
    
    atualizarEstatisticas(statsMock);
}

function renderizarNotificacoes(notificacoes) {
    const tbody = document.getElementById('notificacoes-tbody');
    tbody.innerHTML = '';

    if (notificacoes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="mdi mdi-inbox-outline"></i><br>
                    Nenhuma notificação encontrada
                </td>
            </tr>
        `;
        return;
    }

    notificacoes.forEach(notif => {
        const statusColors = {
            'enviado': 'primary',
            'entregue': 'success', 
            'lido': 'info',
            'erro': 'danger',
            'pendente': 'warning'
        };
        
        const canalIcons = {
            'email': 'email',
            'sms': 'cellphone',
            'push': 'bell',
            'in_app': 'monitor'
        };
        
        const aplicacaoColors = {
            'ecommerce': 'primary',
            'crm': 'success',
            'fidelidade': 'warning',
            'suporte': 'info'
        };

        const row = `
            <tr onclick="verDetalhes(${notif.id})" style="cursor: pointer;">
                <td><strong>#${notif.id}</strong></td>
                <td>
                    <div>
                        <strong>${notif.destinatario || 'N/A'}</strong><br>
                        <small class="text-muted">${notif.email || notif.telefone || 'N/A'}</small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary">
                        <i class="mdi mdi-${canalIcons[notif.canal]}"></i>
                        ${notif.canal.toUpperCase()}
                    </span>
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 200px;" title="${notif.titulo || 'Sem título'}">
                        ${notif.titulo || 'Sem título'}
                    </div>
                    <small class="text-muted">Template: ${notif.template || 'N/A'}</small>
                </td>
                <td>
                    <span class="badge bg-${aplicacaoColors[notif.aplicacao] || 'secondary'}">${notif.aplicacao || 'N/A'}</span>
                </td>
                <td>
                    <span class="badge bg-${statusColors[notif.status]}">${notif.status}</span>
                    ${notif.tentativas > 1 ? `<br><small class="text-muted">${notif.tentativas} tentativas</small>` : ''}
                </td>
                <td>
                    <small>${formatarData(notif.enviado_em)}</small>
                    ${notif.entregue_em ? `<br><small class="text-success">Entregue: ${formatarData(notif.entregue_em)}</small>` : ''}
                    ${notif.lido_em ? `<br><small class="text-info">Lido: ${formatarData(notif.lido_em)}</small>` : ''}
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); verDetalhes(${notif.id})" title="Ver Detalhes">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); reenviarNotificacao(${notif.id})" title="Reenviar">
                            <i class="mdi mdi-refresh"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation(); verLogs(${notif.id})" title="Ver Logs">
                            <i class="mdi mdi-file-document-outline"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        tbody.innerHTML += row;
    });
}

function atualizarEstatisticas(stats) {
    document.getElementById('stats-total').textContent = formatarNumero(stats.total);
    document.getElementById('stats-entregues').textContent = formatarNumero(stats.entregues);
    document.getElementById('stats-lidas').textContent = formatarNumero(stats.lidas);
    document.getElementById('stats-pendentes').textContent = formatarNumero(stats.pendentes);
    document.getElementById('stats-erros').textContent = formatarNumero(stats.erros);
    document.getElementById('stats-taxa').textContent = stats.taxa_sucesso + '%';
}

function verDetalhes(notifId) {
    notificacaoAtual = notifId;
    
    fetch(`<?php echo e(url('admin/notificacoes/enviadas')); ?>/${notifId}/detalhes`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notif = data.data;
                
                const detalhes = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informações Básicas</h6>
                            <table class="table table-sm">
                                <tr><td><strong>ID:</strong></td><td>#${notif.id}</td></tr>
                                <tr><td><strong>Destinatário:</strong></td><td>${notif.destinatario_nome || 'N/A'}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${notif.destinatario_email || 'N/A'}</td></tr>
                                <tr><td><strong>Telefone:</strong></td><td>${notif.destinatario_telefone || 'N/A'}</td></tr>
                                <tr><td><strong>Canal:</strong></td><td>${notif.canal}</td></tr>
                                <tr><td><strong>Aplicação:</strong></td><td>${notif.aplicacao || 'N/A'}</td></tr>
                                <tr><td><strong>Template:</strong></td><td>${notif.template || 'N/A'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Timeline de Entrega</h6>
                            <div class="timeline-sm">
                                <div class="timeline-item">
                                    <span class="badge bg-primary">Enviado</span>
                                    <small class="text-muted">${formatarData(notif.enviado_em)}</small>
                                </div>
                                ${notif.entregue_em ? `
                                <div class="timeline-item">
                                    <span class="badge bg-success">Entregue</span>
                                    <small class="text-muted">${formatarData(notif.entregue_em)}</small>
                                </div>
                                ` : ''}
                                ${notif.lido_em ? `
                                <div class="timeline-item">
                                    <span class="badge bg-info">Lido</span>
                                    <small class="text-muted">${formatarData(notif.lido_em)}</small>
                                </div>
                                ` : ''}
                                ${notif.erro_mensagem ? `
                                <div class="timeline-item">
                                    <span class="badge bg-danger">Erro</span>
                                    <small class="text-muted">${notif.erro_mensagem}</small>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Conteúdo da Notificação</h6>
                            <div class="bg-light p-3 rounded">
                                <h5>${notif.titulo || 'Sem título'}</h5>
                                <div>${notif.conteudo || 'Sem conteúdo'}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Dados Técnicos</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Provider ID:</strong></td><td>${notif.provider_id || 'N/A'}</td></tr>
                                <tr><td><strong>Tentativas:</strong></td><td>${notif.tentativas}</td></tr>
                                <tr><td><strong>Status:</strong></td><td><span class="badge bg-primary">${notif.status}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Logs</h6>
                            <div id="logs-container" style="max-height: 200px; overflow-y: auto;">
                                <small class="text-muted">Carregando logs...</small>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('modal-detalhes-body').innerHTML = detalhes;
                new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
                
                // Carrega logs
                carregarLogs(notifId);
                
            } else {
                mostrarAlerta('Erro ao carregar detalhes: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('Erro ao carregar detalhes da notificação', 'danger');
        });
}

function carregarLogs(notifId) {
    fetch(`<?php echo e(url('admin/notificacoes/enviadas')); ?>/${notifId}/logs`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const logsContainer = document.getElementById('logs-container');
                if (data.data.length === 0) {
                    logsContainer.innerHTML = '<small class="text-muted">Nenhum log encontrado</small>';
                } else {
                    let logsHtml = '';
                    data.data.forEach(log => {
                        const nivelColor = {
                            'info': 'info',
                            'warning': 'warning',
                            'error': 'danger',
                            'success': 'success'
                        };
                        
                        logsHtml += `
                            <div class="mb-2">
                                <span class="badge bg-${nivelColor[log.nivel] || 'secondary'}">${log.nivel}</span>
                                <small class="text-muted">${formatarData(log.created_at)}</small>
                                <div><small>${log.mensagem}</small></div>
                            </div>
                        `;
                    });
                    logsContainer.innerHTML = logsHtml;
                }
            }
        })
        .catch(error => {
            console.error('Erro ao carregar logs:', error);
            document.getElementById('logs-container').innerHTML = '<small class="text-danger">Erro ao carregar logs</small>';
        });
}

function reenviarNotificacao(notifId) {
    if (!notifId && notificacaoAtual) {
        notifId = notificacaoAtual;
    }
    
    if (confirm('Deseja realmente reenviar esta notificação?')) {
        mostrarAlerta(`Reenviando notificação #${notifId}...`, 'info');
        
        fetch(`<?php echo e(url('admin/notificacoes/enviadas')); ?>/${notifId}/reenviar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarAlerta(data.message, 'success');
                if (bootstrap.Modal.getInstance(document.getElementById('modalDetalhes'))) {
                    bootstrap.Modal.getInstance(document.getElementById('modalDetalhes')).hide();
                }
                carregarNotificacoes(); // Recarrega a lista
            } else {
                mostrarAlerta('Erro ao reenviar: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('Erro ao reenviar notificação', 'danger');
        });
    }
}

function verLogs(notifId) {
    window.open(`<?php echo e(url('admin/notificacoes/enviadas')); ?>/${notifId}/logs`, '_blank');
}

function filtrarNotificacoes() {
    paginaAtual = 1; // Reset para primeira página
    carregarNotificacoes();
    carregarEstatisticas();
}

function buscarNotificacoes() {
    paginaAtual = 1; // Reset para primeira página
    carregarNotificacoes();
}

function exportarDados() {
    const filtros = {
        periodo: document.getElementById('filtro-periodo').value,
        canal: document.getElementById('filtro-canal').value,
        status: document.getElementById('filtro-status').value,
        aplicacao: document.getElementById('filtro-aplicacao').value,
        busca: document.getElementById('busca-texto').value
    };
    
    // Remove filtros vazios
    Object.keys(filtros).forEach(key => {
        if (filtros[key] === '' || filtros[key] === null) {
            delete filtros[key];
        }
    });
    
    const params = new URLSearchParams(filtros);
    
    mostrarAlerta('Preparando exportação...', 'info');
    window.open(`<?php echo e(route('admin.notificacoes.enviadas.exportar')); ?>?${params}`, '_blank');
}

function atualizarPaginacao(total, paginaAtual = 1, totalPaginas = 1) {
    const paginacao = document.getElementById('paginacao');
    
    let html = '';
    
    // Anterior
    html += `<li class="page-item ${paginaAtual === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="mudarPagina(${paginaAtual - 1})">Anterior</a>
    </li>`;
    
    // Páginas
    const inicio = Math.max(1, paginaAtual - 2);
    const fim = Math.min(totalPaginas, inicio + 4);
    
    for (let i = inicio; i <= fim; i++) {
        html += `<li class="page-item ${i === paginaAtual ? 'active' : ''}">
            <a class="page-link" href="#" onclick="mudarPagina(${i})">${i}</a>
        </li>`;
    }
    
    // Próxima
    html += `<li class="page-item ${paginaAtual === totalPaginas ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="mudarPagina(${paginaAtual + 1})">Próxima</a>
    </li>`;
    
    paginacao.innerHTML = html;
}

function atualizarInfoPaginacao(data) {
    const inicio = ((data.pagina - 1) * data.por_pagina) + 1;
    const fim = Math.min(data.pagina * data.por_pagina, data.total);
    
    document.getElementById('info-inicio').textContent = inicio;
    document.getElementById('info-fim').textContent = fim;
    document.getElementById('info-total').textContent = data.total;
}

function mudarPagina(pagina) {
    if (pagina < 1) return;
    paginaAtual = pagina;
    carregarNotificacoes();
}

function mostrarCarregando(mostrar) {
    const tbody = document.getElementById('notificacoes-tbody');
    if (mostrar) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                    <div class="mt-2">Carregando notificações...</div>
                </td>
            </tr>
        `;
    }
}

function formatarData(data) {
    if (!data) return 'N/A';
    return new Date(data).toLocaleString('pt-BR');
}

function formatarNumero(numero) {
    return new Intl.NumberFormat('pt-BR').format(numero);
}

function mostrarAlerta(mensagem, tipo) {
    const cores = {
        'success': 'success',
        'danger': 'danger', 
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
    
    setTimeout(() => {
        if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
        }
    }, 5000);
}
</script>

<style>
.tilebox-one {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table tr[onclick] {
    transition: background-color 0.2s;
}

.table tr[onclick]:hover {
    background-color: rgba(0,0,0,0.02);
}

.timeline-sm .timeline-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    border-left: 2px solid #dee2e6;
    padding-left: 10px;
    margin-bottom: 10px;
}

.timeline-sm .timeline-item:last-child {
    margin-bottom: 0;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.bg-light {
    border: 1px solid #dee2e6;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/notificacoes/enviadas.blade.php ENDPATH**/ ?>
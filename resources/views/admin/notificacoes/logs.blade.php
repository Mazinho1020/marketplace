@extends('layouts.admin')

@section('title', 'Logs de Notificação')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notificacoes.index') }}">Notificações</a></li>
                        <li class="breadcrumb-item active">Logs</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-file-document-multiple"></i> Logs do Sistema
                </h4>
            </div>
        </div>
    </div>

    <!-- Controles de Monitoramento -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Nível</label>
                                <select class="form-select" id="filtro-nivel" onchange="filtrarLogs()">
                                    <option value="">Todos</option>
                                    <option value="debug">Debug</option>
                                    <option value="info">Info</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Componente</label>
                                <select class="form-select" id="filtro-componente" onchange="filtrarLogs()">
                                    <option value="">Todos</option>
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option value="push">Push</option>
                                    <option value="in_app">In-App</option>
                                    <option value="database">Database</option>
                                    <option value="api">API</option>
                                    <option value="queue">Queue</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Período</label>
                                <select class="form-select" id="filtro-periodo" onchange="filtrarLogs()">
                                    <option value="1h" selected>Última hora</option>
                                    <option value="6h">Últimas 6 horas</option>
                                    <option value="24h">Últimas 24 horas</option>
                                    <option value="7d">Últimos 7 dias</option>
                                    <option value="30d">Últimos 30 dias</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Buscar</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="busca-logs" placeholder="Mensagem, IP, usuário...">
                                    <button class="btn btn-primary" onclick="buscarLogs()">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="btn-group d-block">
                                    <button class="btn btn-success" onclick="toggleAutoRefresh()" id="btn-refresh">
                                        <i class="mdi mdi-refresh"></i> Auto Refresh
                                    </button>
                                    <button class="btn btn-info" onclick="downloadLogs()">
                                        <i class="mdi mdi-download"></i> Download
                                    </button>
                                    <button class="btn btn-warning" onclick="limparLogs()">
                                        <i class="mdi mdi-delete-sweep"></i> Limpar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas de Logs -->
    <div class="row">
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-information float-end text-info"></i>
                    <h6 class="text-uppercase mt-0">Info</h6>
                    <h2 class="m-b-20" id="stats-info">...</h2>
                    <span class="badge bg-info">normal</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-bug float-end text-secondary"></i>
                    <h6 class="text-uppercase mt-0">Debug</h6>
                    <h2 class="m-b-20" id="stats-debug">...</h2>
                    <span class="badge bg-secondary">dev</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-alert float-end text-warning"></i>
                    <h6 class="text-uppercase mt-0">Warning</h6>
                    <h2 class="m-b-20" id="stats-warning">...</h2>
                    <span class="badge bg-warning">atenção</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-close-circle float-end text-danger"></i>
                    <h6 class="text-uppercase mt-0">Error</h6>
                    <h2 class="m-b-20" id="stats-error">...</h2>
                    <span class="badge bg-danger">crítico</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-alert-octagon float-end text-dark"></i>
                    <h6 class="text-uppercase mt-0">Critical</h6>
                    <h2 class="m-b-20" id="stats-critical">...</h2>
                    <span class="badge bg-dark">urgente</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-clock-outline float-end text-primary"></i>
                    <h6 class="text-uppercase mt-0">Última Hora</h6>
                    <h2 class="m-b-20" id="stats-hora">...</h2>
                    <span class="badge bg-primary">ativo</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Console de Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-console"></i> Console de Logs
                        <span class="badge bg-success ms-2" id="status-conexao">Online</span>
                        <span class="badge bg-info ms-1" id="contador-logs">0 logs</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="console-logs" class="log-console">
                        <!-- Logs carregados em tempo real -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Análise de Erros -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-chart-timeline-variant"></i> Timeline de Eventos
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="logs-timeline-chart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-alert-circle"></i> Principais Erros
                    </h5>
                </div>
                <div class="card-body">
                    <div id="principais-erros">
                        <!-- Carregado via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes do Log -->
<div class="modal fade" id="modalLogDetalhes" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-file-document-outline"></i> 
                    Detalhes do Log
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-log-body">
                <!-- Conteúdo carregado dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="copiarLog()">
                    <i class="mdi mdi-content-copy"></i> Copiar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let autoRefresh = false;
let refreshInterval = null;
let logCount = 0;
let timelineChart = null;
let graficosInicializados = false;

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se os gráficos já foram inicializados para evitar duplicação
    if (graficosInicializados) {
        console.log('Gráficos já inicializados, evitando duplicação - Logs');
        return;
    }
    
    // Aguardar o Chart.js carregar completamente
    function inicializar() {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js carregado com sucesso - Logs');
            inicializarLogs();
        } else {
            console.log('Aguardando Chart.js carregar...');
            setTimeout(inicializar, 100);
        }
    }
    
    inicializar();
});

function inicializarLogs() {
    if (graficosInicializados) {
        console.log('Logs já inicializados, evitando duplicação');
        return;
    }
    
    try {
        carregarLogs();
        carregarEstatisticas();
        criarGraficoTimeline();
        graficosInicializados = true;
        
        // Event listeners
        document.getElementById('busca-logs').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarLogs();
            }
        });
        
        console.log('Logs inicializados com sucesso');
    } catch (error) {
        console.error('Erro ao inicializar logs:', error);
    }
}

function carregarLogs() {
    const nivel = document.getElementById('filtro-nivel').value;
    const componente = document.getElementById('filtro-componente').value;
    const periodo = document.getElementById('filtro-periodo').value;
    const busca = document.getElementById('busca-logs').value;

    // Mostrar loading
    const console = document.getElementById('console-logs');
    console.innerHTML = '<div class="text-center p-4"><i class="mdi mdi-loading mdi-spin"></i> Carregando logs...</div>';

    const params = new URLSearchParams({
        nivel: nivel || '',
        componente: componente || '',
        periodo: periodo || '1h',
        busca: busca || '',
        limite: 50
    });

    fetch(`/admin/notificacoes/logs/api/dados?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.innerHTML = '';
                
                if (data.logs.length === 0) {
                    console.innerHTML = '<div class="text-center text-muted p-4">Nenhum log encontrado para os filtros selecionados</div>';
                    return;
                }

                data.logs.forEach(log => {
                    adicionarLogConsole(log);
                });
                
                logCount = data.logs.length;
                document.getElementById('contador-logs').textContent = `${logCount} logs`;
            } else {
                console.innerHTML = '<div class="text-center text-danger p-4">Erro ao carregar logs: ' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar logs:', error);
            console.innerHTML = '<div class="text-center text-danger p-4">Erro de conexão ao carregar logs</div>';
        });
}

function adicionarLogConsole(log) {
    const console = document.getElementById('console-logs');
    
    const nivelColors = {
        'debug': 'text-muted',
        'info': 'text-info', 
        'warning': 'text-warning',
        'error': 'text-danger',
        'critical': 'text-white bg-danger'
    };
    
    const componenteColors = {
        'email': 'badge-primary',
        'sms': 'badge-success',
        'push': 'badge-warning',
        'in_app': 'badge-info',
        'database': 'badge-secondary',
        'api': 'badge-primary',
        'queue': 'badge-dark'
    };
    
    const timestamp = new Date(log.timestamp).toLocaleString();
    
    const logHtml = `
        <div class="log-entry ${nivelColors[log.nivel]}" onclick="verDetalhesLog('${log.id}')">
            <div class="log-header">
                <span class="log-time">[${timestamp}]</span>
                <span class="badge ${componenteColors[log.componente] || 'badge-secondary'}">${(log.componente || 'SISTEMA').toUpperCase()}</span>
                <span class="badge bg-${getNivelColor(log.nivel)}">${log.nivel.toUpperCase()}</span>
                <span class="log-ip">${log.ip}</span>
            </div>
            <div class="log-message">${log.mensagem}</div>
            ${log.contexto && Object.keys(log.contexto).length > 0 ? `<div class="log-context"><small>Contexto: ${JSON.stringify(log.contexto, null, 2)}</small></div>` : ''}
        </div>
    `;
    
    console.insertAdjacentHTML('afterbegin', logHtml);
    
    // Limitar número de logs no console (performance)
    const logs = console.children;
    if (logs.length > 100) {
        console.removeChild(logs[logs.length - 1]);
    }
}

function getNivelColor(nivel) {
    const colors = {
        'debug': 'secondary',
        'info': 'info',
        'warning': 'warning', 
        'error': 'danger',
        'critical': 'dark'
    };
    return colors[nivel] || 'secondary';
}

function verDetalhesLog(logId) {
    fetch(`/admin/notificacoes/logs/api/detalhes/${logId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const log = data.log;
                const detalhes = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informações Básicas</h6>
                            <table class="table table-sm">
                                <tr><td><strong>ID:</strong></td><td>${log.id}</td></tr>
                                <tr><td><strong>Timestamp:</strong></td><td>${new Date(log.timestamp).toLocaleString()}</td></tr>
                                <tr><td><strong>Nível:</strong></td><td><span class="badge bg-${getNivelColor(log.nivel)}">${log.nivel.toUpperCase()}</span></td></tr>
                                <tr><td><strong>Componente:</strong></td><td>${log.componente || 'N/A'}</td></tr>
                                <tr><td><strong>IP:</strong></td><td>${log.ip}</td></tr>
                                <tr><td><strong>User Agent:</strong></td><td>${log.user_agent}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Contexto da Notificação</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Notificação ID:</strong></td><td>${log.notificacao_id || 'N/A'}</td></tr>
                                <tr><td><strong>Destinatário:</strong></td><td>${log.destinatario}</td></tr>
                                <tr><td><strong>Título:</strong></td><td>${log.titulo || 'N/A'}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>${log.status || 'N/A'}</td></tr>
                                <tr><td><strong>Prioridade:</strong></td><td>${log.prioridade || 'N/A'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Mensagem do Log</h6>
                            <div class="bg-light p-3 rounded" style="font-family: monospace;">
${log.mensagem}
                            </div>
                        </div>
                    </div>
                    ${log.contexto && Object.keys(log.contexto).length > 0 ? `
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Contexto Adicional</h6>
                            <pre class="bg-dark text-light p-3 rounded"><code>${JSON.stringify(log.contexto, null, 2)}</code></pre>
                        </div>
                    </div>
                    ` : ''}
                    ${log.dados_processados && Object.keys(log.dados_processados).length > 0 ? `
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Dados Processados</h6>
                            <pre class="bg-secondary text-light p-3 rounded"><code>${JSON.stringify(log.dados_processados, null, 2)}</code></pre>
                        </div>
                    </div>
                    ` : ''}
                    ${log.dados_evento && Object.keys(log.dados_evento).length > 0 ? `
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Dados do Evento</h6>
                            <pre class="bg-info text-light p-3 rounded"><code>${JSON.stringify(log.dados_evento, null, 2)}</code></pre>
                        </div>
                    </div>
                    ` : ''}
                `;
                
                document.getElementById('modal-log-body').innerHTML = detalhes;
                new bootstrap.Modal(document.getElementById('modalLogDetalhes')).show();
            } else {
                mostrarAlerta('Erro ao carregar detalhes do log: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar detalhes:', error);
            mostrarAlerta('Erro de conexão ao carregar detalhes', 'danger');
        });
}

function carregarEstatisticas() {
    const periodo = document.getElementById('filtro-periodo').value;
    
    fetch(`/admin/notificacoes/logs/api/estatisticas?periodo=${periodo}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Atualizar contadores nas estatísticas
                document.getElementById('stats-info').textContent = data.estatisticas.info.toLocaleString();
                document.getElementById('stats-debug').textContent = data.estatisticas.debug.toLocaleString();
                document.getElementById('stats-warning').textContent = data.estatisticas.warning.toLocaleString();
                document.getElementById('stats-error').textContent = data.estatisticas.error.toLocaleString();
                document.getElementById('stats-critical').textContent = data.estatisticas.critical.toLocaleString();
                
                // Calcular total da última hora
                const totalUltimaHora = data.estatisticas.info + data.estatisticas.debug + 
                                      data.estatisticas.warning + data.estatisticas.error + 
                                      data.estatisticas.critical;
                document.getElementById('stats-hora').textContent = totalUltimaHora.toLocaleString();
                
                // Atualizar gráfico de timeline se existir
                if (timelineChart && data.timeline) {
                    atualizarGraficoTimeline(data.timeline);
                }
                
                // Atualizar principais erros
                if (data.principais_erros) {
                    atualizarPrincipaisErros(data.principais_erros);
                }
            }
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
        });
}

function atualizarPrincipaisErros(erros) {
    const container = document.getElementById('principais-erros');
    container.innerHTML = '';
    
    if (erros.length === 0) {
        container.innerHTML = '<div class="text-center text-muted p-3">Nenhum erro registrado no período</div>';
        return;
    }
    
    erros.forEach(erro => {
        const timeAgo = new Date(erro.ultima_ocorrencia).toLocaleString();
        container.innerHTML += `
            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-start border-danger border-3 bg-light">
                <div>
                    <h6 class="mb-1">${erro.erro}</h6>
                    <small class="text-muted">Última: ${timeAgo}</small>
                </div>
                <span class="badge bg-danger">${erro.ocorrencias}</span>
            </div>
        `;
    });
}

function atualizarGraficoTimeline(timelineData) {
    if (!timelineChart) return;
    
    // Processar dados para o gráfico
    const horas = [];
    const dadosInfo = [];
    const dadosWarning = [];
    const dadosError = [];
    
    // Criar estrutura de dados para as últimas 24 horas
    for (let i = 23; i >= 0; i--) {
        const hora = new Date();
        hora.setHours(hora.getHours() - i, 0, 0, 0);
        const horaFormatada = hora.getHours().toString().padStart(2, '0') + ':00';
        horas.push(horaFormatada);
        
        // Buscar dados para esta hora
        const dadosHora = timelineData.filter(item => item.hora === horaFormatada);
        dadosInfo.push(dadosHora.find(d => d.nivel === 'info')?.total || 0);
        dadosWarning.push(dadosHora.find(d => d.nivel === 'warning')?.total || 0);
        dadosError.push(dadosHora.find(d => d.nivel === 'error')?.total || 0);
    }
    
    timelineChart.data.labels = horas;
    timelineChart.data.datasets[0].data = dadosInfo;
    timelineChart.data.datasets[1].data = dadosWarning;
    timelineChart.data.datasets[2].data = dadosError;
    timelineChart.update();
}

function criarGraficoTimeline() {
    try {
        const canvasElement = document.getElementById('logs-timeline-chart');
        if (canvasElement) {
            const ctx = canvasElement.getContext('2d');
            
            timelineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00'],
                    datasets: [
                        {
                            label: 'Info',
                            data: [45, 52, 38, 65, 42, 58, 71],
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Warning',
                            data: [12, 8, 15, 22, 18, 25, 19],
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Error',
                            data: [2, 1, 4, 7, 3, 8, 5],
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantidade de Logs'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Horário'
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Erro ao criar gráfico timeline:', error);
    }
}

function toggleAutoRefresh() {
    autoRefresh = !autoRefresh;
    const btn = document.getElementById('btn-refresh');
    
    if (autoRefresh) {
        btn.innerHTML = '<i class="mdi mdi-pause"></i> Pausar';
        btn.className = 'btn btn-warning';
        refreshInterval = setInterval(() => {
            carregarLogs();
            carregarEstatisticas();
        }, 5000);
        mostrarAlerta('Auto refresh ativado (5s)', 'success');
    } else {
        btn.innerHTML = '<i class="mdi mdi-refresh"></i> Auto Refresh';
        btn.className = 'btn btn-success';
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        mostrarAlerta('Auto refresh desativado', 'info');
    }
}

function filtrarLogs() {
    carregarLogs();
    carregarEstatisticas();
    mostrarAlerta('Filtros aplicados', 'info');
}

function buscarLogs() {
    carregarLogs();
    const termo = document.getElementById('busca-logs').value;
    if (termo) {
        mostrarAlerta(`Buscando logs: ${termo}`, 'info');
    }
}

function downloadLogs() {
    mostrarAlerta('Preparando download dos logs...', 'info');
    // Implementar download real aqui
}

function limparLogs() {
    if (confirm('Deseja realmente limpar todos os logs do console?')) {
        document.getElementById('console-logs').innerHTML = '';
        logCount = 0;
        document.getElementById('contador-logs').textContent = '0 logs';
        mostrarAlerta('Console limpo', 'success');
    }
}

function copiarLog() {
    const conteudo = document.getElementById('modal-log-body').textContent;
    navigator.clipboard.writeText(conteudo).then(() => {
        mostrarAlerta('Log copiado para área de transferência', 'success');
    });
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
.log-console {
    height: 500px;
    overflow-y: auto;
    background: #1e1e1e;
    color: #fff;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    padding: 15px;
}

.log-entry {
    margin-bottom: 10px;
    padding: 8px;
    border-left: 3px solid #007bff;
    background: rgba(255,255,255,0.05);
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.log-entry:hover {
    background: rgba(255,255,255,0.1);
}

.log-entry.text-danger {
    border-left-color: #dc3545;
    background: rgba(220,53,69,0.1);
}

.log-entry.text-warning {
    border-left-color: #ffc107;
    background: rgba(255,193,7,0.1);
}

.log-entry.text-info {
    border-left-color: #17a2b8;
    background: rgba(23,162,184,0.1);
}

.log-entry.text-muted {
    border-left-color: #6c757d;
    background: rgba(108,117,125,0.1);
}

.log-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.log-time {
    color: #28a745;
    font-weight: bold;
}

.log-ip {
    color: #6c757d;
    font-size: 0.8rem;
    margin-left: auto;
}

.log-message {
    color: #fff;
    margin-bottom: 5px;
}

.log-context {
    color: #6c757d;
    font-size: 0.8rem;
    white-space: pre-wrap;
}

.badge {
    font-size: 0.7rem;
}

.badge-primary { background-color: #007bff; }
.badge-success { background-color: #28a745; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-info { background-color: #17a2b8; }
.badge-secondary { background-color: #6c757d; }
.badge-dark { background-color: #343a40; }

.tilebox-one {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header.bg-dark {
    border-bottom: 1px solid #495057;
}

.modal-xl .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

pre {
    max-height: 300px;
    overflow-y: auto;
}

.border-start {
    border-left-width: 3px !important;
}

.border-3 {
    border-width: 3px !important;
}
</style>
@endpush
@endsection

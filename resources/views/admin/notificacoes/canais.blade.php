@extends('layouts.admin')

@section('title', 'Canais de Notificação')

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
                        <li class="breadcrumb-item active">Canais</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-send"></i> Canais de Notificação
                </h4>
            </div>
        </div>
    </div>

    <!-- Status Geral -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="status-circle bg-success">
                                <i class="mdi mdi-email h3 text-white"></i>
                            </div>
                            <h5 class="mt-2">Email</h5>
                            <span class="badge bg-success">Operacional</span>
                            <p class="text-muted small mt-1">99.9% uptime</p>
                        </div>
                        
                        <div class="col-md-3 text-center">
                            <div class="status-circle bg-warning">
                                <i class="mdi mdi-cellphone h3 text-white"></i>
                            </div>
                            <h5 class="mt-2">SMS</h5>
                            <span class="badge bg-warning">Configuração Pendente</span>
                            <p class="text-muted small mt-1">Aguardando gateway</p>
                        </div>
                        
                        <div class="col-md-3 text-center">
                            <div class="status-circle bg-success">
                                <i class="mdi mdi-bell h3 text-white"></i>
                            </div>
                            <h5 class="mt-2">Push</h5>
                            <span class="badge bg-success">Operacional</span>
                            <p class="text-muted small mt-1">Firebase ativo</p>
                        </div>
                        
                        <div class="col-md-3 text-center">
                            <div class="status-circle bg-info">
                                <i class="mdi mdi-monitor h3 text-white"></i>
                            </div>
                            <h5 class="mt-2">In-App</h5>
                            <span class="badge bg-info">Operacional</span>
                            <p class="text-muted small mt-1">WebSocket ativo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configurações Detalhadas -->
    <div class="row">
        <!-- Email -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-email"></i> Configuração Email
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-success">Ativo</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Servidor SMTP:</strong></div>
                        <div class="col-sm-8">smtp.gmail.com</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Porta:</strong></div>
                        <div class="col-sm-8">587 (TLS)</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Remetente:</strong></div>
                        <div class="col-sm-8">noreply@empresa.com</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Limite/Hora:</strong></div>
                        <div class="col-sm-8">
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 25%"></div>
                            </div>
                            <small class="text-muted">250 de 1000 enviados</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Taxa Entrega:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-success">98.7%</span>
                            <small class="text-muted">(últimas 24h)</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" onclick="testarCanal('email')">
                            <i class="mdi mdi-test-tube"></i> Testar Email
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="configurarCanal('email')">
                            <i class="mdi mdi-cog"></i> Configurar
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="verLogs('email')">
                            <i class="mdi mdi-file-document-outline"></i> Ver Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cellphone"></i> Configuração SMS
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-warning">Pendente</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Gateway:</strong></div>
                        <div class="col-sm-8">
                            <select class="form-select form-select-sm">
                                <option>Selecionar gateway...</option>
                                <option>Twilio</option>
                                <option>AWS SNS</option>
                                <option>TotalVoice</option>
                                <option>Zenvia</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Créditos:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-muted">Não configurado</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Remetente:</strong></div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" placeholder="Nome do remetente">
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert"></i>
                        <strong>Configuração Necessária:</strong> 
                        Configure um gateway SMS para habilitar o envio de notificações por SMS.
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-warning btn-sm" onclick="configurarCanal('sms')">
                            <i class="mdi mdi-cog"></i> Configurar Gateway
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="mdi mdi-test-tube"></i> Testar SMS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Push Notifications -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-bell"></i> Push Notifications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-success">Ativo</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Provedor:</strong></div>
                        <div class="col-sm-8">Firebase Cloud Messaging</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Projeto ID:</strong></div>
                        <div class="col-sm-8">
                            <code>marketplace-push-****</code>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Dispositivos:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-primary">1,234 ativos</span>
                            <small class="text-muted">(Android: 789, iOS: 445)</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Taxa Entrega:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-success">96.2%</span>
                            <small class="text-muted">(últimas 24h)</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Última Sincronização:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-muted">5 minutos atrás</span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-info btn-sm" onclick="testarCanal('push')">
                            <i class="mdi mdi-test-tube"></i> Enviar Teste
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="configurarCanal('push')">
                            <i class="mdi mdi-cog"></i> Configurar
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="sincronizarDispositivos()">
                            <i class="mdi mdi-sync"></i> Sincronizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- In-App Notifications -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-monitor"></i> Notificações In-App
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-success">Ativo</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>WebSocket:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-success">Conectado</span>
                            <i class="mdi mdi-circle text-success blink"></i>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Conexões Ativas:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-primary" id="conexoes-ativas">156 usuários</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Latência Média:</strong></div>
                        <div class="col-sm-8">
                            <span class="text-success">12ms</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Mensagens/Min:</strong></div>
                        <div class="col-sm-8">
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: 40%"></div>
                            </div>
                            <small class="text-muted">40 de 100 por minuto</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Servidor:</strong></div>
                        <div class="col-sm-8">
                            <code>ws://localhost:6001</code>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-secondary btn-sm" onclick="testarCanal('in_app')">
                            <i class="mdi mdi-test-tube"></i> Testar Broadcast
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="configurarCanal('in_app')">
                            <i class="mdi mdi-cog"></i> Configurar
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="monitorarConexoes()">
                            <i class="mdi mdi-monitor-dashboard"></i> Monitor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas dos Canais -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-chart-line"></i> Estatísticas por Canal (Últimos 7 dias)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <canvas id="canal-stats-chart" height="100"></canvas>
                        </div>
                        <div class="col-lg-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Canal</th>
                                            <th class="text-end">Enviados</th>
                                            <th class="text-end">Taxa</th>
                                        </tr>
                                    </thead>
                                    <tbody id="canal-stats-table">
                                        <!-- Carregado via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs de Atividade -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-history"></i> Log de Atividades dos Canais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="logs-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Canal</th>
                                    <th>Evento</th>
                                    <th>Status</th>
                                    <th>Detalhes</th>
                                </tr>
                            </thead>
                            <tbody id="logs-tbody">
                                <!-- Carregado via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let canalChart;

document.addEventListener('DOMContentLoaded', function() {
    carregarEstatisticas();
    carregarLogs();
    atualizarConexoesAtivas();
    
    // Atualizar dados em tempo real
    setInterval(atualizarConexoesAtivas, 5000);
    setInterval(carregarLogs, 30000);
});

function carregarEstatisticas() {
    const ctx = document.getElementById('canal-stats-chart').getContext('2d');
    
    const dados = {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [
            {
                label: 'Email',
                data: [120, 135, 142, 158, 163, 145, 122],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            },
            {
                label: 'Push',
                data: [89, 92, 98, 105, 112, 108, 95],
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4
            },
            {
                label: 'In-App',
                data: [45, 52, 48, 55, 58, 62, 49],
                borderColor: '#6c757d',
                backgroundColor: 'rgba(108, 117, 125, 0.1)',
                tension: 0.4
            },
            {
                label: 'SMS',
                data: [0, 0, 0, 0, 0, 0, 0],
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                borderDash: [5, 5]
            }
        ]
    };

    canalChart = new Chart(ctx, {
        type: 'line',
        data: dados,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Notificações Enviadas'
                    }
                }
            }
        }
    });

    // Atualizar tabela de estatísticas
    const tableData = [
        { canal: 'Email', enviados: 985, taxa: '98.7%', cor: 'primary' },
        { canal: 'Push', enviados: 667, taxa: '96.2%', cor: 'info' },
        { canal: 'In-App', enviados: 379, taxa: '100%', cor: 'secondary' },
        { canal: 'SMS', enviados: 0, taxa: '-', cor: 'warning' }
    ];

    const tbody = document.getElementById('canal-stats-table');
    tbody.innerHTML = '';

    tableData.forEach(item => {
        tbody.innerHTML += `
            <tr>
                <td>
                    <span class="badge bg-${item.cor}">${item.canal}</span>
                </td>
                <td class="text-end">${item.enviados.toLocaleString()}</td>
                <td class="text-end">
                    <span class="text-${item.taxa === '-' ? 'muted' : 'success'}">${item.taxa}</span>
                </td>
            </tr>
        `;
    });
}

function carregarLogs() {
    const logs = [
        {
            timestamp: new Date().toLocaleString(),
            canal: 'Email',
            evento: 'Notificação enviada',
            status: 'sucesso',
            detalhes: 'Confirmação de pedido #1234'
        },
        {
            timestamp: new Date(Date.now() - 300000).toLocaleString(),
            canal: 'Push',
            evento: 'Dispositivo registrado',
            status: 'sucesso',
            detalhes: 'Android - Token atualizado'
        },
        {
            timestamp: new Date(Date.now() - 600000).toLocaleString(),
            canal: 'In-App',
            evento: 'Broadcast enviado',
            status: 'sucesso',
            detalhes: '156 usuários conectados'
        },
        {
            timestamp: new Date(Date.now() - 900000).toLocaleString(),
            canal: 'Email',
            evento: 'Falha no envio',
            status: 'erro',
            detalhes: 'Email inválido: user@invalid.domain'
        },
        {
            timestamp: new Date(Date.now() - 1200000).toLocaleString(),
            canal: 'SMS',
            evento: 'Tentativa de envio',
            status: 'erro',
            detalhes: 'Gateway não configurado'
        }
    ];

    const tbody = document.getElementById('logs-tbody');
    tbody.innerHTML = '';

    logs.forEach(log => {
        const statusClass = log.status === 'sucesso' ? 'success' : 'danger';
        const statusIcon = log.status === 'sucesso' ? 'check-circle' : 'alert-circle';
        
        tbody.innerHTML += `
            <tr>
                <td><small>${log.timestamp}</small></td>
                <td>
                    <span class="badge bg-secondary">${log.canal}</span>
                </td>
                <td>${log.evento}</td>
                <td>
                    <span class="badge bg-${statusClass}">
                        <i class="mdi mdi-${statusIcon}"></i>
                        ${log.status}
                    </span>
                </td>
                <td><small class="text-muted">${log.detalhes}</small></td>
            </tr>
        `;
    });
}

function atualizarConexoesAtivas() {
    // Simular variação de conexões ativas
    const baseConexoes = 156;
    const variacao = Math.floor(Math.random() * 10) - 5;
    const novoTotal = Math.max(0, baseConexoes + variacao);
    
    document.getElementById('conexoes-ativas').textContent = `${novoTotal} usuários`;
}

function testarCanal(canal) {
    const mensagens = {
        email: 'Enviando email de teste...',
        sms: 'Enviando SMS de teste...',
        push: 'Enviando push notification de teste...',
        in_app: 'Enviando notificação in-app de teste...'
    };
    
    mostrarAlerta(mensagens[canal], 'info');
    
    // Simular envio
    setTimeout(() => {
        mostrarAlerta(`Teste do canal ${canal.toUpperCase()} enviado com sucesso!`, 'success');
    }, 2000);
}

function configurarCanal(canal) {
    window.open(`/admin/notificacoes/canais/${canal}/configurar`, '_blank');
}

function verLogs(canal) {
    window.open(`/admin/notificacoes/canais/${canal}/logs`, '_blank');
}

function sincronizarDispositivos() {
    mostrarAlerta('Sincronizando dispositivos...', 'info');
    
    setTimeout(() => {
        mostrarAlerta('Dispositivos sincronizados com sucesso!', 'success');
    }, 3000);
}

function monitorarConexoes() {
    window.open('/admin/notificacoes/canais/in_app/monitor', '_blank');
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
.status-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.blink {
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}

.progress {
    height: 8px;
}

.card-header.bg-primary,
.card-header.bg-warning,
.card-header.bg-info,
.card-header.bg-secondary {
    border-bottom: none;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}

#logs-table {
    font-size: 0.9rem;
}

.alert {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endpush
@endsection

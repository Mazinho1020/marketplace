@extends('layouts.admin')

@section('title', 'Diagnóstico do Sistema de Notificações')

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
                        <li class="breadcrumb-item active">Diagnóstico</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-stethoscope"></i> Diagnóstico do Sistema
                </h4>
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-play-circle"></i> Executar Diagnósticos
                        </h5>
                        <div>
                            <button class="btn btn-primary" onclick="executarDiagnosticoCompleto()">
                                <i class="mdi mdi-refresh"></i> Diagnóstico Completo
                            </button>
                            <button class="btn btn-outline-success" onclick="limparLogs()">
                                <i class="mdi mdi-broom"></i> Limpar Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Geral -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-heart-pulse"></i> Status Geral do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="status-geral">
                        <!-- Carregado via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verificações das Tabelas -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-database"></i> Verificação das Tabelas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Tabela</th>
                                    <th>Status</th>
                                    <th>Registros</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-verificacao">
                                <!-- Carregado via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cog"></i> Verificação dos Services
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Último Uso</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="services-verificacao">
                                <!-- Carregado via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testes de Conectividade -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-wifi"></i> Testes de Conectividade
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="border rounded p-3 mb-3 text-center" id="teste-database">
                                <i class="mdi mdi-database h3 text-primary"></i>
                                <h6>Banco de Dados</h6>
                                <div class="status-indicator">
                                    <span class="badge bg-secondary">Verificando...</span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary mt-2" onclick="testarDatabase()">
                                    Testar
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="border rounded p-3 mb-3 text-center" id="teste-email">
                                <i class="mdi mdi-email h3 text-success"></i>
                                <h6>Email (SMTP)</h6>
                                <div class="status-indicator">
                                    <span class="badge bg-secondary">Verificando...</span>
                                </div>
                                <button class="btn btn-sm btn-outline-success mt-2" onclick="testarEmail()">
                                    Testar
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="border rounded p-3 mb-3 text-center" id="teste-sms">
                                <i class="mdi mdi-cellphone h3 text-warning"></i>
                                <h6>SMS Gateway</h6>
                                <div class="status-indicator">
                                    <span class="badge bg-secondary">Verificando...</span>
                                </div>
                                <button class="btn btn-sm btn-outline-warning mt-2" onclick="testarSms()">
                                    Testar
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="border rounded p-3 mb-3 text-center" id="teste-push">
                                <i class="mdi mdi-bell h3 text-info"></i>
                                <h6>Push Notifications</h6>
                                <div class="status-indicator">
                                    <span class="badge bg-secondary">Verificando...</span>
                                </div>
                                <button class="btn btn-sm btn-outline-info mt-2" onclick="testarPush()">
                                    Testar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance e Logs -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-speedometer"></i> Análise de Performance
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="grafico-performance" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-gauge"></i> Métricas Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Uso de Memória</span>
                            <span id="memoria-uso">0%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" id="memoria-barra" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>CPU</span>
                            <span id="cpu-uso">0%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" id="cpu-barra" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Disco</span>
                            <span id="disco-uso">0%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" id="disco-barra" style="width: 0%"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <h6>Tempo de Resposta Médio</h6>
                        <h3 id="tempo-resposta" class="text-primary">-</h3>
                        <small class="text-muted">milissegundos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log de Diagnósticos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-file-document"></i> Log de Diagnósticos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="bg-dark text-white p-3 rounded" style="height: 300px; overflow-y: auto;" id="log-diagnostico">
                        <div class="text-center text-muted">
                            <i class="mdi mdi-loading mdi-spin"></i> Aguardando diagnósticos...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let graficoPerformance;
let logContainer;
let graficosInicializados = false;

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se os gráficos já foram inicializados para evitar duplicação
    if (graficosInicializados) {
        console.log('Gráficos já inicializados, evitando duplicação - Diagnóstico');
        return;
    }
    
    // Aguardar o Chart.js carregar completamente
    function inicializar() {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js carregado com sucesso - Diagnóstico');
            inicializarDiagnostico();
        } else {
            console.log('Aguardando Chart.js carregar...');
            setTimeout(inicializar, 100);
        }
    }
    
    inicializar();
});

function inicializarDiagnostico() {
    if (graficosInicializados) {
        console.log('Diagnóstico já inicializado, evitando duplicação');
        return;
    }
    
    try {
        logContainer = document.getElementById('log-diagnostico');
        configurarGraficoPerformance();
        executarDiagnosticoCompleto();
        graficosInicializados = true;
        
        // Atualizar métricas a cada 30 segundos
        setInterval(atualizarMetricas, 30000);
        console.log('Diagnóstico inicializado com sucesso');
    } catch (error) {
        console.error('Erro ao inicializar diagnóstico:', error);
    }
}

function configurarGraficoPerformance() {
    try {
        const canvasElement = document.getElementById('grafico-performance');
        if (canvasElement) {
            const ctx = canvasElement.getContext('2d');
            graficoPerformance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Tempo de Resposta (ms)',
                        data: [],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Notificações/min',
                        data: [],
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Erro ao criar gráfico de performance:', error);
    }
}

function executarDiagnosticoCompleto() {
    adicionarLog('🔄 Iniciando diagnóstico completo do sistema...', 'info');
    
    verificarStatusGeral();
    verificarTabelas();
    verificarServices();
    testarConectividade();
    atualizarMetricas();
}

function verificarStatusGeral() {
    adicionarLog('📊 Verificando status geral...', 'info');
    
    fetch('/admin/notificacoes/api/diagnostico/status-geral')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('status-geral');
            container.innerHTML = '';
            
            data.forEach(item => {
                const status = item.status ? 'success' : 'danger';
                const icone = item.status ? 'check-circle' : 'alert-circle';
                
                container.innerHTML += `
                    <div class="col-lg-3 col-md-6">
                        <div class="border rounded p-3 mb-3 text-center">
                            <i class="mdi mdi-${icone} h3 text-${status}"></i>
                            <h6>${item.nome}</h6>
                            <span class="badge bg-${status}">${item.mensagem}</span>
                        </div>
                    </div>
                `;
            });
            
            adicionarLog('✅ Status geral verificado', 'success');
        })
        .catch(error => {
            adicionarLog('❌ Erro ao verificar status geral: ' + error.message, 'error');
        });
}

function verificarTabelas() {
    adicionarLog('🗄️ Verificando integridade das tabelas...', 'info');
    
    fetch('/admin/notificacoes/api/diagnostico/tabelas')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabela-verificacao');
            tbody.innerHTML = '';
            
            data.forEach(tabela => {
                const status = tabela.existe ? 'success' : 'danger';
                const statusTexto = tabela.existe ? 'OK' : 'Não existe';
                
                tbody.innerHTML += `
                    <tr>
                        <td>${tabela.nome}</td>
                        <td><span class="badge bg-${status}">${statusTexto}</span></td>
                        <td>${tabela.registros}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="analisarTabela('${tabela.nome}')">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            adicionarLog('✅ Verificação de tabelas concluída', 'success');
        })
        .catch(error => {
            adicionarLog('❌ Erro ao verificar tabelas: ' + error.message, 'error');
        });
}

function verificarServices() {
    adicionarLog('⚙️ Verificando services...', 'info');
    
    fetch('/admin/notificacoes/api/diagnostico/services')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('services-verificacao');
            tbody.innerHTML = '';
            
            data.forEach(service => {
                const status = service.funcionando ? 'success' : 'danger';
                const statusTexto = service.funcionando ? 'Funcionando' : 'Com problemas';
                
                tbody.innerHTML += `
                    <tr>
                        <td>${service.nome}</td>
                        <td><span class="badge bg-${status}">${statusTexto}</span></td>
                        <td>${service.ultimo_uso || 'Nunca'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="testarService('${service.nome}')">
                                <i class="mdi mdi-test-tube"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            adicionarLog('✅ Verificação de services concluída', 'success');
        })
        .catch(error => {
            adicionarLog('❌ Erro ao verificar services: ' + error.message, 'error');
        });
}

function testarConectividade() {
    testarDatabase();
    testarEmail();
    testarSms();
    testarPush();
}

function testarDatabase() {
    adicionarLog('🔍 Testando conexão com banco de dados...', 'info');
    
    fetch('/admin/notificacoes/api/diagnostico/teste-database')
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('#teste-database .status-indicator');
            const status = data.success ? 'success' : 'danger';
            const texto = data.success ? 'Conectado' : 'Falha';
            
            container.innerHTML = `<span class="badge bg-${status}">${texto}</span>`;
            
            if (data.success) {
                adicionarLog('✅ Banco de dados: Conectado (' + data.tempo + 'ms)', 'success');
            } else {
                adicionarLog('❌ Banco de dados: ' + data.erro, 'error');
            }
        })
        .catch(error => {
            adicionarLog('❌ Erro ao testar banco de dados: ' + error.message, 'error');
        });
}

function testarEmail() {
    adicionarLog('📧 Testando configuração de email...', 'info');
    
    fetch('/admin/notificacoes/api/diagnostico/teste-email')
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('#teste-email .status-indicator');
            const status = data.success ? 'success' : 'danger';
            const texto = data.success ? 'Configurado' : 'Falha';
            
            container.innerHTML = `<span class="badge bg-${status}">${texto}</span>`;
            
            if (data.success) {
                adicionarLog('✅ Email: Configuração OK', 'success');
            } else {
                adicionarLog('❌ Email: ' + data.erro, 'error');
            }
        })
        .catch(error => {
            adicionarLog('❌ Erro ao testar email: ' + error.message, 'error');
        });
}

function testarSms() {
    adicionarLog('📱 Testando gateway SMS...', 'info');
    
    fetch('/admin/notificacoes/api/diagnostico/teste-sms')
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('#teste-sms .status-indicator');
            const status = data.success ? 'success' : 'warning';
            const texto = data.success ? 'Configurado' : 'Não configurado';
            
            container.innerHTML = `<span class="badge bg-${status}">${texto}</span>`;
            
            if (data.success) {
                adicionarLog('✅ SMS: Gateway configurado', 'success');
            } else {
                adicionarLog('⚠️ SMS: Gateway não configurado', 'warning');
            }
        })
        .catch(error => {
            adicionarLog('❌ Erro ao testar SMS: ' + error.message, 'error');
        });
}

function testarPush() {
    adicionarLog('🔔 Testando push notifications...', 'info');
    
    fetch('/admin/notificacoes/api/diagnostico/teste-push')
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('#teste-push .status-indicator');
            const status = data.success ? 'success' : 'warning';
            const texto = data.success ? 'Configurado' : 'Não configurado';
            
            container.innerHTML = `<span class="badge bg-${status}">${texto}</span>`;
            
            if (data.success) {
                adicionarLog('✅ Push: Serviço configurado', 'success');
            } else {
                adicionarLog('⚠️ Push: Serviço não configurado', 'warning');
            }
        })
        .catch(error => {
            adicionarLog('❌ Erro ao testar push: ' + error.message, 'error');
        });
}

function atualizarMetricas() {
    fetch('/admin/notificacoes/api/diagnostico/metricas')
        .then(response => response.json())
        .then(data => {
            // Métricas do sistema
            document.getElementById('memoria-uso').textContent = data.memoria + '%';
            document.getElementById('memoria-barra').style.width = data.memoria + '%';
            
            document.getElementById('cpu-uso').textContent = data.cpu + '%';
            document.getElementById('cpu-barra').style.width = data.cpu + '%';
            
            document.getElementById('disco-uso').textContent = data.disco + '%';
            document.getElementById('disco-barra').style.width = data.disco + '%';
            
            document.getElementById('tempo-resposta').textContent = data.tempo_resposta;
            
            // Atualizar gráfico
            const agora = new Date().toLocaleTimeString();
            
            if (graficoPerformance.data.labels.length > 20) {
                graficoPerformance.data.labels.shift();
                graficoPerformance.data.datasets[0].data.shift();
                graficoPerformance.data.datasets[1].data.shift();
            }
            
            graficoPerformance.data.labels.push(agora);
            graficoPerformance.data.datasets[0].data.push(data.tempo_resposta);
            graficoPerformance.data.datasets[1].data.push(data.notificacoes_por_minuto);
            graficoPerformance.update();
        })
        .catch(error => {
            console.error('Erro ao atualizar métricas:', error);
        });
}

function analisarTabela(nomeTabela) {
    window.open(`/admin/notificacoes/diagnostico/tabela/${nomeTabela}`, '_blank');
}

function testarService(nomeService) {
    adicionarLog(`🧪 Testando service ${nomeService}...`, 'info');
    
    fetch(`/admin/notificacoes/api/diagnostico/teste-service/${nomeService}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                adicionarLog(`✅ Service ${nomeService}: Funcionando corretamente`, 'success');
            } else {
                adicionarLog(`❌ Service ${nomeService}: ${data.erro}`, 'error');
            }
        })
        .catch(error => {
            adicionarLog(`❌ Erro ao testar service ${nomeService}: ${error.message}`, 'error');
        });
}

function limparLogs() {
    logContainer.innerHTML = '<div class="text-center text-muted"><i class="mdi mdi-broom"></i> Logs limpos</div>';
}

function adicionarLog(mensagem, tipo = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const cores = {
        'info': 'text-info',
        'success': 'text-success',
        'warning': 'text-warning',
        'error': 'text-danger'
    };
    
    const cor = cores[tipo] || 'text-white';
    
    logContainer.innerHTML += `
        <div class="${cor}">
            <span class="text-muted">[${timestamp}]</span> ${mensagem}
        </div>
    `;
    
    // Scroll para o final
    logContainer.scrollTop = logContainer.scrollHeight;
}
</script>
@endpush
@endsection

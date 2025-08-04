<?php $__env->startSection('title', 'Estatísticas de Notificações'); ?>

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
                        <li class="breadcrumb-item active">Estatísticas</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-chart-line"></i> Estatísticas de Notificações
                </h4>
            </div>
        </div>
    </div>

    <!-- Filtros de Período -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Período</label>
                                    <select class="form-control" id="filtro-periodo">
                                        <option value="hoje">Hoje</option>
                                        <option value="ontem">Ontem</option>
                                        <option value="7dias" selected>Últimos 7 dias</option>
                                        <option value="30dias">Últimos 30 dias</option>
                                        <option value="personalizado">Período personalizado</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="data-inicio-grupo" style="display: none;">
                                    <label class="form-label">Data Início</label>
                                    <input type="date" class="form-control" id="data-inicio">
                                </div>
                                <div class="col-md-3" id="data-fim-grupo" style="display: none;">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="data-fim">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Canal</label>
                                    <select class="form-control" id="filtro-canal">
                                        <option value="">Todos os Canais</option>
                                        <option value="email">Email</option>
                                        <option value="sms">SMS</option>
                                        <option value="push">Push</option>
                                        <option value="in_app">In-App</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            <button class="btn btn-primary" onclick="atualizarEstatisticas()">
                                <i class="mdi mdi-refresh"></i> Atualizar
                            </button>
                            <button class="btn btn-outline-success" onclick="exportarRelatorio()">
                                <i class="mdi mdi-download"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principais -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-send float-end text-primary"></i>
                    <h6 class="text-uppercase mt-0">Total Enviadas</h6>
                    <h2 class="m-b-20" id="total-enviadas">1,234</h2>
                    <span class="badge bg-primary" id="badge-enviadas">+12%</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-check-circle float-end text-success"></i>
                    <h6 class="text-uppercase mt-0">Taxa de Sucesso</h6>
                    <h2 class="m-b-20" id="taxa-sucesso">97.5%</h2>
                    <span class="badge bg-success" id="badge-sucesso">excelente</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-clock-outline float-end text-warning"></i>
                    <h6 class="text-uppercase mt-0">Tempo Médio</h6>
                    <h2 class="m-b-20" id="tempo-medio">280ms</h2>
                    <span class="badge bg-warning" id="badge-tempo">de processamento</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-alert-circle float-end text-danger"></i>
                    <h6 class="text-uppercase mt-0">Taxa de Erro</h6>
                    <h2 class="m-b-20" id="taxa-erro">2.5%</h2>
                    <span class="badge bg-danger" id="badge-erro">31 falhas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-chart-areaspline"></i> Volume de Notificações
                    </h4>
                    <div class="chart-loading text-center" style="display: none; padding: 50px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando gráfico...</p>
                    </div>
                    <canvas id="grafico-volume" height="400" style="opacity: 0.3;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-pie-chart"></i> Distribuição por Canal
                    </h4>
                    <div class="chart-loading text-center" style="display: none; padding: 50px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando gráfico...</p>
                    </div>
                    <canvas id="grafico-canais" height="400" style="opacity: 0.3;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-chart-bar"></i> Top 10 Tipos de Evento
                    </h4>
                    <div class="chart-loading text-center" style="display: none; padding: 50px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando gráfico...</p>
                    </div>
                    <canvas id="grafico-tipos" height="400" style="opacity: 0.3;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-speedometer"></i> Performance por Hora
                    </h4>
                    <div class="chart-loading text-center" style="display: none; padding: 50px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando gráfico...</p>
                    </div>
                    <canvas id="grafico-horas" height="400" style="opacity: 0.3;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas de Detalhes -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-trophy"></i> Templates Mais Utilizados
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Template</th>
                                    <th>Canal</th>
                                    <th>Enviados</th>
                                    <th>Taxa Sucesso</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-templates-top">
                                <tr>
                                    <td>Pedido Criado</td>
                                    <td><span class="badge bg-primary">email</span></td>
                                    <td>500</td>
                                    <td>98%</td>
                                </tr>
                                <tr>
                                    <td>Pagamento Aprovado</td>
                                    <td><span class="badge bg-success">sms</span></td>
                                    <td>300</td>
                                    <td>96%</td>
                                </tr>
                                <tr>
                                    <td>Baixo Estoque</td>
                                    <td><span class="badge bg-warning">push</span></td>
                                    <td>200</td>
                                    <td>99%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-alert-circle"></i> Últimos Erros
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Horário</th>
                                    <th>Canal</th>
                                    <th>Erro</th>
                                    <th>Tentativas</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-erros">
                                <tr>
                                    <td>14:30</td>
                                    <td><span class="badge bg-primary">email</span></td>
                                    <td><small>SMTP timeout</small></td>
                                    <td>2/3</td>
                                </tr>
                                <tr>
                                    <td>14:25</td>
                                    <td><span class="badge bg-success">sms</span></td>
                                    <td><small>Invalid number</small></td>
                                    <td>1/3</td>
                                </tr>
                                <tr>
                                    <td>14:20</td>
                                    <td><span class="badge bg-warning">push</span></td>
                                    <td><small>Token expired</small></td>
                                    <td>3/3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Estilos para os gráficos */
.chart-container {
    position: relative;
    height: 400px;
    margin-bottom: 20px;
}

canvas {
    max-height: 400px !important;
    transition: opacity 0.5s ease-in-out;
}

.chart-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
}

/* Loading spinner personalizado */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Melhoria visual dos cards */
.tilebox-one {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}

.tilebox-one:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Badges coloridas */
.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Tabelas responsivas */
.table-responsive {
    border-radius: 0.375rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Filtros */
.form-control {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Botões */
.btn {
    border-radius: 0.35rem;
    font-weight: 500;
}

/* Headers dos cards */
.header-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 1rem;
}

.header-title i {
    margin-right: 0.5rem;
    color: #858796;
}

/* Breadcrumb */
.breadcrumb {
    background-color: transparent;
    padding: 0;
}

.breadcrumb-item a {
    color: #858796;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #5a5c69;
}

/* Page title */
.page-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 0;
}

.page-title i {
    color: #858796;
    margin-right: 0.5rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let graficos = {};
let inicializado = false;
let tentativasCarregamento = 0;

document.addEventListener('DOMContentLoaded', function() {
    if (inicializado) return;
    inicializado = true;
    
    console.log('Inicializando página de estatísticas...');
    
    function initCharts() {
        tentativasCarregamento++;
        
        if (typeof Chart === 'undefined') {
            if (tentativasCarregamento < 50) { // Máximo 5 segundos
                console.log(`Aguardando Chart.js... (tentativa ${tentativasCarregamento})`);
                setTimeout(initCharts, 100);
                return;
            } else {
                console.error('Chart.js não pôde ser carregado após 5 segundos');
                mostrarMensagemErro();
                return;
            }
        }
        
        console.log('Chart.js disponível, criando gráficos...');
        criarGraficos();
        configurarEventos();
    }
    
    initCharts();
});

function mostrarMensagemErro() {
    // Mostra mensagem de erro nos gráficos
    const graficosIds = ['grafico-volume', 'grafico-canais', 'grafico-tipos', 'grafico-horas'];
    graficosIds.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.outerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="mdi mdi-alert-triangle"></i>
                    Erro ao carregar gráfico. Tente recarregar a página.
                </div>
            `;
        }
    });
}

function criarGraficos() {
    try {
        console.log('Iniciando criação dos gráficos com dados reais...');
        
        // Configuração padrão para todos os gráficos
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
        
        // Busca dados reais da API
        buscarDadosReais();
        
    } catch (error) {
        console.error('Erro ao criar gráficos:', error);
        mostrarMensagemErro();
    }
}

function buscarDadosReais() {
    console.log('Buscando dados reais da API...');
    
    // Mostra indicadores de carregamento
    const loadings = document.querySelectorAll('.chart-loading');
    const canvases = document.querySelectorAll('canvas');
    
    loadings.forEach(loading => loading.style.display = 'block');
    canvases.forEach(canvas => canvas.style.opacity = '0.3');
    
    // Coleta os filtros
    const periodo = document.getElementById('filtro-periodo')?.value || '7dias';
    const canal = document.getElementById('filtro-canal')?.value || '';
    const dataInicio = document.getElementById('data-inicio')?.value || '';
    const dataFim = document.getElementById('data-fim')?.value || '';
    
    // Monta a URL com parâmetros
    const params = new URLSearchParams({
        periodo: periodo,
        canal: canal,
        data_inicio: dataInicio,
        data_fim: dataFim
    });
    
    fetch(`<?php echo e(route('admin.notificacoes.estatisticas.dados')); ?>?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Dados recebidos:', data);
                atualizarDashboard(data.dados);
            } else {
                throw new Error(data.message || 'Erro ao buscar dados');
            }
        })
        .catch(error => {
            console.error('Erro ao buscar dados:', error);
            mostrarMensagemErro();
            // Fallback para dados mockados em caso de erro
            criarGraficosMockados();
        })
        .finally(() => {
            // Esconde indicadores de carregamento
            setTimeout(() => {
                loadings.forEach(loading => loading.style.display = 'none');
                canvases.forEach(canvas => canvas.style.opacity = '1');
            }, 500);
        });
}

function atualizarDashboard(dados) {
    console.log('Atualizando dashboard com dados reais...');
    
    // Atualiza métricas principais
    if (dados.gerais) {
        document.getElementById('total-enviadas').textContent = dados.gerais.total_enviadas;
        document.getElementById('taxa-sucesso').textContent = dados.gerais.taxa_sucesso;
        document.getElementById('tempo-medio').textContent = dados.gerais.tempo_medio;
        document.getElementById('taxa-erro').textContent = dados.gerais.taxa_erro;
    }
    
    // Cria gráficos com dados reais
    criarGraficoVolume(dados.volume);
    criarGraficoCanais(dados.canais);
    criarGraficoTipos(dados.tipos);
    criarGraficoHoras(dados.horas);
    
    // Atualiza tabelas
    if (dados.templates) {
        atualizarTabelaTemplates(dados.templates);
    }
    
    if (dados.erros) {
        atualizarTabelaErros(dados.erros);
    }
}

function criarGraficoVolume(dadosVolume) {
    const ctx = document.getElementById('grafico-volume');
    if (!ctx || !dadosVolume) return;
    
    console.log('Criando gráfico de volume com dados reais...');
    
    if (graficos.volume) {
        graficos.volume.destroy();
    }
    
    graficos.volume = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dadosVolume.labels || [],
            datasets: dadosVolume.datasets || []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    beginAtZero: true
                }
            }
        }
    });
    
    console.log('Gráfico de volume criado com sucesso');
}

function criarGraficoCanais(dadosCanais) {
    const ctx = document.getElementById('grafico-canais');
    if (!ctx || !dadosCanais) return;
    
    console.log('Criando gráfico de canais com dados reais...');
    
    if (graficos.canais) {
        graficos.canais.destroy();
    }
    
    graficos.canais = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: dadosCanais.labels || [],
            datasets: dadosCanais.datasets || []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    
    console.log('Gráfico de canais criado com sucesso');
}

function criarGraficoTipos(dadosTipos) {
    const ctx = document.getElementById('grafico-tipos');
    if (!ctx || !dadosTipos) return;
    
    console.log('Criando gráfico de tipos com dados reais...');
    
    if (graficos.tipos) {
        graficos.tipos.destroy();
    }
    
    graficos.tipos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dadosTipos.labels || [],
            datasets: dadosTipos.datasets || []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    console.log('Gráfico de tipos criado com sucesso');
}

function criarGraficoHoras(dadosHoras) {
    const ctx = document.getElementById('grafico-horas');
    if (!ctx || !dadosHoras) return;
    
    console.log('Criando gráfico de horas com dados reais...');
    
    if (graficos.horas) {
        graficos.horas.destroy();
    }
    
    graficos.horas = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dadosHoras.labels || [],
            datasets: dadosHoras.datasets || []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            }
        }
    });
    
    console.log('Gráfico de horas criado com sucesso');
}

function atualizarTabelaTemplates(templates) {
    const tbody = document.getElementById('tabela-templates-top');
    if (!tbody || !templates) return;
    
    console.log('Atualizando tabela de templates...');
    
    tbody.innerHTML = '';
    
    templates.forEach(template => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${template.template}</td>
            <td><span class="badge bg-primary">${template.canal}</span></td>
            <td>${template.enviados}</td>
            <td>${template.taxa_sucesso}</td>
        `;
        tbody.appendChild(row);
    });
}

function atualizarTabelaErros(erros) {
    const tbody = document.getElementById('tabela-erros');
    if (!tbody || !erros) return;
    
    console.log('Atualizando tabela de erros...');
    
    tbody.innerHTML = '';
    
    erros.forEach(erro => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${erro.horario}</td>
            <td><span class="badge bg-primary">${erro.canal}</span></td>
            <td><small>${erro.erro}</small></td>
            <td>${erro.tentativas}</td>
        `;
        tbody.appendChild(row);
    });
}

function criarGraficosMockados() {
    console.log('Criando gráficos com dados mockados (fallback)...');
    
    // Gráfico de Volume (fallback)
    const ctxVolume = document.getElementById('grafico-volume');
    if (ctxVolume) {
        if (graficos.volume) graficos.volume.destroy();
        graficos.volume = new Chart(ctxVolume, {
            type: 'line',
            data: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Enviadas',
                    data: [120, 190, 300, 500, 200, 300, 450],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }, {
                    label: 'Falharam',
                    data: [12, 19, 3, 15, 8, 12, 18],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    
    // Gráfico de Canais (fallback)
    const ctxCanais = document.getElementById('grafico-canais');
    if (ctxCanais) {
        if (graficos.canais) graficos.canais.destroy();
        graficos.canais = new Chart(ctxCanais, {
            type: 'doughnut',
            data: {
                labels: ['Email', 'SMS', 'Push', 'In-App'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
    
    // Gráfico de Tipos (fallback)
    const ctxTipos = document.getElementById('grafico-tipos');
    if (ctxTipos) {
        if (graficos.tipos) graficos.tipos.destroy();
        graficos.tipos = new Chart(ctxTipos, {
            type: 'bar',
            data: {
                labels: ['Pedido Criado', 'Pagamento', 'Estoque Baixo', 'Cliente Inativo'],
                datasets: [{
                    label: 'Notificações',
                    data: [150, 120, 80, 50],
                    backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(255, 205, 86, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 192, 1)', 'rgba(255, 205, 86, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    
    // Gráfico de Horas (fallback)
    const ctxHoras = document.getElementById('grafico-horas');
    if (ctxHoras) {
        if (graficos.horas) graficos.horas.destroy();
        graficos.horas = new Chart(ctxHoras, {
            type: 'line',
            data: {
                labels: ['00h', '04h', '08h', '12h', '16h', '20h'],
                datasets: [{
                    label: 'Notificações por Hora',
                    data: [20, 10, 50, 120, 200, 80],
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(255, 159, 64, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
}

function configurarEventos() {
    const filtroPeriodo = document.getElementById('filtro-periodo');
    if (filtroPeriodo) {
        filtroPeriodo.addEventListener('change', function() {
            const periodo = this.value;
            const grupoInicio = document.getElementById('data-inicio-grupo');
            const grupoFim = document.getElementById('data-fim-grupo');
            
            if (periodo === 'personalizado') {
                grupoInicio.style.display = 'block';
                grupoFim.style.display = 'block';
            } else {
                grupoInicio.style.display = 'none';
                grupoFim.style.display = 'none';
            }
        });
    }
}

function atualizarEstatisticas() {
    console.log('Atualizando estatísticas...');
    
    // Destrói gráficos existentes
    Object.values(graficos).forEach(grafico => {
        if (grafico && typeof grafico.destroy === 'function') {
            grafico.destroy();
        }
    });
    
    // Limpa o objeto de gráficos
    graficos = {};
    
    // Busca novos dados
    buscarDadosReais();
}

function exportarRelatorio() {
    console.log('Exportando relatório...');
    
    // Simula download de relatório
    const link = document.createElement('a');
    link.href = '#'; // Substitua pela URL real do relatório
    link.download = `relatorio-notificacoes-${new Date().toISOString().split('T')[0]}.csv`;
    
    // Mostra mensagem
    alert('Funcionalidade em desenvolvimento. Em breve você poderá exportar relatórios completos!');
}

// Função para redimensionar gráficos quando a janela muda de tamanho
window.addEventListener('resize', function() {
    Object.values(graficos).forEach(grafico => {
        if (grafico && typeof grafico.resize === 'function') {
            grafico.resize();
        }
    });
});

// Função para limpar recursos quando a página for fechada
window.addEventListener('beforeunload', function() {
    Object.values(graficos).forEach(grafico => {
        if (grafico && typeof grafico.destroy === 'function') {
            grafico.destroy();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/notificacoes/estatisticas.blade.php ENDPATH**/ ?>
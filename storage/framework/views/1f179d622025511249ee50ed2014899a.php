<?php $__env->startSection('title', 'Relatórios de Pagamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-chart me-2"></i>
                    Relatórios de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.dashboard')); ?>">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Relatórios</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="exportCurrentReport()">
                        <i class="uil uil-export me-1"></i>
                        Exportar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="generateReport()">
                        <i class="uil uil-refresh me-1"></i>
                        Gerar Relatório
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="uil uil-filter me-2"></i>
                Filtros do Relatório
            </h5>
        </div>
        <div class="card-body">
            <form id="reportFilters">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Relatório</label>
                            <select class="form-select" name="report_type" id="reportType" onchange="updateReportOptions()">
                                <option value="transactions">Transações</option>
                                <option value="revenue">Receita</option>
                                <option value="gateways">Performance de Gateways</option>
                                <option value="methods">Métodos de Pagamento</option>
                                <option value="customers">Clientes</option>
                                <option value="refunds">Estornos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Período</label>
                            <select class="form-select" name="period" onchange="updateDateRange()">
                                <option value="today">Hoje</option>
                                <option value="yesterday">Ontem</option>
                                <option value="last_7_days" selected>Últimos 7 dias</option>
                                <option value="last_30_days">Últimos 30 dias</option>
                                <option value="this_month">Este mês</option>
                                <option value="last_month">Mês passado</option>
                                <option value="this_year">Este ano</option>
                                <option value="custom">Personalizado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Data Inicial</label>
                            <input type="date" class="form-control" name="start_date" value="<?php echo e(now()->subDays(7)->format('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Data Final</label>
                            <input type="date" class="form-control" name="end_date" value="<?php echo e(now()->format('Y-m-d')); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row" id="additionalFilters">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Gateway</label>
                            <select class="form-select" name="gateway">
                                <option value="">Todos</option>
                                <option value="mercadopago">Mercado Pago</option>
                                <option value="pagseguro">PagSeguro</option>
                                <option value="picpay">PicPay</option>
                                <option value="asaas">Asaas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="approved">Aprovado</option>
                                <option value="pending">Pendente</option>
                                <option value="rejected">Rejeitado</option>
                                <option value="refunded">Estornado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Método de Pagamento</label>
                            <select class="form-select" name="payment_method">
                                <option value="">Todos</option>
                                <option value="credit_card">Cartão de Crédito</option>
                                <option value="debit_card">Cartão de Débito</option>
                                <option value="pix">PIX</option>
                                <option value="bank_slip">Boleto</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Valor Mínimo</label>
                            <input type="number" class="form-control" name="min_amount" step="0.01" placeholder="0,00">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumo Executivo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Total Transações</h6>
                            <h3 class="mb-0" id="totalTransactions">1.234</h3>
                        </div>
                        <i class="uil uil-transaction text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Volume Total</h6>
                            <h3 class="mb-0" id="totalVolume">R$ 123.456</h3>
                        </div>
                        <i class="uil uil-money-bill text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Taxa de Sucesso</h6>
                            <h3 class="mb-0" id="successRate">85.6%</h3>
                        </div>
                        <i class="uil uil-chart-success text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Ticket Médio</h6>
                            <h3 class="mb-0" id="avgTicket">R$ 156</h3>
                        </div>
                        <i class="uil uil-calculator text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráficos -->
        <div class="col-lg-8">
            <!-- Gráfico Principal -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0" id="mainChartTitle">
                        <i class="uil uil-chart-line me-2"></i>
                        Evolução das Transações
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" onclick="changeChartType('line')">Linha</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="changeChartType('bar')">Barra</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="changeChartType('area')">Área</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="mainChart" height="120"></canvas>
                </div>
            </div>

            <!-- Gráfico de Comparação -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-pie me-2"></i>
                        Distribuição por Gateway
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="gatewayChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="methodChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Dados -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-table me-2"></i>
                        Dados Detalhados
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="exportTable('csv')">CSV</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="exportTable('excel')">Excel</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="exportTable('pdf')">PDF</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="reportTable">
                            <thead>
                                <tr id="tableHeaders">
                                    <th>Data</th>
                                    <th>Transações</th>
                                    <th>Volume</th>
                                    <th>Taxa Sucesso</th>
                                    <th>Ticket Médio</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Dados serão carregados via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar com Insights -->
        <div class="col-lg-4">
            <!-- Top Performers -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-trophy me-2"></i>
                        Top Performers
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Gateway com Maior Volume</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Mercado Pago</span>
                            <span class="badge bg-success">R$ 45.678</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Método Mais Usado</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>PIX</span>
                            <span class="badge bg-info">42.3%</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Melhor Taxa de Conversão</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Cartão de Crédito</span>
                            <span class="badge bg-warning">91.2%</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Hora de Pico</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>14:00 - 16:00</span>
                            <span class="badge bg-primary">28%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insights e Alertas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-lightbulb-alt me-2"></i>
                        Insights
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success alert-dismissible">
                        <i class="uil uil-check-circle me-2"></i>
                        <strong>Ótima performance!</strong> Taxa de sucesso 5% acima da média.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    
                    <div class="alert alert-warning alert-dismissible">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Aumento de 15% em transações rejeitadas hoje.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    
                    <div class="alert alert-info alert-dismissible">
                        <i class="uil uil-info-circle me-2"></i>
                        <strong>Tendência:</strong> PIX cresceu 23% comparado ao período anterior.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>

            <!-- Relatórios Agendados -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-schedule me-2"></i>
                        Relatórios Agendados
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Relatório Diário</strong>
                                <br><small class="text-muted">Todos os dias às 09:00</small>
                            </div>
                            <span class="badge bg-success">Ativo</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Relatório Semanal</strong>
                                <br><small class="text-muted">Segundas às 08:00</small>
                            </div>
                            <span class="badge bg-success">Ativo</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Relatório Mensal</strong>
                                <br><small class="text-muted">1° dia do mês às 10:00</small>
                            </div>
                            <span class="badge bg-secondary">Inativo</span>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="manageScheduledReports()">
                        <i class="uil uil-setting me-1"></i>
                        Gerenciar Agendamentos
                    </button>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-rocket me-2"></i>
                        Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="createCustomReport()">
                            <i class="uil uil-plus me-1"></i>
                            Relatório Personalizado
                        </button>
                        
                        <button type="button" class="btn btn-outline-info" onclick="compareReports()">
                            <i class="uil uil-comparison me-1"></i>
                            Comparar Períodos
                        </button>
                        
                        <button type="button" class="btn btn-outline-success" onclick="scheduleReport()">
                            <i class="uil uil-schedule me-1"></i>
                            Agendar Relatório
                        </button>
                        
                        <button type="button" class="btn btn-outline-warning" onclick="shareReport()">
                            <i class="uil uil-share me-1"></i>
                            Compartilhar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let mainChart, gatewayChart, methodChart;

// Dados de exemplo
const sampleData = {
    labels: ['01/12', '02/12', '03/12', '04/12', '05/12', '06/12', '07/12'],
    transactions: [45, 52, 48, 61, 55, 67, 73],
    volume: [4500, 5200, 4800, 6100, 5500, 6700, 7300],
    successRate: [85, 87, 84, 89, 86, 91, 88]
};

// Inicializar gráficos
document.addEventListener('DOMContentLoaded', function() {
    initMainChart();
    initGatewayChart();
    initMethodChart();
    loadTableData();
});

function initMainChart() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    mainChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: sampleData.labels,
            datasets: [{
                label: 'Transações',
                data: sampleData.transactions,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }, {
                label: 'Volume (R$ x100)',
                data: sampleData.volume.map(v => v/100),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initGatewayChart() {
    const ctx = document.getElementById('gatewayChart').getContext('2d');
    gatewayChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Mercado Pago', 'PagSeguro', 'PicPay', 'Asaas'],
            datasets: [{
                data: [45, 25, 20, 10],
                backgroundColor: ['#007bff', '#ffc107', '#28a745', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function initMethodChart() {
    const ctx = document.getElementById('methodChart').getContext('2d');
    methodChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['PIX', 'Cartão', 'Boleto', 'Débito'],
            datasets: [{
                data: [42, 35, 15, 8],
                backgroundColor: ['#28a745', '#007bff', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function loadTableData() {
    const tableBody = document.getElementById('tableBody');
    const sampleTableData = [
        ['07/12/2024', '73', 'R$ 7.300', '88%', 'R$ 100'],
        ['06/12/2024', '67', 'R$ 6.700', '91%', 'R$ 100'],
        ['05/12/2024', '55', 'R$ 5.500', '86%', 'R$ 100'],
        ['04/12/2024', '61', 'R$ 6.100', '89%', 'R$ 100'],
        ['03/12/2024', '48', 'R$ 4.800', '84%', 'R$ 100']
    ];
    
    tableBody.innerHTML = sampleTableData.map(row => 
        `<tr>${row.map(cell => `<td>${cell}</td>`).join('')}</tr>`
    ).join('');
}

function updateReportOptions() {
    const reportType = document.getElementById('reportType').value;
    const additionalFilters = document.getElementById('additionalFilters');
    
    // Atualizar filtros baseado no tipo de relatório
    // Implementar lógica específica para cada tipo
    
    // Atualizar título do gráfico
    const titles = {
        'transactions': 'Evolução das Transações',
        'revenue': 'Evolução da Receita',
        'gateways': 'Performance dos Gateways',
        'methods': 'Distribuição dos Métodos',
        'customers': 'Análise de Clientes',
        'refunds': 'Evolução dos Estornos'
    };
    
    document.getElementById('mainChartTitle').innerHTML = 
        `<i class="uil uil-chart-line me-2"></i>${titles[reportType]}`;
}

function updateDateRange() {
    const period = document.querySelector('[name="period"]').value;
    const startDate = document.querySelector('[name="start_date"]');
    const endDate = document.querySelector('[name="end_date"]');
    
    const today = new Date();
    const formatDate = (date) => date.toISOString().split('T')[0];
    
    switch(period) {
        case 'today':
            startDate.value = formatDate(today);
            endDate.value = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate.value = formatDate(yesterday);
            endDate.value = formatDate(yesterday);
            break;
        case 'last_7_days':
            const week = new Date(today);
            week.setDate(week.getDate() - 7);
            startDate.value = formatDate(week);
            endDate.value = formatDate(today);
            break;
        case 'last_30_days':
            const month = new Date(today);
            month.setDate(month.getDate() - 30);
            startDate.value = formatDate(month);
            endDate.value = formatDate(today);
            break;
    }
}

function changeChartType(type) {
    // Atualizar botões ativos
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Recriar gráfico com novo tipo
    mainChart.destroy();
    const ctx = document.getElementById('mainChart').getContext('2d');
    mainChart = new Chart(ctx, {
        type: type,
        data: mainChart.data,
        options: mainChart.options
    });
}

function generateReport() {
    alert('Gerando relatório...');
    // Implementar geração de relatório
}

function exportCurrentReport() {
    alert('Exportando relatório atual...');
    // Implementar exportação
}

function exportTable(format) {
    alert(`Exportando tabela em formato ${format.toUpperCase()}...`);
    // Implementar exportação da tabela
}

function createCustomReport() {
    alert('Criando relatório personalizado...');
    // Implementar criação de relatório personalizado
}

function compareReports() {
    alert('Comparando períodos...');
    // Implementar comparação
}

function scheduleReport() {
    alert('Agendando relatório...');
    // Implementar agendamento
}

function shareReport() {
    alert('Compartilhando relatório...');
    // Implementar compartilhamento
}

function manageScheduledReports() {
    alert('Gerenciando relatórios agendados...');
    // Implementar gerenciamento de agendamentos
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/payments/reports.blade.php ENDPATH**/ ?>
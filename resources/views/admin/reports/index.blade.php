@extends('admin.layouts.app')

@section('title', 'Relatórios')
@section('page-title', 'Centro de Relatórios')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Centro de Relatórios</h4>
            <p class="text-muted mb-0">Análises detalhadas e insights da plataforma</p>
        </div>
        <div>
            <button class="btn btn-outline-primary" onclick="scheduleReport()">
                <i class="fas fa-clock me-1"></i>
                Agendar Relatório
            </button>
        </div>
    </div>
</div>

<!-- Executive Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="mb-4">Resumo Executivo</h5>
            <div class="row">
                <div class="col-md-2 text-center mb-3">
                    <div class="h2 text-primary">{{ number_format($executiveSummary['total_merchants']) }}</div>
                    <div class="text-muted">Merchants Totais</div>
                </div>
                <div class="col-md-2 text-center mb-3">
                    <div class="h2 text-success">{{ number_format($executiveSummary['active_subscriptions']) }}</div>
                    <div class="text-muted">Assinaturas Ativas</div>
                </div>
                <div class="col-md-2 text-center mb-3">
                    <div class="h2 text-info">{{ number_format($executiveSummary['active_affiliates']) }}</div>
                    <div class="text-muted">Afiliados Ativos</div>
                </div>
                <div class="col-md-2 text-center mb-3">
                    <div class="h2 text-warning">R$ {{ number_format($executiveSummary['monthly_revenue'], 0, ',', '.') }}</div>
                    <div class="text-muted">Receita Mensal</div>
                </div>
                <div class="col-md-2 text-center mb-3">
                    <div class="h2 text-dark">R$ {{ number_format($executiveSummary['monthly_volume'], 0, ',', '.') }}</div>
                    <div class="text-muted">Volume (30d)</div>
                </div>
                <div class="col-md-2 text-center mb-3">
                    <div class="h2 text-secondary">{{ number_format($executiveSummary['monthly_transactions']) }}</div>
                    <div class="text-muted">Transações</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPIs Principais -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="chart-container">
            <h5 class="mb-3">KPIs Principais</h5>
            <div class="row">
                <div class="col-6 text-center mb-3">
                    <div class="h3 text-success">R$ {{ number_format($kpis['mrr'], 2, ',', '.') }}</div>
                    <div class="text-muted">MRR (Monthly Recurring Revenue)</div>
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i>
                        {{ number_format($kpis['mrr_growth'], 1) }}% vs mês anterior
                    </small>
                </div>
                <div class="col-6 text-center mb-3">
                    <div class="h3 text-primary">R$ {{ number_format($kpis['arpu'], 2, ',', '.') }}</div>
                    <div class="text-muted">ARPU (Average Revenue Per User)</div>
                </div>
                <div class="col-6 text-center mb-3">
                    <div class="h3 text-danger">{{ number_format($kpis['churn_rate'], 1) }}%</div>
                    <div class="text-muted">Taxa de Churn</div>
                </div>
                <div class="col-6 text-center mb-3">
                    <div class="h3 text-info">{{ number_format($kpis['new_subscriptions']) }}</div>
                    <div class="text-muted">Novas Assinaturas (30d)</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="chart-container">
            <h5 class="mb-3">Crescimento de Merchants</h5>
            <canvas id="merchantGrowthChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Available Reports -->
<div class="row">
    <div class="col-12">
        <div class="table-container">
            <h5 class="mb-4">Relatórios Disponíveis</h5>
            <div class="row">
                @foreach($availableReports as $report)
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-{{ $report['icon'] }} fa-3x text-primary"></i>
                            </div>
                            <h6 class="card-title">{{ $report['name'] }}</h6>
                            <p class="card-text small text-muted">{{ $report['description'] }}</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route($report['route']) }}" class="btn btn-primary">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Ver Relatório
                                </a>
                                <button class="btn btn-outline-secondary btn-sm" onclick="exportReport('{{ strtolower($report['name']) }}')">
                                    <i class="fas fa-download me-1"></i>
                                    Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Recent Exports -->
<div class="row mt-4">
    <div class="col-12">
        <div class="table-container">
            <h5 class="mb-3">Exportações Recentes</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Relatório</th>
                            <th>Período</th>
                            <th>Formato</th>
                            <th>Data/Hora</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-line text-primary me-2"></i>
                                    Relatório de Receita
                                </div>
                            </td>
                            <td>Últimos 30 dias</td>
                            <td><span class="badge bg-light text-dark">CSV</span></td>
                            <td>{{ now()->subHours(2)->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-success">Concluído</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="downloadFile('revenue_report.csv')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users text-success me-2"></i>
                                    Relatório de Merchants
                                </div>
                            </td>
                            <td>Últimos 3 meses</td>
                            <td><span class="badge bg-light text-dark">Excel</span></td>
                            <td>{{ now()->subDay()->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-success">Concluído</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="downloadFile('merchants_report.xlsx')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-share-alt text-info me-2"></i>
                                    Relatório de Afiliados
                                </div>
                            </td>
                            <td>Último mês</td>
                            <td><span class="badge bg-light text-dark">PDF</span></td>
                            <td>{{ now()->subMinutes(30)->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-warning">Processando</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="fas fa-clock"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Merchant Growth Chart
const merchantCtx = document.getElementById('merchantGrowthChart').getContext('2d');
const merchantGrowthChart = new Chart(merchantCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
        datasets: [{
            label: 'Novos Merchants',
            data: [12, 19, 15, 25, 22, 30],
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderColor: '#667eea',
            borderWidth: 1
        }]
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

function exportReport(reportType) {
    // Show export modal or redirect to export page
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

function scheduleReport() {
    // Show schedule modal
    alert('Funcionalidade de agendamento em desenvolvimento');
}

function downloadFile(filename) {
    // Simulate file download
    const link = document.createElement('a');
    link.href = `/admin/reports/download/${filename}`;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-refresh data every 5 minutes
setInterval(function() {
    // Update KPIs and charts without full page refresh
    updateKPIs();
}, 300000);

function updateKPIs() {
    axios.get('{{ route("admin.reports.index") }}?ajax=1')
        .then(response => {
            // Update KPI values
            // Implementation depends on API response structure
        })
        .catch(error => {
            console.error('Error updating KPIs:', error);
        });
}
</script>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportar Relatório</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label class="form-label">Período</label>
                        <select class="form-select" name="period">
                            <option value="7d">Últimos 7 dias</option>
                            <option value="30d">Últimos 30 dias</option>
                            <option value="3m">Últimos 3 meses</option>
                            <option value="6m">Últimos 6 meses</option>
                            <option value="12m">Últimos 12 meses</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Formato</label>
                        <select class="form-select" name="format">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email para envio (opcional)</label>
                        <input type="email" class="form-control" name="email" placeholder="seu@email.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="processExport()">Exportar</button>
            </div>
        </div>
    </div>
</div>

<script>
function processExport() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    
    // Show loading state
    const btn = event.target;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processando...';
    btn.disabled = true;
    
    // Process export (simulated)
    setTimeout(() => {
        alert('Relatório exportado com sucesso!');
        bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        btn.innerHTML = 'Exportar';
        btn.disabled = false;
    }, 2000);
}
</script>
@endpush

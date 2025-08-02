@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['total_merchants']) }}</h3>
                    <p class="text-muted mb-0">Total Merchants</p>
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i>
                        +{{ $stats['new_merchants_month'] }} este mês
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['active_subscriptions']) }}</h3>
                    <p class="text-muted mb-0">Assinaturas Ativas</p>
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i>
                        {{ number_format($stats['subscription_growth'], 1) }}% crescimento
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h3 class="mb-0">R$ {{ number_format($stats['monthly_revenue'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Receita Mensal</p>
                    <small class="text-info">
                        <i class="fas fa-chart-line"></i>
                        MRR: R$ {{ number_format($stats['mrr'], 2, ',', '.') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #17a2b8 0%, #6610f2 100%);">
                    <i class="fas fa-share-alt"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['active_affiliates']) }}</h3>
                    <p class="text-muted mb-0">Afiliados Ativos</p>
                    <small class="text-primary">
                        <i class="fas fa-percentage"></i>
                        {{ number_format($stats['conversion_rate'], 1) }}% conversão
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Revenue Chart -->
    <div class="col-md-8 mb-3">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Receita dos Últimos 12 Meses</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateRevenueChart('3m')">3M</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateRevenueChart('6m')">6M</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="updateRevenueChart('12m')">12M</button>
                </div>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>
    
    <!-- Plan Distribution -->
    <div class="col-md-4 mb-3">
        <div class="chart-container">
            <h5 class="mb-3">Distribuição por Plano</h5>
            <canvas id="planChart" height="150"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity and Top Performers -->
<div class="row">
    <!-- Recent Transactions -->
    <div class="col-md-6 mb-3">
        <div class="table-container">
            <h5 class="mb-3">Transações Recentes</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <span class="text-white small">{{ substr($transaction->merchant_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transaction->merchant_name }}</div>
                                        <small class="text-muted">{{ $transaction->merchant_email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                            </td>
                            <td>
                                @if($transaction->status === 'completed')
                                    <span class="badge bg-success">Concluído</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="badge bg-warning">Pendente</span>
                                @else
                                    <span class="badge bg-danger">Falhou</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Top Merchants -->
    <div class="col-md-6 mb-3">
        <div class="table-container">
            <h5 class="mb-3">Top Merchants (30 dias)</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Receita</th>
                            <th>Transações</th>
                            <th>Taxa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topMerchants as $merchant)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-success rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <span class="text-white small">{{ substr($merchant->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $merchant->name }}</div>
                                        <small class="text-muted">{{ $merchant->plan_name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success">R$ {{ number_format($merchant->total_revenue, 2, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ number_format($merchant->total_transactions) }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ number_format($merchant->success_rate, 1) }}%</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h5 class="mb-3">Ações Rápidas</h5>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.merchants.create') }}" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>
                        Novo Merchant
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-chart-line me-2"></i>
                        Relatório de Receita
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.affiliates.programs') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-share-alt me-2"></i>
                        Gerenciar Afiliados
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.payment.analytics') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-analytics me-2"></i>
                        Analytics Pagamentos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($revenueChart, 'month')) !!},
        datasets: [{
            label: 'Receita',
            data: {!! json_encode(array_column($revenueChart, 'revenue')) !!},
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
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
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        },
        elements: {
            point: {
                radius: 5,
                hoverRadius: 8
            }
        }
    }
});

// Plan Distribution Chart
const planCtx = document.getElementById('planChart').getContext('2d');
const planChart = new Chart(planCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_column($planDistribution, 'plan_name')) !!},
        datasets: [{
            data: {!! json_encode(array_column($planDistribution, 'count')) !!},
            backgroundColor: [
                '#667eea',
                '#764ba2',
                '#28a745',
                '#fd7e14',
                '#e83e8c',
                '#17a2b8'
            ],
            borderWidth: 0
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

// Update revenue chart function
function updateRevenueChart(period) {
    // Remove active class from all buttons
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    
    // Add active class to clicked button
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');
    
    // Fetch new data (implement AJAX call here)
    axios.get(`{{ route('admin.dashboard') }}?period=${period}`)
        .then(response => {
            // Update chart data
            revenueChart.data.labels = response.data.revenueChart.labels;
            revenueChart.data.datasets[0].data = response.data.revenueChart.data;
            revenueChart.update();
        })
        .catch(error => {
            console.error('Error updating chart:', error);
        });
}

// Auto refresh every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endpush

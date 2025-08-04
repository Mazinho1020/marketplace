@extends('layouts.admin')

@section('title', 'Detalhes do Gateway')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-credit-card me-2"></i>
                    {{ $gateway->name }}
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.dashboard') }}">Pagamentos</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.gateways') }}">Gateways</a></li>
                        <li class="breadcrumb-item active">{{ $gateway->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payments.gateways') }}" class="btn btn-outline-secondary">
                    <i class="uil uil-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações do Gateway -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-info-circle me-2"></i>
                        Informações do Gateway
                    </h5>
                    <div>
                        @if($gateway->is_active)
                            <span class="badge bg-success">
                                <i class="uil uil-check me-1"></i>Ativo
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="uil uil-times me-1"></i>Inativo
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td>{{ $gateway->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Código:</strong></td>
                                    <td><code>{{ $gateway->code }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($gateway->type) }}</span>
                                    </td>
                                </tr>
                                @if($gateway->description)
                                <tr>
                                    <td><strong>Descrição:</strong></td>
                                    <td>{{ $gateway->description }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Taxa Fixa:</strong></td>
                                    <td>
                                        @if($gateway->fee_fixed)
                                            <span class="text-danger">R$ {{ number_format($gateway->fee_fixed, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">R$ 0,00</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Taxa Percentual:</strong></td>
                                    <td>
                                        @if($gateway->fee_percentage)
                                            <span class="text-danger">{{ number_format($gateway->fee_percentage, 2, ',', '.') }}%</span>
                                        @else
                                            <span class="text-muted">0%</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Valor Mínimo:</strong></td>
                                    <td>
                                        @if($gateway->min_amount)
                                            R$ {{ number_format($gateway->min_amount, 2, ',', '.') }}
                                        @else
                                            <span class="text-muted">Sem mínimo</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Valor Máximo:</strong></td>
                                    <td>
                                        @if($gateway->max_amount)
                                            R$ {{ number_format($gateway->max_amount, 2, ',', '.') }}
                                        @else
                                            <span class="text-muted">Sem máximo</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas do Gateway -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50">Total Transações</h6>
                                    <h3 class="mb-0">{{ number_format($stats['total_transactions']) }}</h3>
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
                                    <h3 class="mb-0">R$ {{ number_format($stats['total_volume'], 0, ',', '.') }}</h3>
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
                                    <h3 class="mb-0">{{ number_format($stats['success_rate'], 1) }}%</h3>
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
                                    <h3 class="mb-0">R$ {{ number_format($stats['avg_transaction'], 0, ',', '.') }}</h3>
                                </div>
                                <i class="uil uil-calculator text-white-50" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Performance -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-line me-2"></i>
                        Performance dos Últimos 30 Dias
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="120"></canvas>
                </div>
            </div>

            <!-- Transações Recentes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-history me-2"></i>
                        Transações Recentes
                    </h5>
                    <a href="{{ route('admin.payments.transactions') }}?gateway={{ $gateway->id }}" class="btn btn-sm btn-outline-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Método</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <code>#{{ $transaction->external_id ?? $transaction->id }}</code>
                                    </td>
                                    <td>
                                        <span class="text-success">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @if($transaction->status === 'approved')
                                            <span class="badge bg-success">Aprovada</span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="badge bg-warning">Pendente</span>
                                        @elseif($transaction->status === 'rejected')
                                            <span class="badge bg-danger">Rejeitada</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->payment_method === 'credit_card')
                                            <i class="uil uil-credit-card me-1"></i>Cartão
                                        @elseif($transaction->payment_method === 'pix')
                                            <i class="uil uil-qrcode-scan me-1"></i>PIX
                                        @elseif($transaction->payment_method === 'bank_slip')
                                            <i class="uil uil-bill me-1"></i>Boleto
                                        @else
                                            {{ ucfirst($transaction->payment_method) }}
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.payments.transaction-details', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="uil uil-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="uil uil-history text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Nenhuma transação encontrada</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-setting me-2"></i>
                        Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($gateway->is_active)
                        <button type="button" class="btn btn-warning" onclick="toggleGateway(false)">
                            <i class="uil uil-pause me-1"></i>
                            Desativar Gateway
                        </button>
                        @else
                        <button type="button" class="btn btn-success" onclick="toggleGateway(true)">
                            <i class="uil uil-play me-1"></i>
                            Ativar Gateway
                        </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                            <i class="uil uil-link me-1"></i>
                            Testar Conexão
                        </button>
                        
                        <button type="button" class="btn btn-outline-info" onclick="exportData()">
                            <i class="uil uil-export me-1"></i>
                            Exportar Dados
                        </button>
                        
                        <a href="{{ route('admin.payments.transactions') }}?gateway={{ $gateway->id }}" class="btn btn-outline-secondary">
                            <i class="uil uil-transaction me-1"></i>
                            Ver Transações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Configurações -->
            @if($gateway->settings)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-cog me-2"></i>
                        Configurações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Ambiente:</small>
                        <div>
                            @if(isset($gateway->settings['environment']) && $gateway->settings['environment'] === 'production')
                                <span class="badge bg-success">Produção</span>
                            @else
                                <span class="badge bg-warning">Sandbox</span>
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($gateway->settings['webhook_url']))
                    <div class="mb-3">
                        <small class="text-muted">URL do Webhook:</small>
                        <div class="small"><code>{{ $gateway->settings['webhook_url'] }}</code></div>
                    </div>
                    @endif
                    
                    @if(isset($gateway->settings['timeout']))
                    <div class="mb-3">
                        <small class="text-muted">Timeout:</small>
                        <div>{{ $gateway->settings['timeout'] }}s</div>
                    </div>
                    @endif
                    
                    @if(isset($gateway->settings['retry_attempts']))
                    <div class="mb-3">
                        <small class="text-muted">Tentativas de Retry:</small>
                        <div>{{ $gateway->settings['retry_attempts'] }}x</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Últimos Webhooks -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-webhook me-2"></i>
                        Últimos Webhooks
                    </h5>
                    <a href="{{ route('admin.payments.webhooks') }}?gateway={{ $gateway->id }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @if($recentWebhooks->count())
                    @foreach($recentWebhooks as $webhook)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <small class="text-muted">{{ $webhook->event_type ?? 'N/A' }}</small>
                            <div class="small">{{ $webhook->created_at->format('d/m H:i') }}</div>
                        </div>
                        <div>
                            @if($webhook->status === 'processed')
                                <span class="badge bg-success">OK</span>
                            @elseif($webhook->status === 'failed')
                                <span class="badge bg-danger">Erro</span>
                            @else
                                <span class="badge bg-warning">Pendente</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-3">
                        <i class="uil uil-webhook text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">Nenhum webhook encontrado</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar o Chart.js carregar completamente
    function inicializar() {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js carregado com sucesso - Gateway Details');
            configurarGraficos();
        } else {
            console.log('Aguardando Chart.js carregar...');
            setTimeout(inicializar, 100);
        }
    }
    
    inicializar();
});

function configurarGraficos() {
    try {
        // Verificar se o Chart.js está carregado
        if (typeof Chart === 'undefined') {
            console.error('Chart.js não está carregado');
            return;
        }

        // Gráfico de Performance
        const performanceCanvas = document.getElementById('performanceChart');
        if (performanceCanvas) {
            const performanceCtx = performanceCanvas.getContext('2d');
            new Chart(performanceCtx, {
    type: 'line',
    data: {
        labels: @json($performanceData['labels']),
        datasets: [{
            label: 'Transações',
            data: @json($performanceData['transactions']),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Volume (R$)',
            data: @json($performanceData['volume']),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
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

function toggleGateway(activate) {
    const action = activate ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${action} este gateway?`)) {
        fetch(`/admin/payments/gateways/{{ $gateway->id }}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ active: activate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Gateway ${action}do com sucesso`);
                location.reload();
            } else {
                alert('Erro ao alterar status do gateway: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao comunicar com o servidor');
        });
    }
}

function testConnection() {
    alert('Testando conexão...');
    fetch(`/admin/payments/gateways/{{ $gateway->id }}/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Conexão testada com sucesso!');
        } else {
            alert('Erro na conexão: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao testar conexão');
    });
}

function exportData() {
    const data = @json($gateway);
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `gateway-${data.code}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection

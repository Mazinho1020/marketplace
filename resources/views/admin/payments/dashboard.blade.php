@extends('layouts.admin')

@section('title', 'Dashboard de Pagamentos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-credit-card me-2"></i>
                    Dashboard de Pagamentos
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pagamentos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payments.transactions') }}" class="btn btn-primary">
                    <i class="uil uil-transaction me-1"></i>
                    Ver Transações
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas Principais -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total_transacoes']) }}</h3>
                            <p class="mb-0">Total de Transações</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-transaction" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['transacoes_aprovadas']) }}</h3>
                            <p class="mb-0">Aprovadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <small>{{ $stats['total_transacoes'] > 0 ? round(($stats['transacoes_aprovadas'] / $stats['total_transacoes']) * 100, 1) : 0 }}% de aprovação</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['transacoes_pendentes']) }}</h3>
                            <p class="mb-0">Pendentes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-clock" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">R$ {{ number_format($stats['valor_total'], 2, ',', '.') }}</h3>
                            <p class="mb-0">Valor Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-money-bill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <small>R$ {{ number_format($stats['valor_mes_atual'], 2, ',', '.') }} este mês</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Relatórios -->
    <div class="row">
        <!-- Transações por Dia -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-line me-2"></i>
                        Transações dos Últimos 7 Dias
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartTransacoesDias" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Resumo Gateways -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-server-network me-2"></i>
                        Gateways Ativos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="text-primary">{{ $stats['gateways_ativos'] }}</h2>
                        <p class="text-muted">Gateways Configurados</p>
                    </div>
                    <div class="text-center mb-3">
                        <h4 class="text-success">{{ $stats['webhooks_recebidos'] }}</h4>
                        <p class="text-muted">Webhooks Hoje</p>
                    </div>
                    <a href="{{ route('admin.payments.gateways') }}" class="btn btn-outline-primary btn-sm w-100">
                        Ver Todos os Gateways
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Transações por Gateway e Métodos -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-server-network me-2"></i>
                        Transações por Gateway
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($transacoesPorGateway as $gateway)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1">{{ $gateway['gateway'] ?? 'Gateway Desconhecido' }}</h6>
                            <small class="text-muted">{{ $gateway['total'] ?? 0 }} transações</small>
                        </div>
                        <div class="text-end">
                            <strong>R$ {{ number_format($gateway['valor_total'] ?? 0, 2, ',', '.') }}</strong>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Nenhuma transação encontrada</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-credit-card me-2"></i>
                        Métodos Mais Usados
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($transacoesPorMetodo as $metodo)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1">{{ $metodo['metodo'] }}</h6>
                            <small class="text-muted">{{ $metodo['total'] }} transações</small>
                        </div>
                        <div class="text-end">
                            <strong>{{ $metodo['percentual'] }}%</strong>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Nenhuma transação encontrada</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Transações Recentes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-list-ul me-2"></i>
                        Transações Recentes
                    </h5>
                    <a href="{{ route('admin.payments.transactions') }}" class="btn btn-outline-primary btn-sm">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Valor</th>
                                    <th>Método</th>
                                    <th>Gateway</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transacoesRecentes as $transacao)
                                <tr>
                                    <td><code>#{{ $transacao->external_id ?? $transacao->id }}</code></td>
                                    <td><strong>R$ {{ number_format($transacao->amount, 2, ',', '.') }}</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($transacao->payment_method) }}</span>
                                    </td>
                                    <td>{{ $transacao->gateway->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($transacao->status === 'approved')
                                            <span class="badge bg-success">Aprovada</span>
                                        @elseif($transacao->status === 'pending')
                                            <span class="badge bg-warning">Pendente</span>
                                        @elseif($transacao->status === 'rejected')
                                            <span class="badge bg-danger">Rejeitada</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transacao->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $transacao->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.payments.transaction-details', $transacao->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                            <i class="uil uil-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="uil uil-credit-card" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Nenhuma transação encontrada</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu de Ações Rápidas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="uil uil-setting me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.payments.transactions') }}" class="btn btn-outline-primary w-100">
                            <i class="uil uil-transaction d-block mb-2" style="font-size: 2rem;"></i>
                            Transações
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.payments.gateways') }}" class="btn btn-outline-success w-100">
                            <i class="uil uil-server-network d-block mb-2" style="font-size: 2rem;"></i>
                            Gateways
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.payments.webhooks') }}" class="btn btn-outline-info w-100">
                            <i class="uil uil-webhook d-block mb-2" style="font-size: 2rem;"></i>
                            Webhooks
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.payments.settings') }}" class="btn btn-outline-warning w-100">
                            <i class="uil uil-cog d-block mb-2" style="font-size: 2rem;"></i>
                            Configurações
                        </a>
                    </div>
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
            console.log('Chart.js carregado com sucesso - Payments Dashboard');
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

        // Gráfico de transações por dia
        const chartCanvas = document.getElementById('chartTransacoesDias');
        if (chartCanvas) {
            const ctx = chartCanvas.getContext('2d');
            const chartData = @json($transacoesPorDia);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => {
                        const date = new Date(item.data);
                        return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                    }),
                    datasets: [{
                        label: 'Transações',
                        data: chartData.map(item => item.total),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Valor (R$)',
                        data: chartData.map(item => item.valor_aprovado),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Transações e Valores dos Últimos 7 Dias'
                        }
                    },
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
        } else {
            console.error('Canvas chartTransacoesDias não encontrado');
        }
    } catch (error) {
        console.error('Erro ao configurar gráficos:', error);
    }
}
</script>
@endsection

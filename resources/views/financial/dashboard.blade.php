@extends('financial.layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="financial-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="mb-3">
                        <i class="fas fa-chart-line me-3"></i>
                        Dashboard Financeiro
                    </h1>
                    <p class="mb-0 fs-5">Visão geral completa das suas finanças</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Contas a Receber</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($resumo['total_receber'], 2, ',', '.') }}
                            </div>
                            <small class="text-muted">{{ $resumo['qtd_receber'] }} conta(s)</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Contas a Pagar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($resumo['total_pagar'], 2, ',', '.') }}
                            </div>
                            <small class="text-muted">{{ $resumo['qtd_pagar'] }} conta(s)</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Líquido</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="{{ $resumo['saldo_liquido'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($resumo['saldo_liquido'], 2, ',', '.') }}
                                </span>
                            </div>
                            <small class="text-muted">Receber - Pagar</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Vencimentos Hoje</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $resumo['vencimentos_hoje'] }}
                            </div>
                            <small class="text-muted">contas vencendo</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card financial-card">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Fluxo de Caixa (Próximos 30 dias)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="fluxoCaixaChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card financial-card">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Receitas por Categoria
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="receitasCategoriaChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Contas Vencendo -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card financial-card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Contas a Receber Vencendo (7 dias)
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($contasReceberVencendo as $conta)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <h6 class="mb-1">{{ $conta->descricao }}</h6>
                                <small class="text-muted">
                                    {{ $conta->pessoa->nome ?? 'Cliente não informado' }} - 
                                    {{ $conta->data_vencimento->format('d/m/Y') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">R$ {{ number_format($conta->valor_final, 2, ',', '.') }}</div>
                                <small class="text-{{ $conta->isVencida() ? 'danger' : 'warning' }}">
                                    {{ $conta->isVencida() ? 'Vencida' : 'Vence em ' . $conta->diasParaVencimento() . ' dias' }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <h6>Nenhuma conta a receber vencendo</h6>
                            <p class="mb-0">Todas as contas estão em dia!</p>
                        </div>
                    @endforelse
                    
                    @if($contasReceberVencendo->count() > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('financial.contas-receber.index', ['situacao' => 'PENDENTE']) }}" class="btn btn-sm btn-outline-warning">
                                Ver Todas as Contas a Receber
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card financial-card">
                <div class="card-header bg-danger text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Contas a Pagar Vencendo (7 dias)
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($contasPagarVencendo as $conta)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <h6 class="mb-1">{{ $conta->descricao }}</h6>
                                <small class="text-muted">
                                    {{ $conta->pessoa->nome ?? 'Fornecedor não informado' }} - 
                                    {{ $conta->data_vencimento->format('d/m/Y') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-danger">R$ {{ number_format($conta->valor_final, 2, ',', '.') }}</div>
                                <small class="text-{{ $conta->isVencida() ? 'danger' : 'warning' }}">
                                    {{ $conta->isVencida() ? 'Vencida' : 'Vence em ' . $conta->diasParaVencimento() . ' dias' }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <h6>Nenhuma conta a pagar vencendo</h6>
                            <p class="mb-0">Todas as contas estão em dia!</p>
                        </div>
                    @endforelse
                    
                    @if($contasPagarVencendo->count() > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('financial.contas-pagar.index', ['situacao' => 'PENDENTE']) }}" class="btn btn-sm btn-outline-danger">
                                Ver Todas as Contas a Pagar
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card financial-card">
                <div class="card-header bg-dark text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('financial.contas-receber.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>
                                Nova Conta a Receber
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('financial.contas-pagar.create') }}" class="btn btn-danger w-100">
                                <i class="fas fa-plus me-2"></i>
                                Nova Conta a Pagar
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('financial.relatorios.fluxo-caixa') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-area me-2"></i>
                                Relatório de Fluxo
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('financial.relatorios.balancete') }}" class="btn btn-primary w-100">
                                <i class="fas fa-balance-scale me-2"></i>
                                Balancete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Dados para os gráficos
const fluxoCaixaData = @json($graficoFluxoCaixa);
const receitasCategoriaData = @json($graficoReceitas);

// Configuração do gráfico de fluxo de caixa
const ctxFluxo = document.getElementById('fluxoCaixaChart').getContext('2d');
const fluxoCaixaChart = new Chart(ctxFluxo, {
    type: 'line',
    data: {
        labels: fluxoCaixaData.labels,
        datasets: [{
            label: 'Receitas',
            data: fluxoCaixaData.receitas,
            borderColor: 'rgb(40, 167, 69)',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }, {
            label: 'Despesas',
            data: fluxoCaixaData.despesas,
            borderColor: 'rgb(220, 53, 69)',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4
        }]
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
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});

// Configuração do gráfico de receitas por categoria
const ctxReceitas = document.getElementById('receitasCategoriaChart').getContext('2d');
const receitasCategoriaChart = new Chart(ctxReceitas, {
    type: 'doughnut',
    data: {
        labels: receitasCategoriaData.labels,
        datasets: [{
            data: receitasCategoriaData.values,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#FF6384',
                '#C9CBCF'
            ]
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

// Atualizar dados a cada 5 minutos
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard - Transações de Cashback')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Dashboard de Transações
                    </h1>
                    <p class="text-muted mb-0">Acompanhe o desempenho das transações de cashback</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.transacoes.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>
                        Ver Todas
                    </a>
                    <a href="{{ route('fidelidade.transacoes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nova Transação
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <!-- Hoje -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Transações Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estatisticas['total_hoje'] }}
                            </div>
                            @if($estatisticas['total_ontem'] > 0)
                            @php
                            $variacao = (($estatisticas['total_hoje'] - $estatisticas['total_ontem']) /
                            $estatisticas['total_ontem']) * 100;
                            @endphp
                            <div class="text-xs {{ $variacao >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $variacao >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($variacao), 1) }}% vs ontem
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valor Hoje -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Valor Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($estatisticas['valor_hoje'], 2, ',', '.') }}
                            </div>
                            @if($estatisticas['valor_ontem'] > 0)
                            @php
                            $variacao = (($estatisticas['valor_hoje'] - $estatisticas['valor_ontem']) /
                            $estatisticas['valor_ontem']) * 100;
                            @endphp
                            <div class="text-xs {{ $variacao >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $variacao >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($variacao), 1) }}% vs ontem
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mês Atual -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Mês Atual
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estatisticas['mes_atual'] }}
                            </div>
                            @if($estatisticas['mes_passado'] > 0)
                            @php
                            $variacao = (($estatisticas['mes_atual'] - $estatisticas['mes_passado']) /
                            $estatisticas['mes_passado']) * 100;
                            @endphp
                            <div class="text-xs {{ $variacao >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $variacao >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($variacao), 1) }}% vs mês passado
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Média Diária -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Média Diária (Mês)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($estatisticas['mes_atual'] / now()->day, 1) }}
                            </div>
                            <div class="text-xs text-muted">
                                transações por dia
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico e Tabela -->
    <div class="row">
        <!-- Gráfico de Transações -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Transações dos Últimos 7 Dias</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="transacoesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo de Status -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status das Transações</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Processadas
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Pendentes
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Canceladas
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Transações -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimas Transações</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Adicionar dados das últimas transações aqui -->
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Implemente a consulta das últimas transações no controller
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuração do gráfico de transações
const ctx = document.getElementById('transacoesChart').getContext('2d');
const transacoesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['7 dias', '6 dias', '5 dias', '4 dias', '3 dias', '2 dias', 'Hoje'],
        datasets: [{
            label: 'Transações',
            data: [12, 19, 3, 5, 2, 3, {{ $estatisticas['total_hoje'] }}],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Evolução das Transações'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Configuração do gráfico de status
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Processadas', 'Pendentes', 'Canceladas'],
        datasets: [{
            data: [80, 15, 5], // Substitua por dados reais
            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            hoverBackgroundColor: ['#218838', '#e0a800', '#c82333'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush
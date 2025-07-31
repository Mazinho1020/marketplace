@extends('layouts.app')

@section('title', 'Relatórios - Fidelidade')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Relatórios do Sistema de Fidelidade
                    </h1>
                    <p class="text-muted mb-0">Acompanhe o desempenho do seu programa de fidelidade</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <select name="periodo" class="form-select">
                                <option value="30">Últimos 30 dias</option>
                                <option value="60">Últimos 60 dias</option>
                                <option value="90">Últimos 90 dias</option>
                                <option value="365">Último ano</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data Início</label>
                            <input type="date" name="data_inicio" class="form-control"
                                value="{{ request('data_inicio') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data Fim</label>
                            <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principais -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Total de Transações</h6>
                            <h4 class="mb-0">{{ number_format($estatisticas['total_transacoes'] ?? 0) }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Cashback Distribuído</h6>
                            <h4 class="mb-0">R$ {{ number_format($estatisticas['total_cashback'] ?? 0, 2, ',', '.') }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Clientes Ativos</h6>
                            <h4 class="mb-0">{{ number_format($estatisticas['clientes_ativos'] ?? 0) }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Cupons Utilizados</h6>
                            <h4 class="mb-0">{{ number_format($estatisticas['cupons_utilizados'] ?? 0) }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Relatórios Detalhados -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Clientes por Cashback</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Nível</th>
                                    <th>Cashback Total</th>
                                    <th>Transações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topClientes ?? [] as $cliente)
                                <tr>
                                    <td>Cliente #{{ $cliente->cliente_id }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ ucfirst($cliente->nivel_atual ?? 'bronze')
                                            }}</span>
                                    </td>
                                    <td>R$ {{ number_format($cliente->saldo_cashback ?? 0, 2, ',', '.') }}</td>
                                    <td>{{ $cliente->total_transacoes ?? 0 }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-muted">Nenhum dado disponível para o período selecionado</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Transações por Tipo</h6>
                </div>
                <div class="card-body">
                    <canvas id="tipoTransacoesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões de Exportação -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Exportar Relatórios</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('fidelidade.relatorios.exportar.transacoes') }}"
                                class="btn btn-outline-success btn-block">
                                <i class="fas fa-file-excel me-2"></i>
                                Exportar Transações
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('fidelidade.relatorios.exportar.clientes') }}"
                                class="btn btn-outline-info btn-block">
                                <i class="fas fa-file-csv me-2"></i>
                                Exportar Clientes
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('fidelidade.cupons.index') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-tags me-2"></i>
                                Relatório de Cupons
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('fidelidade.relatorios.performance') }}"
                                class="btn btn-outline-primary btn-block">
                                <i class="fas fa-chart-bar me-2"></i>
                                Performance Geral
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Transações por Tipo
    const ctx = document.getElementById('tipoTransacoesChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Crédito', 'Uso', 'Estorno'],
            datasets: [{
                data: [
                    {{ $estatisticas['transacoes_credito'] ?? 0 }},
                    {{ $estatisticas['transacoes_uso'] ?? 0 }},
                    {{ $estatisticas['transacoes_estorno'] ?? 0 }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#dc3545',
                    '#ffc107'
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
});
</script>
@endsection
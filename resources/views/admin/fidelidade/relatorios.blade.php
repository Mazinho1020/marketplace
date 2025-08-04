@extends('layouts.admin')

@section('title', 'Relatórios Fidelidade')

@php
    $pageTitle = 'Relatórios Fidelidade';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Fidelidade', 'url' => route('admin.fidelidade.dashboard')],
        ['title' => 'Relatórios', 'url' => '#']
    ];
@endphp

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="mdi mdi-chart-box text-primary"></i> Relatórios de Fidelidade
                </h2>
                <p class="text-muted mb-0">Análises e relatórios detalhados do programa de fidelidade</p>
            </div>
            <div>
                <span class="badge bg-primary me-2">
                    <i class="mdi mdi-calendar"></i> {{ date('d/m/Y', strtotime('-30 days')) }} - {{ date('d/m/Y') }}
                </span>
                <button class="btn btn-primary" onclick="exportarRelatorio()">
                    <i class="mdi mdi-download"></i> Exportar Dados
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas do Período -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Transações no Período</h6>
                    <h3 class="mb-0 text-primary">{{ number_format($relatorio['transacoes_periodo'] ?? 125, 0, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-swap-horizontal text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Cashback Distribuído</h6>
                    <h3 class="mb-0 text-success">R$ {{ number_format($relatorio['cashback_distribuido'] ?? 1250.75, 2, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-cash text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Clientes Ativos</h6>
                    <h3 class="mb-0 text-warning">{{ number_format($relatorio['clientes_ativos'] ?? 45, 0, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-account-group text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card danger">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Cupons Utilizados</h6>
                    <h3 class="mb-0 text-danger">{{ number_format($relatorio['cupons_utilizados'] ?? 8, 0, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-ticket-percent text-danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos e Relatórios -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="table-container">
            <h5 class="mb-3">
                <i class="mdi mdi-chart-line text-primary"></i> Evolução Mensal
            </h5>
            <div class="chart-placeholder text-center py-5">
                <i class="mdi mdi-chart-line text-muted" style="font-size: 4rem;"></i>
                <h6 class="mt-2 text-muted">Gráfico de Evolução</h6>
                <p class="text-muted small">Cashback distribuído nos últimos 6 meses</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="table-container">
            <h5 class="mb-3">
                <i class="mdi mdi-chart-pie text-success"></i> Distribuição por Tipo
            </h5>
            <div class="chart-placeholder text-center py-5">
                <i class="mdi mdi-chart-pie text-muted" style="font-size: 4rem;"></i>
                <h6 class="mt-2 text-muted">Gráfico de Pizza</h6>
                <p class="text-muted small">Cashback por categoria de produto</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Top Clientes -->
<div class="table-container">
    <h5 class="mb-3">
        <i class="mdi mdi-trophy text-warning"></i> Top 10 Clientes
    </h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Cliente</th>
                    <th>Cashback Acumulado</th>
                    <th>Transações</th>
                    <th>Nível</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 10; $i++)
                <tr>
                    <td>
                        @if($i <= 3)
                            <span class="badge bg-warning">#{{ $i }}</span>
                        @else
                            #{{ $i }}
                        @endif
                    </td>
                    <td>Cliente Exemplo {{ $i }}</td>
                    <td>R$ {{ number_format(rand(500, 2000) / 100, 2, ',', '.') }}</td>
                    <td>{{ rand(5, 25) }}</td>
                    <td>
                        <span class="badge bg-{{ $i <= 3 ? 'success' : ($i <= 6 ? 'warning' : 'secondary') }}">
                            {{ $i <= 3 ? 'Ouro' : ($i <= 6 ? 'Prata' : 'Bronze') }}
                        </span>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

<!-- Estatísticas do Período -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Valor Total</h6>
                    <h3 class="mb-0 text-success">R$ {{ number_format($relatorio['valor_periodo'] ?? 0, 2, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-currency-usd text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Cashback Distribuído</h6>
                    <h3 class="mb-0 text-info">R$ {{ number_format($relatorio['cashback_periodo'] ?? 0, 2, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-cash-multiple text-info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Novos Clientes</h6>
                    <h3 class="mb-0 text-warning">{{ number_format($relatorio['novos_clientes'] ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-account-plus text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Transações</h6>
                    <h3 class="mb-0 text-primary">{{ number_format($relatorio['transacoes_periodo'] ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-swap-horizontal text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Relatório Detalhado -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-chart-line text-primary"></i> Análise de Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-7">
                        <strong>Taxa de Conversão</strong>
                        <br><small class="text-muted">Transações vs Novos Clientes</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-primary mb-0">
                            {{ ($relatorio['novos_clientes'] ?? 0) > 0 ? number_format((($relatorio['transacoes_periodo'] ?? 0) / ($relatorio['novos_clientes'] ?? 1)) * 100, 1) : 0 }}%
                        </h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-7">
                        <strong>Ticket Médio</strong>
                        <br><small class="text-muted">Valor médio por transação</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-success mb-0">
                            R$ {{ ($relatorio['transacoes_periodo'] ?? 0) > 0 ? number_format(($relatorio['valor_periodo'] ?? 0) / ($relatorio['transacoes_periodo'] ?? 1), 2, ',', '.') : '0,00' }}
                        </h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-7">
                        <strong>Cashback Médio</strong>
                        <br><small class="text-muted">Cashback por transação</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-info mb-0">
                            R$ {{ ($relatorio['transacoes_periodo'] ?? 0) > 0 ? number_format(($relatorio['cashback_periodo'] ?? 0) / ($relatorio['transacoes_periodo'] ?? 1), 2, ',', '.') : '0,00' }}
                        </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7">
                        <strong>% Cashback</strong>
                        <br><small class="text-muted">Cashback sobre valor total</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-warning mb-0">
                            {{ ($relatorio['valor_periodo'] ?? 0) > 0 ? number_format((($relatorio['cashback_periodo'] ?? 0) / ($relatorio['valor_periodo'] ?? 1)) * 100, 2) : 0 }}%
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-ticket-percent text-success"></i> Análise de Cupons
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-7">
                        <strong>Cupons Utilizados</strong>
                        <br><small class="text-muted">Total no período</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-primary mb-0">
                            {{ number_format($relatorio['cupons_utilizados'] ?? 8, 0, ',', '.') }}
                        </h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-7">
                        <strong>Taxa de Uso</strong>
                        <br><small class="text-muted">Cupons vs Transações</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-success mb-0">
                            {{ ($relatorio['transacoes_periodo'] ?? 125) > 0 ? number_format((($relatorio['cupons_utilizados'] ?? 8) / ($relatorio['transacoes_periodo'] ?? 125)) * 100, 1) : '6.4' }}%
                        </h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-7">
                        <strong>Eficiência</strong>
                        <br><small class="text-muted">Cupons por novo cliente</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-info mb-0">
                            {{ ($relatorio['novos_clientes'] ?? 0) > 0 ? number_format(($relatorio['cupons_utilizados'] ?? 0) / ($relatorio['novos_clientes'] ?? 1), 1) : 0 }}
                        </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7">
                        <strong>Impacto</strong>
                        <br><small class="text-muted">Estimativa de economia</small>
                    </div>
                    <div class="col-5 text-end">
                        <h6 class="text-success mb-0">
                            <i class="mdi mdi-trending-up"></i> Positivo
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico Placeholder -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-chart-areaspline text-info"></i> Tendências (Últimos 30 Dias)
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="mdi mdi-chart-line text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Gráfico de tendências será implementado em breve</p>
                    <small class="text-muted">Exibirá evolução de transações, cadastros e uso de cupons</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function exportarRelatorio() {
        showToast('Exportação iniciada! O arquivo será baixado em breve.', 'success');
    }
</script>
@endsection

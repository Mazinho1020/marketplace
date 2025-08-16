@extends('comerciantes.layout')

@section('title', 'Dashboard de Vendas')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Dashboard de Vendas</h1>
                    <p class="text-muted">Acompanhe o desempenho das vendas da sua empresa</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.vendas.pdv.index', $empresa) }}" class="btn btn-primary">
                        <i class="fas fa-cash-register"></i> Novo PDV
                    </a>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.create', $empresa) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nova Venda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Vendas Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estatisticas['vendas_hoje_quantidade'] }}
                            </div>
                            <div class="text-xs text-muted">
                                R$ {{ number_format($estatisticas['vendas_hoje_valor'], 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Vendas Este Mês
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estatisticas['vendas_mes_quantidade'] }}
                            </div>
                            <div class="text-xs text-muted">
                                R$ {{ number_format($estatisticas['vendas_mes_valor'], 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Ticket Médio Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($estatisticas['ticket_medio_hoje'], 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-muted">
                                Média por venda
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Comparação com Ontem
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $percentual = $estatisticas['vendas_ontem_valor'] > 0 
                                        ? (($estatisticas['vendas_hoje_valor'] - $estatisticas['vendas_ontem_valor']) / $estatisticas['vendas_ontem_valor']) * 100
                                        : 0;
                                @endphp
                                @if($percentual > 0)
                                    <span class="text-success">+{{ number_format($percentual, 1) }}%</span>
                                @elseif($percentual < 0)
                                    <span class="text-danger">{{ number_format($percentual, 1) }}%</span>
                                @else
                                    <span class="text-muted">0%</span>
                                @endif
                            </div>
                            <div class="text-xs text-muted">
                                vs. ontem
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

    <!-- Gráficos e Tabelas -->
    <div class="row">
        <!-- Gráfico de Vendas -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Evolução das Vendas (Últimos 30 dias)</h6>
                    <div class="dropdown no-arrow">
                        <select class="form-control form-control-sm" id="periodoGrafico" style="width: auto;">
                            <option value="7_dias">Últimos 7 dias</option>
                            <option value="30_dias" selected>Últimos 30 dias</option>
                            <option value="3_meses">Últimos 3 meses</option>
                            <option value="12_meses">Últimos 12 meses</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="vendasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendas por Status -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vendas por Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($vendasPorStatus as $status => $total)
                        <span class="mr-2">
                            <i class="fas fa-circle text-{{ 
                                $status === 'confirmada' ? 'success' : 
                                ($status === 'pendente' ? 'warning' : 
                                ($status === 'cancelada' ? 'danger' : 'info')) 
                            }}"></i> {{ ucfirst($status) }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Produtos e Ações Rápidas -->
    <div class="row">
        <!-- Top Produtos Mais Vendidos -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produtos Mais Vendidos (Este Mês)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd. Vendida</th>
                                    <th>Total Faturado</th>
                                    <th>Posição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProdutos as $index => $produto)
                                <tr>
                                    <td>{{ $produto->nome }}</td>
                                    <td>{{ number_format($produto->total_vendido, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($produto->total_faturado, 2, ',', '.') }}</td>
                                    <td>
                                        @if($index === 0)
                                            <span class="badge badge-warning">🥇 1º</span>
                                        @elseif($index === 1)
                                            <span class="badge badge-secondary">🥈 2º</span>
                                        @elseif($index === 2)
                                            <span class="badge badge-dark">🥉 3º</span>
                                        @else
                                            <span class="badge badge-light">{{ $index + 1 }}º</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nenhuma venda encontrada este mês</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ações Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('comerciantes.empresas.vendas.pdv.index', $empresa) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-cash-register text-primary"></i>
                            <strong>PDV - Nova Venda</strong>
                            <br><small class="text-muted">Interface rápida para vendas</small>
                        </a>
                        <a href="{{ route('comerciantes.empresas.vendas.gerenciar.index', $empresa) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-list text-info"></i>
                            <strong>Gerenciar Vendas</strong>
                            <br><small class="text-muted">Ver todas as vendas</small>
                        </a>
                        <a href="{{ route('comerciantes.empresas.vendas.relatorios.vendas-periodo', $empresa) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-line text-success"></i>
                            <strong>Relatório de Vendas</strong>
                            <br><small class="text-muted">Análises detalhadas</small>
                        </a>
                        <a href="{{ route('comerciantes.empresas.vendas.relatorio.exportar', $empresa) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-download text-warning"></i>
                            <strong>Exportar Dados</strong>
                            <br><small class="text-muted">Excel, PDF e mais</small>
                        </a>
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
// Gráfico de Vendas
const ctxVendas = document.getElementById('vendasChart').getContext('2d');
const vendasChart = new Chart(ctxVendas, {
    type: 'line',
    data: {
        labels: @json($vendasUltimos30Dias->pluck('data')),
        datasets: [{
            label: 'Vendas (R$)',
            data: @json($vendasUltimos30Dias->pluck('valor')),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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

// Gráfico de Status
const ctxStatus = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: @json(array_keys($vendasPorStatus)),
        datasets: [{
            data: @json(array_values($vendasPorStatus)),
            backgroundColor: [
                '#28a745', // confirmada - verde
                '#ffc107', // pendente - amarelo  
                '#dc3545', // cancelada - vermelho
                '#17a2b8'  // entregue - azul
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Atualizar gráfico quando o período mudar
document.getElementById('periodoGrafico').addEventListener('change', function() {
    const periodo = this.value;
    
    fetch(`{{ route('comerciantes.empresas.vendas.api.dados-grafico', $empresa) }}?periodo=${periodo}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                vendasChart.data.labels = data.dados.map(d => d.periodo);
                vendasChart.data.datasets[0].data = data.dados.map(d => d.valor);
                vendasChart.update();
            }
        })
        .catch(error => console.error('Erro ao carregar dados do gráfico:', error));
});
</script>
@endpush

@push('styles')
<style>
.chart-area {
    height: 320px;
}
.chart-pie {
    height: 240px;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush
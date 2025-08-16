@extends('comerciantes.layout')

@section('title', 'Dashboard de Vendas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Dashboard de Vendas
                    </h1>
                    <p class="text-muted mb-0">Visão geral das vendas e métricas do negócio</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.vendas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Nova Venda
                    </a>
                    <a href="{{ route('comerciantes.vendas.index') }}" class="btn btn-outline-primary ms-2">
                        <i class="fas fa-list me-1"></i>
                        Todas as Vendas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Período de Análise -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('comerciantes.vendas.dashboard') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="periodo" class="form-label">Período</label>
                            <select class="form-select" id="periodo" name="periodo" onchange="toggleCustomDates()">
                                <option value="7d" {{ $periodo == '7d' ? 'selected' : '' }}>Últimos 7 dias</option>
                                <option value="30d" {{ $periodo == '30d' ? 'selected' : '' }}>Últimos 30 dias</option>
                                <option value="3m" {{ $periodo == '3m' ? 'selected' : '' }}>Últimos 3 meses</option>
                                <option value="6m" {{ $periodo == '6m' ? 'selected' : '' }}>Últimos 6 meses</option>
                                <option value="1y" {{ $periodo == '1y' ? 'selected' : '' }}>Último ano</option>
                                <option value="custom" {{ $periodo == 'custom' ? 'selected' : '' }}>Personalizado</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="data_inicio_group" style="display: {{ $periodo == 'custom' ? 'block' : 'none' }};">
                            <label for="data_inicio" class="form-label">Data Início</label>
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                                   value="{{ $dataInicio->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3" id="data_fim_group" style="display: {{ $periodo == 'custom' ? 'block' : 'none' }};">
                            <label for="data_fim" class="form-label">Data Fim</label>
                            <input type="date" class="form-control" id="data_fim" name="data_fim" 
                                   value="{{ $dataFim->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>
                                    Atualizar
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
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Total de Vendas</h6>
                            <h3 class="mb-0">{{ $metricas['total_vendas'] ?? 0 }}</h3>
                            @if(isset($metricasAnteriores['total_vendas']))
                                @php
                                    $variacao = $metricasAnteriores['total_vendas'] > 0 ? 
                                        (($metricas['total_vendas'] - $metricasAnteriores['total_vendas']) / $metricasAnteriores['total_vendas']) * 100 : 0;
                                @endphp
                                <small class="opacity-75">
                                    @if($variacao > 0)
                                        <i class="fas fa-arrow-up"></i> +{{ number_format($variacao, 1) }}%
                                    @elseif($variacao < 0)
                                        <i class="fas fa-arrow-down"></i> {{ number_format($variacao, 1) }}%
                                    @else
                                        <i class="fas fa-minus"></i> 0%
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Receita Total</h6>
                            <h3 class="mb-0">R$ {{ number_format($metricas['valor_total_vendas'] ?? 0, 2, ',', '.') }}</h3>
                            @if(isset($metricasAnteriores['valor_total_vendas']))
                                @php
                                    $variacao = $metricasAnteriores['valor_total_vendas'] > 0 ? 
                                        (($metricas['valor_total_vendas'] - $metricasAnteriores['valor_total_vendas']) / $metricasAnteriores['valor_total_vendas']) * 100 : 0;
                                @endphp
                                <small class="opacity-75">
                                    @if($variacao > 0)
                                        <i class="fas fa-arrow-up"></i> +{{ number_format($variacao, 1) }}%
                                    @elseif($variacao < 0)
                                        <i class="fas fa-arrow-down"></i> {{ number_format($variacao, 1) }}%
                                    @else
                                        <i class="fas fa-minus"></i> 0%
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Ticket Médio</h6>
                            <h3 class="mb-0">R$ {{ number_format($metricas['ticket_medio'] ?? 0, 2, ',', '.') }}</h3>
                            @if(isset($metricasAnteriores['ticket_medio']))
                                @php
                                    $variacao = $metricasAnteriores['ticket_medio'] > 0 ? 
                                        (($metricas['ticket_medio'] - $metricasAnteriores['ticket_medio']) / $metricasAnteriores['ticket_medio']) * 100 : 0;
                                @endphp
                                <small class="opacity-75">
                                    @if($variacao > 0)
                                        <i class="fas fa-arrow-up"></i> +{{ number_format($variacao, 1) }}%
                                    @elseif($variacao < 0)
                                        <i class="fas fa-arrow-down"></i> {{ number_format($variacao, 1) }}%
                                    @else
                                        <i class="fas fa-minus"></i> 0%
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Comissões</h6>
                            <h3 class="mb-0">R$ {{ number_format(collect($relatorioComissoes)->sum('comissao_marketplace'), 2, ',', '.') }}</h3>
                            <small class="opacity-75">Marketplace</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Vendas por Dia
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="vendasPorDiaChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Vendas por Hora
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="vendasPorHoraChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas de Detalhes -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-medal me-2"></i>
                        Top Produtos
                    </h5>
                </div>
                <div class="card-body">
                    @if($topProdutos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-end">Qtd</th>
                                        <th class="text-end">Receita</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProdutos as $produto)
                                        <tr>
                                            <td>{{ $produto->nome_produto }}</td>
                                            <td class="text-end">{{ number_format($produto->total_vendido, 0, ',', '.') }}</td>
                                            <td class="text-end">R$ {{ number_format($produto->receita_total, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Nenhum produto vendido no período</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>
                        Top Clientes
                    </h5>
                </div>
                <div class="card-body">
                    @if($topClientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th class="text-end">Compras</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topClientes as $cliente)
                                        <tr>
                                            <td>{{ $cliente->nome }}</td>
                                            <td class="text-end">{{ $cliente->total_compras }}</td>
                                            <td class="text-end">R$ {{ number_format($cliente->valor_total, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Nenhum cliente no período</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Vendas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Últimas Vendas
                    </h5>
                </div>
                <div class="card-body">
                    @if($ultimasVendas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Número</th>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Vendedor</th>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ultimasVendas as $venda)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">#{{ $venda->numero_venda }}</strong>
                                            </td>
                                            <td>{{ $venda->data_venda->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($venda->cliente)
                                                    {{ $venda->cliente->nome }}
                                                @else
                                                    <span class="text-muted">Cliente avulso</span>
                                                @endif
                                            </td>
                                            <td>{{ $venda->usuario->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($venda->tipo_venda) }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ $venda->valor_total_formatado }}</strong>
                                            </td>
                                            <td>{!! $venda->status_badge !!}</td>
                                            <td>
                                                <a href="{{ route('comerciantes.vendas.show', $venda->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('comerciantes.vendas.index') }}" class="btn btn-outline-primary">
                                Ver Todas as Vendas
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma venda realizada</h5>
                            <p class="text-muted">Não há vendas registradas ainda.</p>
                            <a href="{{ route('comerciantes.vendas.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Criar Primeira Venda
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleCustomDates() {
    const periodo = document.getElementById('periodo').value;
    const dataInicioGroup = document.getElementById('data_inicio_group');
    const dataFimGroup = document.getElementById('data_fim_group');
    
    if (periodo === 'custom') {
        dataInicioGroup.style.display = 'block';
        dataFimGroup.style.display = 'block';
    } else {
        dataInicioGroup.style.display = 'none';
        dataFimGroup.style.display = 'none';
    }
}

// Gráfico de Vendas por Dia
const vendasPorDiaData = @json($vendasPorDia);
const ctxVendasDia = document.getElementById('vendasPorDiaChart').getContext('2d');
new Chart(ctxVendasDia, {
    type: 'line',
    data: {
        labels: vendasPorDiaData.map(item => {
            const date = new Date(item.data);
            return date.toLocaleDateString('pt-BR');
        }),
        datasets: [{
            label: 'Número de Vendas',
            data: vendasPorDiaData.map(item => item.total_vendas),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1,
            yAxisID: 'y'
        }, {
            label: 'Valor (R$)',
            data: vendasPorDiaData.map(item => item.valor_total),
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Data'
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Número de Vendas'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Valor (R$)'
                },
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

// Gráfico de Vendas por Hora
const vendasPorHoraData = @json($vendasPorHora);
const ctxVendasHora = document.getElementById('vendasPorHoraChart').getContext('2d');
new Chart(ctxVendasHora, {
    type: 'bar',
    data: {
        labels: vendasPorHoraData.map(item => item.hora + 'h'),
        datasets: [{
            label: 'Vendas',
            data: vendasPorHoraData.map(item => item.total_vendas),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Número de Vendas'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Hora do Dia'
                }
            }
        }
    }
});

// Auto-submit quando período muda (exceto custom)
document.getElementById('periodo').addEventListener('change', function() {
    if (this.value !== 'custom') {
        this.form.submit();
    }
});
</script>
@endpush
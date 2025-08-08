@extends('comerciantes.layouts.app')

@section('title', 'Dashboard de Notificações')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-chart-bar me-2"></i>
                        Dashboard de Notificações
                    </h1>
                    <p class="text-muted mb-0">Análise e estatísticas das suas notificações</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.notificacoes.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list me-1"></i>
                        Ver Todas
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
                                Total de Notificações
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
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
                                Não Lidas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['nao_lidas'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                                Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['hoje'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                Taxa de Leitura
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['taxa_leitura'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Gráfico por Canal -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notificações por Canal</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="graficoPorCanal"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico por Dia -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notificações por Dia (Últimos 7 dias)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="graficoPorDia"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificações Recentes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Notificações Recentes</h6>
        </div>
        <div class="card-body">
            @if($notificacoesRecentes->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notificacoesRecentes->take(5) as $notificacao)
                        <div class="list-group-item list-group-item-action {{ is_null($notificacao->lido_em) ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <i class="fas fa-bell {{ is_null($notificacao->lido_em) ? 'text-warning' : 'text-muted' }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $notificacao->titulo }}</h6>
                                        <p class="mb-1">{{ Str::limit($notificacao->mensagem, 80) }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-tag"></i> {{ ucfirst($notificacao->canal) }}
                                        </small>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $notificacao->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('comerciantes.notificacoes.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list"></i> Ver Todas as Notificações
                    </a>
                </div>
            @else
                <div class="text-center py-3">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Nenhuma notificação recente encontrada.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico por Canal
    const ctxCanal = document.getElementById('graficoPorCanal');
    if (ctxCanal) {
        new Chart(ctxCanal, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($notificacoesPorCanal->toArray())) !!},
                datasets: [{
                    data: {!! json_encode(array_values($notificacoesPorCanal->toArray())) !!},
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9',
                        '#17a673',
                        '#2c9faf',
                        '#f4b619',
                        '#e02d1b'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Gráfico por Dia
    const ctxDia = document.getElementById('graficoPorDia');
    if (ctxDia) {
        new Chart(ctxDia, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($notificacoesPorDia->toArray())) !!},
                datasets: [{
                    label: 'Notificações',
                    data: {!! json_encode(array_values($notificacoesPorDia->toArray())) !!},
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
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
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

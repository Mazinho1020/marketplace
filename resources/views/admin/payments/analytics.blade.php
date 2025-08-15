@extends('layouts.admin')

@section('title', 'Analytics de Pagamentos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-analytics me-2"></i>
                    Analytics de Pagamentos
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.dashboard') }}">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <select class="form-select" id="periodSelector">
                        <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>Últimos 7 dias</option>
                        <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>Últimos 30 dias</option>
                        <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>Últimos 90 dias</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Volume -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-line me-2"></i>
                        Volume de Transações
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="volumeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparação de Gateways -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-comparison me-2"></i>
                        Comparação de Gateways
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Gateway</th>
                                    <th>Transações</th>
                                    <th>Volume</th>
                                    <th>Taxa Aprovação</th>
                                    <th>Tempo Médio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gatewayComparison as $gateway)
                                <tr>
                                    <td>
                                        <strong>{{ $gateway->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ number_format($gateway->transactions) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success">R$ {{ number_format($gateway->volume, 2, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $rate = $gateway->success_rate ?? 0;
                                            $badgeClass = $rate >= 80 ? 'bg-success' : ($rate >= 60 ? 'bg-warning' : 'bg-danger');
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ number_format($rate, 1) }}%</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ number_format($gateway->avg_processing_time ?? 0, 1) }}s</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        Análise de Falhas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted">Por Gateway</h6>
                        @foreach($failureAnalysis['by_gateway'] as $failure)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $failure->name }}</span>
                            <div>
                                <span class="badge bg-danger">{{ number_format($failure->failure_rate, 1) }}%</span>
                                <small class="text-muted">({{ $failure->failed_count }}/{{ $failure->total_count }})</small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div>
                        <h6 class="text-muted">Por Método de Pagamento</h6>
                        @foreach($failureAnalysis['by_method'] as $failure)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ ucfirst($failure->forma_pagamento) }}</span>
                            <div>
                                <span class="badge bg-danger">{{ number_format($failure->failure_rate, 1) }}%</span>
                                <small class="text-muted">({{ $failure->failed_count }}/{{ $failure->total_count }})</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Tracking -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-money-bill me-2"></i>
                        Receita ao Longo do Tempo
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-clock me-2"></i>
                        Tempos de Processamento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Gateway</th>
                                    <th>Método</th>
                                    <th>Tempo Médio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processingTimes as $time)
                                <tr>
                                    <td>
                                        <small>{{ Str::limit($time->gateway_name, 10) }}</small>
                                    </td>
                                    <td>
                                        <small>{{ ucfirst($time->forma_pagamento) }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $avgTime = $time->avg_time_seconds ?? 0;
                                            $badgeClass = $avgTime <= 30 ? 'bg-success' : ($avgTime <= 60 ? 'bg-warning' : 'bg-danger');
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ number_format($avgTime, 1) }}s</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Volume
    const volumeCtx = document.getElementById('volumeChart').getContext('2d');
    const volumeChart = new Chart(volumeCtx, {
        type: 'line',
        data: {
            labels: @json($volumeChart['dates'] ?? []),
            datasets: [{
                label: 'Total de Transações',
                data: @json($volumeChart['transactions'] ?? []),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }, {
                label: 'Transações Aprovadas',
                data: @json($volumeChart['successful'] ?? []),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Receita
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: @json(collect($revenueTracking)->pluck('date')->map(fn($date) => date('d/m', strtotime($date)))->toArray()),
            datasets: [{
                label: 'Receita (R$)',
                data: @json(collect($revenueTracking)->pluck('revenue')->toArray()),
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
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

    // Selector de período
    document.getElementById('periodSelector').addEventListener('change', function() {
        const period = this.value;
        window.location.href = `{{ route('admin.payments.analytics') }}?period=${period}`;
    });
});
</script>
@endpush
@endsection

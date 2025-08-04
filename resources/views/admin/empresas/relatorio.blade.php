@extends('layouts.admin')

@section('title', 'Relatórios de Empresas')

@php
    $pageTitle = 'Relatórios de Empresas';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Empresas', 'url' => route('admin.empresas.index')],
        ['title' => 'Relatórios', 'url' => '#']
    ];
@endphp

@section('content')
<div class="container-fluid">
    <!-- Filtros de Relatório -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros do Relatório
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.empresas.relatorio') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                                       value="{{ request('data_inicio') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" 
                                       value="{{ request('data_fim') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                    <option value="suspenso" {{ request('status') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                                    <option value="bloqueado" {{ request('status') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="plano" class="form-label">Plano</label>
                                <select class="form-select" id="plano" name="plano">
                                    <option value="">Todos</option>
                                    <option value="basico" {{ request('plano') == 'basico' ? 'selected' : '' }}>Básico</option>
                                    <option value="pro" {{ request('plano') == 'pro' ? 'selected' : '' }}>Pro</option>
                                    <option value="premium" {{ request('plano') == 'premium' ? 'selected' : '' }}>Premium</option>
                                    <option value="enterprise" {{ request('plano') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-2"></i>Gerar Relatório
                                </button>
                                <a href="{{ route('admin.empresas.relatorio') }}" class="btn btn-secondary">
                                    <i class="fas fa-eraser me-2"></i>Limpar Filtros
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Total de Empresas</h6>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Empresas Ativas</h6>
                            <h3 class="mb-0">{{ $stats['ativas'] ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Vencimento Próximo</h6>
                            <h3 class="mb-0">{{ $stats['vencendo'] ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Bloqueadas</h6>
                            <h3 class="mb-0">{{ $stats['bloqueadas'] ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-ban fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Distribuição por Status
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Distribuição por Plano
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="planoChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Relatório Detalhado -->
    @if(isset($empresas) && $empresas->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-table me-2"></i>Relatório Detalhado
                    </h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-success" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-1"></i>Excel
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf me-1"></i>PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="relatorioTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome Fantasia</th>
                                    <th>CNPJ</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th>Cidade/UF</th>
                                    <th>Plano</th>
                                    <th>Status</th>
                                    <th>Vencimento</th>
                                    <th>Cadastro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empresas as $empresa)
                                <tr>
                                    <td>{{ $empresa->nome_fantasia }}</td>
                                    <td>{{ $empresa->cnpj }}</td>
                                    <td>{{ $empresa->email }}</td>
                                    <td>{{ $empresa->telefone ?: $empresa->celular }}</td>
                                    <td>{{ $empresa->cidade }}/{{ $empresa->uf }}</td>
                                    <td>
                                        <span class="badge bg-{{ $empresa->getPlanoBadgeClass() }}">
                                            {{ ucfirst($empresa->subscription_plan ?: 'N/A') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $empresa->getStatusBadgeClass() }}">
                                            {{ ucfirst($empresa->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($empresa->subscription_ends_at)
                                            {{ $empresa->subscription_ends_at->format('d/m/Y') }}
                                            @if($empresa->isVencido())
                                                <small class="text-danger">(Vencido)</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $empresa->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($empresas->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $empresas->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>Nenhum resultado encontrado</h5>
                    <p class="text-muted">Ajuste os filtros para visualizar o relatório.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Status
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($chartData['status']['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($chartData['status']['data'] ?? []) !!},
                backgroundColor: [
                    '#28a745',
                    '#6c757d', 
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de Planos
    const planoCtx = document.getElementById('planoChart').getContext('2d');
    new Chart(planoCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['plano']['labels'] ?? []) !!},
            datasets: [{
                label: 'Quantidade',
                data: {!! json_encode($chartData['plano']['data'] ?? []) !!},
                backgroundColor: [
                    '#17a2b8',
                    '#007bff',
                    '#ffc107',
                    '#28a745'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

// Funções de Export
function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.empresas.relatorio") }}?' + params.toString();
}

function exportToPDF() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'pdf');
    window.location.href = '{{ route("admin.empresas.relatorio") }}?' + params.toString();
}
</script>
@endpush

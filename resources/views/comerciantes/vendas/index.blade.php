@extends('comerciantes.layout')

@section('title', 'Vendas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-shopping-cart text-primary me-2"></i>
                        Gestão de Vendas
                    </h1>
                    <p class="text-muted mb-0">Gerencie todas as vendas do seu negócio</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.vendas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Nova Venda
                    </a>
                    <a href="{{ route('comerciantes.vendas.dashboard') }}" class="btn btn-outline-primary ms-2">
                        <i class="fas fa-chart-line me-1"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('comerciantes.vendas.index') }}" id="filtrosForm">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                                       value="{{ $dataInicio }}">
                            </div>
                            <div class="col-md-2">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" 
                                       value="{{ $dataFim }}">
                            </div>
                            <div class="col-md-2">
                                <label for="vendedor_id" class="form-label">Vendedor</label>
                                <select class="form-select" id="vendedor_id" name="vendedor_id">
                                    <option value="">Todos os vendedores</option>
                                    {{-- Será populado via JavaScript --}}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select" id="cliente_id" name="cliente_id">
                                    <option value="">Todos os clientes</option>
                                    {{-- Será populado via JavaScript --}}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tipo_venda" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo_venda" name="tipo_venda">
                                    <option value="">Todos os tipos</option>
                                    <option value="balcao" {{ $tipoVenda == 'balcao' ? 'selected' : '' }}>Balcão</option>
                                    <option value="delivery" {{ $tipoVenda == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                    <option value="mesa" {{ $tipoVenda == 'mesa' ? 'selected' : '' }}>Mesa</option>
                                    <option value="online" {{ $tipoVenda == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="whatsapp" {{ $tipoVenda == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status_venda" class="form-label">Status</label>
                                <select class="form-select" id="status_venda" name="status_venda">
                                    <option value="">Todos os status</option>
                                    <option value="orcamento" {{ $statusVenda == 'orcamento' ? 'selected' : '' }}>Orçamento</option>
                                    <option value="pendente" {{ $statusVenda == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="confirmada" {{ $statusVenda == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                    <option value="paga" {{ $statusVenda == 'paga' ? 'selected' : '' }}>Paga</option>
                                    <option value="entregue" {{ $statusVenda == 'entregue' ? 'selected' : '' }}>Entregue</option>
                                    <option value="finalizada" {{ $statusVenda == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                    <option value="cancelada" {{ $statusVenda == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>
                                    Filtrar
                                </button>
                                <a href="{{ route('comerciantes.vendas.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i>
                                    Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Resumo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Total de Vendas</h6>
                            <h3 class="mb-0">{{ $metricas['total_vendas'] ?? 0 }}</h3>
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
                            <h6 class="card-title mb-0">Valor Total</h6>
                            <h3 class="mb-0">R$ {{ number_format($metricas['valor_total_vendas'] ?? 0, 2, ',', '.') }}</h3>
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
                            <h6 class="card-title mb-0">Produtos Únicos</h6>
                            <h3 class="mb-0">{{ $metricas['produtos_mais_vendidos']->count() ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cube fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Vendas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Lista de Vendas
                    </h5>
                </div>
                <div class="card-body">
                    @if($vendas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Número</th>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Vendedor</th>
                                        <th>Tipo</th>
                                        <th>Valor Total</th>
                                        <th>Status</th>
                                        <th>Pagamento</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendas as $venda)
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
                                                @switch($venda->status_pagamento)
                                                    @case('pago')
                                                        <span class="badge bg-success">Pago</span>
                                                        @break
                                                    @case('parcial')
                                                        <span class="badge bg-warning">Parcial</span>
                                                        @break
                                                    @case('estornado')
                                                        <span class="badge bg-danger">Estornado</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Pendente</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('comerciantes.vendas.show', $venda->id) }}" 
                                                       class="btn btn-outline-primary" title="Ver Detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(!$venda->isCancelada() && $venda->status_venda !== 'finalizada')
                                                        <a href="{{ route('comerciantes.vendas.edit', $venda->id) }}" 
                                                           class="btn btn-outline-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('comerciantes.vendas.imprimir', $venda->id) }}" 
                                                       class="btn btn-outline-secondary" title="Imprimir" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    @if($venda->status_venda === 'pendente')
                                                        <button type="button" class="btn btn-outline-success" 
                                                                onclick="confirmarVenda({{ $venda->id }})" title="Confirmar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    @if(!$venda->isCancelada())
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="cancelarVenda({{ $venda->id }})" title="Cancelar">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $vendas->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma venda encontrada</h5>
                            <p class="text-muted">Não há vendas para os filtros selecionados.</p>
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
<script>
function confirmarVenda(vendaId) {
    if (confirm('Tem certeza que deseja confirmar esta venda?')) {
        fetch(`/comerciantes/vendas/${vendaId}/confirmar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao confirmar venda');
        });
    }
}

function cancelarVenda(vendaId) {
    if (confirm('Tem certeza que deseja cancelar esta venda? Esta ação irá devolver o estoque dos produtos.')) {
        fetch(`/comerciantes/vendas/${vendaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao cancelar venda');
        });
    }
}

// Auto-submit do formulário de filtros quando campos mudam
document.querySelectorAll('#filtrosForm select, #filtrosForm input[type="date"]').forEach(element => {
    element.addEventListener('change', function() {
        document.getElementById('filtrosForm').submit();
    });
});
</script>
@endpush
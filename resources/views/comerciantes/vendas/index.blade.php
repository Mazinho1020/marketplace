@extends('comerciantes.layout')

@section('title', 'Gerenciar Vendas')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gerenciar Vendas</h1>
                    <p class="text-muted">Visualize e gerencie todas as vendas da empresa</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.vendas.dashboard', $empresa) }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.create', $empresa) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nova Venda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>{{ $estatisticas['total_vendas_hoje'] }}</h5>
                            <p class="mb-0">Vendas Hoje</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
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
                            <h5>R$ {{ number_format($estatisticas['valor_vendas_hoje'], 2, ',', '.') }}</h5>
                            <p class="mb-0">Faturamento Hoje</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h5>{{ $estatisticas['total_vendas_mes'] }}</h5>
                            <p class="mb-0">Vendas Este Mês</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
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
                            <h5>R$ {{ number_format($estatisticas['valor_vendas_mes'], 2, ',', '.') }}</h5>
                            <p class="mb-0">Faturamento Mês</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="confirmada" {{ request('status') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        <option value="entregue" {{ request('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ request('data_fim') }}">
                </div>
                <div class="col-md-3">
                    <label for="numero_venda" class="form-label">Número da Venda</label>
                    <input type="text" name="numero_venda" id="numero_venda" class="form-control" placeholder="Ex: 2025000001" value="{{ request('numero_venda') }}">
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.index', $empresa) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Vendas -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Lista de Vendas</h5>
        </div>
        <div class="card-body">
            @if($vendas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Valor Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendas as $venda)
                        <tr>
                            <td>
                                <strong>{{ $venda->numero_venda }}</strong>
                            </td>
                            <td>
                                {{ $venda->data_venda->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if($venda->cliente)
                                    {{ $venda->cliente->nome }}
                                @else
                                    <span class="text-muted">Cliente não informado</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $venda->tipo_venda_formatado }}</span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($venda->status) {
                                        'pendente' => 'warning',
                                        'confirmada' => 'success',
                                        'cancelada' => 'danger',
                                        'entregue' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ $venda->status_formatado }}</span>
                            </td>
                            <td>
                                <strong>{{ $venda->valor_liquido_formatado }}</strong>
                                @if($venda->valor_desconto > 0)
                                    <br><small class="text-muted">Desc: R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.show', [$empresa, $venda->id]) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($venda->status === 'pendente')
                                        <a href="{{ route('comerciantes.empresas.vendas.gerenciar.edit', [$empresa, $venda->id]) }}" 
                                           class="btn btn-sm btn-outline-success" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" action="{{ route('comerciantes.empresas.vendas.gerenciar.confirmar', [$empresa, $venda->id]) }}" 
                                              style="display: inline;" onsubmit="return confirm('Confirmar esta venda?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info" title="Confirmar Venda">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                title="Cancelar Venda" 
                                                onclick="cancelarVenda({{ $venda->id }})">
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $vendas->firstItem() }} a {{ $vendas->lastItem() }} de {{ $vendas->total() }} vendas
                    </small>
                </div>
                <div>
                    {{ $vendas->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5>Nenhuma venda encontrada</h5>
                <p class="text-muted">Não há vendas que correspondam aos filtros aplicados.</p>
                <a href="{{ route('comerciantes.empresas.vendas.gerenciar.create', $empresa) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Criar Nova Venda
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Cancelar Venda -->
<div class="modal fade" id="cancelarVendaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Venda</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cancelarVendaForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="motivo">Motivo do Cancelamento *</label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="3" required 
                                  placeholder="Informe o motivo do cancelamento da venda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Cancelar Venda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cancelarVenda(vendaId) {
    const form = document.getElementById('cancelarVendaForm');
    form.action = `{{ route('comerciantes.empresas.vendas.gerenciar.cancelar', [$empresa, ':id']) }}`.replace(':id', vendaId);
    
    $('#cancelarVendaModal').modal('show');
}

// Auto-aplicar filtro de datas para "hoje" se nenhuma data estiver selecionada
document.addEventListener('DOMContentLoaded', function() {
    const dataInicio = document.getElementById('data_inicio');
    const dataFim = document.getElementById('data_fim');
    
    if (!dataInicio.value && !dataFim.value && !new URLSearchParams(window.location.search).has('data_inicio')) {
        const hoje = new Date().toISOString().split('T')[0];
        dataInicio.value = hoje;
        dataFim.value = hoje;
    }
});
</script>
@endpush
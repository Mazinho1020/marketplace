@extends('comerciantes.layouts.app')

@section('title', 'Vendas')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Vendas</li>
                    </ol>
                </div>
                <h4 class="page-title">Gerenciar Vendas</h4>
            </div>
        </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h5 class="card-title">Lista de Vendas</h5>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a href="{{ route('comerciantes.vendas.create') }}" class="btn btn-success">
                                    <i class="mdi mdi-plus-circle me-1"></i> Nova Venda
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <form method="GET" action="{{ route('comerciantes.vendas.index') }}" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Todos os status</option>
                                    @foreach($statusOptions as $key => $label)
                                        <option value="{{ $key }}" {{ ($filtros['status'] ?? '') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tipo_venda" class="form-label">Tipo de Venda</label>
                                <select name="tipo_venda" id="tipo_venda" class="form-select">
                                    <option value="">Todos os tipos</option>
                                    @foreach($tiposVenda as $key => $label)
                                        <option value="{{ $key }}" {{ ($filtros['tipo_venda'] ?? '') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" 
                                       value="{{ $filtros['data_inicio'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control" 
                                       value="{{ $filtros['data_fim'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label for="numero_venda" class="form-label">Número da Venda</label>
                                <input type="text" name="numero_venda" id="numero_venda" class="form-control" 
                                       placeholder="Digite o número da venda" value="{{ $filtros['numero_venda'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-select">
                                    <option value="">Todos os clientes</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ ($filtros['cliente_id'] ?? '') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="mdi mdi-filter-variant me-1"></i> Filtrar
                                </button>
                                <a href="{{ route('comerciantes.vendas.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-refresh me-1"></i> Limpar Filtros
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Vendas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($vendas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Número</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th>Valor Total</th>
                                        <th>Itens</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendas as $venda)
                                        <tr>
                                            <td>
                                                <strong>#{{ $venda->numero_venda }}</strong>
                                                @if($venda->codigo_venda)
                                                    <br><small class="text-muted">{{ $venda->codigo_venda }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $venda->nome_cliente }}
                                                @if($venda->cliente && $venda->cliente->telefone)
                                                    <br><small class="text-muted">{{ $venda->cliente->telefone }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $venda->data_venda->format('d/m/Y') }}
                                                <br><small class="text-muted">{{ $venda->data_venda->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ \App\Models\Venda::TIPOS_VENDA[$venda->tipo_venda] ?? $venda->tipo_venda }}
                                                </span>
                                            </td>
                                            <td>{!! $venda->status_badge !!}</td>
                                            <td>
                                                <strong>{{ $venda->valor_total_formatado }}</strong>
                                                @if($venda->valor_desconto > 0)
                                                    <br><small class="text-success">Desc: R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $venda->total_itens }} itens</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('comerciantes.vendas.show', $venda) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    
                                                    @if($venda->status === 'aberta')
                                                        <a href="{{ route('comerciantes.vendas.edit', $venda) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                        
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="finalizarVenda({{ $venda->id }})" title="Finalizar">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                        
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="cancelarVenda({{ $venda->id }})" title="Cancelar">
                                                            <i class="mdi mdi-close"></i>
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
                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <div class="text-muted">
                                    Mostrando {{ $vendas->firstItem() }} até {{ $vendas->lastItem() }} 
                                    de {{ $vendas->total() }} vendas
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-sm-end">
                                    {{ $vendas->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-basket-outline text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-2">Nenhuma venda encontrada</h5>
                            <p class="text-muted">Tente ajustar os filtros ou criar uma nova venda.</p>
                            <a href="{{ route('comerciantes.vendas.create') }}" class="btn btn-success">
                                <i class="mdi mdi-plus-circle me-1"></i> Nova Venda
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cancelar venda -->
<div class="modal fade" id="modalCancelarVenda" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCancelarVenda">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo do cancelamento <span class="text-danger">*</span></label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="3" 
                                  placeholder="Informe o motivo do cancelamento" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger">Cancelar Venda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let vendaParaCancelar = null;

function finalizarVenda(vendaId) {
    if (confirm('Tem certeza que deseja finalizar esta venda?')) {
        fetch(`/comerciantes/vendas/${vendaId}/finalizar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao finalizar venda: ' + error.message);
        });
    }
}

function cancelarVenda(vendaId) {
    vendaParaCancelar = vendaId;
    document.getElementById('motivo').value = '';
    new bootstrap.Modal(document.getElementById('modalCancelarVenda')).show();
}

document.getElementById('formCancelarVenda').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const motivo = document.getElementById('motivo').value;
    if (!motivo.trim()) {
        alert('Por favor, informe o motivo do cancelamento.');
        return;
    }
    
    fetch(`/comerciantes/vendas/${vendaParaCancelar}/cancelar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ motivo: motivo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCancelarVenda')).hide();
            window.location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro ao cancelar venda: ' + error.message);
    });
});
</script>
@endpush
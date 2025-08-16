@extends('layouts.admin')

@section('title', 'Gestão de Vendas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Vendas</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="fa fa-shopping-cart me-1"></i>
                    Gestão de Vendas
                </h4>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('vendas.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Todos os status</option>
                                <option value="rascunho" {{ request('status') == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="confirmado" {{ request('status') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                <option value="processando" {{ request('status') == 'processando' ? 'selected' : '' }}>Processando</option>
                                <option value="separando" {{ request('status') == 'separando' ? 'selected' : '' }}>Separando</option>
                                <option value="enviado" {{ request('status') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                                <option value="entregue" {{ request('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                                <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="canal" class="form-label">Canal</label>
                            <select name="canal" id="canal" class="form-select">
                                <option value="">Todos os canais</option>
                                <option value="pdv" {{ request('canal') == 'pdv' ? 'selected' : '' }}>PDV</option>
                                <option value="online" {{ request('canal') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="delivery" {{ request('canal') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                <option value="telefone" {{ request('canal') == 'telefone' ? 'selected' : '' }}>Telefone</option>
                                <option value="whatsapp" {{ request('canal') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="data_inicio" class="form-label">Data Início</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="data_fim" class="form-label">Data Fim</label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ request('data_fim') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Número, cliente..." value="{{ request('search') }}">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search me-1"></i> Filtrar
                            </button>
                            <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
                                <i class="fa fa-refresh me-1"></i> Limpar
                            </a>
                            <a href="{{ route('vendas.create') }}" class="btn btn-success">
                                <i class="fa fa-plus me-1"></i> Nova Venda
                            </a>
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
                    <div class="table-responsive">
                        <table class="table table-centered table-striped">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Canal</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendas as $venda)
                                    <tr>
                                        <td>
                                            <strong>{{ $venda->numero_venda }}</strong>
                                        </td>
                                        <td>
                                            {{ $venda->cliente->nome ?? 'Cliente não informado' }}
                                        </td>
                                        <td>
                                            {{ $venda->data_emissao ? $venda->data_emissao->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $venda->canal_formatado }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $venda->valor_liquido_formatado }}</strong>
                                        </td>
                                        <td>
                                            @include('vendas.components.status-badge', ['status' => $venda->situacao_financeira])
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('vendas.show', $venda->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if(in_array($venda->situacao_financeira, ['rascunho', 'pendente']))
                                                    <a href="{{ route('vendas.edit', $venda->id) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if($venda->podeSerCancelada())
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Cancelar" onclick="cancelarVenda({{ $venda->id }})">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <p class="mb-0">Nenhuma venda encontrada.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    @if($vendas->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $vendas->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cancelamento -->
<div class="modal fade" id="cancelarVendaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cancelarVendaForm">
                    <input type="hidden" id="venda_id" name="venda_id">
                    
                    <div class="mb-3">
                        <label for="motivo_categoria" class="form-label">Motivo do Cancelamento</label>
                        <select name="motivo_categoria" id="motivo_categoria" class="form-select" required>
                            <option value="">Selecione o motivo</option>
                            <option value="cliente_desistiu">Cliente Desistiu</option>
                            <option value="produto_indisponivel">Produto Indisponível</option>
                            <option value="erro_preco">Erro de Preço</option>
                            <option value="problema_pagamento">Problema no Pagamento</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="motivo_detalhado" class="form-label">Detalhes do Motivo</label>
                        <textarea name="motivo_detalhado" id="motivo_detalhado" class="form-control" rows="3" required placeholder="Descreva os detalhes do cancelamento..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarCancelamento()">Confirmar Cancelamento</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cancelarVenda(vendaId) {
    document.getElementById('venda_id').value = vendaId;
    document.getElementById('cancelarVendaForm').reset();
    document.getElementById('venda_id').value = vendaId; // Reset limpa tudo, então definir novamente
    
    const modal = new bootstrap.Modal(document.getElementById('cancelarVendaModal'));
    modal.show();
}

function confirmarCancelamento() {
    const form = document.getElementById('cancelarVendaForm');
    const formData = new FormData(form);
    
    fetch(`/vendas/${formData.get('venda_id')}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('cancelarVendaModal')).hide();
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
</script>
@endpush
@extends('layouts.app')

@section('title', 'Transações de Cashback')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Transações de Cashback</h1>
                    <p class="text-muted">Gerencie todas as transações de cashback do sistema</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.transacoes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nova Transação
                    </a>
                    <a href="{{ route('fidelidade.transacoes.exportar') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-2"></i>Exportar
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('fidelidade.transacoes.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select" id="cliente_id" name="cliente_id">
                                    <option value="">Todos os clientes</option>
                                    @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ request('cliente_id')==$cliente->id ?
                                        'selected' : '' }}>
                                        {{ $cliente->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="empresa_id" class="form-label">Empresa</label>
                                <select class="form-select" id="empresa_id" name="empresa_id">
                                    <option value="">Todas as empresas</option>
                                    @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" {{ request('empresa_id')==$empresa->id ?
                                        'selected' : '' }}>
                                        {{ $empresa->nome_fantasia }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo">
                                    <option value="">Todos os tipos</option>
                                    <option value="credito" {{ request('tipo')=='credito' ? 'selected' : '' }}>Crédito
                                    </option>
                                    <option value="debito" {{ request('tipo')=='debito' ? 'selected' : '' }}>Débito
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                    value="{{ request('data_inicio') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim"
                                    value="{{ request('data_fim') }}">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Filtrar
                                </button>
                                <a href="{{ route('fidelidade.transacoes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-plus-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">Total Créditos</h5>
                            <h3 class="text-success">R$ {{ number_format($totalCreditos, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-danger">
                        <div class="card-body text-center">
                            <i class="fas fa-minus-circle fa-2x text-danger mb-2"></i>
                            <h5 class="card-title">Total Débitos</h5>
                            <h3 class="text-danger">R$ {{ number_format($totalDebitos, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-balance-scale fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">Saldo Líquido</h5>
                            <h3 class="text-primary">R$ {{ number_format($totalCreditos - $totalDebitos, 2, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-exchange-alt fa-2x text-info mb-2"></i>
                            <h5 class="card-title">Total Transações</h5>
                            <h3 class="text-info">{{ $transacoes->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Transações -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Lista de Transações
                    </h5>
                </div>
                <div class="card-body">
                    @if($transacoes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Empresa</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Descrição</th>
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transacoes as $transacao)
                                <tr>
                                    <td class="fw-bold">#{{ $transacao->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $transacao->cliente->nome ?? 'N/A' }}</div>
                                                <small class="text-muted">ID: {{ $transacao->cliente_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $transacao->empresa->nome_fantasia ?? 'N/A' }}</div>
                                        <small class="text-muted">ID: {{ $transacao->empresa_id }}</small>
                                    </td>
                                    <td>
                                        @if($transacao->tipo === 'credito')
                                        <span class="badge bg-success">
                                            <i class="fas fa-plus me-1"></i>Crédito
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-minus me-1"></i>Débito
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="fw-bold {{ $transacao->tipo === 'credito' ? 'text-success' : 'text-danger' }}">
                                            {{ $transacao->tipo === 'credito' ? '+' : '-' }}R$ {{
                                            number_format($transacao->valor, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                            title="{{ $transacao->descricao }}">
                                            {{ $transacao->descricao }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transacao->pedido_id)
                                        <span class="badge bg-light text-dark">{{ $transacao->pedido_id }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $transacao->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $transacao->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('fidelidade.transacoes.show', $transacao) }}"
                                                class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('fidelidade.transacoes.edit', $transacao) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmarExclusao({{ $transacao->id }})" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                            <p class="text-muted mb-0">
                                Mostrando {{ $transacoes->firstItem() }} até {{ $transacoes->lastItem() }}
                                de {{ $transacoes->total() }} resultados
                            </p>
                        </div>
                        <div>
                            {{ $transacoes->links() }}
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                        <h4>Nenhuma transação encontrada</h4>
                        <p class="text-muted">Não há transações cadastradas ou que atendam aos filtros selecionados.</p>
                        <a href="{{ route('fidelidade.transacoes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Criar primeira transação
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta transação?</p>
                <div class="alert alert-danger">
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita e o saldo da carteira será ajustado
                    automaticamente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sim, Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarExclusao(transacaoId) {
    const form = document.getElementById('formExclusao');
    form.action = `{{ route('fidelidade.transacoes.index') }}/${transacaoId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit do formulário de filtros quando algum campo mudar
    const filtros = document.querySelectorAll('#cliente_id, #empresa_id, #tipo');
    filtros.forEach(function(filtro) {
        filtro.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card.border-success .card-header {
        border-bottom-color: #198754;
    }

    .card.border-danger .card-header {
        border-bottom-color: #dc3545;
    }

    .card.border-primary .card-header {
        border-bottom-color: #0d6efd;
    }

    .card.border-info .card-header {
        border-bottom-color: #0dcaf0;
    }
</style>
@endpush
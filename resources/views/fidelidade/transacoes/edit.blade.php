@extends('layouts.app')

@section('title', 'Editar Transação de Cashback')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Editar Transação de Cashback</h1>
                    <p class="text-muted">Edite as informações da transação #{{ $transacao->id }}</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.transacoes.show', $transacao) }}"
                        class="btn btn-outline-primary me-2">
                        <i class="fas fa-eye me-2"></i>Visualizar
                    </a>
                    <a href="{{ route('fidelidade.transacoes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card {{ $transacao->tipo === 'credito' ? 'border-success' : 'border-danger' }}">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit me-2"></i>Editar Informações da Transação
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fidelidade.transacoes.update', $transacao) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cliente_id" class="form-label">Cliente</label>
                                            <select class="form-select @error('cliente_id') is-invalid @enderror"
                                                id="cliente_id" name="cliente_id" required>
                                                <option value="">Selecione um cliente</option>
                                                @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ (old('cliente_id') ?? $transacao->
                                                    cliente_id) == $cliente->id ? 'selected' : '' }}>
                                                    {{ $cliente->nome }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('cliente_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="empresa_id" class="form-label">Empresa</label>
                                            <select class="form-select @error('empresa_id') is-invalid @enderror"
                                                id="empresa_id" name="empresa_id" required>
                                                <option value="">Selecione uma empresa</option>
                                                @foreach($empresas as $empresa)
                                                <option value="{{ $empresa->id }}" {{ (old('empresa_id') ?? $transacao->
                                                    empresa_id) == $empresa->id ? 'selected' : '' }}>
                                                    {{ $empresa->nome_fantasia }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('empresa_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Tipo</label>
                                            <select class="form-select @error('tipo') is-invalid @enderror" id="tipo"
                                                name="tipo" required>
                                                <option value="">Selecione o tipo</option>
                                                <option value="credito" {{ (old('tipo') ?? $transacao->tipo) ==
                                                    'credito' ? 'selected' : '' }}>
                                                    Crédito
                                                </option>
                                                <option value="debito" {{ (old('tipo') ?? $transacao->tipo) == 'debito'
                                                    ? 'selected' : '' }}>
                                                    Débito
                                                </option>
                                            </select>
                                            @error('tipo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="valor" class="form-label">Valor</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" step="0.01" min="0.01"
                                                    class="form-control @error('valor') is-invalid @enderror" id="valor"
                                                    name="valor"
                                                    value="{{ old('valor') ?? number_format($transacao->valor, 2, '.', '') }}"
                                                    placeholder="0,00" required>
                                                @error('valor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                        id="descricao" name="descricao" rows="3"
                                        placeholder="Descreva o motivo da transação"
                                        required>{{ old('descricao') ?? $transacao->descricao }}</textarea>
                                    @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="pedido_id" class="form-label">ID do Pedido (opcional)</label>
                                    <input type="text" class="form-control @error('pedido_id') is-invalid @enderror"
                                        id="pedido_id" name="pedido_id"
                                        value="{{ old('pedido_id') ?? $transacao->pedido_id }}"
                                        placeholder="Ex: PED-12345">
                                    @error('pedido_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao()">
                                        <i class="fas fa-trash me-2"></i>Excluir
                                    </button>
                                    <div>
                                        <a href="{{ route('fidelidade.transacoes.show', $transacao) }}"
                                            class="btn btn-secondary me-2">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informações da Transação
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">ID da Transação</small>
                                <div class="fw-bold">#{{ $transacao->id }}</div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Criada em</small>
                                <div>{{ $transacao->created_at->format('d/m/Y H:i') }}</div>
                            </div>

                            @if($transacao->updated_at && $transacao->updated_at != $transacao->created_at)
                            <div class="mb-3">
                                <small class="text-muted">Última alteração</small>
                                <div>{{ $transacao->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @endif

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Atenção:</h6>
                                <ul class="mb-0 small">
                                    <li>Alterar esta transação afetará o saldo da carteira</li>
                                    <li>O valor anterior será revertido automaticamente</li>
                                    <li>O novo valor será aplicado ao saldo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
                <form action="{{ route('fidelidade.transacoes.destroy', $transacao) }}" method="POST" class="d-inline">
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
    function confirmarExclusao() {
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Formatação de moeda no campo valor
    const valorInput = document.getElementById('valor');
    valorInput.addEventListener('input', function(e) {
        let value = e.target.value;
        if (value) {
            value = value.replace(/[^\d.,]/g, '');
            value = value.replace(',', '.');
            e.target.value = value;
        }
    });

    // Mudança visual do tipo
    const tipoSelect = document.getElementById('tipo');
    tipoSelect.addEventListener('change', function() {
        const card = document.querySelector('.card');
        card.classList.remove('border-success', 'border-danger');
        
        if (this.value === 'credito') {
            card.classList.add('border-success');
        } else if (this.value === 'debito') {
            card.classList.add('border-danger');
        }
    });
});
</script>
@endpush
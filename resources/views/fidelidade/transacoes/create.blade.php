@extends('layouts.app')

@section('title', 'Criar Transação de Cashback')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Criar Transação de Cashback</h1>
                    <p class="text-muted">Adicione uma nova transação de cashback ao sistema</p>
                </div>
                <a href="{{ route('fidelidade.transacoes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-money-bill-wave me-2"></i>Informações da Transação
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fidelidade.transacoes.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cliente_id" class="form-label">Cliente</label>
                                            <select class="form-select @error('cliente_id') is-invalid @enderror"
                                                id="cliente_id" name="cliente_id" required>
                                                <option value="">Selecione um cliente</option>
                                                @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ old('cliente_id')==$cliente->id ?
                                                    'selected' : '' }}>
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
                                                <option value="{{ $empresa->id }}" {{ old('empresa_id')==$empresa->id ?
                                                    'selected' : '' }}>
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
                                                <option value="credito" {{ old('tipo')=='credito' ? 'selected' : '' }}>
                                                    <i class="fas fa-plus text-success"></i> Crédito
                                                </option>
                                                <option value="debito" {{ old('tipo')=='debito' ? 'selected' : '' }}>
                                                    <i class="fas fa-minus text-danger"></i> Débito
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
                                                    name="valor" value="{{ old('valor') }}" placeholder="0,00" required>
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
                                        required>{{ old('descricao') }}</textarea>
                                    @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="pedido_id" class="form-label">ID do Pedido (opcional)</label>
                                    <input type="text" class="form-control @error('pedido_id') is-invalid @enderror"
                                        id="pedido_id" name="pedido_id" value="{{ old('pedido_id') }}"
                                        placeholder="Ex: PED-12345">
                                    @error('pedido_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('fidelidade.transacoes.index') }}"
                                        class="btn btn-secondary me-2">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Criar Transação
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informações
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb me-2"></i>Dicas:</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Crédito:</strong> Adiciona valor ao saldo do cliente</li>
                                    <li><strong>Débito:</strong> Remove valor do saldo do cliente</li>
                                    <li>O saldo da carteira será atualizado automaticamente</li>
                                    <li>Use descrições claras para facilitar o histórico</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Formatação de moeda no campo valor
    const valorInput = document.getElementById('valor');
    valorInput.addEventListener('input', function(e) {
        let value = e.target.value;
        if (value) {
            // Remove caracteres não numéricos exceto ponto e vírgula
            value = value.replace(/[^\d.,]/g, '');
            // Substitui vírgula por ponto para cálculos
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
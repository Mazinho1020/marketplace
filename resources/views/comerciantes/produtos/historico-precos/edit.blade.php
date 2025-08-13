@extends('layouts.comerciante')

@section('title', 'Editar Histórico de Preço')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Editar Histórico de Preço</h1>
                    <p class="text-muted">Edite os dados da alteração de preço</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.produtos.historico-precos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Registro de Alteração
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.produtos.historico-precos.update', $historicoPreco->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="produto_id" class="form-label">Produto <span class="text-danger">*</span></label>
                                    <select name="produto_id" id="produto_id" class="form-select @error('produto_id') is-invalid @enderror" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            <option value="{{ $produto->id }}" 
                                                    data-preco-atual="{{ $produto->preco_venda }}"
                                                    data-preco-compra="{{ $produto->preco_compra }}"
                                                    {{ (old('produto_id', $historicoPreco->produto_id) == $produto->id) ? 'selected' : '' }}>
                                                {{ $produto->nome }} - R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('produto_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_alteracao" class="form-label">Data da Alteração <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           name="data_alteracao" 
                                           id="data_alteracao" 
                                           class="form-control @error('data_alteracao') is-invalid @enderror" 
                                           value="{{ old('data_alteracao', $historicoPreco->data_alteracao?->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('data_alteracao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco_venda_anterior" class="form-label">Preço de Venda Anterior <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" 
                                               name="preco_venda_anterior" 
                                               id="preco_venda_anterior" 
                                               class="form-control @error('preco_venda_anterior') is-invalid @enderror" 
                                               value="{{ old('preco_venda_anterior', $historicoPreco->preco_venda_anterior) }}" 
                                               step="0.01" 
                                               min="0"
                                               required>
                                    </div>
                                    @error('preco_venda_anterior')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco_venda_novo" class="form-label">Preço de Venda Novo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" 
                                               name="preco_venda_novo" 
                                               id="preco_venda_novo" 
                                               class="form-control @error('preco_venda_novo') is-invalid @enderror" 
                                               value="{{ old('preco_venda_novo', $historicoPreco->preco_venda_novo) }}" 
                                               step="0.01" 
                                               min="0"
                                               required>
                                    </div>
                                    @error('preco_venda_novo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco_compra_anterior" class="form-label">Preço de Compra Anterior</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" 
                                               name="preco_compra_anterior" 
                                               id="preco_compra_anterior" 
                                               class="form-control @error('preco_compra_anterior') is-invalid @enderror" 
                                               value="{{ old('preco_compra_anterior', $historicoPreco->preco_compra_anterior) }}" 
                                               step="0.01" 
                                               min="0">
                                    </div>
                                    @error('preco_compra_anterior')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco_compra_novo" class="form-label">Preço de Compra Novo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" 
                                               name="preco_compra_novo" 
                                               id="preco_compra_novo" 
                                               class="form-control @error('preco_compra_novo') is-invalid @enderror" 
                                               value="{{ old('preco_compra_novo', $historicoPreco->preco_compra_novo) }}" 
                                               step="0.01" 
                                               min="0">
                                    </div>
                                    @error('preco_compra_novo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="motivo" class="form-label">Motivo da Alteração</label>
                                    <input type="text" 
                                           name="motivo" 
                                           id="motivo" 
                                           class="form-control @error('motivo') is-invalid @enderror" 
                                           value="{{ old('motivo', $historicoPreco->motivo) }}" 
                                           placeholder="Ex: Ajuste de mercado, promoção, etc.">
                                    @error('motivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Painel de Cálculos -->
                        <div class="card bg-light mb-3" id="calculo-painel">
                            <div class="card-body">
                                <h6 class="card-title">Análise da Alteração</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="h5 mb-0" id="variacao-valor">
                                                @php
                                                    $variacao = $historicoPreco->preco_venda_novo - $historicoPreco->preco_venda_anterior;
                                                @endphp
                                                {{ $variacao >= 0 ? '+' : '' }}R$ {{ number_format(abs($variacao), 2, ',', '.') }}
                                            </div>
                                            <small class="text-muted">Variação Monetária</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="h5 mb-0" id="variacao-percentual">
                                                @php
                                                    $percentual = ($variacao / $historicoPreco->preco_venda_anterior) * 100;
                                                @endphp
                                                {{ $percentual >= 0 ? '+' : '' }}{{ number_format($percentual, 2, ',', '.') }}%
                                            </div>
                                            <small class="text-muted">Variação Percentual</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="h5 mb-0 {{ $variacao > 0 ? 'text-success' : ($variacao < 0 ? 'text-danger' : 'text-secondary') }}" id="tipo-alteracao">
                                                {{ $variacao > 0 ? 'Aumento' : ($variacao < 0 ? 'Redução' : 'Sem alteração') }}
                                            </div>
                                            <small class="text-muted">Tipo de Alteração</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="card bg-info bg-opacity-10 mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="fas fa-info-circle me-2"></i>Informações do Registro
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Criado em:</strong> {{ $historicoPreco->created_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Última atualização:</strong> {{ $historicoPreco->updated_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('comerciantes.produtos.historico-precos.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Atualizar Histórico
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const precoAnteriorInput = document.getElementById('preco_venda_anterior');
    const precoNovoInput = document.getElementById('preco_venda_novo');
    
    // Calcular variações em tempo real
    function calcularVariacoes() {
        const precoAnterior = parseFloat(precoAnteriorInput.value) || 0;
        const precoNovo = parseFloat(precoNovoInput.value) || 0;
        
        if (precoAnterior > 0 && precoNovo > 0) {
            const variacaoValor = precoNovo - precoAnterior;
            const variacaoPercentual = ((variacaoValor / precoAnterior) * 100);
            
            document.getElementById('variacao-valor').textContent = 
                (variacaoValor >= 0 ? '+' : '') + 'R$ ' + variacaoValor.toFixed(2).replace('.', ',');
            
            document.getElementById('variacao-percentual').textContent = 
                (variacaoPercentual >= 0 ? '+' : '') + variacaoPercentual.toFixed(2) + '%';
            
            let tipoAlteracao = 'Sem alteração';
            let corClasse = 'text-secondary';
            
            if (variacaoValor > 0) {
                tipoAlteracao = 'Aumento';
                corClasse = 'text-success';
            } else if (variacaoValor < 0) {
                tipoAlteracao = 'Redução';
                corClasse = 'text-danger';
            }
            
            const tipoElemento = document.getElementById('tipo-alteracao');
            tipoElemento.textContent = tipoAlteracao;
            tipoElemento.className = 'h5 mb-0 ' + corClasse;
        }
    }
    
    precoAnteriorInput.addEventListener('input', calcularVariacoes);
    precoNovoInput.addEventListener('input', calcularVariacoes);
});
</script>
@endpush

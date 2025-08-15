@extends('layouts.comerciante')

@section('title', 'Editar Conta a Pagar')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.dashboard.empresa', $empresa) }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}">Contas a Pagar</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}">
                    {{ $contaPagar->descricao }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Conta a Pagar</h1>
        <div class="btn-group">
            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" 
               class="btn btn-info">
                <i class="fas fa-eye"></i> Visualizar
            </a>
            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Formulário -->
    <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-pagar.update', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Dados Principais -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Dados Principais
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição *</label>
                                    <input type="text" name="descricao" id="descricao" 
                                           class="form-control @error('descricao') is-invalid @enderror" 
                                           value="{{ old('descricao', $contaPagar->descricao) }}" required>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="conta_gerencial_id" class="form-label">Conta Gerencial</label>
                                    <select name="conta_gerencial_id" id="conta_gerencial_id" 
                                            class="form-control @error('conta_gerencial_id') is-invalid @enderror">
                                        <option value="">Selecione uma conta gerencial</option>
                                        @foreach($contasGerenciais as $conta)
                                            <option value="{{ $conta->id }}" 
                                                    {{ old('conta_gerencial_id', $contaPagar->conta_gerencial_id) == $conta->id ? 'selected' : '' }}>
                                                {{ $conta->codigo }} - {{ $conta->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('conta_gerencial_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pessoa_id" class="form-label">Fornecedor/Pessoa</label>
                                    <select name="pessoa_id" id="pessoa_id" 
                                            class="form-control @error('pessoa_id') is-invalid @enderror">
                                        <option value="">Selecione uma pessoa</option>
                                        @foreach($pessoas as $pessoa)
                                            <option value="{{ $pessoa->id }}" 
                                                    {{ old('pessoa_id', $contaPagar->pessoa_id) == $pessoa->id ? 'selected' : '' }}>
                                                {{ $pessoa->nome }} ({{ $pessoa->tipo_pessoa }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pessoa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_documento" class="form-label">Número do Documento</label>
                                    <input type="text" name="numero_documento" id="numero_documento" 
                                           class="form-control @error('numero_documento') is-invalid @enderror" 
                                           value="{{ old('numero_documento', $contaPagar->numero_documento) }}" 
                                           placeholder="Ex: NF-001, Boleto 123">
                                    @error('numero_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea name="observacoes" id="observacoes" 
                                      class="form-control @error('observacoes') is-invalid @enderror" 
                                      rows="3">{{ old('observacoes', $contaPagar->observacoes) }}</textarea>
                            @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Valores e Datas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign"></i> Valores e Datas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_original" class="form-label">Valor Original *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="valor_original" id="valor_original" 
                                               class="form-control @error('valor_original') is-invalid @enderror" 
                                               step="0.01" value="{{ old('valor_original', $contaPagar->valor_original) }}" required>
                                        @error('valor_original')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="data_vencimento" class="form-label">Data Vencimento *</label>
                                    <input type="date" name="data_vencimento" id="data_vencimento" 
                                           class="form-control @error('data_vencimento') is-invalid @enderror" 
                                           value="{{ old('data_vencimento', $contaPagar->data_vencimento?->format('Y-m-d')) }}" required>
                                    @error('data_vencimento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="data_emissao" class="form-label">Data Emissão</label>
                                    <input type="date" name="data_emissao" id="data_emissao" 
                                           class="form-control @error('data_emissao') is-invalid @enderror" 
                                           value="{{ old('data_emissao', $contaPagar->data_emissao?->format('Y-m-d')) }}">
                                    @error('data_emissao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="desconto" class="form-label">Desconto</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="desconto" id="desconto" 
                                               class="form-control @error('desconto') is-invalid @enderror" 
                                               step="0.01" value="{{ old('desconto', $contaPagar->desconto) }}">
                                        @error('desconto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="juros" class="form-label">Juros</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="juros" id="juros" 
                                               class="form-control @error('juros') is-invalid @enderror" 
                                               step="0.01" value="{{ old('juros', $contaPagar->juros) }}">
                                        @error('juros')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="multa" class="form-label">Multa</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="multa" id="multa" 
                                               class="form-control @error('multa') is-invalid @enderror" 
                                               step="0.01" value="{{ old('multa', $contaPagar->multa) }}">
                                        @error('multa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parcelamento -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt"></i> Parcelamento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="numero_parcelas" class="form-label">Número de Parcelas</label>
                                    <input type="number" name="numero_parcelas" id="numero_parcelas" 
                                           class="form-control @error('numero_parcelas') is-invalid @enderror" 
                                           min="1" value="{{ old('numero_parcelas', $contaPagar->numero_parcelas) }}">
                                    @error('numero_parcelas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="parcela_atual" class="form-label">Parcela Atual</label>
                                    <input type="number" name="parcela_atual" id="parcela_atual" 
                                           class="form-control @error('parcela_atual') is-invalid @enderror" 
                                           min="1" value="{{ old('parcela_atual', $contaPagar->parcela_atual) }}">
                                    @error('parcela_atual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="documento_numero" class="form-label">Número do Documento</label>
                                    <input type="text" name="documento_numero" id="documento_numero" 
                                           class="form-control @error('documento_numero') is-invalid @enderror" 
                                           value="{{ old('documento_numero', $contaPagar->documento_numero) }}">
                                    @error('documento_numero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Resumo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calculator"></i> Resumo
                        </h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-6">Valor Original:</dt>
                            <dd class="col-6" id="resumo-original">R$ {{ number_format($contaPagar->valor_original, 2, ',', '.') }}</dd>
                            
                            <dt class="col-6">Desconto:</dt>
                            <dd class="col-6 text-success" id="resumo-desconto">- R$ {{ number_format($contaPagar->desconto, 2, ',', '.') }}</dd>
                            
                            <dt class="col-6">Juros:</dt>
                            <dd class="col-6 text-warning" id="resumo-juros">+ R$ {{ number_format($contaPagar->juros, 2, ',', '.') }}</dd>
                            
                            <dt class="col-6">Multa:</dt>
                            <dd class="col-6 text-danger" id="resumo-multa">+ R$ {{ number_format($contaPagar->multa, 2, ',', '.') }}</dd>
                            
                            <hr>
                            
                            <dt class="col-6"><strong>Total:</strong></dt>
                            <dd class="col-6"><strong id="resumo-total">R$ {{ number_format($contaPagar->valor_total, 2, ',', '.') }}</strong></dd>
                        </dl>
                    </div>
                </div>

                <!-- Configurações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Configurações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="situacao" class="form-label">Situação</label>
                            <select name="situacao_financeira" id="situacao_financeira" 
                                    class="form-control @error('situacao') is-invalid @enderror">
                                <option value="pendente" {{ old('situacao_financeira', $contaPagar->situacao_financeira->value) == 'pendente' ? 'selected' : '' }}>
                                    Pendente
                                </option>
                                <option value="pago" {{ old('situacao_financeira', $contaPagar->situacao_financeira->value) == 'pago' ? 'selected' : '' }}>
                                    Pago
                                </option>
                                <option value="cancelado" {{ old('situacao_financeira', $contaPagar->situacao_financeira->value) == 'cancelado' ? 'selected' : '' }}>
                                    Cancelado
                                </option>
                            </select>
                            @error('situacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="natureza" class="form-label">Natureza</label>
                            <select name="natureza_financeira" id="natureza_financeira" 
                                    class="form-control @error('natureza') is-invalid @enderror">
                                <option value="despesa" {{ old('natureza_financeira', $contaPagar->natureza_financeira->value) == 'despesa' ? 'selected' : '' }}>
                                    Despesa
                                </option>
                                <option value="custo" {{ old('natureza_financeira', $contaPagar->natureza_financeira->value) == 'custo' ? 'selected' : '' }}>
                                    Custo
                                </option>
                                <option value="investimento" {{ old('natureza_financeira', $contaPagar->natureza_financeira->value) == 'investimento' ? 'selected' : '' }}>
                                    Investimento
                                </option>
                            </select>
                            @error('natureza')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="e_recorrente" id="e_recorrente" 
                                   class="form-check-input @error('e_recorrente') is-invalid @enderror" 
                                   value="1" {{ old('e_recorrente', $contaPagar->e_recorrente) ? 'checked' : '' }}>
                            <label for="e_recorrente" class="form-check-label">
                                É Recorrente
                            </label>
                            @error('e_recorrente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Atual -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info"></i> Status Atual
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Situação:</strong> 
                            <span class="badge badge-{{ $contaPagar->situacao_financeira->value == 'pago' ? 'success' : 'warning' }}">
                                {{ $contaPagar->situacao_financeira->label() }}
                            </span>
                        </p>
                        
                        @if($contaPagar->valor_pago > 0)
                        <p><strong>Valor Pago:</strong> R$ {{ number_format($contaPagar->valor_pago, 2, ',', '.') }}</p>
                        <p><strong>Saldo:</strong> R$ {{ number_format($contaPagar->valor_total - $contaPagar->valor_pago, 2, ',', '.') }}</p>
                        @endif

                        <small class="text-muted">
                            Criado em: {{ $contaPagar->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>

                <!-- Botões -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" 
                               class="btn btn-info">
                                <i class="fas fa-eye"></i> Visualizar
                            </a>
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function formatarMoeda(valor) {
    return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
}

function atualizarResumo() {
    const original = parseFloat(document.getElementById('valor_original').value) || 0;
    const desconto = parseFloat(document.getElementById('desconto').value) || 0;
    const juros = parseFloat(document.getElementById('juros').value) || 0;
    const multa = parseFloat(document.getElementById('multa').value) || 0;
    
    const total = original - desconto + juros + multa;
    
    document.getElementById('resumo-original').textContent = formatarMoeda(original);
    document.getElementById('resumo-desconto').textContent = '- ' + formatarMoeda(desconto);
    document.getElementById('resumo-juros').textContent = '+ ' + formatarMoeda(juros);
    document.getElementById('resumo-multa').textContent = '+ ' + formatarMoeda(multa);
    document.getElementById('resumo-total').textContent = formatarMoeda(total);
}

// Eventos para atualizar o resumo
document.getElementById('valor_original').addEventListener('input', atualizarResumo);
document.getElementById('desconto').addEventListener('input', atualizarResumo);
document.getElementById('juros').addEventListener('input', atualizarResumo);
document.getElementById('multa').addEventListener('input', atualizarResumo);

// Controlar parcela atual baseado no número de parcelas
document.getElementById('numero_parcelas').addEventListener('input', function() {
    const numeroParcelas = parseInt(this.value) || 1;
    const parcelaAtual = document.getElementById('parcela_atual');
    
    parcelaAtual.max = numeroParcelas;
    
    if (parseInt(parcelaAtual.value) > numeroParcelas) {
        parcelaAtual.value = numeroParcelas;
    }
});
</script>
@endpush









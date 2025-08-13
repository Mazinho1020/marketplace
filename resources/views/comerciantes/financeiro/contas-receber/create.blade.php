@extends('comerciantes.layouts.app')

@section('title', 'Nova Conta a Receber')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.dashboard.empresa', $empresa) }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}">Contas a Receber</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Nova Conta</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nova Conta a Receber</h1>
        <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Formulário -->
    <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-receber.store', $empresa) }}">
        @csrf
        
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
                                           value="{{ old('descricao') }}" 
                                           placeholder="Ex: Venda de produto/serviço">
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
                                                    {{ old('conta_gerencial_id') == $conta->id ? 'selected' : '' }}>
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
                                    <label for="pessoa_id" class="form-label">Cliente</label>
                                    <select name="pessoa_id" id="pessoa_id" 
                                            class="form-control @error('pessoa_id') is-invalid @enderror">
                                        <option value="">Selecione um cliente</option>
                                        @foreach($pessoas as $pessoa)
                                            <option value="{{ $pessoa->id }}" 
                                                    {{ old('pessoa_id') == $pessoa->id ? 'selected' : '' }}>
                                                {{ $pessoa->nome }} 
                                                @if($pessoa->cpf_cnpj)
                                                    ({{ $pessoa->cpf_cnpj }})
                                                @endif
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
                                           value="{{ old('numero_documento') }}" 
                                           placeholder="Ex: NF-001">
                                    @error('numero_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_documento" class="form-label">Número do Documento</label>
                                    <input type="text" name="numero_documento" id="numero_documento" 
                                           class="form-control @error('numero_documento') is-invalid @enderror" 
                                           value="{{ old('numero_documento') }}" 
                                           placeholder="Ex: NF-001">
                                    @error('numero_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações</label>
                                    <textarea name="observacoes" id="observacoes" 
                                              class="form-control @error('observacoes') is-invalid @enderror" 
                                              rows="2" 
                                              placeholder="Informações adicionais">{{ old('observacoes') }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor_original" class="form-label">Valor Original *</label>
                                    <input type="number" step="0.01" name="valor_original" id="valor_original" 
                                           class="form-control @error('valor_original') is-invalid @enderror" 
                                           value="{{ old('valor_original') }}" 
                                           placeholder="0,00">
                                    @error('valor_original')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_vencimento" class="form-label">Data de Vencimento *</label>
                                    <input type="date" name="data_vencimento" id="data_vencimento" 
                                           class="form-control @error('data_vencimento') is-invalid @enderror" 
                                           value="{{ old('data_vencimento') }}">
                                    @error('data_vencimento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_emissao" class="form-label">Data de Emissão</label>
                                    <input type="date" name="data_emissao" id="data_emissao" 
                                           class="form-control @error('data_emissao') is-invalid @enderror" 
                                           value="{{ old('data_emissao', date('Y-m-d')) }}">
                                    @error('data_emissao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_competencia" class="form-label">Data de Competência</label>
                                    <input type="date" name="data_competencia" id="data_competencia" 
                                           class="form-control @error('data_competencia') is-invalid @enderror" 
                                           value="{{ old('data_competencia', date('Y-m-d')) }}">
                                    @error('data_competencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_desconto" class="form-label">Desconto</label>
                                    <input type="number" step="0.01" name="valor_desconto" id="valor_desconto" 
                                           class="form-control @error('valor_desconto') is-invalid @enderror" 
                                           value="{{ old('valor_desconto', 0) }}" 
                                           placeholder="0,00">
                                    @error('valor_desconto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_acrescimo" class="form-label">Acréscimo</label>
                                    <input type="number" step="0.01" name="valor_acrescimo" id="valor_acrescimo" 
                                           class="form-control @error('valor_acrescimo') is-invalid @enderror" 
                                           value="{{ old('valor_acrescimo', 0) }}" 
                                           placeholder="0,00">
                                    @error('valor_acrescimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_final" class="form-label">Valor Final</label>
                                    <input type="number" step="0.01" name="valor_final" id="valor_final" 
                                           class="form-control" 
                                           value="{{ old('valor_final') }}" 
                                           readonly>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="e_parcelado" id="e_parcelado" 
                                               class="form-check-input" value="1"
                                               {{ old('e_parcelado') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="e_parcelado">
                                            Parcelar esta conta
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="parcelamento-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numero_parcelas" class="form-label">Número de Parcelas</label>
                                        <input type="number" name="numero_parcelas" id="numero_parcelas" 
                                               class="form-control" 
                                               value="{{ old('numero_parcelas', 1) }}" 
                                               min="1" max="120">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="intervalo_parcelas" class="form-label">Intervalo entre Parcelas (dias)</label>
                                        <input type="number" name="intervalo_parcelas" id="intervalo_parcelas" 
                                               class="form-control" 
                                               value="{{ old('intervalo_parcelas', 30) }}" 
                                               min="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                            <dd class="col-6" id="resumo-original">R$ 0,00</dd>
                            
                            <dt class="col-6">Desconto:</dt>
                            <dd class="col-6" id="resumo-desconto">R$ 0,00</dd>
                            
                            <dt class="col-6">Acréscimo:</dt>
                            <dd class="col-6" id="resumo-acrescimo">R$ 0,00</dd>
                            
                            <dt class="col-6"><strong>Total:</strong></dt>
                            <dd class="col-6"><strong id="resumo-total">R$ 0,00</strong></dd>
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
                            <label for="natureza_financeira" class="form-label">Natureza</label>
                            <select name="natureza_financeira" id="natureza_financeira" 
                                    class="form-control @error('natureza_financeira') is-invalid @enderror">
                                <option value="receber" {{ old('natureza_financeira', 'receber') == 'receber' ? 'selected' : '' }}>
                                    Conta a Receber
                                </option>
                            </select>
                            @error('natureza_financeira')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="situacao_financeira" class="form-label">Situação</label>
                            <select name="situacao_financeira" id="situacao_financeira" 
                                    class="form-control @error('situacao_financeira') is-invalid @enderror">
                                <option value="pendente" {{ old('situacao_financeira', 'pendente') == 'pendente' ? 'selected' : '' }}>
                                    Pendente
                                </option>
                                <option value="pago" {{ old('situacao_financeira', 'pendente') == 'pago' ? 'selected' : '' }}>
                                    Recebido
                                </option>
                                <option value="cancelado" {{ old('situacao_financeira', 'pendente') == 'cancelado' ? 'selected' : '' }}>
                                    Cancelado
                                </option>
                            </select>
                            @error('situacao_financeira')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Conta
                            </button>
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const valorOriginal = document.getElementById('valor_original');
    const valorDesconto = document.getElementById('valor_desconto');
    const valorAcrescimo = document.getElementById('valor_acrescimo');
    const valorFinal = document.getElementById('valor_final');
    const eParcelado = document.getElementById('e_parcelado');
    const parcelamentoFields = document.getElementById('parcelamento-fields');

    // Resumo
    const resumoOriginal = document.getElementById('resumo-original');
    const resumoDesconto = document.getElementById('resumo-desconto');
    const resumoAcrescimo = document.getElementById('resumo-acrescimo');
    const resumoTotal = document.getElementById('resumo-total');

    // Função para calcular valores
    function calcularValores() {
        const original = parseFloat(valorOriginal.value) || 0;
        const desconto = parseFloat(valorDesconto.value) || 0;
        const acrescimo = parseFloat(valorAcrescimo.value) || 0;
        const total = original - desconto + acrescimo;

        valorFinal.value = total.toFixed(2);

        // Atualizar resumo
        resumoOriginal.textContent = 'R$ ' + original.toLocaleString('pt-BR', {minimumFractionDigits: 2});
        resumoDesconto.textContent = 'R$ ' + desconto.toLocaleString('pt-BR', {minimumFractionDigits: 2});
        resumoAcrescimo.textContent = 'R$ ' + acrescimo.toLocaleString('pt-BR', {minimumFractionDigits: 2});
        resumoTotal.textContent = 'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
    }

    // Eventos
    valorOriginal.addEventListener('input', calcularValores);
    valorDesconto.addEventListener('input', calcularValores);
    valorAcrescimo.addEventListener('input', calcularValores);

    // Controle de parcelamento
    eParcelado.addEventListener('change', function() {
        parcelamentoFields.style.display = this.checked ? 'block' : 'none';
    });

    // Calcular valores iniciais
    calcularValores();

    // Mostrar campos de parcelamento se marcado
    if (eParcelado.checked) {
        parcelamentoFields.style.display = 'block';
    }
});
</script>
@endsection

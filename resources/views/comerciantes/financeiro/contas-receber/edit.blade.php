@extends('comerciantes.layouts.app')

@section('title', 'Editar Conta a Receber')

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
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.show', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}">{{ $contaReceber->descricao }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Conta a Receber</h1>
        <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.show', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulário -->
    <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-receber.update', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}">
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
                                           value="{{ old('descricao', $contaReceber->descricao) }}" 
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
                                                    {{ old('conta_gerencial_id', $contaReceber->conta_gerencial_id) == $conta->id ? 'selected' : '' }}>
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
                                                    {{ old('pessoa_id', $contaReceber->pessoa_id) == $pessoa->id ? 'selected' : '' }}>
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
                                           value="{{ old('numero_documento', $contaReceber->numero_documento) }}" 
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
                                           value="{{ old('numero_documento', $contaReceber->numero_documento) }}" 
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
                                              placeholder="Informações adicionais">{{ old('observacoes', $contaReceber->observacoes) }}</textarea>
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
                                           value="{{ old('valor_original', $contaReceber->valor_original) }}" 
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
                                           value="{{ old('data_vencimento', $contaReceber->data_vencimento->format('Y-m-d')) }}">
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
                                           value="{{ old('data_emissao', $contaReceber->data_emissao ? $contaReceber->data_emissao->format('Y-m-d') : '') }}">
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
                                           value="{{ old('data_competencia', $contaReceber->data_competencia ? $contaReceber->data_competencia->format('Y-m-d') : '') }}">
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
                                           value="{{ old('valor_desconto', $contaReceber->valor_desconto) }}" 
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
                                           value="{{ old('valor_acrescimo', $contaReceber->valor_acrescimo) }}" 
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
                                           value="{{ old('valor_final', $contaReceber->valor_final) }}" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
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
                                <option value="receber" {{ old('natureza_financeira', $contaReceber->natureza_financeira->value) == 'receber' ? 'selected' : '' }}>
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
                                <option value="pendente" {{ old('situacao_financeira', $contaReceber->situacao_financeira->value) == 'pendente' ? 'selected' : '' }}>
                                    Pendente
                                </option>
                                <option value="pago" {{ old('situacao_financeira', $contaReceber->situacao_financeira->value) == 'pago' ? 'selected' : '' }}>
                                    Recebido
                                </option>
                                <option value="cancelado" {{ old('situacao_financeira', $contaReceber->situacao_financeira->value) == 'cancelado' ? 'selected' : '' }}>
                                    Cancelado
                                </option>
                            </select>
                            @error('situacao_financeira')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($contaReceber->situacao_financeira->value == 'pago')
                            <div class="mb-3">
                                <label for="data_pagamento" class="form-label">Data do Recebimento</label>
                                <input type="datetime-local" name="data_pagamento" id="data_pagamento" 
                                       class="form-control @error('data_pagamento') is-invalid @enderror" 
                                       value="{{ old('data_pagamento', $contaReceber->data_pagamento ? $contaReceber->data_pagamento->format('Y-m-d\TH:i') : '') }}">
                                @error('data_pagamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Atual -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Status Atual
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <span class="badge badge-{{ $contaReceber->situacao_financeira->value == 'pago' ? 'success' : 'warning' }} fs-6 mb-3">
                                {{ $contaReceber->situacao_financeira->label() }}
                            </span>
                            
                            <h4 class="text-primary mb-2">
                                R$ {{ number_format($contaReceber->valor_final, 2, ',', '.') }}
                            </h4>
                            
                            <p class="text-muted mb-0">
                                Vencimento: {{ $contaReceber->data_vencimento->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.show', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}" 
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

    // Função para calcular valores
    function calcularValores() {
        const original = parseFloat(valorOriginal.value) || 0;
        const desconto = parseFloat(valorDesconto.value) || 0;
        const acrescimo = parseFloat(valorAcrescimo.value) || 0;
        const total = original - desconto + acrescimo;

        valorFinal.value = total.toFixed(2);
    }

    // Eventos
    valorOriginal.addEventListener('input', calcularValores);
    valorDesconto.addEventListener('input', calcularValores);
    valorAcrescimo.addEventListener('input', calcularValores);

    // Calcular valores iniciais
    calcularValores();
});
</script>
@endsection

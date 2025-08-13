@extends('financial.layout')

@section('financial-title', 'Editar Conta a Pagar')

@section('financial-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit text-warning"></i>
                        Editar Conta a Pagar
                    </h5>
                    <div>
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', [$empresa, $lancamento]) }}" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-pagar.update', [$empresa, $lancamento]) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Informações Básicas -->
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Informações Básicas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="descricao">Descrição *</label>
                                                <input type="text" name="descricao" id="descricao" 
                                                       class="form-control @error('descricao') is-invalid @enderror" 
                                                       value="{{ old('descricao', $lancamento->descricao) }}" required>
                                                @error('descricao')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="codigo_lancamento">Código</label>
                                                <input type="text" name="codigo_lancamento" id="codigo_lancamento" 
                                                       class="form-control @error('codigo_lancamento') is-invalid @enderror" 
                                                       value="{{ old('codigo_lancamento', $lancamento->codigo_lancamento) }}">
                                                @error('codigo_lancamento')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" 
                                                  class="form-control @error('observacoes') is-invalid @enderror" 
                                                  rows="3">{{ old('observacoes', $lancamento->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Valores e Datas -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Valores e Datas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="valor_total">Valor Total *</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">R$</span>
                                                    </div>
                                                    <input type="number" name="valor_total" id="valor_total" 
                                                           class="form-control @error('valor_total') is-invalid @enderror" 
                                                           value="{{ old('valor_total', $lancamento->valor_total) }}" 
                                                           step="0.01" min="0" required onchange="calcularParcelas()">
                                                    @error('valor_total')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="data_vencimento">Data de Vencimento *</label>
                                                <input type="date" name="data_vencimento" id="data_vencimento" 
                                                       class="form-control @error('data_vencimento') is-invalid @enderror" 
                                                       value="{{ old('data_vencimento', $lancamento->data_vencimento ? \Carbon\Carbon::parse($lancamento->data_vencimento)->format('Y-m-d') : '') }}" required>
                                                @error('data_vencimento')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="data_competencia">Data de Competência</label>
                                                <input type="date" name="data_competencia" id="data_competencia" 
                                                       class="form-control @error('data_competencia') is-invalid @enderror" 
                                                       value="{{ old('data_competencia', $lancamento->data_competencia ? \Carbon\Carbon::parse($lancamento->data_competencia)->format('Y-m-d') : '') }}">
                                                @error('data_competencia')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Relacionamentos -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Relacionamentos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="cliente_id">Cliente/Fornecedor</label>
                                        <select name="cliente_id" id="cliente_id" 
                                                class="form-control @error('cliente_id') is-invalid @enderror">
                                            <option value="">Selecione...</option>
                                            @if(isset($clientes))
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}" 
                                                            {{ old('cliente_id', $lancamento->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                                        {{ $cliente->nome }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('cliente_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="funcionario_id">Funcionário</label>
                                        <select name="funcionario_id" id="funcionario_id" 
                                                class="form-control @error('funcionario_id') is-invalid @enderror">
                                            <option value="">Selecione...</option>
                                            @if(isset($funcionarios))
                                                @foreach($funcionarios as $funcionario)
                                                    <option value="{{ $funcionario->id }}" 
                                                            {{ old('funcionario_id', $lancamento->funcionario_id) == $funcionario->id ? 'selected' : '' }}>
                                                        {{ $funcionario->nome }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('funcionario_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="conta_id">Conta Gerencial</label>
                                        <select name="conta_id" id="conta_id" 
                                                class="form-control @error('conta_id') is-invalid @enderror">
                                            <option value="">Selecione...</option>
                                            @if(isset($contas))
                                                @foreach($contas as $conta)
                                                    <option value="{{ $conta->id }}" 
                                                            {{ old('conta_id', $lancamento->conta_id) == $conta->id ? 'selected' : '' }}>
                                                        {{ $conta->nome }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('conta_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="categoria_id">Categoria</label>
                                        <select name="categoria_id" id="categoria_id" 
                                                class="form-control @error('categoria_id') is-invalid @enderror">
                                            <option value="">Selecione...</option>
                                            @if(isset($categorias))
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" 
                                                            {{ old('categoria_id', $lancamento->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                                        {{ $categoria->nome }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('categoria_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações Avançadas -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Configurações Avançadas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="situacao">Situação</label>
                                        <select name="situacao" id="situacao" 
                                                class="form-control @error('situacao') is-invalid @enderror">
                                            @foreach(\App\Enums\SituacaoFinanceiraEnum::cases() as $situacao)
                                                <option value="{{ $situacao->value }}" 
                                                        {{ old('situacao', $lancamento->situacao->value) == $situacao->value ? 'selected' : '' }}>
                                                    {{ $situacao->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('situacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="cobranca_automatica" 
                                               name="cobranca_automatica" value="1" 
                                               {{ old('cobranca_automatica', $lancamento->cobranca_automatica) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cobranca_automatica">
                                            Cobrança Automática
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="juros_multa_config">Configuração de Juros/Multa</label>
                                        <textarea name="juros_multa_config" id="juros_multa_config" 
                                                  class="form-control @error('juros_multa_config') is-invalid @enderror" 
                                                  rows="2" placeholder="Ex: Juros 2% a.m., Multa 10%">{{ old('juros_multa_config', $lancamento->juros_multa_config) }}</textarea>
                                        @error('juros_multa_config')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', [$empresa, $lancamento]) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Atualizar Conta a Pagar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('title', 'Editar Conta a Pagar')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Editar Conta a Pagar #{{ $conta->id }}
                    </h2>
                    <p class="text-muted mb-0">Altere as informações da conta a pagar</p>
                </div>
                <div>
                    <a href="{{ route('financial.contas-pagar.show', $conta->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>
                        Visualizar
                    </a>
                    <a href="{{ route('financial.contas-pagar.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar à Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('financial.contas-pagar.update', $conta->id) }}" id="formContaPagar">
        @csrf
        @method('PUT')
        <input type="hidden" name="natureza" value="PAGAR">
        
        <div class="row">
            <!-- Formulário Principal -->
            <div class="col-lg-8">
                <!-- Status Atual -->
                <div class="alert alert-info">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">Status Atual da Conta</h6>
                            <span class="status-badge" style="background-color: {{ $conta->situacao->getColor() }}; color: white;">
                                <i class="{{ $conta->situacao->getIcon() }} me-2"></i>
                                {{ $conta->situacao->getLabel() }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Valor Total</h6>
                            <h4 class="text-danger mb-0">R$ {{ number_format($conta->valor_final, 2, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Dados Básicos -->
                <div class="card financial-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Dados Básicos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Descrição da Conta *</label>
                                <input type="text" name="descricao" class="form-control @error('descricao') is-invalid @enderror" 
                                       value="{{ old('descricao', $conta->descricao) }}" required
                                       placeholder="Ex: Pagamento de fornecedor, Aluguel, Energia elétrica...">
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Fornecedor</label>
                                <select name="pessoa_id" class="form-select @error('pessoa_id') is-invalid @enderror">
                                    <option value="">Selecione o fornecedor</option>
                                    @foreach($fornecedores as $fornecedor)
                                        <option value="{{ $fornecedor->id }}" 
                                                {{ old('pessoa_id', $conta->pessoa_id) == $fornecedor->id ? 'selected' : '' }}>
                                            {{ $fornecedor->nome }} - {{ $fornecedor->documento ?? 'Sem documento' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pessoa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <a href="#" onclick="abrirModalFornecedor()">+ Cadastrar novo fornecedor</a>
                                </small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <select name="categoria" class="form-select @error('categoria') is-invalid @enderror">
                                    <option value="">Selecione a categoria</option>
                                    <option value="fornecedores" {{ old('categoria', $conta->categoria) == 'fornecedores' ? 'selected' : '' }}>Fornecedores</option>
                                    <option value="aluguel" {{ old('categoria', $conta->categoria) == 'aluguel' ? 'selected' : '' }}>Aluguel</option>
                                    <option value="energia" {{ old('categoria', $conta->categoria) == 'energia' ? 'selected' : '' }}>Energia Elétrica</option>
                                    <option value="agua" {{ old('categoria', $conta->categoria) == 'agua' ? 'selected' : '' }}>Água</option>
                                    <option value="telefone" {{ old('categoria', $conta->categoria) == 'telefone' ? 'selected' : '' }}>Telefone/Internet</option>
                                    <option value="combustivel" {{ old('categoria', $conta->categoria) == 'combustivel' ? 'selected' : '' }}>Combustível</option>
                                    <option value="manutencao" {{ old('categoria', $conta->categoria) == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                                    <option value="marketing" {{ old('categoria', $conta->categoria) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="impostos" {{ old('categoria', $conta->categoria) == 'impostos' ? 'selected' : '' }}>Impostos</option>
                                    <option value="outros" {{ old('categoria', $conta->categoria) == 'outros' ? 'selected' : '' }}>Outros</option>
                                </select>
                                @error('categoria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Valores e Datas -->
                <div class="card financial-card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Valores e Datas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Valor Original *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" name="valor_original" 
                                           class="form-control money @error('valor_original') is-invalid @enderror" 
                                           value="{{ old('valor_original', number_format($conta->valor_original, 2, ',', '.')) }}" required
                                           placeholder="0,00">
                                </div>
                                @error('valor_original')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Data de Vencimento *</label>
                                <input type="date" name="data_vencimento" 
                                       class="form-control @error('data_vencimento') is-invalid @enderror" 
                                       value="{{ old('data_vencimento', $conta->data_vencimento->format('Y-m-d')) }}" required>
                                @error('data_vencimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Conta Gerencial</label>
                                <select name="conta_gerencial_id" class="form-select @error('conta_gerencial_id') is-invalid @enderror">
                                    <option value="">Selecione a conta</option>
                                    @foreach($contasGerenciais as $contaGerencial)
                                        <option value="{{ $contaGerencial->id }}" 
                                                {{ old('conta_gerencial_id', $conta->conta_gerencial_id) == $contaGerencial->id ? 'selected' : '' }}>
                                            {{ $contaGerencial->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('conta_gerencial_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Valores Adicionais -->
                <div class="card financial-card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Valores Adicionais
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Desconto</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" name="valor_desconto" 
                                           class="form-control money @error('valor_desconto') is-invalid @enderror" 
                                           value="{{ old('valor_desconto', number_format($conta->valor_desconto ?? 0, 2, ',', '.')) }}"
                                           placeholder="0,00">
                                </div>
                                @error('valor_desconto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Juros</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" name="valor_juros" 
                                           class="form-control money @error('valor_juros') is-invalid @enderror" 
                                           value="{{ old('valor_juros', number_format($conta->valor_juros ?? 0, 2, ',', '.')) }}"
                                           placeholder="0,00">
                                </div>
                                @error('valor_juros')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Multa</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" name="valor_multa" 
                                           class="form-control money @error('valor_multa') is-invalid @enderror" 
                                           value="{{ old('valor_multa', number_format($conta->valor_multa ?? 0, 2, ',', '.')) }}"
                                           placeholder="0,00">
                                </div>
                                @error('valor_multa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Valor Final</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="valorFinalCalculado" readonly>
                                </div>
                                <small class="form-text text-muted">Calculado automaticamente</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="card financial-card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>
                            Observações
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Observações Gerais</label>
                                <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" 
                                          rows="4" placeholder="Informações adicionais sobre esta conta a pagar...">{{ old('observacoes', $conta->observacoes) }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                @if($conta->situacao === \App\Enums\SituacaoFinanceiraEnum::PAGO)
                    <!-- Informações do Pagamento (Somente Leitura) -->
                    <div class="card financial-card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Informações do Pagamento (Somente Leitura)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @if($conta->data_pagamento)
                                    <div class="col-md-4">
                                        <label class="form-label">Data do Pagamento</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $conta->data_pagamento->format('d/m/Y') }}" readonly>
                                    </div>
                                @endif
                                
                                @if($conta->valor_pago)
                                    <div class="col-md-4">
                                        <label class="form-label">Valor Pago</label>
                                        <input type="text" class="form-control" 
                                               value="R$ {{ number_format($conta->valor_pago, 2, ',', '.') }}" readonly>
                                    </div>
                                @endif
                                
                                @if($conta->forma_pagamento)
                                    <div class="col-md-4">
                                        <label class="form-label">Forma de Pagamento</label>
                                        <input type="text" class="form-control" 
                                               value="{{ ucfirst(str_replace('_', ' ', $conta->forma_pagamento)) }}" readonly>
                                    </div>
                                @endif
                                
                                @if($conta->observacoes_pagamento)
                                    <div class="col-12">
                                        <label class="form-label">Observações do Pagamento</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $conta->observacoes_pagamento }}</textarea>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Atenção:</strong> Esta conta já foi paga. As informações de pagamento não podem ser alteradas através desta tela. 
                                Para modificar dados do pagamento, entre em contato com o administrador do sistema.
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar com Resumo -->
            <div class="col-lg-4">
                <!-- Resumo da Conta -->
                <div class="card financial-card mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Resumo da Conta
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-12">
                                <h3 class="text-danger mb-0" id="valorTotalResumo">R$ {{ number_format($conta->valor_final, 2, ',', '.') }}</h3>
                                <small class="text-muted">Valor Total</small>
                            </div>
                            
                            <div class="col-6">
                                <h5 class="text-primary mb-0">R$ {{ number_format($conta->valor_original, 2, ',', '.') }}</h5>
                                <small class="text-muted">Valor Original</small>
                            </div>
                            
                            <div class="col-6">
                                <h5 class="text-{{ $conta->isVencida() ? 'danger' : 'success' }} mb-0">
                                    {{ $conta->isVencida() ? abs($conta->diasParaVencimento()) . ' dias' : $conta->diasParaVencimento() . ' dias' }}
                                </h5>
                                <small class="text-muted">
                                    {{ $conta->isVencida() ? 'Em atraso' : 'Para vencimento' }}
                                </small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Salvar Alterações
                            </button>
                            
                            <a href="{{ route('financial.contas-pagar.show', $conta->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-2"></i>
                                Visualizar Conta
                            </a>
                            
                            <button type="button" class="btn btn-outline-secondary" onclick="resetarFormulario()">
                                <i class="fas fa-undo me-2"></i>
                                Resetar Formulário
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status e Alertas -->
                <div class="card financial-card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Status e Alertas
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($conta->isVencida())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Conta Vencida!</strong><br>
                                Esta conta está {{ abs($conta->diasParaVencimento()) }} dias em atraso.
                            </div>
                        @elseif($conta->diasParaVencimento() <= 7 && $conta->diasParaVencimento() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Vencimento Próximo!</strong><br>
                                Esta conta vence em {{ $conta->diasParaVencimento() }} dias.
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Conta em Dia!</strong><br>
                                Vencimento em {{ $conta->diasParaVencimento() }} dias.
                            </div>
                        @endif
                        
                        <ul class="list-unstyled mb-0 mt-3">
                            <li class="mb-2">
                                <strong>Criada em:</strong> {{ $conta->created_at->format('d/m/Y H:i') }}
                            </li>
                            <li class="mb-0">
                                <strong>Última alteração:</strong> {{ $conta->updated_at->format('d/m/Y H:i') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal para Cadastrar Fornecedor -->
<div class="modal fade" id="modalFornecedor" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Cadastrar Novo Fornecedor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formFornecedor">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome do Fornecedor *</label>
                            <input type="text" name="nome_fornecedor" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="tipo_fornecedor" class="form-select">
                                <option value="juridica">Pessoa Jurídica</option>
                                <option value="fisica">Pessoa Física</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Documento</label>
                            <input type="text" name="documento_fornecedor" class="form-control documento">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone_fornecedor" class="form-control phone">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_fornecedor" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar Fornecedor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Calcular valor final quando algum campo mudar
    $('input[name="valor_original"], input[name="valor_desconto"], input[name="valor_juros"], input[name="valor_multa"]').on('keyup change', function() {
        calcularValorFinal();
    });
    
    // Máscaras dinâmicas para documento
    $('select[name="tipo_fornecedor"]').change(function() {
        const tipo = $(this).val();
        const documentoField = $('input[name="documento_fornecedor"]');
        
        documentoField.unmask();
        
        if (tipo === 'fisica') {
            documentoField.mask('000.000.000-00');
            documentoField.attr('placeholder', '000.000.000-00');
        } else {
            documentoField.mask('00.000.000/0000-00');
            documentoField.attr('placeholder', '00.000.000/0000-00');
        }
    });
    
    // Inicializar máscara padrão (CNPJ)
    $('input[name="documento_fornecedor"]').mask('00.000.000/0000-00');
    
    // Calcular valor final inicial
    calcularValorFinal();
});

function calcularValorFinal() {
    const valorOriginal = parseMoney($('input[name="valor_original"]').val() || '0');
    const valorDesconto = parseMoney($('input[name="valor_desconto"]').val() || '0');
    const valorJuros = parseMoney($('input[name="valor_juros"]').val() || '0');
    const valorMulta = parseMoney($('input[name="valor_multa"]').val() || '0');
    
    const valorFinal = valorOriginal - valorDesconto + valorJuros + valorMulta;
    
    $('#valorFinalCalculado').val(formatMoney(valorFinal).replace('R$ ', ''));
    $('#valorTotalResumo').text(formatMoney(valorFinal));
}

function abrirModalFornecedor() {
    $('#modalFornecedor').modal('show');
}

function resetarFormulario() {
    if (confirm('Tem certeza que deseja resetar o formulário? Todas as alterações não salvas serão perdidas.')) {
        location.reload();
    }
}

// Cadastro de fornecedor via AJAX
$('#formFornecedor').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '{{ route("pessoas.store.ajax") }}',
        type: 'POST',
        data: formData + '&tipo=fornecedor',
        success: function(response) {
            if (response.success) {
                // Adicionar nova opção ao select
                const option = `<option value="${response.data.id}" selected>
                    ${response.data.nome} - ${response.data.documento || 'Sem documento'}
                </option>`;
                $('select[name="pessoa_id"]').append(option).trigger('change');
                
                // Fechar modal e limpar formulário
                $('#modalFornecedor').modal('hide');
                $('#formFornecedor')[0].reset();
                
                toastr.success('Fornecedor cadastrado com sucesso!');
            } else {
                toastr.error(response.message || 'Erro ao cadastrar fornecedor');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = 'Erro de validação:\n';
                Object.values(errors).forEach(error => {
                    errorMessage += `• ${error[0]}\n`;
                });
                toastr.error(errorMessage);
            } else {
                toastr.error('Erro ao cadastrar fornecedor');
            }
        }
    });
});
</script>
@endsection

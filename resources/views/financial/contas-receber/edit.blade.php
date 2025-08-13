@extends('financial.layout')

@section('financial-title', 'Editar Conta a Receber')

@section('financial-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit text-warning"></i>
                        Editar Conta a Receber
                    </h5>
                    <div>
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.show', [$empresa, $lancamento]) }}" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-receber.update', [$empresa, $lancamento]) }}">
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
                                                           step="0.01" min="0" required>
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
                                        <label for="cliente_id">Cliente *</label>
                                        <select name="cliente_id" id="cliente_id" 
                                                class="form-control @error('cliente_id') is-invalid @enderror" required>
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
                                        <label for="funcionario_id">Funcionário Responsável</label>
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

                            <!-- Configurações de Cobrança -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Configurações de Cobrança</h6>
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
                                <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.show', [$empresa, $lancamento]) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Atualizar Conta a Receber
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

@extends('financial.layout')

@section('financial-title', 'Nova Conta a Receber')

@section('financial-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus text-success"></i>
                        Nova Conta a Receber
                    </h5>
                    <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-receber.store', $empresa) }}">
                    @csrf
                    
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
                                                       value="{{ old('descricao') }}" required>
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
                                                       value="{{ old('codigo_lancamento') }}">
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
                                                  rows="3">{{ old('observacoes') }}</textarea>
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
                                                           value="{{ old('valor_total') }}" step="0.01" min="0" 
                                                           required onchange="calcularParcelas()">
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
                                                       value="{{ old('data_vencimento') }}" required>
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
                                                       value="{{ old('data_competencia') }}">
                                                @error('data_competencia')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parcelamento -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Parcelamento</h6>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tem_parcelamento" 
                                                   name="tem_parcelamento" value="1" 
                                                   {{ old('tem_parcelamento') ? 'checked' : '' }}
                                                   onchange="toggleParcelamento()">
                                            <label class="form-check-label" for="tem_parcelamento">
                                                Parcelar conta
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" id="parcelamento_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="numero_parcelas">Número de Parcelas</label>
                                                <input type="number" name="numero_parcelas" id="numero_parcelas" 
                                                       class="form-control @error('numero_parcelas') is-invalid @enderror" 
                                                       value="{{ old('numero_parcelas', 1) }}" min="1" max="60"
                                                       onchange="calcularParcelas()">
                                                @error('numero_parcelas')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="valor_parcela">Valor por Parcela</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">R$</span>
                                                    </div>
                                                    <input type="number" name="valor_parcela" id="valor_parcela" 
                                                           class="form-control @error('valor_parcela') is-invalid @enderror" 
                                                           value="{{ old('valor_parcela') }}" step="0.01" min="0" readonly>
                                                    @error('valor_parcela')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="frequencia_recorrencia">Frequência</label>
                                                <select name="frequencia_recorrencia" id="frequencia_recorrencia" 
                                                        class="form-control @error('frequencia_recorrencia') is-invalid @enderror">
                                                    @foreach(\App\Enums\FrequenciaRecorrenciaEnum::cases() as $frequencia)
                                                        <option value="{{ $frequencia->value }}" 
                                                                {{ old('frequencia_recorrencia') == $frequencia->value ? 'selected' : '' }}>
                                                            {{ $frequencia->label() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('frequencia_recorrencia')
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
                                                            {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
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
                                                            {{ old('funcionario_id') == $funcionario->id ? 'selected' : '' }}>
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
                                                            {{ old('conta_id') == $conta->id ? 'selected' : '' }}>
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
                                                            {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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
                                                        {{ old('situacao', 'PENDENTE') == $situacao->value ? 'selected' : '' }}>
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
                                               {{ old('cobranca_automatica') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cobranca_automatica">
                                            Cobrança Automática
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="gerar_boleto" 
                                               name="gerar_boleto" value="1" 
                                               {{ old('gerar_boleto') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gerar_boleto">
                                            Gerar Boleto Automaticamente
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="juros_multa_config">Configuração de Juros/Multa</label>
                                        <textarea name="juros_multa_config" id="juros_multa_config" 
                                                  class="form-control @error('juros_multa_config') is-invalid @enderror" 
                                                  rows="2" placeholder="Ex: Juros 2% a.m., Multa 10%">{{ old('juros_multa_config') }}</textarea>
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
                                <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Conta a Receber
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

@push('scripts')
<script>
function toggleParcelamento() {
    const checkbox = document.getElementById('tem_parcelamento');
    const section = document.getElementById('parcelamento_section');
    
    if (checkbox.checked) {
        section.style.display = 'block';
        document.getElementById('numero_parcelas').value = '2';
        calcularParcelas();
    } else {
        section.style.display = 'none';
        document.getElementById('numero_parcelas').value = '1';
        document.getElementById('valor_parcela').value = '';
    }
}

function calcularParcelas() {
    const valorTotal = parseFloat(document.getElementById('valor_total').value) || 0;
    const numeroParcelas = parseInt(document.getElementById('numero_parcelas').value) || 1;
    
    if (valorTotal > 0 && numeroParcelas > 0) {
        const valorParcela = valorTotal / numeroParcelas;
        document.getElementById('valor_parcela').value = valorParcela.toFixed(2);
    }
}

// Inicializar estado do parcelamento
document.addEventListener('DOMContentLoaded', function() {
    toggleParcelamento();
});
</script>
@endpush
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-plus text-success me-2"></i>
                        Nova Conta a Receber
                    </h2>
                    <p class="text-muted mb-0">Cadastre uma nova conta a receber no sistema</p>
                </div>
                <div>
                    <a href="{{ route('financial.contas-receber.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar à Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('financial.contas-receber.store') }}" id="formContaReceber">
        @csrf
        <input type="hidden" name="natureza" value="RECEBER">
        
        <div class="row">
            <!-- Formulário Principal -->
            <div class="col-lg-8">
                <!-- Dados Básicos -->
                <div class="card financial-card mb-4">
                    <div class="card-header bg-success text-white">
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
                                       value="{{ old('descricao') }}" required
                                       placeholder="Ex: Venda de produtos, Prestação de serviços, Comissões...">
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Cliente</label>
                                <select name="pessoa_id" class="form-select @error('pessoa_id') is-invalid @enderror">
                                    <option value="">Selecione o cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('pessoa_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome }} - {{ $cliente->documento ?? 'Sem documento' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pessoa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <a href="#" onclick="abrirModalCliente()">+ Cadastrar novo cliente</a>
                                </small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <select name="categoria" class="form-select @error('categoria') is-invalid @enderror">
                                    <option value="">Selecione a categoria</option>
                                    <option value="vendas" {{ old('categoria') == 'vendas' ? 'selected' : '' }}>Vendas</option>
                                    <option value="servicos" {{ old('categoria') == 'servicos' ? 'selected' : '' }}>Serviços</option>
                                    <option value="comissoes" {{ old('categoria') == 'comissoes' ? 'selected' : '' }}>Comissões</option>
                                    <option value="alugueis" {{ old('categoria') == 'alugueis' ? 'selected' : '' }}>Aluguéis</option>
                                    <option value="royalties" {{ old('categoria') == 'royalties' ? 'selected' : '' }}>Royalties</option>
                                    <option value="dividendos" {{ old('categoria') == 'dividendos' ? 'selected' : '' }}>Dividendos</option>
                                    <option value="juros" {{ old('categoria') == 'juros' ? 'selected' : '' }}>Juros</option>
                                    <option value="reembolsos" {{ old('categoria') == 'reembolsos' ? 'selected' : '' }}>Reembolsos</option>
                                    <option value="outros" {{ old('categoria') == 'outros' ? 'selected' : '' }}>Outros</option>
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
                    <div class="card-header bg-primary text-white">
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
                                           value="{{ old('valor_original') }}" required
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
                                       value="{{ old('data_vencimento') }}" required>
                                @error('data_vencimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Conta Gerencial</label>
                                <select name="conta_gerencial_id" class="form-select @error('conta_gerencial_id') is-invalid @enderror">
                                    <option value="">Selecione a conta</option>
                                    @foreach($contasGerenciais as $conta)
                                        <option value="{{ $conta->id }}" {{ old('conta_gerencial_id') == $conta->id ? 'selected' : '' }}>
                                            {{ $conta->nome }}
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

                <!-- Parcelamento -->
                <div class="card financial-card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Parcelamento
                            </h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="habilitarParcelamento" 
                                       {{ old('is_parcelado') ? 'checked' : '' }}>
                                <label class="form-check-label" for="habilitarParcelamento">
                                    Habilitar Parcelamento
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="parcelamentoContent" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Número de Parcelas</label>
                                <select name="numero_parcelas" class="form-select" id="numeroParcelas">
                                    <option value="">Selecione</option>
                                    @for($i = 2; $i <= 24; $i++)
                                        <option value="{{ $i }}" {{ old('numero_parcelas') == $i ? 'selected' : '' }}>
                                            {{ $i }}x
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Frequência</label>
                                <select name="frequencia_recorrencia" class="form-select">
                                    <option value="">Selecione a frequência</option>
                                    @foreach(\App\Enums\FrequenciaRecorrenciaEnum::cases() as $frequencia)
                                        <option value="{{ $frequencia->value }}" 
                                                {{ old('frequencia_recorrencia') == $frequencia->value ? 'selected' : '' }}>
                                            {{ $frequencia->getLabel() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Valor da Parcela</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control money" id="valorParcela" readonly>
                                </div>
                                <small class="form-text text-muted">Calculado automaticamente</small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Importante:</strong> O parcelamento criará múltiplas contas a receber com as datas de vencimento calculadas automaticamente.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="card financial-card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>
                            Observações Adicionais
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" 
                                          rows="4" placeholder="Informações adicionais sobre esta conta a receber...">{{ old('observacoes') }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
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
                                <h3 class="text-success mb-0" id="valorTotal">R$ 0,00</h3>
                                <small class="text-muted">Valor Total</small>
                            </div>
                            
                            <div class="col-6">
                                <h5 class="text-warning mb-0" id="qtdParcelas">1</h5>
                                <small class="text-muted">Parcela(s)</small>
                            </div>
                            
                            <div class="col-6">
                                <h5 class="text-info mb-0" id="valorParcelaResumo">R$ 0,00</h5>
                                <small class="text-muted">Por Parcela</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Salvar Conta a Receber
                            </button>
                            
                            <button type="button" class="btn btn-outline-secondary" onclick="limparFormulario()">
                                <i class="fas fa-eraser me-2"></i>
                                Limpar Formulário
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dicas -->
                <div class="card financial-card">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Dicas
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Descreva claramente a origem da receita</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Categorize para facilitar relatórios</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Use parcelamento para vendas a prazo</small>
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Monitore os vencimentos regularmente</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal para Cadastrar Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Cadastrar Novo Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCliente">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome do Cliente *</label>
                            <input type="text" name="nome_cliente" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="tipo_cliente" class="form-select">
                                <option value="juridica">Pessoa Jurídica</option>
                                <option value="fisica">Pessoa Física</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Documento</label>
                            <input type="text" name="documento_cliente" class="form-control documento">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone_cliente" class="form-control phone">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email_cliente" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Cadastrar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Controle do parcelamento
    $('#habilitarParcelamento').change(function() {
        if ($(this).is(':checked')) {
            $('#parcelamentoContent').slideDown();
            $('input[name="is_parcelado"]').val('1');
        } else {
            $('#parcelamentoContent').slideUp();
            $('input[name="is_parcelado"]').val('0');
            $('#numeroParcelas').val('');
            calcularResumo();
        }
    });
    
    // Verificar se está marcado no carregamento da página
    if ($('#habilitarParcelamento').is(':checked')) {
        $('#parcelamentoContent').show();
    }
    
    // Calcular valor das parcelas
    $('#numeroParcelas, input[name="valor_original"]').on('change keyup', function() {
        calcularParcelas();
        calcularResumo();
    });
    
    // Atualizar resumo quando valor mudar
    $('input[name="valor_original"]').on('keyup change', function() {
        calcularResumo();
    });
    
    // Máscaras dinâmicas para documento
    $('select[name="tipo_cliente"]').change(function() {
        const tipo = $(this).val();
        const documentoField = $('input[name="documento_cliente"]');
        
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
    $('input[name="documento_cliente"]').mask('00.000.000/0000-00');
});

function calcularParcelas() {
    const valorOriginal = parseMoney($('input[name="valor_original"]').val() || '0');
    const numeroParcelas = parseInt($('#numeroParcelas').val()) || 1;
    
    if (valorOriginal > 0 && numeroParcelas > 1) {
        const valorParcela = valorOriginal / numeroParcelas;
        $('#valorParcela').val(formatMoney(valorParcela).replace('R$ ', ''));
    } else {
        $('#valorParcela').val('0,00');
    }
}

function calcularResumo() {
    const valorOriginal = parseMoney($('input[name="valor_original"]').val() || '0');
    const numeroParcelas = parseInt($('#numeroParcelas').val()) || 1;
    const isParcelado = $('#habilitarParcelamento').is(':checked');
    
    $('#valorTotal').text(formatMoney(valorOriginal));
    
    if (isParcelado && numeroParcelas > 1) {
        $('#qtdParcelas').text(numeroParcelas);
        $('#valorParcelaResumo').text(formatMoney(valorOriginal / numeroParcelas));
    } else {
        $('#qtdParcelas').text('1');
        $('#valorParcelaResumo').text(formatMoney(valorOriginal));
    }
}

function abrirModalCliente() {
    $('#modalCliente').modal('show');
}

function limparFormulario() {
    if (confirm('Tem certeza que deseja limpar todos os campos do formulário?')) {
        $('#formContaReceber')[0].reset();
        $('#habilitarParcelamento').prop('checked', false);
        $('#parcelamentoContent').hide();
        calcularResumo();
        
        // Resetar Select2
        $('select').trigger('change');
    }
}

// Cadastro de cliente via AJAX
$('#formCliente').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '{{ route("pessoas.store.ajax") }}',
        type: 'POST',
        data: formData + '&tipo=cliente',
        success: function(response) {
            if (response.success) {
                // Adicionar nova opção ao select
                const option = `<option value="${response.data.id}" selected>
                    ${response.data.nome} - ${response.data.documento || 'Sem documento'}
                </option>`;
                $('select[name="pessoa_id"]').append(option).trigger('change');
                
                // Fechar modal e limpar formulário
                $('#modalCliente').modal('hide');
                $('#formCliente')[0].reset();
                
                toastr.success('Cliente cadastrado com sucesso!');
            } else {
                toastr.error(response.message || 'Erro ao cadastrar cliente');
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
                toastr.error('Erro ao cadastrar cliente');
            }
        }
    });
});

// Inicializar resumo na carga da página
calcularResumo();
</script>
@endsection

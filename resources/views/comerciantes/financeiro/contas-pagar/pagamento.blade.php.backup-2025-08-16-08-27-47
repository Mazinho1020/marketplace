@extends('layouts.comerciante')

@section('title', 'Registrar Pagamento')

@section('content')
@php
    use Illuminate\Support\Facades\DB;
@endphp
<div class="container-fluid">
    <!-- Header com Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}">Contas a Pagar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Pagamento</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Registrar Pagamento</h1>
        </div>
        <div>
            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Resumo da Conta -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Descrição:</strong><br>
                        <span class="text-muted">{{ $contaPagar->descricao }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Valor Total:</strong><br>
                        <span class="h6 text-danger">R$ {{ number_format($contaPagar->valor_total ?? $contaPagar->valor, 2, ',', '.') }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Saldo a Pagar:</strong><br>
                        @php
                            $valorPago = $contaPagar->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor') ?? 0;
                            $saldoPagar = ($contaPagar->valor_total ?? $contaPagar->valor) - $valorPago;
                        @endphp
                        <span class="h6 text-danger" id="saldoPagar">R$ {{ number_format($saldoPagar, 2, ',', '.') }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Vencimento:</strong><br>
                        <span class="text-muted">{{ $contaPagar->data_vencimento ? \Carbon\Carbon::parse($contaPagar->data_vencimento)->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Pagamento -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Dados do Pagamento
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('comerciantes.empresas.financeiro.contas-pagar.pagamentos.store', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" 
                          method="POST" id="formPagamento">
                        @csrf

                        <div class="row">
                            <!-- Valor do Pagamento -->
                            <div class="col-md-6 mb-3">
                                <label for="valor" class="form-label">Valor do Pagamento *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" 
                                           class="form-control @error('valor') is-invalid @enderror" 
                                           id="valor" 
                                           name="valor" 
                                           step="0.01" 
                                           min="0.01"
                                           max="{{ $saldoPagar }}"
                                           value="{{ old('valor', number_format($saldoPagar, 2, '.', '')) }}" 
                                           required>
                                </div>
                                @error('valor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Máximo: R$ {{ number_format($saldoPagar, 2, ',', '.') }}</small>
                            </div>

                            <!-- Data do Pagamento -->
                            <div class="col-md-6 mb-3">
                                <label for="data_pagamento" class="form-label">Data do Pagamento *</label>
                                <input type="date" 
                                       class="form-control @error('data_pagamento') is-invalid @enderror" 
                                       id="data_pagamento" 
                                       name="data_pagamento" 
                                       value="{{ old('data_pagamento', date('Y-m-d')) }}" 
                                       required>
                                @error('data_pagamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Forma de Pagamento -->
                            <div class="col-md-6 mb-3">
                                <label for="forma_pagamento_id" class="form-label">Forma de Pagamento *</label>
                                <select class="form-control @error('forma_pagamento_id') is-invalid @enderror" 
                                        id="forma_pagamento_id" 
                                        name="forma_pagamento_id" 
                                        required>
                                    <option value="">Selecione...</option>
                                    @foreach($formasPagamento as $forma)
                                        <option value="{{ $forma->id }}" {{ old('forma_pagamento_id') == $forma->id ? 'selected' : '' }}>
                                            {{ $forma->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('forma_pagamento_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Conta Bancária -->
                            <div class="col-md-6 mb-3">
                                <label for="conta_bancaria_id" class="form-label">Conta Bancária *</label>
                                <select class="form-control @error('conta_bancaria_id') is-invalid @enderror" 
                                        id="conta_bancaria_id" 
                                        name="conta_bancaria_id" 
                                        required>
                                    <option value="">Selecione...</option>
                                    @foreach($contasBancarias as $conta)
                                        <option value="{{ $conta->id }}" {{ old('conta_bancaria_id') == $conta->id ? 'selected' : '' }}>
                                            {{ $conta->nome }} - {{ $conta->banco }} ({{ $conta->agencia }}/{{ $conta->conta }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('conta_bancaria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Valores Detalhados (Opcional) -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#valoresDetalhados" aria-expanded="false">
                                    <i class="fas fa-chevron-down me-2"></i>Valores Detalhados (Opcional)
                                </button>
                            </div>
                            <div class="collapse" id="valoresDetalhados">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label for="valor_principal" class="form-label">Valor Principal</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control" id="valor_principal" name="valor_principal" 
                                                       step="0.01" min="0" value="{{ old('valor_principal') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="valor_juros" class="form-label">Juros</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control" id="valor_juros" name="valor_juros" 
                                                       step="0.01" min="0" value="{{ old('valor_juros') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="valor_multa" class="form-label">Multa</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control" id="valor_multa" name="valor_multa" 
                                                       step="0.01" min="0" value="{{ old('valor_multa') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="valor_desconto" class="form-label">Desconto</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control" id="valor_desconto" name="valor_desconto" 
                                                       step="0.01" min="0" value="{{ old('valor_desconto') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="mb-3">
                            <label for="observacao" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacao" name="observacao" rows="3" 
                                      placeholder="Informações adicionais sobre o pagamento...">{{ old('observacao') }}</textarea>
                        </div>

                        <!-- Referência Externa -->
                        <div class="mb-3">
                            <label for="referencia_externa" class="form-label">Referência Externa</label>
                            <input type="text" class="form-control" id="referencia_externa" name="referencia_externa" 
                                   value="{{ old('referencia_externa') }}" placeholder="Número do documento, ID da transação, etc.">
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-dollar-sign me-2"></i>Registrar Pagamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar com Informações -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informações da Conta
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Número:</strong></td>
                            <td>{{ $contaPagar->numero_documento ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Emissão:</strong></td>
                            <td>{{ $contaPagar->data_emissao ? \Carbon\Carbon::parse($contaPagar->data_emissao)->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Vencimento:</strong></td>
                            <td>{{ $contaPagar->data_vencimento ? \Carbon\Carbon::parse($contaPagar->data_vencimento)->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Situação:</strong></td>
                            <td>
                                <span class="badge bg-{{ $contaPagar->situacao_financeira === 'pendente' ? 'warning' : ($contaPagar->situacao_financeira === 'pago' ? 'success' : 'danger') }}">
                                    {{ ucfirst($contaPagar->situacao_financeira) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Histórico de Pagamentos -->
            @if($contaPagar->pagamentos && $contaPagar->pagamentos->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Pagamentos Anteriores
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($contaPagar->pagamentos as $pagamento)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') }}</small><br>
                            <strong>R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</strong>
                        </div>
                        <span class="badge bg-{{ $pagamento->status_pagamento === 'confirmado' ? 'success' : 'warning' }}">
                            {{ ucfirst($pagamento->status_pagamento) }}
                        </span>
                    </div>
                    @if(!$loop->last)<hr>@endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do valor máximo
    const valorInput = document.getElementById('valor');
    const saldoPagar = {{ $saldoPagar ?? 0 }};
    
    valorInput.addEventListener('input', function() {
        const valor = parseFloat(this.value) || 0;
        if (valor > saldoPagar) {
            this.value = saldoPagar.toFixed(2);
            alert('O valor não pode ser maior que o saldo devedor!');
        }
    });
    
    // Auto-cálculo dos valores detalhados
    const valoresDetalhados = ['valor_principal', 'valor_juros', 'valor_multa', 'valor_desconto'];
    
    valoresDetalhados.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            input.addEventListener('input', calcularValorTotal);
        }
    });
    
    function calcularValorTotal() {
        const principal = parseFloat(document.getElementById('valor_principal').value) || 0;
        const juros = parseFloat(document.getElementById('valor_juros').value) || 0;
        const multa = parseFloat(document.getElementById('valor_multa').value) || 0;
        const desconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
        
        if (principal > 0 || juros > 0 || multa > 0 || desconto > 0) {
            const total = principal + juros + multa - desconto;
            document.getElementById('valor').value = total.toFixed(2);
        }
    }
});
</script>
@endsection

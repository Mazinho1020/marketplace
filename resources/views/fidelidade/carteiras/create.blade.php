@extends('layouts.app')

@section('title', 'Nova Carteira de Fidelidade')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-wallet text-primary"></i>
                        Nova Carteira de Fidelidade
                    </h1>
                    <p class="text-muted mb-0">Criar uma nova carteira para cliente</p>
                </div>
                <a href="{{ route('fidelidade.carteiras.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-plus"></i>
                                Dados da Carteira
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fidelidade.carteiras.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <!-- Cliente -->
                                    <div class="col-md-6 mb-3">
                                        <label for="cliente_id" class="form-label">
                                            <i class="fas fa-user"></i> Cliente *
                                        </label>
                                        <select class="form-select @error('cliente_id') is-invalid @enderror"
                                            id="cliente_id" name="cliente_id" required>
                                            <option value="">Selecione um cliente</option>
                                            @foreach($clientes ?? [] as $cliente)
                                            <option value="{{ $cliente->id }}" {{ old('cliente_id')==$cliente->id ?
                                                'selected' : '' }}>
                                                {{ $cliente->nome }} {{ $cliente->email ? '(' . $cliente->email . ')' :
                                                '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('cliente_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Empresa -->
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_id" class="form-label">
                                            <i class="fas fa-building"></i> Empresa *
                                        </label>
                                        <select class="form-select @error('empresa_id') is-invalid @enderror"
                                            id="empresa_id" name="empresa_id" required>
                                            <option value="">Selecione uma empresa</option>
                                            @foreach($empresas ?? [] as $empresa)
                                            <option value="{{ $empresa->id }}" {{ old('empresa_id')==$empresa->id ?
                                                'selected' : '' }}>
                                                {{ $empresa->business_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('empresa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Nível -->
                                    <div class="col-md-6 mb-3">
                                        <label for="nivel_atual" class="form-label">
                                            <i class="fas fa-medal"></i> Nível *
                                        </label>
                                        <select class="form-select @error('nivel_atual') is-invalid @enderror"
                                            id="nivel_atual" name="nivel_atual" required>
                                            <option value="">Selecione o nível</option>
                                            <option value="bronze" {{ old('nivel_atual')=='bronze' ? 'selected' : '' }}>
                                                <i class="fas fa-medal text-warning"></i> Bronze
                                            </option>
                                            <option value="prata" {{ old('nivel_atual')=='prata' ? 'selected' : '' }}>
                                                <i class="fas fa-medal text-secondary"></i> Prata
                                            </option>
                                            <option value="ouro" {{ old('nivel_atual')=='ouro' ? 'selected' : '' }}>
                                                <i class="fas fa-medal text-warning"></i> Ouro
                                            </option>
                                            <option value="diamond" {{ old('nivel_atual')=='diamond' ? 'selected' : ''
                                                }}>
                                                <i class="fas fa-gem text-info"></i> Diamond
                                            </option>
                                        </select>
                                        @error('nivel_atual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">
                                            <i class="fas fa-toggle-on"></i> Status *
                                        </label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="ativa" {{ old('status')=='ativa' ? 'selected' : '' }}>
                                                <i class="fas fa-check-circle text-success"></i> Ativa
                                            </option>
                                            <option value="bloqueada" {{ old('status')=='bloqueada' ? 'selected' : ''
                                                }}>
                                                <i class="fas fa-lock text-danger"></i> Bloqueada
                                            </option>
                                            <option value="suspensa" {{ old('status')=='suspensa' ? 'selected' : '' }}>
                                                <i class="fas fa-pause-circle text-warning"></i> Suspensa
                                            </option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Saldos Iniciais -->
                                <h6 class="text-muted mt-4 mb-3">
                                    <i class="fas fa-coins"></i> Saldos Iniciais (Opcional)
                                </h6>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="saldo_cashback" class="form-label">
                                            <i class="fas fa-money-bill-wave"></i> Saldo Cashback
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('saldo_cashback') is-invalid @enderror"
                                                id="saldo_cashback" name="saldo_cashback"
                                                value="{{ old('saldo_cashback', '0.00') }}" placeholder="0,00">
                                        </div>
                                        @error('saldo_cashback')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="saldo_pontos" class="form-label">
                                            <i class="fas fa-star"></i> Saldo Pontos
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">pts</span>
                                            <input type="number" step="1" min="0"
                                                class="form-control @error('saldo_pontos') is-invalid @enderror"
                                                id="saldo_pontos" name="saldo_pontos"
                                                value="{{ old('saldo_pontos', '0') }}" placeholder="0">
                                        </div>
                                        @error('saldo_pontos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="saldo_creditos" class="form-label">
                                            <i class="fas fa-gift"></i> Saldo Créditos
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('saldo_creditos') is-invalid @enderror"
                                                id="saldo_creditos" name="saldo_creditos"
                                                value="{{ old('saldo_creditos', '0.00') }}" placeholder="0,00">
                                        </div>
                                        @error('saldo_creditos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Observações -->
                                <div class="mb-4">
                                    <label for="observacoes" class="form-label">
                                        <i class="fas fa-sticky-note"></i> Observações
                                    </label>
                                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"
                                        placeholder="Observações sobre a carteira...">{{ old('observacoes') }}</textarea>
                                </div>

                                <!-- Botões -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('fidelidade.carteiras.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Criar Carteira
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Dicas -->
                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle"></i>
                                Dicas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb"></i> Níveis de Fidelidade</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Bronze:</strong> Nível inicial</li>
                                    <li><strong>Prata:</strong> A partir de 100 pontos</li>
                                    <li><strong>Ouro:</strong> A partir de 500 pontos</li>
                                    <li><strong>Diamond:</strong> A partir de 1000 pontos</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Importante</h6>
                                <ul class="mb-0 small">
                                    <li>Todos os campos marcados com * são obrigatórios</li>
                                    <li>Os saldos iniciais são opcionais</li>
                                    <li>A carteira será criada como "Ativa" por padrão</li>
                                </ul>
                            </div>

                            <div class="alert alert-success">
                                <h6><i class="fas fa-shield-alt"></i> Segurança</h6>
                                <ul class="mb-0 small">
                                    <li>Todas as alterações são registradas</li>
                                    <li>Histórico completo de transações</li>
                                    <li>Auditoria automática</li>
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
    // Auto-calcular saldo total disponível
    const saldoCashback = document.getElementById('saldo_cashback');
    const saldoCreditos = document.getElementById('saldo_creditos');
    
    function calcularSaldoTotal() {
        const cashback = parseFloat(saldoCashback.value) || 0;
        const creditos = parseFloat(saldoCreditos.value) || 0;
        const total = cashback + creditos;
        
        // Mostrar total calculado (se desejar)
        console.log('Saldo total disponível:', total.toFixed(2));
    }
    
    saldoCashback.addEventListener('input', calcularSaldoTotal);
    saldoCreditos.addEventListener('input', calcularSaldoTotal);
});
</script>
@endpush
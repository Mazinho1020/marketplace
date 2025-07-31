@extends('layouts.app')

@section('title', 'Editar Carteira de Fidelidade')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-primary"></i>
                        Editar Carteira de Fidelidade
                    </h1>
                    <p class="text-muted mb-0">Editar dados da carteira #{{ $carteira->id }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('fidelidade.carteiras.show', $carteira->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Carteira
                    </a>
                    <a href="{{ route('fidelidade.carteiras.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-edit"></i>
                                Editar Dados da Carteira
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fidelidade.carteiras.update', $carteira->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Cliente (readonly) -->
                                    <div class="col-md-6 mb-3">
                                        <label for="cliente_id" class="form-label">
                                            <i class="fas fa-user"></i> Cliente
                                        </label>
                                        <select class="form-select" id="cliente_id" name="cliente_id" disabled>
                                            <option value="{{ $carteira->cliente_id }}">
                                                Cliente ID: {{ $carteira->cliente_id }}
                                            </option>
                                        </select>
                                        <input type="hidden" name="cliente_id" value="{{ $carteira->cliente_id }}">
                                        <small class="text-muted">O cliente não pode ser alterado após a criação</small>
                                    </div>

                                    <!-- Empresa (readonly) -->
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_id" class="form-label">
                                            <i class="fas fa-building"></i> Empresa
                                        </label>
                                        <select class="form-select" id="empresa_id" name="empresa_id" disabled>
                                            <option value="{{ $carteira->empresa_id }}">
                                                Empresa ID: {{ $carteira->empresa_id }}
                                            </option>
                                        </select>
                                        <input type="hidden" name="empresa_id" value="{{ $carteira->empresa_id }}">
                                        <small class="text-muted">A empresa não pode ser alterada após a criação</small>
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
                                            <option value="bronze" {{ old('nivel_atual', $carteira->nivel_atual) ==
                                                'bronze' ? 'selected' : '' }}>
                                                <i class="fas fa-medal text-warning"></i> Bronze
                                            </option>
                                            <option value="prata" {{ old('nivel_atual', $carteira->nivel_atual) ==
                                                'prata' ? 'selected' : '' }}>
                                                <i class="fas fa-medal text-secondary"></i> Prata
                                            </option>
                                            <option value="ouro" {{ old('nivel_atual', $carteira->nivel_atual) == 'ouro'
                                                ? 'selected' : '' }}>
                                                <i class="fas fa-medal text-warning"></i> Ouro
                                            </option>
                                            <option value="diamond" {{ old('nivel_atual', $carteira->nivel_atual) ==
                                                'diamond' ? 'selected' : '' }}>
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
                                            <option value="ativa" {{ old('status', $carteira->status) == 'ativa' ?
                                                'selected' : '' }}>
                                                <i class="fas fa-check-circle text-success"></i> Ativa
                                            </option>
                                            <option value="bloqueada" {{ old('status', $carteira->status) == 'bloqueada'
                                                ? 'selected' : '' }}>
                                                <i class="fas fa-lock text-danger"></i> Bloqueada
                                            </option>
                                            <option value="suspensa" {{ old('status', $carteira->status) == 'suspensa' ?
                                                'selected' : '' }}>
                                                <i class="fas fa-pause-circle text-warning"></i> Suspensa
                                            </option>
                                            <option value="cancelada" {{ old('status', $carteira->status) == 'cancelada'
                                                ? 'selected' : '' }}>
                                                <i class="fas fa-times-circle text-danger"></i> Cancelada
                                            </option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Saldos Atuais (readonly) -->
                                <h6 class="text-muted mt-4 mb-3">
                                    <i class="fas fa-coins"></i> Saldos Atuais (Somente Leitura)
                                </h6>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="saldo_cashback_display" class="form-label">
                                            <i class="fas fa-money-bill-wave"></i> Saldo Cashback
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control"
                                                value="{{ number_format($carteira->saldo_cashback, 2, ',', '.') }}"
                                                readonly>
                                        </div>
                                        <small class="text-muted">Use a função "Ajustar Saldo" para modificar</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="saldo_pontos_display" class="form-label">
                                            <i class="fas fa-star"></i> XP Total
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">pts</span>
                                            <input type="text" class="form-control"
                                                value="{{ number_format($carteira->xp_total ?? 0, 0, ',', '.') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="saldo_creditos_display" class="form-label">
                                            <i class="fas fa-gift"></i> Saldo Créditos
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control"
                                                value="{{ number_format($carteira->saldo_creditos, 2, ',', '.') }}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informações da Conta -->
                                <h6 class="text-muted mt-4 mb-3">
                                    <i class="fas fa-info-circle"></i> Informações da Conta
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Data de Cadastro</label>
                                        <input type="text" class="form-control"
                                            value="{{ $carteira->data_cadastro ? $carteira->data_cadastro->format('d/m/Y H:i') : $carteira->created_at->format('d/m/Y H:i') }}"
                                            readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Última Transação</label>
                                        <input type="text" class="form-control"
                                            value="{{ $carteira->ultima_transacao ? $carteira->ultima_transacao->format('d/m/Y H:i') : 'Nenhuma' }}"
                                            readonly>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('fidelidade.carteiras.show', $carteira->id) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Ações Rápidas -->
                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-tools"></i>
                                Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Ações de Status -->
                            <div class="d-grid gap-2 mb-3">
                                @if($carteira->status === 'ativa')
                                <form action="{{ route('fidelidade.carteiras.bloquear', $carteira->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning w-100"
                                        onclick="return confirm('Deseja bloquear esta carteira?')">
                                        <i class="fas fa-lock"></i> Bloquear Carteira
                                    </button>
                                </form>
                                @elseif($carteira->status === 'bloqueada')
                                <form action="{{ route('fidelidade.carteiras.desbloquear', $carteira->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Deseja desbloquear esta carteira?')">
                                        <i class="fas fa-unlock"></i> Desbloquear Carteira
                                    </button>
                                </form>
                                @endif
                            </div>

                            <!-- Ações de Saldo -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#ajustarSaldoModal">
                                    <i class="fas fa-coins"></i> Ajustar Saldo
                                </button>
                            </div>

                            <!-- Ações de Dados -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('fidelidade.carteiras.show', $carteira->id) }}"
                                    class="btn btn-info w-100">
                                    <i class="fas fa-chart-line"></i> Ver Relatórios
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Card de Resumo -->
                    <div class="card shadow mt-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-pie"></i>
                                Resumo da Carteira
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">Saldo Total</small>
                                        <h5 class="text-success mb-0">
                                            R$ {{ number_format($carteira->saldo_total_disponivel, 2, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">Status Atual</small>
                                        <h6 class="mb-0">
                                            @if($carteira->status === 'ativa')
                                            <span class="badge bg-success">Ativa</span>
                                            @elseif($carteira->status === 'bloqueada')
                                            <span class="badge bg-danger">Bloqueada</span>
                                            @elseif($carteira->status === 'suspensa')
                                            <span class="badge bg-warning">Suspensa</span>
                                            @else
                                            <span class="badge bg-secondary">{{ ucfirst($carteira->status) }}</span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">Nível</small>
                                        <h6 class="mb-0">
                                            @if($carteira->nivel_atual === 'bronze')
                                            <span class="badge bg-warning">🥉 Bronze</span>
                                            @elseif($carteira->nivel_atual === 'prata')
                                            <span class="badge bg-secondary">🥈 Prata</span>
                                            @elseif($carteira->nivel_atual === 'ouro')
                                            <span class="badge bg-warning">🥇 Ouro</span>
                                            @elseif($carteira->nivel_atual === 'diamond')
                                            <span class="badge bg-info">💎 Diamond</span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ajustar Saldo -->
<div class="modal fade" id="ajustarSaldoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('fidelidade.carteiras.ajustar-saldo', $carteira->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-coins"></i> Ajustar Saldo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipo_ajuste" class="form-label">Tipo de Saldo</label>
                        <select class="form-select" id="tipo_ajuste" name="tipo_ajuste" required>
                            <option value="cashback">Cashback</option>
                            <option value="credito">Crédito</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="operacao" class="form-label">Operação</label>
                        <select class="form-select" id="operacao" name="operacao" required>
                            <option value="adicionar">Adicionar</option>
                            <option value="remover">Remover</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="valor" name="valor"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required
                            placeholder="Descreva o motivo do ajuste..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Aplicar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
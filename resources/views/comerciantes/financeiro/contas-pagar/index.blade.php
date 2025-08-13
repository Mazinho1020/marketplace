@extends('comerciantes.layouts.app')

@section('title', 'Contas a Pagar')

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
            <li class="breadcrumb-item active" aria-current="page">Contas a Pagar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Contas a Pagar</h1>
        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.create', $empresa) }}" 
           class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Conta a Pagar
        </a>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total em Aberto
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($estatisticas['total_aberto'] ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Vencendo Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($estatisticas['vencendo_hoje'] ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Em Atraso
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($estatisticas['em_atraso'] ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-times fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pago Este Mês
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($estatisticas['total_pago'] ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="situacao" class="form-label">Situação</label>
                        <select name="situacao_financeira" id="situacao_financeira" class="form-control">
                            <option value="">Todas</option>
                            <option value="pendente" {{ request('situacao') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="pago" {{ request('situacao') == 'pago' ? 'selected' : '' }}>Pago</option>
                            <option value="cancelado" {{ request('situacao') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            <option value="vencido" {{ request('situacao') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" 
                               value="{{ request('data_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control" 
                               value="{{ request('data_fim') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Descrição, pessoa..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Data Vencimento</th>
                            <th>Descrição</th>
                            <th>Pessoa</th>
                            <th>Valor</th>
                            <th>Situação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contasPagar as $conta)
                        <tr class="{{ $conta->situacao_financeira->value == 'vencido' ? 'table-danger' : '' }}">
                            <td>
                                {{ $conta->data_vencimento->format('d/m/Y') }}
                                @if($conta->data_vencimento->isPast() && $conta->situacao_financeira->value == 'pendente')
                                    <span class="badge badge-danger ml-1">Vencido</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $conta->descricao }}</strong>
                                @if($conta->observacoes)
                                    <br><small class="text-muted">{{ Str::limit($conta->observacoes, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($conta->pessoa)
                                    {{ $conta->pessoa->nome }}
                                    <br><small class="text-muted">{{ $conta->pessoa->tipo_pessoa }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <strong>R$ {{ number_format($conta->valor_original, 2, ',', '.') }}</strong>
                                @if($conta->valor_pago > 0)
                                    <br><small class="text-success">
                                        Pago: R$ {{ number_format($conta->valor_pago, 2, ',', '.') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($conta->situacao_financeira->value) {
                                        'pendente' => 'warning',
                                        'pago' => 'success',
                                        'cancelado' => 'secondary',
                                        'vencido' => 'danger',
                                        default => 'info'
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">
                                    {{ $conta->situacao_financeira->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
                                       class="btn btn-outline-info" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($conta->situacao_financeira->value == 'pendente')
                                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.edit', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="abrirModalPagamento({{ $conta->id }})" title="Pagar">
                                            <i class="fas fa-dollar-sign"></i>
                                        </button>
                                    @endif
                                    
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="excluirConta({{ $conta->id }})" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <br>Nenhuma conta a pagar encontrada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($contasPagar->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $contasPagar->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div class="modal fade" id="modalPagamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPagamento" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="valor_pago" class="form-label">Valor Pago</label>
                        <input type="number" name="valor_pago" id="valor_pago" class="form-control" 
                               step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_pagamento" class="form-label">Data do Pagamento</label>
                        <input type="date" name="data_pagamento" id="data_pagamento" class="form-control" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes_pagamento" class="form-label">Observações</label>
                        <textarea name="observacoes_pagamento" id="observacoes_pagamento" 
                                  class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-danger { border-left: 4px solid #e74c3c !important; }
.border-left-warning { border-left: 4px solid #f39c12 !important; }
.border-left-success { border-left: 4px solid #27ae60 !important; }
.border-left-info { border-left: 4px solid #3498db !important; }

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}

.text-xs {
    font-size: 0.7rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.075);
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush

@push('scripts')
<script>
function abrirModalPagamento(contaId) {
    const form = document.getElementById('formPagamento');
    const action = '{{ route("comerciantes.empresas.financeiro.contas-pagar.pagar", ["empresa" => $empresa, "id" => "__ID__"]) }}';
    form.action = action.replace('__ID__', contaId);
    
    new bootstrap.Modal(document.getElementById('modalPagamento')).show();
}

function excluirConta(contaId) {
    if (confirm('Tem certeza que deseja excluir esta conta?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("comerciantes.empresas.financeiro.contas-pagar.destroy", ["empresa" => $empresa, "id" => "__ID__"]) }}'.replace('__ID__', contaId);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush









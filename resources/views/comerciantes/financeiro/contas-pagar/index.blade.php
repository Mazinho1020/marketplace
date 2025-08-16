@extends('layouts.comerciante')

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
                                <strong>R$ {{ number_format($conta->valor_liquido, 2, ',', '.') }}</strong>
                                @if($conta->pagamentos()->where("status_pagamento", "confirmado")->sum("valor") > 0)
                                    <br><small class="text-success">
                                        Pago: R$ {{ number_format($conta->pagamentos()->where("status_pagamento", "confirmado")->sum("valor"), 2, ',', '.') }}
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

<!-- Modal de Exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1" aria-labelledby="modalExclusaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExclusaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta conta a pagar?</p>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function excluirConta(contaId) {
    const form = document.getElementById('formExclusao');
    form.action = '{{ route("comerciantes.empresas.financeiro.contas-pagar.destroy", ["empresa" => $empresa, "id" => "__ID__"]) }}'.replace('__ID__', contaId);
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
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
// URLs da API para o contexto de comerciantes
const apiBaseUrl = '/comerciantes/empresas/{{ $empresa->id }}/financeiro';

// Carregar formas de pagamento ao abrir o modal
function carregarFormasPagamento() {
    console.log('📋 Carregando formas de pagamento...');
    const select = document.getElementById('forma_pagamento_id');
    select.innerHTML = '<option value="">Carregando...</option>';

    const url = `${apiBaseUrl}/api/formas-pagamento-saida`;
    console.log('🌐 URL da requisição:', url);

    fetch(url)
        .then(response => {
            console.log('📡 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Dados recebidos:', data);
            select.innerHTML = '<option value="">Selecione uma forma de pagamento</option>';
            
            if (data.length === 0) {
                select.innerHTML = '<option value="">Nenhuma forma de pagamento disponível</option>';
                return;
            }

            data.forEach(forma => {
                const option = document.createElement('option');
                option.value = forma.id;
                option.textContent = forma.nome;
                option.dataset.isGateway = forma.is_gateway ? '1' : '0';
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('❌ Erro ao carregar formas de pagamento:', error);
            select.innerHTML = '<option value="">Erro ao carregar formas de pagamento</option>';
        });
}

// Carregar bandeiras baseado na forma de pagamento selecionada
function carregarBandeiras(formaId) {
    const bandeiraSelect = document.getElementById('bandeira_id');
    const bandeiraContainer = document.getElementById('bandeiraContainer');

    if (!formaId) {
        bandeiraContainer.style.display = 'none';
        bandeiraSelect.innerHTML = '<option value="">Selecione uma bandeira</option>';
        return;
    }

    // Sempre tentar carregar bandeiras quando uma forma de pagamento for selecionada
    bandeiraContainer.style.display = 'block';
    bandeiraSelect.innerHTML = '<option value="">Carregando bandeiras...</option>';

    fetch(`${apiBaseUrl}/api/formas-pagamento/${formaId}/bandeiras`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            bandeiraSelect.innerHTML = '<option value="">Selecione uma bandeira</option>';
            
            if (data.length === 0) {
                // Se não há bandeiras, oculta o container
                bandeiraContainer.style.display = 'none';
                bandeiraSelect.innerHTML = '<option value="">Nenhuma bandeira disponível</option>';
                return;
            }

            data.forEach(bandeira => {
                const option = document.createElement('option');
                option.value = bandeira.id;
                option.textContent = bandeira.nome;
                bandeiraSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar bandeiras:', error);
            bandeiraSelect.innerHTML = '<option value="">Erro ao carregar bandeiras</option>';
            // Em caso de erro, manter visível para debug
        });
}

// Event listener para mudança na forma de pagamento
document.getElementById('forma_pagamento_id').addEventListener('change', function() {
    carregarBandeiras(this.value);
});

function abrirModalPagamento(contaId) {
    console.log('🚀 Abrindo modal de pagamento para conta:', contaId);
    
    const form = document.getElementById('formPagamento');
    const action = '{{ route("comerciantes.empresas.financeiro.contas-pagar.pagar", ["empresa" => $empresa, "id" => "__ID__"]) }}';
    form.action = action.replace('__ID__', contaId);
    
    console.log('🔗 URL da API base:', apiBaseUrl);
    
    // Carregar formas de pagamento quando o modal for aberto
    carregarFormasPagamento();
    
    // Limpar campos do formulário
    document.getElementById('valor_pago').value = '';
    document.getElementById('data_pagamento').value = '{{ date('Y-m-d') }}';
    document.getElementById('observacoes_pagamento').value = '';
    document.getElementById('forma_pagamento_id').value = '';
    document.getElementById('bandeira_id').value = '';
    document.getElementById('bandeiraContainer').style.display = 'none';
    
    new bootstrap.Modal(document.getElementById('modalPagamento')).show();
}

function excluirConta(contaId) {
    if (confirm('Tem certeza que deseja excluir esta conta?\n\nEsta ação não pode ser desfeita.')) {
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

// Validação do formulário antes do envio
document.getElementById('formPagamento').addEventListener('submit', function(e) {
    const valorPago = document.getElementById('valor_pago').value;
    const formaPagamento = document.getElementById('forma_pagamento_id').value;
    
    if (!valorPago || parseFloat(valorPago) <= 0) {
        e.preventDefault();
        alert('Por favor, informe um valor válido para o pagamento.');
        return;
    }
    
    if (!formaPagamento) {
        e.preventDefault();
        alert('Por favor, selecione uma forma de pagamento.');
        return;
    }
    
    // Confirmar antes de enviar
    if (!confirm('Confirma o registro deste pagamento?\n\nValor: R$ ' + parseFloat(valorPago).toFixed(2).replace('.', ','))) {
        e.preventDefault();
    }
});
</script>
@endpush









@extends('financial.layout')

@section('financial-title', 'Contas a Pagar')

@section('financial-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-hand-holding-usd text-danger"></i>
                        Contas a Pagar
                    </h5>
                    <div>
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.create', $empresa) }}" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nova Conta a Pagar
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="busca" class="form-control" 
                                   placeholder="Buscar por descrição..." 
                                   value="{{ request('busca') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="situacao" class="form-control">
                                <option value="">Todas as Situações</option>
                                @foreach(\App\Enums\SituacaoFinanceiraEnum::cases() as $situacao)
                                    <option value="{{ $situacao->value }}" 
                                            {{ request('situacao') == $situacao->value ? 'selected' : '' }}>
                                        {{ $situacao->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="data_inicio" class="form-control" 
                                   value="{{ request('data_inicio') }}" 
                                   placeholder="Data Início">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="data_fim" class="form-control" 
                                   value="{{ request('data_fim') }}" 
                                   placeholder="Data Fim">
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Resumo -->
                @if(isset($estatisticas))
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Total Pendente</h6>
                                <h4 class="text-warning">R$ {{ number_format($estatisticas['total_pendente'], 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Total Pago</h6>
                                <h4 class="text-success">R$ {{ number_format($estatisticas['total_pago'], 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Vencidas</h6>
                                <h4 class="text-danger">{{ $estatisticas['vencidas'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Este Mês</h6>
                                <h4 class="text-info">R$ {{ number_format($estatisticas['este_mes'], 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tabela -->
                @if($contasPagar->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Descrição</th>
                                <th>Fornecedor/Cliente</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Situação</th>
                                <th>Parcela</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contasPagar as $lancamento)
                            <tr class="{{ $lancamento->isVencido() ? 'table-warning' : '' }}">
                                <td>
                                    <strong>{{ $lancamento->descricao }}</strong>
                                    @if($lancamento->observacoes)
                                        <br><small class="text-muted">{{ Str::limit($lancamento->observacoes, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($lancamento->pessoa)
                                        <i class="fas fa-user text-info"></i>
                                        {{ $lancamento->pessoa->nome }}
                                    @elseif($lancamento->funcionario)
                                        <i class="fas fa-user-tie text-primary"></i>
                                        {{ $lancamento->funcionario->nome }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-danger">
                                        <strong>R$ {{ number_format($lancamento->valor_total, 2, ',', '.') }}</strong>
                                    </span>
                                    @if($lancamento->valor_pago > 0)
                                        <br><small class="text-success">
                                            Pago: R$ {{ number_format($lancamento->valor_pago, 2, ',', '.') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($lancamento->data_vencimento)->format('d/m/Y') }}
                                    @if($lancamento->isVencido())
                                        <br><small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Vencida há {{ $lancamento->diasParaVencimento() * -1 }} dias
                                        </small>
                                    @elseif($lancamento->diasParaVencimento() <= 5 && $lancamento->diasParaVencimento() > 0)
                                        <br><small class="text-warning">
                                            <i class="fas fa-clock"></i>
                                            Vence em {{ $lancamento->diasParaVencimento() }} dias
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $lancamento->situacao->color() }}">
                                        <i class="{{ $lancamento->situacao->icon() }}"></i>
                                        {{ $lancamento->situacao->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if($lancamento->numero_parcelas > 1)
                                        <span class="badge badge-info">
                                            {{ $lancamento->parcela_atual }}/{{ $lancamento->numero_parcelas }}
                                        </span>
                                    @else
                                        <span class="text-muted">À vista</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm">
                                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.show', [$empresa, $lancamento]) }}" 
                                           class="btn btn-info btn-sm-action" 
                                           data-toggle="tooltip" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($lancamento->situacao->value !== 'PAGO')
                                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.edit', [$empresa, $lancamento]) }}" 
                                           class="btn btn-warning btn-sm-action" 
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-success btn-sm-action" 
                                                onclick="marcarComoPago({{ $lancamento->id }}, 'PAGAR')"
                                                data-toggle="tooltip" title="Marcar como Pago">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        
                                        <form method="POST" 
                                              action="{{ route('comerciantes.empresas.financeiro.contas-pagar.destroy', [$empresa, $lancamento]) }}" 
                                              style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm-action" 
                                                    onclick="confirmarExclusao(this)"
                                                    data-toggle="tooltip" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center">
                    {{ $contasPagar->appends(request()->all())->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma conta a pagar encontrada</h5>
                    <p class="text-muted">Comece criando sua primeira conta a pagar.</p>
                    <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.create', $empresa) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus"></i> Criar Primeira Conta a Pagar
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Atualizar URLs no JavaScript para usar as rotas corretas
function marcarComoPago(lancamentoId, natureza) {
    const url = `{{ url('/') }}/comerciantes/empresas/{{ $empresa->id }}/financeiro/contas-pagar/${lancamentoId}/pagar`;
    
    if (confirm('Confirma o pagamento deste lançamento?')) {
        // Criar e submeter form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@section('title', 'Contas a Pagar')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-money-bill-wave text-danger me-2"></i>
                        Contas a Pagar
                    </h2>
                    <p class="text-muted mb-0">Gerencie todas as suas contas a pagar</p>
                </div>
                <div>
                    <a href="{{ route('financial.contas-pagar.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nova Conta a Pagar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card financial-card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filtros
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('financial.contas-pagar.index') }}" id="filtrosForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="situacao" class="form-select">
                                    <option value="">Todos os Status</option>
                                    @foreach(\App\Enums\SituacaoFinanceiraEnum::cases() as $situacao)
                                        <option value="{{ $situacao->value }}" 
                                                {{ request('situacao') == $situacao->value ? 'selected' : '' }}>
                                            {{ $situacao->getLabel() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Fornecedor</label>
                                <select name="pessoa_id" class="form-select">
                                    <option value="">Todos os Fornecedores</option>
                                    @foreach($fornecedores as $fornecedor)
                                        <option value="{{ $fornecedor->id }}" 
                                                {{ request('pessoa_id') == $fornecedor->id ? 'selected' : '' }}>
                                            {{ $fornecedor->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Data Início</label>
                                <input type="date" name="data_inicio" class="form-control" 
                                       value="{{ request('data_inicio') }}">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Data Fim</label>
                                <input type="date" name="data_fim" class="form-control" 
                                       value="{{ request('data_fim') }}">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('financial.contas-pagar.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card financial-card border-warning">
                <div class="card-body text-center">
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                    </div>
                    <h5 class="card-title">Pendentes</h5>
                    <h4 class="text-warning">R$ {{ number_format($resumo['pendentes'], 2, ',', '.') }}</h4>
                    <small class="text-muted">{{ $resumo['qtd_pendentes'] }} conta(s)</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card financial-card border-danger">
                <div class="card-body text-center">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    </div>
                    <h5 class="card-title">Vencidas</h5>
                    <h4 class="text-danger">R$ {{ number_format($resumo['vencidas'], 2, ',', '.') }}</h4>
                    <small class="text-muted">{{ $resumo['qtd_vencidas'] }} conta(s)</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card financial-card border-success">
                <div class="card-body text-center">
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                    </div>
                    <h5 class="card-title">Pagas</h5>
                    <h4 class="text-success">R$ {{ number_format($resumo['pagas'], 2, ',', '.') }}</h4>
                    <small class="text-muted">{{ $resumo['qtd_pagas'] }} conta(s)</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card financial-card border-primary">
                <div class="card-body text-center">
                    <div class="text-primary">
                        <i class="fas fa-calculator fa-2x mb-2"></i>
                    </div>
                    <h5 class="card-title">Total</h5>
                    <h4 class="text-primary">R$ {{ number_format($resumo['total'], 2, ',', '.') }}</h4>
                    <small class="text-muted">{{ $contasPagar->total() }} conta(s)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Contas -->
    <div class="row">
        <div class="col-12">
            <div class="card financial-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Lista de Contas a Pagar
                    </h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportarExcel()">
                            <i class="fas fa-file-excel me-1"></i>
                            Excel
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="exportarPDF()">
                            <i class="fas fa-file-pdf me-1"></i>
                            PDF
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="contasPagarTable">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Descrição</th>
                                    <th>Fornecedor</th>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Categoria</th>
                                    <th style="width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contasPagar as $conta)
                                    <tr class="{{ $conta->isVencida() ? 'table-danger' : '' }}">
                                        <td>{{ $conta->id }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $conta->descricao }}</div>
                                            @if($conta->observacoes)
                                                <small class="text-muted">{{ Str::limit($conta->observacoes, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($conta->pessoa)
                                                <div class="fw-bold">{{ $conta->pessoa->nome }}</div>
                                                <small class="text-muted">
                                                    {{ $conta->pessoa->documento ?? 'Sem documento' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Não informado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $conta->data_vencimento->format('d/m/Y') }}</div>
                                            @if($conta->isVencida())
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    {{ $conta->diasParaVencimento() }} dias em atraso
                                                </small>
                                            @elseif($conta->diasParaVencimento() <= 7 && $conta->diasParaVencimento() > 0)
                                                <small class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Vence em {{ $conta->diasParaVencimento() }} dias
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold valor-negativo">
                                                R$ {{ number_format($conta->valor_original, 2, ',', '.') }}
                                            </div>
                                            @if($conta->valor_final != $conta->valor_original)
                                                <small class="text-muted">
                                                    Final: R$ {{ number_format($conta->valor_final, 2, ',', '.') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge" style="background-color: {{ $conta->situacao->getColor() }}; color: white;">
                                                <i class="{{ $conta->situacao->getIcon() }} me-1"></i>
                                                {{ $conta->situacao->getLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($conta->categoria)
                                                <span class="badge bg-secondary">{{ $conta->categoria }}</span>
                                            @else
                                                <span class="text-muted">Sem categoria</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('financial.contas-pagar.show', $conta->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($conta->situacao === \App\Enums\SituacaoFinanceiraEnum::PENDENTE)
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            title="Pagar" onclick="abrirModalPagamento({{ $conta->id }})">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </button>
                                                @endif
                                                
                                                <a href="{{ route('financial.contas-pagar.edit', $conta->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="Excluir" onclick="excluirConta({{ $conta->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <h5>Nenhuma conta a pagar encontrada</h5>
                                                <p>Clique no botão "Nova Conta a Pagar" para começar.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($contasPagar->hasPages())
                    <div class="card-footer">
                        {{ $contasPagar->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div class="modal fade" id="modalPagamento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-dollar-sign me-2"></i>
                    Efetuar Pagamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPagamento" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div id="dadosConta"></div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Data do Pagamento *</label>
                            <input type="date" name="data_pagamento" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Valor Pago *</label>
                            <input type="text" name="valor_pago" class="form-control money" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Conta Gerencial</label>
                            <select name="conta_gerencial_id" class="form-select">
                                <option value="">Selecione uma conta</option>
                                @foreach($contasGerenciais as $contaGerencial)
                                    <option value="{{ $contaGerencial->id }}">{{ $contaGerencial->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Forma de Pagamento</label>
                            <select name="forma_pagamento" class="form-select">
                                <option value="">Selecione a forma</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="pix">PIX</option>
                                <option value="cartao_debito">Cartão de Débito</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                                <option value="transferencia">Transferência</option>
                                <option value="boleto">Boleto</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Observações do Pagamento</label>
                            <textarea name="observacoes_pagamento" class="form-control" rows="3" 
                                      placeholder="Informações adicionais sobre o pagamento..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>
                        Confirmar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable
    $('#contasPagarTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        order: [[3, 'asc']], // Ordenar por vencimento
        pageLength: 25,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [7] } // Desabilitar ordenação na coluna de ações
        ]
    });
    
    // Máscara para valores monetários no modal
    $(document).on('focus', '.money', function() {
        $(this).mask('#.##0,00', {reverse: true});
    });
});

function abrirModalPagamento(contaId) {
    // Fazer requisição AJAX para buscar dados da conta
    $.get(`/financial/contas-pagar/${contaId}`, function(response) {
        if (response.success) {
            const conta = response.data;
            
            // Preencher dados da conta no modal
            $('#dadosConta').html(`
                <div class="alert alert-info">
                    <h6><strong>Conta:</strong> ${conta.descricao}</h6>
                    <p class="mb-2"><strong>Fornecedor:</strong> ${conta.pessoa ? conta.pessoa.nome : 'Não informado'}</p>
                    <p class="mb-2"><strong>Valor Original:</strong> R$ ${conta.valor_original_formatado}</p>
                    <p class="mb-0"><strong>Vencimento:</strong> ${conta.data_vencimento_formatada}</p>
                </div>
            `);
            
            // Preencher valor sugerido
            $('input[name="valor_pago"]').val(conta.valor_final_formatado);
            
            // Definir action do formulário
            $('#formPagamento').attr('action', `/financial/contas-pagar/${contaId}/pagar`);
            
            // Mostrar modal
            $('#modalPagamento').modal('show');
        }
    }).fail(function() {
        toastr.error('Erro ao carregar dados da conta');
    });
}

function excluirConta(contaId) {
    confirmarAcao('Tem certeza que deseja excluir esta conta a pagar?', function() {
        $.ajax({
            url: `/financial/contas-pagar/${contaId}`,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Erro ao excluir conta');
            }
        });
    });
}

function exportarExcel() {
    const filtros = $('#filtrosForm').serialize();
    window.open(`{{ route('financial.contas-pagar.export.excel') }}?${filtros}`, '_blank');
}

function exportarPDF() {
    const filtros = $('#filtrosForm').serialize();
    window.open(`{{ route('financial.contas-pagar.export.pdf') }}?${filtros}`, '_blank');
}

// Submissão do formulário de pagamento
$('#formPagamento').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    const actionUrl = $(this).attr('action');
    
    $.ajax({
        url: actionUrl,
        type: 'PATCH',
        data: formData,
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#modalPagamento').modal('hide');
                location.reload();
            } else {
                toastr.error(response.message);
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
                toastr.error('Erro ao processar pagamento');
            }
        }
    });
});
</script>
@endsection

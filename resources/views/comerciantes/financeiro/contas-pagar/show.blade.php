@extends('comerciantes.layouts.app')

@section('title', 'Detalhes da Conta a Pagar')

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
                <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}">Contas a Pagar</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $contaPagar->descricao }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $contaPagar->descricao }}</h1>
        <div class="btn-group">
            @if($contaPagar->situacao_financeira->value == 'pendente')
                <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.edit', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" 
                   class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button type="button" class="btn btn-success" onclick="abrirModalPagamento()">
                    <i class="fas fa-dollar-sign"></i> Pagar
                </button>
            @endif
            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações Principais -->
        <div class="col-md-8">
            <!-- Status e Situação -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $contaPagar->descricao }}</h4>
                            @if($contaPagar->documento_numero)
                                <p class="text-muted mb-0">Documento: {{ $contaPagar->documento_numero }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            @php
                                $badgeClass = match($contaPagar->situacao_financeira->value) {
                                    'pendente' => 'warning',
                                    'pago' => 'success',
                                    'cancelado' => 'secondary',
                                    'vencido' => 'danger',
                                    default => 'info'
                                };
                            @endphp
                            <span class="badge badge-{{ $badgeClass }} fs-6">
                                {{ $contaPagar->situacao_financeira->label() }}
                            </span>
                            
                            @if($contaPagar->data_vencimento->isPast() && $contaPagar->situacao_financeira->value == 'pendente')
                                <br><span class="badge badge-danger mt-1">Em Atraso</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações Gerais -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informações Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Descrição:</dt>
                                <dd class="col-sm-7">{{ $contaPagar->descricao }}</dd>

                                @if($contaPagar->pessoa)
                                <dt class="col-sm-5">Fornecedor:</dt>
                                <dd class="col-sm-7">
                                    {{ $contaPagar->pessoa->nome }}
                                    <br><small class="text-muted">{{ $contaPagar->pessoa->tipo_pessoa }}</small>
                                </dd>
                                @endif

                                @if($contaPagar->categoria)
                                <dt class="col-sm-5">Categoria:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-info">{{ $contaPagar->categoria->nome }}</span>
                                </dd>
                                @endif

                                <dt class="col-sm-5">Natureza:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-primary">{{ $contaPagar->natureza_financeira->label() }}</span>
                                </dd>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Data Emissão:</dt>
                                <dd class="col-sm-7">
                                    {{ $contaPagar->data_emissao ? $contaPagar->data_emissao->format('d/m/Y') : 'N/A' }}
                                </dd>

                                <dt class="col-sm-5">Data Vencimento:</dt>
                                <dd class="col-sm-7">
                                    {{ $contaPagar->data_vencimento->format('d/m/Y') }}
                                    @if($contaPagar->data_vencimento->isPast() && $contaPagar->situacao_financeira->value == 'pendente')
                                        <br><small class="text-danger">
                                            Venceu há {{ $contaPagar->data_vencimento->diffForHumans() }}
                                        </small>
                                    @endif
                                </dd>

                                @if($contaPagar->centro_custo)
                                <dt class="col-sm-5">Centro de Custo:</dt>
                                <dd class="col-sm-7">{{ $contaPagar->centro_custo->nome }}</dd>
                                @endif

                                @if($contaPagar->numero_parcelas > 1)
                                <dt class="col-sm-5">Parcela:</dt>
                                <dd class="col-sm-7">
                                    {{ $contaPagar->parcela_atual }}/{{ $contaPagar->numero_parcelas }}
                                </dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($contaPagar->observacoes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Observações:</strong>
                            <p class="mt-2">{{ $contaPagar->observacoes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Valores -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dollar-sign"></i> Valores
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Valor Original:</strong></td>
                                        <td class="text-end">R$ {{ number_format($contaPagar->valor_original, 2, ',', '.') }}</td>
                                    </tr>
                                    @if($contaPagar->desconto > 0)
                                    <tr class="text-success">
                                        <td>Desconto:</td>
                                        <td class="text-end">- R$ {{ number_format($contaPagar->desconto, 2, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($contaPagar->juros > 0)
                                    <tr class="text-warning">
                                        <td>Juros:</td>
                                        <td class="text-end">+ R$ {{ number_format($contaPagar->juros, 2, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($contaPagar->multa > 0)
                                    <tr class="text-danger">
                                        <td>Multa:</td>
                                        <td class="text-end">+ R$ {{ number_format($contaPagar->multa, 2, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="fw-bold border-top">
                                        <td><strong>Total a Pagar:</strong></td>
                                        <td class="text-end">
                                            <strong>R$ {{ number_format($contaPagar->valor_total, 2, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    @if($contaPagar->valor_pago > 0)
                                    <tr class="text-success">
                                        <td><strong>Valor Pago:</strong></td>
                                        <td class="text-end">R$ {{ number_format($contaPagar->valor_pago, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Saldo Restante:</strong></td>
                                        <td class="text-end">
                                            R$ {{ number_format($contaPagar->valor_total - $contaPagar->valor_pago, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @if($contaPagar->data_pagamento)
                                    <tr>
                                        <td><strong>Data Pagamento:</strong></td>
                                        <td class="text-end">{{ $contaPagar->data_pagamento->format('d/m/Y') }}</td>
                                    </tr>
                                    @endif
                                    @else
                                    <tr>
                                        <td colspan="2" class="text-muted text-center">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Nenhum pagamento registrado
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Pagamentos -->
            @if($contaPagar->valor_pago > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Histórico de Pagamentos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $contaPagar->data_pagamento ? $contaPagar->data_pagamento->format('d/m/Y') : 'N/A' }}</td>
                                    <td>R$ {{ number_format($contaPagar->valor_pago, 2, ',', '.') }}</td>
                                    <td>{{ $contaPagar->observacoes_pagamento ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Ações Rápidas -->
            @if($contaPagar->situacao_financeira->value == 'pendente')
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="abrirModalPagamento()">
                            <i class="fas fa-dollar-sign"></i> Registrar Pagamento
                        </button>
                        
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.edit', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Conta
                        </a>

                        <hr class="my-3">

                        <button class="btn btn-outline-danger" 
                                onclick="if(confirm('Tem certeza que deseja excluir esta conta?')) { document.getElementById('delete-form').submit(); }">
                            <i class="fas fa-trash"></i> Excluir Conta
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informações Técnicas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Informações Técnicas
                    </h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <strong>ID:</strong> {{ $contaPagar->id }}
                        </div>
                        <div class="mb-2">
                            <strong>Criado em:</strong> {{ $contaPagar->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="mb-2">
                            <strong>Atualizado em:</strong> {{ $contaPagar->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @if($contaPagar->e_recorrente)
                        <div class="mb-2">
                            <span class="badge badge-info">Recorrente</span>
                        </div>
                        @endif
                    </small>
                </div>
            </div>

            <!-- Alertas -->
            @if($contaPagar->data_vencimento->isPast() && $contaPagar->situacao_financeira->value == 'pendente')
            <div class="card border-danger mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Atenção
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        Esta conta está <strong>{{ $contaPagar->data_vencimento->diffForHumans() }}</strong> 
                        e precisa ser paga o quanto antes.
                    </p>
                </div>
            </div>
            @endif

            @if($contaPagar->data_vencimento->isToday() && $contaPagar->situacao_financeira->value == 'pendente')
            <div class="card border-warning mb-4">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock"></i> Vence Hoje
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        Esta conta vence hoje. Não se esqueça de fazer o pagamento.
                    </p>
                </div>
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
            <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-pagar.pagar', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="valor_pago" class="form-label">Valor a Pagar</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" name="valor_pago" id="valor_pago" class="form-control" 
                                   step="0.01" value="{{ $contaPagar->valor_total - $contaPagar->valor_pago }}" required>
                        </div>
                        <small class="form-text text-muted">
                            Saldo restante: R$ {{ number_format($contaPagar->valor_total - $contaPagar->valor_pago, 2, ',', '.') }}
                        </small>
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

<!-- Form para deletar -->
<form id="delete-form" 
      action="{{ route('comerciantes.empresas.financeiro.contas-pagar.destroy', ['empresa' => $empresa, 'id' => $contaPagar->id]) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
.border-left-danger { border-left: 4px solid #e74c3c !important; }
.border-left-warning { border-left: 4px solid #f39c12 !important; }
.border-left-success { border-left: 4px solid #27ae60 !important; }

.fs-6 { font-size: 1rem !important; }

dl.row {
    margin-bottom: 0;
}

dl.row dt {
    font-weight: 600;
}

dl.row dd {
    margin-bottom: 0.5rem;
}

.fw-bold {
    font-weight: bold !important;
}
</style>
@endpush

@push('scripts')
<script>
function abrirModalPagamento() {
    new bootstrap.Modal(document.getElementById('modalPagamento')).show();
}
</script>
@endpush









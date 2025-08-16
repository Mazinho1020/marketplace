@extends('layouts.comerciante')

@section('title', 'Detalhes da Conta a Pagar')

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
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard', $empresa) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}">Contas a Pagar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Detalhes da Conta a Pagar</h1>
        </div>
        <div>
            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.edit', ['empresa' => $empresa, 'id' => $lancamento->id]) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações da Conta -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Informações da Conta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>Descrição:</strong><br>{{ $lancamento->descricao }}</p>
                            <p><strong>Número do Documento:</strong><br>{{ $lancamento->numero_documento ?? 'N/A' }}</p>
                            <p><strong>Data de Emissão:</strong><br>{{ $lancamento->data_emissao ? $lancamento->data_emissao->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Data de Vencimento:</strong><br>
                                <span class="@if($lancamento->data_vencimento && $lancamento->data_vencimento->isPast() && $lancamento->situacao_financeira->value !== 'pago') text-danger @endif">
                                    {{ $lancamento->data_vencimento ? $lancamento->data_vencimento->format('d/m/Y') : 'N/A' }}
                                </span>
                            </p>
                            <p><strong>Situação:</strong><br>
                                @if($lancamento->situacao_financeira->value === 'pago')
                                    <span class="badge bg-success">Quitado</span>
                                @elseif($lancamento->situacao_financeira->value === 'parcialmente_pago')
                                    <span class="badge bg-warning">Parcialmente Pago</span>
                                @else
                                    <span class="badge bg-danger">Pendente</span>
                                @endif
                            </p>
                            @if($lancamento->observacoes)
                                <p><strong>Observações:</strong><br>{{ $lancamento->observacoes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo Financeiro -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Resumo Financeiro
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
                        $saldoDevedor = $lancamento->valor_liquido - $valorPago;
                        $percentualPago = $lancamento->valor_liquido > 0 ? ($valorPago / $lancamento->valor_liquido) * 100 : 0;
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="text-muted mb-1">Valor Total</h6>
                                <h4 class="mb-0">R$ {{ number_format($lancamento->valor_liquido, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="text-muted mb-1">Valor Pago</h6>
                                <h4 class="mb-0 text-success">R$ {{ number_format($valorPago, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <h6 class="text-muted mb-1">Saldo Devedor</h6>
                            <h4 class="mb-0 {{ $saldoDevedor > 0 ? 'text-danger' : 'text-success' }}">
                                R$ {{ number_format($saldoDevedor, 2, ',', '.') }}
                            </h4>
                        </div>
                    </div>

                    <!-- Barra de Progresso -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Progresso do Pagamento</small>
                            <small class="text-muted">{{ number_format($percentualPago, 1) }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $percentualPago }}%" 
                                 aria-valuenow="{{ $percentualPago }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Botão de Pagamento -->
                    @if($saldoDevedor > 0)
                    <div class="text-center mt-4">
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.pagamentos.pagamento', ['empresa' => $empresa->id, 'id' => $lancamento->id]) }}" 
                           class="btn btn-success btn-lg">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Registrar Pagamento
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Pagamentos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>
                Histórico de Pagamentos
            </h5>
            <span class="badge bg-primary">{{ $lancamento->pagamentos()->count() }} pagamento(s)</span>
        </div>
        <div class="card-body">
            @if($lancamento->pagamentos()->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Data Pagamento</th>
                                <th>Forma de Pagamento</th>
                                <th>Bandeira</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lancamento->pagamentos()->orderBy('created_at', 'desc')->get() as $pagamento)
                            <tr>
                                <td>{{ $pagamento->numero_parcela_pagamento }}</td>
                                <td>{{ $pagamento->data_pagamento->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $formaPagamento = DB::table('formas_pagamento')->where('id', $pagamento->forma_pagamento_id)->first();
                                    @endphp
                                    {{ $formaPagamento->nome ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($pagamento->bandeira_id)
                                        @php
                                            $bandeira = DB::table('forma_pag_bandeiras')->where('id', $pagamento->bandeira_id)->first();
                                        @endphp
                                        {{ $bandeira->nome ?? 'N/A' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        R$ {{ number_format($pagamento->valor, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($pagamento->status_pagamento === 'confirmado')
                                        <span class="badge bg-success">Confirmado</span>
                                    @elseif($pagamento->status_pagamento === 'processando')
                                        <span class="badge bg-warning">Processando</span>
                                    @elseif($pagamento->status_pagamento === 'cancelado')
                                        <span class="badge bg-secondary">Cancelado</span>
                                    @elseif($pagamento->status_pagamento === 'estornado')
                                        <span class="badge bg-danger">Estornado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="verDetalhesPagamento({{ $pagamento->id }})"
                                                title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($pagamento->status_pagamento === 'confirmado')
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="estornarPagamento({{ $pagamento->id }})"
                                                title="Estornar">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhum pagamento registrado ainda.</p>
                    @if($saldoDevedor > 0)
                    <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.pagamentos.pagamento', ['empresa' => $empresa->id, 'id' => $lancamento->id]) }}" 
                       class="btn btn-success">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Registrar Primeiro Pagamento
                    </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Detalhes do Pagamento -->
<div class="modal fade" id="modalDetalhesPagamento" tabindex="-1" aria-labelledby="modalDetalhesPagamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalhesPagamentoLabel">
                    <i class="fas fa-info-circle me-2"></i>
                    Detalhes do Pagamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="conteudoDetalhesPagamento">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Estorno -->
<div class="modal fade" id="modalEstorno" tabindex="-1" aria-labelledby="modalEstornoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEstornoLabel">
                    <i class="fas fa-undo me-2"></i>
                    Estornar Pagamento
                </h5>
                <button type="button" id="btnFecharModalEstorno" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="formEstorno" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção!</strong> Esta ação irá estornar o pagamento e não pode ser desfeita.
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivo_estorno" class="form-label">
                            Motivo do Estorno <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="motivo_estorno" name="motivo_estorno" 
                                  rows="3" required placeholder="Descreva o motivo do estorno..." 
                                  aria-describedby="motivo_help"></textarea>
                        <div id="motivo_help" class="form-text">Descreva detalhadamente o motivo do estorno</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCancelarEstorno" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" id="btnConfirmarEstorno" class="btn btn-danger">
                        <i class="fas fa-undo me-1"></i>Confirmar Estorno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Variáveis globais
const empresaId = {{ $empresa->id }};
const lancamentoId = {{ $lancamento->id }};

// Ver detalhes de um pagamento
function verDetalhesPagamento(pagamentoId) {
    const modalElement = document.getElementById('modalDetalhesPagamento');
    const modal = new bootstrap.Modal(modalElement);
    const conteudo = document.getElementById('conteudoDetalhesPagamento');
    
    // Mostrar loading
    conteudo.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>
    `;
    
    // Aguardar o modal estar totalmente aberto antes de carregar dados
    modalElement.addEventListener('shown.bs.modal', function carregarDados() {
        // Remover event listener para evitar múltiplas execuções
        modalElement.removeEventListener('shown.bs.modal', carregarDados);
        // Carregar detalhes do pagamento
        fetch(`/comerciantes/empresas/${empresaId}/financeiro/contas-pagar/${lancamentoId}/pagamentos/${pagamentoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const pagamento = data.pagamento;
                    conteudo.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informações do Pagamento</h6>
                                <table class="table table-borderless table-sm">
                                    <tr><td><strong>Parcela:</strong></td><td>#${pagamento.numero_parcela_pagamento}</td></tr>
                                    <tr><td><strong>Valor:</strong></td><td>R$ ${parseFloat(pagamento.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td></tr>
                                    <tr><td><strong>Data Pagamento:</strong></td><td>${new Date(pagamento.data_pagamento).toLocaleDateString('pt-BR')}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${pagamento.status_pagamento === 'confirmado' ? 'success' : 'warning'}">${pagamento.status_pagamento}</span></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Valores Detalhados</h6>
                                <table class="table table-borderless table-sm">
                                    <tr><td><strong>Principal:</strong></td><td>R$ ${parseFloat(pagamento.valor_principal || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td></tr>
                                    <tr><td><strong>Juros:</strong></td><td>R$ ${parseFloat(pagamento.valor_juros || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td></tr>
                                    <tr><td><strong>Multa:</strong></td><td>R$ ${parseFloat(pagamento.valor_multa || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td></tr>
                                    <tr><td><strong>Desconto:</strong></td><td>R$ ${parseFloat(pagamento.valor_desconto || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td></tr>
                                </table>
                            </div>
                        </div>
                        ${pagamento.observacao ? `
                        <div class="mt-3">
                            <h6>Observações</h6>
                            <p class="text-muted">${pagamento.observacao}</p>
                        </div>
                        ` : ''}
                        ${pagamento.referencia_externa ? `
                        <div class="mt-3">
                            <h6>Referência Externa</h6>
                            <p class="text-muted">${pagamento.referencia_externa}</p>
                        </div>
                        ` : ''}
                    `;
                } else {
                    conteudo.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Erro ao carregar detalhes do pagamento.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                conteudo.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Erro ao carregar detalhes do pagamento.
                    </div>
                `;
            });
    }, { once: true });
    
    // Mostrar o modal
    modal.show();
}

// Estornar pagamento
function estornarPagamento(pagamentoId) {
    const form = document.getElementById('formEstorno');
    form.action = `/comerciantes/empresas/${empresaId}/financeiro/contas-pagar/${lancamentoId}/pagamentos/${pagamentoId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEstorno'));
    modal.show();
}

// Submissão do formulário de estorno
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('formEstorno').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('btnConfirmarEstorno');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processando...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fechar modal
                bootstrap.Modal.getInstance(document.getElementById('modalEstorno')).hide();
                
                // Recarregar página
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar estorno');
        })
        .finally(() => {
            // Restaurar botão
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush

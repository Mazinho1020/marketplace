@extends('layouts.app')

@section('title', 'Financeiro')

@section('content')
<div class="container-fluid">
    <!-- Header do Financeiro -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-chart-line"></i>
                                Sistema Financeiro - @yield('financial-title')
                            </h4>
                            <small>Empresa: {{ $empresa->nome ?? 'Empresa' }}</small>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" 
                               class="btn btn-outline-light {{ request()->routeIs('comerciantes.empresas.financeiro.contas-pagar.*') ? 'active' : '' }}">
                                <i class="fas fa-hand-holding-usd"></i> Contas a Pagar
                            </a>
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
                               class="btn btn-outline-light {{ request()->routeIs('comerciantes.empresas.financeiro.contas-receber.*') ? 'active' : '' }}">
                                <i class="fas fa-money-bill-wave"></i> Contas a Receber
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Financeiro -->
    @isset($resumoFinanceiro)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total a Pagar</h6>
                            <h4>R$ {{ number_format($resumoFinanceiro['total_pagar'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total a Receber</h6>
                            <h4>R$ {{ number_format($resumoFinanceiro['total_receber'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Vencidas</h6>
                            <h4>{{ $resumoFinanceiro['vencidas'] ?? 0 }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Saldo</h6>
                            <h4 class="{{ ($resumoFinanceiro['saldo'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($resumoFinanceiro['saldo'] ?? 0, 2, ',', '.') }}
                            </h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endisset

    <!-- Conteúdo Específico -->
    @yield('financial-content')
</div>

<!-- Modais -->
@yield('modals')

@endsection

@push('styles')
<style>
.badge-pago {
    background-color: #28a745 !important;
}
.badge-pendente {
    background-color: #ffc107 !important;
    color: #212529 !important;
}
.badge-cancelado {
    background-color: #dc3545 !important;
}
.badge-vencido {
    background-color: #fd7e14 !important;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,.075);
}
.financial-card {
    border-left: 4px solid;
}
.financial-card.pagar {
    border-left-color: #dc3545;
}
.financial-card.receber {
    border-left-color: #28a745;
}
.btn-sm-action {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
    margin: 0 1px;
}
</style>
@endpush

@push('scripts')
<script>
// Inicializar tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Confirmação de exclusão
function confirmarExclusao(elemento) {
    if (confirm('Tem certeza que deseja excluir este lançamento?')) {
        elemento.closest('form').submit();
    }
}

// Marcar como pago/recebido
function marcarComoPago(lancamentoId, natureza) {
    const acao = natureza === 'PAGAR' ? 'pagar' : 'receber';
    const url = natureza === 'PAGAR' 
        ? `{{ url('/') }}/comerciantes/empresas/{{ $empresa->id ?? '{empresaId}' }}/financeiro/contas-pagar/${lancamentoId}/pagar`
        : `{{ url('/') }}/comerciantes/empresas/{{ $empresa->id ?? '{empresaId}' }}/financeiro/contas-receber/${lancamentoId}/receber`;
    
    if (confirm(`Confirma o ${acao} deste lançamento?`)) {
        // Criar e submeter form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Formatação de moeda
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

// Cálculo automático de parcelas
function calcularParcelas() {
    const valorTotal = parseFloat(document.getElementById('valor_total')?.value) || 0;
    const numeroParcelas = parseInt(document.getElementById('numero_parcelas')?.value) || 1;
    
    if (valorTotal > 0 && numeroParcelas > 0) {
        const valorParcela = valorTotal / numeroParcelas;
        const campoValorParcela = document.getElementById('valor_parcela');
        if (campoValorParcela) {
            campoValorParcela.value = valorParcela.toFixed(2);
        }
    }
}
</script>
@endpush

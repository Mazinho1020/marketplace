@extends('financial.layout')

@section('financial-title', 'Detalhes da Conta a Pagar')

@section('financial-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye text-danger"></i>
                        Detalhes da Conta a Pagar
                    </h5>
                    <div>
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        @if($lancamento->situacao->value !== 'PAGO')
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.edit', [$empresa, $lancamento]) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" 
                                    class="btn btn-success btn-sm" 
                                    onclick="marcarComoPago({{ $lancamento->id }})">
                                <i class="fas fa-check"></i> Marcar como Pago
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Informações Principais -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Informações Principais</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Descrição:</strong><br>
                                        {{ $lancamento->descricao }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Código:</strong><br>
                                        {{ $lancamento->codigo_lancamento ?: '-' }}
                                    </div>
                                </div>
                                
                                @if($lancamento->observacoes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <strong>Observações:</strong><br>
                                        {{ $lancamento->observacoes }}
                                    </div>
                                </div>
                                @endif
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
                                        <strong>Valor Total:</strong><br>
                                        <span class="h5 text-danger">R$ {{ number_format($lancamento->valor_total, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Valor Pago:</strong><br>
                                        <span class="h5 text-success">R$ {{ number_format($lancamento->valor_pago, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Saldo:</strong><br>
                                        <span class="h5 {{ ($lancamento->valor_total - $lancamento->valor_pago) > 0 ? 'text-warning' : 'text-success' }}">
                                            R$ {{ number_format($lancamento->valor_total - $lancamento->valor_pago, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Data de Vencimento:</strong><br>
                                        {{ \Carbon\Carbon::parse($lancamento->data_vencimento)->format('d/m/Y') }}
                                        @if($lancamento->isVencido())
                                            <br><span class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Vencida há {{ $lancamento->diasParaVencimento() * -1 }} dias
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Data de Competência:</strong><br>
                                        {{ $lancamento->data_competencia ? \Carbon\Carbon::parse($lancamento->data_competencia)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Data de Pagamento:</strong><br>
                                        {{ $lancamento->data_pagamento ? \Carbon\Carbon::parse($lancamento->data_pagamento)->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parcelamento -->
                        @if($lancamento->numero_parcelas > 1)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Informações de Parcelamento</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Parcela Atual:</strong><br>
                                        {{ $lancamento->parcela_atual }}/{{ $lancamento->numero_parcelas }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Valor da Parcela:</strong><br>
                                        R$ {{ number_format($lancamento->valor_parcela ?? 0, 2, ',', '.') }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Frequência:</strong><br>
                                        {{ $lancamento->frequencia_recorrencia ? \App\Enums\FrequenciaRecorrenciaEnum::from($lancamento->frequencia_recorrencia)->label() : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Relacionamentos e Status -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Status</h6>
                            </div>
                            <div class="card-body text-center">
                                <span class="badge badge-{{ $lancamento->situacao->color() }} p-2" style="font-size: 1.1em;">
                                    <i class="{{ $lancamento->situacao->icon() }}"></i>
                                    {{ $lancamento->situacao->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Relacionamentos</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Cliente/Fornecedor:</strong><br>
                                    @if($lancamento->cliente)
                                        <i class="fas fa-user text-info"></i>
                                        {{ $lancamento->cliente->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Funcionário:</strong><br>
                                    @if($lancamento->funcionario)
                                        <i class="fas fa-user-tie text-primary"></i>
                                        {{ $lancamento->funcionario->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Conta Gerencial:</strong><br>
                                    @if($lancamento->conta)
                                        <i class="fas fa-chart-line text-success"></i>
                                        {{ $lancamento->conta->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                                
                                <div>
                                    <strong>Categoria:</strong><br>
                                    @if($lancamento->categoria)
                                        <i class="fas fa-tags text-warning"></i>
                                        {{ $lancamento->categoria->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Configurações Avançadas -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Configurações Avançadas</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Cobrança Automática:</strong><br>
                                    @if($lancamento->cobranca_automatica)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i> Ativada
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-times-circle"></i> Desativada
                                        </span>
                                    @endif
                                </div>
                                
                                @if($lancamento->juros_multa_config)
                                <div>
                                    <strong>Configuração de Juros/Multa:</strong><br>
                                    {{ $lancamento->juros_multa_config }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Pagamentos -->
                @if($lancamento->valor_pago > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Histórico de Pagamentos</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    Valor pago: <strong>R$ {{ number_format($lancamento->valor_pago, 2, ',', '.') }}</strong>
                                    @if($lancamento->data_pagamento)
                                        em {{ \Carbon\Carbon::parse($lancamento->data_pagamento)->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function marcarComoPago(lancamentoId) {
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

@section('title', 'Detalhes da Conta a Pagar')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-eye text-info me-2"></i>
                        Detalhes da Conta a Pagar #{{ $conta->id }}
                    </h2>
                    <p class="text-muted mb-0">Visualize todas as informações da conta</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('financial.contas-pagar.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar à Lista
                    </a>
                    
                    @if($conta->situacao === \App\Enums\SituacaoFinanceiraEnum::PENDENTE)
                        <button type="button" class="btn btn-success" onclick="abrirModalPagamento()">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Efetuar Pagamento
                        </button>
                    @endif
                    
                    <a href="{{ route('financial.contas-pagar.edit', $conta->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações Principais -->
        <div class="col-lg-8">
            <!-- Status e Valores -->
            <div class="card financial-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Gerais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Descrição</label>
                            <h5 class="mb-0">{{ $conta->descricao }}</h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                <span class="status-badge fs-6" style="background-color: {{ $conta->situacao->getColor() }}; color: white;">
                                    <i class="{{ $conta->situacao->getIcon() }} me-2"></i>
                                    {{ $conta->situacao->getLabel() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted">Valor Original</label>
                            <h4 class="text-danger mb-0">R$ {{ number_format($conta->valor_original, 2, ',', '.') }}</h4>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted">Valor Final</label>
                            <h4 class="text-primary mb-0">R$ {{ number_format($conta->valor_final, 2, ',', '.') }}</h4>
                            @if($conta->valor_final != $conta->valor_original)
                                <small class="text-muted">
                                    Diferença: R$ {{ number_format($conta->valor_final - $conta->valor_original, 2, ',', '.') }}
                                </small>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted">Data de Vencimento</label>
                            <h5 class="mb-1 {{ $conta->isVencida() ? 'text-danger' : 'text-dark' }}">
                                {{ $conta->data_vencimento->format('d/m/Y') }}
                            </h5>
                            @if($conta->isVencida())
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ abs($conta->diasParaVencimento()) }} dias em atraso
                                </small>
                            @elseif($conta->diasParaVencimento() <= 7 && $conta->diasParaVencimento() > 0)
                                <small class="text-warning">
                                    <i class="fas fa-clock me-1"></i>
                                    Vence em {{ $conta->diasParaVencimento() }} dias
                                </small>
                            @elseif($conta->diasParaVencimento() > 0)
                                <small class="text-muted">
                                    Vence em {{ $conta->diasParaVencimento() }} dias
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados do Fornecedor -->
            <div class="card financial-card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>
                        Dados do Fornecedor
                    </h6>
                </div>
                <div class="card-body">
                    @if($conta->pessoa)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Nome/Razão Social</label>
                                <h6 class="mb-0">{{ $conta->pessoa->nome }}</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted">Documento</label>
                                <h6 class="mb-0">{{ $conta->pessoa->documento ?? 'Não informado' }}</h6>
                            </div>
                            
                            @if($conta->pessoa->email)
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Email</label>
                                    <h6 class="mb-0">
                                        <a href="mailto:{{ $conta->pessoa->email }}">{{ $conta->pessoa->email }}</a>
                                    </h6>
                                </div>
                            @endif
                            
                            @if($conta->pessoa->telefone)
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Telefone</label>
                                    <h6 class="mb-0">
                                        <a href="tel:{{ $conta->pessoa->telefone }}">{{ $conta->pessoa->telefone }}</a>
                                    </h6>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p class="mb-0">Nenhum fornecedor vinculado a esta conta</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card financial-card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Informações Adicionais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted">Categoria</label>
                            <h6 class="mb-0">
                                @if($conta->categoria)
                                    <span class="badge bg-secondary">{{ ucfirst($conta->categoria) }}</span>
                                @else
                                    <span class="text-muted">Não categorizada</span>
                                @endif
                            </h6>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted">Conta Gerencial</label>
                            <h6 class="mb-0">
                                @if($conta->contaGerencial)
                                    {{ $conta->contaGerencial->nome }}
                                @else
                                    <span class="text-muted">Não vinculada</span>
                                @endif
                            </h6>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted">Data de Criação</label>
                            <h6 class="mb-0">{{ $conta->created_at->format('d/m/Y H:i') }}</h6>
                        </div>
                        
                        @if($conta->observacoes)
                            <div class="col-12">
                                <label class="form-label text-muted">Observações</label>
                                <div class="border rounded p-3 bg-light">
                                    {{ $conta->observacoes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Histórico de Pagamentos -->
            @if($conta->situacao === \App\Enums\SituacaoFinanceiraEnum::PAGO && $conta->data_pagamento)
                <div class="card financial-card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Informações do Pagamento
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted">Data do Pagamento</label>
                                <h6 class="mb-0">{{ $conta->data_pagamento->format('d/m/Y') }}</h6>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label text-muted">Valor Pago</label>
                                <h6 class="mb-0 text-success">R$ {{ number_format($conta->valor_pago, 2, ',', '.') }}</h6>
                            </div>
                            
                            @if($conta->forma_pagamento)
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Forma de Pagamento</label>
                                    <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $conta->forma_pagamento)) }}</h6>
                                </div>
                            @endif
                            
                            <div class="col-md-3">
                                <label class="form-label text-muted">Situação do Pagamento</label>
                                <h6 class="mb-0">
                                    @if($conta->valor_pago > $conta->valor_final)
                                        <span class="text-info">Pagamento com excesso</span>
                                    @elseif($conta->valor_pago < $conta->valor_final)
                                        <span class="text-warning">Pagamento parcial</span>
                                    @else
                                        <span class="text-success">Pagamento integral</span>
                                    @endif
                                </h6>
                            </div>
                            
                            @if($conta->observacoes_pagamento)
                                <div class="col-12">
                                    <label class="form-label text-muted">Observações do Pagamento</label>
                                    <div class="border rounded p-3 bg-light">
                                        {{ $conta->observacoes_pagamento }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar com Ações e Resumo -->
        <div class="col-lg-4">
            <!-- Ações Rápidas -->
            <div class="card financial-card mb-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($conta->situacao === \App\Enums\SituacaoFinanceiraEnum::PENDENTE)
                            <button type="button" class="btn btn-success" onclick="abrirModalPagamento()">
                                <i class="fas fa-dollar-sign me-2"></i>
                                Efetuar Pagamento
                            </button>
                        @endif
                        
                        <a href="{{ route('financial.contas-pagar.edit', $conta->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Editar Conta
                        </a>
                        
                        <button type="button" class="btn btn-info" onclick="imprimirConta()">
                            <i class="fas fa-print me-2"></i>
                            Imprimir
                        </button>
                        
                        <button type="button" class="btn btn-warning" onclick="duplicarConta()">
                            <i class="fas fa-copy me-2"></i>
                            Duplicar Conta
                        </button>
                        
                        <hr>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="excluirConta()">
                            <i class="fas fa-trash me-2"></i>
                            Excluir Conta
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="card financial-card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Informações do Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>ID:</strong> {{ $conta->id }}
                        </li>
                        <li class="mb-2">
                            <strong>Empresa:</strong> {{ $conta->empresa->nome ?? 'Não informada' }}
                        </li>
                        <li class="mb-2">
                            <strong>Criado em:</strong> {{ $conta->created_at->format('d/m/Y H:i') }}
                        </li>
                        <li class="mb-2">
                            <strong>Atualizado em:</strong> {{ $conta->updated_at->format('d/m/Y H:i') }}
                        </li>
                        @if($conta->created_by)
                            <li class="mb-0">
                                <strong>Criado por:</strong> {{ $conta->criador->name ?? 'Usuário não encontrado' }}
                            </li>
                        @endif
                    </ul>
                </div>
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
            <form method="POST" action="{{ route('financial.contas-pagar.pagar', $conta->id) }}" id="formPagamento">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><strong>Conta:</strong> {{ $conta->descricao }}</h6>
                        <p class="mb-2"><strong>Fornecedor:</strong> {{ $conta->pessoa->nome ?? 'Não informado' }}</p>
                        <p class="mb-2"><strong>Valor Original:</strong> R$ {{ number_format($conta->valor_original, 2, ',', '.') }}</p>
                        <p class="mb-0"><strong>Vencimento:</strong> {{ $conta->data_vencimento->format('d/m/Y') }}</p>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Data do Pagamento *</label>
                            <input type="date" name="data_pagamento" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Valor Pago *</label>
                            <input type="text" name="valor_pago" class="form-control money" 
                                   value="{{ number_format($conta->valor_final, 2, ',', '.') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Conta Gerencial</label>
                            <select name="conta_gerencial_id" class="form-select">
                                <option value="">Selecione uma conta</option>
                                @foreach($contasGerenciais as $contaGerencial)
                                    <option value="{{ $contaGerencial->id }}" 
                                            {{ $conta->conta_gerencial_id == $contaGerencial->id ? 'selected' : '' }}>
                                        {{ $contaGerencial->nome }}
                                    </option>
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
function abrirModalPagamento() {
    $('#modalPagamento').modal('show');
}

function excluirConta() {
    confirmarAcao('Tem certeza que deseja excluir esta conta a pagar?', function() {
        $.ajax({
            url: '{{ route("financial.contas-pagar.destroy", $conta->id) }}',
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    window.location.href = '{{ route("financial.contas-pagar.index") }}';
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

function duplicarConta() {
    confirmarAcao('Deseja criar uma nova conta baseada nesta?', function() {
        $.ajax({
            url: '{{ route("financial.contas-pagar.duplicate", $conta->id) }}',
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    window.location.href = `/financial/contas-pagar/${response.data.id}/edit`;
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Erro ao duplicar conta');
            }
        });
    });
}

function imprimirConta() {
    window.open('{{ route("financial.contas-pagar.print", $conta->id) }}', '_blank');
}

// Submissão do formulário de pagamento
$('#formPagamento').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: $(this).attr('action'),
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

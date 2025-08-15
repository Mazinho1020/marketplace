@extends('layouts.comerciante')

@section('title', 'Detalhes da Conta a Receber')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

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
                <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}">Contas a Receber</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $contaReceber->descricao }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $contaReceber->descricao }}</h1>
        <div class="btn-group">
            @if($contaReceber->situacao_financeira->value == 'pendente')
                <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.edit', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}" 
                   class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif
            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Dados Principais -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> InformaÃ§Ãµes Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">SituaÃ§Ã£o:</dt>
                        <dd class="col-sm-9">
                            @php
                                $badgeClass = match($contaReceber->situacao_financeira->value) {
                                    'pendente' => 'warning',
                                    'pago' => 'success',
                                    'vencido' => 'danger',
                                    'cancelado' => 'secondary',
                                    'em_negociacao' => 'info',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ $contaReceber->situacao_financeira->label() }}
                            </span>
                            @if($contaReceber->data_vencimento->isPast() && $contaReceber->situacao_financeira->value == 'pendente')
                                <span class="badge bg-danger ms-2">Vencida</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Cliente:</dt>
                        <dd class="col-sm-9">
                            @if($contaReceber->pessoa)
                                {{ $contaReceber->pessoa->nome }}
                                @if($contaReceber->pessoa->cpf_cnpj)
                                    <small class="text-muted">({{ $contaReceber->pessoa->cpf_cnpj }})</small>
                                @endif
                            @else
                                <span class="text-muted">NÃ£o informado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Conta Gerencial:</dt>
                        <dd class="col-sm-9">
                            @if($contaReceber->contaGerencial)
                                {{ $contaReceber->contaGerencial->codigo }} - {{ $contaReceber->contaGerencial->nome }}
                            @else
                                <span class="text-muted">NÃ£o informado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">NÃºmero do Documento:</dt>
                        <dd class="col-sm-9">
                            {{ $contaReceber->numero_documento ?: 'NÃ£o informado' }}
                        </dd>

                        <dt class="col-sm-3">ObservaÃ§Ãµes:</dt>
                        <dd class="col-sm-9">
                            {{ $contaReceber->observacoes ?: 'NÃ£o informado' }}
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Valores -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dollar-sign"></i> Valores Financeiros
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Valor Original:</dt>
                                <dd class="col-6">R$ {{ number_format($contaReceber->valor_original, 2, ',', '.') }}</dd>

                                @if($contaReceber->valor_desconto > 0)
                                    <dt class="col-6">Desconto:</dt>
                                    <dd class="col-6 text-success">- R$ {{ number_format($contaReceber->valor_desconto, 2, ',', '.') }}</dd>
                                @endif

                                @if($contaReceber->valor_acrescimo > 0)
                                    <dt class="col-6">AcrÃ©scimo:</dt>
                                    <dd class="col-6 text-warning">+ R$ {{ number_format($contaReceber->valor_acrescimo, 2, ',', '.') }}</dd>
                                @endif

                                @if($contaReceber->valor_juros > 0)
                                    <dt class="col-6">Juros:</dt>
                                    <dd class="col-6 text-warning">+ R$ {{ number_format($contaReceber->valor_juros, 2, ',', '.') }}</dd>
                                @endif

                                @if($contaReceber->valor_multa > 0)
                                    <dt class="col-6">Multa:</dt>
                                    <dd class="col-6 text-danger">+ R$ {{ number_format($contaReceber->valor_multa, 2, ',', '.') }}</dd>
                                @endif
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6"><strong>Valor Final:</strong></dt>
                                <dd class="col-6"><strong class="fs-5 text-primary">R$ {{ number_format($contaReceber->valor_final, 2, ',', '.') }}</strong></dd>

                                @if($contaReceber->situacao_financeira->value == 'pago' && $contaReceber->data_pagamento)
                                    <dt class="col-6">Data do Recebimento:</dt>
                                    <dd class="col-6">{{ $contaReceber->data_pagamento->format('d/m/Y H:i') }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HistÃ³rico de Recebimentos -->
            <div class="card mb-4" id="historicoRecebimentos">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> HistÃ³rico de Recebimentos
                    </h5>
                    <span class="badge bg-info">{{ $resumoRecebimentos['total_recebimentos'] }} recebimento(s)</span>
                </div>
                <div class="card-body">
                    @if($resumoRecebimentos['total_recebimentos'] > 0)
                        <!-- Resumo Geral -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted mb-1">Valor Total</h6>
                                    <h5 class="text-primary mb-0">R$ {{ number_format($resumoRecebimentos['valor_total'], 2, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted mb-1">Total Recebido</h6>
                                    <h5 class="text-success mb-0">R$ {{ number_format($resumoRecebimentos['valor_recebido'], 2, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted mb-1">Saldo Devedor</h6>
                                    <h5 class="{{ $resumoRecebimentos['saldo_devedor'] > 0 ? 'text-warning' : 'text-success' }} mb-0">
                                        R$ {{ number_format($resumoRecebimentos['saldo_devedor'], 2, ',', '.') }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted mb-1">Percentual Recebido</h6>
                                    <h5 class="text-info mb-0">{{ number_format($resumoRecebimentos['percentual_recebido'], 1) }}%</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Progresso -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Progresso do Recebimento</small>
                                <small>{{ number_format($resumoRecebimentos['percentual_recebido'], 1) }}%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar {{ $resumoRecebimentos['percentual_recebido'] >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ min($resumoRecebimentos['percentual_recebido'], 100) }}%"
                                     aria-valuenow="{{ $resumoRecebimentos['percentual_recebido'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Recebimentos -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabelaRecebimentos">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Forma de Pagamento</th>
                                        <th>Bandeira</th>
                                        <th>Conta BancÃ¡ria</th>
                                        <th>ObservaÃ§Ãµes</th>
                                        <th>AÃ§Ãµes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recebimentos as $recebimento)
                                    <tr>
                                        <td>
                                            <strong>{{ $recebimento->data_pagamento->format('d/m/Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $recebimento->data_pagamento->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <strong class="text-success">R$ {{ number_format($recebimento->valor, 2, ',', '.') }}</strong>
                                            @if($recebimento->valor_principal != $recebimento->valor)
                                                <br>
                                                <small class="text-muted">
                                                    Principal: R$ {{ number_format($recebimento->valor_principal, 2, ',', '.') }}
                                                    @if($recebimento->valor_juros > 0)
                                                        <br>Juros: R$ {{ number_format($recebimento->valor_juros, 2, ',', '.') }}
                                                    @endif
                                                    @if($recebimento->valor_multa > 0)
                                                        <br>Multa: R$ {{ number_format($recebimento->valor_multa, 2, ',', '.') }}
                                                    @endif
                                                    @if($recebimento->valor_desconto > 0)
                                                        <br>Desconto: R$ {{ number_format($recebimento->valor_desconto, 2, ',', '.') }}
                                                    @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($recebimento->formaPagamento)
                                                <span class="badge bg-secondary">{{ $recebimento->formaPagamento->nome }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($recebimento->bandeira)
                                                <span class="badge bg-info">{{ $recebimento->bandeira->nome }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($recebimento->contaBancaria)
                                                <small>{{ $recebimento->contaBancaria->nome_banco ?? 'Conta ' . $recebimento->conta_bancaria_id }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($recebimento->observacao)
                                                <small>{{ Str::limit($recebimento->observacao, 50) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm" 
                                                        onclick="verDetalhesRecebimento({{ $recebimento->id }})"
                                                        title="Ver detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($contaReceber->situacao_financeira->value != 'pago')
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        onclick="confirmarEstorno({{ $recebimento->id }})"
                                                        title="Estornar recebimento">
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
                            <h6 class="text-muted">Nenhum recebimento registrado</h6>
                            <p class="text-muted mb-0">
                                @if($contaReceber->situacao_financeira->value == 'pendente')
                                    Clique em "Registrar Recebimento" para registrar o primeiro pagamento.
                                @else
                                    Esta conta nÃ£o possui histÃ³rico de recebimentos.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Datas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt"></i> Datas Importantes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Data de EmissÃ£o:</dt>
                                <dd class="col-6">
                                    @if($contaReceber->data_emissao)
                                        {{ $contaReceber->data_emissao->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">NÃ£o informado</span>
                                    @endif
                                </dd>

                                <dt class="col-6">Data de CompetÃªncia:</dt>
                                <dd class="col-6">
                                    @if($contaReceber->data_competencia)
                                        {{ $contaReceber->data_competencia->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">NÃ£o informado</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Data de Vencimento:</dt>
                                <dd class="col-6">
                                    @if($contaReceber->data_vencimento->isPast() && $contaReceber->situacao_financeira->value == 'pendente')
                                        <span class="text-danger fw-bold">
                                            {{ $contaReceber->data_vencimento->format('d/m/Y') }}
                                            <small>(Vencida hÃ¡ {{ $contaReceber->data_vencimento->diffForHumans() }})</small>
                                        </span>
                                    @else
                                        {{ $contaReceber->data_vencimento->format('d/m/Y') }}
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status e AÃ§Ãµes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> AÃ§Ãµes RÃ¡pidas
                    </h5>
                </div>
                <div class="card-body">
                    @if($contaReceber->situacao_financeira->value == 'pendente')
                        <div class="d-grid gap-2">
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.recebimentos.pagamento', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}" 
                               class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Registrar Recebimento
                            </a>

                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.edit', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar Dados
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i>
                            Esta conta jÃ¡ foi processada e nÃ£o pode mais ser editada.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resumo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Resumo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary mb-1">
                            R$ {{ number_format($contaReceber->valor_final, 2, ',', '.') }}
                        </h3>
                        <p class="text-muted mb-3">Valor Total</p>

                        @if($contaReceber->situacao_financeira->value == 'pendente')
                            @if($contaReceber->data_vencimento->isFuture())
                                <p class="text-success mb-0">
                                    <i class="fas fa-clock"></i>
                                    Vence em {{ $contaReceber->data_vencimento->diffForHumans() }}
                                </p>
                            @else
                                <p class="text-danger mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Vencida hÃ¡ {{ $contaReceber->data_vencimento->diffForHumans() }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            @if($contaReceber->data_vencimento->isPast() && $contaReceber->situacao_financeira->value == 'pendente')
                <div class="card border-danger mb-4">
                    <div class="card-header bg-danger text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Conta Vencida
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Esta conta estÃ¡ vencida hÃ¡ {{ $contaReceber->data_vencimento->diffForHumans() }}.</p>
                        <p class="mb-0 small text-muted">
                            Considere entrar em contato com o cliente para regularizaÃ§Ã£o.
                        </p>
                    </div>
                </div>
            @endif

            @if($contaReceber->data_vencimento->isToday() && $contaReceber->situacao_financeira->value == 'pendente')
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clock"></i> Vence Hoje
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Esta conta vence hoje. Monitore o recebimento.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// JavaScript mínimo para página de visualização
console.log(' Página de detalhes da conta a receber carregada');

// Função para recarregar a página se necessário no futuro
function recarregarPagina() {
    location.reload();
}
</script>
@endpush

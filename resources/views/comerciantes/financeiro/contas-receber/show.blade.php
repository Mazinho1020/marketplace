@extends('comerciantes.layouts.app')

@section('title', 'Detalhes da Conta a Receber')

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

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Dados Principais -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informações Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Situação:</dt>
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
                                <span class="text-muted">Não informado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Conta Gerencial:</dt>
                        <dd class="col-sm-9">
                            @if($contaReceber->contaGerencial)
                                {{ $contaReceber->contaGerencial->codigo }} - {{ $contaReceber->contaGerencial->nome }}
                            @else
                                <span class="text-muted">Não informado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Número do Documento:</dt>
                        <dd class="col-sm-9">
                            {{ $contaReceber->numero_documento ?: 'Não informado' }}
                        </dd>

                        <dt class="col-sm-3">Observações:</dt>
                        <dd class="col-sm-9">
                            {{ $contaReceber->observacoes ?: 'Não informado' }}
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
                                    <dt class="col-6">Acréscimo:</dt>
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
                                <dt class="col-6">Data de Emissão:</dt>
                                <dd class="col-6">
                                    @if($contaReceber->data_emissao)
                                        {{ $contaReceber->data_emissao->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </dd>

                                <dt class="col-6">Data de Competência:</dt>
                                <dd class="col-6">
                                    @if($contaReceber->data_competencia)
                                        {{ $contaReceber->data_competencia->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Não informado</span>
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
                                            <small>(Vencida há {{ $contaReceber->data_vencimento->diffForHumans() }})</small>
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
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    @if($contaReceber->situacao_financeira->value == 'pendente')
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('comerciantes.empresas.financeiro.contas-receber.update', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="situacao_financeira" value="pago">
                                <input type="hidden" name="data_pagamento" value="{{ now() }}">
                                <button type="submit" class="btn btn-success w-100" 
                                        onclick="return confirm('Confirma o recebimento desta conta?')">
                                    <i class="fas fa-check"></i> Marcar como Recebida
                                </button>
                            </form>

                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.edit', ['empresa' => $empresa, 'id' => $contaReceber->id]) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar Dados
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i>
                            Esta conta já foi processada e não pode mais ser editada.
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
                                    Vencida há {{ $contaReceber->data_vencimento->diffForHumans() }}
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
                        <p class="mb-2">Esta conta está vencida há {{ $contaReceber->data_vencimento->diffForHumans() }}.</p>
                        <p class="mb-0 small text-muted">
                            Considere entrar em contato com o cliente para regularização.
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

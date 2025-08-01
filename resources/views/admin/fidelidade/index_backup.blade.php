@extends('layouts.admin')

@section('title', 'Fidelidade - Admin')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-star me-2"></i>
                    Sistema de Fidelidade
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Fidelidade</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.fidelidade.deletados') }}" class="btn btn-warning">
                    <i class="uil uil-trash-alt me-1"></i>
                    Registros Deletados
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($estatisticas['carteiras_total']) }}</h3>
                            <p class="mb-0">Carteiras Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-wallet" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <small>{{ $estatisticas['carteiras_ativas'] }} ativas | {{ $estatisticas['carteiras_deletadas'] }} deletadas</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($estatisticas['cupons_total']) }}</h3>
                            <p class="mb-0">Cupons Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-ticket" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <small>{{ $estatisticas['cupons_ativos'] }} ativos | {{ $estatisticas['cupons_deletados'] }} deletados</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($estatisticas['creditos_total']) }}</h3>
                            <p class="mb-0">Créditos Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-money-bill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <small>{{ $estatisticas['creditos_ativos'] }} ativos | {{ $estatisticas['creditos_deletados'] }} deletados</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($estatisticas['transacoes_total']) }}</h3>
                            <p class="mb-0">Transações Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="uil uil-transaction" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <small>{{ $estatisticas['transacoes_deletadas'] }} deletadas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu de Ações -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-setting me-2"></i>
                        Gerenciamento de Fidelidade
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body text-center">
                                    <i class="uil uil-wallet text-primary" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Carteiras</h5>
                                    <p class="text-muted">Gerenciar carteiras de fidelidade dos clientes</p>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary btn-sm">Visualizar</button>
                                        <a href="{{ route('admin.fidelidade.deletados', 'carteiras') }}" class="btn btn-warning btn-sm">Deletadas</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body text-center">
                                    <i class="uil uil-ticket text-success" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Cupons</h5>
                                    <p class="text-muted">Gerenciar cupons de desconto e promoções</p>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success btn-sm">Visualizar</button>
                                        <a href="{{ route('admin.fidelidade.deletados', 'cupons') }}" class="btn btn-warning btn-sm">Deletados</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body text-center">
                                    <i class="uil uil-money-bill text-info" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Créditos</h5>
                                    <p class="text-muted">Gerenciar créditos e saldos dos clientes</p>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-info btn-sm">Visualizar</button>
                                        <a href="{{ route('admin.fidelidade.deletados', 'creditos') }}" class="btn btn-warning btn-sm">Deletados</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body text-center">
                                    <i class="uil uil-trophy text-warning" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Conquistas</h5>
                                    <p class="text-muted">Gerenciar conquistas e recompensas</p>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-warning btn-sm">Visualizar</button>
                                        <a href="{{ route('admin.fidelidade.deletados', 'conquistas') }}" class="btn btn-warning btn-sm">Deletadas</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body text-center">
                                    <i class="uil uil-transaction text-secondary" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Transações</h5>
                                    <p class="text-muted">Visualizar histórico de transações</p>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-secondary btn-sm">Visualizar</button>
                                        <a href="{{ route('admin.fidelidade.deletados', 'transacoes') }}" class="btn btn-warning btn-sm">Deletadas</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body text-center">
                                    <i class="uil uil-percentage text-danger" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Regras Cashback</h5>
                                    <p class="text-muted">Configurar regras de cashback</p>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-danger btn-sm">Visualizar</button>
                                        <a href="{{ route('admin.fidelidade.deletados', 'regras') }}" class="btn btn-warning btn-sm">Deletadas</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações sobre Soft Deletes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="uil uil-info-circle me-2"></i>Sistema de Soft Deletes Implementado</h5>
                <p class="mb-0">
                    Todos os modelos de fidelidade agora suportam soft deletes (exclusão lógica). 
                    Os registros "deletados" são mantidos no banco de dados com timestamp em `deleted_at` 
                    e podem ser restaurados se necessário.
                </p>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Recursos Disponíveis:</strong>
                        <ul class="mb-0">
                            <li>Visualizar registros deletados</li>
                            <li>Restaurar registros deletados</li>
                            <li>Deletar permanentemente (force delete)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Modelos com Soft Deletes:</strong>
                        <ul class="mb-0">
                            <li>FidelidadeCarteira</li>
                            <li>FidelidadeCupom</li>
                            <li>FidelidadeCredito</li>
                            <li>FidelidadeConquista</li>
                            <li>FidelidadeCashbackTransacao</li>
                            <li>FidelidadeCashbackRegra</li>
                            <li>FichaTecnicaCategoria</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

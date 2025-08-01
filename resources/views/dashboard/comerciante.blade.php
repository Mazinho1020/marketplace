@extends('layouts.app')

@section('title', 'Dashboard - Comerciante')

@section('page-title', 'Dashboard do Comerciante')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Marketplace</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="h-100">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Bem-vindo, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted mb-0">Gerencie seu negócio e acompanhe suas vendas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Pedidos Hoje</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['pedidos_hoje'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver pedidos</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="bx bx-shopping-bag text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Vendas do Mês</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['vendas_mes'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver relatório</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle rounded fs-3">
                                        <i class="bx bx-line-chart text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Produtos</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['produtos_cadastrados'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Gerenciar produtos</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle rounded fs-3">
                                        <i class="bx bx-package text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Faturamento</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">R$ <span class="counter-value" data-target="{{ $stats['faturamento_mes'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver financeiro</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle rounded fs-3">
                                        <i class="bx bx-dollar-circle text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Menu Rápido</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-sm-6 col-xl-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border border-primary">
                                            <div class="card-body text-center">
                                                <div class="avatar-md mx-auto mb-3">
                                                    <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-2">
                                                        <i class="bx bx-plus"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Novo Produto</h5>
                                                <p class="card-text text-muted">Adicionar produto ao catálogo</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="col-sm-6 col-xl-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border border-success">
                                            <div class="card-body text-center">
                                                <div class="avatar-md mx-auto mb-3">
                                                    <span class="avatar-title bg-success-subtle text-success rounded-3 fs-2">
                                                        <i class="bx bx-shopping-bag"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Pedidos</h5>
                                                <p class="card-text text-muted">Gerenciar pedidos</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="col-sm-6 col-xl-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border border-warning">
                                            <div class="card-body text-center">
                                                <div class="avatar-md mx-auto mb-3">
                                                    <span class="avatar-title bg-warning-subtle text-warning rounded-3 fs-2">
                                                        <i class="bx bx-star"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Fidelidade</h5>
                                                <p class="card-text text-muted">Programa de fidelidade</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="col-sm-6 col-xl-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border border-info">
                                            <div class="card-body text-center">
                                                <div class="avatar-md mx-auto mb-3">
                                                    <span class="avatar-title bg-info-subtle text-info rounded-3 fs-2">
                                                        <i class="bx bx-bar-chart"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Relatórios</h5>
                                                <p class="card-text text-muted">Análises e relatórios</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

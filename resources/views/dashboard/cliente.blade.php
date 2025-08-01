@extends('layouts.app')

@section('title', 'Dashboard - Cliente')

@section('page-title', 'Minha Área')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Marketplace</a></li>
    <li class="breadcrumb-item active">Minha Área</li>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="h-100">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Olá, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted mb-0">Bem-vindo de volta ao Marketplace.</p>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Pedidos Realizados</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['pedidos_realizados'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver histórico</a>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Cashback</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">R$ <span class="counter-value" data-target="{{ $stats['cashback_acumulado'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Resgatar</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle rounded fs-3">
                                        <i class="bx bx-wallet text-success"></i>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Cupons</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['cupons_disponiveis'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver cupons</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle rounded fs-3">
                                        <i class="bx bx-gift text-warning"></i>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Última Compra</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-2">{{ $stats['ultima_compra'] ?? 'Nenhuma compra' }}</h6>
                                    <a href="#" class="text-decoration-underline">Comprar novamente</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle rounded fs-3">
                                        <i class="bx bx-time text-info"></i>
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
                            <h4 class="card-title mb-0">Ações Rápidas</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-sm-6 col-xl-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border border-primary">
                                            <div class="card-body text-center">
                                                <div class="avatar-md mx-auto mb-3">
                                                    <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-2">
                                                        <i class="bx bx-search"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Buscar Produtos</h5>
                                                <p class="card-text text-muted">Explore nosso catálogo</p>
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
                                                        <i class="bx bx-cart"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Meu Carrinho</h5>
                                                <p class="card-text text-muted">Finalizar compras</p>
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
                                                <h5 class="card-title">Favoritos</h5>
                                                <p class="card-text text-muted">Produtos salvos</p>
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
                                                        <i class="bx bx-user"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Meu Perfil</h5>
                                                <p class="card-text text-muted">Dados pessoais</p>
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

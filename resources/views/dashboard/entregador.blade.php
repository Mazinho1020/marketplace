@extends('layouts.app')

@section('title', 'Dashboard - Entregador')

@section('page-title', 'Painel do Entregador')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Marketplace</a></li>
    <li class="breadcrumb-item active">Painel do Entregador</li>
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
                            <p class="text-muted mb-0">Gerencie suas entregas e acompanhe seus ganhos.</p>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Entregas Hoje</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['entregas_hoje'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver entregas</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="bx bx-package text-primary"></i>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Entregas do Mês</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['entregas_mes'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver histórico</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle rounded fs-3">
                                        <i class="bx bx-truck text-success"></i>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Ganhos do Mês</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">R$ <span class="counter-value" data-target="{{ $stats['ganhos_mes'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver relatório</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle rounded fs-3">
                                        <i class="bx bx-dollar text-warning"></i>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Avaliação</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['avaliacao_media'] }}">0</span> ⭐</h4>
                                    <a href="#" class="text-decoration-underline">Ver avaliações</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle rounded fs-3">
                                        <i class="bx bx-star text-info"></i>
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
                                                        <i class="bx bx-plus"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Nova Entrega</h5>
                                                <p class="card-text text-muted">Aceitar nova entrega</p>
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
                                                        <i class="bx bx-map"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Rotas</h5>
                                                <p class="card-text text-muted">Ver rotas otimizadas</p>
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
                                                        <i class="bx bx-time"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Histórico</h5>
                                                <p class="card-text text-muted">Entregas realizadas</p>
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
                                                        <i class="bx bx-cog"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">Configurações</h5>
                                                <p class="card-text text-muted">Ajustar preferências</p>
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

@extends('layouts.app')

@section('title', 'Dashboard - Administrador')

@section('page-title', 'Dashboard')

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
                            <p class="text-muted mb-0">Aqui está o que está acontecendo no seu marketplace hoje.</p>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <form action="javascript:void(0);">
                                <div class="row g-3 mb-0 align-items-center">
                                    <div class="col-sm-auto">
                                        <div class="input-group">
                                            <input type="text" class="form-control border-0 dash-filter-picker shadow" data-provider="flatpickr" data-range-date="true" data-date-format="d M, Y" data-deafult-date="01 Jan 2022 to 31 Jan 2022">
                                            <div class="input-group-text bg-primary border-primary text-white">
                                                <i class="ri-calendar-2-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total de Usuários</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-success fs-14 mb-0">
                                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24%
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_usuarios'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver usuários</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle rounded fs-3">
                                        <i class="bx bx-user-circle text-success"></i>
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
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Empresas Ativas</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-danger fs-14 mb-0">
                                        <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -3.57%
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_empresas'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver empresas</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle rounded fs-3">
                                        <i class="bx bx-shopping-bag text-info"></i>
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
                                <div class="flex-shrink-0">
                                    <h5 class="text-success fs-14 mb-0">
                                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08%
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['vendas_mes'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver vendas</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle rounded fs-3">
                                        <i class="bx bx-store text-warning"></i>
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
                                <div class="flex-shrink-0">
                                    <h5 class="text-muted fs-14 mb-0">+0.00%</h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">R$ <span class="counter-value" data-target="{{ $stats['faturamento_mes'] }}">0</span></h4>
                                    <a href="#" class="text-decoration-underline">Ver relatório</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                                        <i class="bx bx-dollar-circle text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Visão Geral do Sistema</h4>
                        </div>
                        <div class="card-header p-0 border-0 bg-light-subtle">
                            <div class="row g-0 text-center">
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span class="counter-value" data-target="7585">0</span></h5>
                                        <p class="text-muted mb-0">Pedidos</p>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1">R$ <span class="counter-value" data-target="22.89">0</span>k</h5>
                                        <p class="text-muted mb-0">Faturamento</p>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span class="counter-value" data-target="367">0</span></h5>
                                        <p class="text-muted mb-0">Reembolsos</p>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0 border-end-0">
                                        <h5 class="mb-1 text-success"><span class="counter-value" data-target="18.92">0</span>%</h5>
                                        <p class="text-muted mb-0">Taxa de Conversão</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0 pb-2">
                            <div class="w-100">
                                <div id="customer_impression_charts" data-colors='["--vz-primary", "--vz-success", "--vz-danger"]' class="apex-charts" dir="ltr"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Vendas por Localização</h4>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-soft-primary btn-sm">
                                    Exportar <i class="ri-file-download-line align-middle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="sales-by-locations" data-colors='["--vz-light"]' style="height: 269px" dir="ltr"></div>
                            <div class="px-2 py-2 mt-1">
                                <p class="mb-1">São Paulo <span class="float-end">75%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-gradient-1" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-3 mb-1">Rio de Janeiro <span class="float-end">47%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-gradient-2" role="progressbar" style="width: 47%" aria-valuenow="47" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-3 mb-1">Minas Gerais <span class="float-end">82%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-gradient-3" role="progressbar" style="width: 82%" aria-valuenow="82" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Últimos Usuários</h4>
                            <div class="flex-shrink-0">
                                <div class="dropdown card-header-dropdown">
                                    <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="fw-semibold text-uppercase fs-12">Ordenar por: </span><span class="text-muted">Mais recentes<i class="mdi mdi-chevron-down ms-1"></i></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">Mais recentes</a>
                                        <a class="dropdown-item" href="#">Nome A-Z</a>
                                        <a class="dropdown-item" href="#">Nome Z-A</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <img src="{{ asset('Theme1/images/users/avatar-1.jpg') }}" alt="" class="avatar-xs rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 class="fs-13 mb-0">Mary Cousar</h5>
                                                        <p class="fs-12 mb-0 text-muted">mary@example.com</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-success-subtle text-success">Ativo</span></td>
                                            <td>
                                                <h5 class="fs-13 mb-0">Administrador</h5>
                                                <p class="fs-12 mb-0 text-muted">Acesso total</p>
                                            </td>
                                            <td>
                                                <p class="fs-12 mb-0">Último acesso:</p>
                                                <p class="fs-12 mb-0 text-muted">10 Min atrás</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('Theme1/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('Theme1/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('Theme1/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('Theme1/js/pages/dashboard-ecommerce.init.js') }}"></script>
@endpush

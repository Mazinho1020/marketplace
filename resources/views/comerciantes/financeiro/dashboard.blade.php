@extends('comerciantes.layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Sistema Financeiro - Empresa {{ $empresa }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.index') }}">Empresas</a></li>
                        <li class="breadcrumb-item active">Financeiro</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Dashboard Financeiro</h5>
                    
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Categorias">Categorias</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-folder text-primary"></i></h3>
                                    <p class="text-muted">
                                        <a href="{{ route('comerciantes.empresas.financeiro.categorias.index', $empresa) }}" class="btn btn-primary btn-sm">
                                            Gerenciar Categorias
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Contas">Contas Gerenciais</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-bank text-success"></i></h3>
                                    <p class="text-muted">
                                        <a href="{{ route('comerciantes.empresas.financeiro.contas.index', $empresa) }}" class="btn btn-success btn-sm">
                                            Gerenciar Contas
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Relatórios">Relatórios</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-chart-line text-info"></i></h3>
                                    <p class="text-muted">
                                        <a href="#" class="btn btn-info btn-sm">
                                            Visualizar Relatórios
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Configurações">Configurações</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-cog text-warning"></i></h3>
                                    <p class="text-muted">
                                        <a href="#" class="btn btn-warning btn-sm">
                                            Configurações
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="mdi mdi-information-outline"></i> Sistema Financeiro</h5>
                                <p class="mb-0">
                                    Bem-vindo ao sistema financeiro! Aqui você pode gerenciar as categorias de conta e contas gerenciais da sua empresa.
                                    O sistema está configurado para isolar os dados por empresa, garantindo a segurança das informações.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

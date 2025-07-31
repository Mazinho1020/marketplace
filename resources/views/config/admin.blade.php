@extends('layouts.app')

@section('title', 'Painel de Configurações')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    @if($isDevCompany)
                        <i class="mdi mdi-shield-crown"></i> Painel Desenvolvedor - {{ $appName }}
                    @else
                        <i class="mdi mdi-cog"></i> Configurações - {{ $appName }}
                    @endif
                </h4>
            </div>
        </div>
    </div>

    @if($isDevCompany)
        <!-- Painel de Empresa Desenvolvedora -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-account-supervisor"></i> Gerenciamento de Clientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="mb-0">{{ count($clientes) }}</h3>
                                                <p class="mb-0">Total de Clientes</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-account-multiple font-size-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="mb-0">{{ collect($clientes)->where('config.ativo', true)->count() }}</h3>
                                                <p class="mb-0">Clientes Ativos</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-check-circle font-size-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="mb-0">{{ system_config('max_clientes', 100) }}</h3>
                                                <p class="mb-0">Limite de Clientes</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-alert-circle font-size-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Plano</th>
                                        <th>Status</th>
                                        <th>Usuários</th>
                                        <th>Expiração</th>
                                        <th>Dias Restantes</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente['id'] }}</td>
                                        <td>
                                            <strong>{{ $cliente['nome'] }}</strong><br>
                                            <small class="text-muted">{{ $cliente['config']['nome'] }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $cliente['config']['plano'] == 'premium' ? 'warning' : ($cliente['config']['plano'] == 'standard' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($cliente['config']['plano']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($cliente['config']['ativo'] && $cliente['config']['days_remaining'] > 0)
                                                <span class="badge badge-success">Ativo</span>
                                            @elseif($cliente['config']['trial_days'] > 0)
                                                <span class="badge badge-warning">Trial</span>
                                            @else
                                                <span class="badge badge-danger">Expirado</span>
                                            @endif
                                        </td>
                                        <td>{{ $cliente['config']['max_usuarios'] }}</td>
                                        <td>{{ date('d/m/Y', strtotime($cliente['config']['data_expiracao'])) }}</td>
                                        <td>
                                            @if($cliente['config']['days_remaining'] > 0)
                                                <span class="text-success">{{ $cliente['config']['days_remaining'] }} dias</span>
                                            @else
                                                <span class="text-danger">Expirado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('config.manage-client', $cliente['id']) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-cog"></i> Gerenciar
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="mdi mdi-account-off font-size-24 d-block mb-2"></i>
                                            Nenhum cliente cadastrado
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-information"></i> Informações do Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Versão:</dt>
                            <dd class="col-sm-8">{{ $appVersion }}</dd>
                            
                            <dt class="col-sm-4">Ambiente:</dt>
                            <dd class="col-sm-8">
                                <span class="badge badge-info">Desenvolvedor</span>
                            </dd>
                            
                            <dt class="col-sm-4">Total Clientes:</dt>
                            <dd class="col-sm-8">{{ count($clientes) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-tools"></i> Ações Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('config.system-status') }}" class="btn btn-info">
                                <i class="mdi mdi-chart-line"></i> Status do Sistema
                            </a>
                            <a href="{{ route('admin.config.index') }}" class="btn btn-primary">
                                <i class="mdi mdi-cog"></i> Configurações Avançadas
                            </a>
                            <button class="btn btn-success" onclick="refreshClients()">
                                <i class="mdi mdi-refresh"></i> Atualizar Lista
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Painel de Cliente -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-cog"></i> Configurações do Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            <strong>Bem-vindo ao {{ $appName }}!</strong><br>
                            Você está acessando como cliente. Para configurações avançadas, entre em contato com o suporte.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informações Básicas</h6>
                                <dl class="row">
                                    <dt class="col-sm-4">Sistema:</dt>
                                    <dd class="col-sm-8">{{ $appName }}</dd>
                                    
                                    <dt class="col-sm-4">Tipo:</dt>
                                    <dd class="col-sm-8">Cliente</dd>
                                </dl>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Suporte</h6>
                                <p>Para suporte técnico ou dúvidas:</p>
                                <a href="mailto:{{ system_config('suporte_email', 'suporte@marketplace.com') }}" class="btn btn-primary">
                                    <i class="mdi mdi-email"></i> Contatar Suporte
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($isDevCompany)
<script>
function refreshClients() {
    location.reload();
}
</script>
@endif
@endsection

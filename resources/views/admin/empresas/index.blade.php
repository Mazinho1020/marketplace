@extends('layouts.admin')

@section('title', 'Gestão de Empresas')

@php
    $pageTitle = 'Gestão de Empresas';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Empresas', 'url' => '#']
    ];
@endphp

@section('content')
<div class="container-fluid">
    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-primary mb-0">{{ number_format($stats['total']) }}</h3>
                            <p class="text-muted mb-0">Total de Empresas</p>
                        </div>
                        <div class="stats-icon text-primary">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-success mb-0">{{ number_format($stats['ativas']) }}</h3>
                            <p class="text-muted mb-0">Empresas Ativas</p>
                        </div>
                        <div class="stats-icon text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-warning mb-0">{{ number_format($stats['vencimento_proximo']) }}</h3>
                            <p class="text-muted mb-0">Vencimento Próximo</p>
                        </div>
                        <div class="stats-icon text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-info mb-0">R$ {{ number_format($stats['receita_mensal'], 2, ',', '.') }}</h3>
                            <p class="text-muted mb-0">Receita Mensal</p>
                        </div>
                        <div class="stats-icon text-info">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Filtros e Busca</h5>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nova Empresa
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.empresas.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nome, CNPJ, E-mail...">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                            <option value="suspenso" {{ request('status') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                            <option value="bloqueado" {{ request('status') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="plano" class="form-label">Plano</label>
                        <select class="form-select" id="plano" name="plano">
                            <option value="">Todos</option>
                            <option value="basico" {{ request('plano') == 'basico' ? 'selected' : '' }}>Básico</option>
                            <option value="pro" {{ request('plano') == 'pro' ? 'selected' : '' }}>Pro</option>
                            <option value="premium" {{ request('plano') == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="enterprise" {{ request('plano') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="sort_by" class="form-label">Ordenar por</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data Cadastro</option>
                            <option value="nome_fantasia" {{ request('sort_by') == 'nome_fantasia' ? 'selected' : '' }}>Nome</option>
                            <option value="data_vencimento" {{ request('sort_by') == 'data_vencimento' ? 'selected' : '' }}>Vencimento</option>
                            <option value="valor_mensalidade" {{ request('sort_by') == 'valor_mensalidade' ? 'selected' : '' }}>Valor</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                            <a href="{{ route('admin.empresas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Empresas -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Empresas Cadastradas ({{ $empresas->total() }})</h5>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.empresas.export', 'xlsx') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </a>
                <a href="{{ route('admin.empresas.export', 'pdf') }}" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($empresas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Empresa</th>
                                <th>Contato</th>
                                <th>Plano</th>
                                <th>Status</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($empresas as $empresa)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $empresa->nome_fantasia }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $empresa->razao_social }}</small>
                                        <br>
                                        <small class="text-muted">CNPJ: {{ $empresa->cnpj }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <small>{{ $empresa->email }}</small>
                                        @if($empresa->telefone)
                                            <br><small class="text-muted">{{ $empresa->telefone }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{!! $empresa->plano_badge !!}</td>
                                <td>{!! $empresa->status_badge !!}</td>
                                <td>
                                    @if($empresa->data_vencimento)
                                        <span class="{{ $empresa->vencido ? 'text-danger' : ($empresa->vencimento_proximo ? 'text-warning' : 'text-muted') }}">
                                            {{ $empresa->data_vencimento->format('d/m/Y') }}
                                        </span>
                                        @if($empresa->vencido)
                                            <br><small class="text-danger">Vencido</small>
                                        @elseif($empresa->vencimento_proximo)
                                            <br><small class="text-warning">Próximo</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($empresa->valor_mensalidade)
                                        <strong>R$ {{ number_format($empresa->valor_mensalidade, 2, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $empresa->created_at ? $empresa->created_at->format('d/m/Y') : 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.empresas.show', $empresa->id) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.empresas.edit', $empresa->id) }}" 
                                           class="btn btn-outline-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.empresas.toggle-status', $empresa->id) }}" 
                                              class="d-inline" onsubmit="return confirm('Alterar status da empresa?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-secondary btn-sm" 
                                                    title="Alterar status">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer">
                    {{ $empresas->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma empresa encontrada</h5>
                    <p class="text-muted">Não há empresas cadastradas ou que atendam aos filtros selecionados.</p>
                    <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Cadastrar primeira empresa
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-icon {
    opacity: 0.7;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.btn-group .btn {
    margin: 0 1px;
}
</style>
@endpush

@extends('comerciantes.layouts.app')

@section('title', 'Gerenciar Empresas')

@section('content')
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                Minhas Empresas
            </h1>
            <p class="text-muted mb-0">Gerencie suas unidades de negócio</p>
        </div>
        <a href="{{ route('comerciantes.empresas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nova Empresa
        </a>
    </div>

    <!-- Filtros e busca -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.empresas.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="busca" class="form-label">Buscar empresa</label>
                    <input type="text" class="form-control" id="busca" name="busca" 
                           value="{{ request('busca') }}" placeholder="Nome, CNPJ ou cidade">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="ativa" {{ request('status') == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="inativa" {{ request('status') == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        <option value="suspensa" {{ request('status') == 'suspensa' ? 'selected' : '' }}>Suspensa</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('comerciantes.empresas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de empresas -->
    <div class="row">
        @forelse($empresas as $empresa)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card h-100 shadow-sm empresa-card" data-empresa-id="{{ $empresa->id }}">
                    <!-- Badge de status -->
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-{{ $empresa->status == 'ativa' ? 'success' : ($empresa->status == 'inativa' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($empresa->status) }}
                        </span>
                    </div>

                    <div class="card-body">
                        <!-- Nome da empresa -->
                        <h5 class="card-title mb-2">
                            <a href="{{ route('comerciantes.empresas.show', $empresa) }}" 
                               class="text-decoration-none text-primary">
                                {{ $empresa->razao_social ?: $empresa->nome_fantasia ?: 'Empresa sem nome' }}
                            </a>
                        </h5>

                        <!-- Informações básicas -->
                        <div class="empresa-info">
                            @if($empresa->cnpj)
                                <p class="mb-1 small">
                                    <i class="fas fa-id-card me-2 text-muted"></i>
                                    <strong>CNPJ:</strong> {{ $empresa->cnpj }}
                                </p>
                            @endif

                            @if($empresa->cidade && $empresa->uf)
                                <p class="mb-1 small">
                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                    <strong>Local:</strong> {{ $empresa->cidade }}/{{ $empresa->uf }}
                                </p>
                            @endif

                            @if($empresa->telefone)
                                <p class="mb-1 small">
                                    <i class="fas fa-phone me-2 text-muted"></i>
                                    <strong>Telefone:</strong> {{ $empresa->telefone }}
                                </p>
                            @endif

                            @if($empresa->email)
                                <p class="mb-1 small">
                                    <i class="fas fa-envelope me-2 text-muted"></i>
                                    <strong>Email:</strong> {{ $empresa->email }}
                                </p>
                            @endif
                        </div>

                        <!-- Estatísticas rápidas -->
                        <div class="row text-center mt-3 pt-3 border-top">
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $empresa->usuarios_vinculados_count ?? 0 }}</div>
                                    <div class="stat-label">Usuários</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $empresa->produtos_count ?? 0 }}</div>
                                    <div class="stat-label">Produtos</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $empresa->pedidos_count ?? 0 }}</div>
                                    <div class="stat-label">Pedidos</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('comerciantes.empresas.show', $empresa) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                            <a href="{{ route('comerciantes.empresas.edit', $empresa) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Editar
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('comerciantes.dashboard.empresa', $empresa) }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('comerciantes.empresas.usuarios.index', $empresa) }}">
                                            <i class="fas fa-users me-2"></i>
                                            Usuários
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">
                                            <i class="fas fa-coins me-2"></i>
                                            Sistema Financeiro
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('comerciantes.empresas.destroy', $empresa) }}" 
                                              class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta empresa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>
                                                Excluir
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma empresa encontrada</h5>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['busca', 'status']))
                                Tente ajustar os filtros ou criar uma nova empresa.
                            @else
                                Comece criando sua primeira empresa.
                            @endif
                        </p>
                        <a href="{{ route('comerciantes.empresas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Criar Primeira Empresa
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginação -->
    @if($empresas->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $empresas->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
.empresa-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid rgba(0,0,0,.125);
}

.empresa-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bs-primary);
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.empresa-info .small {
    font-size: 0.85rem;
}

.empresa-info i {
    width: 16px;
    text-align: center;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit do formulário quando alterar filtros
    const filtros = document.querySelectorAll('#status');
    filtros.forEach(filtro => {
        filtro.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Atalho para busca
    const campoBusca = document.getElementById('busca');
    if (campoBusca) {
        campoBusca.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});
</script>
@endpush
@endsection

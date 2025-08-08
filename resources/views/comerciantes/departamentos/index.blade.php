@extends('comerciantes.layout')

@section('title', 'Departamentos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Departamentos</h1>
                    <p class="text-muted mb-0">Gerencie os departamentos da empresa</p>
                </div>
                <div>
                    <a href="/comerciantes/empresas/{{ $empresaId }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/comerciantes/clientes/departamentos/create?empresa_id={{ $empresaId }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Departamento
                    </a>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">{{ $stats['total'] }}</h5>
                            <p class="card-text text-muted">Total</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">{{ $stats['ativos'] }}</h5>
                            <p class="card-text text-muted">Ativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-pause-circle fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">{{ $stats['inativos'] }}</h5>
                            <p class="card-text text-muted">Inativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x text-info mb-2"></i>
                            <h5 class="card-title">{{ $stats['funcionarios'] ?? 0 }}</h5>
                            <p class="card-text text-muted">Funcionários</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/comerciantes/clientes/departamentos">
                        <input type="hidden" name="empresa_id" value="{{ $empresaId }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="busca" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="busca" name="busca" 
                                       value="{{ request('busca') }}" placeholder="Nome, código ou descrição...">
                            </div>
                            <div class="col-md-3">
                                <label for="ativo" class="form-label">Status</label>
                                <select class="form-select" id="ativo" name="ativo">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('ativo') == '1' ? 'selected' : '' }}>Ativo</option>
                                    <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="ordem" class="form-label">Ordenar por</label>
                                <select class="form-select" id="ordem" name="ordem">
                                    <option value="nome" {{ request('ordem') == 'nome' ? 'selected' : '' }}>Nome</option>
                                    <option value="codigo" {{ request('ordem') == 'codigo' ? 'selected' : '' }}>Código</option>
                                    <option value="created_at" {{ request('ordem') == 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Departamentos -->
            <div class="card">
                <div class="card-body">
                    @if($departamentos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Funcionários</th>
                                        <th>Status</th>
                                        <th>Criado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departamentos as $departamento)
                                    <tr>
                                        <td>
                                            @if($departamento->codigo)
                                                <code>{{ $departamento->codigo }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $departamento->nome }}</strong>
                                        </td>
                                        <td>
                                            @if($departamento->descricao)
                                                {{ Str::limit($departamento->descricao, 50) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $departamento->funcionarios_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if($departamento->ativo)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($departamento->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/comerciantes/clientes/departamentos/{{ $departamento->id }}" 
                                                   class="btn btn-outline-primary" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/comerciantes/clientes/departamentos/{{ $departamento->id }}/edit" 
                                                   class="btn btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmarExclusao({{ $departamento->id }})" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            {{ $departamentos->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum departamento encontrado</h5>
                            <p class="text-muted">Crie o primeiro departamento para começar</p>
                            <a href="/comerciantes/clientes/departamentos/create?empresa_id={{ $empresaId }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Departamento
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este departamento?</p>
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao(id) {
    const form = document.getElementById('formExclusao');
    form.action = `/comerciantes/clientes/departamentos/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
@endpush

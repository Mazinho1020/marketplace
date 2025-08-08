@extends('comerciantes.layout')

@section('title', 'Cargos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Cargos</h1>
                    <p class="text-muted mb-0">Gerencie os cargos da empresa</p>
                </div>
                <div>
                    <a href="/comerciantes/empresas/{{ $empresaId }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/comerciantes/clientes/cargos/create?empresa_id={{ $empresaId }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Cargo
                    </a>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-user-tie fa-2x text-primary mb-2"></i>
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
                            <h5 class="card-title">{{ $cargos->sum('funcionarios_count') ?? 0 }}</h5>
                            <p class="card-text text-muted">Funcionários</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/comerciantes/clientes/cargos">
                        <input type="hidden" name="empresa_id" value="{{ $empresaId }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="busca" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="busca" name="busca" 
                                       value="{{ request('busca') }}" placeholder="Nome, código ou descrição...">
                            </div>
                            <div class="col-md-3">
                                <label for="departamento_id" class="form-label">Departamento</label>
                                <select class="form-select" id="departamento_id" name="departamento_id">
                                    <option value="">Todos</option>
                                    @foreach($departamentos as $dept)
                                        <option value="{{ $dept->id }}" {{ request('departamento_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ativo" class="form-label">Status</label>
                                <select class="form-select" id="ativo" name="ativo">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('ativo') == '1' ? 'selected' : '' }}>Ativo</option>
                                    <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ordem" class="form-label">Ordenar</label>
                                <select class="form-select" id="ordem" name="ordem">
                                    <option value="departamento" {{ request('ordem') == 'departamento' ? 'selected' : '' }}>Departamento</option>
                                    <option value="nome" {{ request('ordem') == 'nome' ? 'selected' : '' }}>Nome</option>
                                    <option value="codigo" {{ request('ordem') == 'codigo' ? 'selected' : '' }}>Código</option>
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

            <!-- Lista de Cargos -->
            <div class="card">
                <div class="card-body">
                    @if($cargos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Departamento</th>
                                        <th>Nível</th>
                                        <th>Salário Base</th>
                                        <th>Funcionários</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cargos as $cargo)
                                    <tr>
                                        <td>
                                            @if($cargo->codigo)
                                                <code>{{ $cargo->codigo }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $cargo->nome }}</strong>
                                            @if($cargo->descricao)
                                                <br><small class="text-muted">{{ Str::limit($cargo->descricao, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cargo->departamento_nome)
                                                <span class="badge bg-primary">{{ $cargo->departamento_nome }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cargo->nivel_hierarquico)
                                                <span class="badge bg-info">Nível {{ $cargo->nivel_hierarquico }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cargo->salario_base)
                                                <span class="text-success">R$ {{ number_format($cargo->salario_base, 2, ',', '.') }}</span>
                                                @if($cargo->salario_maximo && $cargo->salario_maximo > $cargo->salario_base)
                                                    <br><small class="text-muted">até R$ {{ number_format($cargo->salario_maximo, 2, ',', '.') }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">A definir</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $cargo->funcionarios_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if($cargo->ativo)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/comerciantes/clientes/cargos/{{ $cargo->id }}" 
                                                   class="btn btn-outline-primary" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/comerciantes/clientes/cargos/{{ $cargo->id }}/edit" 
                                                   class="btn btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmarExclusao({{ $cargo->id }})" title="Excluir">
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
                            {{ $cargos->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum cargo encontrado</h5>
                            <p class="text-muted">Crie o primeiro cargo para começar</p>
                            <a href="/comerciantes/clientes/cargos/create?empresa_id={{ $empresaId }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Cargo
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
                <p>Tem certeza que deseja excluir este cargo?</p>
                <p class="text-warning"><strong>Atenção:</strong> Verifique se não há funcionários vinculados a este cargo.</p>
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
    form.action = `/comerciantes/clientes/cargos/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
@endpush

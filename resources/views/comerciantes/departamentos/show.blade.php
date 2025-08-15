@extends('layouts.comerciante')

@section('title', 'Visualizar Departamento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $departamento->nome }}</h1>
                    <p class="text-muted mb-0">
                        @if($departamento->codigo)
                        Código: <span class="badge bg-primary">{{ $departamento->codigo }}</span>
                        @endif
                        Status:
                        @if($departamento->ativo)
                        <span class="badge bg-success">Ativo</span>
                        @else
                        <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/departamentos?empresa_id={{ $departamento->empresa_id }}"
                        class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/comerciantes/clientes/departamentos/{{ $departamento->id }}/edit"
                        class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao({{ $departamento->id }})">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </div>
            </div>

            <!-- Informações Gerais -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informações Gerais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Código:</label>
                                        <p class="form-control-plaintext">
                                            {{ $departamento->codigo ?? 'Não informado' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nome:</label>
                                        <p class="form-control-plaintext">{{ $departamento->nome }}</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Descrição:</label>
                                        <p class="form-control-plaintext">
                                            {{ $departamento->descricao ?? 'Nenhuma descrição informada' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Centro de Custo:</label>
                                        <p class="form-control-plaintext">
                                            {{ $departamento->centro_custo ?? 'Não informado' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Ordem de Exibição:</label>
                                        <p class="form-control-plaintext">{{ $departamento->ordem }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Relacionado à Produção:</label>
                                        <p class="form-control-plaintext">
                                            @if($departamento->relacionado_producao)
                                            <span class="badge bg-success">Sim</span>
                                            @else
                                            <span class="badge bg-secondary">Não</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <p class="form-control-plaintext">
                                            @if($departamento->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                            @else
                                            <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-bar"></i> Estatísticas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h3 class="text-primary">{{ $stats['funcionarios'] }}</h3>
                                <p class="text-muted mb-1">Funcionários</p>
                                <small class="text-success">{{ $stats['funcionarios_ativos'] }} ativos</small>
                            </div>
                            <hr>
                            <div class="text-center mb-3">
                                <h3 class="text-info">{{ $stats['cargos'] }}</h3>
                                <p class="text-muted mb-1">Cargos</p>
                                <small class="text-success">{{ $stats['cargos_ativos'] }} ativos</small>
                            </div>
                            <hr>
                            <div class="text-center">
                                <small class="text-muted">
                                    Criado em: {{ \Carbon\Carbon::parse($departamento->created_at)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Funcionários do Departamento -->
            @if($funcionarios->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> Funcionários ({{ $funcionarios->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($funcionarios as $funcionario)
                                <tr>
                                    <td>
                                        <strong>{{ $funcionario->nome }}</strong>
                                        @if($funcionario->sobrenome)
                                        {{ $funcionario->sobrenome }}
                                        @endif
                                    </td>
                                    <td>{{ $funcionario->email ?? 'Não informado' }}</td>
                                    <td>
                                        @if($funcionario->status == 'ativo')
                                        <span class="badge bg-success">Ativo</span>
                                        @else
                                        <span class="badge bg-secondary">{{ $funcionario->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="/comerciantes/clientes/pessoas/{{ $funcionario->id }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Cargos do Departamento -->
            @if($cargos->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie"></i> Cargos ({{ $cargos->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cargos as $cargo)
                                <tr>
                                    <td><strong>{{ $cargo->nome }}</strong></td>
                                    <td>{{ Str::limit($cargo->descricao ?? 'Sem descrição', 50) }}</td>
                                    <td>
                                        @if($cargo->ativo)
                                        <span class="badge bg-success">Ativo</span>
                                        @else
                                        <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="/comerciantes/clientes/cargos/{{ $cargo->id }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
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
                @if($funcionarios->count() > 0 || $cargos->count() > 0)
                <div class="alert alert-danger">
                    <strong>Aviso:</strong> Este departamento possui
                    {{ $funcionarios->count() }} funcionário(s) e {{ $cargos->count() }} cargo(s) vinculados.
                    Não será possível excluir enquanto houver vínculos.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                @if($funcionarios->count() == 0 && $cargos->count() == 0)
                <form id="formExclusao" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
                @else
                <button type="button" class="btn btn-danger" disabled>
                    Não é possível excluir
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarExclusao(id) {
        const form = document.getElementById('formExclusao');
        if (form) {
            form.action = `/comerciantes/clientes/departamentos/${id}`;
        }

        const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
        modal.show();
    }
</script>
@endpush
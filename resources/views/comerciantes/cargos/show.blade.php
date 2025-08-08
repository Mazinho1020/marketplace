@extends('comerciantes.layout')

@section('title', 'Visualizar Cargo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $cargo->nome }}</h1>
                    <p class="text-muted mb-0">
                        @if($cargo->codigo)
                            Código: <span class="badge bg-primary">{{ $cargo->codigo }}</span>
                        @endif
                        @if($cargo->departamento_nome)
                            Departamento: <span class="badge bg-info">{{ $cargo->departamento_nome }}</span>
                        @endif
                        Status: 
                        @if($cargo->ativo)
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/cargos?empresa_id={{ $cargo->empresa_id }}" 
                       class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/comerciantes/clientes/cargos/{{ $cargo->id }}/edit" 
                       class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao({{ $cargo->id }})">
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
                                            {{ $cargo->codigo ?? 'Não informado' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nome:</label>
                                        <p class="form-control-plaintext">{{ $cargo->nome }}</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Descrição:</label>
                                        <p class="form-control-plaintext">
                                            {{ $cargo->descricao ?? 'Nenhuma descrição informada' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Departamento:</label>
                                        <p class="form-control-plaintext">
                                            @if($cargo->departamento_nome)
                                                <a href="/comerciantes/clientes/departamentos/{{ $cargo->departamento_id }}" 
                                                   class="text-decoration-none">
                                                    {{ $cargo->departamento_nome }}
                                                </a>
                                            @else
                                                Não vinculado
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nível Hierárquico:</label>
                                        <p class="form-control-plaintext">
                                            @if($cargo->nivel_hierarquico)
                                                @switch($cargo->nivel_hierarquico)
                                                    @case(1)
                                                        Nível 1 - Operacional
                                                        @break
                                                    @case(2)
                                                        Nível 2 - Supervisão
                                                        @break
                                                    @case(3)
                                                        Nível 3 - Coordenação
                                                        @break
                                                    @case(4)
                                                        Nível 4 - Gerência
                                                        @break
                                                    @case(5)
                                                        Nível 5 - Diretoria
                                                        @break
                                                    @default
                                                        Nível {{ $cargo->nivel_hierarquico }}
                                                @endswitch
                                            @else
                                                Não definido
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Salário Base:</label>
                                        <p class="form-control-plaintext">
                                            @if($cargo->salario_base)
                                                R$ {{ number_format($cargo->salario_base, 2, ',', '.') }}
                                            @else
                                                Não informado
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <p class="form-control-plaintext">
                                            @if($cargo->ativo)
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
                            <div class="text-center">
                                <small class="text-muted">
                                    Criado em: {{ \Carbon\Carbon::parse($cargo->created_at)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-bolt"></i> Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($cargo->departamento_id)
                                <a href="/comerciantes/clientes/departamentos/{{ $cargo->departamento_id }}" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-building"></i> Ver Departamento
                                </a>
                                @endif
                                <a href="/comerciantes/clientes/pessoas?cargo_id={{ $cargo->id }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-users"></i> Ver Funcionários
                                </a>
                                <a href="/comerciantes/clientes/cargos/create?departamento_id={{ $cargo->departamento_id }}" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus"></i> Novo Cargo Similar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Funcionários do Cargo -->
            @if($funcionarios->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> Funcionários neste Cargo ({{ $funcionarios->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Departamento</th>
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
                                    <td>{{ $funcionario->departamento_nome ?? 'Não definido' }}</td>
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
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita.</p>
                @if($funcionarios->count() > 0)
                <div class="alert alert-danger">
                    <strong>Aviso:</strong> Este cargo possui 
                    {{ $funcionarios->count() }} funcionário(s) vinculado(s).
                    Não será possível excluir enquanto houver vínculos.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                @if($funcionarios->count() == 0)
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
        form.action = `/comerciantes/clientes/cargos/${id}`;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
@endpush

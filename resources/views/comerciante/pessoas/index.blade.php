@extends('layouts.comerciante')

@section('title', 'Pessoas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        Pessoas
                        @if(request('tipo'))
                        - {{ ucfirst(str_replace('_', ' ', request('tipo'))) }}s
                        @endif
                    </h1>
                    <p class="text-muted mb-0">
                        Gerencie as pessoas da empresa
                        @if(request('empresa_id'))
                        (Empresa ID: {{ request('empresa_id') }})
                        @endif
                    </p>
                </div>
                <div>
                    @if(request('empresa_id'))
                    <a href="/comerciantes/empresas/{{ request('empresa_id') }}"
                        class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar à Empresa
                    </a>
                    @endif
                    <a href="/comerciantes/clientes/pessoas/create{{ request('empresa_id') ? '?empresa_id=' . request('empresa_id') : '' }}{{ request('tipo') ? (request('empresa_id') ? '&' : '?') . 'tipo=' . request('tipo') : '' }}"
                        class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Pessoa
                    </a>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">{{ $stats['total'] }}</h5>
                            <p class="card-text text-muted">Total</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-user-friends fa-2x text-success mb-2"></i>
                            <h5 class="card-title">{{ $stats['clientes'] }}</h5>
                            <p class="card-text text-muted">Clientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-user-tie fa-2x text-info mb-2"></i>
                            <h5 class="card-title">{{ $stats['funcionarios'] }}</h5>
                            <p class="card-text text-muted">Funcionários</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">{{ $stats['fornecedores'] + $stats['entregadores'] }}</h5>
                            <p class="card-text text-muted">Fornecedores/Entregadores</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/comerciantes/clientes/pessoas">
                        @if(request('empresa_id'))
                        <input type="hidden" name="empresa_id" value="{{ request('empresa_id') }}">
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome"
                                    value="{{ $filtros['nome'] ?? '' }}" placeholder="Nome da pessoa...">
                            </div>
                            <div class="col-md-2">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo">
                                    <option value="">Todos</option>
                                    <option value="cliente" {{ ($filtros['tipo'] ?? '') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                    <option value="funcionario" {{ ($filtros['tipo'] ?? '') == 'funcionario' ? 'selected' : '' }}>Funcionário</option>
                                    <option value="fornecedor" {{ ($filtros['tipo'] ?? '') == 'fornecedor' ? 'selected' : '' }}>Fornecedor</option>
                                    <option value="entregador" {{ ($filtros['tipo'] ?? '') == 'entregador' ? 'selected' : '' }}>Entregador</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="ativo" {{ ($filtros['status'] ?? '') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ ($filtros['status'] ?? '') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                    <option value="suspenso" {{ ($filtros['status'] ?? '') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="departamento_id" class="form-label">Departamento</label>
                                <select class="form-select" id="departamento_id" name="departamento_id">
                                    <option value="">Todos</option>
                                    @foreach($departamentos as $dept)
                                    <option value="{{ $dept->id }}" {{ ($filtros['departamento_id'] ?? '') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="cargo_id" class="form-label">Cargo</label>
                                <select class="form-select" id="cargo_id" name="cargo_id">
                                    <option value="">Todos</option>
                                    @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->id }}" {{ ($filtros['cargo_id'] ?? '') == $cargo->id ? 'selected' : '' }}>
                                        {{ $cargo->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Pessoas -->
            <div class="card">
                <div class="card-body">
                    @if($pessoas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Departamento</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pessoas as $pessoa)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                @if($pessoa->foto_url)
                                                <img src="{{ $pessoa->foto_url }}" alt="{{ $pessoa->nome }}" class="rounded-circle" width="32" height="32">
                                                @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                                    {{ strtoupper(substr($pessoa->nome, 0, 1)) }}{{ strtoupper(substr($pessoa->sobrenome ?? '', 0, 1)) }}
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>{{ $pessoa->nome }} {{ $pessoa->sobrenome }}</strong>
                                                @if($pessoa->nome_social)
                                                <br><small class="text-muted">"{{ $pessoa->nome_social }}"</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                        $tipos = explode(',', $pessoa->tipo);
                                        $cores = [
                                        'cliente' => 'success',
                                        'funcionario' => 'primary',
                                        'fornecedor' => 'warning',
                                        'entregador' => 'info'
                                        ];
                                        @endphp
                                        @foreach($tipos as $tipo)
                                        <span class="badge bg-{{ $cores[trim($tipo)] ?? 'secondary' }} me-1">
                                            {{ ucfirst(trim($tipo)) }}
                                        </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($pessoa->cpf_cnpj)
                                        <code>{{ $pessoa->cpf_cnpj }}</code>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pessoa->email)
                                        <a href="mailto:{{ $pessoa->email }}">{{ $pessoa->email }}</a>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pessoa->telefone)
                                        <a href="tel:{{ $pessoa->telefone }}">{{ $pessoa->telefone }}</a>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pessoa->departamento_nome)
                                        <span class="badge bg-light text-dark">{{ $pessoa->departamento_nome }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($pessoa->status)
                                        @case('ativo')
                                        <span class="badge bg-success">Ativo</span>
                                        @break
                                        @case('inativo')
                                        <span class="badge bg-secondary">Inativo</span>
                                        @break
                                        @case('suspenso')
                                        <span class="badge bg-warning">Suspenso</span>
                                        @break
                                        @default
                                        <span class="badge bg-light text-dark">{{ ucfirst($pessoa->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/comerciantes/clientes/pessoas/{{ $pessoa->id }}"
                                                class="btn btn-outline-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/comerciantes/clientes/pessoas/{{ $pessoa->id }}/edit"
                                                class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="confirmarExclusao({{ $pessoa->id }})" title="Excluir">
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
                        {{ $pessoas->withQueryString()->links() }}
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma pessoa encontrada</h5>
                        <p class="text-muted">
                            @if(array_filter($filtros))
                            Ajuste os filtros ou
                            @endif
                            crie uma nova pessoa para começar
                        </p>
                        <a href="/comerciantes/clientes/pessoas/create{{ request('empresa_id') ? '?empresa_id=' . request('empresa_id') : '' }}{{ request('tipo') ? (request('empresa_id') ? '&' : '?') . 'tipo=' . request('tipo') : '' }}"
                            class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Pessoa
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
                <p>Tem certeza que deseja excluir esta pessoa?</p>
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita e pode afetar outros registros relacionados.</p>
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
        form.action = `/comerciantes/clientes/pessoas/${id}`;

        const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
        modal.show();
    }

    // Auto-submit ao mudar filtros de tipo e status
    document.addEventListener('DOMContentLoaded', function() {
        const tipo = document.getElementById('tipo');
        const status = document.getElementById('status');
        const departamento = document.getElementById('departamento_id');
        const cargo = document.getElementById('cargo_id');

        [tipo, status, departamento, cargo].forEach(element => {
            if (element) {
                element.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    });
</script>
@endpush
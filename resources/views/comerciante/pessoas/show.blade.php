@extends('layouts.comerciante')

@section('title', 'Visualizar Pessoa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $pessoa->nome }} {{ $pessoa->sobrenome }}</h1>
                    <p class="text-muted mb-0">
                        @php
                        $tipos = explode(',', $pessoa->tipo);
                        @endphp
                        @foreach($tipos as $tipo)
                        <span class="badge bg-primary me-1">{{ ucfirst(trim($tipo)) }}</span>
                        @endforeach
                        @if($pessoa->cargo_nome)
                        <span class="badge bg-info">{{ $pessoa->cargo_nome }}</span>
                        @endif
                        @if($pessoa->departamento_nome)
                        <span class="badge bg-secondary">{{ $pessoa->departamento_nome }}</span>
                        @endif
                        Status:
                        @if($pessoa->status == 'ativo')
                        <span class="badge bg-success">Ativo</span>
                        @else
                        <span class="badge bg-warning">{{ ucfirst($pessoa->status) }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/pessoas?empresa_id={{ $pessoa->empresa_id }}"
                        class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/comerciantes/clientes/pessoas/{{ $pessoa->id }}/edit"
                        class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao({{ $pessoa->id }})">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Informações Pessoais -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user"></i> Informações Pessoais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nome Completo:</label>
                                        <p class="form-control-plaintext">
                                            {{ $pessoa->nome }} {{ $pessoa->sobrenome }}
                                            @if($pessoa->nome_social)
                                            <small class="text-muted">"{{ $pessoa->nome_social }}"</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">CPF/CNPJ:</label>
                                        <p class="form-control-plaintext">{{ $pessoa->cpf_cnpj ?? 'Não informado' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email:</label>
                                        <p class="form-control-plaintext">
                                            @if($pessoa->email)
                                            <a href="mailto:{{ $pessoa->email }}">{{ $pessoa->email }}</a>
                                            @else
                                            Não informado
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Telefone:</label>
                                        <p class="form-control-plaintext">
                                            @if($pessoa->telefone)
                                            <a href="tel:{{ $pessoa->telefone }}">{{ $pessoa->telefone }}</a>
                                            @else
                                            Não informado
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Data de Nascimento:</label>
                                        <p class="form-control-plaintext">
                                            @if($pessoa->data_nascimento)
                                            {{ \Carbon\Carbon::parse($pessoa->data_nascimento)->format('d/m/Y') }}
                                            <small class="text-muted">
                                                ({{ \Carbon\Carbon::parse($pessoa->data_nascimento)->age }} anos)
                                            </small>
                                            @else
                                            Não informado
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Gênero:</label>
                                        <div class="form-control-plaintext">
                                            @if($pessoa->genero)
                                            {{ ucfirst($pessoa->genero) }}
                                            @else
                                            <span class="text-muted">Não informado</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($pessoa->observacoes)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Observações:</label>
                                        <p class="form-control-plaintext">{{ $pessoa->observacoes }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informações Profissionais -->
                    @if(str_contains($pessoa->tipo, 'funcionario'))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-briefcase"></i> Informações Profissionais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Departamento:</label>
                                        <p class="form-control-plaintext">
                                            @if($pessoa->departamento_nome && $pessoa->departamento_id)
                                            <a href="/comerciantes/clientes/departamentos/{{ $pessoa->departamento_id }}"
                                                class="text-decoration-none">
                                                {{ $pessoa->departamento_nome }}
                                            </a>
                                            @else
                                            Não definido
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Cargo:</label>
                                        <p class="form-control-plaintext">
                                            @if($pessoa->cargo_nome && $pessoa->cargo_id)
                                            <a href="/comerciantes/clientes/cargos/{{ $pessoa->cargo_id }}"
                                                class="text-decoration-none">
                                                {{ $pessoa->cargo_nome }}
                                            </a>
                                            @else
                                            Não definido
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Data de Admissão:</label>
                                        <p class="form-control-plaintext">
                                            @if($pessoa->data_admissao)
                                            {{ \Carbon\Carbon::parse($pessoa->data_admissao)->format('d/m/Y') }}
                                            <small class="text-muted">
                                                ({{ \Carbon\Carbon::parse($pessoa->data_admissao)->diffForHumans() }})
                                            </small>
                                            @else
                                            Não informado
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Salário Atual:</label>
                                        <p class="form-control-plaintext">
                                            @if($pessoa->salario_atual)
                                            R$ {{ number_format($pessoa->salario_atual, 2, ',', '.') }}
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
                                            @if($pessoa->status == 'ativo')
                                            <span class="badge bg-success">Ativo</span>
                                            @elseif($pessoa->status == 'inativo')
                                            <span class="badge bg-secondary">Inativo</span>
                                            @elseif($pessoa->status == 'afastado')
                                            <span class="badge bg-warning">Afastado</span>
                                            @elseif($pessoa->status == 'demitido')
                                            <span class="badge bg-danger">Demitido</span>
                                            @else
                                            <span class="badge bg-light text-dark">{{ ucfirst($pessoa->status) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Endereço -->
                    @if($pessoa->endereco_principal_id)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-map-marker-alt"></i> Endereço
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Endereço Principal:</label>
                                        <p class="form-control-plaintext">
                                            <a href="/comerciantes/enderecos/{{ $pessoa->endereco_principal_id }}"
                                                class="text-decoration-none">
                                                Ver endereço completo
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Estatísticas -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informações
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <small class="text-muted">
                                <strong>Cadastrado em:</strong><br>
                                {{ \Carbon\Carbon::parse($pessoa->created_at)->format('d/m/Y H:i') }}
                            </small>
                            @if($pessoa->updated_at && $pessoa->updated_at != $pessoa->created_at)
                            <br><br>
                            <small class="text-muted">
                                <strong>Última alteração:</strong><br>
                                {{ \Carbon\Carbon::parse($pessoa->updated_at)->format('d/m/Y H:i') }}
                            </small>
                            @endif
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
                                @if($pessoa->email)
                                <a href="mailto:{{ $pessoa->email }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-envelope"></i> Enviar Email
                                </a>
                                @endif
                                @if($pessoa->telefone)
                                <a href="tel:{{ $pessoa->telefone }}"
                                    class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-phone"></i> Ligar
                                </a>
                                @endif
                                @if($pessoa->departamento_id)
                                <a href="/comerciantes/clientes/departamentos/{{ $pessoa->departamento_id }}"
                                    class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-building"></i> Ver Departamento
                                </a>
                                @endif
                                @if($pessoa->cargo_id)
                                <a href="/comerciantes/clientes/cargos/{{ $pessoa->cargo_id }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-user-tie"></i> Ver Cargo
                                </a>
                                @endif
                                <a href="/comerciantes/clientes/pessoas/create?empresa_id={{ $pessoa->empresa_id }}"
                                    class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus"></i> Nova Pessoa
                                </a>
                            </div>
                        </div>
                    </div>
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
                <p>Tem certeza que deseja excluir <strong>{{ $pessoa->nome }} {{ $pessoa->sobrenome }}</strong>?</p>
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

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir <strong>{{ $pessoa->nome }} {{ $pessoa->sobrenome }}</strong>?</p>
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
</script>
@endpush
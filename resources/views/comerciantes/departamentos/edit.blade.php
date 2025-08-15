@extends('layouts.comerciante')

@section('title', 'Editar Departamento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Editar Departamento</h1>
                    <p class="text-muted mb-0">Atualize as informações do departamento</p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/departamentos/{{ $departamento->id }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Há problemas no formulário:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="/comerciantes/clientes/departamentos/{{ $departamento->id }}" 
                                  method="POST" id="formDepartamento">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="empresa_id" value="{{ $departamento->empresa_id }}">

                                <div class="row">
                                    <!-- Informações Básicas -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="codigo" class="form-label">Código</label>
                                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                                   id="codigo" name="codigo" value="{{ old('codigo', $departamento->codigo) }}" 
                                                   placeholder="Ex: ADM, VEN, RH...">
                                            @error('codigo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Opcional - Código de identificação rápida</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                                   id="nome" name="nome" value="{{ old('nome', $departamento->nome) }}" 
                                                   placeholder="Nome do departamento" required>
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Obrigatório - Nome completo do departamento</small>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição</label>
                                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                                      id="descricao" name="descricao" rows="3" 
                                                      placeholder="Descrição detalhada do departamento">{{ old('descricao', $departamento->descricao) }}</textarea>
                                            @error('descricao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Configurações -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="centro_custo" class="form-label">Centro de Custo</label>
                                            <input type="text" class="form-control @error('centro_custo') is-invalid @enderror" 
                                                   id="centro_custo" name="centro_custo" 
                                                   value="{{ old('centro_custo', $departamento->centro_custo) }}" 
                                                   placeholder="Ex: CC001">
                                            @error('centro_custo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ordem" class="form-label">Ordem de Exibição</label>
                                            <input type="number" class="form-control @error('ordem') is-invalid @enderror" 
                                                   id="ordem" name="ordem" 
                                                   value="{{ old('ordem', $departamento->ordem) }}" 
                                                   min="0" placeholder="0">
                                            @error('ordem')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Menor número aparece primeiro na lista</small>
                                        </div>
                                    </div>

                                    <!-- Opções -->
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="relacionado_producao" name="relacionado_producao" value="1"
                                                               {{ old('relacionado_producao', $departamento->relacionado_producao) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="relacionado_producao">
                                                            Relacionado à Produção
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Marque se este departamento atua diretamente na produção</small>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="ativo" name="ativo" value="1"
                                                               {{ old('ativo', $departamento->ativo) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="ativo">
                                                            Departamento Ativo
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Departamentos inativos ficam ocultos na seleção</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-danger" onclick="confirmarExclusao({{ $departamento->id }})">
                                            <i class="fas fa-trash"></i> Excluir Departamento
                                        </button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="/comerciantes/clientes/departamentos/{{ $departamento->id }}" 
                                           class="btn btn-secondary">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="btnSalvar">
                                            <i class="fas fa-save"></i> Atualizar Departamento
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informações
                            </h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <p><strong>Campos obrigatórios:</strong></p>
                                <ul>
                                    <li>Nome do departamento</li>
                                </ul>
                                
                                <hr>
                                
                                <p><strong>Dados do sistema:</strong></p>
                                <ul>
                                    <li>ID: {{ $departamento->id }}</li>
                                    <li>Criado: {{ \Carbon\Carbon::parse($departamento->created_at)->format('d/m/Y H:i') }}</li>
                                    <li>Atualizado: {{ \Carbon\Carbon::parse($departamento->updated_at)->format('d/m/Y H:i') }}</li>
                                    <li>Status Sync: {{ $departamento->sync_status }}</li>
                                </ul>
                                
                                <hr>
                                
                                <p><strong>Vínculos:</strong></p>
                                <ul>
                                    <li>Funcionários: {{ $vinculosInfo['funcionarios'] }}</li>
                                    <li>Cargos: {{ $vinculosInfo['cargos'] }}</li>
                                </ul>

                                @if($vinculosInfo['funcionarios'] > 0 || $vinculosInfo['cargos'] > 0)
                                <div class="alert alert-warning mt-3">
                                    <small>
                                        <strong>Atenção:</strong> Este departamento possui vínculos. 
                                        A exclusão só será possível após remover todos os vínculos.
                                    </small>
                                </div>
                                @endif
                            </small>
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
                <p>Tem certeza que deseja excluir este departamento?</p>
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita.</p>
                @if($vinculosInfo['funcionarios'] > 0 || $vinculosInfo['cargos'] > 0)
                <div class="alert alert-danger">
                    <strong>Aviso:</strong> Este departamento possui 
                    {{ $vinculosInfo['funcionarios'] }} funcionário(s) e {{ $vinculosInfo['cargos'] }} cargo(s) vinculados.
                    Não será possível excluir enquanto houver vínculos.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                @if($vinculosInfo['funcionarios'] == 0 && $vinculosInfo['cargos'] == 0)
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
// Loading no botão de salvar
document.getElementById('formDepartamento').addEventListener('submit', function() {
    const btnSalvar = document.getElementById('btnSalvar');
    btnSalvar.disabled = true;
    btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
});

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

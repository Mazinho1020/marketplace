@extends('layouts.comerciante')

@section('title', 'Marcas de Produtos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Marcas de Produtos</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Marcas</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMarca">
                        <i class="fas fa-plus me-2"></i>Nova Marca
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" id="formFiltros" class="row g-3">
                        <div class="col-md-4">
                            <label for="busca" class="form-label fw-semibold">Buscar</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="busca" 
                                   name="busca" 
                                   value="{{ request('busca') }}" 
                                   placeholder="Nome da marca...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativas</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="ordenar" class="form-label fw-semibold">Ordenar por</label>
                            <select class="form-select" id="ordenar" name="ordenar">
                                <option value="nome" {{ request('ordenar') == 'nome' ? 'selected' : '' }}>Nome</option>
                                <option value="created_at" {{ request('ordenar') == 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                                <option value="produtos_count" {{ request('ordenar') == 'produtos_count' ? 'selected' : '' }}>Qtd. Produtos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('comerciantes.marcas.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Marcas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-award text-primary me-2"></i>
                        Lista de Marcas ({{ $marcas->total() ?? $marcas->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($marcas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Marca</th>
                                        <th>Descrição</th>
                                        <th class="text-center">Produtos</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Data Criação</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marcas as $marca)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-award text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">{{ $marca->nome }}</h6>
                                                        @if($marca->slug)
                                                            <small class="text-muted">{{ $marca->slug }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($marca->descricao)
                                                    <span class="text-muted">{{ Str::limit($marca->descricao, 50) }}</span>
                                                @else
                                                    <span class="text-muted fst-italic">Sem descrição</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info fs-6">
                                                    {{ $marca->produtos_count ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($marca->ativo)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Ativa
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-pause-circle me-1"></i>Inativa
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center text-muted">
                                                {{ $marca->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-sm" 
                                                            onclick="editarMarca({{ $marca->id }})"
                                                            title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-{{ $marca->ativo ? 'warning' : 'success' }} btn-sm" 
                                                            onclick="toggleStatus({{ $marca->id }})"
                                                            title="{{ $marca->ativo ? 'Desativar' : 'Ativar' }}">
                                                        <i class="fas fa-{{ $marca->ativo ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                    @if($marca->produtos_count == 0)
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                onclick="excluirMarca({{ $marca->id }})"
                                                                title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        @if(method_exists($marcas, 'hasPages') && $marcas->hasPages())
                            <div class="card-footer bg-white">
                                {{ $marcas->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-award text-muted fs-1 mb-3"></i>
                            <h5 class="text-muted">Nenhuma marca encontrada</h5>
                            <p class="text-muted mb-4">Comece criando sua primeira marca de produto</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMarca">
                                <i class="fas fa-plus me-2"></i>Criar Primeira Marca
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Marca -->
<div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMarcaLabel">
                    <i class="fas fa-award text-primary me-2"></i>
                    <span id="modalTitulo">Nova Marca</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formMarca" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label fw-semibold">Nome da Marca *</label>
                        <input type="text" 
                               class="form-control" 
                               id="nome" 
                               name="nome" 
                               required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-semibold">Slug</label>
                        <input type="text" 
                               class="form-control" 
                               id="slug" 
                               name="slug">
                        <small class="text-muted">Deixe vazio para gerar automaticamente</small>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label fw-semibold">Descrição</label>
                        <textarea class="form-control" 
                                  id="descricao" 
                                  name="descricao" 
                                  rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="ativo" 
                                   name="ativo" 
                                   value="1" 
                                   checked>
                            <label class="form-check-label fw-semibold" for="ativo">
                                Marca Ativa
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit do filtro
    $('#busca, #status, #ordenar').on('change', function() {
        $('#formFiltros').submit();
    });

    // Limpar formulário ao fechar modal
    $('#modalMarca').on('hidden.bs.modal', function() {
        $('#formMarca')[0].reset();
        $('#formMarca').attr('action', '{{ route("comerciantes.marcas.store") }}');
        $('#methodField').html('');
        $('#modalTitulo').text('Nova Marca');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

    // Submit do formulário
    $('#formMarca').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = $(this).attr('action');
        
        // Limpar erros anteriores
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#modalMarca').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.message || 'Erro ao salvar marca');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        const input = $(`#${field}`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    });
                } else {
                    toastr.error('Erro ao salvar marca');
                }
            }
        });
    });
});

// Função para editar marca
function editarMarca(id) {
    $.get(`{{ route('comerciantes.marcas.index') }}/${id}`, function(marca) {
        $('#modalTitulo').text('Editar Marca');
        $('#formMarca').attr('action', `{{ route('comerciantes.marcas.index') }}/${id}`);
        $('#methodField').html('@method("PUT")');
        
        $('#nome').val(marca.nome);
        $('#slug').val(marca.slug);
        $('#descricao').val(marca.descricao);
        $('#ativo').prop('checked', marca.ativo);
        
        $('#modalMarca').modal('show');
    });
}

// Função para toggle status
function toggleStatus(id) {
    $.ajax({
        url: `{{ route('comerciantes.marcas.index') }}/${id}/toggle-status`,
        type: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Erro ao alterar status');
        }
    });
}

// Função para excluir marca
function excluirMarca(id) {
    if (confirm('Tem certeza que deseja excluir esta marca?')) {
        $.ajax({
            url: `{{ route('comerciantes.marcas.index') }}/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Erro ao excluir marca');
            }
        });
    }
}
</script>
@endpush

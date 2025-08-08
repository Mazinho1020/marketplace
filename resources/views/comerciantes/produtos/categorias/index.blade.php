@extends('comerciantes.layouts.app')

@section('title', 'Categorias de Produtos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Categorias de Produtos</h4>
                <div class="page-title-right">
                    <a href="{{ route('comerciantes.produtos.categorias.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Nova Categoria
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="search-box me-2 mb-2 d-inline-block">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar categorias...">
                                    <i class="bx bx-search-alt search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <select class="form-select d-inline-block w-auto" id="statusFilter">
                                    <option value="">Todos os status</option>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Subcategorias</th>
                                    <th>Produtos</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="categoriasList">
                                @forelse($categorias as $categoria)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($categoria->icone)
                                                <i class="{{ $categoria->icone }} me-2" style="color: {{ $categoria->cor ?? '#495057' }}"></i>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $categoria->nome }}</h6>
                                                @if($categoria->categoria_pai_id)
                                                    <small class="text-muted">Sub de: {{ $categoria->categoriaPai->nome ?? 'N/A' }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($categoria->descricao ?? 'Sem descrição', 50) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-info">{{ $categoria->subcategorias_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-primary">{{ $categoria->produtos_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($categoria->ativo)
                                            <span class="badge badge-soft-success">Ativo</span>
                                        @else
                                            <span class="badge badge-soft-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('comerciantes.produtos.categorias.edit', $categoria->id) }}">
                                                    <i class="bx bx-edit-alt me-2"></i> Editar
                                                </a>
                                                @if($categoria->ativo)
                                                    <a class="dropdown-item text-warning" href="#" onclick="toggleStatus({{ $categoria->id }}, false)">
                                                        <i class="bx bx-pause-circle me-2"></i> Desativar
                                                    </a>
                                                @else
                                                    <a class="dropdown-item text-success" href="#" onclick="toggleStatus({{ $categoria->id }}, true)">
                                                        <i class="bx bx-play-circle me-2"></i> Ativar
                                                    </a>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao({{ $categoria->id }})">
                                                    <i class="bx bx-trash me-2"></i> Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-folder-open font-size-24 d-block mb-2"></i>
                                            Nenhuma categoria encontrada
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($categorias->hasPages())
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="pagination-wrap hstack gap-2 justify-content-center">
                                {{ $categorias->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1" role="dialog" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta categoria?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita. Todos os produtos associados a esta categoria perderão a associação.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir</button>
            </div>
        </div>
    </div>
</div>

<form id="formExcluir" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
let categoriaParaExcluir = null;

function confirmarExclusao(id) {
    categoriaParaExcluir = id;
    $('#modalConfirmacao').modal('show');
}

document.getElementById('btnConfirmarExclusao').addEventListener('click', function() {
    if (categoriaParaExcluir) {
        const form = document.getElementById('formExcluir');
        form.action = `{{ route('comerciantes.produtos.categorias.index') }}/${categoriaParaExcluir}`;
        form.submit();
    }
});

function toggleStatus(id, ativo) {
    // Implementar toggle de status via AJAX se necessário
    console.log('Toggle status:', id, ativo);
}

// Busca em tempo real
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#categoriasList tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Filtro por status
document.getElementById('statusFilter').addEventListener('change', function(e) {
    const status = e.target.value;
    const rows = document.querySelectorAll('#categoriasList tr');
    
    rows.forEach(row => {
        if (!status) {
            row.style.display = '';
        } else {
            const badge = row.querySelector('.badge');
            if (badge) {
                const isActive = badge.textContent.trim() === 'Ativo';
                const shouldShow = (status === 'ativo' && isActive) || (status === 'inativo' && !isActive);
                row.style.display = shouldShow ? '' : 'none';
            }
        }
    });
});
</script>
@endpush

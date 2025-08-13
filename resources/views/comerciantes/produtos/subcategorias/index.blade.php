@extends('layouts.comerciante')

@section('title', 'Subcategorias de Produtos')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sitemap"></i> Subcategorias de Produtos
        </h1>
        <div>
            <a href="{{ route('comerciantes.produtos.subcategorias.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Subcategoria
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtros
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.produtos.subcategorias.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="categoria_id">Categoria</label>
                        <select name="categoria_id" id="categoria_id" class="form-control">
                            <option value="">Todas as categorias</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" 
                                    {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="parent_id">Nível</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">Todos os níveis</option>
                            <option value="0" {{ request('parent_id') === '0' ? 'selected' : '' }}>
                                Principais (sem pai)
                            </option>
                            @foreach($subcategoriasPai as $pai)
                                <option value="{{ $pai->id }}" 
                                    {{ request('parent_id') == $pai->id ? 'selected' : '' }}>
                                    Filhas de: {{ $pai->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="ativo">Status</label>
                        <select name="ativo" id="ativo" class="form-control">
                            <option value="">Todos</option>
                            <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativas</option>
                            <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="busca">Buscar</label>
                        <input type="text" name="busca" id="busca" class="form-control" 
                               placeholder="Nome ou descrição..." value="{{ request('busca') }}">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('comerciantes.produtos.subcategorias.index') }}" 
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Subcategorias -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Lista de Subcategorias
                <small class="text-muted">({{ $subcategorias->total() }} encontradas)</small>
            </h6>
        </div>
        <div class="card-body">
            @if($subcategorias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Ordem</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Hierarquia</th>
                                <th>Produtos</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subcategorias as $subcategoria)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge badge-light">{{ $subcategoria->ordem }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($subcategoria->icone)
                                                <i class="{{ $subcategoria->icone }} mr-2 text-primary"></i>
                                            @endif
                                            <div>
                                                <strong>{{ $subcategoria->nome }}</strong>
                                                @if($subcategoria->descricao)
                                                    <br><small class="text-muted">{{ Str::limit($subcategoria->descricao, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $subcategoria->categoria->nome }}</span>
                                    </td>
                                    <td>
                                        @if($subcategoria->parent)
                                            <small class="text-muted">
                                                <i class="fas fa-level-up-alt"></i> {{ $subcategoria->parent->nome }}
                                            </small>
                                        @else
                                            <span class="badge badge-primary">Principal</span>
                                        @endif
                                        
                                        @if($subcategoria->children->count() > 0)
                                            <br><small class="text-success">
                                                <i class="fas fa-level-down-alt"></i> {{ $subcategoria->children->count() }} filhas
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $totalProdutos = $subcategoria->produtos->count();
                                            $produtosAtivos = $subcategoria->produtos->where('ativo', true)->count();
                                        @endphp
                                        
                                        @if($totalProdutos > 0)
                                            <span class="badge badge-success">{{ $totalProdutos }}</span>
                                            @if($produtosAtivos < $totalProdutos)
                                                <small class="text-muted d-block">{{ $produtosAtivos }} ativos</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-ativo" 
                                                   id="ativo_{{ $subcategoria->id }}" 
                                                   data-id="{{ $subcategoria->id }}"
                                                   {{ $subcategoria->ativo ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="ativo_{{ $subcategoria->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('comerciantes.produtos.subcategorias.show', $subcategoria) }}" 
                                               class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comerciantes.produtos.subcategorias.edit', $subcategoria) }}" 
                                               class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($subcategoria->podeSerDeletada())
                                                <form action="{{ route('comerciantes.produtos.subcategorias.destroy', $subcategoria) }}" 
                                                      method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled title="Não pode ser excluída">
                                                    <i class="fas fa-lock"></i>
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
                <div class="d-flex justify-content-center">
                    {{ $subcategorias->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma subcategoria encontrada</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['categoria_id', 'parent_id', 'ativo', 'busca']))
                            Tente ajustar os filtros ou 
                            <a href="{{ route('comerciantes.produtos.subcategorias.index') }}">remover todos os filtros</a>.
                        @else
                            Comece criando sua primeira subcategoria.
                        @endif
                    </p>
                    <a href="{{ route('comerciantes.produtos.subcategorias.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Subcategoria
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle ativo via AJAX
    $('.toggle-ativo').change(function() {
        const id = $(this).data('id');
        const ativo = $(this).is(':checked');
        
        $.ajax({
            url: `/comerciantes/produtos/subcategorias/${id}/toggle-ativo`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(ativo ? 'Subcategoria ativada!' : 'Subcategoria desativada!');
                }
            },
            error: function() {
                toastr.error('Erro ao alterar status da subcategoria');
                // Reverter o checkbox
                $(`#ativo_${id}`).prop('checked', !ativo);
            }
        });
    });

    // Confirmação de exclusão
    $('.form-delete').submit(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Confirmar exclusão?',
            text: 'Esta ação não pode ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // Filtro dinâmico por categoria
    $('#categoria_id').change(function() {
        const categoriaId = $(this).val();
        const parentSelect = $('#parent_id');
        
        if (categoriaId) {
            $.ajax({
                url: '/comerciantes/produtos/subcategorias/principais-por-categoria',
                data: { categoria_id: categoriaId },
                success: function(subcategorias) {
                    parentSelect.empty();
                    parentSelect.append('<option value="">Todos os níveis</option>');
                    parentSelect.append('<option value="0">Principais (sem pai)</option>');
                    
                    subcategorias.forEach(function(sub) {
                        parentSelect.append(`<option value="${sub.id}">Filhas de: ${sub.nome}</option>`);
                    });
                }
            });
        } else {
            parentSelect.empty();
            parentSelect.append('<option value="">Todos os níveis</option>');
            parentSelect.append('<option value="0">Principais (sem pai)</option>');
        }
    });
});
</script>
@endpush

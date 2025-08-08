@extends('comerciantes.layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container-fluid py-4">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-box text-primary me-2"></i>
                Produtos
            </h1>
            <p class="text-muted mb-0">Gerencie o catálogo de produtos da sua empresa</p>
        </div>
        <div>
            <a href="{{ route('comerciantes.produtos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Novo Produto
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.produtos.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-control" 
                           placeholder="Nome, SKU ou código de barras..." 
                           value="{{ request('busca') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas as categorias</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" 
                                    {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponivel" {{ request('status') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="indisponivel" {{ request('status') == 'indisponivel' ? 'selected' : '' }}>Indisponível</option>
                        <option value="pausado" {{ request('status') == 'pausado' ? 'selected' : '' }}>Pausado</option>
                        <option value="esgotado" {{ request('status') == 'esgotado' ? 'selected' : '' }}>Esgotado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Filtros</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="estoque_baixo" 
                               id="estoque_baixo" {{ request('estoque_baixo') ? 'checked' : '' }}>
                        <label class="form-check-label" for="estoque_baixo">
                            Estoque baixo
                        </label>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Links rápidos -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('comerciantes.produtos.categorias.index') }}" class="card text-decoration-none border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-2x text-primary mb-2"></i>
                    <h6 class="card-title mb-0">Categorias</h6>
                    <small class="text-muted">Gerenciar categorias</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('comerciantes.produtos.marcas.index') }}" class="card text-decoration-none border-success">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x text-success mb-2"></i>
                    <h6 class="card-title mb-0">Marcas</h6>
                    <small class="text-muted">Gerenciar marcas</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h6 class="card-title mb-0">Estoque Baixo</h6>
                    <small class="text-muted">
                        {{ $produtos->where('estoque_baixo', true)->count() }} produtos
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                    <h6 class="card-title mb-0">Total</h6>
                    <small class="text-muted">{{ $produtos->total() }} produtos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Produtos
            </h5>
        </div>
        <div class="card-body p-0">
            @if($produtos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Imagem</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>SKU</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produtos as $produto)
                                <tr>
                                    <td>
                                        <img src="{{ $produto->url_imagem_principal }}" 
                                             alt="{{ $produto->nome }}" 
                                             class="img-thumbnail" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $produto->nome }}</strong>
                                            @if($produto->destaque)
                                                <span class="badge bg-warning ms-1">Destaque</span>
                                            @endif
                                            @if($produto->estoque_baixo)
                                                <span class="badge bg-danger ms-1">Estoque Baixo</span>
                                            @endif
                                        </div>
                                        @if($produto->descricao_curta)
                                            <small class="text-muted">{{ \Str::limit($produto->descricao_curta, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($produto->categoria)
                                            <span class="badge bg-secondary">{{ $produto->categoria->nome }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $produto->sku ?: '-' }}</code>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ $produto->preco_venda_formatado }}</strong>
                                    </td>
                                    <td>
                                        @if($produto->controla_estoque)
                                            <span class="badge {{ $produto->estoque_baixo ? 'bg-danger' : 'bg-success' }}">
                                                {{ number_format($produto->estoque_atual, 0) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Não controlado</span>
                                        @endif
                                    </td>
                                    <td>
                                        {!! $produto->status_badge !!}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('comerciantes.produtos.show', $produto) }}" 
                                               class="btn btn-outline-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comerciantes.produtos.edit', $produto) }}" 
                                               class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Excluir" data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $produto->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de Exclusão -->
                                        <div class="modal fade" id="deleteModal{{ $produto->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Excluir Produto</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Tem certeza que deseja excluir o produto <strong>{{ $produto->nome }}</strong>?</p>
                                                        <p class="text-muted small">Esta ação não pode ser desfeita.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('comerciantes.produtos.destroy', $produto) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Excluir</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer">
                    {{ $produtos->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum produto encontrado</h5>
                    <p class="text-muted">Que tal começar criando seu primeiro produto?</p>
                    <a href="{{ route('comerciantes.produtos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Criar Primeiro Produto
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit do formulário de filtros quando mudar categoria ou status
    document.querySelectorAll('select[name="categoria_id"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush
@endsection

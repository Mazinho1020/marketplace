@extends('layouts.comerciante')

@section('title', 'Kits/Combos de Produtos')

@section('styles')
<style>
    .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 1.5rem; }
    .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 0.375rem 0.375rem 0 0 !important; }
    .kit-card { 
        border-left: 4px solid #667eea; 
        transition: all 0.3s ease; 
        cursor: pointer;
    }
    .kit-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        border-left-color: #764ba2;
    }
    .kit-price { font-size: 1.4em; font-weight: bold; color: #28a745; }
    .kit-savings { color: #dc3545; font-weight: 600; }
    .kit-items-count { background: #f8f9fa; border-radius: 50px; padding: 0.25rem 0.75rem; font-size: 0.875em; }
    .filter-card { background: #f8f9fa; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem; }
    .btn-kit { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; }
    .btn-kit:hover { background: linear-gradient(135deg, #218838 0%, #1e7e34 100%); color: white; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-boxes text-success me-2"></i>
                Kits/Combos de Produtos
            </h1>
            <p class="text-muted mb-0">Gerencie kits e combos de produtos para aumentar suas vendas</p>
        </div>
        
        <div class="btn-group">
            <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Produtos
            </a>
            <a href="{{ route('comerciantes.produtos.kits.create') }}" class="btn btn-kit">
                <i class="fas fa-plus me-1"></i> Novo Kit
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar Kit</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome, SKU ou código...">
                </div>
            </div>
            
            <div class="col-md-3">
                <label for="categoria_id" class="form-label">Categoria</label>
                <select class="form-select" id="categoria_id" name="categoria_id">
                    <option value="">Todas as categorias</option>
                    @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nome }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos os status</option>
                    <option value="disponivel" {{ request('status') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                    <option value="indisponivel" {{ request('status') == 'indisponivel' ? 'selected' : '' }}>Indisponível</option>
                    <option value="pausado" {{ request('status') == 'pausado' ? 'selected' : '' }}>Pausado</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('comerciantes.produtos.kits.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-success fs-3">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h5 class="card-title mb-1">{{ $kits->total() }}</h5>
                    <p class="card-text text-muted">Total de Kits</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-primary fs-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="card-title mb-1">{{ $kits->where('status', 'disponivel')->count() }}</h5>
                    <p class="card-text text-muted">Kits Ativos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-warning fs-3">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                    <h5 class="card-title mb-1">{{ $kits->where('status', 'indisponivel')->count() }}</h5>
                    <p class="card-text text-muted">Kits Pausados</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-info fs-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="card-title mb-1">
                        R$ {{ number_format($kits->where('status', 'disponivel')->avg('preco_venda') ?: 0, 2, ',', '.') }}
                    </h5>
                    <p class="card-text text-muted">Preço Médio</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Kits -->
    @if($kits->count() > 0)
        <div class="row">
            @foreach($kits as $kit)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card kit-card" onclick="window.location='{{ route('comerciantes.produtos.kits.show', $kit) }}'">
                    <div class="card-body">
                        @if($kit->imagem_principal)
                        <div class="mb-3 text-center">
                            <img src="{{ asset($kit->imagem_principal) }}" alt="{{ $kit->nome }}" class="img-fluid rounded shadow-sm" style="max-height: 120px;">
                        </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">{{ $kit->nome }}</h5>
                                @if($kit->sku)
                                <small class="text-muted">SKU: {{ $kit->sku }}</small>
                                @endif
                            </div>
                            <div class="ms-3">
                                <span class="badge {{ $kit->status == 'disponivel' ? 'bg-success' : ($kit->status == 'indisponivel' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($kit->status) }}
                                </span>
                            </div>
                        </div>

                        @if($kit->descricao)
                        <p class="card-text text-muted small mb-3">{{ Str::limit($kit->descricao, 80) }}</p>
                        @endif

                        <!-- Preço e Economia -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="kit-price">R$ {{ number_format($kit->preco_venda, 2, ',', '.') }}</div>
                                @php
                                    $precoTotalItens = \App\Models\ProdutoKit::calcularPrecoTotalKit($kit->id);
                                    $economia = $precoTotalItens - $kit->preco_venda;
                                @endphp
                                @if($economia > 0)
                                <small class="kit-savings">
                                    Economiza R$ {{ number_format($economia, 2, ',', '.') }}
                                    ({{ number_format(($economia / $precoTotalItens) * 100, 1) }}%)
                                </small>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="kit-items-count">
                                    <i class="fas fa-cube me-1"></i>
                                    {{ $kit->kits_itens_count }} {{ $kit->kits_itens_count == 1 ? 'item' : 'itens' }}
                                </div>
                            </div>
                        </div>

                        <!-- Categoria e Marca -->
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                @if($kit->categoria)
                                    <i class="fas fa-tag me-1"></i>{{ $kit->categoria->nome }}
                                @endif
                                @if($kit->marca)
                                    | <i class="fas fa-trademark me-1"></i>{{ $kit->marca->nome }}
                                @endif
                            </small>
                            <div class="btn-group btn-group-sm" onclick="event.stopPropagation()">
                                <a href="{{ route('comerciantes.produtos.kits.edit', $kit) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        onclick="confirmarExclusao('{{ $kit->id }}', '{{ $kit->nome }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="d-flex justify-content-center">
            {{ $kits->withQueryString()->links() }}
        </div>

    @else
        <!-- Estado Vazio -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-boxes fa-4x text-muted"></i>
            </div>
            <h4 class="text-muted mb-3">Nenhum kit encontrado</h4>
            @if(request()->hasAny(['search', 'categoria_id', 'status']))
                <p class="text-muted mb-4">Tente ajustar os filtros para encontrar kits.</p>
                <a href="{{ route('comerciantes.produtos.kits.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times me-1"></i> Limpar Filtros
                </a>
            @else
                <p class="text-muted mb-4">Crie seu primeiro kit combinando produtos para oferecer mais valor aos seus clientes.</p>
            @endif
            <a href="{{ route('comerciantes.produtos.kits.create') }}" class="btn btn-kit">
                <i class="fas fa-plus me-1"></i> Criar Primeiro Kit
            </a>
        </div>
    @endif
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o kit <strong id="kitNome"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita. O kit será permanentemente removido do sistema.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Excluir Kit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao(kitId, kitNome) {
    document.getElementById('kitNome').textContent = kitNome;
    document.getElementById('deleteForm').action = `/comerciantes/produtos/kits/${kitId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush

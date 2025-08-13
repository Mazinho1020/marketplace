@extends('layouts.comerciante')

@section('title', 'Visualizar Configuração de Produto')

@section('styles')
<style>
    .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 1.5rem; }
    .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 0.375rem 0.375rem 0 0 !important; }
    .badge { font-size: 0.875em; }
    .info-row { border-bottom: 1px solid #e9ecef; padding: 0.75rem 0; }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-weight: 600; color: #495057; }
    .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
    .item-card { border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1rem; background: #f8f9fa; }
    .item-price { font-size: 1.2em; font-weight: bold; color: #28a745; }
    .btn-back { margin-right: 0.5rem; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-cog text-primary me-2"></i>
                Configuração: {{ $configuracao->nome }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.index') }}">Produtos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.configuracoes.index') }}">Configurações</a></li>
                    <li class="breadcrumb-item active">{{ $configuracao->nome }}</li>
                </ol>
            </nav>
        </div>
        
        <div class="btn-group">
            <a href="{{ route('comerciantes.produtos.configuracoes.index') }}" class="btn btn-outline-secondary btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <a href="{{ route('comerciantes.produtos.configuracoes.edit', $configuracao) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-1"></i> Excluir
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Informações da Configuração -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-label">Nome</div>
                        <div>{{ $configuracao->nome }}</div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Tipo</div>
                        <div>
                            <span class="badge bg-info">{{ $configuracao->tipo_descricao }}</span>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Produto</div>
                        <div>
                            @if($configuracao->produto)
                                <a href="{{ route('comerciantes.produtos.show', $configuracao->produto) }}" class="text-decoration-none">
                                    {{ $configuracao->produto->nome }}
                                </a>
                            @else
                                <span class="text-muted">Todos os produtos</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div>
                            <span class="badge {{ $configuracao->ativo ? 'bg-success' : 'bg-danger' }}">
                                {{ $configuracao->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Obrigatório</div>
                        <div>
                            <span class="badge {{ $configuracao->obrigatorio ? 'bg-warning' : 'bg-secondary' }}">
                                {{ $configuracao->obrigatorio ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Múltipla Seleção</div>
                        <div>
                            <span class="badge {{ $configuracao->permite_multiplos ? 'bg-info' : 'bg-secondary' }}">
                                {{ $configuracao->permite_multiplos ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                    </div>
                    
                    @if($configuracao->permite_multiplos && $configuracao->qtd_maxima)
                    <div class="info-row">
                        <div class="info-label">Máximo de Seleções</div>
                        <div>{{ $configuracao->qtd_maxima }}</div>
                    </div>
                    @endif
                    
                    @if($configuracao->descricao)
                    <div class="info-row">
                        <div class="info-label">Descrição</div>
                        <div>{{ $configuracao->descricao }}</div>
                    </div>
                    @endif
                    
                    <div class="info-row">
                        <div class="info-label">Total de Itens</div>
                        <div>
                            <span class="badge bg-primary">{{ $configuracao->itens->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Criada em</div>
                        <div>{{ $configuracao->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Atualizada em</div>
                        <div>{{ $configuracao->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens da Configuração -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Itens da Configuração ({{ $configuracao->itens->count() }})
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus me-1"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-body">
                    @if($configuracao->itens->count() > 0)
                        <div class="items-grid">
                            @foreach($configuracao->itens->sortBy('ordem') as $item)
                            <div class="item-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $item->nome }}</h6>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                onclick="editItem({{ $item->id }}, {{ json_encode($item->nome) }}, {{ $item->valor_adicional ?? 0 }}, {{ json_encode($item->descricao ?? '') }}, {{ $item->ordem }}, {{ $item->ativo ? 'true' : 'false' }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteItem({{ $item->id }}, {{ json_encode($item->nome) }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="item-price">
                                        {{ ($item->valor_adicional ?? 0) > 0 ? '+' : '' }}R$ {{ number_format($item->valor_adicional ?? 0, 2, ',', '.') }}
                                    </span>
                                </div>
                                
                                @if($item->descricao)
                                <p class="text-muted small mb-2">{{ $item->descricao }}</p>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Ordem: {{ $item->ordem }}</small>
                                    <span class="badge {{ $item->ativo ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum item encontrado</h5>
                            <p class="text-muted">Adicione itens para esta configuração.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="fas fa-plus me-1"></i> Adicionar Primeiro Item
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Item -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('comerciantes.produtos.configuracoes.itens.store', $configuracao) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="valor_adicional" class="form-label">Preço Adicional</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="valor_adicional" name="valor_adicional" 
                                   step="0.01" value="0.00">
                        </div>
                        <small class="form-text text-muted">Valor adicional ao preço base do produto</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ordem" class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" id="ordem" name="ordem" value="{{ $configuracao->itens->count() + 1 }}">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" checked>
                        <label class="form-check-label" for="ativo">
                            Item ativo
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Item -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editItemForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_valor_adicional" class="form-label">Preço Adicional</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="edit_valor_adicional" name="valor_adicional" step="0.01">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_descricao" name="descricao" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_ordem" class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" id="edit_ordem" name="ordem">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_ativo" name="ativo">
                        <label class="form-check-label" for="edit_ativo">
                            Item ativo
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Excluir Configuração -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a configuração <strong>{{ $configuracao->nome }}</strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita. Todos os itens da configuração também serão excluídos.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('comerciantes.produtos.configuracoes.destroy', $configuracao) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir Configuração</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir Item -->
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o item <strong id="deleteItemName"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteItemForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir Item</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Garantir que as funções estejam disponíveis globalmente
window.editItem = function(id, nome, preco, descricao, ordem, ativo) {
    console.log('Editando item:', {id, nome, preco, descricao, ordem, ativo});
    
    try {
        document.getElementById('edit_nome').value = nome || '';
        document.getElementById('edit_valor_adicional').value = preco || 0;
        document.getElementById('edit_descricao').value = descricao || '';
        document.getElementById('edit_ordem').value = ordem || 1;
        document.getElementById('edit_ativo').checked = (ativo === true || ativo === 'true');
        
        const form = document.getElementById('editItemForm');
        form.action = `/comerciantes/produtos/configuracoes/{{ $configuracao->id }}/itens/${id}`;
        
        const modal = new bootstrap.Modal(document.getElementById('editItemModal'));
        modal.show();
    } catch (error) {
        console.error('Erro ao abrir modal de edição:', error);
        alert('Erro ao abrir modal de edição. Verifique o console.');
    }
};

window.deleteItem = function(id, nome) {
    console.log('Excluindo item:', {id, nome});
    
    try {
        document.getElementById('deleteItemName').textContent = nome;
        
        const form = document.getElementById('deleteItemForm');
        form.action = `/comerciantes/produtos/configuracoes/{{ $configuracao->id }}/itens/${id}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
        modal.show();
    } catch (error) {
        console.error('Erro ao abrir modal de exclusão:', error);
        alert('Erro ao abrir modal de exclusão. Verifique o console.');
    }
};

// Verificar se Bootstrap está carregado
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap não está carregado!');
    } else {
        console.log('Bootstrap carregado com sucesso');
    }
});
</script>
@endpush

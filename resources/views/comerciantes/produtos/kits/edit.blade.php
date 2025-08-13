@extends('layouts.comerciante')

@section('title', 'Editar Kit/Combo')

@section('styles')
<style>
    .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 1.5rem; }
    .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 0.375rem 0.375rem 0 0 !important; }
    .form-label { font-weight: 600; color: #374151; }
    .btn-kit { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; }
    .btn-kit:hover { background: linear-gradient(135deg, #218838 0%, #1e7e34 100%); color: white; }
    
    .kit-items-container { 
        background: #f8f9fa; 
        border-radius: 0.5rem; 
        padding: 1.5rem; 
        margin: 1.5rem 0;
        border: 2px dashed #dee2e6;
    }
    
    .item-card { 
        background: white; 
        border-radius: 0.375rem; 
        padding: 1rem; 
        margin-bottom: 1rem; 
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .item-card:hover { box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1); }
    
    .item-remove-btn { 
        position: absolute; 
        top: -8px; 
        right: -8px; 
        width: 24px; 
        height: 24px; 
        border-radius: 50%; 
        border: none;
        background: #dc3545;
        color: white;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .produto-search-result {
        cursor: pointer;
        padding: 0.75rem;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s ease;
    }
    
    .produto-search-result:hover { background-color: #f8f9fa; }
    .produto-search-result:last-child { border-bottom: none; }
    
    .preco-calculation {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 0.375rem;
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .preco-item { 
        display: flex; 
        justify-content: space-between; 
        padding: 0.25rem 0; 
    }
    
    .preco-total { 
        font-weight: bold; 
        font-size: 1.1em; 
        border-top: 1px solid #b3d9ff; 
        padding-top: 0.5rem; 
        margin-top: 0.5rem; 
    }
    
    .economia-highlight { 
        color: #28a745; 
        font-weight: bold; 
    }
    
    .edit-badge {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875em;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-edit text-warning me-2"></i>
                Editar Kit/Combo
                <span class="edit-badge ms-2">
                    <i class="fas fa-pencil-alt me-1"></i>
                    Editando
                </span>
            </h1>
            <p class="text-muted mb-0">Modificar configurações e produtos do kit "{{ $kit->nome }}"</p>
        </div>
        
        <div class="btn-group">
            <a href="{{ route('comerciantes.produtos.kits.show', $kit) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-1"></i> Visualizar
            </a>
            <a href="{{ route('comerciantes.produtos.kits.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar aos Kits
            </a>
        </div>
    </div>

    <form id="kitForm" action="{{ route('comerciantes.produtos.kits.update', $kit) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Informações Básicas -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações Básicas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nome" class="form-label">Nome do Kit *</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" name="nome" value="{{ old('nome', $kit->nome) }}" required 
                                       placeholder="Ex: Kit Café da Manhã Premium">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="sku" class="form-label">SKU/Código</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku', $kit->sku) }}" 
                                       placeholder="Ex: KIT-001">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                          id="descricao" name="descricao" rows="3" 
                                          placeholder="Descreva os benefícios e características do kit...">{{ old('descricao', $kit->descricao) }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="categoria_id" class="form-label">Categoria</label>
                                <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                        id="categoria_id" name="categoria_id">
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id', $kit->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nome }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="marca_id" class="form-label">Marca</label>
                                <select class="form-select @error('marca_id') is-invalid @enderror" 
                                        id="marca_id" name="marca_id">
                                    <option value="">Selecione uma marca</option>
                                    @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id', $kit->marca_id) == $marca->id ? 'selected' : '' }}>
                                        {{ $marca->nome }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('marca_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Produtos do Kit -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Produtos do Kit</h5>
                        <button type="button" class="btn btn-primary btn-sm" onclick="abrirSeletorProduto()">
                            <i class="fas fa-plus me-1"></i> Adicionar Produto
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="kitItemsContainer" class="kit-items-container">
                            <div id="emptyState" class="text-center py-4" style="display: none;">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum produto adicionado</h5>
                                <p class="text-muted">Clique em "Adicionar Produto" para começar a montar seu kit</p>
                            </div>
                        </div>
                        
                        <!-- Cálculo de Preços -->
                        <div id="precoCalculation" class="preco-calculation">
                            <h6><i class="fas fa-calculator me-2"></i>Cálculo de Preços</h6>
                            <div id="precoItens"></div>
                            <div class="preco-item preco-total">
                                <span>Total dos Itens:</span>
                                <span id="totalItens">R$ 0,00</span>
                            </div>
                            <div class="preco-item">
                                <span>Preço do Kit:</span>
                                <span id="precoKit">R$ 0,00</span>
                            </div>
                            <div class="preco-item economia-highlight">
                                <span>Economia:</span>
                                <span id="economia">R$ 0,00 (0%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Configurações -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Configurações</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="preco_venda" class="form-label">Preço de Venda (R$) *</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control @error('preco_venda') is-invalid @enderror" 
                                       id="preco_venda" name="preco_venda" value="{{ old('preco_venda', $kit->preco_venda) }}" 
                                       step="0.01" min="0" required onchange="calcularPrecos()">
                            </div>
                            @error('preco_venda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estoque_inicial" class="form-label">Estoque Atual</label>
                            <input type="number" class="form-control @error('estoque_inicial') is-invalid @enderror" 
                                   id="estoque_inicial" name="estoque_inicial" value="{{ old('estoque_inicial', $kit->estoque_atual) }}" 
                                   min="0">
                            @error('estoque_inicial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
                            <input type="number" class="form-control @error('estoque_minimo') is-invalid @enderror" 
                                   id="estoque_minimo" name="estoque_minimo" value="{{ old('estoque_minimo', $kit->estoque_minimo) }}" 
                                   min="0">
                            @error('estoque_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="disponivel" {{ old('status', $kit->status) == 'disponivel' ? 'selected' : '' }}>
                                    Disponível
                                </option>
                                <option value="indisponivel" {{ old('status', $kit->status) == 'indisponivel' ? 'selected' : '' }}>
                                    Indisponível
                                </option>
                                <option value="pausado" {{ old('status', $kit->status) == 'pausado' ? 'selected' : '' }}>
                                    Pausado
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem do Kit</label>
                            @if($kit->imagem_principal)
                                <div class="mb-2">
                                    <img src="{{ asset($kit->imagem_principal) }}" alt="Imagem atual" class="img-thumbnail" style="max-width: 100px;">
                                    <small class="d-block text-muted">Imagem atual</small>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('imagem') is-invalid @enderror" 
                                   id="imagem" name="imagem" accept="image/*">
                            @error('imagem')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" 
                                   {{ old('ativo', $kit->ativo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ativo">
                                Kit ativo
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="destaque" name="destaque" value="1" 
                                   {{ old('destaque', $kit->destaque) ? 'checked' : '' }}>
                            <label class="form-check-label" for="destaque">
                                Kit em destaque
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="frete_gratis" name="frete_gratis" value="1" 
                                   {{ old('frete_gratis', $kit->frete_gratis) ? 'checked' : '' }}>
                            <label class="form-check-label" for="frete_gratis">
                                Frete grátis
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Ações -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-kit w-100 mb-2">
                            <i class="fas fa-save me-1"></i> Atualizar Kit
                        </button>
                        <a href="{{ route('comerciantes.produtos.kits.show', $kit) }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-eye me-1"></i> Visualizar Kit
                        </a>
                        <a href="{{ route('comerciantes.produtos.kits.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Seletor de Produto -->
<div class="modal fade" id="produtoSelectorModal" tabindex="-1" aria-labelledby="produtoSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="produtoSelectorModalLabel">Selecionar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="produtoSearch" placeholder="Buscar produto por nome, SKU ou código...">
                </div>
                <div id="produtoResults" style="max-height: 400px; overflow-y: auto;">
                    <!-- Resultados da busca aparecerão aqui -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Configurar Item -->
<div class="modal fade" id="itemConfigModal" tabindex="-1" aria-labelledby="itemConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemConfigModalLabel">Configurar Item do Kit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="itemConfigForm">
                    <input type="hidden" id="itemProdutoId">
                    <input type="hidden" id="itemVariacaoId">
                    
                    <div class="mb-3">
                        <label class="form-label">Produto Selecionado</label>
                        <div id="itemProdutoInfo" class="border rounded p-2 bg-light"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="itemQuantidade" class="form-label">Quantidade *</label>
                        <input type="number" class="form-control" id="itemQuantidade" min="1" value="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="itemPreco" class="form-label">Preço Unitário (R$) *</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="itemPreco" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="itemDesconto" class="form-label">Desconto (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="itemDesconto" step="0.01" min="0" max="100" value="0">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="itemObrigatorio" checked>
                                <label class="form-check-label" for="itemObrigatorio">
                                    Item obrigatório
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="itemSubstituivel">
                                <label class="form-check-label" for="itemSubstituivel">
                                    Substituível
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="adicionarItem()">
                    <i class="fas fa-plus me-1"></i> Adicionar Item
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let kitItems = [];
let produtoSelectorModal, itemConfigModal;

document.addEventListener('DOMContentLoaded', function() {
    produtoSelectorModal = new bootstrap.Modal(document.getElementById('produtoSelectorModal'));
    itemConfigModal = new bootstrap.Modal(document.getElementById('itemConfigModal'));
    
    // Carregar itens existentes
    carregarItensExistentes();
    
    // Busca de produtos
    let searchTimeout;
    document.getElementById('produtoSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            buscarProdutos(this.value);
        }, 300);
    });
});

function carregarItensExistentes() {
    // Carregar itens do kit existente
    @foreach($kit->kitsItens as $item)
    kitItems.push({
        produto_id: {{ $item->produto_item_id }},
        variacao_id: {{ $item->variacao_item_id ?? 'null' }},
        nome: "{{ $item->produtoItem->nome }}",
        quantidade: {{ $item->quantidade }},
        preco_item: {{ $item->preco_item ?? $item->produtoItem->preco_venda }},
        desconto_percentual: {{ $item->desconto_percentual ?? 0 }},
        obrigatorio: {{ $item->obrigatorio ? 1 : 0 }},
        substituivel: {{ $item->substituivel ? 1 : 0 }}
    });
    @endforeach
    
    atualizarListaItens();
    calcularPrecos();
}

function abrirSeletorProduto() {
    produtoSelectorModal.show();
    document.getElementById('produtoSearch').focus();
}

function buscarProdutos(term) {
    if (term.length < 2) {
        document.getElementById('produtoResults').innerHTML = '<p class="text-muted text-center">Digite pelo menos 2 caracteres para buscar</p>';
        return;
    }
    
    fetch(`{{ route('comerciantes.produtos.kits.buscar-produto') }}?term=${encodeURIComponent(term)}`)
        .then(response => response.json())
        .then(data => {
            mostrarResultadosProdutos(data);
        })
        .catch(error => {
            console.error('Erro ao buscar produtos:', error);
            document.getElementById('produtoResults').innerHTML = '<p class="text-danger text-center">Erro ao buscar produtos</p>';
        });
}

function mostrarResultadosProdutos(produtos) {
    const resultsContainer = document.getElementById('produtoResults');
    
    if (produtos.length === 0) {
        resultsContainer.innerHTML = '<p class="text-muted text-center">Nenhum produto encontrado</p>';
        return;
    }
    
    let html = '';
    produtos.forEach(produto => {
        html += `
            <div class="produto-search-result" onclick="selecionarProduto(${produto.id}, '${produto.nome}', ${produto.preco_venda}, '${produto.sku || ''}')">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${produto.nome}</h6>
                        ${produto.sku ? `<small class="text-muted">SKU: ${produto.sku}</small>` : ''}
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">R$ ${parseFloat(produto.preco_venda).toFixed(2).replace('.', ',')}</div>
                        <small class="text-muted">Estoque: ${produto.estoque_atual || 0}</small>
                    </div>
                </div>
            </div>
        `;
    });
    
    resultsContainer.innerHTML = html;
}

function selecionarProduto(id, nome, preco, sku) {
    // Verificar se o produto já foi adicionado
    if (kitItems.find(item => item.produto_id == id)) {
        alert('Este produto já foi adicionado ao kit');
        return;
    }
    
    document.getElementById('itemProdutoId').value = id;
    document.getElementById('itemVariacaoId').value = '';
    document.getElementById('itemProdutoInfo').innerHTML = `
        <strong>${nome}</strong><br>
        ${sku ? `SKU: ${sku}<br>` : ''}
        Preço: R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}
    `;
    document.getElementById('itemPreco').value = parseFloat(preco).toFixed(2);
    
    produtoSelectorModal.hide();
    itemConfigModal.show();
}

function adicionarItem() {
    const form = document.getElementById('itemConfigForm');
    const formData = new FormData(form);
    
    const item = {
        produto_id: document.getElementById('itemProdutoId').value,
        variacao_id: document.getElementById('itemVariacaoId').value || null,
        nome: document.getElementById('itemProdutoInfo').querySelector('strong').textContent,
        quantidade: parseInt(document.getElementById('itemQuantidade').value),
        preco_item: parseFloat(document.getElementById('itemPreco').value),
        desconto_percentual: parseFloat(document.getElementById('itemDesconto').value) || 0,
        obrigatorio: document.getElementById('itemObrigatorio').checked ? 1 : 0,
        substituivel: document.getElementById('itemSubstituivel').checked ? 1 : 0
    };
    
    kitItems.push(item);
    atualizarListaItens();
    calcularPrecos();
    
    itemConfigModal.hide();
    
    // Limpar form
    document.getElementById('itemConfigForm').reset();
    document.getElementById('itemQuantidade').value = 1;
    document.getElementById('itemDesconto').value = 0;
    document.getElementById('itemObrigatorio').checked = true;
    document.getElementById('itemSubstituivel').checked = false;
}

function atualizarListaItens() {
    const container = document.getElementById('kitItemsContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (kitItems.length === 0) {
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    
    let html = '';
    kitItems.forEach((item, index) => {
        const precoComDesconto = item.preco_item * (1 - item.desconto_percentual / 100);
        const total = precoComDesconto * item.quantidade;
        
        html += `
            <div class="item-card position-relative">
                <button type="button" class="item-remove-btn" onclick="removerItem(${index})">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-1">${item.nome}</h6>
                        <div class="small text-muted">
                            ${item.obrigatorio ? '<span class="badge bg-primary me-1">Obrigatório</span>' : ''}
                            ${item.substituivel ? '<span class="badge bg-secondary me-1">Substituível</span>' : ''}
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="fw-bold">${item.quantidade}x</div>
                        <div class="small text-muted">R$ ${item.preco_item.toFixed(2).replace('.', ',')}</div>
                        ${item.desconto_percentual > 0 ? `<div class="small text-danger">-${item.desconto_percentual}%</div>` : ''}
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="fw-bold text-success">R$ ${total.toFixed(2).replace('.', ',')}</div>
                    </div>
                </div>
                
                <!-- Campos hidden para envio -->
                <input type="hidden" name="itens[${index}][produto_id]" value="${item.produto_id}">
                <input type="hidden" name="itens[${index}][variacao_id]" value="${item.variacao_id || ''}">
                <input type="hidden" name="itens[${index}][quantidade]" value="${item.quantidade}">
                <input type="hidden" name="itens[${index}][preco_item]" value="${item.preco_item}">
                <input type="hidden" name="itens[${index}][desconto_percentual]" value="${item.desconto_percentual}">
                <input type="hidden" name="itens[${index}][obrigatorio]" value="${item.obrigatorio}">
                <input type="hidden" name="itens[${index}][substituivel]" value="${item.substituivel}">
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function removerItem(index) {
    kitItems.splice(index, 1);
    atualizarListaItens();
    calcularPrecos();
}

function calcularPrecos() {
    const precoCalculation = document.getElementById('precoCalculation');
    const precoItensDiv = document.getElementById('precoItens');
    
    if (kitItems.length === 0) {
        precoCalculation.style.display = 'none';
        return;
    }
    
    precoCalculation.style.display = 'block';
    
    let totalItens = 0;
    let htmlItens = '';
    
    kitItems.forEach(item => {
        const precoComDesconto = item.preco_item * (1 - item.desconto_percentual / 100);
        const subtotal = precoComDesconto * item.quantidade;
        totalItens += subtotal;
        
        htmlItens += `
            <div class="preco-item">
                <span>${item.nome} (${item.quantidade}x)</span>
                <span>R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
            </div>
        `;
    });
    
    precoItensDiv.innerHTML = htmlItens;
    document.getElementById('totalItens').textContent = `R$ ${totalItens.toFixed(2).replace('.', ',')}`;
    
    const precoVenda = parseFloat(document.getElementById('preco_venda').value) || 0;
    document.getElementById('precoKit').textContent = `R$ ${precoVenda.toFixed(2).replace('.', ',')}`;
    
    const economia = totalItens - precoVenda;
    const percentualEconomia = totalItens > 0 ? (economia / totalItens) * 100 : 0;
    
    if (economia > 0) {
        document.getElementById('economia').textContent = `R$ ${economia.toFixed(2).replace('.', ',')} (${percentualEconomia.toFixed(1)}%)`;
        document.getElementById('economia').parentElement.style.display = 'flex';
    } else {
        document.getElementById('economia').parentElement.style.display = 'none';
    }
}

// Validação do formulário
document.getElementById('kitForm').addEventListener('submit', function(e) {
    if (kitItems.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um produto ao kit');
        return false;
    }
    
    const precoVenda = parseFloat(document.getElementById('preco_venda').value);
    if (!precoVenda || precoVenda <= 0) {
        e.preventDefault();
        alert('Informe um preço de venda válido');
        return false;
    }
});
</script>
@endpush

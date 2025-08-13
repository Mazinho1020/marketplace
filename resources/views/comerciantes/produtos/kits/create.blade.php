@extends('layouts.comerciante')

@section('title', 'Novo Kit/Combo')

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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-plus-circle text-success me-2"></i>
                Novo Kit/Combo de Produtos
            </h1>
            <p class="text-muted mb-0">Combine produtos para criar ofertas atrativas</p>
        </div>
        
        <a href="{{ route('comerciantes.produtos.kits.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar aos Kits
        </a>
    </div>

    <form id="kitForm" action="{{ route('comerciantes.produtos.kits.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
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
                                       id="nome" name="nome" value="{{ old('nome') }}" required 
                                       placeholder="Ex: Kit Café da Manhã Premium">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="sku" class="form-label">SKU/Código</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku') }}" 
                                       placeholder="Ex: KIT-001">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                          id="descricao" name="descricao" rows="3" 
                                          placeholder="Descreva os benefícios e características do kit...">{{ old('descricao') }}</textarea>
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
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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
                                    <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
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
                            <div id="emptyState" class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum produto adicionado</h5>
                                <p class="text-muted">Clique em "Adicionar Produto" para começar a montar seu kit</p>
                            </div>
                        </div>
                        
                        <!-- Cálculo de Preços -->
                        <div id="precoCalculation" class="preco-calculation" style="display: none;">
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
                                       id="preco_venda" name="preco_venda" value="{{ old('preco_venda') }}" 
                                       step="0.01" min="0" required onchange="calcularPrecos()">
                            </div>
                            @error('preco_venda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estoque_inicial" class="form-label">Estoque Inicial</label>
                            <input type="number" class="form-control @error('estoque_inicial') is-invalid @enderror" 
                                   id="estoque_inicial" name="estoque_inicial" value="{{ old('estoque_inicial', 0) }}" 
                                   min="0">
                            @error('estoque_inicial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
                            <input type="number" class="form-control @error('estoque_minimo') is-invalid @enderror" 
                                   id="estoque_minimo" name="estoque_minimo" value="{{ old('estoque_minimo', 0) }}" 
                                   min="0">
                            @error('estoque_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="disponivel" {{ old('status', 'disponivel') == 'disponivel' ? 'selected' : '' }}>
                                    Disponível
                                </option>
                                <option value="indisponivel" {{ old('status') == 'indisponivel' ? 'selected' : '' }}>
                                    Indisponível
                                </option>
                                <option value="pausado" {{ old('status') == 'pausado' ? 'selected' : '' }}>
                                    Pausado
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem do Kit</label>
                            <input type="file" class="form-control @error('imagem') is-invalid @enderror" 
                                   id="imagem" name="imagem" accept="image/*">
                            @error('imagem')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="destaque" name="destaque" value="1" 
                                   {{ old('destaque') ? 'checked' : '' }}>
                            <label class="form-check-label" for="destaque">
                                Kit em destaque
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="frete_gratis" name="frete_gratis" value="1" 
                                   {{ old('frete_gratis') ? 'checked' : '' }}>
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
                            <i class="fas fa-save me-1"></i> Criar Kit
                        </button>
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
    // Inicializar modais com configurações de acessibilidade
    const produtoModalElement = document.getElementById('produtoSelectorModal');
    const itemModalElement = document.getElementById('itemConfigModal');
    
    if (produtoModalElement) {
        produtoSelectorModal = new bootstrap.Modal(produtoModalElement, {
            backdrop: 'static',
            keyboard: true
        });
        
        // Event listener para focar no campo de busca quando o modal abrir
        produtoModalElement.addEventListener('shown.bs.modal', function() {
            setTimeout(() => {
                const searchInput = document.getElementById('produtoSearch');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 100);
        });
        
        // Limpar resultados quando modal fechar
        produtoModalElement.addEventListener('hidden.bs.modal', function() {
            const resultsContainer = document.getElementById('produtoResults');
            if (resultsContainer) {
                resultsContainer.innerHTML = '';
            }
            const searchInput = document.getElementById('produtoSearch');
            if (searchInput) {
                searchInput.value = '';
            }
        });
    }
    
    if (itemModalElement) {
        itemConfigModal = new bootstrap.Modal(itemModalElement, {
            backdrop: 'static',
            keyboard: true
        });
        
        // Focar no primeiro campo quando modal abrir
        itemModalElement.addEventListener('shown.bs.modal', function() {
            setTimeout(() => {
                const quantidadeInput = document.getElementById('itemQuantidade');
                if (quantidadeInput) {
                    quantidadeInput.focus();
                }
            }, 100);
        });
    }
    
    // Busca de produtos
    let searchTimeout;
    const produtoSearchInput = document.getElementById('produtoSearch');
    if (produtoSearchInput) {
        produtoSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                buscarProdutos(this.value);
            }, 300);
        });
    }
});

function abrirSeletorProduto() {
    // Fechar outros modais primeiro se estiverem abertos
    if (itemConfigModal) {
        const itemModalElement = document.getElementById('itemConfigModal');
        if (itemModalElement && itemModalElement.classList.contains('show')) {
            itemConfigModal.hide();
            
            // Aguardar o modal fechar antes de abrir o próximo
            itemModalElement.addEventListener('hidden.bs.modal', function openProdutoSelector() {
                itemModalElement.removeEventListener('hidden.bs.modal', openProdutoSelector);
                
                setTimeout(() => {
                    if (produtoSelectorModal) {
                        produtoSelectorModal.show();
                    }
                }, 100);
            });
            return;
        }
    }
    
    // Se não há outros modais abertos, abrir diretamente
    if (produtoSelectorModal) {
        produtoSelectorModal.show();
    }
}

function buscarProdutos(term) {
    if (term.length < 2) {
        const resultsElement = document.getElementById('produtoResults');
        if (resultsElement) {
            resultsElement.innerHTML = '<p class="text-muted text-center">Digite pelo menos 2 caracteres para buscar</p>';
        }
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token não encontrado');
        return;
    }
    
    fetch(`{{ route('comerciantes.produtos.kits.buscar-produto') }}?term=${encodeURIComponent(term)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            mostrarResultadosProdutos(data);
        })
        .catch(error => {
            console.error('Erro ao buscar produtos:', error);
            const resultsElement = document.getElementById('produtoResults');
            if (resultsElement) {
                resultsElement.innerHTML = '<p class="text-danger text-center">Erro ao buscar produtos</p>';
            }
        });
}

function mostrarResultadosProdutos(produtos) {
    const resultsContainer = document.getElementById('produtoResults');
    
    if (!resultsContainer) {
        console.error('Container de resultados não encontrado');
        return;
    }
    
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
    
    const produtoIdElement = document.getElementById('itemProdutoId');
    const variacaoIdElement = document.getElementById('itemVariacaoId');
    const produtoInfoElement = document.getElementById('itemProdutoInfo');
    const precoElement = document.getElementById('itemPreco');
    
    if (produtoIdElement) produtoIdElement.value = id;
    if (variacaoIdElement) variacaoIdElement.value = '';
    
    if (produtoInfoElement) {
        produtoInfoElement.innerHTML = `
            <strong>${nome}</strong><br>
            ${sku ? `SKU: ${sku}<br>` : ''}
            Preço: R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}
        `;
    }
    
    if (precoElement) precoElement.value = parseFloat(preco).toFixed(2);
    
    // Aguardar que o modal selector seja completamente fechado antes de abrir o próximo
    if (produtoSelectorModal) {
        produtoSelectorModal.hide();
        
        // Aguardar o modal fechar completamente antes de abrir o próximo
        const produtoModalElement = document.getElementById('produtoSelectorModal');
        if (produtoModalElement) {
            produtoModalElement.addEventListener('hidden.bs.modal', function openItemConfig() {
                // Remover o event listener para evitar múltiplas chamadas
                produtoModalElement.removeEventListener('hidden.bs.modal', openItemConfig);
                
                // Aguardar um pouco mais antes de abrir o próximo modal
                setTimeout(() => {
                    if (itemConfigModal) {
                        itemConfigModal.show();
                    }
                }, 100);
            });
        }
    }
}

function adicionarItem() {
    // Garantir que o DOM está pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', adicionarItem);
        return;
    }
    
    const form = document.getElementById('itemConfigForm');
    if (!form) {
        console.error('Formulário de configuração não encontrado');
        return;
    }
    
    const formData = new FormData(form);
    
    const produtoInfo = document.getElementById('itemProdutoInfo');
    const strongElement = produtoInfo ? produtoInfo.querySelector('strong') : null;
    
    const item = {
        produto_id: document.getElementById('itemProdutoId')?.value,
        variacao_id: document.getElementById('itemVariacaoId')?.value || null,
        nome: strongElement ? strongElement.textContent : '',
        quantidade: parseInt(document.getElementById('itemQuantidade')?.value || 1),
        preco_item: parseFloat(document.getElementById('itemPreco')?.value || 0),
        desconto_percentual: parseFloat(document.getElementById('itemDesconto')?.value) || 0,
        obrigatorio: document.getElementById('itemObrigatorio')?.checked ? 1 : 0,
        substituivel: document.getElementById('itemSubstituivel')?.checked ? 1 : 0
    };
    
    if (!item.produto_id) {
        alert('Por favor, selecione um produto válido.');
        return;
    }
    
    kitItems.push(item);
    
    // Usar setTimeout para garantir que a atualização aconteça após o push
    setTimeout(() => {
        atualizarListaItens();
        calcularPrecos();
    }, 10);
    
    // Fechar modal de forma segura
    if (itemConfigModal) {
        itemConfigModal.hide();
    }
    
    // Limpar form após fechar o modal
    setTimeout(() => {
        const form = document.getElementById('itemConfigForm');
        if (form) {
            form.reset();
        }
        
        const quantidadeInput = document.getElementById('itemQuantidade');
        if (quantidadeInput) quantidadeInput.value = 1;
        
        const descontoInput = document.getElementById('itemDesconto');
        if (descontoInput) descontoInput.value = 0;
        
        const obrigatorioInput = document.getElementById('itemObrigatorio');
        if (obrigatorioInput) obrigatorioInput.checked = true;
        
        const substituivelInput = document.getElementById('itemSubstituivel');
        if (substituivelInput) substituivelInput.checked = false;
        
        // Limpar informações do produto
        const produtoInfoElement = document.getElementById('itemProdutoInfo');
        if (produtoInfoElement) {
            produtoInfoElement.innerHTML = '<em class="text-muted">Nenhum produto selecionado</em>';
        }
    }, 150);
}

function atualizarListaItens() {
    // Aguardar que o DOM esteja pronto se necessário
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', atualizarListaItens);
        return;
    }
    
    const container = document.getElementById('kitItemsContainer') || document.querySelector('.kit-items-container');
    const emptyState = document.getElementById('emptyState');
    
    // Verificar se o container existe
    if (!container) {
        console.error('Container de itens do kit não encontrado');
        return;
    }
    
    if (kitItems.length === 0) {
        if (emptyState) {
            emptyState.style.display = 'block';
        }
        container.innerHTML = '';
        return;
    }
    
    if (emptyState) {
        emptyState.style.display = 'none';
    }
    
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
    
    // Verificar se os elementos existem
    if (!precoCalculation) {
        console.error('Elemento precoCalculation não encontrado');
        return;
    }
    
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
    
    if (precoItensDiv) {
        precoItensDiv.innerHTML = htmlItens;
    }
    
    const totalItensElement = document.getElementById('totalItens');
    if (totalItensElement) {
        totalItensElement.textContent = `R$ ${totalItens.toFixed(2).replace('.', ',')}`;
    }
    
    const precoVendaInput = document.getElementById('preco_venda');
    const precoVenda = parseFloat(precoVendaInput?.value || 0);
    
    const precoKitElement = document.getElementById('precoKit');
    if (precoKitElement) {
        precoKitElement.textContent = `R$ ${precoVenda.toFixed(2).replace('.', ',')}`;
    }
    
    const economia = totalItens - precoVenda;
    const percentualEconomia = totalItens > 0 ? (economia / totalItens) * 100 : 0;
    
    const economiaElement = document.getElementById('economia');
    if (economiaElement) {
        if (economia > 0) {
            economiaElement.textContent = `R$ ${economia.toFixed(2).replace('.', ',')} (${percentualEconomia.toFixed(1)}%)`;
            const parentElement = economiaElement.parentElement;
            if (parentElement) {
                parentElement.style.display = 'flex';
            }
        } else {
            const parentElement = economiaElement.parentElement;
            if (parentElement) {
                parentElement.style.display = 'none';
            }
        }
    }
}

// Validação do formulário
const kitForm = document.getElementById('kitForm');
if (kitForm) {
    kitForm.addEventListener('submit', function(e) {
        if (kitItems.length === 0) {
            e.preventDefault();
            alert('Adicione pelo menos um produto ao kit');
            return false;
        }
        
        const precoVendaElement = document.getElementById('preco_venda');
        const precoVenda = parseFloat(precoVendaElement?.value || 0);
        if (!precoVenda || precoVenda <= 0) {
            e.preventDefault();
            alert('Informe um preço de venda válido');
            return false;
        }
    });
}
</script>
@endpush

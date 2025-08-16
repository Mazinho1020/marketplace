@extends('comerciantes.layout')

@section('title', 'Nova Venda - PDV')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Nova Venda - PDV</h1>
                    <p class="text-muted">Interface para registro de vendas</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.index', $empresa) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Ver Vendas
                    </a>
                    <a href="{{ route('comerciantes.empresas.vendas.dashboard', $empresa) }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="vendaForm" method="POST" action="{{ route('comerciantes.empresas.vendas.gerenciar.store', $empresa) }}">
        @csrf
        <div class="row">
            <!-- Coluna Esquerda - Busca de Produtos -->
            <div class="col-lg-7">
                <!-- Busca de Produtos -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-search"></i> Buscar Produtos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <input type="text" id="buscarProduto" class="form-control form-control-lg" 
                                       placeholder="Digite o nome, código ou código de barras do produto..." 
                                       autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <select id="filtroCategoria" class="form-control">
                                    <option value="">Todas as categorias</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Lista de produtos encontrados -->
                        <div id="produtosEncontrados" class="row" style="max-height: 400px; overflow-y: auto;">
                            <!-- Produtos serão carregados aqui via AJAX -->
                        </div>
                    </div>
                </div>

                <!-- Produtos em Grade -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-th-large"></i> Produtos Disponíveis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($produtos->take(12) as $produto)
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="card produto-card h-100" data-produto-id="{{ $produto->id }}" 
                                     data-produto-nome="{{ $produto->nome }}" 
                                     data-produto-preco="{{ $produto->preco_venda }}"
                                     data-produto-estoque="{{ $produto->estoque_atual }}"
                                     data-controla-estoque="{{ $produto->controla_estoque ? 'true' : 'false' }}">
                                    <div class="card-body text-center p-2">
                                        @if($produto->imagemPrincipal)
                                            <img src="{{ $produto->imagemPrincipal->url }}" class="img-fluid mb-2" style="max-height: 60px;" alt="{{ $produto->nome }}">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center mb-2" style="height: 60px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                        <h6 class="card-title text-truncate" title="{{ $produto->nome }}">{{ $produto->nome }}</h6>
                                        <p class="text-success mb-1"><strong>R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}</strong></p>
                                        @if($produto->controla_estoque)
                                            <small class="text-muted">Estoque: {{ $produto->estoque_atual }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita - Carrinho e Finalização -->
            <div class="col-lg-5">
                <!-- Informações da Venda -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações da Venda</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_venda" class="form-label">Tipo de Venda *</label>
                                <select name="tipo_venda" id="tipo_venda" class="form-control" required>
                                    <option value="balcao">Balcão</option>
                                    <option value="delivery">Delivery</option>
                                    <option value="online">Online</option>
                                    <option value="telefone">Telefone</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-control">
                                    <option value="">Selecione um cliente (opcional)</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea name="observacoes" id="observacoes" class="form-control" rows="2" 
                                          placeholder="Observações sobre a venda..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carrinho de Compras -->
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Carrinho</h5>
                        <button type="button" id="limparCarrinho" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Limpar
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="itensCarrinho">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <p>Nenhum produto adicionado</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totais e Desconto -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="valor_desconto" class="form-label">Desconto (R$)</label>
                                <input type="number" name="valor_desconto" id="valor_desconto" class="form-control" 
                                       min="0" step="0.01" value="0">
                            </div>
                            <div class="col-6">
                                <label for="percentual_desconto" class="form-label">Desconto (%)</label>
                                <input type="number" id="percentual_desconto" class="form-control" 
                                       min="0" max="100" step="0.01" value="0">
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Desconto:</span>
                            <span id="desconto">R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="total" class="text-success">R$ 0,00</strong>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="confirmar_venda" value="1" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> Finalizar Venda
                            </button>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-save"></i> Salvar como Pendente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens do carrinho (hidden inputs) -->
        <div id="itensHidden"></div>
    </form>
</div>

<!-- Modal para Adicionar Produto -->
<div class="modal fade" id="adicionarProdutoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Produto</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="adicionarProdutoForm">
                    <input type="hidden" id="produtoId">
                    <div class="text-center mb-3">
                        <h6 id="produtoNome"></h6>
                        <p class="text-success"><strong id="produtoPreco"></strong></p>
                    </div>
                    <div class="form-group">
                        <label for="quantidade">Quantidade *</label>
                        <input type="number" id="quantidade" class="form-control" min="0.01" step="0.01" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="valor_unitario">Valor Unitário *</label>
                        <input type="number" id="valor_unitario" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="observacoes_item">Observações</label>
                        <input type="text" id="observacoes_item" class="form-control" placeholder="Ex: sem cebola">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmarAdicionar" class="btn btn-primary">Adicionar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let carrinho = [];
let contadorItens = 0;

// Buscar produtos via AJAX
let timeoutBusca;
document.getElementById('buscarProduto').addEventListener('input', function() {
    const termo = this.value;
    clearTimeout(timeoutBusca);
    
    if (termo.length >= 2) {
        timeoutBusca = setTimeout(() => {
            buscarProdutos(termo);
        }, 300);
    } else {
        document.getElementById('produtosEncontrados').innerHTML = '';
    }
});

function buscarProdutos(termo) {
    fetch(`{{ route('comerciantes.empresas.vendas.pdv.buscar-produtos', $empresa) }}?q=${encodeURIComponent(termo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarProdutosEncontrados(data.produtos);
            }
        })
        .catch(error => console.error('Erro ao buscar produtos:', error));
}

function mostrarProdutosEncontrados(produtos) {
    const container = document.getElementById('produtosEncontrados');
    
    if (produtos.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted">Nenhum produto encontrado</div>';
        return;
    }
    
    let html = '';
    produtos.forEach(produto => {
        html += `
            <div class="col-md-6 mb-2">
                <div class="card produto-card" 
                     data-produto-id="${produto.id}" 
                     data-produto-nome="${produto.nome}" 
                     data-produto-preco="${produto.preco_venda}"
                     data-produto-estoque="${produto.estoque_atual}"
                     data-controla-estoque="${produto.controla_estoque ? 'true' : 'false'}">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">${produto.nome}</h6>
                        <p class="text-success mb-1">R$ ${produto.preco_venda.toFixed(2).replace('.', ',')}</p>
                        <small class="text-muted">Código: ${produto.codigo}</small>
                        ${produto.controla_estoque ? `<br><small class="text-muted">Estoque: ${produto.estoque_atual}</small>` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Adicionar produto ao carrinho
document.addEventListener('click', function(e) {
    if (e.target.closest('.produto-card')) {
        const card = e.target.closest('.produto-card');
        const produtoId = card.dataset.produtoId;
        const produtoNome = card.dataset.produtoNome;
        const produtoPreco = parseFloat(card.dataset.produtoPreco);
        const produtoEstoque = parseFloat(card.dataset.produtoEstoque);
        const controlaEstoque = card.dataset.controlaEstoque === 'true';
        
        // Preencher modal
        document.getElementById('produtoId').value = produtoId;
        document.getElementById('produtoNome').textContent = produtoNome;
        document.getElementById('produtoPreco').textContent = `R$ ${produtoPreco.toFixed(2).replace('.', ',')}`;
        document.getElementById('valor_unitario').value = produtoPreco.toFixed(2);
        document.getElementById('quantidade').value = '1';
        document.getElementById('observacoes_item').value = '';
        
        // Verificar estoque
        if (controlaEstoque && produtoEstoque <= 0) {
            alert('Produto sem estoque disponível!');
            return;
        }
        
        // Configurar máximo da quantidade se controla estoque
        const quantidadeInput = document.getElementById('quantidade');
        if (controlaEstoque) {
            quantidadeInput.max = produtoEstoque;
        } else {
            quantidadeInput.removeAttribute('max');
        }
        
        $('#adicionarProdutoModal').modal('show');
    }
});

// Confirmar adição do produto
document.getElementById('confirmarAdicionar').addEventListener('click', function() {
    const produtoId = document.getElementById('produtoId').value;
    const produtoNome = document.getElementById('produtoNome').textContent;
    const quantidade = parseFloat(document.getElementById('quantidade').value);
    const valorUnitario = parseFloat(document.getElementById('valor_unitario').value);
    const observacoes = document.getElementById('observacoes_item').value;
    
    if (quantidade <= 0 || valorUnitario < 0) {
        alert('Quantidade deve ser maior que 0 e valor unitário não pode ser negativo!');
        return;
    }
    
    // Verificar se produto já está no carrinho
    const itemExistente = carrinho.find(item => item.produto_id == produtoId);
    
    if (itemExistente) {
        itemExistente.quantidade += quantidade;
        itemExistente.valor_total = itemExistente.quantidade * itemExistente.valor_unitario;
    } else {
        carrinho.push({
            id: ++contadorItens,
            produto_id: produtoId,
            produto_nome: produtoNome,
            quantidade: quantidade,
            valor_unitario: valorUnitario,
            valor_total: quantidade * valorUnitario,
            observacoes: observacoes
        });
    }
    
    atualizarCarrinho();
    $('#adicionarProdutoModal').modal('hide');
});

// Atualizar carrinho
function atualizarCarrinho() {
    const container = document.getElementById('itensCarrinho');
    const hiddenContainer = document.getElementById('itensHidden');
    
    if (carrinho.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                <p>Nenhum produto adicionado</p>
            </div>
        `;
        hiddenContainer.innerHTML = '';
        atualizarTotais();
        return;
    }
    
    let html = '';
    let hiddenHtml = '';
    
    carrinho.forEach((item, index) => {
        html += `
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${item.produto_nome}</h6>
                    <small class="text-muted">
                        ${item.quantidade.toFixed(2)} x R$ ${item.valor_unitario.toFixed(2).replace('.', ',')} = 
                        <strong>R$ ${item.valor_total.toFixed(2).replace('.', ',')}</strong>
                    </small>
                    ${item.observacoes ? `<br><small class="text-info">${item.observacoes}</small>` : ''}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerItem(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        hiddenHtml += `
            <input type="hidden" name="itens[${index}][produto_id]" value="${item.produto_id}">
            <input type="hidden" name="itens[${index}][quantidade]" value="${item.quantidade}">
            <input type="hidden" name="itens[${index}][valor_unitario]" value="${item.valor_unitario}">
            <input type="hidden" name="itens[${index}][observacoes]" value="${item.observacoes}">
        `;
    });
    
    container.innerHTML = html;
    hiddenContainer.innerHTML = hiddenHtml;
    atualizarTotais();
}

// Remover item do carrinho
function removerItem(index) {
    carrinho.splice(index, 1);
    atualizarCarrinho();
}

// Limpar carrinho
document.getElementById('limparCarrinho').addEventListener('click', function() {
    if (confirm('Limpar todos os itens do carrinho?')) {
        carrinho = [];
        atualizarCarrinho();
    }
});

// Atualizar totais
function atualizarTotais() {
    const subtotal = carrinho.reduce((total, item) => total + item.valor_total, 0);
    const desconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
    const total = subtotal - desconto;
    
    document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('desconto').textContent = `R$ ${desconto.toFixed(2).replace('.', ',')}`;
    document.getElementById('total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
}

// Desconto em valor
document.getElementById('valor_desconto').addEventListener('input', function() {
    const subtotal = carrinho.reduce((total, item) => total + item.valor_total, 0);
    const desconto = parseFloat(this.value) || 0;
    
    if (desconto > subtotal) {
        this.value = subtotal.toFixed(2);
    }
    
    // Atualizar percentual
    const percentual = subtotal > 0 ? (desconto / subtotal) * 100 : 0;
    document.getElementById('percentual_desconto').value = percentual.toFixed(2);
    
    atualizarTotais();
});

// Desconto em percentual
document.getElementById('percentual_desconto').addEventListener('input', function() {
    const subtotal = carrinho.reduce((total, item) => total + item.valor_total, 0);
    const percentual = parseFloat(this.value) || 0;
    const desconto = (subtotal * percentual) / 100;
    
    document.getElementById('valor_desconto').value = desconto.toFixed(2);
    atualizarTotais();
});

// Validação do formulário
document.getElementById('vendaForm').addEventListener('submit', function(e) {
    if (carrinho.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um produto ao carrinho!');
        return false;
    }
});

// Buscar produtos ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    // Focar no campo de busca
    document.getElementById('buscarProduto').focus();
});
</script>
@endpush

@push('styles')
<style>
.produto-card {
    cursor: pointer;
    transition: all 0.2s;
}

.produto-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card-title {
    font-size: 0.9rem;
}

#itensCarrinho {
    max-height: 300px;
    overflow-y: auto;
}

.form-control-lg {
    font-size: 1.1rem;
}
</style>
@endpush
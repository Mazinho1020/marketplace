@extends('comerciantes.layout')

@section('title', 'Editar Venda #' . $venda->numero_venda)

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Editar Venda #{{ $venda->numero_venda }}</h1>
                    <p class="text-muted">{{ $venda->data_venda->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.show', [$empresa, $venda->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.index', $empresa) }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Ver Todas
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($venda->status !== 'pendente')
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Atenção:</strong> Apenas vendas com status "Pendente" podem ser editadas. Esta venda tem status "{{ $venda->status_formatado }}".
    </div>
    @else
    
    <form method="POST" action="{{ route('comerciantes.empresas.vendas.gerenciar.update', [$empresa, $venda->id]) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Informações da Venda -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Informações da Venda</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_venda" class="form-label">Tipo de Venda *</label>
                                <select name="tipo_venda" id="tipo_venda" class="form-control" required>
                                    <option value="balcao" {{ $venda->tipo_venda === 'balcao' ? 'selected' : '' }}>Balcão</option>
                                    <option value="delivery" {{ $venda->tipo_venda === 'delivery' ? 'selected' : '' }}>Delivery</option>
                                    <option value="online" {{ $venda->tipo_venda === 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="telefone" {{ $venda->tipo_venda === 'telefone' ? 'selected' : '' }}>Telefone</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-control">
                                    <option value="">Selecione um cliente (opcional)</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ $venda->cliente_id == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea name="observacoes" id="observacoes" class="form-control" rows="3" 
                                          placeholder="Observações sobre a venda...">{{ $venda->observacoes }}</textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="valor_desconto" class="form-label">Desconto (R$)</label>
                                <input type="number" name="valor_desconto" id="valor_desconto" class="form-control" 
                                       min="0" step="0.01" value="{{ $venda->valor_desconto }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="percentual_desconto" class="form-label">Desconto (%)</label>
                                <input type="number" id="percentual_desconto" class="form-control" 
                                       min="0" max="100" step="0.01" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Itens da Venda -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Itens da Venda</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#adicionarItemModal">
                                <i class="fas fa-plus"></i> Adicionar Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabelaItens">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th width="100">Qtd</th>
                                        <th width="120">Valor Unit.</th>
                                        <th width="120">Total</th>
                                        <th width="200">Observações</th>
                                        <th width="80">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="itensVenda">
                                    @foreach($venda->itens as $index => $item)
                                    <tr data-item-id="{{ $item->id }}">
                                        <td>
                                            <strong>{{ $item->produto ? $item->produto->nome : 'Produto não encontrado' }}</strong>
                                            @if($item->produto && $item->produto->codigo_sistema)
                                                <br><small class="text-muted">Código: {{ $item->produto->codigo_sistema }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm item-quantidade" 
                                                   value="{{ $item->quantidade }}" min="0.01" step="0.01"
                                                   data-item-id="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm item-valor-unitario" 
                                                   value="{{ $item->valor_unitario }}" min="0" step="0.01"
                                                   data-item-id="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <strong class="item-total">{{ $item->valor_total_formatado }}</strong>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm item-observacoes" 
                                                   value="{{ $item->observacoes }}" placeholder="Observações"
                                                   data-item-id="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger remover-item" 
                                                    data-item-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($venda->itens->isEmpty())
                        <div class="text-center text-muted py-4" id="mensagemSemItens">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p>Nenhum item na venda</p>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#adicionarItemModal">
                                <i class="fas fa-plus"></i> Adicionar Primeiro Item
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar com Totais -->
            <div class="col-lg-4">
                <!-- Totais -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Totais</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-right"><strong id="subtotalVenda">{{ $venda->valor_total_formatado }}</strong></td>
                            </tr>
                            <tr>
                                <td>Desconto:</td>
                                <td class="text-right text-danger">
                                    <strong id="descontoVenda">- R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}</strong>
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Total Líquido:</strong></td>
                                <td class="text-right">
                                    <h4 class="text-success mb-0" id="totalVenda">{{ $venda->valor_liquido_formatado }}</h4>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Ações -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cogs"></i> Ações</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            
                            <button type="submit" name="confirmar_venda" value="1" class="btn btn-primary">
                                <i class="fas fa-check"></i> Salvar e Confirmar
                            </button>
                            
                            <a href="{{ route('comerciantes.empresas.vendas.gerenciar.show', [$empresa, $venda->id]) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar Edição
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

<!-- Modal para Adicionar Item -->
<div class="modal fade" id="adicionarItemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Item à Venda</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Busca de Produto -->
                <div class="form-group">
                    <label for="buscarProdutoModal">Buscar Produto</label>
                    <input type="text" id="buscarProdutoModal" class="form-control" 
                           placeholder="Digite o nome, código ou código de barras..." autocomplete="off">
                </div>
                
                <!-- Lista de produtos encontrados -->
                <div id="produtosEncontradosModal" class="mb-3" style="max-height: 200px; overflow-y: auto;">
                    <!-- Produtos serão carregados aqui via AJAX -->
                </div>
                
                <!-- Formulário do item -->
                <form id="formAdicionarItem">
                    <input type="hidden" id="produtoSelecionadoId">
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label>Produto Selecionado</label>
                            <div id="produtoSelecionadoInfo" class="alert alert-info" style="display: none;">
                                <!-- Info do produto selecionado -->
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="quantidadeItem">Quantidade *</label>
                            <input type="number" id="quantidadeItem" class="form-control" 
                                   min="0.01" step="0.01" value="1" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="valorUnitarioItem">Valor Unitário *</label>
                            <input type="number" id="valorUnitarioItem" class="form-control" 
                                   min="0" step="0.01" required>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <label for="observacoesItem">Observações</label>
                            <input type="text" id="observacoesItem" class="form-control" 
                                   placeholder="Ex: sem cebola, ponto da carne, etc...">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmarAdicionarItem" class="btn btn-primary" disabled>
                    <i class="fas fa-plus"></i> Adicionar Item
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itensModificados = new Map();

// Buscar produtos no modal
let timeoutBuscaModal;
document.getElementById('buscarProdutoModal').addEventListener('input', function() {
    const termo = this.value;
    clearTimeout(timeoutBuscaModal);
    
    if (termo.length >= 2) {
        timeoutBuscaModal = setTimeout(() => {
            buscarProdutosModal(termo);
        }, 300);
    } else {
        document.getElementById('produtosEncontradosModal').innerHTML = '';
    }
});

function buscarProdutosModal(termo) {
    fetch(`{{ route('comerciantes.empresas.vendas.pdv.buscar-produtos', $empresa) }}?q=${encodeURIComponent(termo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarProdutosModal(data.produtos);
            }
        })
        .catch(error => console.error('Erro ao buscar produtos:', error));
}

function mostrarProdutosModal(produtos) {
    const container = document.getElementById('produtosEncontradosModal');
    
    if (produtos.length === 0) {
        container.innerHTML = '<div class="alert alert-warning">Nenhum produto encontrado</div>';
        return;
    }
    
    let html = '';
    produtos.forEach(produto => {
        html += `
            <div class="border rounded p-2 mb-2 produto-opcao" style="cursor: pointer;" 
                 data-produto-id="${produto.id}" 
                 data-produto-nome="${produto.nome}" 
                 data-produto-preco="${produto.preco_venda}"
                 data-produto-estoque="${produto.estoque_atual}"
                 data-controla-estoque="${produto.controla_estoque}">
                <strong>${produto.nome}</strong>
                <span class="float-right text-success">R$ ${produto.preco_venda.toFixed(2).replace('.', ',')}</span>
                <br>
                <small class="text-muted">Código: ${produto.codigo}</small>
                ${produto.controla_estoque ? `<small class="text-muted"> | Estoque: ${produto.estoque_atual}</small>` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Selecionar produto
document.addEventListener('click', function(e) {
    if (e.target.closest('.produto-opcao')) {
        const opcao = e.target.closest('.produto-opcao');
        
        // Remover seleção anterior
        document.querySelectorAll('.produto-opcao').forEach(el => el.classList.remove('bg-primary', 'text-white'));
        
        // Selecionar atual
        opcao.classList.add('bg-primary', 'text-white');
        
        const produtoId = opcao.dataset.produtoId;
        const produtoNome = opcao.dataset.produtoNome;
        const produtoPreco = parseFloat(opcao.dataset.produtoPreco);
        const produtoEstoque = parseFloat(opcao.dataset.produtoEstoque);
        const controlaEstoque = opcao.dataset.controlaEstoque === 'true';
        
        // Preencher formulário
        document.getElementById('produtoSelecionadoId').value = produtoId;
        document.getElementById('valorUnitarioItem').value = produtoPreco.toFixed(2);
        
        // Mostrar info do produto
        const infoDiv = document.getElementById('produtoSelecionadoInfo');
        infoDiv.innerHTML = `
            <strong>${produtoNome}</strong><br>
            Preço: R$ ${produtoPreco.toFixed(2).replace('.', ',')}
            ${controlaEstoque ? `<br>Estoque disponível: ${produtoEstoque}` : ''}
        `;
        infoDiv.style.display = 'block';
        
        // Configurar quantidade máxima
        const quantidadeInput = document.getElementById('quantidadeItem');
        if (controlaEstoque) {
            quantidadeInput.max = produtoEstoque;
        } else {
            quantidadeInput.removeAttribute('max');
        }
        
        // Habilitar botão
        document.getElementById('confirmarAdicionarItem').disabled = false;
    }
});

// Confirmar adição de item (simulado - em produção faria via AJAX)
document.getElementById('confirmarAdicionarItem').addEventListener('click', function() {
    const produtoId = document.getElementById('produtoSelecionadoId').value;
    const quantidade = parseFloat(document.getElementById('quantidadeItem').value);
    const valorUnitario = parseFloat(document.getElementById('valorUnitarioItem').value);
    const observacoes = document.getElementById('observacoesItem').value;
    
    if (!produtoId || quantidade <= 0 || valorUnitario < 0) {
        alert('Selecione um produto e preencha corretamente a quantidade e valor!');
        return;
    }
    
    // Aqui seria feita uma requisição AJAX para adicionar o item
    alert('Funcionalidade de adicionar item será implementada com AJAX em produção.\nPor enquanto, adicione itens pela interface principal de vendas.');
    
    $('#adicionarItemModal').modal('hide');
});

// Eventos para editar itens existentes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('item-quantidade') || 
        e.target.classList.contains('item-valor-unitario') || 
        e.target.classList.contains('item-observacoes')) {
        
        const itemId = e.target.dataset.itemId;
        const row = e.target.closest('tr');
        
        // Recalcular total do item
        if (e.target.classList.contains('item-quantidade') || e.target.classList.contains('item-valor-unitario')) {
            const quantidade = parseFloat(row.querySelector('.item-quantidade').value);
            const valorUnitario = parseFloat(row.querySelector('.item-valor-unitario').value);
            const total = quantidade * valorUnitario;
            
            row.querySelector('.item-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        // Marcar como modificado
        itensModificados.set(itemId, {
            quantidade: row.querySelector('.item-quantidade').value,
            valor_unitario: row.querySelector('.item-valor-unitario').value,
            observacoes: row.querySelector('.item-observacoes').value
        });
        
        recalcularTotaisVenda();
    }
});

// Remover item
document.addEventListener('click', function(e) {
    if (e.target.closest('.remover-item')) {
        if (confirm('Remover este item da venda?')) {
            const button = e.target.closest('.remover-item');
            const itemId = button.dataset.itemId;
            const row = button.closest('tr');
            
            row.remove();
            itensModificados.set(itemId, { removido: true });
            recalcularTotaisVenda();
            
            // Verificar se não há mais itens
            if (document.querySelectorAll('#itensVenda tr').length === 0) {
                document.getElementById('mensagemSemItens').style.display = 'block';
            }
        }
    }
});

// Recalcular totais da venda
function recalcularTotaisVenda() {
    let subtotal = 0;
    
    document.querySelectorAll('#itensVenda tr').forEach(row => {
        const quantidade = parseFloat(row.querySelector('.item-quantidade').value);
        const valorUnitario = parseFloat(row.querySelector('.item-valor-unitario').value);
        subtotal += quantidade * valorUnitario;
    });
    
    const desconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
    const total = subtotal - desconto;
    
    document.getElementById('subtotalVenda').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('descontoVenda').textContent = `- R$ ${desconto.toFixed(2).replace('.', ',')}`;
    document.getElementById('totalVenda').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    
    // Atualizar percentual de desconto
    const percentual = subtotal > 0 ? (desconto / subtotal) * 100 : 0;
    document.getElementById('percentual_desconto').value = percentual.toFixed(2);
}

// Desconto em valor
document.getElementById('valor_desconto').addEventListener('input', recalcularTotaisVenda);

// Limpar modal ao fechar
$('#adicionarItemModal').on('hidden.bs.modal', function() {
    document.getElementById('buscarProdutoModal').value = '';
    document.getElementById('produtosEncontradosModal').innerHTML = '';
    document.getElementById('produtoSelecionadoInfo').style.display = 'none';
    document.getElementById('confirmarAdicionarItem').disabled = true;
    document.getElementById('formAdicionarItem').reset();
});

// Inicializar totais
recalcularTotaisVenda();
</script>
@endpush
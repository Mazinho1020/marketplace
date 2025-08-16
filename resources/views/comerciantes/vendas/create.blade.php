@extends('comerciantes.layout')

@section('title', 'Nova Venda')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Nova Venda
                    </h1>
                    <p class="text-muted mb-0">Registre uma nova venda no sistema</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.vendas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Voltar à Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="vendaForm" action="{{ route('comerciantes.vendas.store') }}" method="POST">
        @csrf
        
        <!-- Informações Básicas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações da Venda
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select" id="cliente_id" name="cliente_id">
                                    <option value="">Cliente Avulso</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tipo_venda" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_venda" name="tipo_venda" required>
                                    <option value="">Selecione...</option>
                                    <option value="balcao">Balcão</option>
                                    <option value="delivery">Delivery</option>
                                    <option value="mesa">Mesa</option>
                                    <option value="online">Online</option>
                                    <option value="whatsapp">WhatsApp</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="origem" class="form-label">Origem <span class="text-danger">*</span></label>
                                <select class="form-select" id="origem" name="origem" required>
                                    <option value="">Selecione...</option>
                                    <option value="pdv">PDV</option>
                                    <option value="manual">Manual</option>
                                    <option value="delivery">Delivery</option>
                                    <option value="api">API</option>
                                    <option value="whatsapp">WhatsApp</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="caixa_id" class="form-label">Caixa</label>
                                <input type="number" class="form-control" id="caixa_id" name="caixa_id" placeholder="Nº do Caixa">
                            </div>
                            <div class="col-md-3">
                                <label for="data_entrega_prevista" class="form-label">Entrega Prevista</label>
                                <input type="datetime-local" class="form-control" id="data_entrega_prevista" name="data_entrega_prevista">
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="2" placeholder="Observações da venda..."></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="cupom_desconto" class="form-label">Cupom de Desconto</label>
                                <input type="text" class="form-control" id="cupom_desconto" name="cupom_desconto" placeholder="Código do cupom">
                            </div>
                            <div class="col-md-3">
                                <label for="desconto_percentual" class="form-label">Desconto (%)</label>
                                <input type="number" class="form-control" id="desconto_percentual" name="desconto_percentual" 
                                       min="0" max="100" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens da Venda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-box me-2"></i>
                                Itens da Venda
                            </h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="adicionarItem()">
                                <i class="fas fa-plus me-1"></i>
                                Adicionar Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="itens-container">
                            <!-- Itens serão adicionados dinamicamente aqui -->
                        </div>
                        
                        <!-- Template do Item (hidden) -->
                        <div id="item-template" style="display: none;">
                            <div class="item-row border rounded p-3 mb-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Produto <span class="text-danger">*</span></label>
                                        <select class="form-select produto-select" name="itens[INDEX][produto_id]" required onchange="carregarDadosProduto(this, INDEX)">
                                            <option value="">Selecione o produto...</option>
                                            @foreach($produtos as $produto)
                                                <option value="{{ $produto->id }}" 
                                                        data-preco="{{ $produto->preco_venda }}"
                                                        data-estoque="{{ $produto->estoque_atual }}"
                                                        data-controla-estoque="{{ $produto->controla_estoque ? '1' : '0' }}">
                                                    {{ $produto->nome }} - R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}
                                                    @if($produto->controla_estoque)
                                                        (Estoque: {{ $produto->estoque_atual }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Quantidade <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control quantidade-input" 
                                               name="itens[INDEX][quantidade]" min="0.01" step="0.01" value="1" required
                                               onchange="calcularTotalItem(INDEX)">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Valor Unit.</label>
                                        <input type="number" class="form-control valor-unitario-input" 
                                               name="itens[INDEX][valor_unitario]" min="0" step="0.01" 
                                               onchange="calcularTotalItem(INDEX)">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Desconto (%)</label>
                                        <input type="number" class="form-control desconto-input" 
                                               name="itens[INDEX][desconto_percentual]" min="0" max="100" step="0.01" value="0"
                                               onchange="calcularTotalItem(INDEX)">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control total-item-display" readonly>
                                        </div>
                                        <input type="hidden" class="total-item-input" name="itens[INDEX][valor_total_calculado]">
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-10">
                                        <label class="form-label">Observações do Item</label>
                                        <input type="text" class="form-control" name="itens[INDEX][observacoes]" 
                                               placeholder="Observações específicas deste item...">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerItem(this)">
                                            <i class="fas fa-trash me-1"></i>
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo e Totais -->
        <div class="row mb-4">
            <div class="col-md-8">
                <!-- Dados de Entrega (se aplicável) -->
                <div class="card" id="dados-entrega-card" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-truck me-2"></i>
                            Dados de Entrega
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="endereco_entrega" class="form-label">Endereço</label>
                                <input type="text" class="form-control" name="dados_entrega[endereco]" 
                                       placeholder="Rua, número">
                            </div>
                            <div class="col-md-3">
                                <label for="bairro_entrega" class="form-label">Bairro</label>
                                <input type="text" class="form-control" name="dados_entrega[bairro]" 
                                       placeholder="Bairro">
                            </div>
                            <div class="col-md-3">
                                <label for="cep_entrega" class="form-label">CEP</label>
                                <input type="text" class="form-control" name="dados_entrega[cep]" 
                                       placeholder="00000-000">
                            </div>
                            <div class="col-md-4">
                                <label for="cidade_entrega" class="form-label">Cidade</label>
                                <input type="text" class="form-control" name="dados_entrega[cidade]" 
                                       placeholder="Cidade">
                            </div>
                            <div class="col-md-4">
                                <label for="telefone_entrega" class="form-label">Telefone</label>
                                <input type="text" class="form-control" name="dados_entrega[telefone]" 
                                       placeholder="(11) 99999-9999">
                            </div>
                            <div class="col-md-4">
                                <label for="observacoes_entrega" class="form-label">Obs. Entrega</label>
                                <input type="text" class="form-control" name="dados_entrega[observacoes]" 
                                       placeholder="Referências para entrega">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Resumo da Venda -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Resumo da Venda
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal-display">R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Desconto:</span>
                            <span id="desconto-display">R$ 0,00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-success" id="total-display">R$ 0,00</strong>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Salvar Venda
                            </button>
                            <button type="button" class="btn btn-primary" onclick="salvarEContinuar()">
                                <i class="fas fa-plus me-2"></i>
                                Salvar e Adicionar Pagamento
                            </button>
                            <a href="{{ route('comerciantes.vendas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 0;

// Adicionar primeiro item automaticamente
document.addEventListener('DOMContentLoaded', function() {
    adicionarItem();
    
    // Mostrar dados de entrega se tipo for delivery
    document.getElementById('tipo_venda').addEventListener('change', function() {
        const dadosEntregaCard = document.getElementById('dados-entrega-card');
        if (this.value === 'delivery') {
            dadosEntregaCard.style.display = 'block';
        } else {
            dadosEntregaCard.style.display = 'none';
        }
    });
});

function adicionarItem() {
    const container = document.getElementById('itens-container');
    const template = document.getElementById('item-template').innerHTML;
    const novoItem = template.replace(/INDEX/g, itemIndex);
    
    const div = document.createElement('div');
    div.innerHTML = novoItem;
    container.appendChild(div.firstElementChild);
    
    itemIndex++;
    calcularTotais();
}

function removerItem(button) {
    if (document.querySelectorAll('.item-row').length > 1) {
        button.closest('.item-row').remove();
        calcularTotais();
    } else {
        alert('Deve haver pelo menos um item na venda.');
    }
}

function carregarDadosProduto(select, index) {
    const option = select.selectedOptions[0];
    const itemRow = select.closest('.item-row');
    
    if (option.value) {
        const preco = parseFloat(option.dataset.preco);
        const estoque = parseFloat(option.dataset.estoque);
        const controlaEstoque = option.dataset.controlaEstoque === '1';
        
        const valorUnitarioInput = itemRow.querySelector('.valor-unitario-input');
        const quantidadeInput = itemRow.querySelector('.quantidade-input');
        
        valorUnitarioInput.value = preco.toFixed(2);
        
        // Limitar quantidade se controla estoque
        if (controlaEstoque) {
            quantidadeInput.max = estoque;
            if (parseFloat(quantidadeInput.value) > estoque) {
                quantidadeInput.value = estoque;
            }
        } else {
            quantidadeInput.removeAttribute('max');
        }
        
        calcularTotalItem(index);
    }
}

function calcularTotalItem(index) {
    const itemRows = document.querySelectorAll('.item-row');
    const itemRow = itemRows[index] || document.querySelector(`.item-row:nth-child(${index + 1})`);
    
    if (!itemRow) return;
    
    const quantidade = parseFloat(itemRow.querySelector('.quantidade-input').value) || 0;
    const valorUnitario = parseFloat(itemRow.querySelector('.valor-unitario-input').value) || 0;
    const desconto = parseFloat(itemRow.querySelector('.desconto-input').value) || 0;
    
    const subtotalItem = quantidade * valorUnitario;
    const valorDesconto = (subtotalItem * desconto) / 100;
    const totalItem = subtotalItem - valorDesconto;
    
    itemRow.querySelector('.total-item-display').value = totalItem.toLocaleString('pt-BR', { 
        style: 'currency', 
        currency: 'BRL' 
    });
    itemRow.querySelector('.total-item-input').value = totalItem.toFixed(2);
    
    calcularTotais();
}

function calcularTotais() {
    let subtotal = 0;
    
    document.querySelectorAll('.total-item-input').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });
    
    const descontoPercentual = parseFloat(document.getElementById('desconto_percentual').value) || 0;
    const valorDesconto = (subtotal * descontoPercentual) / 100;
    const total = subtotal - valorDesconto;
    
    document.getElementById('subtotal-display').textContent = subtotal.toLocaleString('pt-BR', { 
        style: 'currency', 
        currency: 'BRL' 
    });
    document.getElementById('desconto-display').textContent = valorDesconto.toLocaleString('pt-BR', { 
        style: 'currency', 
        currency: 'BRL' 
    });
    document.getElementById('total-display').textContent = total.toLocaleString('pt-BR', { 
        style: 'currency', 
        currency: 'BRL' 
    });
}

function salvarEContinuar() {
    const form = document.getElementById('vendaForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'salvar_e_continuar';
    input.value = '1';
    form.appendChild(input);
    form.submit();
}

// Recalcular totais quando desconto geral muda
document.getElementById('desconto_percentual').addEventListener('input', calcularTotais);

// Validação do formulário
document.getElementById('vendaForm').addEventListener('submit', function(e) {
    const itens = document.querySelectorAll('.item-row');
    if (itens.length === 0) {
        e.preventDefault();
        alert('É necessário adicionar pelo menos um item à venda.');
        return;
    }
    
    let temProdutoSelecionado = false;
    itens.forEach(item => {
        const produtoSelect = item.querySelector('.produto-select');
        if (produtoSelect.value) {
            temProdutoSelecionado = true;
        }
    });
    
    if (!temProdutoSelecionado) {
        e.preventDefault();
        alert('É necessário selecionar pelo menos um produto.');
        return;
    }
});
</script>
@endpush
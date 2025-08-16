@extends('layouts.admin')

@section('title', 'Nova Venda')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('vendas.index') }}">Vendas</a></li>
                        <li class="breadcrumb-item active">Nova Venda</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="fa fa-plus me-1"></i>
                    Nova Venda
                </h4>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="vendaForm">
                        <div class="row">
                            <!-- Informações do Cliente -->
                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="fa fa-user me-1"></i>
                                    Informações do Cliente
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="pessoa_id" class="form-label">Cliente *</label>
                                    <select name="pessoa_id" id="pessoa_id" class="form-select" required>
                                        <option value="">Selecione o cliente</option>
                                        <!-- Carregar via AJAX ou popular no controller -->
                                    </select>
                                </div>
                            </div>

                            <!-- Informações da Venda -->
                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="fa fa-shopping-cart me-1"></i>
                                    Informações da Venda
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="canal_venda" class="form-label">Canal de Venda *</label>
                                            <select name="canal_venda" id="canal_venda" class="form-select" required>
                                                <option value="pdv">PDV</option>
                                                <option value="online">Online</option>
                                                <option value="delivery">Delivery</option>
                                                <option value="telefone">Telefone</option>
                                                <option value="whatsapp">WhatsApp</option>
                                                <option value="presencial">Presencial</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tipo_entrega" class="form-label">Tipo de Entrega *</label>
                                            <select name="tipo_entrega" id="tipo_entrega" class="form-select" required>
                                                <option value="balcao">Balcão</option>
                                                <option value="entrega">Entrega</option>
                                                <option value="correios">Correios</option>
                                                <option value="transportadora">Transportadora</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="data_entrega_prevista" class="form-label">Data de Entrega Prevista</label>
                                            <input type="date" name="data_entrega_prevista" id="data_entrega_prevista" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="prioridade" class="form-label">Prioridade</label>
                                            <select name="prioridade" id="prioridade" class="form-select">
                                                <option value="baixa">Baixa</option>
                                                <option value="normal" selected>Normal</option>
                                                <option value="alta">Alta</option>
                                                <option value="urgente">Urgente</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Itens da Venda -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fa fa-list me-1"></i>
                                    Itens da Venda
                                </h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itensTable">
                                        <thead>
                                            <tr>
                                                <th width="40%">Produto</th>
                                                <th width="15%">Quantidade</th>
                                                <th width="15%">Valor Unitário</th>
                                                <th width="15%">Valor Total</th>
                                                <th width="15%">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="itemTemplate" style="display: none;">
                                                <td>
                                                    <select name="itens[0][produto_id]" class="form-select produto-select" required>
                                                        <option value="">Selecione o produto</option>
                                                        <!-- Carregar produtos via AJAX -->
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="itens[0][quantidade]" class="form-control quantidade-input" min="1" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="itens[0][valor_unitario]" class="form-control valor-unitario-input" min="0" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control valor-total-input" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remover-item">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <button type="button" id="adicionarItem" class="btn btn-sm btn-success">
                                    <i class="fa fa-plus me-1"></i> Adicionar Item
                                </button>
                            </div>
                        </div>

                        <!-- Resumo Financeiro -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea name="descricao" id="descricao" class="form-control" rows="3" placeholder="Descrição da venda..."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações</label>
                                    <textarea name="observacoes" id="observacoes" class="form-control" rows="2" placeholder="Observações adicionais..."></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Resumo Financeiro</h6>
                                        
                                        <div class="row mb-2">
                                            <div class="col">Valor Bruto:</div>
                                            <div class="col text-end"><span id="valorBrutoDisplay">R$ 0,00</span></div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col">Desconto:</div>
                                            <div class="col">
                                                <input type="number" name="valor_desconto" id="valor_desconto" class="form-control form-control-sm" min="0" step="0.01" value="0">
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="row">
                                            <div class="col"><strong>Valor Total:</strong></div>
                                            <div class="col text-end"><strong><span id="valorTotalDisplay">R$ 0,00</span></strong></div>
                                        </div>
                                        
                                        <input type="hidden" name="valor_bruto" id="valor_bruto" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="button" id="salvarRascunho" class="btn btn-secondary">
                                    <i class="fa fa-save me-1"></i> Salvar como Rascunho
                                </button>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check me-1"></i> Criar Venda
                                </button>
                                
                                <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-times me-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Adicionar primeiro item automaticamente
    adicionarItem();
    
    // Event listeners
    document.getElementById('adicionarItem').addEventListener('click', adicionarItem);
    document.getElementById('vendaForm').addEventListener('submit', salvarVenda);
    document.getElementById('salvarRascunho').addEventListener('click', function() {
        salvarVenda('rascunho');
    });
    document.getElementById('valor_desconto').addEventListener('input', calcularTotais);
});

function adicionarItem() {
    const template = document.getElementById('itemTemplate');
    const clone = template.cloneNode(true);
    
    clone.style.display = '';
    clone.id = `item_${itemIndex}`;
    
    // Atualizar nomes dos inputs
    const inputs = clone.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name) {
            input.name = input.name.replace('[0]', `[${itemIndex}]`);
        }
    });
    
    // Event listeners para o item
    const quantidadeInput = clone.querySelector('.quantidade-input');
    const valorUnitarioInput = clone.querySelector('.valor-unitario-input');
    const removerBtn = clone.querySelector('.remover-item');
    
    quantidadeInput.addEventListener('input', calcularTotais);
    valorUnitarioInput.addEventListener('input', calcularTotais);
    removerBtn.addEventListener('click', function() {
        clone.remove();
        calcularTotais();
    });
    
    document.querySelector('#itensTable tbody').appendChild(clone);
    itemIndex++;
}

function calcularTotais() {
    let valorBruto = 0;
    
    // Calcular valor de cada item
    document.querySelectorAll('#itensTable tbody tr:not(#itemTemplate)').forEach(row => {
        const quantidade = parseFloat(row.querySelector('.quantidade-input').value) || 0;
        const valorUnitario = parseFloat(row.querySelector('.valor-unitario-input').value) || 0;
        const valorTotal = quantidade * valorUnitario;
        
        row.querySelector('.valor-total-input').value = valorTotal.toFixed(2);
        valorBruto += valorTotal;
    });
    
    const desconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
    const valorFinal = valorBruto - desconto;
    
    // Atualizar displays
    document.getElementById('valorBrutoDisplay').textContent = formatarMoeda(valorBruto);
    document.getElementById('valorTotalDisplay').textContent = formatarMoeda(valorFinal);
    document.getElementById('valor_bruto').value = valorBruto.toFixed(2);
}

function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

function salvarVenda(status = 'pendente') {
    const form = document.getElementById('vendaForm');
    const formData = new FormData(form);
    
    // Adicionar status se for rascunho
    if (status === 'rascunho') {
        formData.append('status', 'rascunho');
    }
    
    fetch('{{ route("vendas.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Venda criada com sucesso!');
            window.location.href = '{{ route("vendas.index") }}';
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar venda');
    });
    
    return false;
}
</script>
@endpush
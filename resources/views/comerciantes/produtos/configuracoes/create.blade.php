@extends('layouts.comerciante')

@section('title', 'Criar Configuração de Produto')

@section('styles')
<style>
    .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 1.5rem; }
    .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 0.375rem 0.375rem 0 0 !important; }
    .form-label { font-weight: 600; color: #495057; }
    .form-control:focus, .form-select:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
    .required { color: #dc3545; }
    .help-text { font-size: 0.875em; color: #6c757d; margin-top: 0.25rem; }
    .preview-section { background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 0.375rem; padding: 1.5rem; margin-top: 1rem; }
    .item-preview { background: white; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 0.75rem; margin-bottom: 0.5rem; }
    .item-preview:last-child { margin-bottom: 0; }
    .btn-add-item { margin-top: 0.5rem; }
    .dynamic-items { max-height: 400px; overflow-y: auto; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-plus text-success me-2"></i>
                Nova Configuração de Produto
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.index') }}">Produtos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.configuracoes.index') }}">Configurações</a></li>
                    <li class="breadcrumb-item active">Nova Configuração</li>
                </ol>
            </nav>
        </div>
        
        <a href="{{ route('comerciantes.produtos.configuracoes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <form action="{{ route('comerciantes.produtos.configuracoes.store') }}" method="POST" id="configuracaoForm">
        @csrf
        
        <div class="row">
            <!-- Informações Básicas -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações da Configuração
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome da Configuração <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" name="nome" value="{{ old('nome') }}" required>
                                    <div class="help-text">Ex: Tamanhos, Sabores, Adicionais, etc.</div>
                                    @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Configuração <span class="required">*</span></label>
                                    <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="tamanho" {{ old('tipo') == 'tamanho' ? 'selected' : '' }}>Tamanho</option>
                                        <option value="sabor" {{ old('tipo') == 'sabor' ? 'selected' : '' }}>Sabor</option>
                                        <option value="ingrediente" {{ old('tipo') == 'ingrediente' ? 'selected' : '' }}>Ingrediente</option>
                                        <option value="complemento" {{ old('tipo') == 'complemento' ? 'selected' : '' }}>Complemento</option>
                                        <option value="borda" {{ old('tipo') == 'borda' ? 'selected' : '' }}>Borda</option>
                                        <option value="temperatura" {{ old('tipo') == 'temperatura' ? 'selected' : '' }}>Temperatura</option>
                                        <option value="personalizacao" {{ old('tipo') == 'personalizacao' ? 'selected' : '' }}>Personalização</option>
                                        <option value="outro" {{ old('tipo') == 'outro' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                    @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="produto_id" class="form-label">Produto Específico</label>
                            <select class="form-select @error('produto_id') is-invalid @enderror" id="produto_id" name="produto_id">
                                <option value="">Aplicar a todos os produtos</option>
                                @foreach($produtos as $produto)
                                <option value="{{ $produto->id }}" {{ old('produto_id') == $produto->id ? 'selected' : '' }}>
                                    {{ $produto->nome }}
                                </option>
                                @endforeach
                            </select>
                            <div class="help-text">Deixe em branco para aplicar a configuração a todos os produtos</div>
                            @error('produto_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                            <div class="help-text">Descrição opcional para explicar a configuração</div>
                            @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Itens da Configuração -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>
                            Itens da Configuração
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" onclick="addItem()">
                            <i class="fas fa-plus me-1"></i> Adicionar Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="itens-container" class="dynamic-items">
                            <!-- Itens serão adicionados aqui dinamicamente -->
                        </div>
                        
                        <div class="text-center py-3" id="no-items-message">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Clique em "Adicionar Item" para criar os itens desta configuração</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações Avançadas -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Configurações Avançadas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                       {{ old('ativo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ativo">
                                    <strong>Configuração Ativa</strong>
                                </label>
                            </div>
                            <div class="help-text">Se desmarcado, a configuração não será exibida no sistema</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="obrigatorio" name="obrigatorio" 
                                       {{ old('obrigatorio') ? 'checked' : '' }}>
                                <label class="form-check-label" for="obrigatorio">
                                    <strong>Seleção Obrigatória</strong>
                                </label>
                            </div>
                            <div class="help-text">O cliente deve selecionar pelo menos um item</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="multipla_selecao" name="multipla_selecao" 
                                       {{ old('multipla_selecao') ? 'checked' : '' }} onchange="toggleMaxSelecoes()">
                                <label class="form-check-label" for="multipla_selecao">
                                    <strong>Múltipla Seleção</strong>
                                </label>
                            </div>
                            <div class="help-text">Permite selecionar mais de um item</div>
                        </div>
                        
                        <div class="mb-3" id="max-selecoes-group" style="display: none;">
                            <label for="max_selecoes" class="form-label">Máximo de Seleções</label>
                            <input type="number" class="form-control @error('max_selecoes') is-invalid @enderror" 
                                   id="max_selecoes" name="max_selecoes" value="{{ old('max_selecoes') }}" min="1">
                            <div class="help-text">Deixe em branco para permitir seleções ilimitadas</div>
                            @error('max_selecoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="preview-content">
                            <p class="text-muted text-center">
                                <i class="fas fa-info-circle me-1"></i>
                                O preview será exibido conforme você preenche o formulário
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('comerciantes.produtos.configuracoes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Salvar Configuração
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template para novo item -->
<template id="item-template">
    <div class="item-preview" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="flex-grow-1">
                <input type="text" name="itens[__INDEX__][nome]" class="form-control form-control-sm mb-2" 
                       placeholder="Nome do item" required onchange="updatePreview()">
                <div class="row">
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">R$</span>
                            <input type="number" name="itens[__INDEX__][preco_adicional]" class="form-control" 
                                   placeholder="0,00" step="0.01" value="0.00" onchange="updatePreview()">
                        </div>
                    </div>
                    <div class="col-6">
                        <input type="number" name="itens[__INDEX__][ordem]" class="form-control form-control-sm" 
                               placeholder="Ordem" value="__ORDER__" min="1" onchange="updatePreview()">
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="removeItem(__INDEX__)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <textarea name="itens[__INDEX__][descricao]" class="form-control form-control-sm mb-2" 
                  placeholder="Descrição (opcional)" rows="2" onchange="updatePreview()"></textarea>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="itens[__INDEX__][ativo]" 
                   id="item_ativo___INDEX__" checked onchange="updatePreview()">
            <label class="form-check-label" for="item_ativo___INDEX__">
                Item ativo
            </label>
        </div>
    </div>
</template>
@endsection

@section('scripts')
<script>
let itemIndex = 0;

function toggleMaxSelecoes() {
    const checkbox = document.getElementById('multipla_selecao');
    const group = document.getElementById('max-selecoes-group');
    
    if (checkbox.checked) {
        group.style.display = 'block';
    } else {
        group.style.display = 'none';
        document.getElementById('max_selecoes').value = '';
    }
    updatePreview();
}

function addItem() {
    const container = document.getElementById('itens-container');
    const template = document.getElementById('item-template');
    const noItemsMessage = document.getElementById('no-items-message');
    
    const newItem = template.content.cloneNode(true);
    const itemHtml = newItem.firstElementChild.outerHTML
        .replace(/__INDEX__/g, itemIndex)
        .replace(/__ORDER__/g, itemIndex + 1);
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    noItemsMessage.style.display = 'none';
    itemIndex++;
    updatePreview();
}

function removeItem(index) {
    const item = document.querySelector(`[data-index="${index}"]`);
    if (item) {
        item.remove();
        updatePreview();
        
        // Mostrar mensagem se não há itens
        const container = document.getElementById('itens-container');
        if (container.children.length === 0) {
            document.getElementById('no-items-message').style.display = 'block';
        }
    }
}

function updatePreview() {
    const nome = document.getElementById('nome').value;
    const tipo = document.getElementById('tipo').value;
    const obrigatorio = document.getElementById('obrigatorio').checked;
    const multiplaSelecao = document.getElementById('multipla_selecao').checked;
    const maxSelecoes = document.getElementById('max_selecoes').value;
    
    const previewContent = document.getElementById('preview-content');
    
    if (!nome) {
        previewContent.innerHTML = `
            <p class="text-muted text-center">
                <i class="fas fa-info-circle me-1"></i>
                Digite o nome da configuração para ver o preview
            </p>
        `;
        return;
    }
    
    let preview = `
        <div class="mb-3">
            <h6 class="mb-2">
                ${nome}
                ${obrigatorio ? '<span class="badge bg-warning ms-1">Obrigatório</span>' : ''}
                ${multiplaSelecao ? '<span class="badge bg-info ms-1">Múltipla</span>' : ''}
            </h6>
            ${tipo ? `<small class="text-muted">Tipo: ${tipo}</small>` : ''}
        </div>
    `;
    
    // Adicionar itens
    const itens = document.querySelectorAll('#itens-container .item-preview');
    if (itens.length > 0) {
        preview += '<div class="border rounded p-2">';
        itens.forEach((item, idx) => {
            const nomeInput = item.querySelector('input[name*="[nome]"]');
            const precoInput = item.querySelector('input[name*="[preco_adicional]"]');
            const ativoInput = item.querySelector('input[name*="[ativo]"]');
            
            if (nomeInput && nomeInput.value) {
                const preco = parseFloat(precoInput.value || 0);
                const precoFormatado = preco > 0 ? ` (+R$ ${preco.toFixed(2).replace('.', ',')})` : '';
                const ativo = ativoInput.checked;
                
                preview += `
                    <div class="form-check ${!ativo ? 'text-muted' : ''}">
                        <input class="form-check-input" type="${multiplaSelecao ? 'checkbox' : 'radio'}" disabled ${!ativo ? 'style="opacity: 0.5"' : ''}>
                        <label class="form-check-label">
                            ${nomeInput.value}${precoFormatado}
                            ${!ativo ? ' <small>(Inativo)</small>' : ''}
                        </label>
                    </div>
                `;
            }
        });
        preview += '</div>';
        
        if (multiplaSelecao && maxSelecoes) {
            preview += `<small class="text-muted d-block mt-1">Máximo: ${maxSelecoes} seleções</small>`;
        }
    } else {
        preview += '<p class="text-muted"><small>Nenhum item adicionado</small></p>';
    }
    
    previewContent.innerHTML = preview;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar primeiro item automaticamente
    addItem();
    
    // Atualizar preview quando campos principais mudarem
    ['nome', 'tipo', 'obrigatorio', 'multipla_selecao', 'max_selecoes'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updatePreview);
            element.addEventListener('input', updatePreview);
        }
    });
    
    // Verificar múltipla seleção no carregamento
    toggleMaxSelecoes();
});

// Validação do formulário
document.getElementById('configuracaoForm').addEventListener('submit', function(e) {
    const itens = document.querySelectorAll('#itens-container .item-preview');
    if (itens.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um item para a configuração.');
        return false;
    }
    
    // Verificar se pelo menos um item tem nome
    let hasValidItem = false;
    itens.forEach(item => {
        const nomeInput = item.querySelector('input[name*="[nome]"]');
        if (nomeInput && nomeInput.value.trim()) {
            hasValidItem = true;
        }
    });
    
    if (!hasValidItem) {
        e.preventDefault();
        alert('Pelo menos um item deve ter um nome válido.');
        return false;
    }
});
</script>
@endsection

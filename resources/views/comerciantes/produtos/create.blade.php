@extends('comerciantes.layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="container-fluid py-4">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-plus text-primary me-2"></i>
                Novo Produto
            </h1>
            <p class="text-muted mb-0">Cadastre um novo produto no seu catálogo</p>
        </div>
        <div>
            <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    <form action="{{ route('comerciantes.produtos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Coluna Principal -->
            <div class="col-lg-8">
                <!-- Informações Básicas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações Básicas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" 
                                           value="{{ old('nome') }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                                    <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                        <option value="">Selecione...</option>
                                        <option value="produto" {{ old('tipo') == 'produto' ? 'selected' : '' }}>Produto</option>
                                        <option value="servico" {{ old('tipo') == 'servico' ? 'selected' : '' }}>Serviço</option>
                                        <option value="insumo" {{ old('tipo') == 'insumo' ? 'selected' : '' }}>Insumo</option>
                                        <option value="complemento" {{ old('tipo') == 'complemento' ? 'selected' : '' }}>Complemento</option>
                                        <option value="combo" {{ old('tipo') == 'combo' ? 'selected' : '' }}>Combo</option>
                                        <option value="kit" {{ old('tipo') == 'kit' ? 'selected' : '' }}>Kit</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao_curta" class="form-label">Descrição Curta</label>
                            <textarea name="descricao_curta" id="descricao_curta" class="form-control" rows="2" 
                                      placeholder="Breve descrição do produto (máx. 500 caracteres)">{{ old('descricao_curta') }}</textarea>
                            <div class="form-text">Esta descrição aparecerá nas listagens de produtos.</div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição Detalhada</label>
                            <textarea name="descricao" id="descricao" class="form-control" rows="4" 
                                      placeholder="Descrição completa do produto">{{ old('descricao') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Categorização -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tags me-2"></i>
                            Categorização
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                    <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                                        <option value="">Selecione uma categoria...</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <a href="{{ route('comerciantes.produtos.categorias.create') }}" target="_blank">
                                            Criar nova categoria
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="marca_id" class="form-label">Marca</label>
                                    <select name="marca_id" id="marca_id" class="form-select">
                                        <option value="">Sem marca</option>
                                        @foreach($marcas as $marca)
                                            <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                                {{ $marca->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <a href="{{ route('comerciantes.produtos.marcas.create') }}" target="_blank">
                                            Criar nova marca
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preços -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Preços
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="preco_compra" class="form-label">Preço de Compra</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="preco_compra" id="preco_compra" 
                                               class="form-control" step="0.01" min="0" 
                                               value="{{ old('preco_compra') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="preco_venda" class="form-label">Preço de Venda <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="preco_venda" id="preco_venda" 
                                               class="form-control @error('preco_venda') is-invalid @enderror" 
                                               step="0.01" min="0" value="{{ old('preco_venda') }}" required>
                                    </div>
                                    @error('preco_venda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="preco_promocional" class="form-label">Preço Promocional</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="preco_promocional" id="preco_promocional" 
                                               class="form-control" step="0.01" min="0" 
                                               value="{{ old('preco_promocional') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Margem de lucro:</strong> Será calculada automaticamente com base nos preços de compra e venda.
                        </div>
                    </div>
                </div>

                <!-- Estoque -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-warehouse me-2"></i>
                            Controle de Estoque
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="controla_estoque" 
                                       id="controla_estoque" {{ old('controla_estoque') ? 'checked' : '' }}>
                                <label class="form-check-label" for="controla_estoque">
                                    Controlar estoque deste produto
                                </label>
                            </div>
                        </div>

                        <div id="campos_estoque" style="display: {{ old('controla_estoque') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="estoque_atual" class="form-label">Estoque Atual</label>
                                        <input type="number" name="estoque_atual" id="estoque_atual" 
                                               class="form-control" step="0.001" min="0" 
                                               value="{{ old('estoque_atual', 0) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
                                        <input type="number" name="estoque_minimo" id="estoque_minimo" 
                                               class="form-control" step="0.001" min="0" 
                                               value="{{ old('estoque_minimo', 0) }}">
                                        <div class="form-text">Será enviada notificação quando atingir este valor</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="estoque_maximo" class="form-label">Estoque Máximo</label>
                                        <input type="number" name="estoque_maximo" id="estoque_maximo" 
                                               class="form-control" step="0.001" min="0" 
                                               value="{{ old('estoque_maximo') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Imagem -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-image me-2"></i>
                            Imagem Principal
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <input type="file" name="imagem_principal" id="imagem_principal" 
                                   class="form-control" accept="image/*">
                            <div class="form-text">JPG, PNG, GIF até 2MB</div>
                        </div>
                        
                        <div id="preview-imagem" style="display: none;">
                            <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                </div>

                <!-- Códigos -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-barcode me-2"></i>
                            Códigos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" 
                                   value="{{ old('sku') }}" placeholder="Será gerado automaticamente">
                            <div class="form-text">Código único do produto</div>
                        </div>

                        <div class="mb-3">
                            <label for="codigo_barras" class="form-label">Código de Barras</label>
                            <input type="text" name="codigo_barras" id="codigo_barras" 
                                   class="form-control" value="{{ old('codigo_barras') }}">
                        </div>

                        <div class="mb-3">
                            <label for="codigo_fabricante" class="form-label">Código do Fabricante</label>
                            <input type="text" name="codigo_fabricante" id="codigo_fabricante" 
                                   class="form-control" value="{{ old('codigo_fabricante') }}">
                        </div>
                    </div>
                </div>

                <!-- Configurações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Configurações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ativo" 
                                       id="ativo" {{ old('ativo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ativo">
                                    Produto ativo
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="destaque" 
                                       id="destaque" {{ old('destaque') ? 'checked' : '' }}>
                                <label class="form-check-label" for="destaque">
                                    Produto em destaque
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="disponivel" {{ old('status', 'disponivel') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                                <option value="indisponivel" {{ old('status') == 'indisponivel' ? 'selected' : '' }}>Indisponível</option>
                                <option value="pausado" {{ old('status') == 'pausado' ? 'selected' : '' }}>Pausado</option>
                                <option value="novidade" {{ old('status') == 'novidade' ? 'selected' : '' }}>Novidade</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Salvar Produto
                            </button>
                            <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Controle de exibição dos campos de estoque
    document.getElementById('controla_estoque').addEventListener('change', function() {
        const camposEstoque = document.getElementById('campos_estoque');
        camposEstoque.style.display = this.checked ? 'block' : 'none';
    });

    // Preview da imagem
    document.getElementById('imagem_principal').addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('preview-imagem');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    // Auto-geração do SKU baseado no nome
    document.getElementById('nome').addEventListener('input', function() {
        const sku = document.getElementById('sku');
        if (!sku.value) {
            const nome = this.value.toLowerCase()
                              .replace(/[^a-z0-9]/g, '')
                              .substring(0, 8)
                              .toUpperCase();
            if (nome) {
                sku.placeholder = nome + '0001 (exemplo)';
            }
        }
    });

    // Cálculo da margem de lucro
    function calcularMargem() {
        const precoCompra = parseFloat(document.getElementById('preco_compra').value) || 0;
        const precoVenda = parseFloat(document.getElementById('preco_venda').value) || 0;
        
        if (precoCompra > 0 && precoVenda > 0) {
            const margem = ((precoVenda - precoCompra) / precoCompra) * 100;
            console.log('Margem calculada:', margem.toFixed(2) + '%');
        }
    }

    document.getElementById('preco_compra').addEventListener('input', calcularMargem);
    document.getElementById('preco_venda').addEventListener('input', calcularMargem);
</script>
@endpush
@endsection

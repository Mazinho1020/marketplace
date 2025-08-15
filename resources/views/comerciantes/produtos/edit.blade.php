@extends('layouts.comerciante')

@section('title', 'Editar Produto')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Editar Produto</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.index') }}">Produtos</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.show', $produto->id) }}">{{ $produto->nome }}</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('comerciantes.produtos.show', $produto->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- Formulário de Edição -->
            <form action="{{ route('comerciantes.produtos.update', $produto->id) }}" method="POST" enctype="multipart/form-data" id="formProduto">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Informações Básicas -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Informações Básicas
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="nome" class="form-label fw-semibold">Nome do Produto *</label>
                                            <input type="text" 
                                                   class="form-control @error('nome') is-invalid @enderror" 
                                                   id="nome" 
                                                   name="nome" 
                                                   value="{{ old('nome', $produto->nome) }}" 
                                                   required>
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="sku" class="form-label fw-semibold">SKU</label>
                                            <input type="text" 
                                                   class="form-control @error('sku') is-invalid @enderror" 
                                                   id="sku" 
                                                   name="sku" 
                                                   value="{{ old('sku', $produto->sku) }}">
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Deixe vazio para gerar automaticamente</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="categoria_id" class="form-label fw-semibold">Categoria</label>
                                            <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                                    id="categoria_id" 
                                                    name="categoria_id">
                                                <option value="">Selecione uma categoria</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" 
                                                            {{ old('categoria_id', $produto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                                        {{ $categoria->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categoria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="marca_id" class="form-label fw-semibold">Marca</label>
                                            <select class="form-select @error('marca_id') is-invalid @enderror" 
                                                    id="marca_id" 
                                                    name="marca_id">
                                                <option value="">Selecione uma marca</option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->id }}" 
                                                            {{ old('marca_id', $produto->marca_id) == $marca->id ? 'selected' : '' }}>
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

                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="tipo" class="form-label fw-semibold">Tipo *</label>
                                            <select class="form-select @error('tipo') is-invalid @enderror" 
                                                    id="tipo" 
                                                    name="tipo" 
                                                    required>
                                                <option value="">Selecione um tipo</option>
                                                <option value="produto" {{ old('tipo', $produto->tipo) == 'produto' ? 'selected' : '' }}>Produto</option>
                                                <option value="insumo" {{ old('tipo', $produto->tipo) == 'insumo' ? 'selected' : '' }}>Insumo</option>
                                                <option value="complemento" {{ old('tipo', $produto->tipo) == 'complemento' ? 'selected' : '' }}>Complemento</option>
                                                <option value="servico" {{ old('tipo', $produto->tipo) == 'servico' ? 'selected' : '' }}>Serviço</option>
                                                <option value="combo" {{ old('tipo', $produto->tipo) == 'combo' ? 'selected' : '' }}>Combo</option>
                                                <option value="kit" {{ old('tipo', $produto->tipo) == 'kit' ? 'selected' : '' }}>Kit</option>
                                            </select>
                                            @error('tipo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Seção de Preços -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-tag me-2"></i>Preços
                                        </h6>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="preco_compra" class="form-label fw-semibold">Preço de Compra</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" 
                                                       class="form-control money-mask @error('preco_compra') is-invalid @enderror" 
                                                       id="preco_compra" 
                                                       name="preco_compra" 
                                                       value="{{ old('preco_compra', number_format($produto->preco_compra ?? 0, 2, ',', '.')) }}">
                                                @error('preco_compra')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="text-muted">Custo de aquisição do produto</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="preco_venda" class="form-label fw-semibold">Preço de Venda *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" 
                                                       class="form-control money-mask @error('preco_venda') is-invalid @enderror" 
                                                       id="preco_venda" 
                                                       name="preco_venda" 
                                                       value="{{ old('preco_venda', number_format($produto->preco_venda, 2, ',', '.')) }}" 
                                                       required>
                                                @error('preco_venda')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="text-muted">Preço regular de venda</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="preco_promocional" class="form-label fw-semibold">Preço Promocional</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" 
                                                       class="form-control money-mask @error('preco_promocional') is-invalid @enderror" 
                                                       id="preco_promocional" 
                                                       name="preco_promocional" 
                                                       value="{{ old('preco_promocional', $produto->preco_promocional ? number_format($produto->preco_promocional, 2, ',', '.') : '') }}">
                                                @error('preco_promocional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="text-muted">Preço em promoção (opcional)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descricao" class="form-label fw-semibold">Descrição</label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                              id="descricao" 
                                              name="descricao" 
                                              rows="3">{{ old('descricao', $produto->descricao) }}</textarea>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="observacoes" class="form-label fw-semibold">Observações</label>
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                              id="observacoes" 
                                              name="observacoes" 
                                              rows="2">{{ old('observacoes', $produto->observacoes) }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Controle de Estoque -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-cubes text-primary me-2"></i>Controle de Estoque
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="controla_estoque" 
                                                       name="controla_estoque" 
                                                       value="1"
                                                       {{ old('controla_estoque', $produto->controla_estoque) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="controla_estoque">
                                                    Controlar Estoque
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="campo_quantidade_estoque">
                                        <div class="mb-3">
                                            <label for="quantidade_estoque" class="form-label fw-semibold">Qtd. Atual</label>
                                            <input type="number" 
                                                   class="form-control @error('quantidade_estoque') is-invalid @enderror" 
                                                   id="quantidade_estoque" 
                                                   name="quantidade_estoque" 
                                                   value="{{ old('quantidade_estoque', $produto->quantidade_estoque) }}"
                                                   min="0">
                                            @error('quantidade_estoque')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="campo_estoque_minimo">
                                        <div class="mb-3">
                                            <label for="estoque_minimo" class="form-label fw-semibold">Estoque Mínimo</label>
                                            <input type="number" 
                                                   class="form-control @error('estoque_minimo') is-invalid @enderror" 
                                                   id="estoque_minimo" 
                                                   name="estoque_minimo" 
                                                   value="{{ old('estoque_minimo', $produto->estoque_minimo) }}"
                                                   min="0">
                                            @error('estoque_minimo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="campo_estoque_maximo">
                                        <div class="mb-3">
                                            <label for="estoque_maximo" class="form-label fw-semibold">Estoque Máximo</label>
                                            <input type="number" 
                                                   class="form-control @error('estoque_maximo') is-invalid @enderror" 
                                                   id="estoque_maximo" 
                                                   name="estoque_maximo" 
                                                   value="{{ old('estoque_maximo', $produto->estoque_maximo) }}"
                                                   min="0">
                                            @error('estoque_maximo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Painel Lateral -->
                    <div class="col-lg-4">
                        <!-- Status e Configurações -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-cog text-primary me-2"></i>Status e Configurações
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="ativo" 
                                               name="ativo" 
                                               value="1"
                                               {{ old('ativo', $produto->ativo) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="ativo">
                                            Produto Ativo
                                        </label>
                                    </div>
                                    <small class="text-muted">Produto aparece para venda</small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="destaque" 
                                               name="destaque" 
                                               value="1"
                                               {{ old('destaque', $produto->destaque) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="destaque">
                                            Produto em Destaque
                                        </label>
                                    </div>
                                    <small class="text-muted">Aparece nas seções especiais</small>
                                </div>
                            </div>
                        </div>

                        <!-- Upload de Imagens -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-images text-primary me-2"></i>Imagens do Produto
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Imagem Principal Atual -->
                                @if($produto->imagem_principal)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Imagem Principal Atual</label>
                                        <div class="text-center">
                                            <img src="{{ $produto->imagem_principal }}" 
                                                 alt="Imagem Principal" 
                                                 class="img-fluid rounded shadow-sm"
                                                 style="max-height: 150px;">
                                        </div>
                                    </div>
                                @endif

                                <!-- Upload Nova Imagem Principal -->
                                <div class="mb-3">
                                    <label for="imagem_principal" class="form-label fw-semibold">
                                        {{ $produto->imagem_principal ? 'Alterar' : 'Nova' }} Imagem Principal
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('imagem_principal') is-invalid @enderror" 
                                           id="imagem_principal" 
                                           name="imagem_principal" 
                                           accept="image/*">
                                    @error('imagem_principal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">JPG, PNG ou GIF. Máx. 2MB</small>
                                </div>

                                <!-- Upload Imagens Adicionais -->
                                <div class="mb-3">
                                    <label for="imagens_adicionais" class="form-label fw-semibold">Imagens Adicionais</label>
                                    <input type="file" 
                                           class="form-control @error('imagens_adicionais.*') is-invalid @enderror" 
                                           id="imagens_adicionais" 
                                           name="imagens_adicionais[]" 
                                           accept="image/*" 
                                           multiple>
                                    @error('imagens_adicionais.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Selecione múltiplas imagens</small>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Salvar Alterações
                                    </button>
                                    <a href="{{ route('comerciantes.produtos.show', $produto->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    // Máscara para valores monetários
    $('.money-mask').mask('#.##0,00', {
        reverse: true,
        translation: {
            '#': {pattern: /[0-9]/}
        }
    });

    // Controle de exibição dos campos de estoque
    function toggleCamposEstoque() {
        const controlaEstoque = $('#controla_estoque').is(':checked');
        
        if (controlaEstoque) {
            $('#campo_quantidade_estoque, #campo_estoque_minimo, #campo_estoque_maximo').show();
        } else {
            $('#campo_quantidade_estoque, #campo_estoque_minimo, #campo_estoque_maximo').hide();
            $('#quantidade_estoque, #estoque_minimo, #estoque_maximo').val('');
        }
    }

    // Aplicar controle inicial
    toggleCamposEstoque();

    // Evento de mudança do checkbox
    $('#controla_estoque').change(function() {
        toggleCamposEstoque();
    });

    // Validação do formulário
    $('#formProduto').on('submit', function(e) {
        let isValid = true;
        
        // Validar campos obrigatórios
        $('.form-control[required]').each(function() {
            if (!$(this).val().trim()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });

    // Preview das imagens
    $('#imagem_principal').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Criar preview se não existir
                if (!$('#preview-principal').length) {
                    $('#imagem_principal').after(`
                        <div id="preview-principal" class="mt-2 text-center">
                            <img src="" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 100px;">
                        </div>
                    `);
                }
                $('#preview-principal img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-switch .form-check-input {
    width: 2.5rem;
    height: 1.25rem;
}

.card {
    transition: all 0.2s ease-in-out;
}

.money-mask {
    text-align: right;
}

.is-invalid {
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 20%, 40%, 60%, 80% {
        transform: translateX(-2px);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(2px);
    }
}
</style>
@endpush

@extends('layouts.comerciante')

@section('title', 'Nova Subcategoria')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> Nova Subcategoria
        </h1>
        <div>
            <a href="{{ route('comerciantes.produtos.subcategorias.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informações da Subcategoria
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('comerciantes.produtos.subcategorias.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="categoria_id">Categoria Principal *</label>
                                    <select name="categoria_id" id="categoria_id" class="form-control @error('categoria_id') is-invalid @enderror" required>
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
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id">Subcategoria Pai</label>
                                    <select name="parent_id" id="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                        <option value="">Subcategoria principal (sem pai)</option>
                                        @foreach($subcategoriasPai as $pai)
                                            <option value="{{ $pai->id }}" {{ old('parent_id') == $pai->id ? 'selected' : '' }}>
                                                {{ $pai->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Deixe vazio para criar uma subcategoria principal
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nome">Nome da Subcategoria *</label>
                                    <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" 
                                           value="{{ old('nome') }}" required maxlength="255">
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="slug">Slug (URL amigável)</label>
                                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" 
                                           value="{{ old('slug') }}" maxlength="255">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Deixe vazio para gerar automaticamente
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea name="descricao" id="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror" 
                                      maxlength="1000">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="icone">Ícone (FontAwesome)</label>
                                    <input type="text" name="icone" id="icone" class="form-control @error('icone') is-invalid @enderror" 
                                           value="{{ old('icone') }}" placeholder="fas fa-tag" maxlength="100">
                                    @error('icone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Ex: fas fa-tag, fas fa-star, etc.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cor_fundo">Cor de Fundo</label>
                                    <input type="color" name="cor_fundo" id="cor_fundo" class="form-control @error('cor_fundo') is-invalid @enderror" 
                                           value="{{ old('cor_fundo', '#007bff') }}">
                                    @error('cor_fundo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ordem">Ordem de Exibição</label>
                                    <input type="number" name="ordem" id="ordem" class="form-control @error('ordem') is-invalid @enderror" 
                                           value="{{ old('ordem') }}" min="1">
                                    @error('ordem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Deixe vazio para definir automaticamente
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="imagem">Imagem da Subcategoria</label>
                            <input type="file" name="imagem" id="imagem" class="form-control-file @error('imagem') is-invalid @enderror" 
                                   accept="image/*">
                            @error('imagem')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos aceitos: JPEG, PNG, JPG, GIF. Tamanho máximo: 2MB
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1" 
                                       {{ old('ativo', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="ativo">
                                    Subcategoria ativa
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Subcategoria
                                </button>
                                <a href="{{ route('comerciantes.produtos.subcategorias.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- SEO -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-search"></i> SEO (Opcional)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control @error('meta_title') is-invalid @enderror" 
                               value="{{ old('meta_title') }}" maxlength="255">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3" 
                                  class="form-control @error('meta_description') is-invalid @enderror" 
                                  maxlength="500">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="meta_keywords">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" 
                               value="{{ old('meta_keywords') }}" maxlength="255" placeholder="palavra1, palavra2, palavra3">
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Preview do Ícone -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-eye"></i> Preview
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div id="icon-preview" style="display: none;">
                        <i id="icon-element" class="fa-3x mb-2 text-primary"></i>
                        <br>
                        <span id="name-preview" class="font-weight-bold"></span>
                    </div>
                    <div id="no-preview" class="text-muted">
                        <i class="fas fa-eye-slash fa-2x mb-2"></i>
                        <br>
                        Preencha os campos para ver o preview
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gerar slug automaticamente
    $('#nome').on('input', function() {
        const nome = $(this).val();
        if (nome && !$('#slug').val()) {
            const slug = nome.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            $('#slug').val(slug);
        }
        updatePreview();
    });

    // Atualizar preview do ícone
    $('#icone, #nome').on('input', updatePreview);

    function updatePreview() {
        const icone = $('#icone').val();
        const nome = $('#nome').val();

        if (icone && nome) {
            $('#icon-element').attr('class', icone + ' fa-3x mb-2 text-primary');
            $('#name-preview').text(nome);
            $('#icon-preview').show();
            $('#no-preview').hide();
        } else {
            $('#icon-preview').hide();
            $('#no-preview').show();
        }
    }

    // Buscar subcategorias quando categoria mudar
    $('#categoria_id').change(function() {
        const categoriaId = $(this).val();
        const parentSelect = $('#parent_id');
        
        parentSelect.empty();
        parentSelect.append('<option value="">Subcategoria principal (sem pai)</option>');
        
        if (categoriaId) {
            $.ajax({
                url: '/comerciantes/produtos/subcategorias/principais-por-categoria',
                data: { categoria_id: categoriaId },
                success: function(subcategorias) {
                    subcategorias.forEach(function(sub) {
                        parentSelect.append(`<option value="${sub.id}">${sub.nome}</option>`);
                    });
                }
            });
        }
    });

    // Preview da imagem
    $('#imagem').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Adicionar preview da imagem se necessário
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush

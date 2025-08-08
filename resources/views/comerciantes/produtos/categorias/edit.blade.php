@extends('comerciantes.layouts.app')

@section('title', 'Editar Categoria de Produto')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Editar Categoria de Produto</h4>
                <div class="page-title-right">
                    <a href="{{ route('comerciantes.produtos.categorias.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <form action="{{ route('comerciantes.produtos.categorias.update', $categoria->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" name="nome" value="{{ old('nome', $categoria->nome) }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoria_pai_id" class="form-label">Categoria Pai</label>
                                    <select class="form-select @error('categoria_pai_id') is-invalid @enderror" 
                                            id="categoria_pai_id" name="categoria_pai_id">
                                        <option value="">Categoria Principal</option>
                                        @foreach($categoriasPai as $categoriaPai)
                                            @if($categoriaPai->id != $categoria->id)
                                                <option value="{{ $categoriaPai->id }}" 
                                                        {{ old('categoria_pai_id', $categoria->categoria_pai_id) == $categoriaPai->id ? 'selected' : '' }}>
                                                    {{ $categoriaPai->nome }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('categoria_pai_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" name="descricao" rows="3">{{ old('descricao', $categoria->descricao) }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icone" class="form-label">Ícone</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('icone') is-invalid @enderror" 
                                               id="icone" name="icone" value="{{ old('icone', $categoria->icone) }}" 
                                               placeholder="Ex: bx bx-category">
                                        <button type="button" class="btn btn-outline-secondary" onclick="selecionarIcone()">
                                            <i class="bx bx-search"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Use ícones do BoxIcons (ex: bx bx-category)</small>
                                    @error('icone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cor" class="form-label">Cor</label>
                                    <input type="color" class="form-control form-control-color @error('cor') is-invalid @enderror" 
                                           id="cor" name="cor" value="{{ old('cor', $categoria->cor ?? '#495057') }}">
                                    @error('cor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ordem" class="form-label">Ordem de Exibição</label>
                                    <input type="number" class="form-control @error('ordem') is-invalid @enderror" 
                                           id="ordem" name="ordem" value="{{ old('ordem', $categoria->ordem ?? 0) }}" min="0">
                                    @error('ordem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="ativo" 
                                               name="ativo" value="1" {{ old('ativo', $categoria->ativo) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ativo">
                                            Categoria ativa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($categoria->produtos_count > 0)
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-1"></i>
                                Esta categoria possui <strong>{{ $categoria->produtos_count }}</strong> produto(s) vinculado(s).
                            </div>
                        @endif

                        @if($categoria->subcategorias_count > 0)
                            <div class="alert alert-warning">
                                <i class="bx bx-warning me-1"></i>
                                Esta categoria possui <strong>{{ $categoria->subcategorias_count }}</strong> subcategoria(s).
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="text-end">
                            <a href="{{ route('comerciantes.produtos.categorias.index') }}" class="btn btn-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Atualizar Categoria
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div id="preview" class="text-center">
                        <div class="mb-3">
                            <i id="previewIcone" class="font-size-48"></i>
                        </div>
                        <h6 id="previewNome"></h6>
                        <p id="previewDescricao" class="text-muted small"></p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Criado em:</strong> {{ $categoria->created_at->format('d/m/Y H:i') }}</li>
                        <li><strong>Atualizado em:</strong> {{ $categoria->updated_at->format('d/m/Y H:i') }}</li>
                        @if($categoria->categoria_pai)
                            <li><strong>Categoria Pai:</strong> {{ $categoria->categoria_pai->nome }}</li>
                        @endif
                        <li><strong>Produtos:</strong> {{ $categoria->produtos_count ?? 0 }}</li>
                        <li><strong>Subcategorias:</strong> {{ $categoria->subcategorias_count ?? 0 }}</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ícones Populares</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-category">
                                <i class="bx bx-category"></i>
                            </button>
                        </div>
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-package">
                                <i class="bx bx-package"></i>
                            </button>
                        </div>
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-shopping-bag">
                                <i class="bx bx-shopping-bag"></i>
                            </button>
                        </div>
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-store">
                                <i class="bx bx-store"></i>
                            </button>
                        </div>
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-food-menu">
                                <i class="bx bx-food-menu"></i>
                            </button>
                        </div>
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-drink">
                                <i class="bx bx-drink"></i>
                            </button>
                        </div>
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-book">
                                <i class="bx bx-book"></i>
                            </button>
                        </div>
                        <div class="col-3 text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 icone-btn" data-icone="bx bx-laptop">
                                <i class="bx bx-laptop"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Atualizar preview em tempo real
function atualizarPreview() {
    const nome = document.getElementById('nome').value || 'Nome da Categoria';
    const descricao = document.getElementById('descricao').value || 'Descrição da categoria';
    const icone = document.getElementById('icone').value || 'bx bx-category';
    const cor = document.getElementById('cor').value || '#495057';

    document.getElementById('previewNome').textContent = nome;
    document.getElementById('previewDescricao').textContent = descricao;
    document.getElementById('previewIcone').className = icone + ' font-size-48';
    document.getElementById('previewIcone').style.color = cor;
}

// Event listeners para atualizar preview
document.getElementById('nome').addEventListener('input', atualizarPreview);
document.getElementById('descricao').addEventListener('input', atualizarPreview);
document.getElementById('icone').addEventListener('input', atualizarPreview);
document.getElementById('cor').addEventListener('change', atualizarPreview);

// Seleção de ícones
document.querySelectorAll('.icone-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const icone = this.dataset.icone;
        document.getElementById('icone').value = icone;
        atualizarPreview();
    });
});

function selecionarIcone() {
    // Implementar modal de seleção de ícones se necessário
    alert('Modal de seleção de ícones será implementado em breve');
}

// Inicializar preview ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    atualizarPreview();
});
</script>
@endpush

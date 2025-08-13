@extends('layouts.comerciante')

@section('title', 'Editar Subcategoria')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Editar Subcategoria</h1>
                    <p class="text-muted">Edite as informações da subcategoria</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.produtos.subcategorias.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Subcategoria
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.produtos.subcategorias.update', $subcategoria->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoria Principal <span class="text-danger">*</span></label>
                                    <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                                        <option value="">Selecione uma categoria</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" 
                                                    {{ (old('categoria_id', $subcategoria->categoria_id) == $categoria->id) ? 'selected' : '' }}>
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
                                    <label for="pai_id" class="form-label">Subcategoria Pai</label>
                                    <select name="pai_id" id="pai_id" class="form-select @error('pai_id') is-invalid @enderror">
                                        <option value="">Subcategoria de nível 1 (sem pai)</option>
                                        @foreach($subcategoriasPais as $pai)
                                            <option value="{{ $pai->id }}" 
                                                    {{ (old('pai_id', $subcategoria->pai_id) == $pai->id) ? 'selected' : '' }}>
                                                {{ $pai->nome }}
                                                @if($pai->categoria)
                                                    ({{ $pai->categoria->nome }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pai_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Deixe vazio para criar uma subcategoria de primeiro nível
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome da Subcategoria <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control @error('nome') is-invalid @enderror" 
                                           value="{{ old('nome', $subcategoria->nome) }}" 
                                           required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="slug" 
                                           id="slug" 
                                           class="form-control @error('slug') is-invalid @enderror" 
                                           value="{{ old('slug', $subcategoria->slug) }}" 
                                           required>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-link me-1"></i>
                                        URL amigável (preenchido automaticamente)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea name="descricao" 
                                              id="descricao" 
                                              class="form-control @error('descricao') is-invalid @enderror" 
                                              rows="4"
                                              placeholder="Descrição detalhada da subcategoria">{{ old('descricao', $subcategoria->descricao) }}</textarea>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ordem" class="form-label">Ordem de Exibição</label>
                                    <input type="number" 
                                           name="ordem" 
                                           id="ordem" 
                                           class="form-control @error('ordem') is-invalid @enderror" 
                                           value="{{ old('ordem', $subcategoria->ordem ?? 0) }}" 
                                           min="0">
                                    @error('ordem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-sort-numeric-up me-1"></i>
                                        Menor número aparece primeiro
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cor" class="form-label">Cor de Destaque</label>
                                    <input type="color" 
                                           name="cor" 
                                           id="cor" 
                                           class="form-control form-control-color @error('cor') is-invalid @enderror" 
                                           value="{{ old('cor', $subcategoria->cor ?? '#6366f1') }}">
                                    @error('cor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-palette me-1"></i>
                                        Cor usada na interface
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ativa" 
                                           id="ativa" 
                                           value="1"
                                           {{ old('ativa', $subcategoria->ativa ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativa">
                                        <strong>Subcategoria Ativa</strong>
                                        <br><small class="text-muted">Exibir subcategoria no site</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="destaque" 
                                           id="destaque" 
                                           value="1"
                                           {{ old('destaque', $subcategoria->destaque) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="destaque">
                                        <strong>Subcategoria em Destaque</strong>
                                        <br><small class="text-muted">Exibir na página inicial</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Preview da Hierarquia -->
                        <div class="card bg-light mb-3" id="hierarchy-preview">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-sitemap me-2"></i>Prévia da Hierarquia
                                </h6>
                                <div id="hierarchy-path" class="text-muted">
                                    Selecione uma categoria para ver a hierarquia
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="card bg-info bg-opacity-10 mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="fas fa-info-circle me-2"></i>Informações do Registro
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">
                                            <strong>Criado em:</strong> {{ $subcategoria->created_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">
                                            <strong>Última atualização:</strong> {{ $subcategoria->updated_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">
                                            <strong>Produtos vinculados:</strong> {{ $subcategoria->produtos_count ?? 0 }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('comerciantes.produtos.subcategorias.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Atualizar Subcategoria
                                </button>
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
document.addEventListener('DOMContentLoaded', function() {
    const nomeInput = document.getElementById('nome');
    const slugInput = document.getElementById('slug');
    const categoriaSelect = document.getElementById('categoria_id');
    const paiSelect = document.getElementById('pai_id');
    const hierarchyPath = document.getElementById('hierarchy-path');
    
    // Auto-gerar slug a partir do nome
    nomeInput.addEventListener('input', function() {
        if (!slugInput.dataset.userModified) {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });
    
    // Marcar slug como modificado pelo usuário
    slugInput.addEventListener('input', function() {
        slugInput.dataset.userModified = 'true';
    });
    
    // Atualizar prévia da hierarquia
    function updateHierarchyPreview() {
        const categoriaOption = categoriaSelect.options[categoriaSelect.selectedIndex];
        const paiOption = paiSelect.options[paiSelect.selectedIndex];
        const nomeAtual = nomeInput.value || '[Nome da Subcategoria]';
        
        let path = '';
        
        if (categoriaOption && categoriaOption.value) {
            path = categoriaOption.text;
            
            if (paiOption && paiOption.value) {
                const paiText = paiOption.text.split(' (')[0]; // Remove a categoria entre parênteses
                path += ' → ' + paiText;
            }
            
            path += ' → ' + nomeAtual;
        } else {
            path = 'Selecione uma categoria para ver a hierarquia';
        }
        
        hierarchyPath.innerHTML = `
            <i class="fas fa-layer-group me-2"></i>
            ${path}
        `;
    }
    
    // Event listeners para atualizar hierarquia
    categoriaSelect.addEventListener('change', updateHierarchyPreview);
    paiSelect.addEventListener('change', updateHierarchyPreview);
    nomeInput.addEventListener('input', updateHierarchyPreview);
    
    // Carregar subcategorias pai quando categoria mudar
    categoriaSelect.addEventListener('change', function() {
        const categoriaId = this.value;
        
        if (categoriaId) {
            // Em uma implementação real, carregaria via AJAX
            // Por ora, mantém as opções existentes
        }
    });
    
    // Inicializar prévia
    updateHierarchyPreview();
    
    // Validação do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const nome = nomeInput.value.trim();
        const slug = slugInput.value.trim();
        const categoria = categoriaSelect.value;
        
        if (!nome) {
            e.preventDefault();
            alert('Por favor, digite um nome para a subcategoria.');
            nomeInput.focus();
            return false;
        }
        
        if (!slug) {
            e.preventDefault();
            alert('Por favor, digite um slug para a subcategoria.');
            slugInput.focus();
            return false;
        }
        
        if (!categoria) {
            e.preventDefault();
            alert('Por favor, selecione uma categoria principal.');
            categoriaSelect.focus();
            return false;
        }
        
        // Validar se não está tentando ser pai de si mesmo
        const pai = paiSelect.value;
        if (pai && pai === '{{ $subcategoria->id }}') {
            e.preventDefault();
            alert('Uma subcategoria não pode ser pai de si mesma.');
            paiSelect.focus();
            return false;
        }
    });
});
</script>
@endpush

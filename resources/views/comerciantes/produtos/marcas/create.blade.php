@extends('layouts.comerciante')

@section('title', 'Nova Marca de Produto')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Nova Marca de Produto</h4>
                <div class="page-title-right">
                    <a href="{{ route('comerciantes.produtos.marcas.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <form action="{{ route('comerciantes.produtos.marcas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome da Marca <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" name="nome" value="{{ old('nome') }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug') }}" readonly>
                                    <small class="text-muted">Gerado automaticamente a partir do nome</small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Logo da Marca</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo" accept="image/*">
                                    <small class="text-muted">Formatos aceitos: JPG, PNG, GIF. Máximo: 2MB</small>
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website') }}" 
                                           placeholder="https://exemplo.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light border">
                            <div class="card-header">
                                <h6 class="mb-0">Informações de Contato</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contato_nome" class="form-label">Nome do Contato</label>
                                            <input type="text" class="form-control @error('contato_nome') is-invalid @enderror" 
                                                   id="contato_nome" name="contato_nome" value="{{ old('contato_nome') }}">
                                            @error('contato_nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contato_email" class="form-label">E-mail de Contato</label>
                                            <input type="email" class="form-control @error('contato_email') is-invalid @enderror" 
                                                   id="contato_email" name="contato_email" value="{{ old('contato_email') }}">
                                            @error('contato_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contato_telefone" class="form-label">Telefone</label>
                                            <input type="text" class="form-control @error('contato_telefone') is-invalid @enderror" 
                                                   id="contato_telefone" name="contato_telefone" value="{{ old('contato_telefone') }}">
                                            @error('contato_telefone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contato_whatsapp" class="form-label">WhatsApp</label>
                                            <input type="text" class="form-control @error('contato_whatsapp') is-invalid @enderror" 
                                                   id="contato_whatsapp" name="contato_whatsapp" value="{{ old('contato_whatsapp') }}">
                                            @error('contato_whatsapp')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ordem" class="form-label">Ordem de Exibição</label>
                                    <input type="number" class="form-control @error('ordem') is-invalid @enderror" 
                                           id="ordem" name="ordem" value="{{ old('ordem', 0) }}" min="0">
                                    @error('ordem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="ativo" 
                                               name="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ativo">
                                            Marca ativa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="text-end">
                            <a href="{{ route('comerciantes.produtos.marcas.index') }}" class="btn btn-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Salvar Marca
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preview da Marca</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div id="logoPreview" class="mx-auto" style="width: 100px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                            <i class="bx bx-image font-size-24 text-muted"></i>
                        </div>
                    </div>
                    <h6 id="previewNome">Nome da Marca</h6>
                    <p id="previewDescricao" class="text-muted small">Descrição da marca</p>
                    <div id="previewWebsite" class="d-none">
                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-link-external me-1"></i> Website
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Dicas</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-1"></i>
                            Use um nome claro e reconhecível
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-1"></i>
                            Adicione uma logo de alta qualidade
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-1"></i>
                            Preencha as informações de contato
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-1"></i>
                            Escreva uma descrição atrativa
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gerar slug automaticamente
function gerarSlug(texto) {
    return texto
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// Atualizar preview em tempo real
function atualizarPreview() {
    const nome = document.getElementById('nome').value || 'Nome da Marca';
    const descricao = document.getElementById('descricao').value || 'Descrição da marca';
    const website = document.getElementById('website').value;

    document.getElementById('previewNome').textContent = nome;
    document.getElementById('previewDescricao').textContent = descricao;
    
    if (website) {
        document.getElementById('previewWebsite').classList.remove('d-none');
        document.querySelector('#previewWebsite a').href = website;
    } else {
        document.getElementById('previewWebsite').classList.add('d-none');
    }
}

// Event listeners
document.getElementById('nome').addEventListener('input', function() {
    const nome = this.value;
    document.getElementById('slug').value = gerarSlug(nome);
    atualizarPreview();
});

document.getElementById('descricao').addEventListener('input', atualizarPreview);
document.getElementById('website').addEventListener('input', atualizarPreview);

// Preview da logo
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('logoPreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 6px;">';
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<i class="bx bx-image font-size-24 text-muted"></i>';
    }
});

// Máscaras para telefones
document.getElementById('contato_telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
    }
    e.target.value = value;
});

document.getElementById('contato_whatsapp').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
    }
    e.target.value = value;
});

// Inicializar preview
atualizarPreview();
</script>
@endpush

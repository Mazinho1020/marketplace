@extends('layouts.comerciante')

@section('title', 'Editar Marca de Produto')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Editar Marca de Produto</h4>
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
                <form action="{{ route('comerciantes.produtos.marcas.update', $marca->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome da Marca <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" name="nome" value="{{ old('nome', $marca->nome) }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $marca->slug) }}" readonly>
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
                                      id="descricao" name="descricao" rows="3">{{ old('descricao', $marca->descricao) }}</textarea>
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
                                    @if($marca->logo)
                                        <div class="mt-2">
                                            <small class="text-info">Logo atual:</small>
                                            <img src="{{ asset('storage/' . $marca->logo) }}" alt="Logo atual" class="img-thumbnail" style="max-width: 100px;">
                                        </div>
                                    @endif
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website', $marca->website) }}" 
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
                                                   id="contato_nome" name="contato_nome" value="{{ old('contato_nome', $marca->contato_nome) }}">
                                            @error('contato_nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contato_email" class="form-label">E-mail de Contato</label>
                                            <input type="email" class="form-control @error('contato_email') is-invalid @enderror" 
                                                   id="contato_email" name="contato_email" value="{{ old('contato_email', $marca->contato_email) }}">
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
                                                   id="contato_telefone" name="contato_telefone" value="{{ old('contato_telefone', $marca->contato_telefone) }}">
                                            @error('contato_telefone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contato_whatsapp" class="form-label">WhatsApp</label>
                                            <input type="text" class="form-control @error('contato_whatsapp') is-invalid @enderror" 
                                                   id="contato_whatsapp" name="contato_whatsapp" value="{{ old('contato_whatsapp', $marca->contato_whatsapp) }}">
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
                                           id="ordem" name="ordem" value="{{ old('ordem', $marca->ordem ?? 0) }}" min="0">
                                    @error('ordem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="ativo" 
                                               name="ativo" value="1" {{ old('ativo', $marca->ativo) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ativo">
                                            Marca ativa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($marca->produtos_count > 0)
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-1"></i>
                                Esta marca possui <strong>{{ $marca->produtos_count }}</strong> produto(s) vinculado(s).
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="text-end">
                            <a href="{{ route('comerciantes.produtos.marcas.index') }}" class="btn btn-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Atualizar Marca
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
                            @if($marca->logo)
                                <img src="{{ asset('storage/' . $marca->logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 6px;">
                            @else
                                <i class="bx bx-image font-size-24 text-muted"></i>
                            @endif
                        </div>
                    </div>
                    <h6 id="previewNome">{{ $marca->nome }}</h6>
                    <p id="previewDescricao" class="text-muted small">{{ $marca->descricao ?: 'Descrição da marca' }}</p>
                    <div id="previewWebsite" class="{{ $marca->website ? '' : 'd-none' }}">
                        <a href="{{ $marca->website }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-link-external me-1"></i> Website
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Criado em:</strong> {{ $marca->created_at->format('d/m/Y H:i') }}</li>
                        <li><strong>Atualizado em:</strong> {{ $marca->updated_at->format('d/m/Y H:i') }}</li>
                        <li><strong>Produtos:</strong> {{ $marca->produtos_count ?? 0 }}</li>
                        <li><strong>Status:</strong> 
                            <span class="badge bg-{{ $marca->ativo ? 'success' : 'danger' }}">
                                {{ $marca->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            @if($marca->logo)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ações da Logo</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ asset('storage/' . $marca->logo) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-show me-1"></i> Visualizar Logo
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerLogo()">
                            <i class="bx bx-trash me-1"></i> Remover Logo
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Remover Logo -->
<div class="modal fade" id="modalRemoverLogo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remover Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover a logo desta marca?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('comerciantes.produtos.marcas.remover-logo', $marca->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remover Logo</button>
                </form>
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

// Função para remover logo
function removerLogo() {
    const modal = new bootstrap.Modal(document.getElementById('modalRemoverLogo'));
    modal.show();
}

// Inicializar preview
atualizarPreview();
</script>
@endpush

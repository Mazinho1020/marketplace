@extends('layouts.comerciante')

@section('title', 'Galeria de Imagens - ' . $produto->nome)

@section('content')
<!-- Script de emergência carregado no início -->
<script src="{{ asset('js/emergency-functions.js') }}"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>🖼️ Galeria de Imagens</h1>
                    <p class="text-muted mb-0">{{ $produto->nome }}</p>
                </div>
                <div>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUpload">
                        📤 Enviar Imagens
                    </button>
                    <a href="{{ route('comerciantes.produtos.show', $produto->id) }}" class="btn btn-outline-primary">
                        👁️ Ver Produto
                    </a>
                    <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-secondary">
                        ← Voltar aos Produtos
                    </a>
                </div>
            </div>

            <!-- Informações do Produto -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5>{{ $produto->nome }}</h5>
                            <p class="mb-1"><strong>SKU:</strong> {{ $produto->sku }}</p>
                            <p class="mb-1"><strong>Categoria:</strong> {{ $produto->categoria->nome ?? '-' }}</p>
                            <p class="mb-0"><strong>Marca:</strong> {{ $produto->marca->nome ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="badge badge-primary badge-lg">
                                {{ $produto->imagens->count() }} imagem(ns)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($produto->imagens->count() > 0)
                <!-- Galeria de Imagens -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">🖼️ Imagens do Produto</h5>
                        <small class="text-muted">Arraste para reordenar</small>
                    </div>
                    <div class="card-body">
                        <div class="row" id="galeria-imagens">
                            @foreach($produto->imagens->sortBy('ordem') as $imagem)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-imagem-id="{{ $imagem->id }}">
                                <div class="card h-100 imagem-card">
                                    <!-- Badge do Tipo -->
                                    <div class="position-relative">
                                        @if($imagem->tipo === 'principal')
                                            <span class="badge badge-danger position-absolute" style="top: 5px; left: 5px; z-index: 10;">
                                                ⭐ Principal
                                            </span>
                                        @else
                                            <span class="badge badge-secondary position-absolute" style="top: 5px; left: 5px; z-index: 10;">
                                                📷 {{ ucfirst($imagem->tipo) }}
                                            </span>
                                        @endif
                                        
                                        <!-- Imagem -->
                                        <img src="{{ asset('storage/produtos/' . $imagem->arquivo) }}" 
                                             class="card-img-top" 
                                             style="height: 200px; object-fit: cover; cursor: pointer;"
                                             alt="{{ $imagem->alt_text }}"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#modalVisualizarImagem"
                                             data-imagem-url="{{ asset('storage/produtos/' . $imagem->arquivo) }}"
                                             data-imagem-titulo="{{ $imagem->titulo }}"
                                             data-imagem-alt="{{ $imagem->alt_text }}">
                                    </div>
                                    
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1">{{ $imagem->titulo ?: 'Sem título' }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Ordem: {{ $imagem->ordem }}<br>
                                                {{ $imagem->dimensoes ?? 'N/A' }}
                                            </small>
                                        </p>
                                    </div>
                                    
                                    <div class="card-footer p-2">
                                        <div class="btn-group btn-group-sm w-100" role="group">
                                            @if($imagem->tipo !== 'principal')
                                                <button type="button" class="btn btn-outline-warning btn-sm" 
                                                        onclick="setPrincipal({{ $imagem->id }})"
                                                        title="Definir como Principal">
                                                    ⭐
                                                </button>
                                            @endif
                                            
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEditarImagem"
                                                    data-imagem-id="{{ $imagem->id }}"
                                                    data-imagem-titulo="{{ $imagem->titulo }}"
                                                    data-imagem-alt="{{ $imagem->alt_text }}"
                                                    data-imagem-tipo="{{ $imagem->tipo }}"
                                                    data-imagem-ordem="{{ $imagem->ordem }}"
                                                    title="Editar">
                                                ✏️
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="removerImagem({{ $imagem->id }})"
                                                    title="Remover">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- Estado Vazio -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-images fa-3x text-muted"></i>
                        </div>
                        <h5>Nenhuma imagem encontrada</h5>
                        <p class="text-muted">Este produto ainda não possui imagens.</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUpload">
                            📤 Enviar Primeira Imagem
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="modalUpload" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('comerciantes.produtos.imagens.upload', $produto->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">📤 Enviar Imagens</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="imagens">Selecionar Imagens *</label>
                        <input type="file" name="imagens[]" id="imagens" class="form-control-file" 
                               multiple accept="image/*" required>
                        <small class="form-text text-muted">
                            Formatos aceitos: JPEG, PNG, JPG, GIF, WebP. Tamanho máximo: 5MB por imagem.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo">Tipo das Imagens</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="galeria">📷 Galeria</option>
                            <option value="principal">⭐ Principal</option>
                            <option value="miniatura">🔍 Miniatura</option>
                            <option value="zoom">🔍 Zoom</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="titulo">Título</label>
                                <input type="text" name="titulo" id="titulo" class="form-control" 
                                       placeholder="Título das imagens" value="{{ $produto->nome }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="alt_text">Texto Alternativo</label>
                                <input type="text" name="alt_text" id="alt_text" class="form-control" 
                                       placeholder="Descrição da imagem" value="{{ $produto->nome }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview das imagens -->
                    <div id="preview-imagens" class="row mt-3" style="display: none;">
                        <div class="col-12">
                            <h6>📋 Preview das Imagens:</h6>
                            <div id="preview-container" class="row"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">📤 Enviar Imagens</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Imagem -->
<div class="modal fade" id="modalEditarImagem" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="formEditarImagem">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Editar Imagem</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_titulo">Título</label>
                        <input type="text" name="titulo" id="edit_titulo" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_alt_text">Texto Alternativo</label>
                        <input type="text" name="alt_text" id="edit_alt_text" class="form-control">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo">Tipo</label>
                                <select name="tipo" id="edit_tipo" class="form-control">
                                    <option value="galeria">📷 Galeria</option>
                                    <option value="principal">⭐ Principal</option>
                                    <option value="miniatura">🔍 Miniatura</option>
                                    <option value="zoom">🔍 Zoom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ordem">Ordem</label>
                                <input type="number" name="ordem" id="edit_ordem" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Visualizar Imagem -->
<div class="modal fade" id="modalVisualizarImagem" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="visualizar-titulo">Visualizar Imagem</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="visualizar-imagem" src="" class="img-fluid" alt="">
                <p id="visualizar-alt" class="text-muted mt-2"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<!-- Script inline para garantir carregamento -->
<script type="text/javascript">
// Verificar se jQuery está carregado
if (typeof jQuery === 'undefined') {
    console.error('jQuery não está carregado!');
}

// Definir funções imediatamente (não aguardar document ready)
window.setPrincipal = function(imagemId) {
    console.log('setPrincipal chamada com ID:', imagemId);
    if (confirm('Deseja definir esta imagem como principal?')) {
        window.location.href = `{{ route('comerciantes.produtos.imagens.setPrincipal', [$produto->id, '__ID__']) }}`.replace('__ID__', imagemId);
    }
};

window.removerImagem = function(imagemId) {
    console.log('removerImagem chamada com ID:', imagemId);
    if (confirm('Tem certeza que deseja remover esta imagem? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('comerciantes.produtos.imagens.destroy', [$produto->id, '__ID__']) }}`.replace('__ID__', imagemId);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
};

// Log para debug
console.log('Funções definidas:');
console.log('- removerImagem:', typeof window.removerImagem);
console.log('- setPrincipal:', typeof window.setPrincipal);

// Aguardar jQuery se disponível
if (typeof jQuery !== 'undefined') {
    $(document).ready(function() {
        console.log('jQuery document ready executado');
        
        // Preview de imagens no upload
        $('#imagens').on('change', function() {
            const files = this.files;
            const previewContainer = $('#preview-container');
            const previewSection = $('#preview-imagens');
            
            previewContainer.empty();
            
            if (files.length > 0) {
                previewSection.show();
                
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.append(`
                            <div class="col-md-3 mb-2">
                                <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                <small class="d-block text-center">${file.name}</small>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                previewSection.hide();
            }
        });

        // Modal Editar Imagem
        $('#modalEditarImagem').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const imagemId = button.data('imagem-id');
            const titulo = button.data('imagem-titulo');
            const altText = button.data('imagem-alt');
            const tipo = button.data('imagem-tipo');
            const ordem = button.data('imagem-ordem');
            
            const modal = $(this);
            modal.find('#edit_titulo').val(titulo);
            modal.find('#edit_alt_text').val(altText);
            modal.find('#edit_tipo').val(tipo);
            modal.find('#edit_ordem').val(ordem);
            
            // Atualizar action do form
            const form = modal.find('#formEditarImagem');
            form.attr('action', `{{ route('comerciantes.produtos.imagens.update', [$produto->id, '__ID__']) }}`.replace('__ID__', imagemId));
        });

        // Modal Visualizar Imagem
        $('#modalVisualizarImagem').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const imagemUrl = button.data('imagem-url');
            const titulo = button.data('imagem-titulo');
            const altText = button.data('imagem-alt');
            
            const modal = $(this);
            modal.find('#visualizar-titulo').text(titulo || 'Visualizar Imagem');
            modal.find('#visualizar-imagem').attr('src', imagemUrl);
            modal.find('#visualizar-alt').text(altText || '');
        });

        // Sortable para reordenar imagens
        const galeria = document.getElementById('galeria-imagens');
        if (galeria && typeof Sortable !== 'undefined') {
            new Sortable(galeria, {
                animation: 150,
                onEnd: function(evt) {
                    const imagensIds = Array.from(galeria.children).map(div => div.dataset.imagemId);
                    
                    // Enviar nova ordem para o servidor
                    fetch(`{{ route('comerciantes.produtos.imagens.reordenar', $produto->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            imagens: imagensIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Atualizar números de ordem na interface
                            galeria.querySelectorAll('.card-text small').forEach((small, index) => {
                                small.innerHTML = small.innerHTML.replace(/Ordem: \d+/, `Ordem: ${index + 1}`);
                            });
                            console.log('Ordem das imagens atualizada com sucesso');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao reordenar:', error);
                    });
                }
            });
        }
    });
} else {
    console.error('jQuery não disponível, funcionalidades limitadas');
}
</script>
@endpush

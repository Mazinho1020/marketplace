@extends('layouts.comerciante')

@section('title', 'Nova Marca')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="fas fa-plus me-2 text-primary"></i>
                    Nova Marca
                </h2>
                <p class="text-muted mb-0">Crie uma nova marca para organizar suas empresas</p>
            </div>
            <a href="{{ route('comerciantes.marcas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informações da Marca
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('comerciantes.marcas.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Nome da Marca -->
                        <div class="col-md-8 mb-3">
                            <label for="nome" class="form-label">
                                <i class="fas fa-tag me-1"></i>
                                Nome da Marca *
                            </label>
                            <input type="text" 
                                   class="form-control @error('nome') is-invalid @enderror" 
                                   id="nome" 
                                   name="nome" 
                                   value="{{ old('nome') }}"
                                   placeholder="Ex: Pizzaria Tradição"
                                   required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Este será o nome principal da sua marca</div>
                        </div>
                        
                        <!-- Status -->
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>
                                Status
                            </label>
                            <select class="form-select" id="status" name="status">
                                <option value="ativa" selected>Ativa</option>
                                <option value="inativa">Inativa</option>
                            </select>
                            <div class="form-text">Marcas inativas ficam ocultas</div>
                        </div>
                    </div>
                    
                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="descricao" class="form-label">
                            <i class="fas fa-align-left me-1"></i>
                            Descrição da Marca
                        </label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                  id="descricao" 
                                  name="descricao" 
                                  rows="3"
                                  placeholder="Descreva sua marca, seus valores e diferenciais...">{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Esta descrição será exibida no perfil da marca</div>
                    </div>
                    
                    <!-- Logo -->
                    <div class="mb-4">
                        <label for="logo" class="form-label">
                            <i class="fas fa-image me-1"></i>
                            Logo da Marca
                        </label>
                        <input type="file" 
                               class="form-control @error('logo') is-invalid @enderror" 
                               id="logo" 
                               name="logo"
                               accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                        
                        <!-- Preview do Logo -->
                        <div id="logoPreview" class="mt-2" style="display: none;">
                            <img id="logoImg" src="" alt="Preview" style="max-width: 150px; max-height: 150px;" class="border rounded">
                        </div>
                    </div>
                    
                    <!-- Identidade Visual -->
                    <div class="mb-4">
                        <h6 class="mb-3">
                            <i class="fas fa-palette me-2"></i>
                            Identidade Visual
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cor_primaria" class="form-label">Cor Primária</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color @error('cor_primaria') is-invalid @enderror" 
                                           id="cor_primaria" 
                                           name="cor_primaria" 
                                           value="{{ old('cor_primaria', '#2ECC71') }}">
                                    <input type="text" 
                                           class="form-control" 
                                           id="cor_primaria_text" 
                                           value="{{ old('cor_primaria', '#2ECC71') }}" 
                                           readonly>
                                </div>
                                @error('cor_primaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="cor_secundaria" class="form-label">Cor Secundária</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color @error('cor_secundaria') is-invalid @enderror" 
                                           id="cor_secundaria" 
                                           name="cor_secundaria" 
                                           value="{{ old('cor_secundaria', '#27AE60') }}">
                                    <input type="text" 
                                           class="form-control" 
                                           id="cor_secundaria_text" 
                                           value="{{ old('cor_secundaria', '#27AE60') }}" 
                                           readonly>
                                </div>
                                @error('cor_secundaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Preview das Cores -->
                        <div class="mt-2">
                            <small class="text-muted">Preview das cores:</small>
                            <div class="d-flex gap-2 mt-1">
                                <div id="preview_primaria" 
                                     class="rounded border" 
                                     style="width: 40px; height: 40px; background-color: #2ECC71;"></div>
                                <div id="preview_secundaria" 
                                     class="rounded border" 
                                     style="width: 40px; height: 40px; background-color: #27AE60;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Criar Marca
                        </button>
                        <a href="{{ route('comerciantes.marcas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar com Dicas -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Dicas Importantes
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">
                        <i class="fas fa-info-circle me-1"></i>
                        O que é uma Marca?
                    </h6>
                    <p class="small text-muted">
                        Uma marca é como um "guarda-chuva" que pode conter várias empresas (unidades/lojas). 
                        Por exemplo: "Pizzaria Tradição" pode ter unidades em diferentes bairros.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-success">
                        <i class="fas fa-palette me-1"></i>
                        Identidade Visual
                    </h6>
                    <p class="small text-muted">
                        As cores escolhidas serão usadas em todas as suas empresas e materiais relacionados a esta marca.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-warning">
                        <i class="fas fa-image me-1"></i>
                        Logo da Marca
                    </h6>
                    <p class="small text-muted">
                        Use um logo de boa qualidade. Ele será exibido em listagens e perfis da marca.
                    </p>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-rocket me-2"></i>
                    <strong>Próximo passo:</strong> Após criar a marca, você poderá adicionar suas empresas/unidades.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview do logo
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoImg').src = e.target.result;
                document.getElementById('logoPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('logoPreview').style.display = 'none';
        }
    });
    
    // Sincronizar cores com preview
    document.getElementById('cor_primaria').addEventListener('change', function() {
        const cor = this.value;
        document.getElementById('cor_primaria_text').value = cor;
        document.getElementById('preview_primaria').style.backgroundColor = cor;
    });
    
    document.getElementById('cor_secundaria').addEventListener('change', function() {
        const cor = this.value;
        document.getElementById('cor_secundaria_text').value = cor;
        document.getElementById('preview_secundaria').style.backgroundColor = cor;
    });
    
    // Gerar slug automaticamente baseado no nome
    document.getElementById('nome').addEventListener('input', function() {
        // Aqui você poderia mostrar um preview do slug que será gerado
        // O slug é gerado automaticamente no backend
    });
</script>
@endpush

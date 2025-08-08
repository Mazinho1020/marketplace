@extends('comerciantes.layout')

@section('title', 'Novo Cargo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Novo Cargo</h1>
                    <p class="text-muted mb-0">Preencha as informações do cargo</p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/cargos?empresa_id={{ $empresaId }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-plus"></i> Dados do Cargo
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Erro!</strong> Corrija os problemas abaixo:
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="/comerciantes/clientes/cargos">
                                @csrf
                                <input type="hidden" name="empresa_id" value="{{ $empresaId }}">

                                <div class="row">
                                    <!-- Código -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="codigo" class="form-label">
                                                Código
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('codigo') is-invalid @enderror" 
                                                   id="codigo" 
                                                   name="codigo" 
                                                   value="{{ old('codigo') }}"
                                                   maxlength="20"
                                                   placeholder="Ex: ADM001">
                                            @error('codigo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Código único para identificação (opcional)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Nome -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nome" class="form-label">
                                                Nome <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('nome') is-invalid @enderror" 
                                                   id="nome" 
                                                   name="nome" 
                                                   value="{{ old('nome') }}"
                                                   required 
                                                   maxlength="100"
                                                   placeholder="Ex: Analista Administrativo">
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Nome do cargo é obrigatório
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Descrição -->
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição</label>
                                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                                      id="descricao" 
                                                      name="descricao" 
                                                      rows="3"
                                                      maxlength="500"
                                                      placeholder="Descreva as responsabilidades e atividades do cargo">{{ old('descricao') }}</textarea>
                                            @error('descricao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Descrição das responsabilidades e atividades do cargo
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Departamento -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="departamento_id" class="form-label">Departamento</label>
                                            <select class="form-select @error('departamento_id') is-invalid @enderror" 
                                                    id="departamento_id" 
                                                    name="departamento_id">
                                                <option value="">Selecione um departamento</option>
                                                @foreach($departamentos as $departamento)
                                                    <option value="{{ $departamento->id }}" 
                                                            {{ old('departamento_id', request('departamento_id')) == $departamento->id ? 'selected' : '' }}>
                                                        {{ $departamento->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('departamento_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Departamento ao qual o cargo pertence
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Nível Hierárquico -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nivel_hierarquico" class="form-label">Nível Hierárquico</label>
                                            <select class="form-select @error('nivel_hierarquico') is-invalid @enderror" 
                                                    id="nivel_hierarquico" 
                                                    name="nivel_hierarquico">
                                                <option value="">Selecione o nível</option>
                                                <option value="1" {{ old('nivel_hierarquico') == '1' ? 'selected' : '' }}>
                                                    Nível 1 - Operacional
                                                </option>
                                                <option value="2" {{ old('nivel_hierarquico') == '2' ? 'selected' : '' }}>
                                                    Nível 2 - Supervisão
                                                </option>
                                                <option value="3" {{ old('nivel_hierarquico') == '3' ? 'selected' : '' }}>
                                                    Nível 3 - Coordenação
                                                </option>
                                                <option value="4" {{ old('nivel_hierarquico') == '4' ? 'selected' : '' }}>
                                                    Nível 4 - Gerência
                                                </option>
                                                <option value="5" {{ old('nivel_hierarquico') == '5' ? 'selected' : '' }}>
                                                    Nível 5 - Diretoria
                                                </option>
                                            </select>
                                            @error('nivel_hierarquico')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Nível hierárquico do cargo na organização (1=mais baixo, 5=mais alto)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Salário Base -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="salario_base" class="form-label">Salário Base</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" 
                                                       class="form-control @error('salario_base') is-invalid @enderror" 
                                                       id="salario_base" 
                                                       name="salario_base" 
                                                       value="{{ old('salario_base') }}"
                                                       min="0" 
                                                       step="0.01"
                                                       max="999999.99"
                                                       placeholder="0,00">
                                                @error('salario_base')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">
                                                Valor base do salário para o cargo (opcional)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Status Ativo -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input @error('ativo') is-invalid @enderror" 
                                                       type="checkbox" 
                                                       id="ativo" 
                                                       name="ativo" 
                                                       value="1"
                                                       {{ old('ativo', 1) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ativo">
                                                    <span class="fw-bold text-success">Ativo</span>
                                                </label>
                                            </div>
                                            @error('ativo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Marque para manter o cargo ativo
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <a href="/comerciantes/clientes/cargos?empresa_id={{ $empresaId }}" 
                                               class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Salvar Cargo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Informações -->
                <div class="col-lg-4">
                    <!-- Ajuda -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-question-circle"></i> Ajuda
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-primary">Campos Obrigatórios:</h6>
                            <ul class="small mb-3">
                                <li><strong>Nome:</strong> Nome do cargo (máx. 100 caracteres)</li>
                            </ul>
                            
                            <h6 class="text-info">Campos Opcionais:</h6>
                            <ul class="small mb-0">
                                <li><strong>Código:</strong> Identificador único (máx. 20 caracteres)</li>
                                <li><strong>Descrição:</strong> Detalhes sobre responsabilidades</li>
                                <li><strong>Departamento:</strong> Vínculo organizacional</li>
                                <li><strong>Nível Hierárquico:</strong> Posição na estrutura</li>
                                <li><strong>Salário Base:</strong> Valor de referência</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Estatísticas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-bar"></i> Cargos Existentes
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $stats['total'] ?? 0 }}</h4>
                                <p class="text-muted mb-1">Total de cargos</p>
                                <small class="text-success">{{ $stats['ativos'] ?? 0 }} ativos</small>
                            </div>
                        </div>
                    </div>

                    <!-- Links Úteis -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-link"></i> Links Úteis
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="/comerciantes/clientes/cargos?empresa_id={{ $empresaId }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list"></i> Ver Todos os Cargos
                                </a>
                                <a href="/comerciantes/clientes/departamentos?empresa_id={{ $empresaId }}" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-building"></i> Ver Departamentos
                                </a>
                                <a href="/comerciantes/clientes/pessoas?empresa_id={{ $empresaId }}" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-users"></i> Ver Funcionários
                                </a>
                            </div>
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
// Formatação do campo de salário
document.getElementById('salario_base').addEventListener('input', function(e) {
    let value = e.target.value;
    
    // Remove caracteres não numéricos, exceto ponto e vírgula
    value = value.replace(/[^\d.,]/g, '');
    
    // Substitui vírgula por ponto
    value = value.replace(',', '.');
    
    e.target.value = value;
});

// Auto-gerar código baseado no nome (opcional)
document.getElementById('nome').addEventListener('blur', function(e) {
    const codigo = document.getElementById('codigo');
    if (!codigo.value && e.target.value) {
        // Gerar código simples baseado no nome
        let codigoSugerido = e.target.value
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remove acentos
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '') // Remove caracteres especiais
            .substring(0, 6); // Primeiros 6 caracteres
        
        if (codigoSugerido) {
            codigo.value = codigoSugerido;
        }
    }
});
</script>
@endpush

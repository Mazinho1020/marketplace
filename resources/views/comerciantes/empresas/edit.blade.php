@extends('comerciantes.layout')

@section('title', 'Editar Empresa - ' . ($empresa->nome_fantasia ?: $empresa->razao_social))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Editar Empresa</h1>
        <p class="text-muted mb-0">{{ $empresa->nome_fantasia ?: $empresa->razao_social }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('comerciantes.empresas.show', $empresa) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
        <a href="{{ route('comerciantes.empresas.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-list me-1"></i>
            Todas as Empresas
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <h6><i class="fas fa-exclamation-triangle me-2"></i>Existem erros no formulário:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('comerciantes.empresas.update', $empresa) }}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Informações Básicas -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informações Básicas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nome_fantasia" class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nome_fantasia') is-invalid @enderror" 
                                   id="nome_fantasia" 
                                   name="nome_fantasia" 
                                   value="{{ old('nome_fantasia', $empresa->nome_fantasia) }}" 
                                   required>
                            @error('nome_fantasia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="marca_id" class="form-label">Marca</label>
                            <select class="form-select @error('marca_id') is-invalid @enderror" id="marca_id" name="marca_id">
                                <option value="">Selecione uma marca</option>
                                @if(isset($marcas) && $marcas->count() > 0)
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ old('marca_id', $empresa->marca_id) == $marca->id ? 'selected' : '' }}>
                                            {{ $marca->nome }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Nenhuma marca disponível</option>
                                @endif
                            </select>
                            @error('marca_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                            <input type="text" 
                                   class="form-control @error('nome_fantasia') is-invalid @enderror" 
                                   id="nome_fantasia" 
                                   name="nome_fantasia" 
                                   value="{{ old('nome_fantasia', $empresa->nome_fantasia) }}">
                            @error('nome_fantasia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input type="text" 
                                   class="form-control @error('cnpj') is-invalid @enderror" 
                                   id="cnpj" 
                                   name="cnpj" 
                                   value="{{ old('cnpj', $empresa->cnpj) }}"
                                   placeholder="00.000.000/0001-00">
                            @error('cnpj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Amigável <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">{{ url('/') }}/loja/</span>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $empresa->slug) }}" 
                                   required>
                        </div>
                        <div class="form-text">URL única para sua loja online. Use apenas letras, números e hífens.</div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Endereço
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="endereco_cep" class="form-label">CEP</label>
                            <input type="text" 
                                   class="form-control @error('endereco_cep') is-invalid @enderror" 
                                   id="endereco_cep" 
                                   name="endereco_cep" 
                                   value="{{ old('endereco_cep', $empresa->endereco_cep) }}"
                                   placeholder="00000-000">
                            @error('endereco_cep')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endereco_logradouro" class="form-label">Logradouro</label>
                            <input type="text" 
                                   class="form-control @error('endereco_logradouro') is-invalid @enderror" 
                                   id="endereco_logradouro" 
                                   name="endereco_logradouro" 
                                   value="{{ old('endereco_logradouro', $empresa->endereco_logradouro) }}"
                                   placeholder="Rua, Avenida, etc.">
                            @error('endereco_logradouro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="endereco_numero" class="form-label">Número</label>
                            <input type="text" 
                                   class="form-control @error('endereco_numero') is-invalid @enderror" 
                                   id="endereco_numero" 
                                   name="endereco_numero" 
                                   value="{{ old('endereco_numero', $empresa->endereco_numero) }}"
                                   placeholder="123">
                            @error('endereco_numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="endereco_complemento" class="form-label">Complemento</label>
                            <input type="text" 
                                   class="form-control @error('endereco_complemento') is-invalid @enderror" 
                                   id="endereco_complemento" 
                                   name="endereco_complemento" 
                                   value="{{ old('endereco_complemento', $empresa->endereco_complemento) }}"
                                   placeholder="Apartamento, Sala, etc.">
                            @error('endereco_complemento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endereco_bairro" class="form-label">Bairro</label>
                            <input type="text" 
                                   class="form-control @error('endereco_bairro') is-invalid @enderror" 
                                   id="endereco_bairro" 
                                   name="endereco_bairro" 
                                   value="{{ old('endereco_bairro', $empresa->endereco_bairro) }}">
                            @error('endereco_bairro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="endereco_cidade" class="form-label">Cidade</label>
                            <input type="text" 
                                   class="form-control @error('endereco_cidade') is-invalid @enderror" 
                                   id="endereco_cidade" 
                                   name="endereco_cidade" 
                                   value="{{ old('endereco_cidade', $empresa->endereco_cidade) }}">
                            @error('endereco_cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endereco_estado" class="form-label">Estado</label>
                            <select class="form-select @error('endereco_estado') is-invalid @enderror" id="endereco_estado" name="endereco_estado">
                                <option value="">Selecione o estado</option>
                                <option value="AC" {{ old('endereco_estado', $empresa->endereco_estado) == 'AC' ? 'selected' : '' }}>Acre</option>
                                <option value="AL" {{ old('endereco_estado', $empresa->endereco_estado) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                <option value="AP" {{ old('endereco_estado', $empresa->endereco_estado) == 'AP' ? 'selected' : '' }}>Amapá</option>
                                <option value="AM" {{ old('endereco_estado', $empresa->endereco_estado) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                <option value="BA" {{ old('endereco_estado', $empresa->endereco_estado) == 'BA' ? 'selected' : '' }}>Bahia</option>
                                <option value="CE" {{ old('endereco_estado', $empresa->endereco_estado) == 'CE' ? 'selected' : '' }}>Ceará</option>
                                <option value="DF" {{ old('endereco_estado', $empresa->endereco_estado) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                <option value="ES" {{ old('endereco_estado', $empresa->endereco_estado) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                <option value="GO" {{ old('endereco_estado', $empresa->endereco_estado) == 'GO' ? 'selected' : '' }}>Goiás</option>
                                <option value="MA" {{ old('endereco_estado', $empresa->endereco_estado) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                <option value="MT" {{ old('endereco_estado', $empresa->endereco_estado) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                <option value="MS" {{ old('endereco_estado', $empresa->endereco_estado) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                <option value="MG" {{ old('endereco_estado', $empresa->endereco_estado) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                <option value="PA" {{ old('endereco_estado', $empresa->endereco_estado) == 'PA' ? 'selected' : '' }}>Pará</option>
                                <option value="PB" {{ old('endereco_estado', $empresa->endereco_estado) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                <option value="PR" {{ old('endereco_estado', $empresa->endereco_estado) == 'PR' ? 'selected' : '' }}>Paraná</option>
                                <option value="PE" {{ old('endereco_estado', $empresa->endereco_estado) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                <option value="PI" {{ old('endereco_estado', $empresa->endereco_estado) == 'PI' ? 'selected' : '' }}>Piauí</option>
                                <option value="RJ" {{ old('endereco_estado', $empresa->endereco_estado) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                <option value="RN" {{ old('endereco_estado', $empresa->endereco_estado) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                <option value="RS" {{ old('endereco_estado', $empresa->endereco_estado) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                <option value="RO" {{ old('endereco_estado', $empresa->endereco_estado) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                <option value="RR" {{ old('endereco_estado', $empresa->endereco_estado) == 'RR' ? 'selected' : '' }}>Roraima</option>
                                <option value="SC" {{ old('endereco_estado', $empresa->endereco_estado) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                <option value="SP" {{ old('endereco_estado', $empresa->endereco_estado) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                <option value="SE" {{ old('endereco_estado', $empresa->endereco_estado) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                <option value="TO" {{ old('endereco_estado', $empresa->endereco_estado) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                            </select>
                            @error('endereco_estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contato -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-phone text-primary me-2"></i>
                        Contato
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" 
                                   class="form-control @error('telefone') is-invalid @enderror" 
                                   id="telefone" 
                                   name="telefone" 
                                   value="{{ old('telefone', $empresa->telefone) }}"
                                   placeholder="(00) 0000-0000">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $empresa->email) }}"
                                   placeholder="contato@empresa.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" 
                                   class="form-control @error('website') is-invalid @enderror" 
                                   id="website" 
                                   name="website" 
                                   value="{{ old('website', $empresa->website) }}"
                                   placeholder="https://www.empresa.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status e Configurações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Status e Configurações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="ativa" {{ old('status', $empresa->status) == 'ativa' ? 'selected' : '' }}>Ativa</option>
                            <option value="inativa" {{ old('status', $empresa->status) == 'inativa' ? 'selected' : '' }}>Inativa</option>
                            <option value="suspensa" {{ old('status', $empresa->status) == 'suspensa' ? 'selected' : '' }}>Suspensa</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-muted small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Criada em:</span>
                            <span>{{ $empresa->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Atualizada em:</span>
                            <span>{{ $empresa->updated_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horário de Funcionamento -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Horário de Funcionamento
                    </h6>
                    <a href="{{ route('comerciantes.horarios.index', $empresa->id) }}" 
                       class="btn btn-sm btn-outline-primary"
                       title="Gerenciar horários completos">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0" style="background-color: #e7f3ff;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <div>
                                <small class="text-primary fw-medium">Sistema Avançado de Horários</small>
                                <div class="small text-muted mt-1">
                                    Configure horários por sistema (PDV, Online, Financeiro) e exceções especiais através do módulo especializado.
                                </div>
                                <a href="{{ route('comerciantes.horarios.index', $empresa->id) }}" 
                                   class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    Gerenciar Horários Completos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-1"></i>
                        Salvar Alterações
                    </button>
                    <a href="{{ route('comerciantes.empresas.show', $empresa) }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
.form-label {
    font-weight: 500;
    color: #374151;
}

.card-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.card-title {
    font-size: 0.95rem;
    color: #374151;
}

.text-danger {
    color: #dc2626 !important;
}

.input-group-text {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #6b7280;
    font-size: 0.875rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
// Validação do formulário
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Auto-gerar slug a partir do nome
document.getElementById('nome').addEventListener('input', function() {
    const nome = this.value;
    const slug = nome
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    
    document.getElementById('slug').value = slug;
});

// Máscara para CNPJ
document.getElementById('cnpj').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 14) {
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        this.value = value;
    }
});

// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        this.value = value;
    }
});

// Máscara para CEP
document.getElementById('endereco_cep').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 8) {
        value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        this.value = value;
    }
});

// Buscar endereço pelo CEP
document.getElementById('endereco_cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('endereco_logradouro').value = data.logradouro;
                    document.getElementById('endereco_bairro').value = data.bairro;
                    document.getElementById('endereco_cidade').value = data.localidade;
                    document.getElementById('endereco_estado').value = data.uf;
                    document.getElementById('endereco_numero').focus();
                }
            })
            .catch(error => console.log('Erro ao buscar CEP:', error));
    }
});
</script>
@endpush

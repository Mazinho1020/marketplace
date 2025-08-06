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
                            <label for="razao_social" class="form-label">Razão Social <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('razao_social') is-invalid @enderror" 
                                   id="razao_social" 
                                   name="razao_social" 
                                   value="{{ old('razao_social', $empresa->razao_social) }}" 
                                   required>
                            @error('razao_social')
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
                        
                        <div class="col-md-6 mb-3">
                            <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                            <input type="text" 
                                   class="form-control @error('inscricao_estadual') is-invalid @enderror" 
                                   id="inscricao_estadual" 
                                   name="inscricao_estadual" 
                                   value="{{ old('inscricao_estadual', $empresa->inscricao_estadual) }}"
                                   placeholder="000.000.000.000">
                            @error('inscricao_estadual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" 
                                   class="form-control @error('cep') is-invalid @enderror" 
                                   id="cep" 
                                   name="cep" 
                                   value="{{ old('cep', $empresa->cep) }}"
                                   placeholder="00000-000">
                            @error('cep')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="logradouro" class="form-label">Logradouro</label>
                            <input type="text" 
                                   class="form-control @error('logradouro') is-invalid @enderror" 
                                   id="logradouro" 
                                   name="logradouro" 
                                   value="{{ old('logradouro', $empresa->logradouro) }}"
                                   placeholder="Rua, Avenida, etc.">
                            @error('logradouro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" 
                                   class="form-control @error('numero') is-invalid @enderror" 
                                   id="numero" 
                                   name="numero" 
                                   value="{{ old('numero', $empresa->numero) }}"
                                   placeholder="123">
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" 
                                   class="form-control @error('complemento') is-invalid @enderror" 
                                   id="complemento" 
                                   name="complemento" 
                                   value="{{ old('complemento', $empresa->complemento) }}"
                                   placeholder="Apartamento, Sala, etc.">
                            @error('complemento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" 
                                   class="form-control @error('bairro') is-invalid @enderror" 
                                   id="bairro" 
                                   name="bairro" 
                                   value="{{ old('bairro', $empresa->bairro) }}">
                            @error('bairro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" 
                                   class="form-control @error('cidade') is-invalid @enderror" 
                                   id="cidade" 
                                   name="cidade" 
                                   value="{{ old('cidade', $empresa->cidade) }}">
                            @error('cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="uf" class="form-label">Estado</label>
                            <select class="form-select @error('uf') is-invalid @enderror" id="uf" name="uf">
                                <option value="">Selecione o estado</option>
                                <option value="AC" {{ old('uf', $empresa->uf) == 'AC' ? 'selected' : '' }}>Acre</option>
                                <option value="AL" {{ old('uf', $empresa->uf) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                <option value="AP" {{ old('uf', $empresa->uf) == 'AP' ? 'selected' : '' }}>Amapá</option>
                                <option value="AM" {{ old('uf', $empresa->uf) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                <option value="BA" {{ old('uf', $empresa->uf) == 'BA' ? 'selected' : '' }}>Bahia</option>
                                <option value="CE" {{ old('uf', $empresa->uf) == 'CE' ? 'selected' : '' }}>Ceará</option>
                                <option value="DF" {{ old('uf', $empresa->uf) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                <option value="ES" {{ old('uf', $empresa->uf) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                <option value="GO" {{ old('uf', $empresa->uf) == 'GO' ? 'selected' : '' }}>Goiás</option>
                                <option value="MA" {{ old('uf', $empresa->uf) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                <option value="MT" {{ old('uf', $empresa->uf) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                <option value="MS" {{ old('uf', $empresa->uf) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                <option value="MG" {{ old('uf', $empresa->uf) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                <option value="PA" {{ old('uf', $empresa->uf) == 'PA' ? 'selected' : '' }}>Pará</option>
                                <option value="PB" {{ old('uf', $empresa->uf) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                <option value="PR" {{ old('uf', $empresa->uf) == 'PR' ? 'selected' : '' }}>Paraná</option>
                                <option value="PE" {{ old('uf', $empresa->uf) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                <option value="PI" {{ old('uf', $empresa->uf) == 'PI' ? 'selected' : '' }}>Piauí</option>
                                <option value="RJ" {{ old('uf', $empresa->uf) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                <option value="RN" {{ old('uf', $empresa->uf) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                <option value="RS" {{ old('uf', $empresa->uf) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                <option value="RO" {{ old('uf', $empresa->uf) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                <option value="RR" {{ old('uf', $empresa->uf) == 'RR' ? 'selected' : '' }}>Roraima</option>
                                <option value="SC" {{ old('uf', $empresa->uf) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                <option value="SP" {{ old('uf', $empresa->uf) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                <option value="SE" {{ old('uf', $empresa->uf) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                <option value="TO" {{ old('uf', $empresa->uf) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                            </select>
                            @error('uf')
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
                            <label for="celular" class="form-label">Celular</label>
                            <input type="text" 
                                   class="form-control @error('celular') is-invalid @enderror" 
                                   id="celular" 
                                   name="celular" 
                                   value="{{ old('celular', $empresa->celular) }}"
                                   placeholder="(00) 90000-0000">
                            @error('celular')
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
                            <label for="site" class="form-label">Website</label>
                            <input type="url" 
                                   class="form-control @error('site') is-invalid @enderror" 
                                   id="site" 
                                   name="site" 
                                   value="{{ old('site', $empresa->site) }}"
                                   placeholder="https://www.empresa.com">
                            @error('site')
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
                            <option value="ativo" {{ old('status', $empresa->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ old('status', $empresa->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                            <option value="suspenso" {{ old('status', $empresa->status) == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                            <option value="bloqueado" {{ old('status', $empresa->status) == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
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
            value = value.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3');
        } else {
            value = value.replace(/^(\d{2})(\d{5})(\d)/, '($1) $2-$3');
        }
        this.value = value;
    }
});

// Máscara para celular
document.getElementById('celular').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3');
        } else {
            value = value.replace(/^(\d{2})(\d{5})(\d)/, '($1) $2-$3');
        }
        this.value = value;
    }
});
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
document.getElementById('cep').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 8) {
        value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        this.value = value;
    }
});

// Buscar endereço pelo CEP
document.getElementById('cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('logradouro').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('uf').value = data.uf;
                    document.getElementById('numero').focus();
                }
            })
            .catch(error => console.log('Erro ao buscar CEP:', error));
    }
});
</script>
@endpush

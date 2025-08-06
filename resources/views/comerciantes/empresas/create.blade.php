@extends('comerciantes.layouts.app')

@section('title', 'Nova Empresa')

@section('content')
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>
                Nova Empresa
            </h1>
            <p class="text-muted mb-0">Cadastre uma nova unidade de negócio</p>
        </div>
        <a href="{{ route('comerciantes.empresas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <form method="POST" action="{{ route('comerciantes.empresas.store') }}" class="needs-validation" novalidate>
        @csrf
        
        <div class="row">
            <!-- Coluna principal -->
            <div class="col-lg-8">
                <!-- Informações básicas -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações Básicas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome_fantasia" class="form-label">Nome da Empresa *</label>
                                    <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror" 
                                           id="nome_fantasia" name="nome_fantasia" value="{{ old('nome_fantasia') }}" 
                                           placeholder="Ex: Pizzaria Tradição Concórdia" required>
                                    @error('nome_fantasia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="marca_id" class="form-label">Marca</label>
                                    <select class="form-select @error('marca_id') is-invalid @enderror" 
                                            id="marca_id" name="marca_id">
                                        <option value="">Selecione uma marca</option>
                                        @if(isset($marcas) && $marcas->count() > 0)
                                            @foreach($marcas as $marca)
                                                <option value="{{ $marca->id }}" 
                                                        {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
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
                                    <div class="form-text">
                                        <a href="{{ route('comerciantes.marcas.create') }}" target="_blank">
                                            <i class="fas fa-plus me-1"></i>Criar nova marca
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                                    <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror" 
                                           id="nome_fantasia" name="nome_fantasia" value="{{ old('nome_fantasia') }}" 
                                           placeholder="Se diferente do nome principal">
                                    @error('nome_fantasia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                                           id="cnpj" name="cnpj" value="{{ old('cnpj') }}" 
                                           placeholder="00.000.000/0000-00" data-mask="00.000.000/0000-00">
                                    @error('cnpj')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Endereço
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="endereco_cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control @error('endereco_cep') is-invalid @enderror" 
                                           id="endereco_cep" name="endereco_cep" value="{{ old('endereco_cep') }}" 
                                           placeholder="00000-000" data-mask="00000-000">
                                    @error('endereco_cep')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="endereco_logradouro" class="form-label">Logradouro</label>
                                    <input type="text" class="form-control @error('endereco_logradouro') is-invalid @enderror" 
                                           id="endereco_logradouro" name="endereco_logradouro" value="{{ old('endereco_logradouro') }}" 
                                           placeholder="Rua, Avenida, etc.">
                                    @error('endereco_logradouro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="endereco_numero" class="form-label">Número</label>
                                    <input type="text" class="form-control @error('endereco_numero') is-invalid @enderror" 
                                           id="endereco_numero" name="endereco_numero" value="{{ old('endereco_numero') }}" 
                                           placeholder="123">
                                    @error('endereco_numero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="endereco_complemento" class="form-label">Complemento</label>
                                    <input type="text" class="form-control @error('endereco_complemento') is-invalid @enderror" 
                                           id="endereco_complemento" name="endereco_complemento" value="{{ old('endereco_complemento') }}" 
                                           placeholder="Apto, Sala, etc.">
                                    @error('endereco_complemento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="endereco_bairro" class="form-label">Bairro</label>
                                    <input type="text" class="form-control @error('endereco_bairro') is-invalid @enderror" 
                                           id="endereco_bairro" name="endereco_bairro" value="{{ old('endereco_bairro') }}" 
                                           placeholder="Nome do bairro">
                                    @error('endereco_bairro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="endereco_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control @error('endereco_cidade') is-invalid @enderror" 
                                           id="endereco_cidade" name="endereco_cidade" value="{{ old('endereco_cidade') }}" 
                                           placeholder="Nome da cidade">
                                    @error('endereco_cidade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="mb-3">
                                    <label for="endereco_estado" class="form-label">UF</label>
                                    <select class="form-select @error('endereco_estado') is-invalid @enderror" 
                                            id="endereco_estado" name="endereco_estado">
                                        <option value="">UF</option>
                                        <option value="AC" {{ old('endereco_estado') == 'AC' ? 'selected' : '' }}>AC</option>
                                        <option value="AL" {{ old('endereco_estado') == 'AL' ? 'selected' : '' }}>AL</option>
                                        <option value="AP" {{ old('endereco_estado') == 'AP' ? 'selected' : '' }}>AP</option>
                                        <option value="AM" {{ old('endereco_estado') == 'AM' ? 'selected' : '' }}>AM</option>
                                        <option value="BA" {{ old('endereco_estado') == 'BA' ? 'selected' : '' }}>BA</option>
                                        <option value="CE" {{ old('endereco_estado') == 'CE' ? 'selected' : '' }}>CE</option>
                                        <option value="DF" {{ old('endereco_estado') == 'DF' ? 'selected' : '' }}>DF</option>
                                        <option value="ES" {{ old('endereco_estado') == 'ES' ? 'selected' : '' }}>ES</option>
                                        <option value="GO" {{ old('endereco_estado') == 'GO' ? 'selected' : '' }}>GO</option>
                                        <option value="MA" {{ old('endereco_estado') == 'MA' ? 'selected' : '' }}>MA</option>
                                        <option value="MT" {{ old('endereco_estado') == 'MT' ? 'selected' : '' }}>MT</option>
                                        <option value="MS" {{ old('endereco_estado') == 'MS' ? 'selected' : '' }}>MS</option>
                                        <option value="MG" {{ old('endereco_estado') == 'MG' ? 'selected' : '' }}>MG</option>
                                        <option value="PA" {{ old('endereco_estado') == 'PA' ? 'selected' : '' }}>PA</option>
                                        <option value="PB" {{ old('endereco_estado') == 'PB' ? 'selected' : '' }}>PB</option>
                                        <option value="PR" {{ old('endereco_estado') == 'PR' ? 'selected' : '' }}>PR</option>
                                        <option value="PE" {{ old('endereco_estado') == 'PE' ? 'selected' : '' }}>PE</option>
                                        <option value="PI" {{ old('endereco_estado') == 'PI' ? 'selected' : '' }}>PI</option>
                                        <option value="RJ" {{ old('endereco_estado') == 'RJ' ? 'selected' : '' }}>RJ</option>
                                        <option value="RN" {{ old('endereco_estado') == 'RN' ? 'selected' : '' }}>RN</option>
                                        <option value="RS" {{ old('endereco_estado') == 'RS' ? 'selected' : '' }}>RS</option>
                                        <option value="RO" {{ old('endereco_estado') == 'RO' ? 'selected' : '' }}>RO</option>
                                        <option value="RR" {{ old('endereco_estado') == 'RR' ? 'selected' : '' }}>RR</option>
                                        <option value="SC" {{ old('endereco_estado') == 'SC' ? 'selected' : '' }}>SC</option>
                                        <option value="SP" {{ old('endereco_estado') == 'SP' ? 'selected' : '' }}>SP</option>
                                        <option value="SE" {{ old('endereco_estado') == 'SE' ? 'selected' : '' }}>SE</option>
                                        <option value="TO" {{ old('endereco_estado') == 'TO' ? 'selected' : '' }}>TO</option>
                                    </select>
                                    @error('endereco_estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contato -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-phone me-2"></i>
                            Informações de Contato
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                           id="telefone" name="telefone" value="{{ old('telefone') }}" 
                                           placeholder="(00) 0000-0000" data-mask="(00) 0000-0000">
                                    @error('telefone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           placeholder="contato@empresa.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website') }}" 
                                           placeholder="https://www.empresa.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-toggle-on me-2"></i>
                            Status e Configurações
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status da Empresa</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="ativa" {{ old('status', 'ativa') == 'ativa' ? 'selected' : '' }}>
                                    Ativa
                                </option>
                                <option value="inativa" {{ old('status') == 'inativa' ? 'selected' : '' }}>
                                    Inativa
                                </option>
                                <option value="suspensa" {{ old('status') == 'suspensa' ? 'selected' : '' }}>
                                    Suspensa
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Horário de funcionamento -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-2"></i>
                            Horário de Funcionamento
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-text mb-3">
                            Configure os horários de funcionamento da empresa. Deixe em branco os dias que não funciona.
                        </div>
                        
                        @php
                            $diasSemana = [
                                'segunda' => 'Segunda-feira',
                                'terca' => 'Terça-feira',
                                'quarta' => 'Quarta-feira',
                                'quinta' => 'Quinta-feira',
                                'sexta' => 'Sexta-feira',
                                'sabado' => 'Sábado',
                                'domingo' => 'Domingo'
                            ];
                        @endphp

                        @foreach($diasSemana as $dia => $nome)
                            <div class="mb-2">
                                <label class="form-label small">{{ $nome }}</label>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="time" class="form-control form-control-sm" 
                                               name="horario[{{ $dia }}][abertura]" 
                                               value="{{ old("horario.{$dia}.abertura") }}" 
                                               placeholder="Abertura">
                                    </div>
                                    <div class="col">
                                        <input type="time" class="form-control form-control-sm" 
                                               name="horario[{{ $dia }}][fechamento]" 
                                               value="{{ old("horario.{$dia}.fechamento") }}" 
                                               placeholder="Fechamento">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Ações -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Salvar Empresa
                            </button>
                            <a href="{{ route('comerciantes.empresas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.form-control:focus, .form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

.card-header {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    border-bottom: 1px solid rgba(var(--bs-primary-rgb), 0.2);
}

.form-text a {
    color: var(--bs-primary);
    text-decoration: none;
}

.form-text a:hover {
    text-decoration: underline;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscaras de input
    $('#cnpj').mask('00.000.000/0000-00');
    $('#endereco_cep').mask('00000-000');
    $('#telefone').mask('(00) 0000-0000');

    // Busca CEP automática
    $('#endereco_cep').on('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('endereco_logradouro').value = data.logradouro || '';
                        document.getElementById('endereco_bairro').value = data.bairro || '';
                        document.getElementById('endereco_cidade').value = data.localidade || '';
                        document.getElementById('endereco_estado').value = data.uf || '';
                        
                        // Foca no campo número
                        document.getElementById('endereco_numero').focus();
                    }
                })
                .catch(error => console.log('Erro ao buscar CEP:', error));
        }
    });

    // Geração automática de slug
    $('#nome').on('input', function() {
        // Implementar se necessário
    });

    // Validação do formulário
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
@endpush
@endsection

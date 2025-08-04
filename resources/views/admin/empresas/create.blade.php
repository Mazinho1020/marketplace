@extends('layouts.admin')

@section('title', 'Nova Empresa')

@php
    $pageTitle = 'Nova Empresa';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Empresas', 'url' => route('admin.empresas.index')],
        ['title' => 'Nova Empresa', 'url' => '#']
    ];
@endphp

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Cadastrar Nova Empresa
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.empresas.store') }}">
                        @csrf
                        
                        <!-- Informações Básicas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informações Básicas
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nome_fantasia" class="form-label">Nome Fantasia <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror" 
                                       id="nome_fantasia" name="nome_fantasia" value="{{ old('nome_fantasia') }}" required>
                                @error('nome_fantasia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="razao_social" class="form-label">Razão Social <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('razao_social') is-invalid @enderror" 
                                       id="razao_social" name="razao_social" value="{{ old('razao_social') }}" required>
                                @error('razao_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cnpj" class="form-label">CNPJ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                                       id="cnpj" name="cnpj" value="{{ old('cnpj') }}" 
                                       placeholder="00.000.000/0000-00" maxlength="14" required>
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                       id="telefone" name="telefone" value="{{ old('telefone') }}" 
                                       placeholder="(00) 00000-0000">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Endereço
                                </h6>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                                       id="endereco" name="endereco" value="{{ old('endereco') }}" 
                                       placeholder="Rua, número, complemento">
                                @error('endereco')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                                       id="cep" name="cep" value="{{ old('cep') }}" 
                                       placeholder="00000-000" maxlength="8">
                                @error('cep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
                                       id="cidade" name="cidade" value="{{ old('cidade') }}">
                                @error('cidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                                    <option value="">Selecione...</option>
                                    <option value="AC" {{ old('estado') == 'AC' ? 'selected' : '' }}>Acre</option>
                                    <option value="AL" {{ old('estado') == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                    <option value="AP" {{ old('estado') == 'AP' ? 'selected' : '' }}>Amapá</option>
                                    <option value="AM" {{ old('estado') == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                    <option value="BA" {{ old('estado') == 'BA' ? 'selected' : '' }}>Bahia</option>
                                    <option value="CE" {{ old('estado') == 'CE' ? 'selected' : '' }}>Ceará</option>
                                    <option value="DF" {{ old('estado') == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                    <option value="ES" {{ old('estado') == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                    <option value="GO" {{ old('estado') == 'GO' ? 'selected' : '' }}>Goiás</option>
                                    <option value="MA" {{ old('estado') == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                    <option value="MT" {{ old('estado') == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                    <option value="MS" {{ old('estado') == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                    <option value="MG" {{ old('estado') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                    <option value="PA" {{ old('estado') == 'PA' ? 'selected' : '' }}>Pará</option>
                                    <option value="PB" {{ old('estado') == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                    <option value="PR" {{ old('estado') == 'PR' ? 'selected' : '' }}>Paraná</option>
                                    <option value="PE" {{ old('estado') == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                    <option value="PI" {{ old('estado') == 'PI' ? 'selected' : '' }}>Piauí</option>
                                    <option value="RJ" {{ old('estado') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                    <option value="RN" {{ old('estado') == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                    <option value="RS" {{ old('estado') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                    <option value="RO" {{ old('estado') == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                    <option value="RR" {{ old('estado') == 'RR' ? 'selected' : '' }}>Roraima</option>
                                    <option value="SC" {{ old('estado') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                    <option value="SP" {{ old('estado') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                    <option value="SE" {{ old('estado') == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                    <option value="TO" {{ old('estado') == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informações Comerciais -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-handshake me-2"></i>Informações Comerciais
                                </h6>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="plano" class="form-label">Plano <span class="text-danger">*</span></label>
                                <select class="form-select @error('plano') is-invalid @enderror" id="plano" name="plano" required>
                                    <option value="">Selecione...</option>
                                    <option value="basico" {{ old('plano') == 'basico' ? 'selected' : '' }}>Básico</option>
                                    <option value="pro" {{ old('plano') == 'pro' ? 'selected' : '' }}>Pro</option>
                                    <option value="premium" {{ old('plano') == 'premium' ? 'selected' : '' }}>Premium</option>
                                    <option value="enterprise" {{ old('plano') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                </select>
                                @error('plano')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                    <option value="suspenso" {{ old('status') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                                    <option value="bloqueado" {{ old('status') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="valor_mensalidade" class="form-label">Valor Mensalidade</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('valor_mensalidade') is-invalid @enderror" 
                                           id="valor_mensalidade" name="valor_mensalidade" value="{{ old('valor_mensalidade') }}" 
                                           step="0.01" min="0" placeholder="0,00">
                                </div>
                                @error('valor_mensalidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="data_vencimento" class="form-label">Data Vencimento</label>
                                <input type="date" class="form-control @error('data_vencimento') is-invalid @enderror" 
                                       id="data_vencimento" name="data_vencimento" value="{{ old('data_vencimento') }}">
                                @error('data_vencimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                          id="observacoes" name="observacoes" rows="3" 
                                          placeholder="Observações adicionais sobre a empresa...">{{ old('observacoes') }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.empresas.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Voltar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Salvar Empresa
                                    </button>
                                </div>
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
    // Máscara para CNPJ
    const cnpjInput = document.getElementById('cnpj');
    cnpjInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 14) {
            e.target.value = value;
        }
    });

    // Máscara para CEP
    const cepInput = document.getElementById('cep');
    cepInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 8) {
            e.target.value = value;
        }
    });

    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    telefoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            e.target.value = value;
        }
    });
});
</script>
@endpush

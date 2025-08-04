@extends('layouts.admin')

@section('title', 'Editar Empresa')

@php
    $pageTitle = 'Editar Empresa';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Empresas', 'url' => route('admin.empresas.index')],
        ['title' => $empresa->nome_fantasia, 'url' => route('admin.empresas.show', $empresa->id)],
        ['title' => 'Editar', 'url' => '#']
    ];
@endphp

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Empresa: {{ $empresa->nome_fantasia }}
                    </h5>
                    <span class="badge bg-{{ $empresa->getStatusBadgeClass() }}">{{ ucfirst($empresa->status) }}</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.empresas.update', $empresa->id) }}">
                        @csrf
                        @method('PUT')
                        
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
                                       id="nome_fantasia" name="nome_fantasia" value="{{ old('nome_fantasia', $empresa->nome_fantasia) }}" required>
                                @error('nome_fantasia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="razao_social" class="form-label">Razão Social <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('razao_social') is-invalid @enderror" 
                                       id="razao_social" name="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}" required>
                                @error('razao_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cnpj" class="form-label">CNPJ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                                       id="cnpj" name="cnpj" value="{{ old('cnpj', $empresa->cnpj) }}" 
                                       placeholder="00.000.000/0000-00" maxlength="14" required>
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $empresa->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                       id="telefone" name="telefone" value="{{ old('telefone', $empresa->telefone) }}" 
                                       placeholder="(00) 00000-0000">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cadastrado em</label>
                                <input type="text" class="form-control" value="{{ $empresa->created_at->format('d/m/Y H:i') }}" readonly>
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
                                       id="endereco" name="endereco" value="{{ old('endereco', $empresa->endereco) }}" 
                                       placeholder="Rua, número, complemento">
                                @error('endereco')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                                       id="cep" name="cep" value="{{ old('cep', $empresa->cep) }}" 
                                       placeholder="00000-000" maxlength="8">
                                @error('cep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
                                       id="cidade" name="cidade" value="{{ old('cidade', $empresa->cidade) }}">
                                @error('cidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                                    <option value="">Selecione...</option>
                                    <option value="AC" {{ old('estado', $empresa->estado) == 'AC' ? 'selected' : '' }}>Acre</option>
                                    <option value="AL" {{ old('estado', $empresa->estado) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                    <option value="AP" {{ old('estado', $empresa->estado) == 'AP' ? 'selected' : '' }}>Amapá</option>
                                    <option value="AM" {{ old('estado', $empresa->estado) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                    <option value="BA" {{ old('estado', $empresa->estado) == 'BA' ? 'selected' : '' }}>Bahia</option>
                                    <option value="CE" {{ old('estado', $empresa->estado) == 'CE' ? 'selected' : '' }}>Ceará</option>
                                    <option value="DF" {{ old('estado', $empresa->estado) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                    <option value="ES" {{ old('estado', $empresa->estado) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                    <option value="GO" {{ old('estado', $empresa->estado) == 'GO' ? 'selected' : '' }}>Goiás</option>
                                    <option value="MA" {{ old('estado', $empresa->estado) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                    <option value="MT" {{ old('estado', $empresa->estado) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                    <option value="MS" {{ old('estado', $empresa->estado) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                    <option value="MG" {{ old('estado', $empresa->estado) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                    <option value="PA" {{ old('estado', $empresa->estado) == 'PA' ? 'selected' : '' }}>Pará</option>
                                    <option value="PB" {{ old('estado', $empresa->estado) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                    <option value="PR" {{ old('estado', $empresa->estado) == 'PR' ? 'selected' : '' }}>Paraná</option>
                                    <option value="PE" {{ old('estado', $empresa->estado) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                    <option value="PI" {{ old('estado', $empresa->estado) == 'PI' ? 'selected' : '' }}>Piauí</option>
                                    <option value="RJ" {{ old('estado', $empresa->estado) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                    <option value="RN" {{ old('estado', $empresa->estado) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                    <option value="RS" {{ old('estado', $empresa->estado) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                    <option value="RO" {{ old('estado', $empresa->estado) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                    <option value="RR" {{ old('estado', $empresa->estado) == 'RR' ? 'selected' : '' }}>Roraima</option>
                                    <option value="SC" {{ old('estado', $empresa->estado) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                    <option value="SP" {{ old('estado', $empresa->estado) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                    <option value="SE" {{ old('estado', $empresa->estado) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                    <option value="TO" {{ old('estado', $empresa->estado) == 'TO' ? 'selected' : '' }}>Tocantins</option>
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
                                    <option value="basico" {{ old('plano', $empresa->plano) == 'basico' ? 'selected' : '' }}>Básico</option>
                                    <option value="pro" {{ old('plano', $empresa->plano) == 'pro' ? 'selected' : '' }}>Pro</option>
                                    <option value="premium" {{ old('plano', $empresa->plano) == 'premium' ? 'selected' : '' }}>Premium</option>
                                    <option value="enterprise" {{ old('plano', $empresa->plano) == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                </select>
                                @error('plano')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="ativo" {{ old('status', $empresa->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ old('status', $empresa->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                    <option value="suspenso" {{ old('status', $empresa->status) == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                                    <option value="bloqueado" {{ old('status', $empresa->status) == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
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
                                           id="valor_mensalidade" name="valor_mensalidade" value="{{ old('valor_mensalidade', $empresa->valor_mensalidade) }}" 
                                           step="0.01" min="0" placeholder="0,00">
                                </div>
                                @error('valor_mensalidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="data_vencimento" class="form-label">Data Vencimento</label>
                                <input type="date" class="form-control @error('data_vencimento') is-invalid @enderror" 
                                       id="data_vencimento" name="data_vencimento" 
                                       value="{{ old('data_vencimento', $empresa->data_vencimento ? $empresa->data_vencimento->format('Y-m-d') : '') }}">
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
                                          placeholder="Observações adicionais sobre a empresa...">{{ old('observacoes', $empresa->observacoes) }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Alertas para Status -->
                        @if($empresa->status === 'suspenso' || $empresa->status === 'bloqueado')
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atenção:</strong> Esta empresa está com status <strong>{{ ucfirst($empresa->status) }}</strong>.
                                Verifique os motivos antes de reativar.
                            </div>
                        @endif

                        @if($empresa->isVencido())
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Vencimento:</strong> A mensalidade desta empresa está vencida desde 
                                {{ $empresa->data_vencimento->format('d/m/Y') }}.
                            </div>
                        @endif

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('admin.empresas.show', $empresa->id) }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-arrow-left me-2"></i>Voltar
                                        </a>
                                        <a href="{{ route('admin.empresas.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-list me-2"></i>Lista
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Atualizar Empresa
                                        </button>
                                    </div>
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

    // Alerta de confirmação para mudanças de status críticas
    const statusSelect = document.getElementById('status');
    statusSelect.addEventListener('change', function(e) {
        if (e.target.value === 'bloqueado' || e.target.value === 'suspenso') {
            if (!confirm('Tem certeza que deseja alterar o status para ' + e.target.value + '? Esta ação pode afetar o acesso da empresa.')) {
                e.target.value = '{{ $empresa->status }}'; // Volta ao valor original
            }
        }
    });
});
</script>
@endpush

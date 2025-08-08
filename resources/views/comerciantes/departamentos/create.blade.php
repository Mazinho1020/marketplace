@extends('comerciantes.layout')

@section('title', 'Novo Departamento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Novo Departamento</h1>
                    <p class="text-muted mb-0">Adicione um novo departamento à empresa</p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/departamentos?empresa_id={{ $empresaId }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="/comerciantes/clientes/departamentos" method="POST" id="formDepartamento">
                                @csrf
                                <input type="hidden" name="empresa_id" value="{{ $empresaId }}">

                                <div class="row">
                                    <!-- Informações Básicas -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="codigo" class="form-label">Código</label>
                                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                                   id="codigo" name="codigo" value="{{ old('codigo') }}" 
                                                   placeholder="Ex: ADM, VEN, RH...">
                                            @error('codigo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Opcional - Código de identificação rápida</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                                   id="nome" name="nome" value="{{ old('nome') }}" 
                                                   placeholder="Nome do departamento" required>
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Obrigatório - Nome completo do departamento</small>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição</label>
                                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                                      id="descricao" name="descricao" rows="3" 
                                                      placeholder="Descrição detalhada do departamento">{{ old('descricao') }}</textarea>
                                            @error('descricao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Configurações -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="responsavel_nome" class="form-label">Nome do Responsável</label>
                                            <input type="text" class="form-control @error('responsavel_nome') is-invalid @enderror" 
                                                   id="responsavel_nome" name="responsavel_nome" value="{{ old('responsavel_nome') }}" 
                                                   placeholder="Nome do responsável">
                                            @error('responsavel_nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="responsavel_email" class="form-label">Email do Responsável</label>
                                            <input type="email" class="form-control @error('responsavel_email') is-invalid @enderror" 
                                                   id="responsavel_email" name="responsavel_email" value="{{ old('responsavel_email') }}" 
                                                   placeholder="email@exemplo.com">
                                            @error('responsavel_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefone" class="form-label">Telefone</label>
                                            <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                                   id="telefone" name="telefone" value="{{ old('telefone') }}" 
                                                   placeholder="(00) 00000-0000">
                                            @error('telefone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="centro_custo" class="form-label">Centro de Custo</label>
                                            <input type="text" class="form-control @error('centro_custo') is-invalid @enderror" 
                                                   id="centro_custo" name="centro_custo" value="{{ old('centro_custo') }}" 
                                                   placeholder="Ex: CC001">
                                            @error('centro_custo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Localização -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="localizacao" class="form-label">Localização</label>
                                            <input type="text" class="form-control @error('localizacao') is-invalid @enderror" 
                                                   id="localizacao" name="localizacao" value="{{ old('localizacao') }}" 
                                                   placeholder="Ex: Andar 2, Sala 205">
                                            @error('localizacao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ordem" class="form-label">Ordem de Exibição</label>
                                            <input type="number" class="form-control @error('ordem') is-invalid @enderror" 
                                                   id="ordem" name="ordem" value="{{ old('ordem', 0) }}" 
                                                   min="0" placeholder="0">
                                            @error('ordem')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="ativo" name="ativo" value="1" 
                                                       {{ old('ativo', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ativo">
                                                    Departamento ativo
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="/comerciantes/clientes/departamentos?empresa_id={{ $empresaId }}" 
                                       class="btn btn-secondary">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btnSalvar">
                                        <i class="fas fa-save"></i> Salvar Departamento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informações
                            </h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <p><strong>Campos obrigatórios:</strong></p>
                                <ul>
                                    <li>Nome do departamento</li>
                                </ul>

                                <p><strong>Dicas:</strong></p>
                                <ul>
                                    <li>Use códigos curtos e descritivos</li>
                                    <li>A descrição ajuda na identificação</li>
                                    <li>Configure responsável para facilitar contato</li>
                                    <li>A ordem define a sequência na listagem</li>
                                </ul>

                                <p><strong>Exemplos de departamentos:</strong></p>
                                <ul>
                                    <li>Administração (ADM)</li>
                                    <li>Recursos Humanos (RH)</li>
                                    <li>Vendas (VEN)</li>
                                    <li>Financeiro (FIN)</li>
                                    <li>Tecnologia (TI)</li>
                                </ul>
                            </small>
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
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
    const form = document.getElementById('formDepartamento');
    const btnSalvar = document.getElementById('btnSalvar');
    
    form.addEventListener('submit', function(e) {
        btnSalvar.disabled = true;
        btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    });

    // Máscara para telefone
    const telefone = document.getElementById('telefone');
    telefone.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 11) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 6) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length >= 2) {
            value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        }
        e.target.value = value;
    });

    // Auto-gerar código baseado no nome
    const nome = document.getElementById('nome');
    const codigo = document.getElementById('codigo');
    
    nome.addEventListener('input', function(e) {
        if (!codigo.value) {
            let nomeValue = e.target.value.toUpperCase();
            let codigoValue = nomeValue.split(' ')
                .map(word => word.charAt(0))
                .join('')
                .substring(0, 5);
            codigo.value = codigoValue;
        }
    });
});
</script>
@endpush

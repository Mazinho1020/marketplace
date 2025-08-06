@extends('layouts.comerciante')

@section('title', 'Editar Usuário')

@php
    $pageTitle = 'Editar Usuário';
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('comerciantes.dashboard')],
        ['title' => 'Empresas', 'url' => route('comerciantes.empresas.index')],
        ['title' => $empresa->nome_fantasia, 'url' => route('comerciantes.empresas.show', $empresa)],
        ['title' => 'Usuários', 'url' => route('comerciantes.empresas.usuarios.index', $empresa)],
        ['title' => 'Editar ' . $usuarioParaEdicao['nome'], 'url' => '#']
    ];
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Cabeçalho -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-2 text-gray-800">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Usuário: {{ $usuarioParaEdicao['nome'] }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @foreach($breadcrumbs as $breadcrumb)
                                @if($loop->last)
                                    <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.usuarios.index', $empresa) }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Usuários
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Edição -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>
                        Dados do Usuário
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.empresas.usuarios.update', [$empresa, $userVinculado]) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informações Básicas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Informações Básicas
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" value="{{ $usuarioParaEdicao['nome'] }}" readonly>
                                <small class="text-muted">O nome não pode ser alterado</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" value="{{ $usuarioParaEdicao['email'] }}" readonly>
                                <small class="text-muted">O e-mail não pode ser alterado</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" class="form-control" value="{{ $usuarioParaEdicao['telefone'] }}" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo</label>
                                <input type="text" class="form-control" value="{{ $usuarioParaEdicao['cargo'] }}" readonly>
                            </div>
                        </div>

                        <!-- Configurações do Vínculo -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>Configurações do Vínculo
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="perfil" class="form-label">Perfil na Empresa <span class="text-danger">*</span></label>
                                <select class="form-select @error('perfil') is-invalid @enderror" 
                                        id="perfil" name="perfil" required>
                                    <option value="colaborador" {{ $usuarioParaEdicao['perfil'] == 'colaborador' ? 'selected' : '' }}>Colaborador</option>
                                    <option value="gerente" {{ $usuarioParaEdicao['perfil'] == 'gerente' ? 'selected' : '' }}>Gerente</option>
                                    <option value="administrador" {{ $usuarioParaEdicao['perfil'] == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                @error('perfil')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="ativo" {{ $usuarioParaEdicao['status'] == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ $usuarioParaEdicao['status'] == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permissões -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-shield-alt me-2"></i>Permissões
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissoes[]" 
                                                   value="empresas.view" id="perm_empresas_view"
                                                   {{ in_array('empresas.view', $usuarioParaEdicao['permissoes']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_empresas_view">
                                                Ver Empresas
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissoes[]" 
                                                   value="empresas.edit" id="perm_empresas_edit"
                                                   {{ in_array('empresas.edit', $usuarioParaEdicao['permissoes']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_empresas_edit">
                                                Editar Empresas
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissoes[]" 
                                                   value="usuarios.manage" id="perm_usuarios_manage"
                                                   {{ in_array('usuarios.manage', $usuarioParaEdicao['permissoes']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_usuarios_manage">
                                                Gerenciar Usuários
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissoes[]" 
                                                   value="horarios.manage" id="perm_horarios_manage"
                                                   {{ in_array('horarios.manage', $usuarioParaEdicao['permissoes']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_horarios_manage">
                                                Gerenciar Horários
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissoes[]" 
                                                   value="relatorios.view" id="perm_relatorios_view"
                                                   {{ in_array('relatorios.view', $usuarioParaEdicao['permissoes']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_relatorios_view">
                                                Ver Relatórios
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permissoes[]" 
                                                   value="configuracoes.edit" id="perm_config_edit"
                                                   {{ in_array('configuracoes.edit', $usuarioParaEdicao['permissoes']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_config_edit">
                                                Editar Configurações
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('comerciantes.empresas.usuarios.index', $empresa) }}" 
                                       class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Salvar Alterações
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Informações -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informações do Vínculo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary text-white mx-auto mb-2" style="width: 60px; height: 60px; font-size: 24px;">
                            {{ strtoupper(substr($usuarioParaEdicao['nome'], 0, 1)) }}
                        </div>
                        <h6 class="mb-1">{{ $usuarioParaEdicao['nome'] }}</h6>
                        <small class="text-muted">{{ $usuarioParaEdicao['email'] }}</small>
                    </div>
                    
                    <hr>
                    
                    <div class="small">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Perfil:</strong></div>
                            <div class="col-6">
                                <span class="badge bg-{{ $usuarioParaEdicao['perfil'] == 'administrador' ? 'danger' : ($usuarioParaEdicao['perfil'] == 'gerente' ? 'warning' : 'info') }}">
                                    {{ ucfirst($usuarioParaEdicao['perfil']) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6"><strong>Status:</strong></div>
                            <div class="col-6">
                                <span class="badge bg-{{ $usuarioParaEdicao['status'] == 'ativo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($usuarioParaEdicao['status']) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6"><strong>Vinculado em:</strong></div>
                            <div class="col-6">{{ \Carbon\Carbon::parse($usuarioParaEdicao['data_vinculo'])->format('d/m/Y') }}</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-6"><strong>Permissões:</strong></div>
                            <div class="col-6">{{ count($usuarioParaEdicao['permissoes']) }} ativas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Adicionais -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools me-2"></i>Ações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('comerciantes.empresas.usuarios.show', [$empresa, $userVinculado]) }}" 
                           class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye me-2"></i>Ver Detalhes
                        </a>
                        
                        <hr>
                        
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                onclick="confirmarRemocao()">
                            <i class="fas fa-user-minus me-2"></i>Remover da Empresa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
function confirmarRemocao() {
    if (confirm('Tem certeza que deseja remover {{ $usuarioParaEdicao["nome"] }} desta empresa?\n\nEsta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("comerciantes.empresas.usuarios.destroy", [$empresa, $userVinculado]) }}';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfField);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

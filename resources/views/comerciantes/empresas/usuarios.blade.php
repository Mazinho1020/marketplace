@php
    // Função helper para obter o nome do usuário de forma segura
    function getNomeUsuario($usuario) {
        if (!$usuario) return 'Nome não disponível';
        
        // Tentar diferentes campos possíveis baseado na estrutura real da tabela
        $campos = ['username', 'name', 'email', 'nome', 'first_name', 'nome_completo'];
        
        foreach ($campos as $campo) {
            if (isset($usuario->$campo) && !empty($usuario->$campo)) {
                return $usuario->$campo;
            }
        }
        
        return 'Nome não disponível';
    }
    
    // Função helper para obter a inicial do usuário
    function getInicialUsuario($usuario) {
        $nome = getNomeUsuario($usuario);
        return strtoupper(substr($nome, 0, 1));
    }
@endphp

@extends('layouts.comerciante')

@section('title', 'Usuários - ' . ($empresa->nome_fantasia ?: $empresa->razao_social))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Usuários</h1>
        <p class="text-muted mb-0">{{ $empresa->nome_fantasia ?: $empresa->razao_social }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('comerciantes.empresas.show', $empresa) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar à Empresa
        </a>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-plus me-1"></i>
                Gerenciar Usuários
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalVincularUsuario">
                        <i class="fas fa-link me-1 text-info"></i>
                        Vincular Usuário Existente
                        <br><small class="text-muted">Para usuários já cadastrados no sistema</small>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCriarUsuario">
                        <i class="fas fa-plus me-1 text-success"></i>
                        Criar Novo Usuário
                        <br><small class="text-muted">Para criar um usuário completamente novo</small>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Informações da Empresa -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h6 class="card-title mb-3">
                    <i class="fas fa-building text-primary me-2"></i>
                    {{ $empresa->nome_fantasia ?: $empresa->razao_social }}
                </h6>
                <div class="row">
                    <div class="col-sm-6">
                        <small class="text-muted">Marca:</small><br>
                        <span class="fw-medium">{{ $empresa->marca?->nome ?? 'Sem marca' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Proprietário:</small><br>
                        <span class="fw-medium">
                            @if($empresa->proprietario)
                                {{ getNomeUsuario($empresa->proprietario) }}
                            @else
                                Sem proprietário
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="badge bg-{{ $empresa->status === 'ativo' ? 'success' : 'secondary' }} fs-6 px-3 py-2">
                    {{ ucfirst($empresa->status) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Usuários -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            <i class="fas fa-users me-2"></i>
            Usuários Vinculados ({{ $empresa->usuariosVinculados ? $empresa->usuariosVinculados->count() : 0 }})
        </h6>
        <div class="d-flex gap-2 flex-wrap">
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-plus me-1"></i>
                    Adicionar Usuário
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalVincularUsuario">
                            <i class="fas fa-link me-1 text-info"></i>
                            Vincular Existente
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCriarUsuario">
                            <i class="fas fa-plus me-1 text-success"></i>
                            Criar Novo
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- DEBUG: Mostrar informações quando há problemas --}}
        @if(request()->has('debug') || !isset($empresa->usuariosVinculados))
            <div class="alert alert-info m-3">
                <h6>DEBUG INFO:</h6>
                <p><strong>Empresa ID:</strong> {{ $empresa->id ?? 'N/A' }}</p>
                <p><strong>Nome:</strong> {{ $empresa->nome_fantasia ?? 'N/A' }}</p>
                <p><strong>usuariosVinculados definido:</strong> {{ isset($empresa->usuariosVinculados) ? 'SIM' : 'NÃO' }}</p>
                @if(isset($empresa->usuariosVinculados))
                    <p><strong>Tipo:</strong> {{ get_class($empresa->usuariosVinculados) }}</p>
                    <p><strong>Count:</strong> {{ $empresa->usuariosVinculados->count() }}</p>
                @endif
                <p><strong>Query SQL:</strong> {{ $empresa->usuariosVinculados()->toSql() ?? 'N/A' }}</p>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuário</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th>Data Vínculo</th>
                        <th width="120" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($empresa->usuariosVinculados) && is_object($empresa->usuariosVinculados))
                        @forelse($empresa->usuariosVinculados as $vinculo)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        {{ getInicialUsuario($vinculo) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ getNomeUsuario($vinculo) }}</div>
                                        <small class="text-muted">{{ $vinculo->email ?? 'Email não disponível' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(isset($vinculo->pivot) && isset($vinculo->pivot->perfil))
                                    <span class="badge bg-{{ $vinculo->pivot->perfil === 'proprietario' ? 'danger' : ($vinculo->pivot->perfil === 'administrador' ? 'warning' : 'info') }}">
                                        {{ ucfirst($vinculo->pivot->perfil) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Indefinido</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($vinculo->pivot) && isset($vinculo->pivot->status))
                                    <span class="badge bg-{{ $vinculo->pivot->status === 'ativo' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($vinculo->pivot->status) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Indefinido</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($vinculo->pivot) && isset($vinculo->pivot->data_vinculo))
                                    <small>{{ \Carbon\Carbon::parse($vinculo->pivot->data_vinculo)->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-muted">Data não disponível</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(isset($vinculo->pivot) && $vinculo->pivot->perfil !== 'proprietario')
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('comerciantes.empresas.usuarios.edit', [$empresa, $vinculo]) }}" 
                                       class="btn btn-outline-primary btn-sm" 
                                       title="Editar usuário">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button class="btn btn-outline-info btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarUsuario"
                                            data-user-id="{{ $vinculo->id ?? '' }}"
                                            data-user-nome="{{ getNomeUsuario($vinculo) }}"
                                            data-user-perfil="{{ isset($vinculo->pivot->perfil) ? $vinculo->pivot->perfil : '' }}"
                                            data-user-status="{{ isset($vinculo->pivot->status) ? $vinculo->pivot->status : '' }}"
                                            title="Configurações rápidas">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="confirmarRemocao({{ $vinculo->id ?? 0 }}, '{{ addslashes(getNomeUsuario($vinculo)) }}')"
                                            title="Remover usuário">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @else
                                <small class="text-muted">{{ isset($vinculo->pivot) && $vinculo->pivot->perfil === 'proprietario' ? 'Proprietário' : 'N/A' }}</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Nenhum usuário vinculado encontrado</p>
                                <small class="text-muted">Clique em "Criar Novo" ou "Vincular Usuário" para adicionar usuários</small>
                            </td>
                        </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <p class="mb-0"><strong>Problema com os dados de usuários vinculados</strong></p>
                                    <small>usuariosVinculados não está definido ou não é um objeto válido</small>
                                    <br><br>
                                    <a href="{{ url()->current() }}?debug=1" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-bug me-1"></i>
                                        Ver Debug
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Vincular Usuário Existente -->
<div class="modal fade" id="modalVincularUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('comerciantes.empresas.usuarios.store', $empresa) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-link me-2 text-info"></i>
                        Vincular Usuário Existente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Vincular usuário existente:</strong> Use esta opção para adicionar um usuário que já está cadastrado no sistema.
                    </div>
                    
                    <div class="mb-3">
                        <label for="user_email_vincular" class="form-label">E-mail do Usuário <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="user_email_vincular" name="user_email" required>
                        <div class="form-text">Digite o e-mail do usuário que já está cadastrado no sistema.</div>
                    </div>
                    <div class="mb-3">
                        <label for="perfil_vincular" class="form-label">Perfil <span class="text-danger">*</span></label>
                        <select class="form-select" id="perfil_vincular" name="perfil" required>
                            <option value="colaborador">Colaborador</option>
                            <option value="gerente">Gerente</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    
                    @include('components.permissions-list', ['prefix' => 'vincular'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-link me-1"></i>
                        Vincular Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Criar Novo Usuário -->
<div class="modal fade" id="modalCriarUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('comerciantes.empresas.usuarios.create', $empresa) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2 text-success"></i>
                        Criar Novo Usuário
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-plus-circle me-1"></i>
                        <strong>Criar novo usuário:</strong> Use esta opção para criar um usuário completamente novo no sistema.
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="criar_nome" name="nome" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_username" class="form-label">Nome de Usuário <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="criar_username" name="username" required maxlength="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="criar_email" name="email" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="criar_telefone" name="telefone" maxlength="20">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_senha" class="form-label">Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="criar_senha" name="senha" required minlength="6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_senha_confirmation" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="criar_senha_confirmation" name="senha_confirmation" required minlength="6">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_cargo" class="form-label">Cargo</label>
                                <input type="text" class="form-control" id="criar_cargo" name="cargo" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="criar_perfil" class="form-label">Perfil <span class="text-danger">*</span></label>
                                <select class="form-select" id="criar_perfil" name="perfil" required>
                                    <option value="colaborador">Colaborador</option>
                                    <option value="gerente">Gerente</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    @include('components.permissions-list', ['prefix' => 'criar'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>
                        Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuário -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditarUsuario">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2 text-warning"></i>
                        Editar Usuário
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-edit me-1"></i>
                        <strong>Editar usuário vinculado:</strong> Modifique as configurações de acesso deste usuário na empresa.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_nome" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_perfil" class="form-label">Perfil</label>
                        <select class="form-select" id="edit_perfil" name="perfil" required>
                            <option value="colaborador">Colaborador</option>
                            <option value="gerente">Gerente</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                            <option value="suspenso">Suspenso</option>
                        </select>
                    </div>
                    
                    @include('components.permissions-list', ['prefix' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const todasPermissoes = [
    "atendente-acesso_sistema",
    "caixa-abrir_caixa",
    "caixa-acesso_pdv",
    "caixa-excluir_item",
    "caixa-form_pgto",
    "caixa.abrir",
    "caixa.fechar",
    "caixa.relatorio",
    "caixa.sangria",
    "caixa.suprimento",
    "clientes.criar",
    "clientes.editar",
    "clientes.excluir",
    "clientes.listar",
    "clientes.visualizar",
    "configuracoes.backup",
    "configuracoes.empresa",
    "configuracoes.gerais",
    "configuracoes.impressao",
    "configuracoes.pdv",
    "configuracoes.seguranca",
    "configuracoes.sistema",
    "dashboard.relatorios",
    "dashboard.visualizar",
    "empresas.criar",
    "empresas.editar",
    "empresas.excluir",
    "empresas.listar",
    "empresas.visualizar",
    "estoque.ajustar",
    "estoque.relatorios",
    "estoque.transferir",
    "estoque.visualizar",
    "Finalizar venda",
    "financeiro.contas_pagar",
    "financeiro.contas_receber",
    "financeiro.fluxo_caixa",
    "financeiro.relatorios",
    "financeiro.visualizar",
    "horarios.criar",
    "horarios.editar",
    "horarios.excecoes.visualizar",
    "horarios.excluir",
    "horarios.listar",
    "horarios.padrao.visualizar",
    "horarios.visualizar",
    "marcas.criar",
    "marcas.editar",
    "marcas.excluir",
    "marcas.listar",
    "marcas.visualizar",
    "pdv.acessar",
    "pdv.adicionar_item",
    "pdv.aplicar_desconto",
    "pdv.cancelar_venda",
    "pdv.finalizar_venda",
    "pdv.iniciar_venda",
    "pdv.remover_item",
    "produtos.criar",
    "produtos.editar",
    "produtos.excluir",
    "produtos.gerenciar_estoque",
    "produtos.importar",
    "produtos.listar",
    "produtos.visualizar",
    "relatorios.avancados",
    "relatorios.clientes",
    "relatorios.estoque",
    "relatorios.financeiros",
    "relatorios.vendas",
    "sistema.admin",
    "sistema.logs",
    "sistema.manutencao",
    "usuarios.criar",
    "usuarios.editar",
    "usuarios.excluir",
    "usuarios.gerenciar_papeis",
    "usuarios.gerenciar_permissoes",
    "usuarios.listar",
    "usuarios.visualizar",
    "vendas.cancelar",
    "vendas.criar",
    "vendas.listar",
    "vendas.relatorios",
    "vendas.visualizar"
];

function toggleTodasPermissoes(isAdmin) {
    const checkboxes = document.querySelectorAll('input[name="permissoes[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = isAdmin;
        checkbox.disabled = isAdmin;
    });
}

// Adicionar event listener para o select de perfil
document.addEventListener('DOMContentLoaded', function() {
    // Função consolidada para gerenciar permissões de administrador
    function setupAdminPermissions(modalSelector, perfilSelector) {
        const perfilSelect = document.querySelector(modalSelector + ' ' + perfilSelector);
        if (perfilSelect) {
            perfilSelect.addEventListener('change', function() {
                const isAdmin = this.value === 'administrador';
                const modalCheckboxes = document.querySelectorAll(modalSelector + ' input[name="permissoes[]"]');
                modalCheckboxes.forEach(checkbox => {
                    checkbox.checked = isAdmin;
                    checkbox.disabled = isAdmin;
                });
            });
        }
    }

    // Aplicar para todos os modais
    setupAdminPermissions('#modalVincularUsuario', 'select[name="perfil"]');
    setupAdminPermissions('#modalCriarUsuario', 'select[name="perfil"]');
    setupAdminPermissions('#modalEditarUsuario', 'select[name="perfil"]');
});

</script>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

.table-responsive {
    border-radius: 0;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
// Editar usuário - carregar dados via AJAX
document.addEventListener('DOMContentLoaded', function() {
    const modalEditarUsuario = document.getElementById('modalEditarUsuario');
    
    modalEditarUsuario.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-user-id');
        
        if (userId) {
            // Fazer requisição AJAX para carregar dados do usuário
            fetch(`{{ route('comerciantes.empresas.usuarios.show', [$empresa, '__USER_ID__']) }}`.replace('__USER_ID__', userId))
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_nome').value = data.nome || '';
                    document.getElementById('edit_perfil').value = data.perfil || '';
                    document.getElementById('edit_status').value = data.status || '';
                    
                    // Limpar todas as permissões primeiro
                    const checkboxes = document.querySelectorAll('#modalEditarUsuario input[name="permissoes[]"]');
                    checkboxes.forEach(checkbox => checkbox.checked = false);
                    
                    // Marcar permissões do usuário
                    if (data.permissoes && Array.isArray(data.permissoes)) {
                        data.permissoes.forEach(permissao => {
                            const checkbox = document.querySelector(`#modalEditarUsuario input[value="${permissao}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }
                    
                    // Atualizar action do formulário
                    const form = document.getElementById('formEditarUsuario');
                    form.action = `{{ route('comerciantes.empresas.usuarios.update', [$empresa, '__USER_ID__']) }}`.replace('__USER_ID__', userId);
                })
                .catch(error => {
                    console.error('Erro ao carregar dados do usuário:', error);
                    alert('Erro ao carregar dados do usuário.');
                });
        }
    });
});

// Confirmar remoção
function confirmarRemocao(userId, userName) {
    if (confirm(`Tem certeza que deseja remover ${userName} desta empresa?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('comerciantes.empresas.usuarios.destroy', [$empresa, '__USER_ID__']) }}`.replace('__USER_ID__', userId);
        
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

// Validação de senha
document.addEventListener('DOMContentLoaded', function() {
    const senhaField = document.getElementById('criar_senha');
    const confirmSenhaField = document.getElementById('criar_senha_confirmation');
    
    function validarSenhas() {
        if (senhaField.value !== confirmSenhaField.value) {
            confirmSenhaField.setCustomValidity('As senhas não coincidem');
        } else {
            confirmSenhaField.setCustomValidity('');
        }
    }
    
    if (senhaField && confirmSenhaField) {
        senhaField.addEventListener('input', validarSenhas);
        confirmSenhaField.addEventListener('input', validarSenhas);
    }
});
</script>
@endpush

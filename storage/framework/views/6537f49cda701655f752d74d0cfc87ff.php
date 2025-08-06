<?php
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
?>



<?php $__env->startSection('title', 'Usuários - ' . ($empresa->nome_fantasia ?: $empresa->razao_social)); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Usuários</h1>
        <p class="text-muted mb-0"><?php echo e($empresa->nome_fantasia ?: $empresa->razao_social); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('comerciantes.empresas.show', $empresa)); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar à Empresa
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarUsuario">
            <i class="fas fa-plus me-1"></i>
            Adicionar Usuário
        </button>
    </div>
</div>

<!-- Informações da Empresa -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h6 class="card-title mb-3">
                    <i class="fas fa-building text-primary me-2"></i>
                    <?php echo e($empresa->nome_fantasia ?: $empresa->razao_social); ?>

                </h6>
                <div class="row">
                    <div class="col-sm-6">
                        <small class="text-muted">Marca:</small><br>
                        <span class="fw-medium"><?php echo e($empresa->marca?->nome ?? 'Sem marca'); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Proprietário:</small><br>
                        <span class="fw-medium">
                            <?php if($empresa->proprietario): ?>
                                <?php echo e(getNomeUsuario($empresa->proprietario)); ?>

                            <?php else: ?>
                                Sem proprietário
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="badge bg-<?php echo e($empresa->status === 'ativa' ? 'success' : 'secondary'); ?> fs-6 px-3 py-2">
                    <?php echo e(ucfirst($empresa->status)); ?>

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
            Usuários Vinculados (<?php echo e($empresa->usuariosVinculados ? $empresa->usuariosVinculados->count() : 0); ?>)
        </h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdicionarUsuario">
                <i class="fas fa-user-plus me-1"></i>
                Vincular Usuário
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCriarUsuario">
                <i class="fas fa-plus me-1"></i>
                Criar Novo
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        
        <?php if(request()->has('debug') || !isset($empresa->usuariosVinculados)): ?>
            <div class="alert alert-info m-3">
                <h6>DEBUG INFO:</h6>
                <p><strong>Empresa ID:</strong> <?php echo e($empresa->id ?? 'N/A'); ?></p>
                <p><strong>Nome:</strong> <?php echo e($empresa->nome_fantasia ?? 'N/A'); ?></p>
                <p><strong>usuariosVinculados definido:</strong> <?php echo e(isset($empresa->usuariosVinculados) ? 'SIM' : 'NÃO'); ?></p>
                <?php if(isset($empresa->usuariosVinculados)): ?>
                    <p><strong>Tipo:</strong> <?php echo e(get_class($empresa->usuariosVinculados)); ?></p>
                    <p><strong>Count:</strong> <?php echo e($empresa->usuariosVinculados->count()); ?></p>
                <?php endif; ?>
                <p><strong>Query SQL:</strong> <?php echo e($empresa->usuariosVinculados()->toSql() ?? 'N/A'); ?></p>
            </div>
        <?php endif; ?>

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
                    <?php if(isset($empresa->usuariosVinculados) && is_object($empresa->usuariosVinculados)): ?>
                        <?php $__empty_1 = true; $__currentLoopData = $empresa->usuariosVinculados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vinculo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        <?php echo e(getInicialUsuario($vinculo)); ?>

                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo e(getNomeUsuario($vinculo)); ?></div>
                                        <small class="text-muted"><?php echo e($vinculo->email ?? 'Email não disponível'); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if(isset($vinculo->pivot) && isset($vinculo->pivot->perfil)): ?>
                                    <span class="badge bg-<?php echo e($vinculo->pivot->perfil === 'proprietario' ? 'danger' : ($vinculo->pivot->perfil === 'administrador' ? 'warning' : 'info')); ?>">
                                        <?php echo e(ucfirst($vinculo->pivot->perfil)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Indefinido</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(isset($vinculo->pivot) && isset($vinculo->pivot->status)): ?>
                                    <span class="badge bg-<?php echo e($vinculo->pivot->status === 'ativo' ? 'success' : 'secondary'); ?>">
                                        <?php echo e(ucfirst($vinculo->pivot->status)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Indefinido</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(isset($vinculo->pivot) && isset($vinculo->pivot->data_vinculo)): ?>
                                    <small><?php echo e(\Carbon\Carbon::parse($vinculo->pivot->data_vinculo)->format('d/m/Y H:i')); ?></small>
                                <?php else: ?>
                                    <small class="text-muted">Data não disponível</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if(isset($vinculo->pivot) && $vinculo->pivot->perfil !== 'proprietario'): ?>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarUsuario"
                                            data-user-id="<?php echo e($vinculo->id ?? ''); ?>"
                                            data-user-nome="<?php echo e(getNomeUsuario($vinculo)); ?>"
                                            data-user-perfil="<?php echo e(isset($vinculo->pivot->perfil) ? $vinculo->pivot->perfil : ''); ?>"
                                            data-user-status="<?php echo e(isset($vinculo->pivot->status) ? $vinculo->pivot->status : ''); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="confirmarRemocao(<?php echo e($vinculo->id ?? 0); ?>, '<?php echo e(addslashes(getNomeUsuario($vinculo))); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <?php else: ?>
                                <small class="text-muted"><?php echo e(isset($vinculo->pivot) && $vinculo->pivot->perfil === 'proprietario' ? 'Proprietário' : 'N/A'); ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Nenhum usuário vinculado encontrado</p>
                                <small class="text-muted">Clique em "Criar Novo" ou "Vincular Usuário" para adicionar usuários</small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <p class="mb-0"><strong>Problema com os dados de usuários vinculados</strong></p>
                                    <small>usuariosVinculados não está definido ou não é um objeto válido</small>
                                    <br><br>
                                    <a href="<?php echo e(url()->current()); ?>?debug=1" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-bug me-1"></i>
                                        Ver Debug
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Adicionar Usuário -->
<div class="modal fade" id="modalAdicionarUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('comerciantes.empresas.usuarios.store', $empresa)); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_email" class="form-label">E-mail do Usuário</label>
                        <input type="email" class="form-control" id="user_email" name="user_email" required>
                        <div class="form-text">O usuário deve já estar cadastrado no sistema.</div>
                    </div>
                    <div class="mb-3">
                        <label for="perfil" class="form-label">Perfil</label>
                        <select class="form-select" id="perfil" name="perfil" required>
                            <option value="colaborador">Colaborador</option>
                            <option value="gerente">Gerente</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissões</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.view" id="perm_produtos_view">
                                    <label class="form-check-label" for="perm_produtos_view">Ver Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.create" id="perm_produtos_create">
                                    <label class="form-check-label" for="perm_produtos_create">Criar Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="vendas.view" id="perm_vendas_view">
                                    <label class="form-check-label" for="perm_vendas_view">Ver Vendas</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="relatorios.view" id="perm_relatorios_view">
                                    <label class="form-check-label" for="perm_relatorios_view">Ver Relatórios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="configuracoes.edit" id="perm_config_edit">
                                    <label class="form-check-label" for="perm_config_edit">Editar Configurações</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Criar Novo Usuário -->
<div class="modal fade" id="modalCriarUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('comerciantes.empresas.usuarios.create', $empresa)); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Criar Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                    
                    <div class="mb-3">
                        <label class="form-label">Permissões</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.view" id="criar_perm_produtos_view">
                                    <label class="form-check-label" for="criar_perm_produtos_view">Ver Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.create" id="criar_perm_produtos_create">
                                    <label class="form-check-label" for="criar_perm_produtos_create">Criar Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.edit" id="criar_perm_produtos_edit">
                                    <label class="form-check-label" for="criar_perm_produtos_edit">Editar Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="vendas.view" id="criar_perm_vendas_view">
                                    <label class="form-check-label" for="criar_perm_vendas_view">Ver Vendas</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="relatorios.view" id="criar_perm_relatorios_view">
                                    <label class="form-check-label" for="criar_perm_relatorios_view">Ver Relatórios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="configuracoes.edit" id="criar_perm_config_edit">
                                    <label class="form-check-label" for="criar_perm_config_edit">Editar Configurações</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="usuarios.manage" id="criar_perm_usuarios_manage">
                                    <label class="form-check-label" for="criar_perm_usuarios_manage">Gerenciar Usuários</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="horarios.manage" id="criar_perm_horarios_manage">
                                    <label class="form-check-label" for="criar_perm_horarios_manage">Gerenciar Horários</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Usuário</button>
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
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                    
                    <div class="mb-3">
                        <label class="form-label">Permissões</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.view" id="edit_perm_produtos_view">
                                    <label class="form-check-label" for="edit_perm_produtos_view">Ver Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.create" id="edit_perm_produtos_create">
                                    <label class="form-check-label" for="edit_perm_produtos_create">Criar Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="produtos.edit" id="edit_perm_produtos_edit">
                                    <label class="form-check-label" for="edit_perm_produtos_edit">Editar Produtos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="vendas.view" id="edit_perm_vendas_view">
                                    <label class="form-check-label" for="edit_perm_vendas_view">Ver Vendas</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="relatorios.view" id="edit_perm_relatorios_view">
                                    <label class="form-check-label" for="edit_perm_relatorios_view">Ver Relatórios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="configuracoes.edit" id="edit_perm_config_edit">
                                    <label class="form-check-label" for="edit_perm_config_edit">Editar Configurações</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="usuarios.manage" id="edit_perm_usuarios_manage">
                                    <label class="form-check-label" for="edit_perm_usuarios_manage">Gerenciar Usuários</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]" value="horarios.manage" id="edit_perm_horarios_manage">
                                    <label class="form-check-label" for="edit_perm_horarios_manage">Gerenciar Horários</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Editar usuário - carregar dados via AJAX
document.addEventListener('DOMContentLoaded', function() {
    const modalEditarUsuario = document.getElementById('modalEditarUsuario');
    
    modalEditarUsuario.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-user-id');
        
        if (userId) {
            // Fazer requisição AJAX para carregar dados do usuário
            fetch(`<?php echo e(route('comerciantes.empresas.usuarios.show', [$empresa, '__USER_ID__'])); ?>`.replace('__USER_ID__', userId))
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
                    form.action = `<?php echo e(route('comerciantes.empresas.usuarios.update', [$empresa, '__USER_ID__'])); ?>`.replace('__USER_ID__', userId);
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
        form.action = `<?php echo e(route('comerciantes.empresas.usuarios.destroy', [$empresa, '__USER_ID__'])); ?>`.replace('__USER_ID__', userId);
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '<?php echo e(csrf_token()); ?>';
        
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/usuarios.blade.php ENDPATH**/ ?>
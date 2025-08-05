<?php $__env->startSection('title', 'Usuários - ' . $empresa->nome); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Usuários</h1>
        <p class="text-muted mb-0"><?php echo e($empresa->nome); ?></p>
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
                    <?php echo e($empresa->nome); ?>

                </h6>
                <div class="row">
                    <div class="col-sm-6">
                        <small class="text-muted">Marca:</small><br>
                        <span class="fw-medium"><?php echo e($empresa->marca?->nome ?? 'Sem marca'); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Proprietário:</small><br>
                        <span class="fw-medium"><?php echo e($empresa->proprietario?->nome ?? 'Sem proprietário'); ?></span>
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
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="fas fa-users me-2"></i>
            Usuários Vinculados (<?php echo e($empresa->usuariosVinculados->count()); ?>)
        </h6>
    </div>
    <div class="card-body p-0">
        <?php if($empresa->usuariosVinculados->count() > 0): ?>
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
                        <?php $__currentLoopData = $empresa->usuariosVinculados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vinculo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($vinculo): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        <?php echo e(substr($vinculo->nome ?? 'U', 0, 1)); ?>

                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo e($vinculo->nome ?? 'Nome não disponível'); ?></div>
                                        <small class="text-muted"><?php echo e($vinculo->email ?? 'Email não disponível'); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($vinculo->pivot->perfil === 'proprietario' ? 'danger' : ($vinculo->pivot->perfil === 'administrador' ? 'warning' : 'info')); ?>">
                                    <?php echo e(ucfirst($vinculo->pivot->perfil ?? 'indefinido')); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($vinculo->pivot->status === 'ativo' ? 'success' : 'secondary'); ?>">
                                    <?php echo e(ucfirst($vinculo->pivot->status ?? 'indefinido')); ?>

                                </span>
                            </td>
                            <td>
                                <small><?php echo e($vinculo->pivot->data_vinculo ? \Carbon\Carbon::parse($vinculo->pivot->data_vinculo)->format('d/m/Y H:i') : 'Data não disponível'); ?></small>
                            </td>
                            <td class="text-center">
                                <?php if($vinculo->pivot->perfil !== 'proprietario'): ?>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarUsuario"
                                            data-user-id="<?php echo e($vinculo->id ?? ''); ?>"
                                            data-user-nome="<?php echo e($vinculo->nome ?? ''); ?>"
                                            data-user-perfil="<?php echo e($vinculo->pivot->perfil ?? ''); ?>"
                                            data-user-status="<?php echo e($vinculo->pivot->status ?? ''); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="confirmarRemocao(<?php echo e($vinculo->id ?? 0); ?>, '<?php echo e($vinculo->nome ?? 'Usuário'); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <?php else: ?>
                                <small class="text-muted">Proprietário</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">Nenhum usuário vinculado</h6>
                <p class="text-muted mb-3">Adicione usuários para colaborar na gestão desta empresa.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarUsuario">
                    <i class="fas fa-plus me-1"></i>
                    Adicionar Primeiro Usuário
                </button>
            </div>
        <?php endif; ?>
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
                        <label for="email" class="form-label">E-mail do Usuário</label>
                        <input type="email" class="form-control" id="email" name="email" required>
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
// Editar usuário
document.getElementById('modalEditarUsuario').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const userId = button.getAttribute('data-user-id');
    const userName = button.getAttribute('data-user-nome');
    const userPerfil = button.getAttribute('data-user-perfil');
    const userStatus = button.getAttribute('data-user-status');
    
    document.getElementById('edit_nome').value = userName;
    document.getElementById('edit_perfil').value = userPerfil;
    document.getElementById('edit_status').value = userStatus;
    
    const form = document.getElementById('formEditarUsuario');
    form.action = `<?php echo e(route('comerciantes.empresas.usuarios.update', [$empresa, '__USER_ID__'])); ?>`.replace('__USER_ID__', userId);
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
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/usuarios.blade.php ENDPATH**/ ?>
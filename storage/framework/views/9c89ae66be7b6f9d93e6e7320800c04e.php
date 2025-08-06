<?php $__env->startSection('title', $marca->nome); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tag me-2"></i>
                <?php echo e($marca->nome); ?>

            </h1>
            <p class="text-muted mb-0">
                <span class="badge bg-<?php echo e($marca->status == 'ativa' ? 'success' : ($marca->status == 'inativa' ? 'secondary' : 'warning')); ?> ms-2">
                    <?php echo e(ucfirst($marca->status)); ?>

                </span>
            </p>
        </div>
        <div class="btn-group">
            <a href="<?php echo e(route('comerciantes.marcas.edit', $marca)); ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Editar
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao()">
                        <i class="fas fa-trash me-2"></i>
                        Excluir Marca
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <!-- Coluna principal -->
        <div class="col-lg-8">
            <!-- Informações gerais -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Gerais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-gray-700 mb-2">Nome da Marca</h6>
                            <p class="mb-3"><?php echo e($marca->nome); ?></p>

                            <?php if($marca->slug): ?>
                                <h6 class="text-gray-700 mb-2">Slug</h6>
                                <p class="mb-3"><?php echo e($marca->slug); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-gray-700 mb-2">Status</h6>
                            <p class="mb-3">
                                <span class="badge bg-<?php echo e($marca->status == 'ativa' ? 'success' : ($marca->status == 'inativa' ? 'secondary' : 'warning')); ?>">
                                    <?php echo e(ucfirst($marca->status)); ?>

                                </span>
                            </p>

                            <h6 class="text-gray-700 mb-2">Criada em</h6>
                            <p class="mb-3"><?php echo e($marca->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>

                    <?php if($marca->descricao): ?>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-gray-700 mb-2">Descrição</h6>
                                <p class="mb-0"><?php echo e($marca->descricao); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Identidade Visual -->
            <?php if($marca->identidade_visual): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-palette me-2"></i>
                            Identidade Visual
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if(isset($marca->identidade_visual['cor_primaria'])): ?>
                                <div class="col-md-6">
                                    <h6 class="text-gray-700 mb-2">Cor Primária</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded me-2" 
                                             style="width: 30px; height: 30px; background-color: <?php echo e($marca->identidade_visual['cor_primaria']); ?>;">
                                        </div>
                                        <span><?php echo e($marca->identidade_visual['cor_primaria']); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(isset($marca->identidade_visual['cor_secundaria'])): ?>
                                <div class="col-md-6">
                                    <h6 class="text-gray-700 mb-2">Cor Secundária</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded me-2" 
                                             style="width: 30px; height: 30px; background-color: <?php echo e($marca->identidade_visual['cor_secundaria']); ?>;">
                                        </div>
                                        <span><?php echo e($marca->identidade_visual['cor_secundaria']); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Logo da marca -->
            <?php if($marca->logo_url): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-image me-2"></i>
                            Logo
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?php echo e($marca->logo_url); ?>" alt="Logo <?php echo e($marca->nome); ?>" 
                             class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Estatísticas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="stat-item">
                                <div class="stat-number text-primary">-</div>
                                <div class="stat-label">Empresas</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-item">
                                <div class="stat-number text-success">-</div>
                                <div class="stat-label">Empresas Ativas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-building me-2"></i>
                            Ver Empresas
                        </a>
                        <hr>
                        <a href="<?php echo e(route('comerciantes.marcas.edit', $marca)); ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Editar Marca
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informações do proprietário -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>
                        Proprietário
                    </h6>
                </div>
                <div class="card-body">
                    <?php if($marca->proprietario): ?>
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <?php echo e(substr($marca->proprietario->nome, 0, 1)); ?>

                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1"><?php echo e($marca->proprietario->nome); ?></h6>
                                <p class="text-muted mb-0 small"><?php echo e($marca->proprietario->email); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-user-times fa-2x mb-2"></i>
                            <p class="mb-0">Nenhum proprietário definido</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão voltar -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="<?php echo e(route('comerciantes.marcas.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar para Lista
            </a>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a marca <strong><?php echo e($marca->nome); ?></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?php echo e(route('comerciantes.marcas.destroy', $marca)); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 600;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.avatar {
    flex-shrink: 0;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmarExclusao() {
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/marcas/show.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Minhas Marcas'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="fas fa-tags me-2 text-primary"></i>
                    Minhas Marcas
                </h2>
                <p class="text-muted mb-0">Gerencie suas marcas e identidade visual</p>
            </div>
            <a href="<?php echo e(route('comerciantes.marcas.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nova Marca
            </a>
        </div>
    </div>
</div>

<?php if($marcas->count() > 0): ?>
    <div class="row">
        <?php $__currentLoopData = $marcas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <!-- Header da Marca -->
                        <div class="d-flex align-items-center mb-3">
                            <?php if($marca->logo_url): ?>
                                <img src="<?php echo e($marca->logo_url_completo); ?>" alt="<?php echo e($marca->nome); ?>" 
                                     class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-tags text-white fa-lg"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <h5 class="mb-1"><?php echo e($marca->nome); ?></h5>
                                <div>
                                    <span class="badge badge-<?php echo e($marca->status == 'ativa' ? 'ativa' : 'inativa'); ?>">
                                        <?php echo e(ucfirst($marca->status)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Descrição -->
                        <?php if($marca->descricao): ?>
                            <p class="text-muted small mb-3"><?php echo e(Str::limit($marca->descricao ?? '', 100)); ?></p>
                        <?php endif; ?>
                        
                        <!-- Estatísticas -->
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h6 class="mb-0 text-primary">-</h6>
                                    <small class="text-muted">Empresas</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="mb-0 text-success">-</h6>
                                <small class="text-muted">Ativas</small>
                            </div>
                        </div>
                        
                        <!-- Cores da Identidade Visual -->
                        <?php if($marca->identidade_visual): ?>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Cores da marca:</small>
                                <div class="d-flex gap-2">
                                    <div class="rounded-circle" 
                                         style="width: 20px; height: 20px; background-color: <?php echo e($marca->cor_primaria); ?>"
                                         title="Cor primária: <?php echo e($marca->cor_primaria); ?>"></div>
                                    <div class="rounded-circle" 
                                         style="width: 20px; height: 20px; background-color: <?php echo e($marca->cor_secundaria); ?>"
                                         title="Cor secundária: <?php echo e($marca->cor_secundaria); ?>"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Ações -->
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('comerciantes.marcas.show', $marca)); ?>" 
                               class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                            <a href="<?php echo e(route('comerciantes.marcas.edit', $marca)); ?>" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    onclick="confirmarExclusao('<?php echo e($marca->id); ?>', '<?php echo e($marca->nome); ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <!-- Paginação -->
    <?php if($marcas->hasPages()): ?>
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <?php echo e($marcas->links()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <!-- Estado Vazio -->
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-body py-5">
                    <i class="fas fa-tags fa-4x text-muted mb-4"></i>
                    <h4>Nenhuma marca encontrada</h4>
                    <p class="text-muted mb-4">
                        Crie sua primeira marca para começar a organizar suas empresas.
                        <br>
                        Uma marca pode ter várias empresas (unidades/lojas).
                    </p>
                    <a href="<?php echo e(route('comerciantes.marcas.create')); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Criar Primeira Marca
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a marca <strong id="nomeMarca"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> Só é possível excluir marcas que não possuem empresas vinculadas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Excluir Marca
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmarExclusao(marcaId, nomeMarca) {
        document.getElementById('nomeMarca').textContent = nomeMarca;
        document.getElementById('formExclusao').action = '/comerciantes/marcas/' + marcaId;
        
        const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
        modal.show();
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/marcas/index.blade.php ENDPATH**/ ?>
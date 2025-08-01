  

                           

<?php $__env->startSection('title', 'Nova Configuração'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Nova Configuração
                    </h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.config.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome *</label>
                                <input type="text" name="nome" class="form-control" value="<?php echo e(old('nome')); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chave *</label>
                                <input type="text" name="chave" class="form-control" value="<?php echo e(old('chave')); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="string" <?php echo e(old('tipo') == 'string' ? 'selected' : ''); ?>>Texto</option>
                                    <option value="boolean" <?php echo e(old('tipo') == 'boolean' ? 'selected' : ''); ?>>Verdadeiro/Falso</option>
                                    <option value="integer" <?php echo e(old('tipo') == 'integer' ? 'selected' : ''); ?>>Número</option>
                                    <option value="email" <?php echo e(old('tipo') == 'email' ? 'selected' : ''); ?>>Email</option>
                                    <option value="url" <?php echo e(old('tipo') == 'url' ? 'selected' : ''); ?>>URL</option>
                                    <option value="json" <?php echo e(old('tipo') == 'json' ? 'selected' : ''); ?>>JSON</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grupo ID *</label>
                                <input type="number" name="grupo_id" class="form-control" value="<?php echo e(old('grupo_id', 1)); ?>" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Valor Padrão</label>
                                <input type="text" name="valor_padrao" class="form-control" value="<?php echo e(old('valor_padrao')); ?>">
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3"><?php echo e(old('descricao')); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="visivel" value="1" class="form-check-input" <?php echo e(old('visivel', true) ? 'checked' : ''); ?>>
                                <label class="form-check-label">Visível</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="editavel" value="1" class="form-check-input" <?php echo e(old('editavel', true) ? 'checked' : ''); ?>>
                                <label class="form-check-label">Editável</label>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Salvar
                            </button>
                            <a href="<?php echo e(route('admin.config.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
                               

                               
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/config/create.blade.php ENDPATH**/ ?>
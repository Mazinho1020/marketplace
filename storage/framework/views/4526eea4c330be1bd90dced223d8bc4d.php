<?php $__env->startSection('title', $module ?? 'Módulo'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-layer-group me-2"></i>
                    <?php echo e($module ?? 'Módulo'); ?>

                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?php echo e($module ?? 'Módulo'); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="uil uil-constructor" style="font-size: 4rem; color: #667eea;"></i>
                    </div>
                    <h3 class="mb-3">Módulo <?php echo e($module ?? 'Este'); ?> em Desenvolvimento</h3>
                    <p class="text-muted mb-4">
                        Este módulo está sendo desenvolvido e estará disponível em breve.
                        <br>
                        Funcionalidades planejadas incluem gerenciamento completo de <?php echo e(strtolower($module ?? 'dados')); ?>.
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <i class="uil uil-info-circle me-2"></i>
                                <strong>Status:</strong> Em desenvolvimento ativo
                                <br>
                                <strong>Previsão:</strong> Próximas atualizações do sistema
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-primary">
                        <i class="uil uil-arrow-left me-2"></i>
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/temp.blade.php ENDPATH**/ ?>
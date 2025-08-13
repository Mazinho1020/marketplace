<?php $__env->startSection('title', 'Contas Gerenciais'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Contas Gerenciais - Empresa <?php echo e($empresa); ?></h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.empresas.financeiro.dashboard', $empresa)); ?>">Financeiro</a></li>
                        <li class="breadcrumb-item active">Contas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h5 class="card-title mb-0">Plano de Contas</h5>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.create', $empresa)); ?>" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Nova Conta
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100" id="contas-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Natureza</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $contas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($conta->codigo); ?></strong>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1"><?php echo e($conta->nome); ?></h5>
                                        <?php if($conta->descricao): ?>
                                            <span class="text-muted font-13"><?php echo e($conta->descricao); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($conta->categoria): ?>
                                            <span class="badge bg-primary"><?php echo e($conta->categoria->nome); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($conta->natureza->color()); ?>"><?php echo e($conta->natureza->value); ?></span>
                                    </td>
                                    <td>
                                        <?php if($conta->ativo): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.show', [$empresa, $conta->id])); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.edit', [$empresa, $conta->id])); ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="py-4">
                                            <i class="mdi mdi-bank-outline h1 text-muted"></i>
                                            <h5 class="text-muted">Nenhuma conta encontrada</h5>
                                            <p class="text-muted">Comece criando seu plano de contas.</p>
                                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.create', $empresa)); ?>" class="btn btn-primary">
                                                <i class="mdi mdi-plus"></i> Criar Primeira Conta
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($contas instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
                        <div class="row">
                            <div class="col-12">
                                <?php echo e($contas->links()); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas/index.blade.php ENDPATH**/ ?>
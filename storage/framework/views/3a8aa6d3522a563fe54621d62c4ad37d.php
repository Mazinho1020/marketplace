<?php $__env->startSection('title', 'Dashboard Financeiro'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Sistema Financeiro - Empresa <?php echo e($empresa); ?></h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.empresas.index')); ?>">Empresas</a></li>
                        <li class="breadcrumb-item active">Financeiro</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Dashboard Financeiro</h5>
                    
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Contas a Pagar">Contas a Pagar</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-credit-card-minus text-danger"></i></h3>
                                    <p class="text-muted">
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa)); ?>" class="btn btn-danger btn-sm">
                                            Gerenciar Contas a Pagar
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Contas a Receber">Contas a Receber</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-credit-card-plus text-success"></i></h3>
                                    <p class="text-muted">
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>" class="btn btn-success btn-sm">
                                            Gerenciar Contas a Receber
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Categorias">Categorias</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-folder text-primary"></i></h3>
                                    <p class="text-muted">
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.categorias.index', $empresa)); ?>" class="btn btn-primary btn-sm">
                                            Gerenciar Categorias
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Contas">Contas Gerenciais</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-bank text-info"></i></h3>
                                    <p class="text-muted">
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.index', $empresa)); ?>" class="btn btn-info btn-sm">
                                            Gerenciar Contas
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Relatórios">Relatórios</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-chart-line text-warning"></i></h3>
                                    <p class="text-muted">
                                        <a href="#" class="btn btn-warning btn-sm">
                                            Visualizar Relatórios
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Configurações">Configurações</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-cog text-secondary"></i></h3>
                                    <p class="text-muted">
                                        <a href="#" class="btn btn-secondary btn-sm">
                                            Configurações
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Novo Lançamento">Novo Lançamento</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-plus-circle text-success"></i></h3>
                                    <p class="text-muted">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.create', $empresa)); ?>" class="btn btn-sm btn-outline-danger">
                                                <i class="mdi mdi-minus"></i> Pagar
                                            </a>
                                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.create', $empresa)); ?>" class="btn btn-sm btn-outline-success">
                                                <i class="mdi mdi-plus"></i> Receber
                                            </a>
                                        </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-muted fw-normal mt-0" title="Dashboard">Dashboard</h4>
                                    <h3 class="my-2 py-1"><i class="mdi mdi-view-dashboard text-primary"></i></h3>
                                    <p class="text-muted">
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Resumo Geral
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="mdi mdi-information-outline"></i> Sistema Financeiro</h5>
                                <p class="mb-0">
                                    Bem-vindo ao sistema financeiro! Aqui você pode gerenciar as categorias de conta e contas gerenciais da sua empresa.
                                    O sistema está configurado para isolar os dados por empresa, garantindo a segurança das informações.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>









<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/dashboard.blade.php ENDPATH**/ ?>
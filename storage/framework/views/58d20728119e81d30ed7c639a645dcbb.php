

<?php $__env->startSection('title', 'Dashboard - Fidelidade'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h1 class="h3 mb-0">Dashboard - Programa de Fidelidade</h1>
                <p class="text-muted">Gerencie cashback, créditos e cupons dos seus clientes</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Carteiras
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e(number_format($estatisticas['total_carteiras'] ?? 0)); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Carteiras Ativas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e(number_format($estatisticas['carteiras_ativas'] ?? 0)); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Transações (Mês)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo e(number_format($estatisticas['transacoes_mes'] ?? 0)); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Cashback (Mês)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo e(number_format($estatisticas['cashback_distribuido_mes'] ?? 0, 2, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Transações Recentes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Transações Recentes</h6>
                    <a href="<?php echo e(route('fidelidade.transacoes.index')); ?>" class="btn btn-sm btn-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <?php if($transacoesRecentes->isEmpty()): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-exchange-alt fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhuma transação encontrada</p>
                        <a href="<?php echo e(route('fidelidade.transacoes.index')); ?>" class="btn btn-primary">
                            Ver Todas as Transações
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Cliente ID</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $transacoesRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>Cliente #<?php echo e($transacao->cliente_id); ?></td>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo e($transacao->tipo == 'credito' ? 'success' : 'warning'); ?>">
                                            <?php echo e(ucfirst($transacao->tipo)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-<?php echo e($transacao->tipo == 'credito' ? 'success' : 'danger'); ?>">
                                            <?php echo e($transacao->tipo == 'credito' ? '+' : '-'); ?>R$ <?php echo e(number_format($transacao->valor, 2, ',', '.')); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo e($transacao->status == 'disponivel' ? 'success' : 'secondary'); ?>">
                                            <?php echo e(ucfirst($transacao->status)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e(\Carbon\Carbon::parse($transacao->created_at)->format('d/m/Y H:i')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Clientes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Top Clientes por Saldo</h6>
                    <a href="<?php echo e(route('fidelidade.carteiras.index')); ?>" class="btn btn-sm btn-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <?php if($topClientes->isEmpty()): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhuma carteira encontrada</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Cliente ID</th>
                                    <th>Nível</th>
                                    <th>Cashback</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $topClientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carteira): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>Cliente #<?php echo e($carteira->cliente_id); ?></td>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo e($carteira->nivel_atual == 'diamond' ? 'info' : ($carteira->nivel_atual == 'ouro' ? 'warning' : ($carteira->nivel_atual == 'prata' ? 'secondary' : 'dark'))); ?>">
                                            <?php echo e(ucfirst($carteira->nivel_atual)); ?>

                                        </span>
                                    </td>
                                    <td>R$ <?php echo e(number_format($carteira->saldo_cashback, 2, ',', '.')); ?></td>
                                    <td class="font-weight-bold">R$ <?php echo e(number_format($carteira->saldo_total_disponivel,
                                        2, ',', '.')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ações Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo e(route('fidelidade.carteiras.index')); ?>"
                                class="btn btn-outline-primary btn-block">
                                <i class="fas fa-wallet me-2"></i>
                                Gerenciar Carteiras
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo e(route('fidelidade.cupons.index')); ?>" class="btn btn-outline-success btn-block">
                                <i class="fas fa-tags me-2"></i>
                                Cupons (<?php echo e($estatisticas['cupons_ativos'] ?? 0); ?>)
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo e(route('fidelidade.regras.index')); ?>" class="btn btn-outline-info btn-block">
                                <i class="fas fa-cogs me-2"></i>
                                Regras de Cashback
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo e(route('fidelidade.relatorios.index')); ?>"
                                class="btn btn-outline-warning btn-block">
                                <i class="fas fa-chart-bar me-2"></i>
                                Relatórios
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .avatar-sm {
        width: 2rem;
        height: 2rem;
        font-size: 0.875rem;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/fidelidade/dashboard.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Métodos de Pagamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-credit-card me-2"></i>
                    Métodos de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.dashboard')); ?>">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Métodos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Método -->
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $paymentMethodsStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <?php if($method->method === 'credit_card'): ?>
                                <i class="uil uil-credit-card text-primary me-2"></i>
                                Cartão de Crédito
                            <?php elseif($method->method === 'debit_card'): ?>
                                <i class="uil uil-credit-card text-success me-2"></i>
                                Cartão de Débito
                            <?php elseif($method->method === 'pix'): ?>
                                <i class="uil uil-qrcode-scan text-info me-2"></i>
                                PIX
                            <?php elseif($method->method === 'bank_slip'): ?>
                                <i class="uil uil-bill text-warning me-2"></i>
                                Boleto Bancário
                            <?php elseif($method->method === 'bank_transfer'): ?>
                                <i class="uil uil-exchange text-secondary me-2"></i>
                                Transferência Bancária
                            <?php else: ?>
                                <i class="uil uil-money-bill text-dark me-2"></i>
                                <?php echo e(ucfirst(str_replace('_', ' ', $method->method))); ?>

                            <?php endif; ?>
                        </h5>
                    </div>
                    <span class="badge bg-primary"><?php echo e($method->total_transactions); ?> transações</span>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h4 class="text-success mb-0">R$ <?php echo e(number_format($method->total_amount, 2, ',', '.')); ?></h4>
                            <small class="text-muted">Valor Total</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-primary mb-0">R$ <?php echo e(number_format($method->avg_amount, 2, ',', '.')); ?></h4>
                            <small class="text-muted">Ticket Médio</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Distribuição de Uso:</small>
                        <?php
                            $totalTransactions = $paymentMethodsStats->sum('total_transactions');
                            $percentage = $totalTransactions > 0 ? ($method->total_transactions / $totalTransactions) * 100 : 0;
                        ?>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: <?php echo e($percentage); ?>%"
                                 title="<?php echo e(number_format($percentage, 1)); ?>%"></div>
                        </div>
                        <small class="text-muted"><?php echo e(number_format($percentage, 1)); ?>% do total</small>
                    </div>

                    <a href="<?php echo e(route('admin.payments.transactions', ['payment_method' => $method->method])); ?>" 
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="uil uil-eye me-1"></i>
                        Ver Transações
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="uil uil-credit-card text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Nenhuma Transação Encontrada</h4>
                    <p class="text-muted">Quando houver transações, as estatísticas por método aparecerão aqui</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Resumo Geral -->
    <?php if($paymentMethodsStats->count() > 0): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-pie me-2"></i>
                        Resumo por Método de Pagamento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th>Transações</th>
                                    <th>Valor Total</th>
                                    <th>Ticket Médio</th>
                                    <th>Participação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $paymentMethodsStats->sortByDesc('total_amount'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if($method->method === 'credit_card'): ?>
                                            <i class="uil uil-credit-card text-primary me-2"></i>
                                            Cartão de Crédito
                                        <?php elseif($method->method === 'debit_card'): ?>
                                            <i class="uil uil-credit-card text-success me-2"></i>
                                            Cartão de Débito
                                        <?php elseif($method->method === 'pix'): ?>
                                            <i class="uil uil-qrcode-scan text-info me-2"></i>
                                            PIX
                                        <?php elseif($method->method === 'bank_slip'): ?>
                                            <i class="uil uil-bill text-warning me-2"></i>
                                            Boleto Bancário
                                        <?php elseif($method->method === 'bank_transfer'): ?>
                                            <i class="uil uil-exchange text-secondary me-2"></i>
                                            Transferência Bancária
                                        <?php else: ?>
                                            <i class="uil uil-money-bill text-dark me-2"></i>
                                            <?php echo e(ucfirst(str_replace('_', ' ', $method->method))); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo e(number_format($method->total_transactions)); ?></strong></td>
                                    <td><strong class="text-success">R$ <?php echo e(number_format($method->total_amount, 2, ',', '.')); ?></strong></td>
                                    <td>R$ <?php echo e(number_format($method->avg_amount, 2, ',', '.')); ?></td>
                                    <td>
                                        <?php
                                            $totalAmount = $paymentMethodsStats->sum('total_amount');
                                            $percentage = $totalAmount > 0 ? ($method->total_amount / $totalAmount) * 100 : 0;
                                        ?>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: <?php echo e($percentage); ?>%"></div>
                                            </div>
                                            <small><?php echo e(number_format($percentage, 1)); ?>%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.payments.transactions', ['payment_method' => $method->method])); ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="uil uil-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/payments/payment-methods.blade.php ENDPATH**/ ?>
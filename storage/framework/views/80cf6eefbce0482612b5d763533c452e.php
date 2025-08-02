<?php $__env->startSection('title', 'Transações de Pagamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-transaction me-2"></i>
                    Transações de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.dashboard')); ?>">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Transações</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="uil uil-filter me-2"></i>
                Filtros de Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.payments.transactions')); ?>">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pendente</option>
                            <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Aprovada</option>
                            <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejeitada</option>
                            <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gateway</label>
                        <select name="gateway_id" class="form-select">
                            <option value="">Todos</option>
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gateway->id); ?>" <?php echo e(request('gateway_id') == $gateway->id ? 'selected' : ''); ?>>
                                    <?php echo e($gateway->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Método</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Todos</option>
                            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($method->value); ?>" <?php echo e(request('payment_method') === $method->value ? 'selected' : ''); ?>>
                                    <?php echo e($method->label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Final</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="ID, email..." value="<?php echo e(request('search')); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="uil uil-filter me-1"></i>
                            Filtrar
                        </button>
                        <a href="<?php echo e(route('admin.payments.transactions')); ?>" class="btn btn-outline-secondary">
                            <i class="uil uil-refresh me-1"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="uil uil-list-ul me-2"></i>
                Lista de Transações (<?php echo e($transactions->count()); ?>)
            </h5>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm">
                    <i class="uil uil-export me-1"></i>
                    Exportar
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID Transação</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Email</th>
                            <th>Data/Hora</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div>
                                    <code>#<?php echo e($transaction->external_id ?? $transaction->id); ?></code>
                                    <?php if($transaction->description): ?>
                                        <br><small class="text-muted"><?php echo e(Str::limit($transaction->description, 30)); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong class="text-primary">R$ <?php echo e(number_format($transaction->amount, 2, ',', '.')); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e(ucfirst($transaction->payment_method)); ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if($transaction->gateway): ?>
                                        <div class="me-2">
                                            <?php if($transaction->gateway->logo_url): ?>
                                                <img src="<?php echo e($transaction->gateway->logo_url); ?>" 
                                                     alt="<?php echo e($transaction->gateway->name); ?>" 
                                                     style="width: 24px; height: 24px; object-fit: contain;">
                                            <?php else: ?>
                                                <i class="uil uil-server-network text-primary"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong><?php echo e($transaction->gateway->name); ?></strong>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if($transaction->status === 'approved'): ?>
                                    <span class="badge bg-success">
                                        <i class="uil uil-check me-1"></i>Aprovada
                                    </span>
                                <?php elseif($transaction->status === 'pending'): ?>
                                    <span class="badge bg-warning">
                                        <i class="uil uil-clock me-1"></i>Pendente
                                    </span>
                                <?php elseif($transaction->status === 'rejected'): ?>
                                    <span class="badge bg-danger">
                                        <i class="uil uil-times me-1"></i>Rejeitada
                                    </span>
                                <?php elseif($transaction->status === 'cancelled'): ?>
                                    <span class="badge bg-secondary">
                                        <i class="uil uil-ban me-1"></i>Cancelada
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($transaction->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($transaction->payer_email): ?>
                                    <div><?php echo e($transaction->payer_email); ?></div>
                                    <?php if($transaction->payer_name): ?>
                                        <small class="text-muted"><?php echo e($transaction->payer_name); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?php echo e($transaction->created_at->format('d/m/Y')); ?></div>
                                <small class="text-muted"><?php echo e($transaction->created_at->format('H:i:s')); ?></small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo e(route('admin.payments.transaction-details', $transaction->id)); ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                    <?php if($transaction->external_url): ?>
                                        <a href="<?php echo e($transaction->external_url); ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-info" title="Ver no Gateway">
                                            <i class="uil uil-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="uil uil-credit-card text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">Nenhuma transação encontrada</h5>
                                <p class="text-muted">Tente ajustar os filtros ou aguarde novas transações</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Informações de registros -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="pagination-info">
                <small class="text-muted">
                    Mostrando <?php echo e($transactions->count()); ?> 
                    de <?php echo e($transactions->count()); ?> registros
                </small>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/payments/transactions.blade.php ENDPATH**/ ?>
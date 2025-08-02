<?php $__env->startSection('title', 'Webhooks de Pagamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-webhook me-2"></i>
                    Webhooks de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.dashboard')); ?>">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Webhooks</li>
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
                Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.payments.webhooks')); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="received" <?php echo e(request('status') === 'received' ? 'selected' : ''); ?>>Recebido</option>
                            <option value="processed" <?php echo e(request('status') === 'processed' ? 'selected' : ''); ?>>Processado</option>
                            <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Falhou</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Final</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-filter me-1"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Webhooks -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="uil uil-list-ul me-2"></i>
                Webhooks Recebidos (<?php echo e($webhooks->count()); ?>)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Transação</th>
                            <th>Evento</th>
                            <th>Status</th>
                            <th>Tentativas</th>
                            <th>Data/Hora</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $webhooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $webhook): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code>#<?php echo e($webhook->id); ?></code></td>
                            <td>
                                <?php if($webhook->transaction): ?>
                                    <a href="<?php echo e(route('admin.payments.transaction-details', $webhook->transaction->id)); ?>" 
                                       class="text-decoration-none">
                                        <code>#<?php echo e($webhook->transaction->external_id ?? $webhook->transaction->id); ?></code>
                                    </a>
                                    <br>
                                    <small class="text-muted">R$ <?php echo e(number_format($webhook->transaction->amount, 2, ',', '.')); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($webhook->event_type): ?>
                                    <span class="badge bg-info"><?php echo e($webhook->event_type); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Webhook</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($webhook->status === 'processed'): ?>
                                    <span class="badge bg-success">
                                        <i class="uil uil-check me-1"></i>Processado
                                    </span>
                                <?php elseif($webhook->status === 'failed'): ?>
                                    <span class="badge bg-danger">
                                        <i class="uil uil-times me-1"></i>Falhou
                                    </span>
                                <?php elseif($webhook->status === 'received'): ?>
                                    <span class="badge bg-warning">
                                        <i class="uil uil-clock me-1"></i>Recebido
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($webhook->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($webhook->attempts > 1): ?>
                                    <span class="badge bg-warning"><?php echo e($webhook->attempts); ?>x</span>
                                <?php else: ?>
                                    <span class="text-muted"><?php echo e($webhook->attempts ?? 1); ?>x</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?php echo e($webhook->created_at->format('d/m/Y')); ?></div>
                                <small class="text-muted"><?php echo e($webhook->created_at->format('H:i:s')); ?></small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo e(route('admin.payments.webhook-details', $webhook->id)); ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                    <?php if($webhook->status === 'failed'): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                title="Reprocessar"
                                                onclick="reprocessWebhook(<?php echo e($webhook->id); ?>)">
                                            <i class="uil uil-refresh"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="uil uil-webhook text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">Nenhum webhook encontrado</h5>
                                <p class="text-muted">Os webhooks recebidos dos gateways aparecerão aqui</p>
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
                    Mostrando <?php echo e($webhooks->count()); ?> 
                    de <?php echo e($webhooks->count()); ?> registros
                </small>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function reprocessWebhook(webhookId) {
    if (confirm('Tem certeza que deseja reprocessar este webhook?')) {
        fetch(`/admin/payments/webhooks/${webhookId}/reprocess`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Webhook reprocessado com sucesso');
                location.reload();
            } else {
                alert('Erro ao reprocessar webhook: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao comunicar com o servidor');
        });
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/payments/webhooks.blade.php ENDPATH**/ ?>
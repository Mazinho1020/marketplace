<?php $__env->startSection('title', 'Detalhes da Transação'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-transaction me-2"></i>
                    Detalhes da Transação #<?php echo e($transaction->external_id ?? $transaction->id); ?>

                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.dashboard')); ?>">Pagamentos</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.transactions')); ?>">Transações</a></li>
                        <li class="breadcrumb-item active">Detalhes</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('admin.payments.transactions')); ?>" class="btn btn-outline-secondary">
                    <i class="uil uil-arrow-left me-1"></i>
                    Voltar
                </a>
                <?php if($transaction->external_url): ?>
                    <a href="<?php echo e($transaction->external_url); ?>" target="_blank" class="btn btn-primary">
                        <i class="uil uil-external-link-alt me-1"></i>
                        Ver no Gateway
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações Principais -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-info-circle me-2"></i>
                        Informações da Transação
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Interno:</strong></td>
                                    <td><code><?php echo e($transaction->id); ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>ID Externo:</strong></td>
                                    <td><code><?php echo e($transaction->external_id ?? 'N/A'); ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>Valor:</strong></td>
                                    <td><strong class="text-success">R$ <?php echo e(number_format($transaction->amount, 2, ',', '.')); ?></strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Método:</strong></td>
                                    <td><span class="badge bg-secondary"><?php echo e(ucfirst($transaction->payment_method)); ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
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
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Gateway:</strong></td>
                                    <td>
                                        <?php if($transaction->gateway): ?>
                                            <div class="d-flex align-items-center">
                                                <?php if($transaction->gateway->logo_url): ?>
                                                    <img src="<?php echo e($transaction->gateway->logo_url); ?>" 
                                                         alt="<?php echo e($transaction->gateway->name); ?>" 
                                                         style="width: 24px; height: 24px; object-fit: contain;"
                                                         class="me-2">
                                                <?php endif; ?>
                                                <?php echo e($transaction->gateway->name); ?>

                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Data/Hora:</strong></td>
                                    <td><?php echo e($transaction->created_at->format('d/m/Y H:i:s')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Atualizada:</strong></td>
                                    <td><?php echo e($transaction->updated_at->format('d/m/Y H:i:s')); ?></td>
                                </tr>
                                <?php if($transaction->processed_at): ?>
                                <tr>
                                    <td><strong>Processada:</strong></td>
                                    <td><?php echo e($transaction->processed_at->format('d/m/Y H:i:s')); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($transaction->gateway_fee): ?>
                                <tr>
                                    <td><strong>Taxa Gateway:</strong></td>
                                    <td>R$ <?php echo e(number_format($transaction->gateway_fee, 2, ',', '.')); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <?php if($transaction->description): ?>
                    <div class="mt-3">
                        <h6>Descrição:</h6>
                        <p class="text-muted"><?php echo e($transaction->description); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dados do Pagador -->
            <?php if($transaction->payer_email || $transaction->payer_name || $transaction->payer_document): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-user me-2"></i>
                        Dados do Pagador
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if($transaction->payer_name): ?>
                            <div class="mb-3">
                                <strong>Nome:</strong> <?php echo e($transaction->payer_name); ?>

                            </div>
                            <?php endif; ?>
                            <?php if($transaction->payer_email): ?>
                            <div class="mb-3">
                                <strong>Email:</strong> <?php echo e($transaction->payer_email); ?>

                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if($transaction->payer_document): ?>
                            <div class="mb-3">
                                <strong>Documento:</strong> <?php echo e($transaction->payer_document); ?>

                            </div>
                            <?php endif; ?>
                            <?php if($transaction->payer_phone): ?>
                            <div class="mb-3">
                                <strong>Telefone:</strong> <?php echo e($transaction->payer_phone); ?>

                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Dados Brutos -->
            <?php if($transaction->gateway_data): ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-code-branch me-2"></i>
                        Dados do Gateway
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleRawData()">
                        <i class="uil uil-eye me-1"></i>
                        Mostrar/Ocultar
                    </button>
                </div>
                <div class="card-body" id="rawDataContainer" style="display: none;">
                    <pre class="bg-light p-3 rounded"><code><?php echo e(json_encode($transaction->gateway_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-history-alt me-2"></i>
                        Timeline de Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Transação Criada</h6>
                                <small class="text-muted"><?php echo e($transaction->created_at->format('d/m/Y H:i:s')); ?></small>
                            </div>
                        </div>
                        
                        <?php if($transaction->processed_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?php echo e($transaction->status === 'approved' ? 'success' : ($transaction->status === 'rejected' ? 'danger' : 'warning')); ?>"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Status: <?php echo e(ucfirst($transaction->status)); ?></h6>
                                <small class="text-muted"><?php echo e($transaction->processed_at->format('d/m/Y H:i:s')); ?></small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($transaction->updated_at != $transaction->created_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Última Atualização</h6>
                                <small class="text-muted"><?php echo e($transaction->updated_at->format('d/m/Y H:i:s')); ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-setting me-2"></i>
                        Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if($transaction->external_url): ?>
                        <a href="<?php echo e($transaction->external_url); ?>" target="_blank" class="btn btn-outline-primary">
                            <i class="uil uil-external-link-alt me-1"></i>
                            Ver no Gateway
                        </a>
                        <?php endif; ?>
                        
                        <?php if($transaction->status === 'pending'): ?>
                        <button type="button" class="btn btn-outline-warning" onclick="refreshTransaction()">
                            <i class="uil uil-refresh me-1"></i>
                            Atualizar Status
                        </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-outline-info" onclick="exportTransaction()">
                            <i class="uil uil-export me-1"></i>
                            Exportar Dados
                        </button>
                    </div>
                </div>
            </div>

            <!-- Webhooks Relacionados -->
            <?php if($transaction->webhooks && $transaction->webhooks->count() > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-webhook me-2"></i>
                        Webhooks Recebidos (<?php echo e($transaction->webhooks->count()); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $transaction->webhooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $webhook): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?php echo e($webhook->event_type ?? 'Webhook'); ?></h6>
                                <small class="text-muted"><?php echo e($webhook->created_at->format('d/m/Y H:i:s')); ?></small>
                            </div>
                            <span class="badge bg-<?php echo e($webhook->status === 'processed' ? 'success' : 'warning'); ?>">
                                <?php echo e(ucfirst($webhook->status)); ?>

                            </span>
                        </div>
                        <?php if($webhook->gateway_data): ?>
                        <div class="mt-2">
                            <a href="<?php echo e(route('admin.payments.webhook-details', $webhook->id)); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="uil uil-eye me-1"></i>
                                Ver Detalhes
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -19px;
    top: 20px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-content {
    padding-left: 20px;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function toggleRawData() {
    const container = document.getElementById('rawDataContainer');
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
}

function refreshTransaction() {
    // Implementar atualização de status via AJAX
    alert('Funcionalidade em desenvolvimento');
}

function exportTransaction() {
    // Implementar exportação de dados
    const data = <?php echo json_encode($transaction, 15, 512) ?>;
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `transaction-${data.id}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/payments/transaction-details.blade.php ENDPATH**/ ?>
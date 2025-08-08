<?php $__env->startSection('title', 'Histórico de Pagamentos'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Histórico de Pagamentos</h1>
            <p class="text-muted mb-0">Acompanhe todas as suas transações</p>
        </div>
        <div>
            <a href="<?php echo e(route('comerciantes.planos.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-success">R$ <?php echo e(number_format($statsHistorico['total_pago'], 2, ',', '.')); ?></h4>
                    <p class="text-muted mb-0">Total Pago</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-primary"><?php echo e($statsHistorico['total_transacoes']); ?></h4>
                    <p class="text-muted mb-0">Total de Transações</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-warning"><?php echo e($statsHistorico['pendentes']); ?></h4>
                    <p class="text-muted mb-0">Pendentes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="pendente" <?php echo e(request('status') === 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                            <option value="aprovado" <?php echo e(request('status') === 'aprovado' ? 'selected' : ''); ?>>Aprovado</option>
                            <option value="cancelado" <?php echo e(request('status') === 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                            <option value="recusado" <?php echo e(request('status') === 'recusado' ? 'selected' : ''); ?>>Recusado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <select class="form-select" name="forma_pagamento">
                            <option value="">Todas</option>
                            <option value="pix" <?php echo e(request('forma_pagamento') === 'pix' ? 'selected' : ''); ?>>PIX</option>
                            <option value="credit_card" <?php echo e(request('forma_pagamento') === 'credit_card' ? 'selected' : ''); ?>>Cartão</option>
                            <option value="bank_slip" <?php echo e(request('forma_pagamento') === 'bank_slip' ? 'selected' : ''); ?>>Boleto</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Início</label>
                        <input type="date" class="form-control" name="data_inicio" value="<?php echo e(request('data_inicio')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Fim</label>
                        <input type="date" class="form-control" name="data_fim" value="<?php echo e(request('data_fim')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Transações</h5>
        </div>
        <div class="card-body p-0">
            <?php if($transacoes->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Forma de Pagamento</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $transacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo e($transacao->created_at->format('d/m/Y')); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($transacao->created_at->format('H:i')); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo e($transacao->descricao); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($transacao->codigo_transacao); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php switch($transacao->forma_pagamento):
                                            case ('pix'): ?>
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-qrcode me-1"></i>PIX
                                                </span>
                                                <?php break; ?>
                                            <?php case ('credit_card'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-credit-card me-1"></i>Cartão
                                                </span>
                                                <?php break; ?>
                                            <?php case ('bank_slip'): ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-barcode me-1"></i>Boleto
                                                </span>
                                                <?php break; ?>
                                            <?php default: ?>
                                                <span class="badge bg-secondary"><?php echo e($transacao->forma_pagamento); ?></span>
                                        <?php endswitch; ?>
                                    </td>
                                    <td>
                                        <strong>R$ <?php echo e(number_format($transacao->valor_final, 2, ',', '.')); ?></strong>
                                        <?php if($transacao->valor_desconto > 0): ?>
                                            <br>
                                            <small class="text-success">
                                                Desconto: R$ <?php echo e(number_format($transacao->valor_desconto, 2, ',', '.')); ?>

                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($transacao->status_cor); ?>">
                                            <i class="<?php echo e($transacao->status_icone); ?> me-1"></i>
                                            <?php echo e(ucfirst($transacao->status)); ?>

                                        </span>
                                        <?php if($transacao->aprovado_em): ?>
                                            <br>
                                            <small class="text-muted"><?php echo e($transacao->aprovado_em->format('d/m/Y H:i')); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" 
                                                    onclick="verDetalhes('<?php echo e($transacao->uuid); ?>')"
                                                    title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <?php if($transacao->status === 'pendente' && $transacao->forma_pagamento === 'bank_slip'): ?>
                                                <a href="#" class="btn btn-outline-secondary" title="Baixar Boleto">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if($transacao->status === 'pendente'): ?>
                                                <a href="<?php echo e(route('comerciantes.planos.checkout', $transacao->uuid)); ?>" 
                                                   class="btn btn-outline-success" title="Finalizar Pagamento">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer">
                    <?php echo e($transacoes->appends(request()->query())->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma transação encontrada</h5>
                    <p class="text-muted">Quando você realizar pagamentos, eles aparecerão aqui.</p>
                    <a href="<?php echo e(route('comerciantes.planos.planos')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Escolher um Plano
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Transação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhes-content">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function verDetalhes(uuid) {
    // Implementar modal com detalhes da transação
    $('#modalDetalhes').modal('show');
    $('#detalhes-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>');
    
    // Simular carregamento de detalhes
    setTimeout(() => {
        $('#detalhes-content').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6>Informações da Transação</h6>
                    <table class="table table-sm">
                        <tr><td><strong>UUID:</strong></td><td>${uuid}</td></tr>
                        <tr><td><strong>Data:</strong></td><td><?php echo e(now()->format('d/m/Y H:i')); ?></td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-success">Aprovado</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Informações do Pagamento</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Forma:</strong></td><td>PIX</td></tr>
                        <tr><td><strong>Valor:</strong></td><td>R$ 50,00</td></tr>
                        <tr><td><strong>Gateway:</strong></td><td>PIX Interno</td></tr>
                    </table>
                </div>
            </div>
        `);
    }, 1000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/planos/historico.blade.php ENDPATH**/ ?>
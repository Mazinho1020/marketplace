<?php $__env->startSection('title', 'Transações Fidelidade'); ?>

<?php $__env->startSection('content'); ?>
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="mdi mdi-swap-horizontal text-primary"></i> Sistema de Transações
                </h2>
                <p class="text-muted mb-0">Visualização geral de todas as transações do programa de fidelidade</p>
            </div>
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalFiltros">
                    <i class="mdi mdi-filter"></i> Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total Transações</h6>
                    <h4 class="mb-0"><?php echo e($stats['total_transacoes'] ?? 0); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-swap-horizontal text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Entradas</h6>
                    <h4 class="mb-0"><?php echo e($stats['transacoes_entrada'] ?? 0); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-arrow-up text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Saídas</h6>
                    <h4 class="mb-0"><?php echo e($stats['transacoes_saida'] ?? 0); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-arrow-down text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card danger">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Valor Total</h6>
                    <h4 class="mb-0">R$ <?php echo e(number_format($stats['valor_total'] ?? 0, 2, ',', '.')); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-currency-usd text-danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Transações -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transacoes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($transacao->created_at ?? now())->format('d/m/Y H:i')); ?></td>
                    <td><?php echo e($transacao->cliente_nome ?? 'Cliente Exemplo'); ?></td>
                    <td>
                        <span class="badge bg-<?php echo e(($transacao->tipo ?? 'credito') == 'credito' ? 'success' : 'warning'); ?>">
                            <?php echo e(ucfirst($transacao->tipo ?? 'Crédito')); ?>

                        </span>
                    </td>
                    <td>R$ <?php echo e(number_format($transacao->valor_cashback ?? 10.00, 2, ',', '.')); ?></td>
                    <td>
                        <span class="badge bg-<?php echo e(($transacao->status ?? 'confirmado') == 'confirmado' ? 'success' : 'warning'); ?>">
                            <?php echo e(ucfirst($transacao->status ?? 'Confirmado')); ?>

                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="mdi mdi-swap-horizontal text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-2 text-muted">Nenhuma transação encontrada</h5>
                        <p class="text-muted">As transações aparecerão aqui conforme são processadas</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-filter text-primary"></i> Filtros de Pesquisa
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" class="form-control" placeholder="Buscar transação...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select">
                            <option value="">Todos os Tipos</option>
                            <option value="compra">Compra</option>
                            <option value="resgate">Resgate</option>
                            <option value="bonus">Bônus</option>
                            <option value="cashback">Cashback</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select">
                            <option value="">Todos Status</option>
                            <option value="concluida">Concluída</option>
                            <option value="pendente">Pendente</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" placeholder="Data Inicial">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" placeholder="Data Final">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary">
                            <i class="mdi mdi-filter"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Tabela de Transações -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Valor/Pontos</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong>#<?php echo e($transacao->id); ?></strong></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <i class="mdi mdi-account-circle text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <strong>Cliente <?php echo e($transacao->cliente_id); ?></strong>
                                <br><small class="text-muted">ID: <?php echo e($transacao->cliente_id); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="transaction-badge <?php echo e($transacao->tipo_transacao == 'entrada' ? 'entrada' : 'saida'); ?>">
                            <i class="mdi mdi-<?php echo e($transacao->tipo_transacao == 'entrada' ? 'arrow-up' : 'arrow-down'); ?>"></i>
                            <?php echo e(ucfirst($transacao->tipo_transacao ?? 'Compra')); ?>

                        </span>
                    </td>
                    <td><?php echo e($transacao->descricao ?? 'Transação do sistema'); ?></td>
                    <td>
                        <?php if($transacao->tipo_transacao == 'entrada'): ?>
                        <span class="valor-positivo">+<?php echo e($transacao->pontos ?? 0); ?> pts</span>
                        <?php else: ?>
                        <span class="valor-negativo">-<?php echo e($transacao->pontos ?? 0); ?> pts</span>
                        <?php endif; ?>
                        <br><small class="text-muted">R$ <?php echo e(number_format($transacao->valor ?? 0, 2, ',', '.')); ?></small>
                    </td>
                    <td>
                        <?php if(($transacao->status ?? 'concluida') == 'concluida'): ?>
                        <span class="badge bg-success">Concluída</span>
                        <?php elseif($transacao->status == 'pendente'): ?>
                        <span class="badge bg-warning">Pendente</span>
                        <?php elseif($transacao->status == 'cancelada'): ?>
                        <span class="badge bg-danger">Cancelada</span>
                        <?php else: ?>
                        <span class="badge bg-secondary"><?php echo e(ucfirst($transacao->status ?? 'Concluída')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><small><?php echo e(\Carbon\Carbon::parse($transacao->criado_em ?? now())->format('d/m/Y H:i')); ?></small></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" title="Imprimir">
                            <i class="mdi mdi-printer"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="mdi mdi-swap-horizontal text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-2 text-muted">Nenhuma transação encontrada</p>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovaTransacao">
                            <i class="mdi mdi-plus"></i> Registrar Primeira Transação
                        </button>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Paginação -->
<div class="row">
    <div class="col-md-6">
        <div class="pagination-info">
            <small class="text-muted">
                <?php if(method_exists($transacoes, 'firstItem')): ?>
                    Mostrando <span><?php echo e($transacoes->firstItem() ?? 0); ?></span> a <span><?php echo e($transacoes->lastItem() ?? 0); ?></span> de <span><?php echo e($transacoes->total() ?? 0); ?></span> registros
                <?php else: ?>
                    Mostrando <?php echo e($transacoes->count() ?? 0); ?> registros
                <?php endif; ?>
            </small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end">
            <?php if(method_exists($transacoes, 'links')): ?>
                <?php echo e($transacoes->links()); ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Nova Transação -->
<div class="modal fade" id="modalNovaTransacao" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-plus"></i> Nova Transação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    Esta é uma página administrativa apenas para visualização. 
                    Para registrar transações, utilize o sistema operacional completo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.fidelidade', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/fidelidade/transacoes.blade.php ENDPATH**/ ?>
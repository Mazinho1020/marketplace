<?php $__env->startSection('title', 'Cupons Fidelidade'); ?>

<?php $__env->startSection('content'); ?>
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="mdi mdi-ticket-percent text-primary"></i> Sistema de Cupons
                </h2>
                <p class="text-muted mb-0">Gerenciamento geral de cupons do programa de fidelidade</p>
            </div>
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoCupom">
                    <i class="mdi mdi-plus"></i> Novo Cupom
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
                    <h6 class="text-muted mb-1">Total de Cupons</h6>
                    <h4 class="mb-0"><?php echo e($stats['total_cupons'] ?? 0); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-ticket-percent text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Cupons Ativos</h6>
                    <h4 class="mb-0"><?php echo e($stats['cupons_ativos'] ?? 0); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Cupons Utilizados</h6>
                    <h4 class="mb-0"><?php echo e($stats['cupons_utilizados'] ?? 0); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-check text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card danger">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Desconto Total</h6>
                    <h4 class="mb-0">R$ <?php echo e(number_format($stats['desconto_total'] ?? 0, 2, ',', '.')); ?></h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-currency-usd text-danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Lista -->
<div class="table-container">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                <input type="text" class="form-control" placeholder="Buscar cupom...">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">Todos os Status</option>
                <option value="ativo">Ativo</option>
                <option value="usado">Usado</option>
                <option value="expirado">Expirado</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">Todos os Tipos</option>
                <option value="percentual">Percentual</option>
                <option value="fixo">Valor Fixo</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select">
                <option value="codigo">Ordenar por Código</option>
                <option value="data">Ordenar por Data</option>
                <option value="desconto">Ordenar por Desconto</option>
            </select>
        </div>
    </div>

    <!-- Lista de Cupons -->
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $cupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cupom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 mb-3">
            <div class="cupom-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="mdi mdi-ticket-percent text-success me-2" style="font-size: 1.5rem;"></i>
                            <strong class="coupon-code"><?php echo e($cupom->codigo ?? 'EXEMPLO'); ?></strong>
                        </div>
                        <h6 class="mb-1"><?php echo e($cupom->nome ?? 'Nome do Cupom'); ?></h6>
                        <p class="text-muted small mb-2"><?php echo e($cupom->descricao ?? 'Descrição do cupom'); ?></p>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Desconto:</small>
                                <br><strong class="text-success">
                                    <?php echo e($cupom->valor ?? '0'); ?><?php echo e(($cupom->tipo_desconto ?? 'percentual') == 'percentual' ? '%' : ''); ?>

                                </strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Programa:</small>
                                <br><strong><?php echo e($cupom->programa_nome ?? 'Geral'); ?></strong>
                            </div>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Criado:</small>
                                <br><small><?php echo e(isset($cupom->created_at) ? \Carbon\Carbon::parse($cupom->created_at)->format('d/m/Y') : 'N/A'); ?></small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Expira:</small>
                                <br><small><?php echo e(isset($cupom->data_fim) ? \Carbon\Carbon::parse($cupom->data_fim)->format('d/m/Y') : 'N/A'); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ms-3">
                        <span class="badge bg-<?php echo e(($cupom->status ?? 'ativo') == 'ativo' ? 'success' : 'secondary'); ?>">
                            <?php echo e(ucfirst($cupom->status ?? 'Ativo')); ?>

                        </span>
                        <div class="mt-2">
                            <div class="btn-group-vertical">
                                <button class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Desativar">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="mdi mdi-ticket-percent text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">Nenhum cupom encontrado</h4>
                <p class="text-muted">Comece criando o primeiro cupom do sistema</p>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoCupom">
                    <i class="mdi mdi-plus"></i> Criar Primeiro Cupom
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="pagination-info">
                <small class="text-muted">
                    <?php if(isset($cupons) && method_exists($cupons, 'total')): ?>
                        Mostrando <span><?php echo e($cupons->firstItem() ?? 0); ?></span> a <span><?php echo e($cupons->lastItem() ?? 0); ?></span> de <span><?php echo e($cupons->total() ?? 0); ?></span> registros
                    <?php else: ?>
                        Mostrando registros de exemplo
                    <?php endif; ?>
                </small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <?php if(isset($cupons) && method_exists($cupons, 'links')): ?>
                    <?php echo e($cupons->links()); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Cupom -->
<div class="modal fade" id="modalNovoCupom" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-ticket-plus"></i> Novo Cupom
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    Esta é uma página administrativa apenas para visualização. 
                    Para criar cupons, utilize o sistema operacional completo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.fidelidade', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/fidelidade/cupons.blade.php ENDPATH**/ ?>
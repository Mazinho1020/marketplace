<?php $__env->startSection('title', 'Relatório de Estoque'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Relatório de Estoque</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.produtos.index')); ?>">Produtos</a></li>
                            <li class="breadcrumb-item active">Relatório de Estoque</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning" onclick="verificarEstoqueBaixo()">
                        <i class="fas fa-search me-2"></i>Verificar Estoque
                    </button>
                    <a href="<?php echo e(route('comerciantes.produtos.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- Resumo -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-cubes text-primary fs-1"></i>
                            </div>
                            <h3 class="fw-bold mb-1"><?php echo e($relatorio['resumo']['total_produtos']); ?></h3>
                            <p class="text-muted mb-0">Total de Produtos</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-times-circle text-danger fs-1"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-danger"><?php echo e($relatorio['resumo']['total_zerado']); ?></h3>
                            <p class="text-muted mb-0">Estoque Esgotado</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-triangle text-warning fs-1"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-warning"><?php echo e($relatorio['resumo']['total_baixo']); ?></h3>
                            <p class="text-muted mb-0">Estoque Baixo</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success fs-1"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-success"><?php echo e($relatorio['resumo']['total_normal']); ?></h3>
                            <p class="text-muted mb-0">Estoque Normal</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie text-primary me-2"></i>
                                Distribuição do Estoque
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="progress" style="height: 30px;">
                                        <?php
                                        $total = $relatorio['resumo']['total_produtos'];
                                        $percZerado = $total > 0 ? ($relatorio['resumo']['total_zerado'] / $total) * 100 : 0;
                                        $percBaixo = $total > 0 ? ($relatorio['resumo']['total_baixo'] / $total) * 100 : 0;
                                        $percCritico = $total > 0 ? ($relatorio['resumo']['total_critico'] / $total) * 100 : 0;
                                        $percNormal = $total > 0 ? ($relatorio['resumo']['total_normal'] / $total) * 100 : 0;
                                        ?>
                                        <?php if($percZerado > 0): ?>
                                        <div class="progress-bar bg-danger"
                                            role="progressbar"
                                            style="width: <?php echo e($percZerado); ?>%"
                                            title="Esgotado: <?php echo e(number_format($percZerado, 1)); ?>%">
                                            <?php if($percZerado > 10): ?><?php echo e(number_format($percZerado, 1)); ?>%<?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if($percBaixo > 0): ?>
                                        <div class="progress-bar bg-warning"
                                            role="progressbar"
                                            style="width: <?php echo e($percBaixo); ?>%"
                                            title="Baixo: <?php echo e(number_format($percBaixo, 1)); ?>%">
                                            <?php if($percBaixo > 10): ?><?php echo e(number_format($percBaixo, 1)); ?>%<?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if($percCritico > 0): ?>
                                        <div class="progress-bar bg-info"
                                            role="progressbar"
                                            style="width: <?php echo e($percCritico); ?>%"
                                            title="Crítico: <?php echo e(number_format($percCritico, 1)); ?>%">
                                            <?php if($percCritico > 10): ?><?php echo e(number_format($percCritico, 1)); ?>%<?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if($percNormal > 0): ?>
                                        <div class="progress-bar bg-success"
                                            role="progressbar"
                                            style="width: <?php echo e($percNormal); ?>%"
                                            title="Normal: <?php echo e(number_format($percNormal, 1)); ?>%">
                                            <?php if($percNormal > 10): ?><?php echo e(number_format($percNormal, 1)); ?>%<?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger rounded me-2" style="width: 12px; height: 12px;"></div>
                                            <small>Esgotado (<?php echo e(number_format($percZerado, 1)); ?>%)</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded me-2" style="width: 12px; height: 12px;"></div>
                                            <small>Baixo (<?php echo e(number_format($percBaixo, 1)); ?>%)</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info rounded me-2" style="width: 12px; height: 12px;"></div>
                                            <small>Crítico (<?php echo e(number_format($percCritico, 1)); ?>%)</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded me-2" style="width: 12px; height: 12px;"></div>
                                            <small>Normal (<?php echo e(number_format($percNormal, 1)); ?>%)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs de Produtos -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="estoqueTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active position-relative"
                                id="zerado-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#zerado"
                                type="button"
                                role="tab">
                                <i class="fas fa-times-circle text-danger me-2"></i>Estoque Esgotado
                                <?php if($relatorio['resumo']['total_zerado'] > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo e($relatorio['resumo']['total_zerado']); ?>

                                </span>
                                <?php endif; ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative"
                                id="baixo-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#baixo"
                                type="button"
                                role="tab">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Estoque Baixo
                                <?php if($relatorio['resumo']['total_baixo'] > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    <?php echo e($relatorio['resumo']['total_baixo']); ?>

                                </span>
                                <?php endif; ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative"
                                id="critico-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#critico"
                                type="button"
                                role="tab">
                                <i class="fas fa-exclamation text-info me-2"></i>Estoque Crítico
                                <?php if($relatorio['resumo']['total_critico'] > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">
                                    <?php echo e($relatorio['resumo']['total_critico']); ?>

                                </span>
                                <?php endif; ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="normal-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#normal"
                                type="button"
                                role="tab">
                                <i class="fas fa-check-circle text-success me-2"></i>Estoque Normal
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="estoqueTabContent">
                        <!-- Estoque Esgotado -->
                        <div class="tab-pane fade show active" id="zerado" role="tabpanel">
                            <?php echo $__env->make('comerciantes.produtos.partials.tabela-estoque', [
                            'produtos' => $relatorio['estoque_zerado'],
                            'tipo' => 'zerado',
                            'mensagemVazia' => 'Nenhum produto com estoque esgotado! 🎉'
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Estoque Baixo -->
                        <div class="tab-pane fade" id="baixo" role="tabpanel">
                            <?php echo $__env->make('comerciantes.produtos.partials.tabela-estoque', [
                            'produtos' => $relatorio['estoque_baixo'],
                            'tipo' => 'baixo',
                            'mensagemVazia' => 'Nenhum produto com estoque baixo! 👍'
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Estoque Crítico -->
                        <div class="tab-pane fade" id="critico" role="tabpanel">
                            <?php echo $__env->make('comerciantes.produtos.partials.tabela-estoque', [
                            'produtos' => $relatorio['estoque_critico'],
                            'tipo' => 'critico',
                            'mensagemVazia' => 'Nenhum produto com estoque crítico! ✅'
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <!-- Estoque Normal -->
                        <div class="tab-pane fade" id="normal" role="tabpanel">
                            <?php echo $__env->make('comerciantes.produtos.partials.tabela-estoque', [
                            'produtos' => $relatorio['estoque_normal']->take(50), // Limitar para performance
                            'tipo' => 'normal',
                            'mensagemVazia' => 'Nenhum produto com estoque normal encontrado.'
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function verificarEstoqueBaixo() {
        const btn = event.target;
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';

        $.ajax({
            url: '<?php echo e(route("comerciantes.produtos.verificar-estoque-baixo")); ?>',
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.dados.total_notificacoes_criadas > 0) {
                        setTimeout(() => location.reload(), 2000);
                    }
                } else {
                    toastr.error(response.message || 'Erro ao verificar estoque');
                }
            },
            error: function() {
                toastr.error('Erro ao verificar estoque');
            },
            complete: function() {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }

    // Auto-refresh a cada 5 minutos
    setInterval(function() {
        verificarEstoqueBaixo();
    }, 300000);
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
    }

    .nav-tabs .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
    }

    .progress {
        border-radius: 15px;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/produtos/relatorio-estoque.blade.php ENDPATH**/ ?>
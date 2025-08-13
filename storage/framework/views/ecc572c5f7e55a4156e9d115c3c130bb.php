<?php $__env->startSection('title', 'Minha Assinatura'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Alertas -->
    <?php if(count($alertas) > 0): ?>
        <?php $__currentLoopData = $alertas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert alert-<?php echo e($alerta['tipo']); ?> alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo e($alerta['titulo']); ?>

                </h6>
                <p class="mb-2"><?php echo e($alerta['mensagem']); ?></p>
                <a href="<?php echo e($alerta['acao']); ?>" class="btn btn-sm btn-<?php echo e($alerta['tipo']); ?>">
                    Tomar Ação
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Header da Assinatura -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-<?php echo e($assinatura ? $assinatura->plano->cor : 'secondary'); ?> text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">
                                <i class="fas fa-crown me-2"></i>
                                <?php echo e($assinatura ? $assinatura->plano->nome : 'Sem Plano Ativo'); ?>

                            </h3>
                            <p class="mb-0 opacity-75">
                                <?php if($assinatura): ?>
                                    Status: <?php echo e(ucfirst($assinatura->status)); ?> • 
                                    Ciclo: <?php echo e(ucfirst($assinatura->ciclo_cobranca)); ?> •
                                    Expira em: <?php echo e($assinatura->expira_em->format('d/m/Y')); ?>

                                    (<?php echo e($assinatura->dias_restantes); ?> dias restantes)
                                <?php else: ?>
                                    Escolha um plano para começar a usar o sistema
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h4 class="mb-0">
                                <?php if($assinatura): ?>
                                    R$ <?php echo e(number_format($assinatura->valor, 2, ',', '.')); ?>

                                    <small>/<?php echo e($assinatura->ciclo_cobranca === 'anual' ? 'ano' : 'mês'); ?></small>
                                <?php else: ?>
                                    --
                                <?php endif; ?>
                            </h4>
                            <a href="<?php echo e(route('comerciantes.planos.planos')); ?>" class="btn btn-light btn-sm mt-2">
                                <i class="fas fa-arrow-up me-1"></i>
                                <?php echo e($assinatura ? 'Alterar Plano' : 'Escolher Plano'); ?>

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas de Uso -->
    <?php if($assinatura): ?>
        <div class="row mb-4">
            <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-1">
                                        <?php echo e(match($key) {
                                            'transacoes_mes' => 'Transações/Mês',
                                            'usuarios' => 'Usuários Ativos',
                                            'storage_mb' => 'Armazenamento (MB)',
                                            default => ucfirst($key)
                                        }); ?>

                                    </h6>
                                    <h4 class="mb-0">
                                        <?php echo e($stat['usado']); ?>

                                        <?php if($stat['limite'] > 0): ?>
                                            <small class="text-muted">/ <?php echo e($stat['limite']); ?></small>
                                        <?php else: ?>
                                            <small class="text-success">/ Ilimitado</small>
                                        <?php endif; ?>
                                    </h4>
                                </div>
                                <div class="text-end">
                                    <?php
                                        $porcentagem = $stat['limite'] > 0 ? ($stat['usado'] / $stat['limite']) * 100 : 0;
                                        $cor = $porcentagem > 80 ? 'danger' : ($porcentagem > 60 ? 'warning' : 'success');
                                    ?>
                                    <span class="badge bg-<?php echo e($cor); ?>">
                                        <?php echo e($stat['limite'] > 0 ? number_format($porcentagem, 0) . '%' : '∞'); ?>

                                    </span>
                                </div>
                            </div>
                            
                            <?php if($stat['limite'] > 0): ?>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-<?php echo e($cor); ?>" 
                                         style="width: <?php echo e(min($porcentagem, 100)); ?>%"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <!-- Transações Recentes e Ações Rápidas -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Transações Recentes
                    </h5>
                    <a href="<?php echo e(route('comerciantes.planos.historico')); ?>" class="btn btn-outline-primary btn-sm">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if($transacoesRecentes->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <?php $__currentLoopData = $transacoesRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="<?php echo e($transacao->status_icone); ?> text-<?php echo e($transacao->status_cor); ?> me-2"></i>
                                                    <div>
                                                        <div class="fw-bold"><?php echo e($transacao->descricao); ?></div>
                                                        <small class="text-muted"><?php echo e($transacao->created_at->format('d/m/Y H:i')); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="fw-bold">R$ <?php echo e(number_format($transacao->valor_final, 2, ',', '.')); ?></div>
                                                <span class="badge bg-<?php echo e($transacao->status_cor); ?>">
                                                    <?php echo e(ucfirst($transacao->status)); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Nenhuma transação encontrada</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-rocket me-2"></i>Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if(!$assinatura): ?>
                            <a href="<?php echo e(route('comerciantes.planos.planos')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Escolher Plano
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('comerciantes.planos.planos')); ?>" class="btn btn-warning">
                                <i class="fas fa-arrow-up me-2"></i>Fazer Upgrade
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo e(route('comerciantes.planos.historico')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-history me-2"></i>Ver Histórico
                        </a>
                        
                        <?php if($assinatura && $assinatura->renovacao_automatica): ?>
                            <button class="btn btn-outline-danger" onclick="toggleRenovacao(false)">
                                <i class="fas fa-pause me-2"></i>Pausar Renovação
                            </button>
                        <?php elseif($assinatura): ?>
                            <button class="btn btn-outline-success" onclick="toggleRenovacao(true)">
                                <i class="fas fa-play me-2"></i>Ativar Renovação
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Suporte -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-headset me-2"></i>Suporte
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Precisa de ajuda? Entre em contato:</p>
                    
                    <?php if($assinatura && $assinatura->plano->hasFeature('suporte_24h')): ?>
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-success btn-sm">
                                <i class="fas fa-phone me-2"></i>Suporte 24h
                            </a>
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-comments me-2"></i>Chat Online
                            </a>
                            <a href="mailto:suporte@marketplace.com" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-envelope me-2"></i>Email
                            </a>
                        </div>
                    <?php elseif($assinatura && $assinatura->plano->hasFeature('suporte_chat')): ?>
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-comments me-2"></i>Chat Online
                            </a>
                            <a href="mailto:suporte@marketplace.com" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-envelope me-2"></i>Email
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="mailto:suporte@marketplace.com" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-envelope me-2"></i>Suporte por Email
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleRenovacao(ativar) {
    if (confirm(ativar ? 'Ativar renovação automática?' : 'Pausar renovação automática?')) {
        // Implementar AJAX para toggle de renovação
        fetch('<?php echo e(route("comerciantes.planos.toggle-renovacao")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ renovacao_automatica: ativar })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar configuração');
            }
        });
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/planos/dashboard.blade.php ENDPATH**/ ?>
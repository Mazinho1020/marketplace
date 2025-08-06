<?php $__env->startSection('title', 'Horários de Funcionamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-clock text-primary"></i>
                        Horários de Funcionamento
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.empresas.show', $empresaId)); ?>">Empresa</a></li>
                            <li class="breadcrumb-item active">Horários</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?php echo e(route('comerciantes.empresas.show', $empresaId)); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <?php if(session('sucesso')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo e(session('sucesso')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('erro')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo e(session('erro')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Status Atual -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Status Atual
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($horarioAtual): ?>
                        <div class="d-flex align-items-center">
                            <?php if($horarioAtual->estaAberto()): ?>
                                <span class="badge bg-success fs-6 me-3">
                                    <i class="fas fa-clock"></i> ABERTO
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6 me-3">
                                    <i class="fas fa-times-circle"></i> FECHADO
                                </span>
                            <?php endif; ?>
                            <div>
                                <strong><?php echo e($horarioAtual->horario_formatado); ?></strong>
                                <div class="small text-muted">Sistema: <?php echo e($horarioAtual->sistema); ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-question-circle fa-2x mb-2"></i>
                            <p>Nenhum horário configurado para hoje</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Resumo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 text-primary mb-1"><?php echo e($horariosPadrao->count()); ?></div>
                            <small class="text-muted">Horários Padrão</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-warning mb-1"><?php echo e($proximasExcecoes->count()); ?></div>
                            <small class="text-muted">Próximas Exceções</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-info mb-1"><?php echo e(count($sistemas)); ?></div>
                            <small class="text-muted">Sistemas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu de Ações -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo e(route('comerciantes.horarios.padrao.index', $empresaId)); ?>" 
                               class="btn btn-outline-primary w-100 p-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-week fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h6 class="mb-1">Horários Padrão</h6>
                                        <small class="text-muted">Configure horários da semana</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo e(route('comerciantes.horarios.excecoes.index', $empresaId)); ?>" 
                               class="btn btn-outline-warning w-100 p-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h6 class="mb-1">Exceções</h6>
                                        <small class="text-muted">Feriados e datas especiais</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Horários Padrão (Resumo) -->
    <?php if($horariosPadrao->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week"></i> Horários da Semana
                    </h5>
                    <a href="<?php echo e(route('comerciantes.horarios.padrao.index', $empresaId)); ?>" 
                       class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Dia</th>
                                    <th>Sistema</th>
                                    <th>Horário</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $horariosPadrao->take(7); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $horario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($horario->nome_dia_semana); ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($horario->sistema === 'TODOS' ? 'primary' : 'secondary'); ?>">
                                            <?php echo e($horario->sistema); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($horario->horario_formatado); ?></td>
                                    <td>
                                        <?php if(!$horario->aberto): ?>
                                            <span class="badge bg-danger">Fechado</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Aberto</span>
                                        <?php endif; ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Auto-atualizar status a cada 5 minutos
    setInterval(function() {
        fetch('<?php echo e(route("comerciantes.horarios.api.status", $empresaId)); ?>')
            .then(response => response.json())
            .then(data => {
                // Atualizar status na tela se necessário
                console.log('Status atualizado:', data);
            })
            .catch(error => console.error('Erro ao atualizar status:', error));
    }, 300000); // 5 minutos
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/horarios/index.blade.php ENDPATH**/ ?>
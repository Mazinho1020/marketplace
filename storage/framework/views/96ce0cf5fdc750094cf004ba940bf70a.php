<?php $__env->startSection('title', 'Notificações'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-bell me-2"></i>
                        Notificações
                    </h1>
                    <p class="text-muted mb-0">Gerencie suas notificações e alertas</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="marcarTodasComoLidas()">
                        <i class="fas fa-check-double me-1"></i>
                        Marcar Todas como Lidas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Notificações
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Não Lidas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['nao_lidas']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['hoje']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Taxa de Leitura
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['taxa_leitura']); ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('comerciantes.notificacoes.index')); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="entregue" <?php echo e(request('status') == 'entregue' ? 'selected' : ''); ?>>Entregue</option>
                                <option value="lido" <?php echo e(request('status') == 'lido' ? 'selected' : ''); ?>>Lido</option>
                                <option value="erro" <?php echo e(request('status') == 'erro' ? 'selected' : ''); ?>>Erro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="canal">Canal</label>
                            <select name="canal" id="canal" class="form-control">
                                <option value="">Todos</option>
                                <option value="in_app" <?php echo e(request('canal') == 'in_app' ? 'selected' : ''); ?>>In-App</option>
                                <option value="push" <?php echo e(request('canal') == 'push' ? 'selected' : ''); ?>>Push</option>
                                <option value="email" <?php echo e(request('canal') == 'email' ? 'selected' : ''); ?>>Email</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="data_inicio">Data Início</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo e(request('data_inicio')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="data_fim">Data Fim</label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?php echo e(request('data_fim')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Notificações -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Notificações</h6>
        </div>
        <div class="card-body">
            <?php if($notificacoes->count() > 0): ?>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $notificacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notificacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item list-group-item-action <?php echo e(is_null($notificacao->lido_em) ? 'bg-light' : ''); ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <i class="fas fa-bell <?php echo e(is_null($notificacao->lido_em) ? 'text-warning' : 'text-muted'); ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 <?php echo e(is_null($notificacao->lido_em) ? 'font-weight-bold' : ''); ?>">
                                            <?php echo e($notificacao->titulo); ?>

                                        </h6>
                                        <p class="mb-1"><?php echo e(Str::limit($notificacao->mensagem, 100)); ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-tag"></i> <?php echo e(ucfirst($notificacao->canal)); ?>

                                            <?php if($notificacao->lido_em): ?>
                                                • <i class="fas fa-check text-success"></i> Lida em <?php echo e($notificacao->lido_em->format('d/m/Y H:i')); ?>

                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted"><?php echo e($notificacao->created_at->diffForHumans()); ?></small>
                                    <div class="mt-1">
                                        <?php if(is_null($notificacao->lido_em)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="marcarComoLida(<?php echo e($notificacao->id); ?>)">
                                                <i class="fas fa-check"></i> Marcar como Lida
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('comerciantes.notificacoes.show', $notificacao->id)); ?>" 
                                           class="btn btn-sm btn-outline-secondary ml-1">
                                            <i class="fas fa-eye"></i> Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($notificacoes->appends(request()->query())->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma notificação encontrada</h5>
                    <p class="text-muted">Quando você receber notificações, elas aparecerão aqui.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function marcarComoLida(id) {
    fetch(`/comerciantes/notificacoes/${id}/marcar-lida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao marcar como lida');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao marcar como lida');
    });
}

function marcarTodasComoLidas() {
    if (confirm('Tem certeza que deseja marcar todas as notificações como lidas?')) {
        fetch('/comerciantes/notificacoes/marcar-todas-lidas', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao marcar todas como lidas');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao marcar todas como lidas');
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/notificacoes/index.blade.php ENDPATH**/ ?>
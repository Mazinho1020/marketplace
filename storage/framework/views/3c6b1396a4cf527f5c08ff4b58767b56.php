<?php $__env->startSection('title', 'Registros Deletados - Fidelidade'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-trash-alt me-2"></i>
                    Registros Deletados - <?php echo e(ucfirst($tipo)); ?>

                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.fidelidade.index')); ?>">Fidelidade</a></li>
                        <li class="breadcrumb-item active">Registros Deletados</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('admin.fidelidade.index')); ?>" class="btn btn-primary">
                    <i class="uil uil-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros por Tipo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Filtrar por Tipo:</h6>
                    <div class="btn-group" role="group">
                        <a href="<?php echo e(route('admin.fidelidade.deletados', 'carteiras')); ?>" 
                           class="btn btn-outline-primary <?php echo e($tipo === 'carteiras' ? 'active' : ''); ?>">
                            Carteiras
                        </a>
                        <a href="<?php echo e(route('admin.fidelidade.deletados', 'cupons')); ?>" 
                           class="btn btn-outline-success <?php echo e($tipo === 'cupons' ? 'active' : ''); ?>">
                            Cupons
                        </a>
                        <a href="<?php echo e(route('admin.fidelidade.deletados', 'creditos')); ?>" 
                           class="btn btn-outline-info <?php echo e($tipo === 'creditos' ? 'active' : ''); ?>">
                            Créditos
                        </a>
                        <a href="<?php echo e(route('admin.fidelidade.deletados', 'conquistas')); ?>" 
                           class="btn btn-outline-warning <?php echo e($tipo === 'conquistas' ? 'active' : ''); ?>">
                            Conquistas
                        </a>
                        <a href="<?php echo e(route('admin.fidelidade.deletados', 'transacoes')); ?>" 
                           class="btn btn-outline-secondary <?php echo e($tipo === 'transacoes' ? 'active' : ''); ?>">
                            Transações
                        </a>
                        <a href="<?php echo e(route('admin.fidelidade.deletados', 'regras')); ?>" 
                           class="btn btn-outline-danger <?php echo e($tipo === 'regras' ? 'active' : ''); ?>">
                            Regras
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Registros Deletados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-list-ul me-2"></i>
                        Registros Deletados - <?php echo e(ucfirst($tipo)); ?>

                        <span class="badge bg-warning ms-2"><?php echo e($dados->total()); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($dados->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <?php if($tipo === 'carteiras'): ?>
                                            <th>Cliente ID</th>
                                            <th>Empresa ID</th>
                                            <th>Status</th>
                                            <th>Nível</th>
                                        <?php elseif($tipo === 'cupons'): ?>
                                            <th>Código</th>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                        <?php elseif($tipo === 'creditos'): ?>
                                            <th>Cliente ID</th>
                                            <th>Tipo</th>
                                            <th>Valor Original</th>
                                            <th>Status</th>
                                        <?php elseif($tipo === 'conquistas'): ?>
                                            <th>Nome</th>
                                            <th>XP Recompensa</th>
                                            <th>Crédito Recompensa</th>
                                            <th>Ativo</th>
                                        <?php elseif($tipo === 'transacoes'): ?>
                                            <th>Cliente ID</th>
                                            <th>Tipo</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                        <?php endif; ?>
                                        <th>Deletado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $dados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($item->id); ?></td>
                                            <?php if($tipo === 'carteiras'): ?>
                                                <td><?php echo e($item->cliente_id); ?></td>
                                                <td><?php echo e($item->empresa_id); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo e($item->status === 'ativa' ? 'success' : 'warning'); ?>">
                                                        <?php echo e(ucfirst($item->status)); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?php echo e(ucfirst($item->nivel_atual)); ?></span>
                                                </td>
                                            <?php elseif($tipo === 'cupons'): ?>
                                                <td><code><?php echo e($item->codigo); ?></code></td>
                                                <td><?php echo e($item->nome); ?></td>
                                                <td><?php echo e($item->tipo); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo e($item->status === 'ativo' ? 'success' : 'secondary'); ?>">
                                                        <?php echo e(ucfirst($item->status)); ?>

                                                    </span>
                                                </td>
                                            <?php elseif($tipo === 'creditos'): ?>
                                                <td><?php echo e($item->cliente_id); ?></td>
                                                <td><?php echo e($item->tipo); ?></td>
                                                <td>R$ <?php echo e(number_format($item->valor_original, 2, ',', '.')); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo e($item->status === 'ativo' ? 'success' : 'secondary'); ?>">
                                                        <?php echo e(ucfirst($item->status)); ?>

                                                    </span>
                                                </td>
                                            <?php elseif($tipo === 'conquistas'): ?>
                                                <td><?php echo e($item->nome); ?></td>
                                                <td><?php echo e($item->xp_recompensa); ?></td>
                                                <td>R$ <?php echo e(number_format($item->credito_recompensa, 2, ',', '.')); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo e($item->ativo ? 'success' : 'danger'); ?>">
                                                        <?php echo e($item->ativo ? 'Sim' : 'Não'); ?>

                                                    </span>
                                                </td>
                                            <?php elseif($tipo === 'transacoes'): ?>
                                                <td><?php echo e($item->cliente_id); ?></td>
                                                <td><?php echo e($item->tipo); ?></td>
                                                <td>R$ <?php echo e(number_format($item->valor, 2, ',', '.')); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo e(ucfirst($item->status)); ?></span>
                                                </td>
                                            <?php endif; ?>
                                            <td><?php echo e($item->deleted_at->format('d/m/Y H:i')); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            onclick="restaurarRegistro('<?php echo e($tipo); ?>', <?php echo e($item->id); ?>)">
                                                        <i class="uil uil-redo"></i>
                                                        Restaurar
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="deletarPermanente('<?php echo e($tipo); ?>', <?php echo e($item->id); ?>)">
                                                        <i class="uil uil-times"></i>
                                                        Excluir
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($dados->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="uil uil-smile text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhum registro deletado encontrado</h5>
                            <p class="text-muted">Não há <?php echo e($tipo); ?> deletados no momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function restaurarRegistro(tipo, id) {
    if (confirm('Tem certeza que deseja restaurar este registro?')) {
        fetch('<?php echo e(route("admin.fidelidade.restaurar")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                tipo: tipo.slice(0, -1), // Remove 's' do final
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao restaurar registro');
        });
    }
}

function deletarPermanente(tipo, id) {
    if (confirm('ATENÇÃO: Esta ação é irreversível! Tem certeza que deseja deletar permanentemente este registro?')) {
        fetch('<?php echo e(route("admin.fidelidade.deletar-permanente")); ?>', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                tipo: tipo.slice(0, -1), // Remove 's' do final
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao deletar registro');
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/fidelidade/deletados.blade.php ENDPATH**/ ?>
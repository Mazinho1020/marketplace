<?php $__env->startSection('title', 'Gerenciar Empresas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                Minhas Empresas
            </h1>
            <p class="text-muted mb-0">Gerencie suas unidades de negócio</p>
        </div>
        <a href="<?php echo e(route('comerciantes.empresas.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nova Empresa
        </a>
    </div>

    <!-- Filtros e busca -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('comerciantes.empresas.index')); ?>" class="row g-3">
                <div class="col-md-6">
                    <label for="busca" class="form-label">Buscar empresa</label>
                    <input type="text" class="form-control" id="busca" name="busca" 
                           value="<?php echo e(request('busca')); ?>" placeholder="Nome, CNPJ ou cidade">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="ativa" <?php echo e(request('status') == 'ativa' ? 'selected' : ''); ?>>Ativa</option>
                        <option value="inativa" <?php echo e(request('status') == 'inativa' ? 'selected' : ''); ?>>Inativa</option>
                        <option value="suspensa" <?php echo e(request('status') == 'suspensa' ? 'selected' : ''); ?>>Suspensa</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Filtrar
                    </button>
                    <a href="<?php echo e(route('comerciantes.empresas.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de empresas -->
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card h-100 shadow-sm empresa-card" data-empresa-id="<?php echo e($empresa->id); ?>">
                    <!-- Badge de status -->
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-<?php echo e($empresa->status == 'ativa' ? 'success' : ($empresa->status == 'inativa' ? 'secondary' : 'warning')); ?>">
                            <?php echo e(ucfirst($empresa->status)); ?>

                        </span>
                    </div>

                    <div class="card-body">
                        <!-- Nome da empresa -->
                        <h5 class="card-title mb-2">
                            <a href="<?php echo e(route('comerciantes.empresas.show', $empresa)); ?>" 
                               class="text-decoration-none text-primary">
                                <?php echo e($empresa->razao_social ?: $empresa->nome_fantasia ?: 'Empresa sem nome'); ?>

                            </a>
                        </h5>

                        <!-- Informações básicas -->
                        <div class="empresa-info">
                            <?php if($empresa->cnpj): ?>
                                <p class="mb-1 small">
                                    <i class="fas fa-id-card me-2 text-muted"></i>
                                    <strong>CNPJ:</strong> <?php echo e($empresa->cnpj); ?>

                                </p>
                            <?php endif; ?>

                            <?php if($empresa->cidade && $empresa->uf): ?>
                                <p class="mb-1 small">
                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                    <strong>Local:</strong> <?php echo e($empresa->cidade); ?>/<?php echo e($empresa->uf); ?>

                                </p>
                            <?php endif; ?>

                            <?php if($empresa->telefone): ?>
                                <p class="mb-1 small">
                                    <i class="fas fa-phone me-2 text-muted"></i>
                                    <strong>Telefone:</strong> <?php echo e($empresa->telefone); ?>

                                </p>
                            <?php endif; ?>

                            <?php if($empresa->email): ?>
                                <p class="mb-1 small">
                                    <i class="fas fa-envelope me-2 text-muted"></i>
                                    <strong>Email:</strong> <?php echo e($empresa->email); ?>

                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Estatísticas rápidas -->
                        <div class="row text-center mt-3 pt-3 border-top">
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo e($empresa->usuarios_vinculados_count ?? 0); ?></div>
                                    <div class="stat-label">Usuários</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo e($empresa->produtos_count ?? 0); ?></div>
                                    <div class="stat-label">Produtos</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo e($empresa->pedidos_count ?? 0); ?></div>
                                    <div class="stat-label">Pedidos</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100" role="group">
                            <a href="<?php echo e(route('comerciantes.empresas.show', $empresa)); ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                            <a href="<?php echo e(route('comerciantes.empresas.edit', $empresa)); ?>" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Editar
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('comerciantes.dashboard.empresa', $empresa)); ?>">
                                            <i class="fas fa-tachometer-alt me-2"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.usuarios.index', $empresa)); ?>">
                                            <i class="fas fa-users me-2"></i>
                                            Usuários
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.financeiro.dashboard', $empresa)); ?>">
                                            <i class="fas fa-coins me-2"></i>
                                            Sistema Financeiro
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('comerciantes.empresas.destroy', $empresa)); ?>" 
                                              class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta empresa?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>
                                                Excluir
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma empresa encontrada</h5>
                        <p class="text-muted mb-4">
                            <?php if(request()->hasAny(['busca', 'status'])): ?>
                                Tente ajustar os filtros ou criar uma nova empresa.
                            <?php else: ?>
                                Comece criando sua primeira empresa.
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo e(route('comerciantes.empresas.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Criar Primeira Empresa
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <?php if($empresas->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($empresas->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.empresa-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid rgba(0,0,0,.125);
}

.empresa-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bs-primary);
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.empresa-info .small {
    font-size: 0.85rem;
}

.empresa-info i {
    width: 16px;
    text-align: center;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit do formulário quando alterar filtros
    const filtros = document.querySelectorAll('#status');
    filtros.forEach(filtro => {
        filtro.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Atalho para busca
    const campoBusca = document.getElementById('busca');
    if (campoBusca) {
        campoBusca.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', $empresa->razao_social ?: $empresa->nome_fantasia ?: 'Empresa'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                <?php echo e($empresa->razao_social ?: $empresa->nome_fantasia ?: 'Empresa sem nome'); ?>

            </h1>
            <p class="text-muted mb-0">
                <span class="badge bg-<?php echo e($empresa->status == 'ativa' ? 'success' : ($empresa->status == 'inativa' ? 'secondary' : 'warning')); ?> ms-2">
                    <?php echo e(ucfirst($empresa->status)); ?>

                </span>
            </p>
        </div>
        <div class="btn-group">
            <a href="<?php echo e(route('comerciantes.empresas.edit', $empresa)); ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Editar
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
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
                        Gerenciar Usuários
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao()">
                        <i class="fas fa-trash me-2"></i>
                        Excluir Empresa
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Cards de Estatísticas Principais -->
    <div class="row mb-4">
        <!-- Clientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Clientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($estatisticas['clientes']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Funcionários -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Funcionários
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($estatisticas['funcionarios']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fornecedores -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Fornecedores
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($estatisticas['fornecedores']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entregadores -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Entregadores
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($estatisticas['entregadores']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-motorcycle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Gestão de Pessoas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users-cog me-2"></i>
                Gestão de Pessoas
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Clientes -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6 class="text-primary mb-1">
                                <i class="fas fa-users me-2"></i>
                                Clientes
                            </h6>
                            <small class="text-muted"><?php echo e($estatisticas['clientes']); ?> registros</small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('comerciantes.clientes.pessoas.index')); ?>?empresa_id=<?php echo e($empresa->id); ?>&tipo=cliente" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Funcionários -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6 class="text-success mb-1">
                                <i class="fas fa-user-tie me-2"></i>
                                Funcionários
                            </h6>
                            <small class="text-muted"><?php echo e($estatisticas['funcionarios']); ?> registros</small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('comerciantes.clientes.pessoas.index')); ?>?empresa_id=<?php echo e($empresa->id); ?>&tipo=funcionario" 
                               class="btn btn-sm btn-outline-success">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Fornecedores -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6 class="text-info mb-1">
                                <i class="fas fa-truck me-2"></i>
                                Fornecedores
                            </h6>
                            <small class="text-muted"><?php echo e($estatisticas['fornecedores']); ?> registros</small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('comerciantes.clientes.pessoas.index')); ?>?empresa_id=<?php echo e($empresa->id); ?>&tipo=fornecedor" 
                               class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Entregadores -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6 class="text-warning mb-1">
                                <i class="fas fa-motorcycle me-2"></i>
                                Entregadores
                            </h6>
                            <small class="text-muted"><?php echo e($estatisticas['entregadores']); ?> registros</small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('comerciantes.clientes.pessoas.index')); ?>?empresa_id=<?php echo e($empresa->id); ?>&tipo=entregador" 
                               class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Departamentos -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6 class="text-secondary mb-1">
                                <i class="fas fa-sitemap me-2"></i>
                                Departamentos
                            </h6>
                            <small class="text-muted"><?php echo e($estatisticas['departamentos']); ?> registros</small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('comerciantes.clientes.departamentos.index')); ?>?empresa_id=<?php echo e($empresa->id); ?>" 
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Cargos -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6 class="text-dark mb-1">
                                <i class="fas fa-briefcase me-2"></i>
                                Cargos
                            </h6>
                            <small class="text-muted"><?php echo e($estatisticas['cargos']); ?> registros</small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('comerciantes.clientes.cargos.index')); ?>?empresa_id=<?php echo e($empresa->id); ?>" 
                               class="btn btn-sm btn-outline-dark">
                                <i class="fas fa-eye me-1"></i>
                                Ver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Coluna principal -->
        <div class="col-lg-8">
            <!-- Informações gerais -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Gerais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-gray-700 mb-2">Razão Social</h6>
                            <p class="mb-3"><?php echo e($empresa->razao_social ?: 'Não informado'); ?></p>

                            <?php if($empresa->nome_fantasia): ?>
                                <h6 class="text-gray-700 mb-2">Nome Fantasia</h6>
                                <p class="mb-3"><?php echo e($empresa->nome_fantasia); ?></p>
                            <?php endif; ?>

                            <?php if($empresa->cnpj): ?>
                                <h6 class="text-gray-700 mb-2">CNPJ</h6>
                                <p class="mb-3"><?php echo e($empresa->cnpj); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-gray-700 mb-2">Status</h6>
                            <p class="mb-3">
                                <span class="badge bg-<?php echo e($empresa->status == 'ativa' ? 'success' : ($empresa->status == 'inativa' ? 'secondary' : 'warning')); ?>">
                                    <?php echo e(ucfirst($empresa->status)); ?>

                                </span>
                            </p>

                            <h6 class="text-gray-700 mb-2">Criada em</h6>
                            <p class="mb-3"><?php echo e($empresa->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contato -->
            <?php if($empresa->telefone || $empresa->email || $empresa->site): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-phone me-2"></i>
                            Informações de Contato
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if($empresa->telefone): ?>
                                <div class="col-md-4">
                                    <h6 class="text-gray-700 mb-2">Telefone</h6>
                                    <p class="mb-3">
                                        <a href="tel:<?php echo e($empresa->telefone); ?>" class="text-decoration-none">
                                            <?php echo e($empresa->telefone); ?>

                                        </a>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if($empresa->email): ?>
                                <div class="col-md-4">
                                    <h6 class="text-gray-700 mb-2">Email</h6>
                                    <p class="mb-3">
                                        <a href="mailto:<?php echo e($empresa->email); ?>" class="text-decoration-none">
                                            <?php echo e($empresa->email); ?>

                                        </a>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if($empresa->site): ?>
                                <div class="col-md-4">
                                    <h6 class="text-gray-700 mb-2">Website</h6>
                                    <p class="mb-3">
                                        <a href="<?php echo e($empresa->site); ?>" target="_blank" class="text-decoration-none">
                                            <?php echo e($empresa->site); ?>

                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estatísticas de Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Status das Pessoas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pessoas Ativas</span>
                            <span class="text-success font-weight-bold"><?php echo e($estatisticasStatus['pessoas_ativas']); ?></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo e($estatisticasStatus['pessoas_ativas'] > 0 ? ($estatisticasStatus['pessoas_ativas'] / max(1, $estatisticasStatus['pessoas_ativas'] + $estatisticasStatus['pessoas_inativas'])) * 100 : 0); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pessoas Inativas</span>
                            <span class="text-danger font-weight-bold"><?php echo e($estatisticasStatus['pessoas_inativas']); ?></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: <?php echo e($estatisticasStatus['pessoas_inativas'] > 0 ? ($estatisticasStatus['pessoas_inativas'] / max(1, $estatisticasStatus['pessoas_ativas'] + $estatisticasStatus['pessoas_inativas'])) * 100 : 0); ?>%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Funcionários Ativos</span>
                            <span class="text-info font-weight-bold"><?php echo e($estatisticasStatus['funcionarios_ativos']); ?></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: <?php echo e($estatisticasStatus['funcionarios_ativos'] > 0 ? ($estatisticasStatus['funcionarios_ativos'] / max(1, $estatisticas['funcionarios'])) * 100 : 0); ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('comerciantes.dashboard.empresa', $empresa)); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Ver Dashboard
                        </a>
                        <a href="<?php echo e(route('comerciantes.clientes.pessoas.create')); ?>?empresa_id=<?php echo e($empresa->id); ?>" class="btn btn-outline-success">
                            <i class="fas fa-user-plus me-2"></i>
                            Adicionar Pessoa
                        </a>
                        <a href="<?php echo e(route('comerciantes.empresas.usuarios.index', $empresa)); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-users me-2"></i>
                            Gerenciar Usuários
                        </a>
                        <hr>
                        <a href="<?php echo e(route('comerciantes.empresas.edit', $empresa)); ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Editar Empresa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão voltar -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="<?php echo e(route('comerciantes.empresas.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar para Lista
            </a>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a empresa <strong><?php echo e($empresa->razao_social ?: $empresa->nome_fantasia ?: 'Esta empresa'); ?></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?php echo e(route('comerciantes.empresas.destroy', $empresa)); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmarExclusao() {
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/show.blade.php ENDPATH**/ ?>
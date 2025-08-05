<?php $__env->startSection('title', $empresa->nome); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                <?php echo e($empresa->nome); ?>

            </h1>
            <p class="text-muted mb-0">
                <?php if($empresa->marca): ?>
                    <i class="fas fa-tag me-1"></i>
                    <?php echo e($empresa->marca->nome); ?>

                <?php endif; ?>
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
                            <h6 class="text-gray-700 mb-2">Nome da Empresa</h6>
                            <p class="mb-3"><?php echo e($empresa->nome); ?></p>

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
                            <?php if($empresa->marca): ?>
                                <h6 class="text-gray-700 mb-2">Marca</h6>
                                <p class="mb-3">
                                    <a href="<?php echo e(route('comerciantes.marcas.show', $empresa->marca)); ?>" 
                                       class="text-decoration-none">
                                        <?php echo e($empresa->marca->nome); ?>

                                    </a>
                                </p>
                            <?php endif; ?>

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

            <!-- Endereço -->
            <?php if($empresa->endereco_logradouro || $empresa->endereco_cidade): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Endereço
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                    $endereco = collect([
                                        $empresa->endereco_logradouro,
                                        $empresa->endereco_numero,
                                        $empresa->endereco_complemento
                                    ])->filter()->implode(', ');
                                    
                                    $localidade = collect([
                                        $empresa->endereco_bairro,
                                        $empresa->endereco_cidade,
                                        $empresa->endereco_estado
                                    ])->filter()->implode(', ');
                                ?>

                                <?php if($endereco): ?>
                                    <p class="mb-2">
                                        <strong>Logradouro:</strong> <?php echo e($endereco); ?>

                                    </p>
                                <?php endif; ?>

                                <?php if($localidade): ?>
                                    <p class="mb-2">
                                        <strong>Localidade:</strong> <?php echo e($localidade); ?>

                                    </p>
                                <?php endif; ?>

                                <?php if($empresa->endereco_cep): ?>
                                    <p class="mb-2">
                                        <strong>CEP:</strong> <?php echo e($empresa->endereco_cep); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contato -->
            <?php if($empresa->telefone || $empresa->email || $empresa->website): ?>
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

                            <?php if($empresa->website): ?>
                                <div class="col-md-4">
                                    <h6 class="text-gray-700 mb-2">Website</h6>
                                    <p class="mb-3">
                                        <a href="<?php echo e($empresa->website); ?>" target="_blank" class="text-decoration-none">
                                            <?php echo e($empresa->website); ?>

                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Horário de funcionamento -->
            <?php if($empresa->horario_funcionamento): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-2"></i>
                            Horário de Funcionamento
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php
                            $horarios = $empresa->horario_funcionamento;
                            $diasSemana = [
                                'segunda' => 'Segunda-feira',
                                'terca' => 'Terça-feira',
                                'quarta' => 'Quarta-feira',
                                'quinta' => 'Quinta-feira',
                                'sexta' => 'Sexta-feira',
                                'sabado' => 'Sábado',
                                'domingo' => 'Domingo'
                            ];
                        ?>

                        <div class="row">
                            <?php $__currentLoopData = $diasSemana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dia => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <strong><?php echo e($nome); ?>:</strong>
                                    <?php if(isset($horarios[$dia]) && $horarios[$dia]['abertura'] && $horarios[$dia]['fechamento']): ?>
                                        <span class="text-success">
                                            <?php echo e($horarios[$dia]['abertura']); ?> às <?php echo e($horarios[$dia]['fechamento']); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Fechado</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estatísticas rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number text-primary"><?php echo e($empresa->usuarios_vinculados_count ?? 0); ?></div>
                                <div class="stat-label">Usuários</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number text-success"><?php echo e($empresa->produtos_count ?? 0); ?></div>
                                <div class="stat-label">Produtos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="stat-number text-info"><?php echo e($empresa->pedidos_count ?? 0); ?></div>
                                <div class="stat-label">Pedidos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="stat-number text-warning"><?php echo e($empresa->avaliacoes_count ?? 0); ?></div>
                                <div class="stat-label">Avaliações</div>
                            </div>
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

            <!-- Informações do proprietário -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>
                        Proprietário
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <?php if($empresa->proprietario->avatar): ?>
                                <img src="<?php echo e($empresa->proprietario->avatar); ?>" alt="Avatar" 
                                     class="rounded-circle" width="50" height="50">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <?php echo e(substr($empresa->proprietario->nome, 0, 1)); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h6 class="mb-1"><?php echo e($empresa->proprietario->nome); ?></h6>
                            <p class="text-muted mb-0 small"><?php echo e($empresa->proprietario->email); ?></p>
                        </div>
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
                <p>Tem certeza que deseja excluir a empresa <strong><?php echo e($empresa->nome); ?></strong>?</p>
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
.stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 600;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.avatar {
    flex-shrink: 0;
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

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/show.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Detalhes da Conta Gerencial'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.dashboard.empresa', $empresa)); ?>">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.dashboard', $empresa)); ?>">Financeiro</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.index', $empresa)); ?>">Contas Gerenciais</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($conta->nome); ?></li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?php echo e($conta->nome); ?></h1>
        <div class="btn-group">
            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.edit', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
               class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.index', $empresa)); ?>" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações Principais -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informações da Conta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Nome:</dt>
                                <dd class="col-sm-8"><?php echo e($conta->nome); ?></dd>

                                <?php if($conta->codigo): ?>
                                <dt class="col-sm-4">Código:</dt>
                                <dd class="col-sm-8">
                                    <code><?php echo e($conta->codigo); ?></code>
                                </dd>
                                <?php endif; ?>

                                <dt class="col-sm-4">Natureza:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-<?php echo e($conta->natureza->color()); ?>">
                                        <?php echo e($conta->natureza->label()); ?>

                                    </span>
                                </dd>

                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-<?php echo e($conta->ativo ? 'success' : 'secondary'); ?>">
                                        <?php echo e($conta->ativo ? 'Ativa' : 'Inativa'); ?>

                                    </span>
                                </dd>

                                <?php if($conta->nivel): ?>
                                <dt class="col-sm-4">Nível:</dt>
                                <dd class="col-sm-8"><?php echo e($conta->nivel); ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <dl class="row">
                                <?php if($conta->categoria): ?>
                                <dt class="col-sm-4">Categoria:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-info"><?php echo e($conta->categoria->nome); ?></span>
                                </dd>
                                <?php endif; ?>

                                <?php if($conta->classificacaoDre): ?>
                                <dt class="col-sm-4">Classificação DRE:</dt>
                                <dd class="col-sm-8"><?php echo e($conta->classificacaoDre->nome); ?></dd>
                                <?php endif; ?>

                                <?php if($conta->tipo): ?>
                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8"><?php echo e($conta->tipo->nome); ?></dd>
                                <?php endif; ?>

                                <?php if($conta->contaPai): ?>
                                <dt class="col-sm-4">Conta Pai:</dt>
                                <dd class="col-sm-8">
                                    <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->contaPai->id])); ?>">
                                        <?php echo e($conta->contaPai->nome); ?>

                                    </a>
                                </dd>
                                <?php endif; ?>

                                <dt class="col-sm-4">Aceita Lançamento:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-<?php echo e($conta->aceita_lancamento ? 'success' : 'warning'); ?>">
                                        <?php echo e($conta->aceita_lancamento ? 'Sim' : 'Não'); ?>

                                    </span>
                                </dd>

                                <dt class="col-sm-4">É Sintética:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-<?php echo e($conta->e_sintetica ? 'info' : 'light'); ?>">
                                        <?php echo e($conta->e_sintetica ? 'Sim' : 'Não'); ?>

                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <?php if($conta->descricao): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Descrição:</strong>
                            <p class="mt-2"><?php echo e($conta->descricao); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Classificações -->
            <?php if($conta->e_custo || $conta->e_despesa || $conta->e_receita): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags"></i> Classificações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php if($conta->e_custo): ?>
                            <span class="badge badge-warning">Custo</span>
                        <?php endif; ?>
                        <?php if($conta->e_despesa): ?>
                            <span class="badge badge-danger">Despesa</span>
                        <?php endif; ?>
                        <?php if($conta->e_receita): ?>
                            <span class="badge badge-success">Receita</span>
                        <?php endif; ?>
                    </div>

                    <?php if($conta->grupo_dre): ?>
                    <div class="mt-3">
                        <strong>Grupo DRE:</strong> <?php echo e($conta->grupo_dre); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Contas Filhas -->
            <?php if($conta->filhos && $conta->filhos->count() > 0): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sitemap"></i> Contas Filhas (<?php echo e($conta->filhos->count()); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Código</th>
                                    <th>Natureza</th>
                                    <th>Status</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $conta->filhos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filho): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($filho->nome); ?></td>
                                    <td>
                                        <?php if($filho->codigo): ?>
                                            <code><?php echo e($filho->codigo); ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo e($filho->natureza->color()); ?> badge-sm">
                                            <?php echo e($filho->natureza->value); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo e($filho->ativo ? 'success' : 'secondary'); ?> badge-sm">
                                            <?php echo e($filho->ativo ? 'Ativa' : 'Inativa'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $filho->id])); ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.edit', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Editar Conta
                        </a>
                        
                        <?php if($conta->aceita_lancamento): ?>
                        <button class="btn btn-success btn-sm" type="button">
                            <i class="fas fa-plus"></i> Novo Lançamento
                        </button>
                        <?php endif; ?>

                        <button class="btn btn-info btn-sm" type="button">
                            <i class="fas fa-chart-line"></i> Ver Relatórios
                        </button>

                        <hr class="my-3">

                        <button class="btn btn-outline-danger btn-sm" 
                                onclick="if(confirm('Tem certeza que deseja excluir esta conta?')) { document.getElementById('delete-form').submit(); }">
                            <i class="fas fa-trash"></i> Excluir Conta
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informações Técnicas -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Informações Técnicas
                    </h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <strong>ID:</strong> <?php echo e($conta->id); ?>

                        </div>
                        <div class="mb-2">
                            <strong>Criado em:</strong> <?php echo e($conta->created_at->format('d/m/Y H:i')); ?>

                        </div>
                        <div class="mb-2">
                            <strong>Atualizado em:</strong> <?php echo e($conta->updated_at->format('d/m/Y H:i')); ?>

                        </div>
                        <?php if($conta->ordem_exibicao): ?>
                        <div class="mb-2">
                            <strong>Ordem:</strong> <?php echo e($conta->ordem_exibicao); ?>

                        </div>
                        <?php endif; ?>
                    </small>
                </div>
            </div>

            <!-- Visual -->
            <?php if($conta->cor || $conta->icone): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-palette"></i> Visual
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($conta->cor): ?>
                    <div class="mb-3">
                        <strong>Cor:</strong>
                        <span class="d-inline-block ms-2" 
                              style="width: 20px; height: 20px; background-color: <?php echo e($conta->cor); ?>; border: 1px solid #ddd; border-radius: 3px;"></span>
                        <code class="ms-2"><?php echo e($conta->cor); ?></code>
                    </div>
                    <?php endif; ?>

                    <?php if($conta->icone): ?>
                    <div>
                        <strong>Ícone:</strong>
                        <i class="<?php echo e($conta->icone); ?> ms-2"></i>
                        <code class="ms-2"><?php echo e($conta->icone); ?></code>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Form para deletar -->
<form id="delete-form" 
      action="<?php echo e(route('comerciantes.empresas.financeiro.contas.destroy', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
      method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.badge-sm {
    font-size: 0.75em;
}

.gap-2 > * {
    margin-bottom: 0.5rem;
}

dl.row {
    margin-bottom: 0;
}

dl.row dt {
    font-weight: 600;
}

dl.row dd {
    margin-bottom: 0.5rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas/show.blade.php ENDPATH**/ ?>
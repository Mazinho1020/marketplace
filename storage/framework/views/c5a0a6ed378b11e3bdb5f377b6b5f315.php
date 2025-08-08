<?php $__env->startSection('title', 'Pessoas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        Pessoas
                        <?php if(request('tipo')): ?>
                            - <?php echo e(ucfirst(str_replace('_', ' ', request('tipo')))); ?>s
                        <?php endif; ?>
                    </h1>
                    <p class="text-muted mb-0">
                        Gerencie as pessoas da empresa
                        <?php if(request('empresa_id')): ?>
                            (Empresa ID: <?php echo e(request('empresa_id')); ?>)
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <?php if(request('empresa_id')): ?>
                        <a href="/comerciantes/empresas/<?php echo e(request('empresa_id')); ?>" 
                           class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Voltar à Empresa
                        </a>
                    <?php endif; ?>
                    <a href="/comerciantes/clientes/pessoas/create<?php echo e(request('empresa_id') ? '?empresa_id=' . request('empresa_id') : ''); ?><?php echo e(request('tipo') ? (request('empresa_id') ? '&' : '?') . 'tipo=' . request('tipo') : ''); ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Pessoa
                    </a>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h5 class="card-title"><?php echo e($stats['total']); ?></h5>
                            <p class="card-text text-muted">Total</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-user-friends fa-2x text-success mb-2"></i>
                            <h5 class="card-title"><?php echo e($stats['clientes']); ?></h5>
                            <p class="card-text text-muted">Clientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-user-tie fa-2x text-info mb-2"></i>
                            <h5 class="card-title"><?php echo e($stats['funcionarios']); ?></h5>
                            <p class="card-text text-muted">Funcionários</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                            <h5 class="card-title"><?php echo e($stats['fornecedores'] + $stats['entregadores']); ?></h5>
                            <p class="card-text text-muted">Fornecedores/Entregadores</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/comerciantes/clientes/pessoas">
                        <?php if(request('empresa_id')): ?>
                            <input type="hidden" name="empresa_id" value="<?php echo e(request('empresa_id')); ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?php echo e($filtros['nome'] ?? ''); ?>" placeholder="Nome da pessoa...">
                            </div>
                            <div class="col-md-2">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo">
                                    <option value="">Todos</option>
                                    <option value="cliente" <?php echo e(($filtros['tipo'] ?? '') == 'cliente' ? 'selected' : ''); ?>>Cliente</option>
                                    <option value="funcionario" <?php echo e(($filtros['tipo'] ?? '') == 'funcionario' ? 'selected' : ''); ?>>Funcionário</option>
                                    <option value="fornecedor" <?php echo e(($filtros['tipo'] ?? '') == 'fornecedor' ? 'selected' : ''); ?>>Fornecedor</option>
                                    <option value="entregador" <?php echo e(($filtros['tipo'] ?? '') == 'entregador' ? 'selected' : ''); ?>>Entregador</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="ativo" <?php echo e(($filtros['status'] ?? '') == 'ativo' ? 'selected' : ''); ?>>Ativo</option>
                                    <option value="inativo" <?php echo e(($filtros['status'] ?? '') == 'inativo' ? 'selected' : ''); ?>>Inativo</option>
                                    <option value="suspenso" <?php echo e(($filtros['status'] ?? '') == 'suspenso' ? 'selected' : ''); ?>>Suspenso</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="departamento_id" class="form-label">Departamento</label>
                                <select class="form-select" id="departamento_id" name="departamento_id">
                                    <option value="">Todos</option>
                                    <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($dept->id); ?>" <?php echo e(($filtros['departamento_id'] ?? '') == $dept->id ? 'selected' : ''); ?>>
                                            <?php echo e($dept->nome); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="cargo_id" class="form-label">Cargo</label>
                                <select class="form-select" id="cargo_id" name="cargo_id">
                                    <option value="">Todos</option>
                                    <?php $__currentLoopData = $cargos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cargo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cargo->id); ?>" <?php echo e(($filtros['cargo_id'] ?? '') == $cargo->id ? 'selected' : ''); ?>>
                                            <?php echo e($cargo->nome); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Pessoas -->
            <div class="card">
                <div class="card-body">
                    <?php if($pessoas->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Departamento</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $pessoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pessoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    <?php if($pessoa->foto_url): ?>
                                                        <img src="<?php echo e($pessoa->foto_url); ?>" alt="<?php echo e($pessoa->nome); ?>" class="rounded-circle" width="32" height="32">
                                                    <?php else: ?>
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                                            <?php echo e(strtoupper(substr($pessoa->nome, 0, 1))); ?><?php echo e(strtoupper(substr($pessoa->sobrenome ?? '', 0, 1))); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <strong><?php echo e($pessoa->nome); ?> <?php echo e($pessoa->sobrenome); ?></strong>
                                                    <?php if($pessoa->nome_social): ?>
                                                        <br><small class="text-muted">"<?php echo e($pessoa->nome_social); ?>"</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                                $tipos = explode(',', $pessoa->tipo);
                                                $cores = [
                                                    'cliente' => 'success',
                                                    'funcionario' => 'primary',
                                                    'fornecedor' => 'warning',
                                                    'entregador' => 'info'
                                                ];
                                            ?>
                                            <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-<?php echo e($cores[trim($tipo)] ?? 'secondary'); ?> me-1">
                                                    <?php echo e(ucfirst(trim($tipo))); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td>
                                            <?php if($pessoa->cpf_cnpj): ?>
                                                <code><?php echo e($pessoa->cpf_cnpj); ?></code>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($pessoa->email): ?>
                                                <a href="mailto:<?php echo e($pessoa->email); ?>"><?php echo e($pessoa->email); ?></a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($pessoa->telefone): ?>
                                                <a href="tel:<?php echo e($pessoa->telefone); ?>"><?php echo e($pessoa->telefone); ?></a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($pessoa->departamento_nome): ?>
                                                <span class="badge bg-light text-dark"><?php echo e($pessoa->departamento_nome); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php switch($pessoa->status):
                                                case ('ativo'): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                    <?php break; ?>
                                                <?php case ('inativo'): ?>
                                                    <span class="badge bg-secondary">Inativo</span>
                                                    <?php break; ?>
                                                <?php case ('suspenso'): ?>
                                                    <span class="badge bg-warning">Suspenso</span>
                                                    <?php break; ?>
                                                <?php default: ?>
                                                    <span class="badge bg-light text-dark"><?php echo e(ucfirst($pessoa->status)); ?></span>
                                            <?php endswitch; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/comerciantes/clientes/pessoas/<?php echo e($pessoa->id); ?>" 
                                                   class="btn btn-outline-primary" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/comerciantes/clientes/pessoas/<?php echo e($pessoa->id); ?>/edit" 
                                                   class="btn btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmarExclusao(<?php echo e($pessoa->id); ?>)" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            <?php echo e($pessoas->withQueryString()->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma pessoa encontrada</h5>
                            <p class="text-muted">
                                <?php if(array_filter($filtros)): ?>
                                    Ajuste os filtros ou 
                                <?php endif; ?>
                                crie uma nova pessoa para começar
                            </p>
                            <a href="/comerciantes/clientes/pessoas/create<?php echo e(request('empresa_id') ? '?empresa_id=' . request('empresa_id') : ''); ?><?php echo e(request('tipo') ? (request('empresa_id') ? '&' : '?') . 'tipo=' . request('tipo') : ''); ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Pessoa
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta pessoa?</p>
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita e pode afetar outros registros relacionados.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmarExclusao(id) {
    const form = document.getElementById('formExclusao');
    form.action = `/comerciantes/clientes/pessoas/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}

// Auto-submit ao mudar filtros de tipo e status
document.addEventListener('DOMContentLoaded', function() {
    const tipo = document.getElementById('tipo');
    const status = document.getElementById('status');
    const departamento = document.getElementById('departamento_id');
    const cargo = document.getElementById('cargo_id');
    
    [tipo, status, departamento, cargo].forEach(element => {
        if (element) {
            element.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciante/pessoas/index.blade.php ENDPATH**/ ?>
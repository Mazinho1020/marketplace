<?php $__env->startSection('title', 'Contas a Receber'); ?>

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
            <li class="breadcrumb-item active" aria-current="page">Contas a Receber</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Contas a Receber</h1>
        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.create', $empresa)); ?>" 
           class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Conta a Receber
        </a>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total em Aberto
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo e(number_format($estatisticas['total_aberto'] ?? 0, 2, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Vencendo Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo e(number_format($estatisticas['vencendo_hoje'] ?? 0, 2, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Em Atraso
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo e(number_format($estatisticas['em_atraso'] ?? 0, 2, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-times fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Recebido Este Mês
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo e(number_format($estatisticas['total_recebido'] ?? 0, 2, ',', '.')); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <label for="situacao" class="form-label">Situação</label>
                        <select name="situacao_financeira" id="situacao_financeira" class="form-control">
                            <option value="">Todas</option>
                            <option value="pendente" <?php echo e(request('situacao') == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                            <option value="recebido" <?php echo e(request('situacao') == 'pago' ? 'selected' : ''); ?>>Recebido</option>
                            <option value="cancelado" <?php echo e(request('situacao') == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                            <option value="vencido" <?php echo e(request('situacao') == 'vencido' ? 'selected' : ''); ?>>Vencido</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" 
                               value="<?php echo e(request('data_inicio')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control" 
                               value="<?php echo e(request('data_fim')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Descrição, cliente..." value="<?php echo e(request('search')); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Data Vencimento</th>
                            <th>Descrição</th>
                            <th>Cliente</th>
                            <th>Valor</th>
                            <th>Situação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $contasReceber; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($conta->situacao_financeira->value == 'vencido' ? 'table-danger' : ''); ?>">
                            <td>
                                <?php echo e($conta->data_vencimento->format('d/m/Y')); ?>

                                <?php if($conta->data_vencimento->isPast() && $conta->situacao_financeira->value == 'pendente'): ?>
                                    <span class="badge badge-danger ml-1">Vencido</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo e($conta->descricao); ?></strong>
                                <?php if($conta->observacoes): ?>
                                    <br><small class="text-muted"><?php echo e(Str::limit($conta->observacoes, 50)); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($conta->pessoa): ?>
                                    <?php echo e($conta->pessoa->nome); ?>

                                    <br><small class="text-muted"><?php echo e($conta->pessoa->tipo_pessoa); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong>R$ <?php echo e(number_format($conta->valor_liquido, 2, ',', '.')); ?></strong>
                                <?php if($conta->pagamentos()->where("status_pagamento", "confirmado")->sum("valor") > 0): ?>
                                    <br><small class="text-success">
                                        Recebido: R$ <?php echo e(number_format($conta->pagamentos()->where("status_pagamento", "confirmado")->sum("valor"), 2, ',', '.')); ?>

                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $badgeClass = match($conta->situacao_financeira->value) {
                                        'pendente' => 'warning',
                                        'pago' => 'success',
                                        'cancelado' => 'secondary',
                                        'vencido' => 'danger',
                                        default => 'info'
                                    };
                                ?>
                                <span class="badge badge-<?php echo e($badgeClass); ?>">
                                    <?php echo e($conta->situacao_financeira->label()); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.show', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
                                       class="btn btn-outline-info" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if($conta->situacao_financeira->value == 'pendente'): ?>
                                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.edit', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="gerarBoleto(<?php echo e($conta->id); ?>)" title="Gerar Boleto">
                                            <i class="fas fa-barcode"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="excluirConta(<?php echo e($conta->id); ?>)" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <br>Nenhuma conta a receber encontrada.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if($contasReceber->hasPages()): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?php echo e($contasReceber->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.border-left-danger { border-left: 4px solid #e74c3c !important; }
.border-left-warning { border-left: 4px solid #f39c12 !important; }
.border-left-success { border-left: 4px solid #27ae60 !important; }
.border-left-info { border-left: 4px solid #3498db !important; }

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}

.text-xs {
    font-size: 0.7rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.075);
}

.badge {
    font-size: 0.75em;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function gerarBoleto(contaId) {
    if (confirm('Deseja gerar o boleto para esta conta?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("comerciantes.empresas.financeiro.contas-receber.gerar-boleto", ["empresa" => $empresa, "id" => "__ID__"])); ?>'.replace('__ID__', contaId);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function excluirConta(contaId) {
    if (confirm('Tem certeza que deseja excluir esta conta?')) {
        // Usar form submit tradicional
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("comerciantes.empresas.financeiro.contas-receber.destroy", ["empresa" => $empresa, "id" => "__ID__"])); ?>'.replace('__ID__', contaId);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php $__env->stopPush(); ?>









<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas-receber/index.blade.php ENDPATH**/ ?>
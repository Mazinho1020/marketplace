

<?php $__env->startSection('title', 'Detalhes da Conta a Receber'); ?>

<?php $__env->startPush('styles'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopPush(); ?>

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
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>">Contas a Receber</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($contaReceber->descricao); ?></li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?php echo e($contaReceber->descricao); ?></h1>
        <div class="btn-group">
            <?php if($contaReceber->situacao_financeira->value == 'pendente'): ?>
            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.edit', ['empresa' => $empresa, 'id' => $contaReceber->id])); ?>"
                class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Dados Principais -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informações Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Situação:</dt>
                        <dd class="col-sm-9">
                            <?php
                            $badgeClass = match($contaReceber->situacao_financeira) {
                            'pendente' => 'warning',
                            'pago' => 'success',
                            'vencido' => 'danger',
                            'cancelado' => 'secondary',
                            'em_negociacao' => 'info',
                            default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?php echo e($badgeClass); ?>">
                                <?php echo e($contaReceber->situacao_financeira->label()); ?>

                            </span>
                            <?php if($contaReceber->data_vencimento && $contaReceber->data_vencimento->isPast() && $contaReceber->situacao_financeira->value == 'pendente'): ?>
                            <span class="badge bg-danger ms-2">Vencida</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-3">Cliente:</dt>
                        <dd class="col-sm-9">
                            <?php if($contaReceber->pessoa): ?>
                            <?php echo e($contaReceber->pessoa->nome); ?>

                            <?php if($contaReceber->pessoa->cpf_cnpj): ?>
                            <small class="text-muted">(<?php echo e($contaReceber->pessoa->cpf_cnpj); ?>)</small>
                            <?php endif; ?>
                            <?php else: ?>
                            <span class="text-muted">Não informado</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-3">Conta Gerencial:</dt>
                        <dd class="col-sm-9">
                            <?php if($contaReceber->contaGerencial): ?>
                            <?php echo e($contaReceber->contaGerencial->codigo); ?> - <?php echo e($contaReceber->contaGerencial->nome); ?>

                            <?php else: ?>
                            <span class="text-muted">Não informado</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-3">Número do Documento:</dt>
                        <dd class="col-sm-9">
                            <?php echo e($contaReceber->numero_documento ?: 'Não informado'); ?>

                        </dd>

                        <dt class="col-sm-3">Observações:</dt>
                        <dd class="col-sm-9">
                            <?php echo e($contaReceber->observacoes ?: 'Não informado'); ?>

                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Valores -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dollar-sign"></i> Valores Financeiros
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Valor Original:</dt>
                                <dd class="col-6">R$ <?php echo e(number_format($contaReceber->valor_liquido, 2, ',', '.')); ?></dd>

                                <?php if($contaReceber->valor_desconto > 0): ?>
                                <dt class="col-6">Desconto:</dt>
                                <dd class="col-6 text-success">- R$ <?php echo e(number_format($contaReceber->valor_desconto, 2, ',', '.')); ?></dd>
                                <?php endif; ?>

                                <?php if($contaReceber->valor_acrescimo > 0): ?>
                                <dt class="col-6">Acréscimo:</dt>
                                <dd class="col-6 text-warning">+ R$ <?php echo e(number_format($contaReceber->valor_acrescimo, 2, ',', '.')); ?></dd>
                                <?php endif; ?>

                                <?php if($contaReceber->valor_juros > 0): ?>
                                <dt class="col-6">Juros:</dt>
                                <dd class="col-6 text-warning">+ R$ <?php echo e(number_format($contaReceber->valor_juros, 2, ',', '.')); ?></dd>
                                <?php endif; ?>

                                <?php if($contaReceber->valor_multa > 0): ?>
                                <dt class="col-6">Multa:</dt>
                                <dd class="col-6 text-danger">+ R$ <?php echo e(number_format($contaReceber->valor_multa, 2, ',', '.')); ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6"><strong>Valor Final:</strong></dt>
                                <dd class="col-6"><strong class="fs-5 text-primary">R$ <?php echo e(number_format($contaReceber->valor_liquido, 2, ',', '.')); ?></strong></dd>

                                <?php if($contaReceber->situacao_financeira->value == 'recebido' && $contaReceber->data_pagamento): ?>
                                <dt class="col-6">Data do Recebimento:</dt>
                                <dd class="col-6"><?php echo e($contaReceber->data_pagamento->format('d/m/Y H:i')); ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Recebimentos -->
            <div class="card mb-4" id="historicoRecebimentos">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Histórico de Recebimentos
                    </h5>
                    <span class="badge bg-info"><?php echo e($resumoRecebimentos['total_recebimentos']); ?> recebimento(s)</span>
                </div>
                <div class="card-body">
                    <?php if($resumoRecebimentos['total_recebimentos'] > 0): ?>
                    <!-- Resumo Geral -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted mb-1">Valor Total</h6>
                                <h5 class="text-primary mb-0">R$ <?php echo e(number_format($resumoRecebimentos['valor_liquido'], 2, ',', '.')); ?></h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted mb-1">Total Recebido</h6>
                                <h5 class="text-success mb-0">R$ <?php echo e(number_format($resumoRecebimentos['valor_pago'], 2, ',', '.')); ?></h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted mb-1">Saldo Devedor</h6>
                                <h5 class="<?php echo e($resumoRecebimentos['saldo_devedor'] > 0 ? 'text-warning' : 'text-success'); ?> mb-0">
                                    R$ <?php echo e(number_format($resumoRecebimentos['saldo_devedor'], 2, ',', '.')); ?>

                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted mb-1">Percentual Recebido</h6>
                                <h5 class="text-info mb-0"><?php echo e(number_format($resumoRecebimentos['percentual_recebido'], 1)); ?>%</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de Progresso -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Progresso do Recebimento</small>
                            <small><?php echo e(number_format($resumoRecebimentos['percentual_recebido'], 1)); ?>%</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar <?php echo e($resumoRecebimentos['percentual_recebido'] >= 100 ? 'bg-success' : 'bg-primary'); ?>"
                                role="progressbar"
                                style="width: <?php echo e(min($resumoRecebimentos['percentual_recebido'], 100)); ?>%"
                                aria-valuenow="<?php echo e($resumoRecebimentos['percentual_recebido']); ?>"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Recebimentos -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaRecebimentos">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Forma de Pagamento</th>
                                    <th>Bandeira</th>
                                    <th>Conta Bancária</th>
                                    <th>Observações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recebimentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recebimento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($recebimento->data_pagamento->format('d/m/Y')); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e($recebimento->data_pagamento->format('H:i')); ?></small>
                                    </td>
                                    <td>
                                        <strong class="text-success">R$ <?php echo e(number_format($recebimento->valor, 2, ',', '.')); ?></strong>
                                        <?php if($recebimento->valor_principal != $recebimento->valor): ?>
                                        <br>
                                        <small class="text-muted">
                                            Principal: R$ <?php echo e(number_format($recebimento->valor_principal, 2, ',', '.')); ?>

                                            <?php if($recebimento->valor_juros > 0): ?>
                                            <br>Juros: R$ <?php echo e(number_format($recebimento->valor_juros, 2, ',', '.')); ?>

                                            <?php endif; ?>
                                            <?php if($recebimento->valor_multa > 0): ?>
                                            <br>Multa: R$ <?php echo e(number_format($recebimento->valor_multa, 2, ',', '.')); ?>

                                            <?php endif; ?>
                                            <?php if($recebimento->valor_desconto > 0): ?>
                                            <br>Desconto: R$ <?php echo e(number_format($recebimento->valor_desconto, 2, ',', '.')); ?>

                                            <?php endif; ?>
                                        </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($recebimento->formaPagamento): ?>
                                        <span class="badge bg-secondary"><?php echo e($recebimento->formaPagamento->nome); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($recebimento->bandeira): ?>
                                        <span class="badge bg-info"><?php echo e($recebimento->bandeira->nome); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($recebimento->contaBancaria): ?>
                                        <small><?php echo e($recebimento->contaBancaria->nome_banco ?? 'Conta ' . $recebimento->conta_bancaria_id); ?></small>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($recebimento->observacao): ?>
                                        <small><?php echo e(Str::limit($recebimento->observacao, 50)); ?></small>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                onclick="verDetalhesRecebimento(<?php echo e($recebimento->id); ?>)"
                                                title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if($contaReceber->situacao_financeira != 'pago'): ?>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="confirmarEstorno(<?php echo e($recebimento->id); ?>)"
                                                title="Estornar recebimento">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Nenhum recebimento registrado</h6>
                        <p class="text-muted mb-0">
                            <?php if($contaReceber->situacao_financeira->value == 'pendente'): ?>
                            Clique em "Registrar Recebimento" para registrar o primeiro pagamento.
                            <?php else: ?>
                            Esta conta não possui histórico de recebimentos.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Datas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt"></i> Datas Importantes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Data de Emissão:</dt>
                                <dd class="col-6">
                                    <?php if($contaReceber->data_emissao): ?>
                                    <?php echo e($contaReceber->data_emissao->format('d/m/Y')); ?>

                                    <?php else: ?>
                                    <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-6">Data de Competência:</dt>
                                <dd class="col-6">
                                    <?php if($contaReceber->data_competencia): ?>
                                    <?php echo e($contaReceber->data_competencia->format('d/m/Y')); ?>

                                    <?php else: ?>
                                    <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Data de Vencimento:</dt>
                                <dd class="col-6">
                                    <?php if($contaReceber->data_vencimento && $contaReceber->data_vencimento->isPast() && $contaReceber->situacao_financeira->value == 'pendente'): ?>
                                    <span class="text-danger fw-bold">
                                        <?php echo e($contaReceber->data_vencimento->format('d/m/Y')); ?>

                                        <small>(Vencida há <?php echo e($contaReceber->data_vencimento->diffForHumans()); ?>)</small>
                                    </span>
                                    <?php else: ?>
                                    <?php echo e($contaReceber->data_vencimento->format('d/m/Y')); ?>

                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($contaReceber->situacao_financeira->value == 'pendente'): ?>
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.recebimentos.pagamento', ['empresa' => $empresa, 'id' => $contaReceber->id])); ?>"
                            class="btn btn-success w-100">
                            <i class="fas fa-check"></i> Registrar Recebimento
                        </a>

                        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.edit', ['empresa' => $empresa, 'id' => $contaReceber->id])); ?>"
                            class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Dados
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        Esta conta já foi processada e não pode mais ser editada.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resumo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Resumo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary mb-1">
                            R$ <?php echo e(number_format($contaReceber->valor_liquido, 2, ',', '.')); ?>

                        </h3>
                        <p class="text-muted mb-3">Valor Total</p>

                        <?php if($contaReceber->situacao_financeira->value == 'pendente'): ?>
                        <?php if($contaReceber->data_vencimento->isFuture()): ?>
                        <p class="text-success mb-0">
                            <i class="fas fa-clock"></i>
                            Vence em <?php echo e($contaReceber->data_vencimento->diffForHumans()); ?>

                        </p>
                        <?php else: ?>
                        <p class="text-danger mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            Vencida há <?php echo e($contaReceber->data_vencimento->diffForHumans()); ?>

                        </p>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <?php if($contaReceber->data_vencimento && $contaReceber->data_vencimento->isPast() && $contaReceber->situacao_financeira->value == 'pendente'): ?>
            <div class="card border-danger mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Conta Vencida
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">Esta conta está vencida há <?php echo e($contaReceber->data_vencimento->diffForHumans()); ?>.</p>
                    <p class="mb-0 small text-muted">
                        Considere entrar em contato com o cliente para regularização.
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <?php if($contaReceber->data_vencimento && $contaReceber->data_vencimento->isToday() && $contaReceber->situacao_financeira->value == 'pendente'): ?>
            <div class="card border-warning mb-4">
                <div class="card-header bg-warning">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock"></i> Vence Hoje
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">Esta conta vence hoje. Monitore o recebimento.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // JavaScript m�nimo para p�gina de visualiza��o
    console.log(' P�gina de detalhes da conta a receber carregada');

    // Fun��o para recarregar a p�gina se necess�rio no futuro
    function recarregarPagina() {
        location.reload();
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas-receber/show.blade.php ENDPATH**/ ?>
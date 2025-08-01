<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações Fidelidade - Admin</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: color 0.3s ease;
        }
        .navbar-custom .nav-link:hover {
            color: white !important;
        }
        .navbar-custom .nav-link.active {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card.success {
            border-left-color: #28a745;
        }
        .stats-card.warning {
            border-left-color: #ffc107;
        }
        .stats-card.danger {
            border-left-color: #dc3545;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }
        .transaction-badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
        }
        .transaction-badge.entrada {
            background-color: #d4edda;
            color: #155724;
        }
        .transaction-badge.saida {
            background-color: #f8d7da;
            color: #721c24;
        }
        .valor-positivo {
            color: #28a745;
            font-weight: bold;
        }
        .valor-negativo {
            color: #dc3545;
            font-weight: bold;
        }
        .pagination-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar Superior do Admin Fidelidade -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('admin.fidelidade.index')); ?>">
                <i class="mdi mdi-chart-line"></i> Admin Fidelidade
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFidelidade">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarFidelidade">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.fidelidade.index')); ?>">
                            <i class="mdi mdi-view-dashboard"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.fidelidade.clientes')); ?>">
                            <i class="mdi mdi-account-group"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('admin.fidelidade.transacoes')); ?>">
                            <i class="mdi mdi-swap-horizontal"></i> Transações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.fidelidade.cupons')); ?>">
                            <i class="mdi mdi-ticket-percent"></i> Cupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.fidelidade.cashback')); ?>">
                            <i class="mdi mdi-cash-multiple"></i> Cashback
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.fidelidade.relatorios')); ?>">
                            <i class="mdi mdi-chart-box"></i> Relatórios
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="mdi mdi-arrow-left"></i> Voltar Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="mdi mdi-swap-horizontal text-primary"></i> Transações do Sistema
                        </h2>
                        <p class="text-muted mb-0">Visualização de todas as transações do programa de fidelidade</p>
                    </div>
                    <div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaTransacao">
                            <i class="mdi mdi-plus"></i> Nova Transação
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Transações</h6>
                            <h4 class="mb-0"><?php echo e($stats['total_transacoes'] ?? 0); ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-swap-horizontal text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Entradas</h6>
                            <h4 class="mb-0"><?php echo e($stats['transacoes_entrada'] ?? 0); ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-arrow-up text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Saídas</h6>
                            <h4 class="mb-0"><?php echo e($stats['transacoes_saida'] ?? 0); ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-arrow-down text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Valor Total</h6>
                            <h4 class="mb-0">R$ <?php echo e(number_format($stats['valor_total'] ?? 0, 2, ',', '.')); ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-currency-usd text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="table-container">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar transação...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">Todos os Tipos</option>
                        <option value="compra">Compra</option>
                        <option value="resgate">Resgate</option>
                        <option value="bonus">Bônus</option>
                        <option value="cashback">Cashback</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">Todos Status</option>
                        <option value="concluida">Concluída</option>
                        <option value="pendente">Pendente</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" placeholder="Data Inicial">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" placeholder="Data Final">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary">
                        <i class="mdi mdi-filter"></i>
                    </button>
                </div>
            </div>

            <!-- Tabela de Transações -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Valor/Pontos</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong>#<?php echo e($transacao->id); ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <i class="mdi mdi-account-circle text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <strong>Cliente <?php echo e($transacao->cliente_id); ?></strong>
                                        <br><small class="text-muted">ID: <?php echo e($transacao->cliente_id); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="transaction-badge <?php echo e($transacao->tipo_transacao == 'entrada' ? 'entrada' : 'saida'); ?>">
                                    <i class="mdi mdi-<?php echo e($transacao->tipo_transacao == 'entrada' ? 'arrow-up' : 'arrow-down'); ?>"></i>
                                    <?php echo e(ucfirst($transacao->tipo_transacao ?? 'Compra')); ?>

                                </span>
                            </td>
                            <td><?php echo e($transacao->descricao ?? 'Transação do sistema'); ?></td>
                            <td>
                                <?php if($transacao->tipo_transacao == 'entrada'): ?>
                                    <span class="valor-positivo">+<?php echo e($transacao->pontos ?? 0); ?> pts</span>
                                <?php else: ?>
                                    <span class="valor-negativo">-<?php echo e($transacao->pontos ?? 0); ?> pts</span>
                                <?php endif; ?>
                                <br><small class="text-muted">R$ <?php echo e(number_format($transacao->valor ?? 0, 2, ',', '.')); ?></small>
                            </td>
                            <td>
                                <?php if(($transacao->status ?? 'concluida') == 'concluida'): ?>
                                    <span class="badge bg-success">Concluída</span>
                                <?php elseif($transacao->status == 'pendente'): ?>
                                    <span class="badge bg-warning">Pendente</span>
                                <?php elseif($transacao->status == 'cancelada'): ?>
                                    <span class="badge bg-danger">Cancelada</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($transacao->status ?? 'Concluída')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><small><?php echo e(\Carbon\Carbon::parse($transacao->criado_em ?? now())->format('d/m/Y H:i')); ?></small></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir">
                                    <i class="mdi mdi-printer"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="mdi mdi-swap-horizontal text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">Nenhuma transação encontrada</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovaTransacao">
                                    <i class="mdi mdi-plus"></i> Registrar Primeira Transação
                                </button>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="row">
                <div class="col-md-6">
                    <div class="pagination-info">
                        <small class="text-muted">
                            Mostrando <span><?php echo e($transacoes->firstItem() ?? 0); ?></span> a <span><?php echo e($transacoes->lastItem() ?? 0); ?></span> de <span><?php echo e($transacoes->total() ?? 0); ?></span> registros
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <?php echo e($transacoes->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Transação -->
    <div class="modal fade" id="modalNovaTransacao" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Nova Transação
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information"></i>
                        Esta é uma página administrativa apenas para visualização. 
                        Para registrar transações, utilize o sistema operacional completo.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/fidelidade/transacoes.blade.php ENDPATH**/ ?>
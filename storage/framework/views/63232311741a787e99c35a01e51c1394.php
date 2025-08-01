<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashback Fidelidade - Admin</title>
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
        .cashback-badge {
            background: linear-gradient(135deg, #fd7e14 0%, #e67e22 100%);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: bold;
        }
        .rule-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        .rule-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
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
                        <a class="nav-link" href="<?php echo e(route('admin.fidelidade.transacoes')); ?>">
                            <i class="mdi mdi-swap-horizontal"></i> Transações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.fidelidade.cupons')); ?>">
                            <i class="mdi mdi-ticket-percent"></i> Cupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('admin.fidelidade.cashback')); ?>">
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
                            <i class="mdi mdi-cash-multiple text-primary"></i> Regras de Cashback
                        </h2>
                        <p class="text-muted mb-0">Configurações e regras do sistema de cashback</p>
                    </div>
                    <div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaRegra">
                            <i class="mdi mdi-plus"></i> Nova Regra
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
                            <h6 class="text-muted mb-1">Total de Regras</h6>
                            <h4 class="mb-0"><?php echo e($stats['total_regras'] ?? 0); ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-cog text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Regras Ativas</h6>
                            <h4 class="mb-0"><?php echo e($stats['regras_ativas'] ?? 0); ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cashback Pago</h6>
                            <h4 class="mb-0">R$ <?php echo e(number_format($stats['cashback_pago'] ?? 0, 2, ',', '.')); ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-cash text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Economia Total</h6>
                            <h4 class="mb-0">R$ <?php echo e(number_format($stats['economia_total'] ?? 0, 2, ',', '.')); ?></h4>
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
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar regra...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Todos os Tipos</option>
                        <option value="percentual">Percentual</option>
                        <option value="fixo">Valor Fixo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="nome">Ordenar por Nome</option>
                        <option value="valor">Ordenar por Valor</option>
                        <option value="data">Ordenar por Data</option>
                    </select>
                </div>
            </div>

            <!-- Lista de Regras -->
            <div class="row">
                <?php $__empty_1 = true; $__currentLoopData = $regras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-6 mb-3">
                    <div class="rule-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1"><?php echo e($regra->nome ?? 'Regra de Cashback'); ?></h5>
                                <p class="text-muted small mb-0"><?php echo e($regra->descricao ?? 'Regra padrão do sistema'); ?></p>
                            </div>
                            <div>
                                <?php if(($regra->status ?? 'ativo') == 'ativo'): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Inativo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="cashback-badge">
                                        <?php echo e($regra->valor_cashback ?? 5); ?><?php echo e(($regra->tipo_cashback ?? 'percentual') == 'percentual' ? '%' : ''); ?>

                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <?php if(($regra->tipo_cashback ?? 'percentual') == 'percentual'): ?>
                                            Percentual
                                        <?php else: ?>
                                            Valor Fixo
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong class="text-primary"><?php echo e($regra->empresa_nome ?? 'Geral'); ?></strong>
                                    <small class="text-muted d-block mt-1">Empresa</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Valor Mínimo:</small>
                                <br><strong>R$ <?php echo e(number_format($regra->valor_minimo ?? 0, 2, ',', '.')); ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Valor Máximo:</small>
                                <br><strong>R$ <?php echo e(number_format($regra->valor_maximo ?? 999999, 2, ',', '.')); ?></strong>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Criado em <?php echo e(\Carbon\Carbon::parse($regra->criado_em ?? now())->format('d/m/Y')); ?>

                            </small>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Desativar">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="mdi mdi-cash-multiple text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Nenhuma regra de cashback encontrada</h4>
                        <p class="text-muted">Configure as primeiras regras para o sistema de cashback</p>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaRegra">
                            <i class="mdi mdi-plus"></i> Criar Primeira Regra
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Paginação -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="pagination-info">
                        <small class="text-muted">
                            Mostrando <span><?php echo e($regras->firstItem() ?? 0); ?></span> a <span><?php echo e($regras->lastItem() ?? 0); ?></span> de <span><?php echo e($regras->total() ?? 0); ?></span> registros
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <?php echo e($regras->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Regra -->
    <div class="modal fade" id="modalNovaRegra" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Nova Regra de Cashback
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information"></i>
                        Esta é uma página administrativa apenas para visualização. 
                        Para configurar regras de cashback, utilize o sistema operacional completo.
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
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/fidelidade/cashback.blade.php ENDPATH**/ ?>
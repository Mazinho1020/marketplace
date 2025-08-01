<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Fidelidade - Admin</title>
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
        .relatorio-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .metric-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .metric-item:last-child {
            border-bottom: none;
        }
        .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }
        .periodo-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar Superior do Admin Fidelidade -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="mdi mdi-chart-line"></i> Admin Fidelidade - Relatórios
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo e(route('admin.fidelidade.index')); ?>">
                    <i class="mdi mdi-view-dashboard"></i> Dashboard
                </a>
                <a class="nav-link" href="<?php echo e(route('admin.fidelidade.clientes')); ?>">
                    <i class="mdi mdi-account-group"></i> Clientes
                </a>
                <a class="nav-link" href="<?php echo e(route('admin.fidelidade.transacoes')); ?>">
                    <i class="mdi mdi-swap-horizontal"></i> Transações
                </a>
                <a class="nav-link" href="<?php echo e(route('admin.fidelidade.cupons')); ?>">
                    <i class="mdi mdi-ticket-percent"></i> Cupons
                </a>
                <a class="nav-link active" href="<?php echo e(route('admin.fidelidade.relatorios')); ?>">
                    <i class="mdi mdi-chart-box"></i> Relatórios
                </a>
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
                            <i class="mdi mdi-chart-box text-primary"></i> Relatórios Administrativos
                        </h2>
                        <p class="text-muted mb-0">Análise detalhada do sistema de fidelidade</p>
                    </div>
                    <div>
                        <span class="periodo-badge">
                            <i class="mdi mdi-calendar"></i> <?php echo e($relatorio['periodo']['inicio']); ?> - <?php echo e($relatorio['periodo']['fim']); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas do Período -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Transações no Período</h6>
                            <h3 class="mb-0 text-primary"><?php echo e(number_format($relatorio['transacoes_periodo'], 0, ',', '.')); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-swap-horizontal text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Valor Total</h6>
                            <h3 class="mb-0 text-success">R$ <?php echo e(number_format($relatorio['valor_periodo'], 2, ',', '.')); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-currency-usd text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cashback Distribuído</h6>
                            <h3 class="mb-0 text-info">R$ <?php echo e(number_format($relatorio['cashback_periodo'], 2, ',', '.')); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-cash-multiple text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Novos Clientes</h6>
                            <h3 class="mb-0 text-warning"><?php echo e(number_format($relatorio['novos_clientes'], 0, ',', '.')); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-plus text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relatório Detalhado -->
        <div class="row">
            <div class="col-md-6">
                <div class="relatorio-card">
                    <h5 class="mb-4">
                        <i class="mdi mdi-chart-line text-primary"></i> Análise de Performance
                    </h5>
                    
                    <div class="metric-item">
                        <div>
                            <strong>Taxa de Conversão</strong>
                            <br><small class="text-muted">Transações vs Novos Clientes</small>
                        </div>
                        <div class="metric-value">
                            <?php echo e($relatorio['novos_clientes'] > 0 ? number_format(($relatorio['transacoes_periodo'] / $relatorio['novos_clientes']) * 100, 1) : 0); ?>%
                        </div>
                    </div>

                    <div class="metric-item">
                        <div>
                            <strong>Ticket Médio</strong>
                            <br><small class="text-muted">Valor médio por transação</small>
                        </div>
                        <div class="metric-value">
                            R$ <?php echo e($relatorio['transacoes_periodo'] > 0 ? number_format($relatorio['valor_periodo'] / $relatorio['transacoes_periodo'], 2, ',', '.') : '0,00'); ?>

                        </div>
                    </div>

                    <div class="metric-item">
                        <div>
                            <strong>Cashback Médio</strong>
                            <br><small class="text-muted">Cashback por transação</small>
                        </div>
                        <div class="metric-value">
                            R$ <?php echo e($relatorio['transacoes_periodo'] > 0 ? number_format($relatorio['cashback_periodo'] / $relatorio['transacoes_periodo'], 2, ',', '.') : '0,00'); ?>

                        </div>
                    </div>

                    <div class="metric-item">
                        <div>
                            <strong>% Cashback</strong>
                            <br><small class="text-muted">Cashback sobre valor total</small>
                        </div>
                        <div class="metric-value">
                            <?php echo e($relatorio['valor_periodo'] > 0 ? number_format(($relatorio['cashback_periodo'] / $relatorio['valor_periodo']) * 100, 2) : 0); ?>%
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="relatorio-card">
                    <h5 class="mb-4">
                        <i class="mdi mdi-ticket-percent text-success"></i> Análise de Cupons
                    </h5>
                    
                    <div class="metric-item">
                        <div>
                            <strong>Cupons Utilizados</strong>
                            <br><small class="text-muted">Total no período</small>
                        </div>
                        <div class="metric-value">
                            <?php echo e(number_format($relatorio['cupons_utilizados'], 0, ',', '.')); ?>

                        </div>
                    </div>

                    <div class="metric-item">
                        <div>
                            <strong>Taxa de Uso</strong>
                            <br><small class="text-muted">Cupons vs Transações</small>
                        </div>
                        <div class="metric-value">
                            <?php echo e($relatorio['transacoes_periodo'] > 0 ? number_format(($relatorio['cupons_utilizados'] / $relatorio['transacoes_periodo']) * 100, 1) : 0); ?>%
                        </div>
                    </div>

                    <div class="metric-item">
                        <div>
                            <strong>Eficiência</strong>
                            <br><small class="text-muted">Cupons por novo cliente</small>
                        </div>
                        <div class="metric-value">
                            <?php echo e($relatorio['novos_clientes'] > 0 ? number_format($relatorio['cupons_utilizados'] / $relatorio['novos_clientes'], 1) : 0); ?>

                        </div>
                    </div>

                    <div class="metric-item">
                        <div>
                            <strong>Impacto</strong>
                            <br><small class="text-muted">Estimativa de economia</small>
                        </div>
                        <div class="metric-value text-success">
                            <i class="mdi mdi-trending-up"></i> Positivo
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico Placeholder -->
        <div class="row">
            <div class="col-12">
                <div class="relatorio-card">
                    <h5 class="mb-4">
                        <i class="mdi mdi-chart-areaspline text-info"></i> Tendências (Últimos 30 Dias)
                    </h5>
                    <div class="text-center py-5">
                        <i class="mdi mdi-chart-line text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3">Gráfico de tendências será implementado em breve</p>
                        <small class="text-muted">Exibirá evolução de transações, cadastros e uso de cupons</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/fidelidade/relatorios.blade.php ENDPATH**/ ?>
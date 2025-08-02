<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Sistema de Pagamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-width: 800px;
            width: 100%;
        }

        .logo {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 30px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .menu-item {
            background: white;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .menu-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
            color: #667eea;
        }

        .menu-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }

        .menu-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .menu-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .stats-row {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin: 30px 0;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .system-status {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #28a745;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .quick-links {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
        }

        .quick-links h5 {
            color: white;
            margin-bottom: 15px;
            text-align: center;
        }

        .quick-link {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 8px;
            display: inline-block;
            margin: 5px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .quick-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
        }

        .laravel-option {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .laravel-btn {
            background: rgba(255, 255, 255, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .laravel-btn:hover {
            background: rgba(255, 255, 255, 0.4);
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="container">
            <div class="welcome-card">
                <!-- Laravel Option -->
                <div class="laravel-option">
                    <p class="text-white mb-2">
                        <i class="fas fa-rocket me-2"></i>
                        <strong>Acessar via Laravel Framework</strong>
                    </p>
                    <a href="public/index.php" class="laravel-btn">
                        <i class="fab fa-laravel me-2"></i>
                        Entrar no Sistema Laravel
                    </a>
                </div>

                <!-- Header -->
                <div class="text-center">
                    <div class="logo">
                        <i class="fas fa-store"></i>
                        Marketplace
                    </div>
                    <h2 class="mb-3">Sistema de Pagamentos</h2>
                    <div class="system-status">
                        <div class="status-indicator"></div>
                        <span class="text-success fw-bold">Sistema Online</span>
                    </div>
                    <p class="text-muted">Plataforma completa para gerenciamento de pagamentos, merchants e afiliados</p>
                </div>

                <!-- Menu Principal -->
                <div class="menu-grid">
                    <a href="public/index.php#/admin" class="menu-item">
                        <i class="fas fa-tachometer-alt menu-icon text-primary"></i>
                        <div class="menu-title">Dashboard Admin</div>
                        <div class="menu-description">
                            Painel principal com visão geral do sistema, KPIs e estatísticas em tempo real
                        </div>
                    </a>

                    <a href="public/index.php#/admin/merchants" class="menu-item">
                        <i class="fas fa-store menu-icon text-success"></i>
                        <div class="menu-title">Merchants</div>
                        <div class="menu-description">
                            Gestão completa de merchants, assinaturas e análise de uso da plataforma
                        </div>
                    </a>

                    <a href="public/index.php#/admin/payments" class="menu-item">
                        <i class="fas fa-credit-card menu-icon text-info"></i>
                        <div class="menu-title">Pagamentos</div>
                        <div class="menu-description">
                            Transações, gateways de pagamento e análise de performance financeira
                        </div>
                    </a>

                    <a href="public/index.php#/admin/affiliates" class="menu-item">
                        <i class="fas fa-share-alt menu-icon text-warning"></i>
                        <div class="menu-title">Afiliados</div>
                        <div class="menu-description">
                            Programa de afiliados, comissões, referrals e análise de conversão
                        </div>
                    </a>

                    <a href="public/index.php#/admin/subscriptions" class="menu-item">
                        <i class="fas fa-calendar-alt menu-icon text-purple"></i>
                        <div class="menu-title">Assinaturas</div>
                        <div class="menu-description">
                            Planos, renovações, análise de churn e métricas de retenção
                        </div>
                    </a>

                    <a href="public/index.php#/admin/reports" class="menu-item">
                        <i class="fas fa-chart-bar menu-icon text-danger"></i>
                        <div class="menu-title">Relatórios</div>
                        <div class="menu-description">
                            Centro de relatórios com analytics avançados e exportação de dados
                        </div>
                    </a>
                </div>

                <!-- Quick Links -->
                <div class="quick-links">
                    <h5><i class="fas fa-bolt me-2"></i>Acesso Rápido</h5>
                    <div class="text-center">
                        <a href="public/index.php#/admin/merchants/create" class="quick-link">
                            <i class="fas fa-plus me-1"></i> Novo Merchant
                        </a>
                        <a href="public/index.php#/admin/reports/revenue" class="quick-link">
                            <i class="fas fa-chart-line me-1"></i> Relatório Receita
                        </a>
                        <a href="public/index.php#/admin/payments/analytics" class="quick-link">
                            <i class="fas fa-analytics me-1"></i> Analytics
                        </a>
                        <a href="public/index.php#/admin/affiliates/top-performers" class="quick-link">
                            <i class="fas fa-trophy me-1"></i> Top Afiliados
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Stats -->
            <div class="stats-row mt-4">
                <div class="row">
                    <div class="col-md-3 stat-item">
                        <span class="stat-number" id="totalMerchants">--</span>
                        <span class="stat-label">Merchants Ativos</span>
                    </div>
                    <div class="col-md-3 stat-item">
                        <span class="stat-number" id="monthlyRevenue">--</span>
                        <span class="stat-label">Receita Mensal</span>
                    </div>
                    <div class="col-md-3 stat-item">
                        <span class="stat-number" id="totalTransactions">--</span>
                        <span class="stat-label">Transações (30d)</span>
                    </div>
                    <div class="col-md-3 stat-item">
                        <span class="stat-number" id="activeAffiliates">--</span>
                        <span class="stat-label">Afiliados Ativos</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-white mt-4 pb-4">
        <small>
            <i class="fas fa-shield-alt me-1"></i>
            Sistema Seguro |
            <i class="fas fa-clock me-1"></i>
            Última atualização: <?php echo date('d/m/Y H:i'); ?> |
            <i class="fas fa-server me-1"></i>
            PHP <?php echo phpversion(); ?>
        </small>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulate loading stats
        document.addEventListener('DOMContentLoaded', function() {
            // Simulate API calls to get real-time stats
            setTimeout(() => {
                document.getElementById('totalMerchants').textContent = '248';
                document.getElementById('monthlyRevenue').textContent = 'R$ 156.2K';
                document.getElementById('totalTransactions').textContent = '1.8K';
                document.getElementById('activeAffiliates').textContent = '89';
            }, 1000);

            // Add hover effects to menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });

        // Check system status
        function checkSystemStatus() {
            // Simulate system health check
            const indicator = document.querySelector('.status-indicator');
            const laravelExists = <?php echo file_exists('public/index.php') ? 'true' : 'false'; ?>;

            if (laravelExists) {
                indicator.style.background = '#28a745';
            } else {
                indicator.style.background = '#dc3545';
            }
        }

        // Check status every 30 seconds
        setInterval(checkSystemStatus, 30000);

        // Initial status check
        checkSystemStatus();
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal - Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
        }

        .header-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .logo-title {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .menu-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .menu-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .menu-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .menu-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
        }

        .menu-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .menu-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .menu-link {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .menu-link:hover {
            background: #5a6fd8;
            color: white;
            transform: translateY(-2px);
        }

        .status-bar {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .quick-access {
            margin-top: 30px;
            text-align: center;
        }

        .quick-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .quick-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="container-custom">
        <!-- Header -->
        <div class="header-section">
            <div class="logo-title">
                <i class="fas fa-store"></i> Marketplace
            </div>
            <h2>Sistema de Pagamentos Integrado</h2>
            <p class="lead text-muted">Escolha o módulo que deseja acessar</p>
        </div>

        <!-- Status -->
        <div class="status-bar">
            <strong><i class="fas fa-check-circle me-2"></i>Sistema Online e Funcionando</strong>
            <span class="ms-3">Laravel Server: http://127.0.0.1:8000</span>
        </div>

        <!-- Menu Principal -->
        <div class="menu-section">
            <h3 class="text-center mb-4">Módulos do Sistema</h3>

            <div class="menu-grid">
                <!-- Dashboard Admin -->
                <div class="menu-card" onclick="window.open('http://127.0.0.1:8000/admin', '_blank')">
                    <i class="fas fa-tachometer-alt menu-icon text-primary"></i>
                    <div class="menu-title">Dashboard Admin</div>
                    <div class="menu-description">
                        Painel principal com visão geral completa do sistema, KPIs, estatísticas e métricas em tempo real
                    </div>
                    <a href="http://127.0.0.1:8000/admin" target="_blank" class="menu-link">
                        <i class="fas fa-external-link-alt me-2"></i>Acessar Dashboard
                    </a>
                </div>

                <!-- Merchants -->
                <div class="menu-card" onclick="window.open('http://127.0.0.1:8000/admin/merchants', '_blank')">
                    <i class="fas fa-store menu-icon text-success"></i>
                    <div class="menu-title">Gestão de Merchants</div>
                    <div class="menu-description">
                        Controle completo de merchants, assinaturas, planos, análise de uso e performance da plataforma
                    </div>
                    <a href="http://127.0.0.1:8000/admin/merchants" target="_blank" class="menu-link">
                        <i class="fas fa-external-link-alt me-2"></i>Gerenciar Merchants
                    </a>
                </div>

                <!-- Pagamentos -->
                <div class="menu-card" onclick="window.open('http://127.0.0.1:8000/admin/payments', '_blank')">
                    <i class="fas fa-credit-card menu-icon text-info"></i>
                    <div class="menu-title">Sistema de Pagamentos</div>
                    <div class="menu-description">
                        Transações, gateways de pagamento, análise de performance financeira e controle de falhas
                    </div>
                    <a href="http://127.0.0.1:8000/admin/payments" target="_blank" class="menu-link">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Pagamentos
                    </a>
                </div>

                <!-- Afiliados -->
                <div class="menu-card" onclick="window.open('http://127.0.0.1:8000/admin/affiliates', '_blank')">
                    <i class="fas fa-share-alt menu-icon text-warning"></i>
                    <div class="menu-title">Programa de Afiliados</div>
                    <div class="menu-description">
                        Gestão completa de afiliados, comissões, referrals, análise de conversão e ROI do programa
                    </div>
                    <a href="http://127.0.0.1:8000/admin/affiliates" target="_blank" class="menu-link">
                        <i class="fas fa-external-link-alt me-2"></i>Gerenciar Afiliados
                    </a>
                </div>

                <!-- Assinaturas -->
                <div class="menu-card" onclick="window.open('http://127.0.0.1:8000/admin/subscriptions', '_blank')">
                    <i class="fas fa-calendar-alt menu-icon text-purple"></i>
                    <div class="menu-title">Assinaturas e Planos</div>
                    <div class="menu-description">
                        Controle de planos, renovações, análise de churn, métricas de retenção e lifecycle dos clientes
                    </div>
                    <a href="http://127.0.0.1:8000/admin/subscriptions" target="_blank" class="menu-link">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Assinaturas
                    </a>
                </div>

                <!-- Relatórios -->
                <div class="menu-card" onclick="window.open('http://127.0.0.1:8000/admin/reports', '_blank')">
                    <i class="fas fa-chart-bar menu-icon text-danger"></i>
                    <div class="menu-title">Centro de Relatórios</div>
                    <div class="menu-description">
                        Analytics avançados, relatórios customizados, exportação de dados e insights de negócio
                    </div>
                    <a href="http://127.0.0.1:8000/admin/reports" target="_blank" class="menu-link">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Relatórios
                    </a>
                </div>
            </div>

            <!-- Acesso Rápido -->
            <div class="quick-access">
                <h5 class="text-white mb-3">
                    <i class="fas fa-bolt me-2"></i>Links de Acesso Rápido
                </h5>
                <a href="http://127.0.0.1:8000/admin/merchants/create" target="_blank" class="quick-btn">
                    <i class="fas fa-plus me-1"></i> Novo Merchant
                </a>
                <a href="http://127.0.0.1:8000/admin/reports/revenue" target="_blank" class="quick-btn">
                    <i class="fas fa-chart-line me-1"></i> Relatório de Receita
                </a>
                <a href="http://127.0.0.1:8000/admin/payments/analytics" target="_blank" class="quick-btn">
                    <i class="fas fa-analytics me-1"></i> Analytics de Pagamentos
                </a>
                <a href="http://127.0.0.1:8000/admin/affiliates/top-performers" target="_blank" class="quick-btn">
                    <i class="fas fa-trophy me-1"></i> Top Afiliados
                </a>
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="text-center text-white mt-4 pb-4">
        <div class="mb-3">
            <strong>Informações do Sistema:</strong>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-3">
                <i class="fas fa-server me-1"></i>
                Servidor: Laravel
            </div>
            <div class="col-md-3">
                <i class="fas fa-code me-1"></i>
                PHP: <?php echo phpversion(); ?>
            </div>
            <div class="col-md-3">
                <i class="fas fa-clock me-1"></i>
                Online desde: <?php echo date('H:i:s'); ?>
            </div>
            <div class="col-md-3">
                <i class="fas fa-shield-alt me-1"></i>
                Sistema Seguro
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Verificar se o servidor Laravel está rodando
        function checkLaravelServer() {
            fetch('http://127.0.0.1:8000/admin')
                .then(response => {
                    if (response.ok) {
                        document.querySelector('.status-bar').innerHTML =
                            '<strong><i class="fas fa-check-circle me-2"></i>Sistema Online e Funcionando</strong>' +
                            '<span class="ms-3">Laravel Server: Conectado (Port 8000)</span>';
                    }
                })
                .catch(error => {
                    document.querySelector('.status-bar').innerHTML =
                        '<strong style="color: #dc3545;"><i class="fas fa-exclamation-triangle me-2"></i>Atenção: Verifique se o Laravel está rodando</strong>' +
                        '<span class="ms-3">Execute: php artisan serve</span>';
                    document.querySelector('.status-bar').style.background = 'rgba(220, 53, 69, 0.1)';
                    document.querySelector('.status-bar').style.borderColor = 'rgba(220, 53, 69, 0.3)';
                    document.querySelector('.status-bar').style.color = '#dc3545';
                });
        }

        // Verificar status ao carregar a página
        checkLaravelServer();

        // Verificar status a cada 30 segundos
        setInterval(checkLaravelServer, 30000);

        // Adicionar efeitos hover aos cards
        document.querySelectorAll('.menu-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>

</html>
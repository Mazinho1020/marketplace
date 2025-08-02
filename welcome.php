<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Página Inicial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-width: 900px;
            width: 100%;
            text-align: center;
        }

        .logo {
            font-size: 4rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }

        .access-buttons {
            margin-top: 40px;
        }

        .access-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .access-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .features {
            margin-top: 50px;
            text-align: left;
        }

        .feature-item {
            padding: 20px;
            margin: 15px 0;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            border-left: 5px solid #667eea;
        }

        .feature-icon {
            color: #667eea;
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .status-bar {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 30px;
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #28a745;
            border-radius: 50%;
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
    </style>
</head>

<body>
    <div class="welcome-container">
        <div class="welcome-card">
            <!-- Status Bar -->
            <div class="status-bar">
                <div class="status-indicator"></div>
                <strong class="text-success">Sistema Online</strong>
                <span class="text-muted ms-3">Última verificação: <?php echo date('H:i:s'); ?></span>
            </div>

            <!-- Logo e Título -->
            <div class="logo">
                <i class="fas fa-store"></i> Marketplace
            </div>
            <h2 class="mb-4">Sistema de Pagamentos Integrado</h2>
            <p class="lead text-muted mb-4">
                Plataforma completa para gerenciamento de pagamentos, merchants, afiliados e relatórios em tempo real.
            </p>

            <!-- Botões de Acesso -->
            <div class="access-buttons">
                <a href="/admin" class="access-btn">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Acessar Dashboard Admin
                </a>
                <a href="/admin/merchants" class="access-btn">
                    <i class="fas fa-store me-2"></i>
                    Gerenciar Merchants
                </a>
            </div>

            <!-- Funcionalidades -->
            <div class="features">
                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-item">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <strong>Dashboard Analytics</strong>
                            <p class="mb-0 text-muted">KPIs em tempo real, gráficos interativos e métricas de performance</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <i class="fas fa-users feature-icon"></i>
                            <strong>Gestão de Merchants</strong>
                            <p class="mb-0 text-muted">Controle completo de merchants, planos e assinaturas</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <i class="fas fa-credit-card feature-icon"></i>
                            <strong>Processamento de Pagamentos</strong>
                            <p class="mb-0 text-muted">Múltiplos gateways, análise de transações e controle de falhas</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <i class="fas fa-share-alt feature-icon"></i>
                            <strong>Sistema de Afiliados</strong>
                            <p class="mb-0 text-muted">Programa completo com comissões, referrals e tracking</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links Rápidos -->
            <div class="mt-4">
                <h5>Acesso Rápido:</h5>
                <div class="d-flex justify-content-center flex-wrap">
                    <a href="/admin/reports" class="btn btn-outline-primary btn-sm m-1">
                        <i class="fas fa-chart-bar me-1"></i>Relatórios
                    </a>
                    <a href="/admin/payments/analytics" class="btn btn-outline-primary btn-sm m-1">
                        <i class="fas fa-analytics me-1"></i>Analytics
                    </a>
                    <a href="/admin/affiliates/top-performers" class="btn btn-outline-primary btn-sm m-1">
                        <i class="fas fa-trophy me-1"></i>Top Afiliados
                    </a>
                    <a href="/admin/subscriptions/analytics" class="btn btn-outline-primary btn-sm m-1">
                        <i class="fas fa-calendar-check me-1"></i>Assinaturas
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-5 pt-4 border-top">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>Sistema Seguro |
                    <i class="fas fa-server me-1"></i>PHP <?php echo phpversion(); ?> |
                    <i class="fas fa-database me-1"></i>Laravel Framework
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Verificação de status do sistema a cada 30 segundos
        setInterval(function() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            document.querySelector('.status-bar span').textContent = `Última verificação: ${timeString}`;
        }, 30000);

        // Efeito hover nos botões
        document.querySelectorAll('.access-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.05)';
            });

            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>

</html>
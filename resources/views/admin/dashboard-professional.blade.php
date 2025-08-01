<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - MeuFinanceiro</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .main-content {
            background: rgba(255,255,255,0.95);
            min-height: calc(100vh - 76px);
            border-radius: 20px 20px 0 0;
            margin-top: 1rem;
            padding: 2rem;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .stat-card:hover::before {
            top: -30%;
            right: -30%;
        }
        .stat-card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card-warning {
            background: linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%);
        }
        .stat-card-info {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        .stat-card-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .stat-number {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        .stat-label {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 2.5rem;
            opacity: 0.3;
            z-index: 0;
        }
        .user-info {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .section-title {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 0.5rem;
            color: #667eea;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .alert-item {
            border-left: 4px solid;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
        }
        .alert-warning {
            border-left-color: #f59e0b;
        }
        .alert-info {
            border-left-color: #3b82f6;
        }
        .alert-danger {
            border-left-color: #ef4444;
        }
        .quick-action {
            transition: all 0.3s ease;
        }
        .quick-action:hover {
            transform: scale(1.05);
        }
        .activity-item {
            border-left: 3px solid #667eea;
            padding-left: 1rem;
            margin-bottom: 1rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 0 10px 10px 0;
        }
        .fidelidade-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .fidelidade-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .progress-custom {
            height: 8px;
            border-radius: 10px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="mdi mdi-shield-crown me-2" style="font-size: 1.8rem;"></i>
                <span style="background: linear-gradient(45deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700;">MeuFinanceiro Admin</span>
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="mdi mdi-account text-white"></i>
                        </div>
                        <span class="fw-medium">{{ $user->nome ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 10px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <li><h6 class="dropdown-header">Nível: {{ $user->tipo_nome ?? 'Administrador' }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-account-circle me-2"></i>Perfil</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.config.index') }}"><i class="mdi mdi-cog me-2"></i>Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/logout"><i class="mdi mdi-logout me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="main-content">
            
            <!-- Header com informações do usuário -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card user-info">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2">Bem-vindo, {{ $user->nome ?? 'Administrador' }}!</h3>
                                    <p class="mb-0 opacity-75">
                                        <i class="mdi mdi-shield-account me-2"></i>{{ $user->tipo_nome ?? 'Administrador' }} - {{ $user->empresa ?? 'Sistema' }}
                                    </p>
                                    <small class="opacity-75">
                                        <i class="mdi mdi-clock-outline me-1"></i>Último acesso: {{ now()->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="d-flex justify-content-end">
                                        <div class="text-center me-4">
                                            <div class="h4 mb-0">{{ date('d') }}</div>
                                            <small>{{ date('M/Y') }}</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="h4 mb-0">{{ date('H:i') }}</div>
                                            <small>{{ date('D') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas do Sistema -->
            <h4 class="section-title">
                <i class="mdi mdi-chart-box"></i>Visão Geral do Sistema
            </h4>
            
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="mdi mdi-account-group stat-icon"></i>
                            <h4 class="stat-number">{{ $systemStats['total_usuarios'] ?? 0 }}</h4>
                            <p class="stat-label">Total de Usuários</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card stat-card stat-card-success">
                        <div class="card-body">
                            <i class="mdi mdi-office-building stat-icon"></i>
                            <h4 class="stat-number">{{ $systemStats['total_empresas'] ?? 0 }}</h4>
                            <p class="stat-label">Empresas Ativas</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card stat-card stat-card-info">
                        <div class="card-body">
                            <i class="mdi mdi-cart stat-icon"></i>
                            <h4 class="stat-number">{{ $systemStats['total_pedidos'] ?? 0 }}</h4>
                            <p class="stat-label">Pedidos Realizados</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card stat-card stat-card-warning">
                        <div class="card-body">
                            <i class="mdi mdi-package-variant stat-icon"></i>
                            <h4 class="stat-number">{{ $systemStats['total_produtos'] ?? 0 }}</h4>
                            <p class="stat-label">Produtos Cadastrados</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Banco de Dados -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="section-title">
                        <i class="mdi mdi-database"></i>Informações do Banco de Dados
                    </h4>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-server me-2"></i>Detalhes da Conexão
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status da Conexão:</label>
                                        <span class="badge bg-{{ $databaseInfo['status_class'] ?? 'secondary' }} ms-2">
                                            <i class="mdi mdi-circle me-1"></i>{{ $databaseInfo['status'] ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Driver:</label>
                                        <span class="ms-2">{{ $databaseInfo['driver'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Host:</label>
                                        <span class="ms-2">{{ $databaseInfo['host'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Porta:</label>
                                        <span class="ms-2">{{ $databaseInfo['port'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nome do Banco:</label>
                                        <span class="ms-2 text-primary fw-medium">{{ $databaseInfo['database'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Charset:</label>
                                        <span class="ms-2">{{ $databaseInfo['charset'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Versão:</label>
                                        <span class="ms-2">{{ $databaseInfo['version'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Conexão:</label>
                                        <span class="ms-2">{{ $databaseInfo['connection_name'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-chart-pie me-2"></i>Estatísticas do Banco
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <div class="stat-number text-primary" style="font-size: 2.5rem;">{{ $databaseInfo['total_tables'] ?? 0 }}</div>
                                <p class="stat-label text-muted">Total de Tabelas</p>
                            </div>
                            <div class="mb-3">
                                <div class="h4 text-success">{{ $databaseInfo['size'] ?? 'N/A' }}</div>
                                <p class="text-muted mb-0">Tamanho do Banco</p>
                            </div>
                            @if(isset($databaseInfo['error']))
                            <div class="alert alert-warning alert-sm mt-3">
                                <small><i class="mdi mdi-alert me-1"></i>{{ $databaseInfo['error'] }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Sistema de Fidelidade -->
            <div class="fidelidade-section">
                <h4 class="mb-3">
                    <i class="mdi mdi-star-circle me-2"></i>Sistema de Fidelidade
                </h4>
                
                <div class="row">
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <div class="card fidelidade-card">
                            <div class="card-body text-center">
                                <i class="mdi mdi-trophy-variant text-warning mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ $fidelidadeStats['total_programas'] ?? 0 }}</h5>
                                <small>Programas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <div class="card fidelidade-card">
                            <div class="card-body text-center">
                                <i class="mdi mdi-account-heart text-info mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ $fidelidadeStats['total_clientes'] ?? 0 }}</h5>
                                <small>Clientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <div class="card fidelidade-card">
                            <div class="card-body text-center">
                                <i class="mdi mdi-credit-card-multiple text-success mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ $fidelidadeStats['total_cartoes'] ?? 0 }}</h5>
                                <small>Cartões</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <div class="card fidelidade-card">
                            <div class="card-body text-center">
                                <i class="mdi mdi-swap-horizontal text-primary mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ $fidelidadeStats['total_transacoes'] ?? 0 }}</h5>
                                <small>Transações</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <div class="card fidelidade-card">
                            <div class="card-body text-center">
                                <i class="mdi mdi-star text-warning mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">{{ number_format($fidelidadeStats['total_pontos'] ?? 0, 0, ',', '.') }}</h5>
                                <small>Pontos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <div class="card fidelidade-card">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash text-success mb-2" style="font-size: 2rem;"></i>
                                <h5 class="mb-1">R$ {{ number_format($fidelidadeStats['total_cashback'] ?? 0, 2, ',', '.') }}</h5>
                                <small>Cashback</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <a href="/admin/fidelidade/programas" class="btn btn-light btn-sm me-2 quick-action">
                            <i class="mdi mdi-trophy me-1"></i>Gerenciar Programas
                        </a>
                        <a href="/admin/fidelidade/clientes" class="btn btn-light btn-sm me-2 quick-action">
                            <i class="mdi mdi-account-group me-1"></i>Gerenciar Clientes
                        </a>
                        <a href="/admin/fidelidade/transacoes" class="btn btn-light btn-sm quick-action">
                            <i class="mdi mdi-history me-1"></i>Histórico
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gráficos e Análises -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-chart-line me-2"></i>Tendências do Sistema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="trendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-package-variant me-2"></i>Top Produtos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas e Notificações -->
            @if(isset($systemAlerts) && count($systemAlerts) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="section-title">
                        <i class="mdi mdi-alert-circle"></i>Alertas do Sistema
                    </h4>
                    
                    <div class="row">
                        @foreach($systemAlerts as $alert)
                        <div class="col-md-4 mb-3">
                            <div class="alert alert-item alert-{{ $alert['type'] ?? 'info' }} mb-0">
                                <h6 class="alert-heading">
                                    <i class="mdi mdi-{{ $alert['icon'] ?? 'information' }} me-2"></i>
                                    {{ $alert['title'] ?? 'Alerta' }}
                                </h6>
                                <p class="mb-0">{{ $alert['message'] ?? 'Mensagem do alerta' }}</p>
                                @if(isset($alert['count']))
                                <small class="text-muted">Total: {{ $alert['count'] }}</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Ações Rápidas e Atividades Recentes -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-lightning-bolt me-2"></i>Ações Rápidas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="/admin/fidelidade/programas/create" class="btn btn-outline-primary w-100 quick-action">
                                        <i class="mdi mdi-plus-circle me-2"></i>
                                        <div>Novo Programa</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="/admin/fidelidade/clientes/create" class="btn btn-outline-success w-100 quick-action">
                                        <i class="mdi mdi-account-plus me-2"></i>
                                        <div>Novo Cliente</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="/admin/fidelidade/cashback" class="btn btn-outline-warning w-100 quick-action">
                                        <i class="mdi mdi-cash me-2"></i>
                                        <div>Cashback</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="/admin/fidelidade/cupons" class="btn btn-outline-info w-100 quick-action">
                                        <i class="mdi mdi-ticket me-2"></i>
                                        <div>Cupons</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-history me-2"></i>Atividades Recentes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Sistema de Fidelidade Atualizado</h6>
                                        <p class="mb-0 text-muted small">Novas funcionalidades implementadas</p>
                                    </div>
                                    <small class="text-muted">Hoje</small>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Dashboard Profissional</h6>
                                        <p class="mb-0 text-muted small">Interface modernizada com dados em tempo real</p>
                                    </div>
                                    <small class="text-muted">Hoje</small>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Integração de Dados</h6>
                                        <p class="mb-0 text-muted small">Todas as tabelas do sistema integradas</p>
                                    </div>
                                    <small class="text-muted">Hoje</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configuração dos gráficos
        const ctx1 = document.getElementById('trendsChart').getContext('2d');
        const trendsChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: @json(array_column($chartData['usuarios_por_mes'] ?? [], 'mes')),
                datasets: [{
                    label: 'Usuários',
                    data: @json(array_column($chartData['usuarios_por_mes'] ?? [], 'count')),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Vendas (R$)',
                    data: @json(array_column($chartData['vendas_por_mes'] ?? [], 'valor')),
                    borderColor: '#38ef7d',
                    backgroundColor: 'rgba(56, 239, 125, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        const ctx2 = document.getElementById('topProductsChart').getContext('2d');
        const topProductsChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: @json(array_column($chartData['top_produtos'] ?? [], 'nome')),
                datasets: [{
                    data: @json(array_column($chartData['top_produtos'] ?? [], 'vendas')),
                    backgroundColor: [
                        '#667eea',
                        '#38ef7d',
                        '#f093fb',
                        '#4facfe',
                        '#ee9ca7'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Atualização em tempo real (simulada)
        setInterval(function() {
            const currentTime = new Date().toLocaleTimeString('pt-BR');
            const timeElements = document.querySelectorAll('.current-time');
            timeElements.forEach(el => el.textContent = currentTime);
        }, 1000);
    </script>

</body>
</html>

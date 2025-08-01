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
    </style>
</head>
<body>
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .activity-item {
            border-left: 3px solid #667eea;
            padding-left: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
        .activity-item:last-child {
            border-left-color: #e9ecef;
            margin-bottom: 0;
        }
        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .badge-access-level {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">
                🏢 MeuFinanceiro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/dashboard">
                            <i class="mdi mdi-view-dashboard me-1"></i> Dashboard
                        </a>
                    </li>
                    @if(session('nivel_acesso', 0) >= 80)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarAdmin" role="button" data-bs-toggle="dropdown">
                            <i class="mdi mdi-cogs me-1"></i> Administração
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/usuarios">
                                <i class="mdi mdi-account-group me-1"></i> Usuários
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.config.index') }}">
                                <i class="mdi mdi-cog me-1"></i> Configurações
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/admin/fidelidade">
                                <i class="mdi mdi-heart me-1"></i> Admin Fidelidade
                            </a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="mdi mdi-account-circle me-1"></i> 
                            {{ $user->nome }}
                            <span class="badge badge-access-level ms-1">{{ $user->tipo_nome }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/admin/perfil">
                                <i class="mdi mdi-account-edit me-1"></i> Meu Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">
                                <i class="mdi mdi-logout me-1"></i> Sair
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Informações do Usuário -->
        <div class="user-info">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->nome, 0, 1)) }}
                    </div>
                </div>
                <div class="col">
                    <h4 class="mb-1">Bem-vindo, {{ $user->nome }}!</h4>
                    <p class="text-muted mb-1">{{ $user->email }} | {{ $user->tipo_nome }}</p>
                    <p class="text-muted mb-0">Nível de Acesso: {{ $user->nivel_acesso }} | Último login: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-auto">
                    <span class="badge bg-success fs-6">Sistema Ativo</span>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-account-group display-6 mb-3"></i>
                        <h3 class="stat-number">{{ $stats['usuarios_total'] }}</h3>
                        <p class="stat-label">Total de Usuários</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card-success">
                    <div class="card-body text-center">
                        <i class="mdi mdi-account-check display-6 mb-3"></i>
                        <h3 class="stat-number">{{ $stats['usuarios_ativos'] }}</h3>
                        <p class="stat-label">Usuários Ativos</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card-warning">
                    <div class="card-body text-center">
                        <i class="mdi mdi-login display-6 mb-3"></i>
                        <h3 class="stat-number">{{ $stats['tentativas_login_hoje'] }}</h3>
                        <p class="stat-label">Logins Hoje</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card-info">
                    <div class="card-body text-center">
                        <i class="mdi mdi-clock display-6 mb-3"></i>
                        <h3 class="stat-number">{{ $stats['ultimo_login'] ? \Carbon\Carbon::parse($stats['ultimo_login'])->diffForHumans() : 'N/A' }}</h3>
                        <p class="stat-label">Último Login</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Atividades Recentes -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-history me-2"></i>Atividades Recentes
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($atividades->count() > 0)
                            @foreach($atividades as $atividade)
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $atividade->nome }}</strong>
                                        <span class="text-muted">{{ $atividade->action }}</span>
                                    </div>
                                    <small class="activity-time">
                                        {{ \Carbon\Carbon::parse($atividade->created_at)->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                @if($atividade->description)
                                <div class="text-muted mt-1">{{ $atividade->description }}</div>
                                @endif
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="mdi mdi-information-outline display-6 text-muted"></i>
                                <p class="text-muted mt-2">Nenhuma atividade recente</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-information me-2"></i>Informações do Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>Versão:</strong> 
                                <span class="badge bg-primary">2.0 Simplificado</span>
                            </li>
                            <li class="mb-2">
                                <strong>Ambiente:</strong> 
                                <span class="badge bg-info">{{ config('app.env', 'local') }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Servidor:</strong> 
                                <code>{{ request()->getHost() }}</code>
                            </li>
                            <li class="mb-2">
                                <strong>Laravel:</strong> 
                                <code>{{ app()->version() }}</code>
                            </li>
                            <li class="mb-2">
                                <strong>PHP:</strong> 
                                <code>{{ PHP_VERSION }}</code>
                            </li>
                            <li class="mb-0">
                                <strong>Timezone:</strong> 
                                <code>{{ config('app.timezone') }}</code>
                            </li>
                        </ul>

                        <hr>

                        <div class="text-center">
                            <h6>Tipos de Usuário</h6>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-danger">Admin (100)</span>
                                <span class="badge bg-warning">Gerente (80)</span>
                                <span class="badge bg-info">Supervisor (60)</span>
                                <span class="badge bg-success">Operador (40)</span>
                                <span class="badge bg-secondary">Consulta (20)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Links Rápidos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-link me-2"></i>Links Rápidos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if(session('nivel_acesso', 0) >= 80)
                            <a href="/admin/usuarios" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-account-group me-1"></i> Gerenciar Usuários
                            </a>
                            <a href="{{ route('admin.config.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-cog me-1"></i> Configurações
                            </a>
                            @endif
                            @if(session('nivel_acesso', 0) >= 60)
                            <a href="/admin/fidelidade" class="btn btn-outline-success btn-sm">
                                <i class="mdi mdi-shield-crown me-1"></i> Admin Fidelidade
                            </a>
                            @endif
                            <a href="/fidelidade" class="btn btn-outline-success btn-sm">
                                <i class="mdi mdi-heart me-1"></i> Sistema Fidelidade (Cliente)
                            </a>
                            <a href="/admin/relatorios" class="btn btn-outline-info btn-sm">
                                <i class="mdi mdi-chart-bar me-1"></i> Relatórios
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script>
        // Atualizar hora atual a cada minuto
        setInterval(function() {
            const now = new Date();
            const timeString = now.toLocaleString('pt-BR');
            console.log('Sistema ativo:', timeString);
        }, 60000);
        
        // Auto-refresh das estatísticas a cada 5 minutos
        setTimeout(function() {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>

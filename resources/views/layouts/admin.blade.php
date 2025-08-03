<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Painel Administrativo') - {{ config('app.name', 'Marketplace') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Admin CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }

        .sidebar .nav-link .fas.fa-chevron-down {
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link[aria-expanded="true"] .fas.fa-chevron-down {
            transform: rotate(180deg);
        }

        .sidebar .collapse .nav-link {
            padding: 8px 20px 8px 40px;
            font-size: 0.9em;
        }

        .sidebar .collapse .nav-link:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(3px);
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar-admin {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #6c757d;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: #f8f9fa;
            border: none;
            color: #495057;
            font-weight: 600;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }
        
        .badge {
            border-radius: 20px;
            padding: 8px 12px;
            font-weight: 500;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }
        
        .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .dropdown-item {
            padding: 10px 20px;
            border-radius: 5px;
            margin: 2px 5px;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }

        /* Notificações */
        .notification-dropdown {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-dropdown .dropdown-item {
            border-bottom: 1px solid #f1f1f1;
            padding: 15px 20px;
        }

        .notification-dropdown .dropdown-item:last-child {
            border-bottom: none;
        }

        .notification-dropdown .dropdown-item:hover {
            background: #f8f9fa;
        }

        /* Badge de notificação */
        .nav-link .badge {
            font-size: 0.6em;
            min-width: 18px;
            height: 18px;
            line-height: 18px;
        }

        /* Menu responsivo */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-store me-2"></i>
                            Admin Panel
                        </h4>
                    </div>
                    
                    <ul class="nav flex-column">
                        <!-- Dashboard Principal -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>

                        <!-- Configurações -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.config.*') ? 'active' : '' }}" href="#configSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.config.*') ? 'true' : 'false' }}">
                                <i class="fas fa-cog me-2"></i>
                                Configurações
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.config.*') ? 'show' : '' }}" id="configSubmenu">
                                <ul class="list-unstyled ps-3">
                                    <li><a href="#" class="nav-link"><i class="fas fa-sliders-h me-2"></i>Geral</a></li>
                                    <li><a href="#" class="nav-link"><i class="fas fa-envelope me-2"></i>E-mail</a></li>
                                    <li><a href="#" class="nav-link"><i class="fas fa-palette me-2"></i>Aparência</a></li>
                                    <li><a href="#" class="nav-link"><i class="fas fa-shield-alt me-2"></i>Segurança</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Sistema de Fidelidade -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.fidelidade.*') ? 'active' : '' }}" href="#fidelidadeSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.fidelidade.*') ? 'true' : 'false' }}">
                                <i class="fas fa-star me-2"></i>
                                Fidelidade
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.fidelidade.*') ? 'show' : '' }}" id="fidelidadeSubmenu">
                                <ul class="list-unstyled ps-3">
                                    <li><a href="{{ route('admin.fidelidade.dashboard') }}" class="nav-link {{ request()->routeIs('admin.fidelidade.dashboard', 'admin.fidelidade.index') ? 'active' : '' }}"><i class="fas fa-chart-line me-2"></i>Dashboard</a></li>
                                    <li><a href="{{ route('admin.fidelidade.clientes') }}" class="nav-link {{ request()->routeIs('admin.fidelidade.clientes') ? 'active' : '' }}"><i class="fas fa-users me-2"></i>Clientes</a></li>
                                    <li><a href="{{ route('admin.fidelidade.transacoes') }}" class="nav-link {{ request()->routeIs('admin.fidelidade.transacoes') ? 'active' : '' }}"><i class="fas fa-exchange-alt me-2"></i>Transações</a></li>
                                    <li><a href="{{ route('admin.fidelidade.cupons') }}" class="nav-link {{ request()->routeIs('admin.fidelidade.cupons') ? 'active' : '' }}"><i class="fas fa-ticket-alt me-2"></i>Cupons</a></li>
                                    <li><a href="{{ route('admin.fidelidade.cashback') }}" class="nav-link {{ request()->routeIs('admin.fidelidade.cashback') ? 'active' : '' }}"><i class="fas fa-gift me-2"></i>Cashback</a></li>
                                    <li><a href="{{ route('admin.fidelidade.relatorios') }}" class="nav-link {{ request()->routeIs('admin.fidelidade.relatorios') ? 'active' : '' }}"><i class="fas fa-chart-bar me-2"></i>Relatórios</a></li>
                                    <li><a href="{{ route('admin.fidelidade.configuracoes') }}" class="nav-link {{ request()->routeIs('admin.fidelidade.configuracoes') ? 'active' : '' }}"><i class="fas fa-cogs me-2"></i>Configurações</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Sistema de Pagamentos -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="#paymentsSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.payments.*') ? 'true' : 'false' }}">
                                <i class="fas fa-credit-card me-2"></i>
                                Pagamentos
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.payments.*') ? 'show' : '' }}" id="paymentsSubmenu">
                                <ul class="list-unstyled ps-3">
                                    <li><a href="{{ route('admin.payments.dashboard') }}" class="nav-link {{ request()->routeIs('admin.payments.dashboard') ? 'active' : '' }}"><i class="fas fa-chart-line me-2"></i>Dashboard</a></li>
                                    <li><a href="{{ route('admin.payments.transactions') }}" class="nav-link {{ request()->routeIs('admin.payments.transactions') ? 'active' : '' }}"><i class="fas fa-list me-2"></i>Transações</a></li>
                                    <li><a href="{{ route('admin.payments.gateways') }}" class="nav-link {{ request()->routeIs('admin.payments.gateways') ? 'active' : '' }}"><i class="fas fa-gateway me-2"></i>Gateways</a></li>
                                    <li><a href="{{ route('admin.payments.methods') }}" class="nav-link {{ request()->routeIs('admin.payments.methods') ? 'active' : '' }}"><i class="fas fa-money-bill me-2"></i>Métodos</a></li>
                                    <li><a href="{{ route('admin.payments.webhooks') }}" class="nav-link {{ request()->routeIs('admin.payments.webhooks') ? 'active' : '' }}"><i class="fas fa-webhook me-2"></i>Webhooks</a></li>
                                    <li><a href="{{ route('admin.payments.reports') }}" class="nav-link {{ request()->routeIs('admin.payments.reports') ? 'active' : '' }}"><i class="fas fa-chart-bar me-2"></i>Relatórios</a></li>
                                    <li><a href="{{ route('admin.payments.settings') }}" class="nav-link {{ request()->routeIs('admin.payments.settings') ? 'active' : '' }}"><i class="fas fa-cogs me-2"></i>Configurações</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Gestão de Usuários -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="#usuariosSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.usuarios.*') ? 'true' : 'false' }}">
                                <i class="fas fa-users me-2"></i>
                                Usuários
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.usuarios.*') ? 'show' : '' }}" id="usuariosSubmenu">
                                <ul class="list-unstyled ps-3">
                                    <li><a href="#" class="nav-link"><i class="fas fa-list me-2"></i>Listar Usuários</a></li>
                                    <li><a href="#" class="nav-link"><i class="fas fa-user-plus me-2"></i>Adicionar Usuário</a></li>
                                    <li><a href="#" class="nav-link"><i class="fas fa-user-shield me-2"></i>Permissões</a></li>
                                    <li><a href="#" class="nav-link"><i class="fas fa-users-cog me-2"></i>Grupos</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Notificações (Futuro) -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.notificacoes.*') ? 'active' : '' }}" href="#notificacoesSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.notificacoes.*') ? 'true' : 'false' }}">
                                <i class="fas fa-bell me-2"></i>
                                Notificações
                                <span class="badge bg-danger ms-2">3</span>
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.notificacoes.*') ? 'show' : '' }}" id="notificacoesSubmenu">
                                <ul class="list-unstyled ps-3">
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-inbox me-2"></i>Caixa de Entrada</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-paper-plane me-2"></i>Enviar Notificação</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-cogs me-2"></i>Configurações</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-history me-2"></i>Histórico</a></li>
                                </ul>
                            </div>
                        </li>

                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">

                        <!-- Módulos do Sistema -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="fas fa-store me-2"></i>
                                Empresas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="fas fa-chart-line me-2"></i>
                                Financeiro
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="fas fa-cash-register me-2"></i>
                                PDV
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="fas fa-truck me-2"></i>
                                Delivery
                            </a>
                        </li>

                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">

                        <!-- Relatórios Gerais -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.relatorios.*') ? 'active' : '' }}" href="#relatoriosSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.relatorios.*') ? 'true' : 'false' }}">
                                <i class="fas fa-chart-bar me-2"></i>
                                Relatórios
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.relatorios.*') ? 'show' : '' }}" id="relatoriosSubmenu">
                                <ul class="list-unstyled ps-3">
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-chart-area me-2"></i>Vendas</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-users me-2"></i>Usuários</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-money-bill-wave me-2"></i>Financeiro</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-download me-2"></i>Exportar</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Sistema -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sistema.*') ? 'active' : '' }}" href="#sistemaSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.sistema.*') ? 'true' : 'false' }}">
                                <i class="fas fa-server me-2"></i>
                                Sistema
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.sistema.*') ? 'show' : '' }}" id="sistemaSubmenu">
                                <ul class="list-unstyled ps-3">
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-info-circle me-2"></i>Informações</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-database me-2"></i>Backup</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-file-alt me-2"></i>Logs</a></li>
                                    <li><a href="#" class="nav-link" onclick="alert('Módulo em desenvolvimento')"><i class="fas fa-sync me-2"></i>Manutenção</a></li>
                                </ul>
                            </div>
                        </li>

                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">

                        <!-- Links Externos -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Ver Site
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Documentação em desenvolvimento')">
                                <i class="fas fa-book me-2"></i>
                                Documentação
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <nav class="navbar navbar-expand-lg navbar-admin sticky-top mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler d-md-none" type="button" onclick="toggleSidebar()">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        <div class="navbar-nav ms-auto">
                            <!-- Notificações -->
                            <div class="nav-item dropdown me-3">
                                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-bell fa-lg"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        3
                                        <span class="visually-hidden">notificações não lidas</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 300px;">
                                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                                        <span>Notificações</span>
                                        <small class="text-muted">3 não lidas</small>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex" href="#" onclick="alert('Sistema de notificações em desenvolvimento')">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold">Nova transação</div>
                                                <div class="small text-muted">Pagamento de R$ 150,00 aprovado</div>
                                                <div class="small text-muted">2 min atrás</div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex" href="#" onclick="alert('Sistema de notificações em desenvolvimento')">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-user-plus text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold">Novo usuário</div>
                                                <div class="small text-muted">João Silva se cadastrou</div>
                                                <div class="small text-muted">5 min atrás</div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex" href="#" onclick="alert('Sistema de notificações em desenvolvimento')">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold">Sistema</div>
                                                <div class="small text-muted">Backup automático realizado</div>
                                                <div class="small text-muted">1 hora atrás</div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-center" href="#" onclick="alert('Sistema de notificações em desenvolvimento')">
                                            Ver todas as notificações
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Perfil do Usuário -->
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="https://via.placeholder.com/32x32" class="rounded-circle me-2" alt="Avatar">
                                    <span>{{ auth()->user()->name ?? 'Administrador' }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Perfil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configurações</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page header -->
                @if(isset($pageTitle) || isset($breadcrumbs))
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            @isset($pageTitle)
                                <h1 class="h3 mb-0">{{ $pageTitle }}</h1>
                            @endisset
                            
                            @isset($breadcrumbs)
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        @foreach($breadcrumbs as $breadcrumb)
                                            @if($loop->last)
                                                <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                                            @else
                                                <li class="breadcrumb-item">
                                                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ol>
                                </nav>
                            @endisset
                        </div>
                        
                        @hasSection('page-actions')
                            <div class="col-auto">
                                @yield('page-actions')
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="uil uil-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        <strong>Ops! Algo deu errado:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Main content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Admin JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Add loading state to forms
        $('form').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
        });
        
        // Confirm delete actions
        $('.btn-delete').on('click', function(e) {
            if (!confirm('Tem certeza que deseja excluir este item?')) {
                e.preventDefault();
            }
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Menu responsivo
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('show');
        }

        // Fechar sidebar ao clicar fora (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggleButton = document.querySelector('.navbar-toggler');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Destacar menu ativo
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href;
            const menuLinks = document.querySelectorAll('.sidebar .nav-link');
            
            menuLinks.forEach(link => {
                if (link.href === currentUrl) {
                    link.classList.add('active');
                    
                    // Expandir submenu se necessário
                    const collapse = link.getAttribute('href');
                    if (collapse && collapse.startsWith('#')) {
                        const submenu = document.querySelector(collapse);
                        if (submenu) {
                            submenu.classList.add('show');
                            link.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });
        });

        // Animação dos ícones do menu
        document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]').forEach(link => {
            link.addEventListener('click', function() {
                const icon = this.querySelector('.fa-chevron-down');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                if (icon) {
                    if (isExpanded) {
                        icon.style.transform = 'rotate(0deg)';
                    } else {
                        icon.style.transform = 'rotate(180deg)';
                    }
                }
            });
        });

        // Contador de notificações (simulado)
        function updateNotificationCount() {
            // Esta função seria conectada a uma API real
            const count = Math.floor(Math.random() * 10);
            const badges = document.querySelectorAll('.nav-link .badge, .position-absolute.badge');
            
            badges.forEach(badge => {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            });
        }

        // Atualizar contadores a cada 30 segundos (simulação)
        setInterval(updateNotificationCount, 30000);
    </script>
    
    @stack('scripts')
</body>
</html>

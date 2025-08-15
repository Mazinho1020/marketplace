<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel do Comerciante') - {{ config('app.name', 'Marketplace') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .navbar-comerciante {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 70px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 10px 15px !important;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.2);
        }

        .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mega-menu {
            min-width: 600px;
        }

        .mega-menu-header {
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }

        .mega-menu-item {
            display: block;
            padding: 8px 12px;
            color: #495057;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .mega-menu-item:hover {
            background: #f8f9fa;
            color: #2c3e50;
            transform: translateX(5px);
        }

        .mega-menu-item i {
            width: 20px;
            margin-right: 8px;
            color: #6c757d;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-user {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 5px 15px;
        }

        .main-content {
            background: #f8f9fa;
            min-height: calc(100vh - 70px);
            padding-top: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-comerciante sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('comerciantes.dashboard') }}">
                <i class="fas fa-store me-2"></i>Marketplace
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('comerciantes.dashboard*') ? 'active' : '' }}"
                            href="{{ route('comerciantes.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>

                    <!-- Produtos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('comerciantes.produtos*') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-box me-1"></i>Produtos
                        </a>
                        <div class="dropdown-menu mega-menu p-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-box"></i> Gestão
                                    </div>
                                    <a href="{{ route('comerciantes.produtos.index') }}" class="mega-menu-item">
                                        <i class="fas fa-list"></i>Todos os Produtos
                                    </a>
                                    <a href="{{ route('comerciantes.produtos.create') }}" class="mega-menu-item">
                                        <i class="fas fa-plus"></i>Novo Produto
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-tags"></i> Organização
                                    </div>
                                    <a href="{{ route('comerciantes.produtos.categorias.index') }}" class="mega-menu-item">
                                        <i class="fas fa-folder"></i>Categorias
                                    </a>
                                    <a href="{{ route('comerciantes.produtos.marcas.index') }}" class="mega-menu-item">
                                        <i class="fas fa-copyright"></i>Marcas
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-boxes"></i> Avançado
                                    </div>
                                    <a href="{{ route('comerciantes.produtos.kits.index') }}" class="mega-menu-item">
                                        <i class="fas fa-cubes"></i>Kits e Combos
                                    </a>
                                    <a href="{{ route('comerciantes.produtos.configuracoes.index') }}" class="mega-menu-item">
                                        <i class="fas fa-cogs"></i>Configurações
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-warehouse"></i> Controle
                                    </div>
                                    <a href="{{ route('comerciantes.produtos.estoque.alertas') }}" class="mega-menu-item">
                                        <i class="fas fa-exclamation-triangle"></i>Alertas Estoque
                                    </a>
                                    <a href="{{ route('comerciantes.produtos.codigos-barras.index') }}" class="mega-menu-item">
                                        <i class="fas fa-barcode"></i>Códigos Barras
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Pessoas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('comerciantes.pessoas*') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users me-1"></i>Pessoas
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">Gestão de Pessoas</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.pessoas.index') }}">
                                    <i class="fas fa-list me-2"></i>Todas as Pessoas
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.pessoas.create') }}">
                                    <i class="fas fa-user-plus me-2"></i>Nova Pessoa
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.departamentos.index') }}">
                                    <i class="fas fa-building me-2"></i>Departamentos
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.cargos.index') }}">
                                    <i class="fas fa-user-tie me-2"></i>Cargos
                                </a></li>
                        </ul>
                    </li>

                    <!-- Empresas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('comerciantes.empresas*') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-building me-1"></i>Empresas
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">Gestão Empresarial</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.empresas.index') }}">
                                    <i class="fas fa-list me-2"></i>Minhas Empresas
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.empresas.create') }}">
                                    <i class="fas fa-plus me-2"></i>Nova Empresa
                                </a></li>
                        </ul>
                    </li>

                    <!-- Financeiro -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-line me-1"></i>Financeiro
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">Gestão Financeira</h6>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-arrow-up me-2 text-success"></i>Contas a Receber</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-arrow-down me-2 text-danger"></i>Contas a Pagar</a></li>
                        </ul>
                    </li>

                    <!-- Relatórios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar me-1"></i>Relatórios
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">Análises</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.relatorios.vendas') }}">
                                    <i class="fas fa-shopping-cart me-2"></i>Vendas
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('comerciantes.relatorios.clientes') }}">
                                    <i class="fas fa-users me-2"></i>Clientes
                                </a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Menu Direita -->
                <ul class="navbar-nav">
                    <!-- Notificações -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="notification-badge">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                            <h6 class="dropdown-header">Notificações</h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                Estoque baixo em 5 produtos
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="{{ route('comerciantes.notificacoes.index') }}">
                                Ver todas
                            </a>
                        </div>
                    </li>

                    <!-- Usuário -->
                    <li class="nav-item dropdown">
                        <div class="navbar-user">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    <i class="fas fa-user text-dark"></i>
                                </div>
                                <span>{{ auth()->user()->name ?? 'Comerciante' }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <h6 class="dropdown-header">Minha Conta</h6>
                                </li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configurações</a></li>
                                <li><a class="dropdown-item" href="{{ route('comerciantes.planos.dashboard') }}"><i class="fas fa-crown me-2"></i>Meu Plano</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Ver Site</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('comerciantes.logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Alerts -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><strong>Ops! Algo deu errado:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts
            setTimeout(() => $('.alert').fadeOut('slow'), 5000);

            // Form loading states
            $('form').on('submit', function() {
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
            });

            // Delete confirmations
            $('.btn-delete').on('click', function(e) {
                if (!confirm('Tem certeza que deseja excluir este item?')) e.preventDefault();
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Highlight active menu
            const currentUrl = window.location.href;
            $('.navbar-nav .nav-link').each(function() {
                if (this.href === currentUrl) $(this).addClass('active');
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
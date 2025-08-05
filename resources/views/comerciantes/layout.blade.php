<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel do Comerciante') - Marketplace</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--light-color);
            color: #374151;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(4px);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }

        /* Header */
        .main-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-bottom: 1px solid var(--border-color);
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .card-header {
            background-color: var(--light-color);
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0 !important;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--light-color);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--dark-color);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border-left-color: var(--success-color);
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border-left-color: var(--danger-color);
        }

        .alert-warning {
            background-color: #fffbeb;
            color: #92400e;
            border-left-color: var(--warning-color);
        }

        .alert-info {
            background-color: #eff6ff;
            color: #1e40af;
            border-left-color: var(--primary-color);
        }

        /* Forms */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        /* User dropdown */
        .user-dropdown {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 8px 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
                transition: margin-left 0.3s ease;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        /* Loading */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column h-100">
                    <!-- Logo -->
                    <div class="p-3 text-center border-bottom border-light border-opacity-25">
                        <h4 class="text-white mb-0">
                            <i class="fas fa-store me-2"></i>
                            Marketplace
                        </h4>
                        <small class="text-white-50">Painel do Comerciante</small>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-grow-1 p-3">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('comerciantes.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('comerciantes.dashboard') }}">
                                    <i class="fas fa-chart-line"></i>
                                    Dashboard
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('comerciantes.marcas.*') ? 'active' : '' }}" 
                                   href="{{ route('comerciantes.marcas.index') }}">
                                    <i class="fas fa-tags"></i>
                                    Marcas
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('comerciantes.empresas.*') ? 'active' : '' }}" 
                                   href="{{ route('comerciantes.empresas.index') }}">
                                    <i class="fas fa-building"></i>
                                    Empresas
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('comerciantes.horarios.*') ? 'active' : '' }}" 
                                   href="{{ route('comerciantes.empresas.index') }}">
                                    <i class="fas fa-clock"></i>
                                    Horários de Funcionamento
                                </a>
                            </li>

                            <hr class="border-light border-opacity-25 my-3">
                            
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-box"></i>
                                    Produtos
                                    <span class="badge bg-warning ms-auto">Em breve</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-shopping-cart"></i>
                                    Pedidos
                                    <span class="badge bg-warning ms-auto">Em breve</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-chart-bar"></i>
                                    Relatórios
                                    <span class="badge bg-warning ms-auto">Em breve</span>
                                </a>
                            </li>

                            <hr class="border-light border-opacity-25 my-3">
                            
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-cog"></i>
                                    Configurações
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <!-- User Info -->
                    <div class="p-3 border-top border-light border-opacity-25">
                        <div class="user-dropdown">
                            <div class="d-flex align-items-center">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px; font-weight: 600;">
                                    {{ substr(Auth::guard('comerciante')->user()->nome, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-white fw-medium small">{{ Auth::guard('comerciante')->user()->nome }}</div>
                                    <div class="text-white-50" style="font-size: 0.75rem;">{{ Auth::guard('comerciante')->user()->email }}</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-white p-0" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Perfil</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configurações</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('comerciantes.logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <!-- Header -->
                <header class="main-header">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary d-md-none me-2" id="sidebarToggle">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="h4 mb-0">@yield('title', 'Painel do Comerciante')</h1>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <div class="text-muted small d-none d-sm-block">
                                <i class="fas fa-clock me-1"></i>
                                {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-3 p-md-4">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // CSRF token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Setup AJAX headers
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>

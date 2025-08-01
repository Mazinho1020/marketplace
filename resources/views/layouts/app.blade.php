<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Marketplace Sistema')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Marketplace" name="description" />
    <meta content="Marketplace" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('Theme1/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('Theme1/js/layout.js') }}"></script>
    
    <!-- Bootstrap Css -->
    <link href="{{ asset('Theme1/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('Theme1/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Custom Css-->
    <link href="{{ asset('Theme1/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    
    <!-- Additional CSS -->
    @stack('css')
    
    <!-- Custom Styles -->
    <style>
        :root {
            --bs-primary: #405189;
            --bs-primary-rgb: 64, 81, 137;
            --bs-secondary: #74788d;
            --bs-success: #06d6a0;
            --bs-info: #0ab39c;
            --bs-warning: #f7b84b;
            --bs-danger: #f06548;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;
        }
        
        .navbar-brand-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: calc(100vh - 60px);
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        .page-title-box {
            background: #fff;
            padding: 20px 24px;
            margin: -20px -20px 20px -20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
        }
        
        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .card {
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
            border: 1px solid #e9ecef;
        }
        
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        
        .btn-primary:hover {
            background-color: #364574;
            border-color: #364574;
        }
    </style>
    
    @stack('styles')
</head>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        @unless(request()->routeIs('login', 'register', 'password.*'))
            @include('layouts.header')
            @include('layouts.sidebar')
        @endunless

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        @if(request()->routeIs('login', 'register', 'password.*'))
            <!-- Auth pages without sidebar -->
            @yield('content')
        @else
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        
                        <!-- Page Title -->
                        @hasSection('page-title')
                            <div class="page-title-box">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="page-title">@yield('page-title')</h6>
                                        @hasSection('breadcrumb')
                                            <ol class="breadcrumb m-0">
                                                @yield('breadcrumb')
                                            </ol>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="float-end d-none d-md-block">
                                            @yield('page-actions')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Alerts -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ri-check-line me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="ri-alert-line me-2"></i>{{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="ri-information-line me-2"></i>{{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Main Content -->
                        @yield('content')

                    </div>
                </div>
                
                @include('layouts.footer')
            </div>
        @endif
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @stack('modals')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">
                                <i class="fas fa-home me-1"></i>
                                Início
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="fidelidadeDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-star me-1"></i>
                                Fidelidade
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="fidelidadeDropdown">
                                <li><a class="dropdown-item" href="{{ route('fidelidade.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="{{ route('fidelidade.carteiras.index') }}">
                                        <i class="fas fa-wallet me-2"></i>Carteiras
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('fidelidade.cupons.index') }}">
                                        <i class="fas fa-tags me-2"></i>Cupons
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('fidelidade.regras.index') }}">
                                        <i class="fas fa-cogs me-2"></i>Regras de Cashback
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('fidelidade.relatorios.index') }}">
                                        <i class="fas fa-chart-bar me-2"></i>Relatórios
                                    </a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a></li>
                            </ul>
                        </li>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            @endif

            @if(session('info'))
            <div class="container mt-4">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-white py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Marketplace</h5>
                        <p class="mb-0">Sistema de gestão completo para seu negócio.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">&copy; {{ date('Y') }} Marketplace. Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Manual dropdown test -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
            
            // Test manual toggle
            const dropdownToggle = document.getElementById('fidelidadeDropdown');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    if (dropdownMenu) {
                        dropdownMenu.classList.toggle('show');
                    }
                    console.log('Dropdown clicked');
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
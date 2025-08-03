<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title', 'Marketplace Sistema'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Marketplace" name="description" />
    <meta content="Marketplace" name="author" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset('Theme1/images/favicon.ico')); ?>">

    <!-- Layout config Js -->
    <script src="<?php echo e(asset('Theme1/js/layout.js')); ?>"></script>
    
    <!-- Bootstrap Css -->
    <link href="<?php echo e(asset('Theme1/css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?php echo e(asset('Theme1/css/icons.min.css')); ?>" rel="stylesheet" type="text/css" />
    <!-- Custom Css-->
    <link href="<?php echo e(asset('Theme1/css/custom.min.css')); ?>" rel="stylesheet" type="text/css" />
    
    <!-- Additional CSS -->
    <?php echo $__env->yieldPushContent('css'); ?>
    
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
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php if (! (request()->routeIs('login', 'register', 'password.*'))): ?>
            <?php echo $__env->make('layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <?php if(request()->routeIs('login', 'register', 'password.*')): ?>
            <!-- Auth pages without sidebar -->
            <?php echo $__env->yieldContent('content'); ?>
        <?php else: ?>
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        
                        <!-- Page Title -->
                        <?php if (! empty(trim($__env->yieldContent('page-title')))): ?>
                            <div class="page-title-box">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="page-title"><?php echo $__env->yieldContent('page-title'); ?></h6>
                                        <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
                                            <ol class="breadcrumb m-0">
                                                <?php echo $__env->yieldContent('breadcrumb'); ?>
                                            </ol>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="float-end d-none d-md-block">
                                            <?php echo $__env->yieldContent('page-actions'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Alerts -->
                        <?php if(session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ri-check-line me-2"></i><?php echo e(session('success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(session('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-error-warning-line me-2"></i><?php echo e(session('error')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(session('warning')): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="ri-alert-line me-2"></i><?php echo e(session('warning')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(session('info')): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="ri-information-line me-2"></i><?php echo e(session('info')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Main Content -->
                        <?php echo $__env->yieldContent('content'); ?>

                    </div>
                </div>
                
                <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php endif; ?>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    <?php echo $__env->yieldPushContent('modals'); ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(url('/')); ?>">
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
                                <li><a class="dropdown-item" href="<?php echo e(route('fidelidade.dashboard')); ?>">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo e(route('fidelidade.carteiras.index')); ?>">
                                        <i class="fas fa-wallet me-2"></i>Carteiras
                                    </a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('fidelidade.cupons.index')); ?>">
                                        <i class="fas fa-tags me-2"></i>Cupons
                                    </a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('fidelidade.regras.index')); ?>">
                                        <i class="fas fa-cogs me-2"></i>Regras de Cashback
                                    </a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('fidelidade.relatorios.index')); ?>">
                                        <i class="fas fa-chart-bar me-2"></i>Relatórios
                                    </a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <?php if(auth()->guard()->guest()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>">Login</a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                <?php echo e(Auth::user()->name); ?>

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a></li>
                            </ul>
                        </li>

                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <?php if(session('success')): ?>
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php endif; ?>

            <?php if(session('info')): ?>
            <div class="container mt-4">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo e(session('info')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
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
                        <p class="mb-0">&copy; <?php echo e(date('Y')); ?> Marketplace. Todos os direitos reservados.</p>
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

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/layouts/app.blade.php ENDPATH**/ ?>
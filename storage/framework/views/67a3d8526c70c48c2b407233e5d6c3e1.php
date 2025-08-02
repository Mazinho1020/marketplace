<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Painel Administrativo'); ?> - <?php echo e(config('app.name', 'Marketplace')); ?></title>

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
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
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
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                                <i class="uil uil-dashboard me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.config.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.config.index')); ?>">
                                <i class="uil uil-setting me-2"></i>
                                Configurações
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.fidelidade.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.fidelidade.index')); ?>">
                                <i class="uil uil-star me-2"></i>
                                Fidelidade
                            </a>
                        </li>
                        
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.payments.*') ? 'active' : ''); ?>" href="#paymentsSubmenu" data-bs-toggle="collapse" aria-expanded="<?php echo e(request()->routeIs('admin.payments.*') ? 'true' : 'false'); ?>">
                            <i class="uil uil-credit-card me-2"></i>
                            Pagamentos
                            <i class="uil uil-angle-down ms-auto"></i>
                        </a>
                        <div class="collapse <?php echo e(request()->routeIs('admin.payments.*') ? 'show' : ''); ?>" id="paymentsSubmenu">
                            <ul class="list-unstyled ps-3">
                                <li>
                                    <a href="<?php echo e(route('admin.payments.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.payments.dashboard') ? 'active' : ''); ?>">
                                        <i class="uil uil-chart-line me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.payments.transactions')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.payments.transactions') ? 'active' : ''); ?>">
                                        <i class="uil uil-transaction me-2"></i>Transações
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.payments.gateways')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.payments.gateways') ? 'active' : ''); ?>">
                                        <i class="uil uil-credit-card me-2"></i>Gateways
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.payments.methods')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.payments.methods') ? 'active' : ''); ?>">
                                        <i class="uil uil-money-bill me-2"></i>Métodos
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.payments.webhooks')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.payments.webhooks') ? 'active' : ''); ?>">
                                        <i class="uil uil-webhook me-2"></i>Webhooks
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.payments.reports')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.payments.reports') ? 'active' : ''); ?>">
                                        <i class="uil uil-chart me-2"></i>Relatórios
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.payments.settings')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.payments.settings') ? 'active' : ''); ?>">
                                        <i class="uil uil-setting me-2"></i>Configurações
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('admin.usuarios')); ?>">
                                <i class="uil uil-users-alt me-2"></i>
                                Usuários
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="uil uil-shop me-2"></i>
                                Empresas
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="uil uil-bill me-2"></i>
                                Financeiro
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="uil uil-cash-stack me-2"></i>
                                PDV
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="uil uil-truck me-2"></i>
                                Delivery
                            </a>
                        </li>
                        
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="uil uil-chart-line me-2"></i>
                                Relatórios
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Módulo em desenvolvimento')">
                                <i class="uil uil-cog me-2"></i>
                                Sistema
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="<?php echo e(url('/')); ?>">
                                <i class="uil uil-external-link-alt me-2"></i>
                                Ver Site
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
                        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="https://via.placeholder.com/32x32" class="rounded-circle me-2" alt="Avatar">
                                    <span><?php echo e(auth()->user()->name ?? 'Administrador'); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="uil uil-user me-2"></i>Perfil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="uil uil-setting me-2"></i>Configurações</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="dropdown-item">
                                                <i class="uil uil-sign-out-alt me-2"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page header -->
                <?php if(isset($pageTitle) || isset($breadcrumbs)): ?>
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <?php if(isset($pageTitle)): ?>
                                <h1 class="h3 mb-0"><?php echo e($pageTitle); ?></h1>
                            <?php endif; ?>
                            
                            <?php if(isset($breadcrumbs)): ?>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($loop->last): ?>
                                                <li class="breadcrumb-item active"><?php echo e($breadcrumb['title']); ?></li>
                                            <?php else: ?>
                                                <li class="breadcrumb-item">
                                                    <a href="<?php echo e($breadcrumb['url']); ?>"><?php echo e($breadcrumb['title']); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ol>
                                </nav>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (! empty(trim($__env->yieldContent('page-actions')))): ?>
                            <div class="col-auto">
                                <?php echo $__env->yieldContent('page-actions'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Alerts -->
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="uil uil-check-circle me-2"></i>
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        <?php echo e(session('warning')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        <strong>Ops! Algo deu errado:</strong>
                        <ul class="mb-0 mt-2">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Main content -->
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
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
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/layouts/admin.blade.php ENDPATH**/ ?>
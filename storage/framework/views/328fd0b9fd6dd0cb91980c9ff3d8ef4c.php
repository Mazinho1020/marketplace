<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Painel do Comerciante'); ?> - <?php echo e(config('app.name', 'Marketplace')); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --navbar-bg: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
        }

        [data-bs-theme="dark"] {
            --navbar-bg: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            --bg-color: #121212;
            --text-color: #ffffff;
            --card-bg: #1e1e1e;
            --border-color: #444444;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .navbar-comerciante {
            background: var(--navbar-bg);
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
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: var(--text-color);
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #333333;
            color: var(--text-color);
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

        [data-bs-theme="dark"] .mega-menu-header {
            color: #ffffff;
            border-bottom: 2px solid #444444;
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
            background: var(--bg-color);
            min-height: calc(100vh - 70px);
            padding-top: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* Botão de modo escuro */
        .theme-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 20px;
            padding: 5px 10px;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Alertas modo escuro */
        [data-bs-theme="dark"] .alert {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }

        /* Dropdown menus no modo escuro */
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: var(--text-color);
        }

        [data-bs-theme="dark"] .dropdown-item:hover,
        [data-bs-theme="dark"] .dropdown-item:focus {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
        }

        [data-bs-theme="dark"] .dropdown-header {
            color: var(--text-color);
        }

        /* Navbar modo escuro específico */
        [data-bs-theme="dark"] .navbar-comerciante {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }

        /* Mega menu modo escuro */
        [data-bs-theme="dark"] .mega-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-bs-theme="dark"] .mega-menu-header {
            color: #fff;
            border-bottom-color: var(--border-color);
        }

        [data-bs-theme="dark"] .mega-menu-item {
            color: var(--text-color);
        }

        [data-bs-theme="dark"] .mega-menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Seletor de empresas */
        .empresa-selector .dropdown-item {
            padding: 0.75rem 1rem;
        }

        .empresa-selector .dropdown-item.active {
            background-color: var(--bs-primary);
            color: white;
        }

        .empresa-selector .dropdown-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        [data-bs-theme="dark"] .empresa-selector .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>

    <script>
        // Tema toggle function
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const themeIcon = document.getElementById('theme-icon');

            if (currentTheme === 'dark') {
                html.setAttribute('data-bs-theme', 'light');
                themeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Carregar tema salvo
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const themeIcon = document.getElementById('theme-icon');

            document.documentElement.setAttribute('data-bs-theme', savedTheme);

            if (savedTheme === 'dark') {
                themeIcon.className = 'fas fa-sun';
            } else {
                themeIcon.className = 'fas fa-moon';
            }
        });
    </script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-comerciante sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(route('comerciantes.dashboard')); ?>">
                <i class="fas fa-store me-2"></i>Marketplace
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('comerciantes.dashboard*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('comerciantes.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>

                    <!-- Produtos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('comerciantes.produtos*') ? 'active' : ''); ?>"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-box me-1"></i>Produtos
                        </a>
                        <div class="dropdown-menu mega-menu p-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-box"></i> Gestão
                                    </div>
                                    <a href="<?php echo e(route('comerciantes.produtos.index')); ?>" class="mega-menu-item">
                                        <i class="fas fa-list"></i>Todos os Produtos
                                    </a>
                                    <a href="<?php echo e(route('comerciantes.produtos.create')); ?>" class="mega-menu-item">
                                        <i class="fas fa-plus"></i>Novo Produto
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-tags"></i> Organização
                                    </div>
                                    <a href="<?php echo e(route('comerciantes.produtos.categorias.index')); ?>" class="mega-menu-item">
                                        <i class="fas fa-folder"></i>Categorias
                                    </a>
                                    <a href="<?php echo e(route('comerciantes.produtos.marcas.index')); ?>" class="mega-menu-item">
                                        <i class="fas fa-copyright"></i>Marcas
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-boxes"></i> Avançado
                                    </div>
                                    <a href="<?php echo e(route('comerciantes.produtos.subcategorias.index')); ?>" class="mega-menu-item">
                                        <i class="fas fa-sitemap"></i>Subcategorias
                                    </a>
                                    <a href="<?php echo e(route('comerciantes.produtos.codigos-barras.create')); ?>" class="mega-menu-item">
                                        <i class="fas fa-plus"></i>Novo Código
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <div class="mega-menu-header">
                                        <i class="fas fa-warehouse"></i> Controle
                                    </div>
                                    <a href="<?php echo e(route('comerciantes.produtos.relatorio-estoque')); ?>" class="mega-menu-item">
                                        <i class="fas fa-chart-bar"></i>Relatório Estoque
                                    </a>
                                    <a href="<?php echo e(route('comerciantes.produtos.codigos-barras.index')); ?>" class="mega-menu-item">
                                        <i class="fas fa-barcode"></i>Códigos Barras
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Pessoas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('comerciantes.pessoas*') ? 'active' : ''); ?>"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users me-1"></i>Pessoas
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">Gestão de Pessoas</h6>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.pessoas.index')); ?>">
                                    <i class="fas fa-list me-2"></i>Todas as Pessoas
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.pessoas.create')); ?>">
                                    <i class="fas fa-user-plus me-2"></i>Nova Pessoa
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.departamentos.index')); ?>">
                                    <i class="fas fa-building me-2"></i>Departamentos
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.cargos.index')); ?>">
                                    <i class="fas fa-user-tie me-2"></i>Cargos
                                </a></li>
                        </ul>
                    </li>

                    <!-- Empresas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('comerciantes.empresas*') ? 'active' : ''); ?>"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-building me-1"></i>Empresas
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">Gestão Empresarial</h6>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.index')); ?>">
                                    <i class="fas fa-list me-2"></i>Minhas Empresas
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.create')); ?>">
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
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.relatorios.financeiro-detalhado')); ?>">
                                    <i class="fas fa-chart-bar me-2"></i>Relatório Detalhado
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php if(function_exists('tem_empresa_selecionada') && tem_empresa_selecionada()): ?>
                            <li><a class="dropdown-item" href="<?php echo e(route_financeiro('contas-receber.index')); ?>">
                                    <i class="fas fa-arrow-up me-2 text-success"></i>Contas a Receber
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route_financeiro('contas-pagar.index')); ?>">
                                    <i class="fas fa-arrow-down me-2 text-danger"></i>Contas a Pagar
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route_financeiro('dashboard')); ?>">
                                    <i class="fas fa-calculator me-2"></i>Dashboard Financeiro
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route_financeiro('contas.index')); ?>">
                                    <i class="fas fa-list me-2"></i>Contas Gerenciais
                                </a></li>
                            <?php else: ?>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.index')); ?>">
                                    <i class="fas fa-exclamation-circle me-2 text-warning"></i>Selecione uma empresa
                                </a></li>
                            <?php endif; ?>
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
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.dashboard')); ?>">
                                    <i class="fas fa-chart-line me-2"></i>Dashboard
                                </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.produtos.relatorio-estoque')); ?>">
                                    <i class="fas fa-boxes me-2"></i>Estoque
                                </a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Menu Direita -->
                <ul class="navbar-nav">
                    <!-- Seletor de Empresa -->
                    <?php if(auth('comerciante')->check()): ?>
                    <?php
                        $user = auth('comerciante')->user();
                        $empresas = $user->todas_empresas ?? collect();
                        $empresaAtual = $empresas->where('id', session('empresa_atual_id'))->first();
                    ?>
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-building text-white" style="font-size: 0.8rem;"></i>
                            </div>
                            <span class="text-truncate" style="max-width: 120px;">
                                <?php echo e($empresaAtual->nome_fantasia ?? 'Selecionar Empresa'); ?>

                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end empresa-selector" style="min-width: 280px;">
                            <h6 class="dropdown-header">Minhas Empresas</h6>
                            <?php if($empresas->count() > 0): ?>
                                <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a class="dropdown-item <?php echo e(session('empresa_atual_id') == $empresa->id ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('comerciantes.dashboard.empresa', $empresa->id)); ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-<?php echo e(session('empresa_atual_id') == $empresa->id ? 'success' : 'secondary'); ?> rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 25px; height: 25px;">
                                            <i class="fas fa-building text-white" style="font-size: 0.7rem;"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-semibold"><?php echo e($empresa->nome_fantasia); ?></div>
                                            <small class="text-muted"><?php echo e($empresa->razao_social); ?></small>
                                        </div>
                                        <?php if(session('empresa_atual_id') == $empresa->id): ?>
                                        <i class="fas fa-check text-success ms-2"></i>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo e(route('comerciantes.dashboard.limpar')); ?>">
                                    <i class="fas fa-times-circle me-2 text-danger"></i>Limpar Seleção
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.index')); ?>">
                                    <i class="fas fa-cog me-2"></i>Gerenciar Empresas
                                </a>
                            <?php else: ?>
                                <span class="dropdown-item-text text-muted">Nenhuma empresa encontrada</span>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.create')); ?>">
                                    <i class="fas fa-plus me-2"></i>Criar Nova Empresa
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endif; ?>

                    <!-- Modo Escuro/Claro -->
                    <li class="nav-item me-3">
                        <button class="btn theme-toggle" onclick="toggleTheme()" title="Alternar modo escuro/claro">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </li>

                    <!-- Notificações -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="notification-badge">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                            <h6 class="dropdown-header">Notificações</h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo e(route('comerciantes.produtos.relatorio-estoque')); ?>">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                Estoque baixo em 5 produtos
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="<?php echo e(route('comerciantes.notificacoes.index')); ?>">
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
                                <span><?php echo e(auth()->user()->name ?? 'Comerciante'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <h6 class="dropdown-header">Minha Conta</h6>
                                </li>
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.dashboard')); ?>"><i class="fas fa-user me-2"></i>Perfil</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.dashboard')); ?>"><i class="fas fa-cog me-2"></i>Configurações</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.planos.dashboard')); ?>"><i class="fas fa-crown me-2"></i>Meu Plano</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo e(url('/')); ?>" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Ver Site</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('comerciantes.logout')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
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
            <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><strong>Ops! Algo deu errado:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
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

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/layouts/comerciante.blade.php ENDPATH**/ ?>
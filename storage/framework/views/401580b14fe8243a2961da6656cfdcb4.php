<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Marketplace Comerciante</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Padrão de Cores do Marketplace -->
    <link rel="stylesheet" href="<?php echo e(asset('estilos/cores.css')); ?>">
    
    <style>
        /* Layout específico do painel comerciante */
        body {
            background: var(--background);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-comerciante {
            background: var(--primary) !important;
            box-shadow: var(--shadow-md);
            border: none;
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 1.4rem;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }
        
        .empresa-atual {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 6px 12px;
            color: white;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .empresa-atual i {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .dropdown-menu {
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
        }
        
        .dropdown-item {
            color: var(--text-primary);
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--surface-hover);
            color: var(--primary);
        }
        
        .main-content {
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }
        
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .stats-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        
        .stats-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .quick-action-btn {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }
        
        .quick-action-btn:hover {
            background: var(--surface-hover);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
        }
        
        .quick-action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .status-online {
            color: var(--success);
        }
        
        .status-offline {
            color: var(--text-muted);
        }
        
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-ativa {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }
        
        .badge-inativa {
            background: rgba(149, 165, 166, 0.1);
            color: var(--text-muted);
        }
        
        /* Modo escuro específico */
        [data-theme="dark"] .navbar-comerciante {
            background: var(--primary-dark) !important;
        }
        
        [data-theme="dark"] .quick-action-btn:hover {
            background: var(--surface-hover);
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .stats-card {
                padding: 1rem;
            }
            
            .stats-value {
                font-size: 1.5rem;
            }
            
            .quick-action-btn {
                padding: 1rem;
            }
            
            .main-content {
                padding: 1rem 0;
            }
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Navbar Principal -->
    <nav class="navbar navbar-expand-lg navbar-comerciante">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand" href="<?php echo e(route('comerciantes.dashboard')); ?>">
                <i class="fas fa-store me-2"></i>
                Marketplace
                <small class="opacity-75 ms-2" style="font-size: 0.8rem;">Comerciante</small>
            </a>
            
            <!-- Toggle para mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu Principal -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('comerciantes.dashboard') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('comerciantes.dashboard')); ?>">
                            <i class="fas fa-chart-line me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('comerciantes.produtos.*') ? 'active' : ''); ?>" 
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-box me-1"></i>
                            Produtos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.produtos.index')); ?>">
                                <i class="fas fa-list me-2"></i>Todos os Produtos</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.produtos.create')); ?>">
                                <i class="fas fa-plus me-2"></i>Novo Produto</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.produtos.categorias.index')); ?>">
                                <i class="fas fa-tags me-2"></i>Categorias</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.produtos.marcas.index')); ?>">
                                <i class="fas fa-star me-2"></i>Marcas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('comerciantes.empresas.*') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('comerciantes.empresas.index')); ?>">
                            <i class="fas fa-building me-1"></i>
                            Empresas
                        </a>
                    </li>
                    
                    <!-- Sistema Financeiro - Disponível quando empresa está sendo acessada -->
                    <?php if(request()->route('empresa') || session('empresa_atual_id')): ?>
                        <?php
                            $empresaId = request()->route('empresa') ?? session('empresa_atual_id') ?? 1;
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('comerciantes.empresas.*.financeiro.*') ? 'active' : ''); ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-coins me-1"></i>
                                Financeiro
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.financeiro.dashboard', ['empresa' => $empresaId])); ?>">
                                    <i class="fas fa-chart-pie me-2"></i>Dashboard Financeiro</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.financeiro.categorias.index', ['empresa' => $empresaId])); ?>">
                                    <i class="fas fa-folder-open me-2"></i>Categorias de Contas</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.financeiro.contas.index', ['empresa' => $empresaId])); ?>">
                                    <i class="fas fa-list-alt me-2"></i>Plano de Contas</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.financeiro.contas.create', ['empresa' => $empresaId])); ?>">
                                    <i class="fas fa-plus me-2"></i>Nova Conta</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('comerciantes.empresas.financeiro.categorias.create', ['empresa' => $empresaId])); ?>">
                                    <i class="fas fa-plus me-2"></i>Nova Categoria</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('comerciantes.notificacoes.*') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('comerciantes.notificacoes.index')); ?>">
                            <i class="fas fa-bell me-1"></i>
                            Notificações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar me-1"></i>
                            Relatórios
                        </a>
                    </li>
                </ul>
                
                <!-- Indicador da Empresa Atual -->
                <?php if(session('empresa_atual_id')): ?>
                    <?php
                        $empresaAtual = \App\Comerciantes\Models\Empresa::find(session('empresa_atual_id'));
                    ?>
                    <?php if($empresaAtual): ?>
                        <div class="empresa-atual me-3">
                            <i class="fas fa-building"></i>
                            <span><?php echo e(Str::limit($empresaAtual->nome_fantasia ?: $empresaAtual->razao_social ?: 'Empresa', 20)); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Menu do Usuário -->
                <ul class="navbar-nav">
                    <!-- Toggle de Tema -->
                    <li class="nav-item me-2">
                        <button class="theme-toggle" onclick="toggleTheme()" title="Alternar tema">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </li>
                    
                    <!-- Notificações -->
                    <?php echo $__env->make('comerciantes.partials.header-notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    
                    <!-- Menu do Usuário -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2" style="font-size: 1.5rem;"></i>
                            <span><?php echo e(auth('comerciante')->user()?->nome ?? 'Usuário'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user me-2"></i>
                                    Meu Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i>
                                    Configurações
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('comerciantes.logout')); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container-fluid main-content">
        <!-- Alertas de Feedback -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Ops! Temos alguns problemas:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Conteúdo da Página -->
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // CSRF Token para AJAX
        window.Laravel = {
            csrfToken: '<?php echo e(csrf_token()); ?>'
        };
        
        // Sistema de alternância de tema
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Adiciona classe para desabilitar transições temporariamente
            document.body.classList.add('theme-transition-disabled');
            
            // Muda o tema
            html.setAttribute('data-theme', newTheme);
            
            // Atualiza o ícone
            const icon = document.getElementById('theme-icon');
            icon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            
            // Salva no localStorage
            localStorage.setItem('theme', newTheme);
            
            // Remove a classe de transição desabilitada após um breve delay
            setTimeout(() => {
                document.body.classList.remove('theme-transition-disabled');
            }, 100);
        }
        
        // Carrega o tema salvo
        function loadSavedTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            
            html.setAttribute('data-theme', savedTheme);
            icon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
        
        // Detecta preferência do sistema
        function detectSystemTheme() {
            if (!localStorage.getItem('theme')) {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = prefersDark ? 'dark' : 'light';
                localStorage.setItem('theme', theme);
                loadSavedTheme();
            }
        }
        
        // Inicializa o tema quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            detectSystemTheme();
            loadSavedTheme();
        });
        
        // Auto-hide alerts após 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Confirmação para ações destrutivas
        function confirmAction(message = 'Tem certeza que deseja continuar?') {
            return confirm(message);
        }
        
        // Função para mostrar loading
        function showLoading(button) {
            const original = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Carregando...';
            button.disabled = true;
            
            return function() {
                button.innerHTML = original;
                button.disabled = false;
            };
        }
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/layouts/app.blade.php ENDPATH**/ ?>
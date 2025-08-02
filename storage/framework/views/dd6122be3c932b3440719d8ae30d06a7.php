
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo e(url('/admin/dashboard')); ?>">
            <i class="mdi mdi-view-dashboard"></i> Admin Dashboard
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto">
                
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('admin/dashboard') ? 'active' : ''); ?>" 
                       href="<?php echo e(url('/admin/dashboard')); ?>">
                        <i class="mdi mdi-view-dashboard"></i> Dashboard
                    </a>
                </li>

                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo e(request()->is('admin/config*') ? 'active' : ''); ?>" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Configurações
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/config')); ?>">
                            <i class="mdi mdi-settings"></i> Geral
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/config/clientes')); ?>">
                            <i class="mdi mdi-account-group"></i> Clientes
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/config/empresas')); ?>">
                            <i class="mdi mdi-office-building"></i> Empresas
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/config/sistema')); ?>">
                            <i class="mdi mdi-desktop-tower"></i> Sistema
                        </a></li>
                    </ul>
                </li>

                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo e(request()->is('admin/fidelidade*') ? 'active' : ''); ?>" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-star"></i> Fidelidade
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.fidelidade.index')); ?>">
                            <i class="mdi mdi-view-dashboard"></i> Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.fidelidade.clientes')); ?>">
                            <i class="mdi mdi-account-group"></i> Clientes
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.fidelidade.transacoes')); ?>">
                            <i class="mdi mdi-swap-horizontal"></i> Transações
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.fidelidade.cupons')); ?>">
                            <i class="mdi mdi-ticket-percent"></i> Cupons
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.fidelidade.cashback')); ?>">
                            <i class="mdi mdi-cash-multiple"></i> Cashback
                        </a></li>
                    </ul>
                </li>

                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo e(request()->is('admin/pagamentos*') ? 'active' : ''); ?>" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-credit-card"></i> Pagamentos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/pagamentos')); ?>">
                            <i class="mdi mdi-view-dashboard"></i> Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/pagamentos/transacoes')); ?>">
                            <i class="mdi mdi-swap-horizontal"></i> Transações
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/pagamentos/faturas')); ?>">
                            <i class="mdi mdi-file-document"></i> Faturas
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/pagamentos/configuracoes')); ?>">
                            <i class="mdi mdi-cog"></i> Configurações
                        </a></li>
                    </ul>
                </li>

                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo e(request()->is('admin/clientes*') ? 'active' : ''); ?>" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-account-group"></i> Clientes
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/clientes')); ?>">
                            <i class="mdi mdi-view-list"></i> Lista
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/clientes/create')); ?>">
                            <i class="mdi mdi-plus"></i> Novo Cliente
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/clientes/relatorios')); ?>">
                            <i class="mdi mdi-chart-box"></i> Relatórios
                        </a></li>
                    </ul>
                </li>

                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo e(request()->is('admin/relatorios*') ? 'active' : ''); ?>" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-chart-box"></i> Relatórios
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/relatorios/vendas')); ?>">
                            <i class="mdi mdi-trending-up"></i> Vendas
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/relatorios/clientes')); ?>">
                            <i class="mdi mdi-account-group"></i> Clientes
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/relatorios/financeiro')); ?>">
                            <i class="mdi mdi-cash"></i> Financeiro
                        </a></li>
                    </ul>
                </li>
            </ul>

            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-account-circle"></i> Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/perfil')); ?>">
                            <i class="mdi mdi-account"></i> Meu Perfil
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/admin/configuracoes')); ?>">
                            <i class="mdi mdi-cog"></i> Configurações
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo e(url('/logout')); ?>">
                            <i class="mdi mdi-logout"></i> Sair
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>


<nav aria-label="breadcrumb" class="bg-light py-2">
    <div class="container-fluid">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="<?php echo e(url('/admin/dashboard')); ?>">
                    <i class="mdi mdi-home"></i> Home
                </a>
            </li>
            <?php if(request()->is('admin/fidelidade*')): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.fidelidade.index')); ?>">Fidelidade</a>
                </li>
                <?php if(request()->is('admin/fidelidade/clientes')): ?>
                    <li class="breadcrumb-item active">Clientes</li>
                <?php elseif(request()->is('admin/fidelidade/transacoes')): ?>
                    <li class="breadcrumb-item active">Transações</li>
                <?php elseif(request()->is('admin/fidelidade/cupons')): ?>
                    <li class="breadcrumb-item active">Cupons</li>
                <?php endif; ?>
            <?php elseif(request()->is('admin/config*')): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(url('/admin/config')); ?>">Configurações</a>
                </li>
                <?php if(request()->is('admin/config/clientes')): ?>
                    <li class="breadcrumb-item active">Clientes</li>
                <?php endif; ?>
            <?php elseif(request()->is('admin/pagamentos*')): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(url('/admin/pagamentos')); ?>">Pagamentos</a>
                </li>
            <?php elseif(request()->is('admin/clientes*')): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(url('/admin/clientes')); ?>">Clientes</a>
                </li>
            <?php endif; ?>
        </ol>
    </div>
</nav>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/partials/menuConfig.blade.php ENDPATH**/ ?>
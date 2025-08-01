<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('Theme1/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('Theme1/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('Theme1/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('Theme1/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <!-- Fidelidade -->
                @if(Auth::user() && in_array(Auth::user()->tipo_id, [1, 2])) <!-- Admin ou Comerciante -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('fidelidade*') ? 'active' : '' }}" href="#sidebarFidelidade" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('fidelidade*') ? 'true' : 'false' }}" aria-controls="sidebarFidelidade">
                        <i class="ri-star-line"></i> <span data-key="t-fidelidade">Fidelidade</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->is('fidelidade*') ? 'show' : '' }}" id="sidebarFidelidade">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('fidelidade.dashboard') }}" class="nav-link {{ request()->routeIs('fidelidade.dashboard') ? 'active' : '' }}" data-key="t-dashboard-fidelidade"> Dashboard </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('fidelidade.carteiras.index') }}" class="nav-link {{ request()->routeIs('fidelidade.carteiras.*') ? 'active' : '' }}" data-key="t-carteiras"> Carteiras </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('fidelidade.cupons.index') }}" class="nav-link {{ request()->routeIs('fidelidade.cupons.*') ? 'active' : '' }}" data-key="t-cupons"> Cupons </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('fidelidade.regras.index') }}" class="nav-link {{ request()->routeIs('fidelidade.regras.*') ? 'active' : '' }}" data-key="t-regras"> Regras de Cashback </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('fidelidade.relatorios.index') }}" class="nav-link {{ request()->routeIs('fidelidade.relatorios.*') ? 'active' : '' }}" data-key="t-relatorios"> Relatórios </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- E-commerce -->
                @if(Auth::user() && in_array(Auth::user()->tipo_id, [1, 2])) <!-- Admin ou Comerciante -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('ecommerce*') ? 'active' : '' }}" href="#sidebarEcommerce" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('ecommerce*') ? 'true' : 'false' }}" aria-controls="sidebarEcommerce">
                        <i class="ri-shopping-cart-line"></i> <span data-key="t-ecommerce">E-commerce</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->is('ecommerce*') ? 'show' : '' }}" id="sidebarEcommerce">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-products"> Produtos </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-orders"> Pedidos </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-customers"> Clientes </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-shopping-cart"> Carrinho </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-checkout"> Checkout </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Delivery -->
                @if(Auth::user() && in_array(Auth::user()->tipo_id, [1, 4])) <!-- Admin ou Entregador -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('delivery*') ? 'active' : '' }}" href="#sidebarDelivery" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('delivery*') ? 'true' : 'false' }}" aria-controls="sidebarDelivery">
                        <i class="ri-truck-line"></i> <span data-key="t-delivery">Delivery</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->is('delivery*') ? 'show' : '' }}" id="sidebarDelivery">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-deliveries"> Entregas </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-routes"> Rotas </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-tracking"> Rastreamento </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Administração -->
                @if(Auth::user() && Auth::user()->tipo_id == 1) <!-- Apenas Admin -->
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-admin">Administração</span></li>
                
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('admin*') ? 'active' : '' }}" href="#sidebarAdmin" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('admin*') ? 'true' : 'false' }}" aria-controls="sidebarAdmin">
                        <i class="ri-settings-2-line"></i> <span data-key="t-admin">Sistema</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->is('admin*') ? 'show' : '' }}" id="sidebarAdmin">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-users"> Usuários </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-companies"> Empresas </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-permissions"> Permissões </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-config"> Configurações </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('reports*') ? 'active' : '' }}" href="#sidebarReports" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('reports*') ? 'true' : 'false' }}" aria-controls="sidebarReports">
                        <i class="ri-bar-chart-line"></i> <span data-key="t-reports">Relatórios</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->is('reports*') ? 'show' : '' }}" id="sidebarReports">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-sales"> Vendas </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-users-report"> Usuários </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-finance"> Financeiro </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Configurações do Usuário -->
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-user">Usuário</span></li>
                
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#">
                        <i class="ri-user-line"></i> <span data-key="t-profile">Meu Perfil</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#">
                        <i class="ri-settings-3-line"></i> <span data-key="t-settings">Configurações</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <i class="ri-logout-box-line"></i> <span data-key="t-logout">Sair</span>
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->

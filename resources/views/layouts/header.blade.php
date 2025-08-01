<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('Theme1/images/logo-sm.png') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('Theme1/images/logo-dark.png') }}" alt="" height="24">
                        <span class="logo-txt">Marketplace</span>
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('Theme1/images/logo-sm.png') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('Theme1/images/logo-light.png') }}" alt="" height="24">
                        <span class="logo-txt">Marketplace</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                <span class="hamburger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>

            <!-- App Search-->
            <form class="app-search d-none d-md-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Buscar..." autocomplete="off" id="search-options" value="">
                    <span class="mdi mdi-magnify search-widget-icon"></span>
                    <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                </div>
            </form>
        </div>

        <div class="d-flex align-items-center">

            <div class="dropdown d-md-none topbar-head-dropdown header-item">
                <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-search fs-22"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Buscar..." aria-label="Recipient's username">
                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications -->
            <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                    <i class='bx bx-bell fs-22'></i>
                    <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">3<span class="visually-hidden">unread messages</span></span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                    <div class="dropdown-head bg-primary bg-pattern rounded-top">
                        <div class="p-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold text-white"> Notificações </h6>
                                </div>
                                <div class="col-auto dropdown-tabs">
                                    <span class="badge bg-light-subtle text-body fs-13"> 4 Novas</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content position-relative" id="notificationItemsTab">
                        <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                <div class="text-reset notification-item d-block dropdown-item position-relative">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                <i class="bx bx-badge-check"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-2 lh-base">Seu pedido foi <b>confirmado</b></h6>
                                            </a>
                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> Há 3 min</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-15">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check01">
                                                <label class="form-check-label" for="all-notification-check01"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="dropdown ms-sm-3 header-item topbar-user">
                <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-flex align-items-center">
                        <img class="rounded-circle header-profile-user" src="{{ Auth::user()->avatar ?? asset('Theme1/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                        <span class="text-start ms-xl-2">
                            <span class="d-none d-xl-inline-block ms-1 fw-semibold user-name-text">{{ Auth::user()->name ?? 'Usuário' }}</span>
                            <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ Auth::user()->tipo_nome ?? 'Administrador' }}</span>
                        </span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <h6 class="dropdown-header">Bem-vindo {{ Auth::user()->name ?? 'Usuário' }}!</h6>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Perfil</span></a>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Mensagens</span></a>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Agenda</span></a>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Suporte</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Saldo: <b>R$ 5.971,67</b></span></a>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Configurações</span></a>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Tela de Bloqueio</span></a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> 
                        <span class="align-middle" data-key="t-logout">Sair</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Marketplace</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .module-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }
        .module-card:hover {
            transform: translateY(-5px);
            text-decoration: none;
            color: inherit;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .module-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    {{-- Include do Menu Principal --}}
    @include('admin.partials.menuConfig')

    <div class="container-fluid mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="mdi mdi-view-dashboard text-primary"></i> Dashboard Administrativo
                        </h2>
                        <p class="text-muted mb-0">Painel central de gerenciamento do marketplace</p>
                    </div>
                    <div>
                        <button class="btn btn-primary">
                            <i class="mdi mdi-refresh"></i> Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas Gerais -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total de Clientes</h6>
                            <h4 class="mb-0">1,248</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-group text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Vendas Hoje</h6>
                            <h4 class="mb-0">R$ 12.456</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-trending-up text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Transações</h6>
                            <h4 class="mb-0">324</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-swap-horizontal text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Fidelidade</h6>
                            <h4 class="mb-0">R$ 8.900</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-star text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Módulos do Sistema -->
        <div class="row">
            <div class="col-12">
                <h4 class="mb-4">
                    <i class="mdi mdi-apps"></i> Módulos do Sistema
                </h4>
            </div>
            
            <!-- Módulo Fidelidade -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="{{ route('admin.fidelidade.index') }}" class="module-card d-block">
                    <div class="text-center">
                        <i class="mdi mdi-star module-icon text-warning"></i>
                        <h5>Sistema de Fidelidade</h5>
                        <p class="text-muted mb-3">Gerenciar programa de fidelidade, clientes, cashback e cupons</p>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Clientes</small>
                                <div class="fw-bold">1,248</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Cupons</small>
                                <div class="fw-bold">156</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Saldo</small>
                                <div class="fw-bold">R$ 8.9k</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Módulo Pagamentos -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="{{ url('/admin/pagamentos') }}" class="module-card d-block">
                    <div class="text-center">
                        <i class="mdi mdi-credit-card module-icon text-success"></i>
                        <h5>Sistema de Pagamentos</h5>
                        <p class="text-muted mb-3">Transações, faturas, cartões e repasses financeiros</p>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Transações</small>
                                <div class="fw-bold">324</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Faturas</small>
                                <div class="fw-bold">89</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Volume</small>
                                <div class="fw-bold">R$ 45k</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Módulo Clientes -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="{{ url('/admin/clientes') }}" class="module-card d-block">
                    <div class="text-center">
                        <i class="mdi mdi-account-group module-icon text-info"></i>
                        <h5>Gestão de Clientes</h5>
                        <p class="text-muted mb-3">Cadastro, edição e relatórios de clientes</p>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Total</small>
                                <div class="fw-bold">1,248</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Ativos</small>
                                <div class="fw-bold">1,180</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Novos</small>
                                <div class="fw-bold">24</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Módulo Configurações -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="{{ url('/admin/config') }}" class="module-card d-block">
                    <div class="text-center">
                        <i class="mdi mdi-cog module-icon text-secondary"></i>
                        <h5>Configurações</h5>
                        <p class="text-muted mb-3">Configurações gerais, empresas, usuários e sistema</p>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Empresas</small>
                                <div class="fw-bold">5</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Usuários</small>
                                <div class="fw-bold">12</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Status</small>
                                <div class="fw-bold text-success">OK</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Módulo Relatórios -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="{{ url('/admin/relatorios') }}" class="module-card d-block">
                    <div class="text-center">
                        <i class="mdi mdi-chart-box module-icon text-primary"></i>
                        <h5>Relatórios</h5>
                        <p class="text-muted mb-3">Relatórios de vendas, clientes e financeiro</p>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Vendas</small>
                                <div class="fw-bold">R$ 45k</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Comissões</small>
                                <div class="fw-bold">R$ 2.1k</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Margem</small>
                                <div class="fw-bold">18%</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Módulo Suporte -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="{{ url('/admin/suporte') }}" class="module-card d-block">
                    <div class="text-center">
                        <i class="mdi mdi-help-circle module-icon text-danger"></i>
                        <h5>Suporte</h5>
                        <p class="text-muted mb-3">Tickets, logs do sistema e ferramentas de diagnóstico</p>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Tickets</small>
                                <div class="fw-bold">8</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Logs</small>
                                <div class="fw-bold">245</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Uptime</small>
                                <div class="fw-bold text-success">99.9%</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

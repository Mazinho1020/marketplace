<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sistema Financeiro') - Marketplace</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    
    <style>
        .financial-sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: calc(100vh - 56px);
        }
        
        .financial-nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s ease;
        }
        
        .financial-nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .financial-nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .financial-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .financial-card:hover {
            transform: translateY(-5px);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .valor-positivo {
            color: #28a745;
            font-weight: 600;
        }
        
        .valor-negativo {
            color: #dc3545;
            font-weight: 600;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .financial-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .nav-pills .nav-link {
            border-radius: 25px;
            margin: 0 5px;
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        @media (max-width: 768px) {
            .financial-sidebar {
                min-height: auto;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-store me-2"></i>
                Marketplace
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Usuário' }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Sair</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 financial-sidebar">
                <div class="d-flex flex-column p-3">
                    <h5 class="text-white mb-4">
                        <i class="fas fa-chart-line me-2"></i>
                        Sistema Financeiro
                    </h5>
                    
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="{{ route('financial.dashboard') }}" 
                               class="financial-nav-link {{ request()->routeIs('financial.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <span class="text-white-50 small text-uppercase fw-bold px-3">Contas a Pagar</span>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('financial.contas-pagar.index') }}" 
                               class="financial-nav-link {{ request()->routeIs('financial.contas-pagar.*') ? 'active' : '' }}">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Listar Contas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('financial.contas-pagar.create') }}" 
                               class="financial-nav-link">
                                <i class="fas fa-plus me-2"></i>
                                Nova Conta a Pagar
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <span class="text-white-50 small text-uppercase fw-bold px-3">Contas a Receber</span>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('financial.contas-receber.index') }}" 
                               class="financial-nav-link {{ request()->routeIs('financial.contas-receber.*') ? 'active' : '' }}">
                                <i class="fas fa-hand-holding-usd me-2"></i>
                                Listar Contas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('financial.contas-receber.create') }}" 
                               class="financial-nav-link">
                                <i class="fas fa-plus me-2"></i>
                                Nova Conta a Receber
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <span class="text-white-50 small text-uppercase fw-bold px-3">Relatórios</span>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('financial.relatorios.fluxo-caixa') }}" 
                               class="financial-nav-link">
                                <i class="fas fa-chart-area me-2"></i>
                                Fluxo de Caixa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('financial.relatorios.balancete') }}" 
                               class="financial-nav-link">
                                <i class="fas fa-balance-scale me-2"></i>
                                Balancete
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Erro de validação:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/pt-br.min.js"></script>
    
    <!-- Mask JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <script>
        // Configurações globais
        moment.locale('pt-br');
        
        // CSRF Token para Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Configurações do Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        // Máscaras
        $(document).ready(function() {
            $('.money').mask('#.##0,00', {reverse: true});
            $('.cpf').mask('000.000.000-00');
            $('.cnpj').mask('00.000.000/0000-00');
            $('.phone').mask('(00) 00000-0000');
            $('.date').mask('00/00/0000');
            
            // Select2 para todos os selects
            $('select:not(.no-select2)').select2({
                theme: 'bootstrap-5',
                language: 'pt-BR'
            });
        });
        
        // Função para confirmar ações
        function confirmarAcao(mensagem, callback) {
            if (confirm(mensagem)) {
                callback();
            }
        }
        
        // Função para formatar valores monetários
        function formatMoney(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        }
        
        // Função para converter string monetária para número
        function parseMoney(value) {
            return parseFloat(value.replace(/[^\d,]/g, '').replace(',', '.'));
        }
    </script>
    
    @yield('scripts')
</body>
</html>

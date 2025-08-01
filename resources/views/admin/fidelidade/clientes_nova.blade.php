<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes Fidelidade - Admin</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: color 0.3s ease;
        }
        .navbar-custom .nav-link:hover {
            color: white !important;
        }
        .navbar-custom .nav-link.active {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
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
        .stats-card.success {
            border-left-color: #28a745;
        }
        .stats-card.warning {
            border-left-color: #ffc107;
        }
        .stats-card.danger {
            border-left-color: #dc3545;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }
        .btn-action {
            border: none;
            background: none;
            font-size: 1.2rem;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: scale(1.2);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nivel-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .nivel-badge.bronze {
            background-color: #cd7f32;
            color: white;
        }
        .nivel-badge.prata {
            background-color: #c0c0c0;
            color: #333;
        }
        .nivel-badge.ouro {
            background-color: #ffd700;
            color: #333;
        }
        .badge-status {
            font-size: 0.75rem;
        }
        .valor-positivo {
            color: #28a745;
            font-weight: bold;
        }
        .pagination-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar Superior do Admin Fidelidade -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.fidelidade.index') }}">
                <i class="mdi mdi-chart-line"></i> Admin Fidelidade
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFidelidade">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarFidelidade">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.fidelidade.index') }}">
                            <i class="mdi mdi-view-dashboard"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.fidelidade.clientes') }}">
                            <i class="mdi mdi-account-group"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.fidelidade.transacoes') }}">
                            <i class="mdi mdi-swap-horizontal"></i> Transações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.fidelidade.cupons') }}">
                            <i class="mdi mdi-ticket-percent"></i> Cupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.fidelidade.cashback') }}">
                            <i class="mdi mdi-cash-multiple"></i> Cashback
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.fidelidade.relatorios') }}">
                            <i class="mdi mdi-chart-box"></i> Relatórios
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="mdi mdi-arrow-left"></i> Voltar Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="mdi mdi-account-group text-primary"></i> Clientes do Sistema
                        </h2>
                        <p class="text-muted mb-0">Visualização dos clientes cadastrados no programa de fidelidade</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoCliente">
                            <i class="mdi mdi-plus"></i> Novo Cliente
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total de Clientes</h6>
                            <h4 class="mb-0">{{ $stats['total_clientes'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-group text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Clientes Ativos</h6>
                            <h4 class="mb-0">{{ $stats['clientes_ativos'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Clientes Inativos</h6>
                            <h4 class="mb-0">{{ $stats['clientes_inativos'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-clock text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Saldo Total</h6>
                            <h4 class="mb-0">R$ {{ number_format($stats['saldo_total'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-currency-usd text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="table-container">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar cliente...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Todos os Níveis</option>
                        <option value="bronze">Bronze</option>
                        <option value="prata">Prata</option>
                        <option value="ouro">Ouro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="nome">Ordenar por Nome</option>
                        <option value="data">Ordenar por Data</option>
                        <option value="saldo">Ordenar por Saldo</option>
                        <option value="xp">Ordenar por XP</option>
                    </select>
                </div>
            </div>

            <!-- Tabela de Clientes -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>Cliente</th>
                            <th>Empresa</th>
                            <th>Nível</th>
                            <th>Saldo/XP</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar">
                                        <i class="mdi mdi-account-circle text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="ms-2">
                                        <strong>Cliente {{ $cliente->cliente_id }}</strong>
                                        <br><small class="text-muted">ID: {{ $cliente->cliente_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $cliente->empresa_nome ?? 'Geral' }}</span></td>
                            <td>
                                <div class="nivel-badge {{ strtolower($cliente->nivel_atual ?? 'bronze') }}">
                                    @if(($cliente->nivel_atual ?? 'bronze') == 'bronze')
                                        <i class="mdi mdi-medal"></i>
                                    @elseif($cliente->nivel_atual == 'prata')
                                        <i class="mdi mdi-medal"></i>
                                    @elseif($cliente->nivel_atual == 'ouro')
                                        <i class="mdi mdi-medal"></i>
                                    @endif
                                    {{ ucfirst($cliente->nivel_atual ?? 'Bronze') }}
                                </div>
                            </td>
                            <td>
                                <strong class="valor-positivo">{{ $cliente->xp_total ?? 0 }} XP</strong>
                                <br><small class="text-muted">R$ {{ number_format($cliente->saldo_total_disponivel ?? 0, 2, ',', '.') }}</small>
                            </td>
                            <td>
                                @if($cliente->status == 'ativa')
                                    <span class="badge bg-success badge-status">Ativo</span>
                                @elseif($cliente->status == 'inativa')
                                    <span class="badge bg-warning badge-status">Inativo</span>
                                @else
                                    <span class="badge bg-secondary badge-status">{{ ucfirst($cliente->status ?? 'Ativo') }}</span>
                                @endif
                            </td>
                            <td><small>{{ \Carbon\Carbon::parse($cliente->criado_em)->format('d/m/Y') }}</small></td>
                            <td>
                                <button class="btn btn-action btn-outline-primary" title="Ver Perfil">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <button class="btn btn-action btn-outline-warning" title="Editar">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-action btn-outline-success" title="Adicionar Pontos">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                                <button class="btn btn-action btn-outline-info" title="Histórico">
                                    <i class="mdi mdi-history"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="mdi mdi-account-group text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">Nenhum cliente encontrado</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoCliente">
                                    <i class="mdi mdi-plus"></i> Cadastrar Primeiro Cliente
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="row">
                <div class="col-md-6">
                    <div class="pagination-info">
                        <small class="text-muted">
                            Mostrando <span id="showing-from">{{ $clientes->firstItem() ?? 0 }}</span> a <span id="showing-to">{{ $clientes->lastItem() ?? 0 }}</span> de <span id="total-records">{{ $clientes->total() ?? 0 }}</span> registros
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Cliente -->
    <div class="modal fade" id="modalNovoCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-account-plus"></i> Novo Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information"></i>
                        Esta é uma página administrativa apenas para visualização. 
                        Para cadastrar novos clientes, acesse o sistema de gestão completo.
                    </div>
                    <form id="form-novo-cliente">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" class="form-control" name="telefone" placeholder="(11) 99999-9999">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control" name="cpf" placeholder="000.000.000-00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Programa de Fidelidade *</label>
                                <select class="form-select" name="programa_id" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">Programa Geral</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nível Inicial</label>
                                <select class="form-select" name="nivel">
                                    <option value="bronze">Bronze</option>
                                    <option value="prata">Prata</option>
                                    <option value="ouro">Ouro</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="salvarCliente()">
                        <i class="mdi mdi-check"></i> Salvar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function salvarCliente() {
            alert('Funcionalidade em desenvolvimento - dados serão salvos via AJAX');
        }
    </script>
</body>
</html>

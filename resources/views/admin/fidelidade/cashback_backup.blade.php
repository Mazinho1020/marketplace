<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashback Fidelidade - Admin</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" h                    <tbody>
                        @forelse($regras as $regra)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td><strong>{{ $regra->nome }}</strong></td>
                            <td><span class="badge bg-light text-dark">{{ $regra->empresa_nome ?? 'Geral' }}</span></td>
                            <td>
                                @if($regra->tipo_cashback == 'percentual')
                                    <i class="mdi mdi-percent me-1"></i> Percentual
                                @else
                                    <i class="mdi mdi-currency-usd me-1"></i> Fixo
                                @endif
                            </td>
                            <td><span class="discount-badge">{{ $regra->valor_cashback }}{{ $regra->tipo_cashback == 'percentual' ? '%' : '' }}</span></td>
                            <td><small>R$ {{ number_format($regra->valor_minimo ?? 0, 2, ',', '.') }} - R$ {{ number_format($regra->valor_maximo ?? 0, 2, ',', '.') }}</small></td>
                            <td><small>{{ \Carbon\Carbon::parse($regra->criado_em)->format('d/m/Y') }}</small></td>
                            <td>
                                @if($regra->status == 'ativo')
                                    <span class="badge bg-success badge-status">Ativo</span>
                                @elseif($regra->status == 'inativo')
                                    <span class="badge bg-warning badge-status">Inativo</span>
                                @else
                                    <span class="badge bg-secondary badge-status">{{ ucfirst($regra->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-action btn-outline-primary" title="Detalhes">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <button class="btn btn-action btn-outline-warning" title="Editar">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-action btn-outline-info" title="Duplicar">
                                    <i class="mdi mdi-content-copy"></i>
                                </button>
                                <button class="btn btn-action btn-outline-danger" title="Desativar">
                                    <i class="mdi mdi-cancel"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="mdi mdi-currency-usd text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">Nenhuma regra de cashback encontrada</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovaRegra">
                                    <i class="mdi mdi-plus"></i> Criar Primeira Regra
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>e1/css/icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .navbar-custom .navbar-brand {
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .navbar-custom .nav-link:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateY(-2px);
        }
        .navbar-custom .nav-link.active {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid;
        }
        .stats-card.success { border-left-color: #28a745; }
        .stats-card.warning { border-left-color: #ffc107; }
        .stats-card.danger { border-left-color: #dc3545; }
        .stats-card.info { border-left-color: #17a2b8; }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 0.125rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .badge-status {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        .percentage-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navbar Superior do Admin Fidelidade -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="/admin/fidelidade">
                <i class="mdi mdi-heart"></i> Admin Fidelidade
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFidelidade">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarFidelidade">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade">
                            <i class="mdi mdi-view-dashboard"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade/programas">
                            <i class="mdi mdi-gift"></i> Programas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade/clientes">
                            <i class="mdi mdi-account-group"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade/cartoes">
                            <i class="mdi mdi-credit-card"></i> Cartões
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade/transacoes">
                            <i class="mdi mdi-cash-multiple"></i> Transações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/fidelidade/cashback">
                            <i class="mdi mdi-currency-usd"></i> Cashback
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade/cupons">
                            <i class="mdi mdi-ticket-percent"></i> Cupons
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

    <div class="container">
        <!-- Cabeçalho da Página -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="mb-1">
                        <i class="mdi mdi-currency-usd me-2"></i>Regras de Cashback
                    </h2>
                    <p class="text-muted mb-0">Configure e gerencie as regras de cashback dos programas</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaRegra">
                        <i class="mdi mdi-plus"></i> Nova Regra
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="mdi mdi-calculator"></i> Simulador
                    </button>
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Ações
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-export me-1"></i> Exportar</a></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-import me-1"></i> Importar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-history me-1"></i> Histórico</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Regras Ativas</h6>
                            <h4 class="mb-0">{{ $stats['regras_ativas'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cashback Distribuído</h6>
                            <h4 class="mb-0">R$ {{ number_format($stats['cashback_distribuido'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-cash-multiple text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Regras</h6>
                            <h4 class="mb-0">{{ $stats['total_regras'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-percent text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Transações</h6>
                            <h4 class="mb-0">{{ $stats['total_transacoes'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-calendar-month text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="table-container">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar regras..." id="buscar">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filtro-status">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                        <option value="pausado">Pausado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-programa">
                        <option value="">Todos os Programas</option>
                        <option value="1">Programa Padrão</option>
                        <option value="2">Programa VIP</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="percentual">Percentual</option>
                        <option value="fixo">Valor Fixo</option>
                        <option value="escalonado">Escalonado</option>
                    </select>
                </div>
            </div>

            <!-- Tabela de Regras -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Nome da Regra</th>
                            <th>Programa</th>
                            <th>Tipo</th>
                            <th>Valor/Percentual</th>
                            <th>Faixa de Valores</th>
                            <th>Status</th>
                            <th>Vigência</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-regras">
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando regras...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando <span id="showing-from">0</span> a <span id="showing-to">0</span> de <span id="total-records">0</span> registros
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Próximo</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Nova Regra -->
    <div class="modal fade" id="modalNovaRegra" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Nova Regra de Cashback
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-nova-regra">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome da Regra *</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Programa *</label>
                                <select class="form-select" name="programa_id" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">Programa Padrão</option>
                                    <option value="2">Programa VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Cashback *</label>
                                <select class="form-select" name="tipo_cashback" required>
                                    <option value="">Selecione...</option>
                                    <option value="percentual">Percentual (%)</option>
                                    <option value="fixo">Valor Fixo (R$)</option>
                                    <option value="escalonado">Escalonado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor/Percentual *</label>
                                <input type="number" class="form-control" name="valor_cashback" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor Mínimo de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="valor_minimo" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor Máximo de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="valor_maximo" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data Início *</label>
                                <input type="date" class="form-control" name="data_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data Fim</label>
                                <input type="date" class="form-control" name="data_fim">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Limite Mensal</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="limite_mensal" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" name="status" required>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="descricao" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="salvarRegra()">
                        <i class="mdi mdi-check"></i> Salvar Regra
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function salvarRegra() {
            alert('Funcionalidade em desenvolvimento - dados serão salvos via AJAX');
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartões Fidelidade - Admin</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
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
        .card-mini {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            margin: 0.25rem 0;
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
                        <a class="nav-link active" href="/admin/fidelidade/cartoes">
                            <i class="mdi mdi-credit-card"></i> Cartões
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade/transacoes">
                            <i class="mdi mdi-cash-multiple"></i> Transações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fidelidade/cashback">
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
                        <i class="mdi mdi-credit-card me-2"></i>Cartões de Fidelidade
                    </h2>
                    <p class="text-muted mb-0">Gerencie todos os cartões de fidelidade emitidos</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoCartao">
                        <i class="mdi mdi-plus"></i> Novo Cartão
                    </button>
                    <button class="btn btn-outline-warning">
                        <i class="mdi mdi-lock"></i> Bloquear Selecionados
                    </button>
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Ações
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-export me-1"></i> Exportar</a></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-refresh me-1"></i> Reativar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-archive me-1"></i> Arquivados</a></li>
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
                            <h6 class="text-muted mb-1">Cartões Ativos</h6>
                            <h4 class="mb-0" id="cartoes-ativos">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-credit-card-check text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cartões Bloqueados</h6>
                            <h4 class="mb-0" id="cartoes-bloqueados">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-credit-card-off text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cartões Expirados</h6>
                            <h4 class="mb-0" id="cartoes-expirados">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-credit-card-remove text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Novos (30 dias)</h6>
                            <h4 class="mb-0" id="novos-cartoes">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-credit-card-plus text-info" style="font-size: 2rem;"></i>
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
                        <input type="text" class="form-control" placeholder="Buscar por número, cliente..." id="buscar">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filtro-status">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="bloqueado">Bloqueado</option>
                        <option value="expirado">Expirado</option>
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
                    <select class="form-select" id="filtro-expiracao">
                        <option value="">Todas as Expirações</option>
                        <option value="30">Expira em 30 dias</option>
                        <option value="60">Expira em 60 dias</option>
                        <option value="vencidos">Já expirados</option>
                    </select>
                </div>
            </div>

            <!-- Tabela de Cartões -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>Número do Cartão</th>
                            <th>Cliente</th>
                            <th>Programa</th>
                            <th>Data Emissão</th>
                            <th>Data Expiração</th>
                            <th>Status</th>
                            <th>Uso</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-cartoes">
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando cartões...</p>
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

    <!-- Modal Novo Cartão -->
    <div class="modal fade" id="modalNovoCartao" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Novo Cartão de Fidelidade
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-novo-cartao">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cliente *</label>
                                <select class="form-select" name="cliente_id" required>
                                    <option value="">Selecione o cliente...</option>
                                    <option value="1">Maria Silva Santos</option>
                                    <option value="2">João Carlos Oliveira</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número do Cartão</label>
                                <input type="text" class="form-control" name="numero_cartao" placeholder="Será gerado automaticamente">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Expiração *</label>
                                <input type="date" class="form-control" name="data_expiracao" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" name="status" required>
                                    <option value="ativo">Ativo</option>
                                    <option value="bloqueado">Bloqueado</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="observacoes" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="salvarCartao()">
                        <i class="mdi mdi-check"></i> Emitir Cartão
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            carregarEstatisticas();
            carregarCartoes();
        });

        function carregarEstatisticas() {
            document.getElementById('cartoes-ativos').textContent = '1,156';
            document.getElementById('cartoes-bloqueados').textContent = '23';
            document.getElementById('cartoes-expirados').textContent = '45';
            document.getElementById('novos-cartoes').textContent = '67';
        }

        function carregarCartoes() {
            const cartoes = [
                {
                    id: 1,
                    numero: '4532 **** **** 1234',
                    cliente: 'Maria Silva Santos',
                    programa: 'Programa VIP',
                    emissao: '15/07/2024',
                    expiracao: '15/07/2027',
                    status: 'ativo',
                    uso: 'Último uso: Hoje'
                },
                {
                    id: 2,
                    numero: '4532 **** **** 5678',
                    cliente: 'João Carlos Oliveira',
                    programa: 'Programa Padrão',
                    emissao: '20/07/2024',
                    expiracao: '20/07/2027',
                    status: 'ativo',
                    uso: 'Último uso: 2 dias'
                },
                {
                    id: 3,
                    numero: '4532 **** **** 9012',
                    cliente: 'Ana Paula Costa',
                    programa: 'Programa VIP',
                    emissao: '10/06/2024',
                    expiracao: '10/06/2025',
                    status: 'bloqueado',
                    uso: 'Bloqueado há 5 dias'
                },
                {
                    id: 4,
                    numero: '4532 **** **** 3456',
                    cliente: 'Pedro Henrique Lima',
                    programa: 'Programa Padrão',
                    emissao: '05/01/2022',
                    expiracao: '05/01/2025',
                    status: 'expirado',
                    uso: 'Expirado há 3 meses'
                }
            ];

            let html = '';
            cartoes.forEach(cartao => {
                let statusBadge = '';
                let statusClass = '';
                
                switch(cartao.status) {
                    case 'ativo':
                        statusBadge = '<span class="badge bg-success badge-status">Ativo</span>';
                        break;
                    case 'bloqueado':
                        statusBadge = '<span class="badge bg-warning badge-status">Bloqueado</span>';
                        break;
                    case 'expirado':
                        statusBadge = '<span class="badge bg-danger badge-status">Expirado</span>';
                        break;
                }

                html += `
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <div class="card-mini">
                                <i class="mdi mdi-credit-card me-1"></i>
                                ${cartao.numero}
                            </div>
                        </td>
                        <td><strong>${cartao.cliente}</strong></td>
                        <td><span class="badge bg-light text-dark">${cartao.programa}</span></td>
                        <td>${cartao.emissao}</td>
                        <td>${cartao.expiracao}</td>
                        <td>${statusBadge}</td>
                        <td><small class="text-muted">${cartao.uso}</small></td>
                        <td>
                            <button class="btn btn-action btn-outline-primary" title="Detalhes">
                                <i class="mdi mdi-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-warning" title="Bloquear">
                                <i class="mdi mdi-lock"></i>
                            </button>
                            <button class="btn btn-action btn-outline-success" title="Renovar">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" title="Cancelar">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('tabela-cartoes').innerHTML = html;
            document.getElementById('total-records').textContent = cartoes.length;
            document.getElementById('showing-from').textContent = '1';
            document.getElementById('showing-to').textContent = cartoes.length;
        }

        function salvarCartao() {
            alert('Funcionalidade em desenvolvimento - dados serão salvos via AJAX');
        }
    </script>
</body>
</html>

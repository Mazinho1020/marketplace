<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações Fidelidade - Admin</title>
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
        .transaction-type {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .transaction-type.credito {
            background-color: #d4edda;
            color: #155724;
        }
        .transaction-type.debito {
            background-color: #f8d7da;
            color: #721c24;
        }
        .valor-positivo {
            color: #28a745;
            font-weight: 600;
        }
        .valor-negativo {
            color: #dc3545;
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
                        <a class="nav-link active" href="/admin/fidelidade/transacoes">
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
                        <i class="mdi mdi-cash-multiple me-2"></i>Transações de Fidelidade
                    </h2>
                    <p class="text-muted mb-0">Monitore todas as transações, pontos e cashback do sistema</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaTransacao">
                        <i class="mdi mdi-plus"></i> Nova Transação
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="mdi mdi-chart-line"></i> Relatório
                    </button>
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Ações
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-export me-1"></i> Exportar</a></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-file-excel me-1"></i> Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-sync me-1"></i> Sincronizar</a></li>
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
                            <h6 class="text-muted mb-1">Total Transações</h6>
                            <h4 class="mb-0" id="total-transacoes">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-swap-horizontal text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Valor Total</h6>
                            <h4 class="mb-0" id="valor-total">R$ 0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-currency-usd text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pontos Gerados</h6>
                            <h4 class="mb-0" id="pontos-gerados">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-star text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cashback Total</h6>
                            <h4 class="mb-0" id="cashback-total">R$ 0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-cash-multiple text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="table-container">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar transação..." id="buscar">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filtro-tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="compra">Compra</option>
                        <option value="resgate">Resgate</option>
                        <option value="cashback">Cashback</option>
                        <option value="bonus">Bônus</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filtro-programa">
                        <option value="">Todos Programas</option>
                        <option value="1">Programa Padrão</option>
                        <option value="2">Programa VIP</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="data-inicio" placeholder="Data início">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="data-fim" placeholder="Data fim">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-primary w-100" onclick="aplicarFiltros()">
                        <i class="mdi mdi-filter"></i>
                    </button>
                </div>
            </div>

            <!-- Tabela de Transações -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Data/Hora</th>
                            <th>Cliente</th>
                            <th>Programa</th>
                            <th>Tipo</th>
                            <th>Valor Compra</th>
                            <th>Pontos</th>
                            <th>Cashback</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-transacoes">
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando transações...</p>
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

    <!-- Modal Nova Transação -->
    <div class="modal fade" id="modalNovaTransacao" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Nova Transação
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-nova-transacao">
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
                                <label class="form-label">Programa *</label>
                                <select class="form-select" name="programa_id" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">Programa Padrão</option>
                                    <option value="2">Programa VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Transação *</label>
                                <select class="form-select" name="tipo_transacao" required>
                                    <option value="">Selecione...</option>
                                    <option value="compra">Compra</option>
                                    <option value="resgate">Resgate de Pontos</option>
                                    <option value="bonus">Bônus</option>
                                    <option value="cashback">Cashback</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor da Compra *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="valor_compra" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pontos Ganhos</label>
                                <input type="number" class="form-control" name="pontos_ganhos" min="0">
                                <small class="text-muted">Será calculado automaticamente se deixar em branco</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor Cashback</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="valor_cashback" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data da Transação</label>
                                <input type="datetime-local" class="form-control" name="data_transacao">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="processada">Processada</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="descricao" rows="3" placeholder="Detalhes da transação..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="salvarTransacao()">
                        <i class="mdi mdi-check"></i> Salvar Transação
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            carregarEstatisticas();
            carregarTransacoes();
        });

        function carregarEstatisticas() {
            document.getElementById('total-transacoes').textContent = '1,847';
            document.getElementById('valor-total').textContent = 'R$ 45,230.80';
            document.getElementById('pontos-gerados').textContent = '45,678';
            document.getElementById('cashback-total').textContent = 'R$ 2,261.54';
        }

        function carregarTransacoes() {
            const transacoes = [
                {
                    id: 'TXN001',
                    data: '28/07/2024 14:32',
                    cliente: 'Maria Silva Santos',
                    programa: 'Programa VIP',
                    tipo: 'compra',
                    valor_compra: 150.00,
                    pontos: 300,
                    cashback: 7.50,
                    status: 'processada'
                },
                {
                    id: 'TXN002',
                    data: '28/07/2024 11:15',
                    cliente: 'João Carlos Oliveira',
                    programa: 'Programa Padrão',
                    tipo: 'resgate',
                    valor_compra: 0,
                    pontos: -500,
                    cashback: 0,
                    status: 'processada'
                },
                {
                    id: 'TXN003',
                    data: '27/07/2024 16:45',
                    cliente: 'Ana Paula Costa',
                    programa: 'Programa VIP',
                    tipo: 'bonus',
                    valor_compra: 0,
                    pontos: 1000,
                    cashback: 0,
                    status: 'processada'
                },
                {
                    id: 'TXN004',
                    data: '27/07/2024 09:22',
                    cliente: 'Pedro Henrique Lima',
                    programa: 'Programa Padrão',
                    tipo: 'compra',
                    valor_compra: 89.90,
                    pontos: 89,
                    cashback: 4.50,
                    status: 'pendente'
                }
            ];

            let html = '';
            transacoes.forEach(transacao => {
                const tipoClass = transacao.tipo === 'compra' || transacao.tipo === 'bonus' ? 'credito' : 'debito';
                const tipoIcon = {
                    'compra': 'mdi-shopping',
                    'resgate': 'mdi-gift',
                    'bonus': 'mdi-star',
                    'cashback': 'mdi-cash'
                };

                const pontosClass = transacao.pontos >= 0 ? 'valor-positivo' : 'valor-negativo';
                const pontosSignal = transacao.pontos >= 0 ? '+' : '';

                html += `
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td><code>${transacao.id}</code></td>
                        <td><small>${transacao.data}</small></td>
                        <td><strong>${transacao.cliente}</strong></td>
                        <td><span class="badge bg-light text-dark">${transacao.programa}</span></td>
                        <td>
                            <span class="transaction-type ${tipoClass}">
                                <i class="mdi ${tipoIcon[transacao.tipo]} me-1"></i>
                                ${transacao.tipo.charAt(0).toUpperCase() + transacao.tipo.slice(1)}
                            </span>
                        </td>
                        <td><strong>R$ ${transacao.valor_compra.toFixed(2)}</strong></td>
                        <td><span class="${pontosClass}">${pontosSignal}${transacao.pontos} pts</span></td>
                        <td><strong class="valor-positivo">R$ ${transacao.cashback.toFixed(2)}</strong></td>
                        <td>
                            <button class="btn btn-action btn-outline-primary" title="Detalhes">
                                <i class="mdi mdi-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" title="Cancelar">
                                <i class="mdi mdi-cancel"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('tabela-transacoes').innerHTML = html;
            document.getElementById('total-records').textContent = transacoes.length;
            document.getElementById('showing-from').textContent = '1';
            document.getElementById('showing-to').textContent = transacoes.length;
        }

        function aplicarFiltros() {
            // Implementar filtros
            carregarTransacoes();
        }

        function salvarTransacao() {
            alert('Funcionalidade em desenvolvimento - dados serão salvos via AJAX');
        }
    </script>
</body>
</html>

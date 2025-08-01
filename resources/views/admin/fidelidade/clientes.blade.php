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
        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
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
                        <a class="nav-link active" href="/admin/fidelidade/clientes">
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
                        <i class="mdi mdi-account-group me-2"></i>Clientes Fidelidade
                    </h2>
                    <p class="text-muted mb-0">Gerencie todos os clientes cadastrados nos programas de fidelidade</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoCliente">
                        <i class="mdi mdi-plus"></i> Novo Cliente
                    </button>
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Ações
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-export me-1"></i> Exportar</a></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-import me-1"></i> Importar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-email me-1"></i> Enviar Email</a></li>
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
                            <h6 class="text-muted mb-1">Clientes Ativos</h6>
                            <h4 class="mb-0" id="total-ativos">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-check text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Clientes Inativos</h6>
                            <h4 class="mb-0" id="total-inativos">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-off text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Pontos</h6>
                            <h4 class="mb-0" id="total-pontos">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-star text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Novos (30 dias)</h6>
                            <h4 class="mb-0" id="novos-clientes">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-plus text-danger" style="font-size: 2rem;"></i>
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
                        <input type="text" class="form-control" placeholder="Buscar por nome, email..." id="buscar">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filtro-status">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                        <option value="suspenso">Suspenso</option>
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
                    <select class="form-select" id="filtro-ordenacao">
                        <option value="nome">Ordenar por Nome</option>
                        <option value="pontos">Ordenar por Pontos</option>
                        <option value="data">Ordenar por Data</option>
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
                            <th>Email</th>
                            <th>Programa</th>
                            <th>Cartão</th>
                            <th>Pontos</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-clientes">
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando clientes...</p>
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

    <!-- Modal Novo Cliente -->
    <div class="modal fade" id="modalNovoCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Novo Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                                    <option value="1">Programa Padrão</option>
                                    <option value="2">Programa VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" name="status" required>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" name="data_nascimento">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gênero</label>
                                <select class="form-select" name="genero">
                                    <option value="">Não informado</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                    <option value="O">Outro</option>
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
                    <button type="button" class="btn btn-success" onclick="salvarCliente()">
                        <i class="mdi mdi-check"></i> Salvar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            carregarEstatisticas();
            carregarClientes();
        });

        function carregarEstatisticas() {
            // Simulação de dados - substituir por AJAX real
            document.getElementById('total-ativos').textContent = '1,234';
            document.getElementById('total-inativos').textContent = '89';
            document.getElementById('total-pontos').textContent = '45,678';
            document.getElementById('novos-clientes').textContent = '23';
        }

        function carregarClientes() {
            // Simulação de dados da tabela
            const clientes = [
                {
                    id: 1,
                    nome: 'Maria Silva Santos',
                    email: 'maria.silva@email.com',
                    programa: 'Programa VIP',
                    cartao: '**** 1234',
                    pontos: 2456,
                    status: 'ativo',
                    cadastro: '15/07/2024'
                },
                {
                    id: 2,
                    nome: 'João Carlos Oliveira',
                    email: 'joao.oliveira@email.com',
                    programa: 'Programa Padrão',
                    cartao: '**** 5678',
                    pontos: 1890,
                    status: 'ativo',
                    cadastro: '20/07/2024'
                },
                {
                    id: 3,
                    nome: 'Ana Paula Costa',
                    email: 'ana.costa@email.com',
                    programa: 'Programa VIP',
                    cartao: '**** 9012',
                    pontos: 4125,
                    status: 'inativo',
                    cadastro: '25/07/2024'
                },
                {
                    id: 4,
                    nome: 'Pedro Henrique Lima',
                    email: 'pedro.lima@email.com',
                    programa: 'Programa Padrão',
                    cartao: '**** 3456',
                    pontos: 856,
                    status: 'ativo',
                    cadastro: '28/07/2024'
                }
            ];

            let html = '';
            clientes.forEach(cliente => {
                const statusBadge = cliente.status === 'ativo' 
                    ? '<span class="badge bg-success badge-status">Ativo</span>'
                    : '<span class="badge bg-warning badge-status">Inativo</span>';

                const iniciais = cliente.nome.split(' ').map(n => n[0]).join('').substr(0, 2);

                html += `
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2">${iniciais}</div>
                                <div>
                                    <strong>${cliente.nome}</strong>
                                    <br><small class="text-muted">#${cliente.id}</small>
                                </div>
                            </div>
                        </td>
                        <td>${cliente.email}</td>
                        <td><span class="badge bg-light text-dark">${cliente.programa}</span></td>
                        <td><code>${cliente.cartao}</code></td>
                        <td><strong>${cliente.pontos.toLocaleString()} pts</strong></td>
                        <td>${statusBadge}</td>
                        <td>${cliente.cadastro}</td>
                        <td>
                            <button class="btn btn-action btn-outline-info" title="Ver Histórico">
                                <i class="mdi mdi-history"></i>
                            </button>
                            <button class="btn btn-action btn-outline-primary" title="Visualizar">
                                <i class="mdi mdi-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" title="Bloquear">
                                <i class="mdi mdi-block-helper"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('tabela-clientes').innerHTML = html;
            document.getElementById('total-records').textContent = clientes.length;
            document.getElementById('showing-from').textContent = '1';
            document.getElementById('showing-to').textContent = clientes.length;
        }

        function salvarCliente() {
            // Implementar salvamento
            alert('Funcionalidade em desenvolvimento - dados serão salvos via AJAX');
        }
    </script>
</body>
</html>

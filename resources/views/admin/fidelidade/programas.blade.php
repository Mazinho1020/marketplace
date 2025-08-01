<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programas de Fidelidade - Admin</title>
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
                        <a class="nav-link active" href="/admin/fidelidade/programas">
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
                        <i class="mdi mdi-gift me-2"></i>Programas de Fidelidade
                    </h2>
                    <p class="text-muted mb-0">Gerencie todos os programas de fidelidade cadastrados no sistema</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoPrograma">
                        <i class="mdi mdi-plus"></i> Novo Programa
                    </button>
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Ações
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-export me-1"></i> Exportar</a></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-import me-1"></i> Importar</a></li>
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
                            <h6 class="text-muted mb-1">Programas Ativos</h6>
                            <h4 class="mb-0" id="total-ativos">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Programas Inativos</h6>
                            <h4 class="mb-0" id="total-inativos">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-pause-circle text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Clientes</h6>
                            <h4 class="mb-0" id="total-clientes">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-group text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pontos Emitidos</h6>
                            <h4 class="mb-0" id="total-pontos">0</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-star text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="table-container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar programas..." id="buscar">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-status">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                        <option value="suspenso">Suspenso</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-empresa">
                        <option value="">Todas as Empresas</option>
                        <option value="1">Empresa 1</option>
                        <option value="2">Empresa 2</option>
                    </select>
                </div>
            </div>

            <!-- Tabela de Programas -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Nome do Programa</th>
                            <th>Empresa</th>
                            <th>Clientes</th>
                            <th>Pontos por R$</th>
                            <th>Status</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-programas">
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2 text-muted">Carregando programas...</p>
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

    <!-- Modal Novo Programa -->
    <div class="modal fade" id="modalNovoPrograma" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Novo Programa de Fidelidade
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-novo-programa">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome do Programa *</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Empresa *</label>
                                <select class="form-select" name="empresa_id" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">Empresa 1</option>
                                    <option value="2">Empresa 2</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pontos por R$ 1,00 *</label>
                                <input type="number" class="form-control" name="pontos_real" min="0" step="0.01" required>
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
                    <button type="button" class="btn btn-success" onclick="salvarPrograma()">
                        <i class="mdi mdi-check"></i> Salvar Programa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            carregarEstatisticas();
            carregarProgramas();
        });

        function carregarEstatisticas() {
            // Simulação de dados - substituir por AJAX real
            document.getElementById('total-ativos').textContent = '3';
            document.getElementById('total-inativos').textContent = '1';
            document.getElementById('total-clientes').textContent = '1,234';
            document.getElementById('total-pontos').textContent = '45,678';
        }

        function carregarProgramas() {
            // Simulação de dados da tabela
            const programas = [
                {
                    id: 1,
                    nome: 'Programa Padrão',
                    empresa: 'MeuFinanceiro',
                    clientes: 856,
                    pontos_real: '1.00',
                    status: 'ativo',
                    data_criacao: '15/07/2024'
                },
                {
                    id: 2,
                    nome: 'Programa VIP',
                    empresa: 'MeuFinanceiro',
                    clientes: 234,
                    pontos_real: '2.00',
                    status: 'ativo',
                    data_criacao: '20/07/2024'
                },
                {
                    id: 3,
                    nome: 'Programa Especial',
                    empresa: 'MeuFinanceiro',
                    clientes: 144,
                    pontos_real: '1.50',
                    status: 'inativo',
                    data_criacao: '25/07/2024'
                }
            ];

            let html = '';
            programas.forEach(programa => {
                const statusBadge = programa.status === 'ativo' 
                    ? '<span class="badge bg-success badge-status">Ativo</span>'
                    : '<span class="badge bg-warning badge-status">Inativo</span>';

                html += `
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>#${programa.id}</td>
                        <td><strong>${programa.nome}</strong></td>
                        <td>${programa.empresa}</td>
                        <td>${programa.clientes.toLocaleString()}</td>
                        <td>${programa.pontos_real}</td>
                        <td>${statusBadge}</td>
                        <td>${programa.data_criacao}</td>
                        <td>
                            <button class="btn btn-action btn-outline-primary" title="Visualizar">
                                <i class="mdi mdi-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-warning" title="Editar">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" title="Excluir">
                                <i class="mdi mdi-delete"></i>
                            </button>
                            <button class="btn btn-action btn-outline-secondary" title="Mais">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('tabela-programas').innerHTML = html;
            document.getElementById('total-records').textContent = programas.length;
            document.getElementById('showing-from').textContent = '1';
            document.getElementById('showing-to').textContent = programas.length;
        }

        function salvarPrograma() {
            // Implementar salvamento
            alert('Funcionalidade em desenvolvimento - dados serão salvos via AJAX');
        }
    </script>
</body>
</html>

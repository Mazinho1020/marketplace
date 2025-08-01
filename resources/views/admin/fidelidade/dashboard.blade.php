<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Fidelidade - MeuFinanceiro</title>
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
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateY(-2px);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stats-icon {
            font-size: 3rem;
            opacity: 0.3;
            position: absolute;
            right: 1rem;
            top: 1rem;
        }
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 1.5rem;
        }
        .table-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #495057;
        }
        .btn-action {
            padding: 0.375rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 6px;
            font-size: 0.875rem;
        }
        .color-success { color: #28a745; }
        .color-primary { color: #007bff; }
        .color-warning { color: #ffc107; }
        .color-info { color: #17a2b8; }
        .color-danger { color: #dc3545; }
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
                        <a class="nav-link active" href="/admin/fidelidade">
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
        <!-- Estatísticas Gerais -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card position-relative">
                    <i class="mdi mdi-gift stats-icon color-success"></i>
                    <div class="stats-number color-success" id="total-programas">0</div>
                    <div class="stats-label">Programas Ativos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card position-relative">
                    <i class="mdi mdi-account-group stats-icon color-primary"></i>
                    <div class="stats-number color-primary" id="total-clientes">0</div>
                    <div class="stats-label">Clientes Cadastrados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card position-relative">
                    <i class="mdi mdi-credit-card stats-icon color-warning"></i>
                    <div class="stats-number color-warning" id="total-cartoes">0</div>
                    <div class="stats-label">Cartões Emitidos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card position-relative">
                    <i class="mdi mdi-currency-usd stats-icon color-info"></i>
                    <div class="stats-number color-info" id="total-cashback">R$ 0</div>
                    <div class="stats-label">Cashback Total</div>
                </div>
            </div>
        </div>

        <!-- Visão Geral das Tabelas -->
        <div class="row">
            <div class="col-md-6">
                <div class="table-card">
                    <div class="table-title">
                        <i class="mdi mdi-gift"></i> Programas de Fidelidade
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="programas-table">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Carregando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="/admin/fidelidade/programas" class="btn btn-sm btn-success">
                            <i class="mdi mdi-plus"></i> Gerenciar Programas
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-card">
                    <div class="table-title">
                        <i class="mdi mdi-account-group"></i> Clientes Recentes
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Pontos</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="clientes-table">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Carregando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="/admin/fidelidade/clientes" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-eye"></i> Ver Todos Clientes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="table-card">
                    <div class="table-title">
                        <i class="mdi mdi-cash-multiple"></i> Transações Recentes
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Pontos</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody id="transacoes-table">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Carregando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="/admin/fidelidade/transacoes" class="btn btn-sm btn-info">
                            <i class="mdi mdi-eye"></i> Ver Todas Transações
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-card">
                    <div class="table-title">
                        <i class="mdi mdi-ticket-percent"></i> Cupons Ativos
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Desconto</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="cupons-table">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Carregando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="/admin/fidelidade/cupons" class="btn btn-sm btn-warning">
                            <i class="mdi mdi-plus"></i> Gerenciar Cupons
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Carregar dados das tabelas via AJAX
        document.addEventListener('DOMContentLoaded', function() {
            carregarEstatisticas();
            carregarTabelasPrevias();
        });

        function carregarEstatisticas() {
            // Simulação - substituir por AJAX real
            document.getElementById('total-programas').textContent = '5';
            document.getElementById('total-clientes').textContent = '1,234';
            document.getElementById('total-cartoes').textContent = '987';
            document.getElementById('total-cashback').textContent = 'R$ 15,430';
        }

        function carregarTabelasPrevias() {
            // Programas
            document.getElementById('programas-table').innerHTML = `
                <tr>
                    <td>1</td>
                    <td>Programa Padrão</td>
                    <td><span class="badge bg-success">Ativo</span></td>
                    <td>
                        <button class="btn btn-action btn-outline-primary btn-sm">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button class="btn btn-action btn-outline-warning btn-sm">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                    </td>
                </tr>
            `;

            // Clientes
            document.getElementById('clientes-table').innerHTML = `
                <tr>
                    <td>1</td>
                    <td>João Silva</td>
                    <td>450</td>
                    <td>
                        <button class="btn btn-action btn-outline-primary btn-sm">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    </td>
                </tr>
            `;

            // Transações
            document.getElementById('transacoes-table').innerHTML = `
                <tr>
                    <td>1</td>
                    <td>João Silva</td>
                    <td>+50</td>
                    <td>Hoje</td>
                </tr>
            `;

            // Cupons
            document.getElementById('cupons-table').innerHTML = `
                <tr>
                    <td>1</td>
                    <td>DESC10</td>
                    <td>10%</td>
                    <td><span class="badge bg-success">Ativo</span></td>
                </tr>
            `;
        }
    </script>
</body>
</html>

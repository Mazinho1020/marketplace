<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupons Fidelidade - Admin</title>
    <link rel="stylesheet" href="/Theme                        <div>
                            <h6 class="text-muted mb-1">Cupons Utilizados</h6>
                            <h4 class="mb-0">{{ $stats['cupons_utilizados'] ?? 0 }}</h4>
                        </div>s/bootstrap.min.css">
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
        .coupon-code {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .discount-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
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
                        <a class="nav-link" href="/admin/fidelidade/cashback">
                            <i class="mdi mdi-currency-usd"></i> Cashback
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/fidelidade/cupons">
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
                        <i class="mdi mdi-ticket-percent me-2"></i>Cupons de Desconto
                    </h2>
                    <p class="text-muted mb-0">Gerencie cupons de desconto e promoções especiais</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoCupom">
                        <i class="mdi mdi-plus"></i> Novo Cupom
                    </button>
                    <button class="btn btn-outline-warning">
                        <i class="mdi mdi-email"></i> Enviar Cupons
                    </button>
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Ações
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-export me-1"></i> Exportar</a></li>
                        <li><a class="dropdown-item" href="#"><i class="mdi mdi-content-copy me-1"></i> Duplicar</a></li>
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
                            <h6 class="text-muted mb-1">Cupons Ativos</h6>
                            <h4 class="mb-0">{{ $stats['cupons_ativos'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-ticket-confirmation text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total de Cupons</h6>
                            <h4 class="mb-0">{{ $stats['total_cupons'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-ticket-percent text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cupons Inativos</h6>
                            <h4 class="mb-0">{{ $stats['cupons_inativos'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-ticket-outline text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cupons Usados</h6>
                            <h4 class="mb-0">{{ $stats['cupons_usados'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-all text-danger" style="font-size: 2rem;"></i>
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
                        <input type="text" class="form-control" placeholder="Buscar cupom..." id="buscar">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filtro-status">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="usado">Usado</option>
                        <option value="expirado">Expirado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filtro-tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="percentual">Percentual</option>
                        <option value="fixo">Valor Fixo</option>
                        <option value="frete">Frete Grátis</option>
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

            <!-- Tabela de Cupons -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Desconto</th>
                            <th>Cliente</th>
                            <th>Usos</th>
                            <th>Validade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cupons as $cupom)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td><span class="coupon-code">{{ $cupom->codigo }}</span></td>
                            <td><strong>{{ $cupom->nome }}</strong></td>
                            <td>
                                @if($cupom->tipo == 'desconto_sacola')
                                    <i class="mdi mdi-percent me-1"></i> Desconto
                                @elseif($cupom->tipo == 'frete_gratis')
                                    <i class="mdi mdi-truck-delivery me-1"></i> Frete
                                @else
                                    <i class="mdi mdi-tag me-1"></i> {{ ucfirst($cupom->tipo) }}
                                @endif
                            </td>
                            <td>
                                @if($cupom->percentual_desconto)
                                    <span class="discount-badge">{{ $cupom->percentual_desconto }}%</span>
                                @elseif($cupom->valor_desconto)
                                    <span class="discount-badge">R$ {{ number_format($cupom->valor_desconto, 2, ',', '.') }}</span>
                                @else
                                    <span class="discount-badge">-</span>
                                @endif
                            </td>
                            <td><small>{{ $cupom->empresa_nome ?? 'Geral' }}</small></td>
                            <td><small>{{ $cupom->quantidade_usada ?? 0 }}/{{ $cupom->quantidade_maxima_uso ?? '∞' }}</small></td>
                            <td><small>{{ $cupom->data_fim ? \Carbon\Carbon::parse($cupom->data_fim)->format('d/m/Y') : 'Sem limite' }}</small></td>
                            <td>
                                @if($cupom->status == 'ativo')
                                    <span class="badge bg-success badge-status">Ativo</span>
                                @elseif($cupom->status == 'inativo')
                                    <span class="badge bg-warning badge-status">Inativo</span>
                                @elseif($cupom->status == 'expirado')
                                    <span class="badge bg-danger badge-status">Expirado</span>
                                @else
                                    <span class="badge bg-secondary badge-status">{{ ucfirst($cupom->status) }}</span>
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
                            <td colspan="10" class="text-center py-4">
                                <i class="mdi mdi-ticket-percent-outline text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">Nenhum cupom encontrado</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoCupom">
                                    <i class="mdi mdi-plus"></i> Criar Primeiro Cupom
                                </button>
                            </td>
                        </tr>
                        @endforelse
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

    <!-- Modal Novo Cupom -->
    <div class="modal fade" id="modalNovoCupom" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus"></i> Novo Cupom de Desconto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-novo-cupom">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código do Cupom *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="codigo" required style="text-transform: uppercase;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="gerarCodigo()">
                                        <i class="mdi mdi-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Descrição *</label>
                                <input type="text" class="form-control" name="descricao" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Desconto *</label>
                                <select class="form-select" name="tipo_desconto" required>
                                    <option value="">Selecione...</option>
                                    <option value="percentual">Percentual (%)</option>
                                    <option value="fixo">Valor Fixo (R$)</option>
                                    <option value="frete">Frete Grátis</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor do Desconto *</label>
                                <input type="number" class="form-control" name="valor_desconto" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cliente Específico</label>
                                <select class="form-select" name="cliente_id">
                                    <option value="">Todos os clientes</option>
                                    <option value="1">Maria Silva Santos</option>
                                    <option value="2">João Carlos Oliveira</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Programa</label>
                                <select class="form-select" name="programa_id">
                                    <option value="">Todos os programas</option>
                                    <option value="1">Programa Padrão</option>
                                    <option value="2">Programa VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor Mínimo de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="valor_minimo" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Limite de Usos</label>
                                <input type="number" class="form-control" name="limite_usos" min="1" placeholder="Ilimitado">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Início *</label>
                                <input type="date" class="form-control" name="data_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Expiração *</label>
                                <input type="date" class="form-control" name="data_expiracao" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" name="status" required>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="enviar_email" id="enviar_email">
                                    <label class="form-check-label" for="enviar_email">
                                        Enviar por email
                                    </label>
                                </div>
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
                    <button type="button" class="btn btn-success" onclick="salvarCupom()">
                        <i class="mdi mdi-check"></i> Criar Cupom
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function gerarCodigo() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 8; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.querySelector('input[name="codigo"]').value = result;
        }

        function aplicarFiltros() {
            // Aplicar filtros na interface
            console.log('Filtros aplicados');
        }

        function salvarCupom() {
            alert('Funcionalidade em desenvolvimento - dados serão salvos via AJAX');
        }
    </script>
</body>
</html>

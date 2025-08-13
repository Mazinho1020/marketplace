@extends('comerciantes.layouts.app')

@section('title', 'Sistema Financeiro')

@push('styles')
<style>
    .dashboard-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin-bottom: 15px;
    }
    
    .card-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .card-icon.success { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .card-icon.warning { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .card-icon.info { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .card-icon.danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .card-icon.secondary { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
    }
    
    .quick-action-btn {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        text-decoration: none;
        color: #495057;
        transition: all 0.3s ease;
        display: block;
        text-align: center;
    }
    
    .quick-action-btn:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
        text-decoration: none;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #6c757d;
    }
    
    .table-modern {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .pagination-info {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .pagination-links .pagination {
        margin: 0;
    }
    
    .pagination .page-link {
        color: #667eea;
        border-color: #e9ecef;
        border-radius: 8px !important;
        margin: 0 2px;
    }
    
    .pagination .page-link:hover {
        color: #5a67d8;
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">💰 Sistema Financeiro</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('comerciantes.dashboard.empresa', $empresa) }}">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Financeiro</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-chart-pie"></i> Dashboard Financeiro
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Acesso Rápido -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body text-center">
                    <div class="card-icon primary mx-auto">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h5 class="card-title">Contas a Pagar</h5>
                    <p class="card-text text-muted">Gerencie suas despesas e pagamentos</p>
                    <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Acessar
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body text-center">
                    <div class="card-icon success mx-auto">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h5 class="card-title">Contas a Receber</h5>
                    <p class="card-text text-muted">Controle seus recebimentos</p>
                    <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" class="btn btn-success">
                        <i class="fas fa-arrow-right"></i> Acessar
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body text-center">
                    <div class="card-icon warning mx-auto">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <h5 class="card-title">Plano de Contas</h5>
                    <p class="card-text text-muted">Configure suas contas gerenciais</p>
                    <a href="#contas-section" class="btn btn-warning scroll-to">
                        <i class="fas fa-arrow-down"></i> Ver Abaixo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body text-center">
                    <div class="card-icon info mx-auto">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="card-title">Relatórios</h5>
                    <p class="card-text text-muted">Análises e demonstrativos</p>
                    <a href="#" class="btn btn-info">
                        <i class="fas fa-arrow-right"></i> Em Breve
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-pagar.create', $empresa) }}" class="quick-action-btn">
                                <i class="fas fa-plus-circle fa-2x text-danger mb-2"></i>
                                <div class="fw-bold">Nova Conta a Pagar</div>
                                <small class="text-muted">Registrar despesa</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.create', $empresa) }}" class="quick-action-btn">
                                <i class="fas fa-plus-circle fa-2x text-success mb-2"></i>
                                <div class="fw-bold">Nova Conta a Receber</div>
                                <small class="text-muted">Registrar receita</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('comerciantes.empresas.financeiro.contas.create', $empresa) }}" class="quick-action-btn">
                                <i class="fas fa-sitemap fa-2x text-primary mb-2"></i>
                                <div class="fw-bold">Nova Conta Gerencial</div>
                                <small class="text-muted">Estruturar plano</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="quick-action-btn">
                                <i class="fas fa-file-export fa-2x text-info mb-2"></i>
                                <div class="fw-bold">Exportar Dados</div>
                                <small class="text-muted">Relatórios PDF/Excel</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="quick-action-btn">
                                <i class="fas fa-cog fa-2x text-secondary mb-2"></i>
                                <div class="fw-bold">Configurações</div>
                                <small class="text-muted">Ajustes do sistema</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="quick-action-btn">
                                <i class="fas fa-question-circle fa-2x text-warning mb-2"></i>
                                <div class="fw-bold">Ajuda</div>
                                <small class="text-muted">Suporte e tutoriais</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção do Plano de Contas -->
    <div id="contas-section" class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-transparent border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-sitemap text-primary"></i> Plano de Contas Gerenciais
                            </h5>
                            <small class="text-muted">Estrutura organizacional das contas financeiras</small>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('comerciantes.empresas.financeiro.contas.create', $empresa) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nova Conta
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($contas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern table-hover">
                                <thead>
                                    <tr>
                                        <th width="10%">Código</th>
                                        <th width="35%">Nome da Conta</th>
                                        <th width="20%">Categoria</th>
                                        <th width="15%">Natureza</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contas as $conta)
                                    <tr>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $conta->codigo ?: 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $conta->nome }}</div>
                                                @if($conta->descricao)
                                                    <small class="text-muted">{{ Str::limit($conta->descricao, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($conta->categoria)
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $conta->categoria->nome }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark rounded-pill">Sem categoria</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($conta->natureza)
                                                <span class="badge bg-{{ $conta->natureza->color() }} rounded-pill">
                                                    <i class="fas fa-{{ $conta->natureza->icon() }}"></i>
                                                    {{ $conta->natureza->label() }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">Não definida</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($conta->ativo)
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fas fa-check"></i> Ativo
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">
                                                    <i class="fas fa-pause"></i> Inativo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('comerciantes.empresas.financeiro.contas.show', [$empresa, $conta->id]) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('comerciantes.empresas.financeiro.contas.edit', [$empresa, $conta->id]) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        @if($contas instanceof \Illuminate\Pagination\LengthAwarePaginator && $contas->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="pagination-info">
                                    <span class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Mostrando {{ $contas->firstItem() }} até {{ $contas->lastItem() }} 
                                        de {{ $contas->total() }} contas
                                    </span>
                                </div>
                                <div class="pagination-links">
                                    {{ $contas->links() }}
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Estado Vazio -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-sitemap fa-5x text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">Nenhuma conta cadastrada</h4>
                            <p class="text-muted mb-4">
                                Comece criando seu plano de contas para organizar melhor suas finanças.
                            </p>
                            <a href="{{ route('comerciantes.empresas.financeiro.contas.create', $empresa) }}" 
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-plus"></i> Criar Primeira Conta
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll suave para seções
    document.querySelectorAll('.scroll-to').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Tooltip para botões
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush









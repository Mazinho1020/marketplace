{{-- Menu Secundário Específico do Módulo Pagamentos --}}
<div class="bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light py-2">
            <div class="navbar-nav me-auto">
                <a class="nav-link {{ request()->is('admin/pagamentos') && !request()->is('admin/pagamentos/*') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/pagamentos') }}">
                    <i class="mdi mdi-view-dashboard text-primary"></i> Dashboard
                </a>
                
                <a class="nav-link {{ request()->is('admin/pagamentos/transacoes') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/pagamentos/transacoes') }}">
                    <i class="mdi mdi-swap-horizontal text-success"></i> Transações
                </a>
                
                <a class="nav-link {{ request()->is('admin/pagamentos/faturas') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/pagamentos/faturas') }}">
                    <i class="mdi mdi-file-document text-info"></i> Faturas
                </a>
                
                <a class="nav-link {{ request()->is('admin/pagamentos/cartoes') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/pagamentos/cartoes') }}">
                    <i class="mdi mdi-credit-card text-warning"></i> Cartões
                </a>
                
                <a class="nav-link {{ request()->is('admin/pagamentos/repasses') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/pagamentos/repasses') }}">
                    <i class="mdi mdi-bank-transfer text-danger"></i> Repasses
                </a>
                
                <a class="nav-link {{ request()->is('admin/pagamentos/configuracoes') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/pagamentos/configuracoes') }}">
                    <i class="mdi mdi-cog text-secondary"></i> Config
                </a>
                
                <a class="nav-link {{ request()->is('admin/pagamentos/relatorios') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/pagamentos/relatorios') }}">
                    <i class="mdi mdi-chart-box text-dark"></i> Relatórios
                </a>
            </div>
            
            {{-- Ações Rápidas Pagamentos --}}
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn btn-outline-success btn-sm" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-plus"></i> Ações
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalNovaTransacao">
                            <i class="mdi mdi-swap-horizontal-bold"></i> Nova Transação
                        </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalNovaFatura">
                            <i class="mdi mdi-file-plus"></i> Nova Fatura
                        </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEstorno">
                            <i class="mdi mdi-undo"></i> Processar Estorno
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="sincronizarPagamentos()">
                            <i class="mdi mdi-sync"></i> Sincronizar
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarFinanceiro()">
                            <i class="mdi mdi-download"></i> Exportar
                        </a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

{{-- CSS específico para o menu pagamentos --}}
<style>
.nav-link.active {
    background-color: rgba(40, 167, 69, 0.1);
    border-radius: 5px;
    color: #28a745 !important;
}
.nav-link:hover {
    background-color: rgba(0,0,0,0.05);
    border-radius: 5px;
}
</style>

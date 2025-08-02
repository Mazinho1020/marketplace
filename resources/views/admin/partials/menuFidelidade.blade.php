{{-- Menu Secundário Específico do Módulo Fidelidade --}}
<div class="bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light py-2">
            <div class="navbar-nav me-auto">
                <a class="nav-link {{ request()->is('admin/fidelidade') || request()->is('admin/fidelidade/index') ? 'active fw-bold' : '' }}" 
                   href="{{ route('admin.fidelidade.index') }}">
                    <i class="mdi mdi-view-dashboard text-primary"></i> Dashboard
                </a>
                
                <a class="nav-link {{ request()->is('admin/fidelidade/clientes') ? 'active fw-bold' : '' }}" 
                   href="{{ route('admin.fidelidade.clientes') }}">
                    <i class="mdi mdi-account-group text-success"></i> Clientes
                    @if(isset($stats['total_clientes']))
                        <span class="badge bg-success ms-1">{{ $stats['total_clientes'] }}</span>
                    @endif
                </a>
                
                <a class="nav-link {{ request()->is('admin/fidelidade/transacoes') ? 'active fw-bold' : '' }}" 
                   href="{{ route('admin.fidelidade.transacoes') }}">
                    <i class="mdi mdi-swap-horizontal text-info"></i> Transações
                </a>
                
                <a class="nav-link {{ request()->is('admin/fidelidade/cupons') ? 'active fw-bold' : '' }}" 
                   href="{{ route('admin.fidelidade.cupons') }}">
                    <i class="mdi mdi-ticket-percent text-warning"></i> Cupons
                </a>
                
                <a class="nav-link {{ request()->is('admin/fidelidade/cashback') ? 'active fw-bold' : '' }}" 
                   href="{{ route('admin.fidelidade.cashback') }}">
                    <i class="mdi mdi-cash-multiple text-danger"></i> Cashback
                </a>
                
                <a class="nav-link {{ request()->is('admin/fidelidade/relatorios') ? 'active fw-bold' : '' }}" 
                   href="{{ route('admin.fidelidade.relatorios') }}">
                    <i class="mdi mdi-chart-box text-secondary"></i> Relatórios
                </a>
                
                <a class="nav-link {{ request()->is('admin/fidelidade/configuracoes') ? 'active fw-bold' : '' }}" 
                   href="{{ route('admin.fidelidade.configuracoes') }}">
                    <i class="mdi mdi-cog text-dark"></i> Config
                </a>
            </div>
            
            {{-- Ações Rápidas --}}
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn btn-outline-primary btn-sm" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-plus"></i> Ações
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalNovoCliente">
                            <i class="mdi mdi-account-plus"></i> Novo Cliente
                        </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalNovoCupom">
                            <i class="mdi mdi-ticket-plus"></i> Novo Cupom
                        </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalAjustarSaldo">
                            <i class="mdi mdi-cash-plus"></i> Ajustar Saldo
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarDados()">
                            <i class="mdi mdi-download"></i> Exportar Dados
                        </a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

{{-- CSS específico para o menu fidelidade --}}
<style>
.nav-link.active {
    background-color: rgba(102, 126, 234, 0.1);
    border-radius: 5px;
    color: #667eea !important;
}
.nav-link:hover {
    background-color: rgba(0,0,0,0.05);
    border-radius: 5px;
}
.badge {
    font-size: 0.6rem;
}
</style>

{{-- Menu Secundário Específico do Módulo Configurações --}}
<div class="bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light py-2">
            <div class="navbar-nav me-auto">
                <a class="nav-link {{ request()->is('admin/config') && !request()->is('admin/config/*') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/config') }}">
                    <i class="mdi mdi-view-dashboard text-primary"></i> Geral
                </a>
                
                <a class="nav-link {{ request()->is('admin/config/clientes') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/config/clientes') }}">
                    <i class="mdi mdi-account-group text-success"></i> Clientes
                </a>
                
                <a class="nav-link {{ request()->is('admin/config/empresas') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/config/empresas') }}">
                    <i class="mdi mdi-office-building text-info"></i> Empresas
                </a>
                
                <a class="nav-link {{ request()->is('admin/config/sistema') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/config/sistema') }}">
                    <i class="mdi mdi-desktop-tower text-warning"></i> Sistema
                </a>
                
                <a class="nav-link {{ request()->is('admin/config/usuarios') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/config/usuarios') }}">
                    <i class="mdi mdi-account-multiple text-danger"></i> Usuários
                </a>
                
                <a class="nav-link {{ request()->is('admin/config/seguranca') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/config/seguranca') }}">
                    <i class="mdi mdi-shield-check text-secondary"></i> Segurança
                </a>
                
                <a class="nav-link {{ request()->is('admin/config/backup') ? 'active fw-bold' : '' }}" 
                   href="{{ url('/admin/config/backup') }}">
                    <i class="mdi mdi-backup-restore text-dark"></i> Backup
                </a>
            </div>
            
            {{-- Ações Rápidas Config --}}
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn btn-outline-secondary btn-sm" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Ferramentas
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="limparCache()">
                            <i class="mdi mdi-cached"></i> Limpar Cache
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="executarMigracoes()">
                            <i class="mdi mdi-database-sync"></i> Executar Migrações
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="verificarSistema()">
                            <i class="mdi mdi-check-network"></i> Verificar Sistema
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarConfiguracoes()">
                            <i class="mdi mdi-download"></i> Exportar Config
                        </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalImportarConfig">
                            <i class="mdi mdi-upload"></i> Importar Config
                        </a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

{{-- CSS específico para o menu config --}}
<style>
.nav-link.active {
    background-color: rgba(108, 117, 125, 0.1);
    border-radius: 5px;
    color: #6c757d !important;
}
.nav-link:hover {
    background-color: rgba(0,0,0,0.05);
    border-radius: 5px;
}
</style>

@extends('layouts.admin')

@section('title', 'Sistema de Fidelidade Integrado')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        Sistema de Fidelidade Integrado com Sucesso!
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Integração Completa!</strong> Todos os links do sistema de fidelidade foram integrados ao novo menu administrativo.
                    </div>

                    <h5>Páginas de Fidelidade Disponíveis:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="list-group">
                                <a href="{{ route('admin.fidelidade.dashboard') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-chart-line me-2 text-primary"></i>
                                            Dashboard Fidelidade
                                        </h6>
                                        <small class="text-success">Ativo</small>
                                    </div>
                                    <p class="mb-1">Visão geral das métricas e estatísticas do sistema de fidelidade</p>
                                </a>
                                
                                <a href="{{ route('admin.fidelidade.clientes') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-users me-2 text-primary"></i>
                                            Clientes
                                        </h6>
                                        <small class="text-success">Ativo</small>
                                    </div>
                                    <p class="mb-1">Gestão de clientes e carteiras de fidelidade</p>
                                </a>
                                
                                <a href="{{ route('admin.fidelidade.transacoes') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-exchange-alt me-2 text-primary"></i>
                                            Transações
                                        </h6>
                                        <small class="text-success">Ativo</small>
                                    </div>
                                    <p class="mb-1">Histórico e gerenciamento de transações de cashback</p>
                                </a>
                                
                                <a href="{{ route('admin.fidelidade.cupons') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-ticket-alt me-2 text-primary"></i>
                                            Cupons
                                        </h6>
                                        <small class="text-success">Ativo</small>
                                    </div>
                                    <p class="mb-1">Gestão de cupons de desconto e promoções</p>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="list-group">
                                <a href="{{ route('admin.fidelidade.cashback') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-gift me-2 text-primary"></i>
                                            Cashback
                                        </h6>
                                        <small class="text-success">Ativo</small>
                                    </div>
                                    <p class="mb-1">Configuração de regras e programas de cashback</p>
                                </a>
                                
                                <a href="{{ route('admin.fidelidade.relatorios') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                                            Relatórios
                                        </h6>
                                        <small class="text-success">Ativo</small>
                                    </div>
                                    <p class="mb-1">Relatórios detalhados do sistema de fidelidade</p>
                                </a>
                                
                                <a href="{{ route('admin.fidelidade.configuracoes') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-cogs me-2 text-primary"></i>
                                            Configurações
                                        </h6>
                                        <small class="text-success">Ativo</small>
                                    </div>
                                    <p class="mb-1">Configurações gerais do sistema de fidelidade</p>
                                </a>
                                
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-link me-2 text-success"></i>
                                            Layout Unificado
                                        </h6>
                                        <small class="text-success">Integrado</small>
                                    </div>
                                    <p class="mb-1">Todas as páginas agora usam o novo layout administrativo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-route me-2"></i>
                                    Rotas
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Todas as rotas de fidelidade foram migradas para o arquivo <code>admin.php</code> e estão funcionando corretamente.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <i class="fas fa-eye me-2"></i>
                                    Views
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Todas as views foram atualizadas para usar o layout <code>layouts.admin</code> com breadcrumbs e navegação integrada.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <i class="fas fa-bars me-2"></i>
                                    Menu
                                </div>
                                <div class="card-body">
                                    <p class="card-text">O menu lateral agora inclui todos os links de fidelidade organizados em submenus expansíveis.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> O sistema mantém compatibilidade com as rotas antigas através de redirecionamentos automáticos.
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary me-3">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Voltar ao Dashboard
                        </a>
                        <a href="{{ route('admin.fidelidade.dashboard') }}" class="btn btn-success">
                            <i class="fas fa-star me-2"></i>
                            Acessar Dashboard Fidelidade
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Destacar o item de menu ativo
    document.addEventListener('DOMContentLoaded', function() {
        // Expandir o menu de fidelidade
        const fidelidadeMenu = document.getElementById('fidelidadeSubmenu');
        if (fidelidadeMenu) {
            fidelidadeMenu.classList.add('show');
            const fidelidadeToggle = document.querySelector('[href="#fidelidadeSubmenu"]');
            if (fidelidadeToggle) {
                fidelidadeToggle.setAttribute('aria-expanded', 'true');
                fidelidadeToggle.classList.add('active');
            }
        }
        
        // Notificação de sucesso
        setTimeout(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Sistema de Fidelidade integrado com sucesso!', 'Integração Completa');
            }
        }, 1000);
    });
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Configurações Fidelidade')

@php
    $pageTitle = 'Configurações Fidelidade';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Fidelidade', 'url' => route('admin.fidelidade.dashboard')],
        ['title' => 'Configurações', 'url' => '#']
    ];
@endphp

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="mdi mdi-cog text-primary"></i> Configurações do Sistema de Fidelidade
                </h2>
                <p class="text-muted mb-0">Visualização das configurações ativas do programa de fidelidade</p>
            </div>
            <div>
                <div class="alert alert-info mb-0 py-2 px-3">
                    <i class="mdi mdi-information"></i> Modo somente leitura
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total de Configurações</h6>
                    <h4 class="mb-0">{{ $stats['total_configuracoes'] ?? 12 }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-cog text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Configurações Ativas</h6>
                    <h4 class="mb-0">{{ $stats['configuracoes_ativas'] ?? 8 }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stats-card info">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Última Atualização</h6>
                    <h4 class="mb-0" style="font-size: 1rem;">{{ $stats['ultima_atualizacao'] ?? date('d/m/Y H:i') }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-clock text-info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configurações do Sistema -->
<div class="row">
    <!-- Configurações Gerais -->
    <div class="col-lg-6 mb-4">
        <div class="table-container">
            <h5 class="mb-4">
                <i class="mdi mdi-settings text-primary"></i> Configurações Gerais
            </h5>
            
            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Sistema de Fidelidade</strong>
                        <small class="text-muted d-block">Status geral do sistema</small>
                    </div>
                    <span class="badge bg-success">Ativo</span>
                </div>
            </div>

            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Pontos por Real</strong>
                        <small class="text-muted d-block">Conversão de valor para pontos</small>
                    </div>
                    <span class="badge bg-primary">1 ponto = R$ 1,00</span>
                </div>
            </div>

            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Cashback Padrão</strong>
                        <small class="text-muted d-block">Percentual padrão de cashback</small>
                    </div>
                    <span class="badge bg-success">2,5%</span>
                </div>
            </div>

            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Resgate Mínimo</strong>
                        <small class="text-muted d-block">Pontos mínimos para resgate</small>
                    </div>
                    <span class="badge bg-warning">100 pontos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Configurações de Níveis -->
    <div class="col-lg-6 mb-4">
        <div class="table-container">
            <h5 class="mb-4">
                <i class="mdi mdi-star text-warning"></i> Níveis de Fidelidade
            </h5>
            
            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Nível Bronze</strong>
                        <small class="text-muted d-block">0 - 499 pontos</small>
                    </div>
                    <span class="badge bg-secondary">Padrão</span>
                </div>
            </div>

            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Nível Prata</strong>
                        <small class="text-muted d-block">500 - 999 pontos</small>
                    </div>
                    <span class="badge bg-info">+5% Cashback</span>
                </div>
            </div>

            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Nível Ouro</strong>
                        <small class="text-muted d-block">1000+ pontos</small>
                    </div>
                    <span class="badge bg-warning">+10% Cashback</span>
                </div>
            </div>

            <div class="config-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Nível Diamante</strong>
                        <small class="text-muted d-block">5000+ pontos</small>
                    </div>
                    <span class="badge bg-success">+15% Cashback</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configurações Avançadas -->
<div class="table-container">
    <h5 class="mb-4">
        <i class="mdi mdi-tune text-danger"></i> Configurações Avançadas
    </h5>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="config-item">
                <div class="text-center">
                    <i class="mdi mdi-clock-outline text-warning" style="font-size: 2rem;"></i>
                    <h6 class="mt-2">Expiração de Pontos</h6>
                    <p class="text-muted small">365 dias</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="config-item">
                <div class="text-center">
                    <i class="mdi mdi-gift-outline text-success" style="font-size: 2rem;"></i>
                    <h6 class="mt-2">Bônus de Cadastro</h6>
                    <p class="text-muted small">50 pontos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="config-item">
                <div class="text-center">
                    <i class="mdi mdi-account-multiple text-info" style="font-size: 2rem;"></i>
                    <h6 class="mt-2">Indicação de Amigos</h6>
                    <p class="text-muted small">100 pontos</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configurações do Sistema com Bootstrap Cards -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-settings text-primary"></i> Configurações do Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="config-item mb-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="config-label">Sistema Ativo</div>
                            <small class="text-muted">Status geral do programa de fidelidade</small>
                        </div>
                        <div class="col-4 text-end">
                            @if(($configs['sistema_ativo'] ?? false))
                            <span class="badge bg-success">Ativo</span>
                            @else
                            <span class="badge bg-danger">Inativo</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="config-item mb-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="config-label">Pontos por Real</div>
                            <small class="text-muted">Quantos pontos o cliente ganha por R$ 1,00</small>
                        </div>
                        <div class="col-4 text-end">
                            <span class="config-value">{{ $configs['pontos_por_real'] ?? 1 }} pts</span>
                        </div>
                    </div>
                </div>
                
                <div class="config-item mb-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="config-label">Limite Diário Cashback</div>
                            <small class="text-muted">Valor máximo de cashback por dia</small>
                        </div>
                        <div class="col-4 text-end">
                            <span class="config-value">R$ {{ number_format($configs['limite_diario_cashback'] ?? 100, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="config-item mb-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="config-label">Validade dos Cupons</div>
                            <small class="text-muted">Dias de validade dos cupons gerados</small>
                        </div>
                        <div class="col-4 text-end">
                            <span class="config-value">{{ $configs['validade_cupons'] ?? 30 }} dias</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Níveis de Fidelidade -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-star text-warning"></i> Níveis de Fidelidade
                </h5>
            </div>
            <div class="card-body">
                <div class="config-item mb-3">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="nivel-badge bronze">
                                <i class="mdi mdi-medal"></i> Bronze
                                <br><small class="text-muted">Nível inicial</small>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="config-value">{{ $configs['nivel_bronze_min'] ?? 0 }} pts</div>
                            <small class="text-muted">Cashback: {{ $configs['cashback_bronze'] ?? 1 }}%</small>
                        </div>
                    </div>
                </div>
                
                <div class="config-item mb-3">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="nivel-badge prata">
                                <i class="mdi mdi-medal"></i> Prata
                                <br><small class="text-muted">Nível intermediário</small>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="config-value">{{ number_format($configs['nivel_prata_min'] ?? 1000, 0, ',', '.') }} pts</div>
                            <small class="text-muted">Cashback: {{ $configs['cashback_prata'] ?? 2 }}%</small>
                        </div>
                    </div>
                </div>
                
                <div class="config-item mb-3">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="nivel-badge ouro">
                                <i class="mdi mdi-medal"></i> Ouro
                                <br><small class="text-muted">Nível premium</small>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="config-value">{{ number_format($configs['nivel_ouro_min'] ?? 5000, 0, ',', '.') }} pts</div>
                            <small class="text-muted">Cashback: {{ $configs['cashback_ouro'] ?? 3 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Informações Adicionais -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-information text-info"></i> Informações do Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="mdi mdi-information-outline"></i> Sobre esta Página</h6>
                    <p class="mb-2">Esta é uma página administrativa <strong>somente leitura</strong> que exibe as configurações atuais do sistema de fidelidade.</p>
                    <p class="mb-0">Para alterar essas configurações, acesse o painel de configurações principal do sistema.</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Recursos Disponíveis:</h6>
                        <ul class="list-unstyled">
                            <li><i class="mdi mdi-check text-success"></i> Visualização de configurações</li>
                            <li><i class="mdi mdi-check text-success"></i> Monitoramento de status</li>
                            <li><i class="mdi mdi-check text-success"></i> Histórico de alterações</li>
                            <li><i class="mdi mdi-check text-success"></i> Relatórios de performance</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Próximas Funcionalidades:</h6>
                        <ul class="list-unstyled">
                            <li><i class="mdi mdi-clock text-warning"></i> Edição inline de configurações</li>
                            <li><i class="mdi mdi-clock text-warning"></i> Backup automático de configs</li>
                            <li><i class="mdi mdi-clock text-warning"></i> Notificações de mudanças</li>
                            <li><i class="mdi mdi-clock text-warning"></i> API de configurações</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

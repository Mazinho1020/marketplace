@extends('comerciantes.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Dashboard
                </h2>
                <p class="text-muted mb-0">Bem-vindo, <strong>{{ $user->nome }}</strong>!</p>
                <small class="text-muted">Último acesso: {{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Primeiro acesso' }}</small>
            </div>
            
            @if($user->todas_empresas->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-building me-2"></i>
                        @if(session('empresa_atual_id'))
                            @php
                                $empresaAtual = $user->todas_empresas->firstWhere('id', session('empresa_atual_id'));
                            @endphp
                            {{ $empresaAtual ? Str::limit(($empresaAtual->nome_fantasia ?: $empresaAtual->razao_social) ?? 'Empresa', 20) : 'Todas as Empresas' }}
                        @else
                            Todas as Empresas
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ !session('empresa_atual_id') ? 'active' : '' }}" 
                               href="{{ route('comerciantes.dashboard.limpar') }}">
                                <i class="fas fa-list me-2"></i>
                                Todas as Empresas
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach($user->todas_empresas as $empresa)
                            <li>
                                <a class="dropdown-item {{ session('empresa_atual_id') == $empresa->id ? 'active' : '' }}" 
                                   href="{{ route('comerciantes.dashboard.empresa', $empresa->id) }}">
                                    <i class="fas fa-building me-2"></i>
                                    {{ Str::limit(($empresa->nome_fantasia ?: $empresa->razao_social) ?? 'Empresa', 30) }}
                                    <span class="badge badge-{{ $empresa->status == 'ativa' ? 'ativa' : 'inativa' }} ms-2">
                                        {{ ucfirst($empresa->status) }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cards Estatísticos -->
<div class="row mb-4">
    <!-- Total de Marcas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-primary">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Total de Marcas</div>
                    <div class="stats-value">{{ $dashboardData['estatisticas']['total_marcas'] }}</div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-tags"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Empresas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-success">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Total de Empresas</div>
                    <div class="stats-value">{{ $dashboardData['estatisticas']['total_empresas'] }}</div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empresas Ativas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-info">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Empresas Ativas</div>
                    <div class="stats-value">{{ $dashboardData['estatisticas']['empresas_ativas'] }}</div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-info">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuários Vinculados -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-warning">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Usuários Vinculados</div>
                    <div class="stats-value">{{ $dashboardData['estatisticas']['usuarios_vinculados'] }}</div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progresso de Configuração -->
@if($dashboardData['progresso_configuracao']['porcentagem'] < 100)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks me-2"></i>
                    Progresso de Configuração
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="mb-2">Complete seu perfil para aproveitar todas as funcionalidades:</p>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $dashboardData['progresso_configuracao']['porcentagem'] }}%">
                                {{ $dashboardData['progresso_configuracao']['porcentagem'] }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $dashboardData['progresso_configuracao']['completos'] }} de {{ $dashboardData['progresso_configuracao']['total'] }} itens completos
                        </small>
                    </div>
                    <div class="col-md-4">
                        <div class="checklist">
                            @foreach($dashboardData['progresso_configuracao']['itens'] as $key => $completo)
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas {{ $completo ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} me-2"></i>
                                    <small class="{{ $completo ? 'text-success' : 'text-muted' }}">
                                        @switch($key)
                                            @case('perfil_completo')
                                                Perfil completo
                                                @break
                                            @case('tem_marca')
                                                Marca criada
                                                @break
                                            @case('tem_empresa')
                                                Empresa criada
                                                @break
                                            @case('empresa_com_endereco')
                                                Endereço configurado
                                                @break
                                            @case('empresa_com_horario')
                                                Horário configurado
                                                @break
                                        @endswitch
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Sugestões de Ações ou Ações Rápidas -->
@if(!empty($sugestoes))
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-lightbulb me-2"></i>
                    Sugestões para Você
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($sugestoes as $sugestao)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <a href="{{ $sugestao['url'] }}" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="{{ $sugestao['icone'] }}"></i>
                                </div>
                                <strong>{{ $sugestao['titulo'] }}</strong>
                                <small class="text-muted text-center">{{ $sugestao['descricao'] }}</small>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-rocket me-2"></i>
                    Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('comerciantes.marcas.create') }}" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <strong>Nova Marca</strong>
                            <small class="text-muted">Criar uma nova marca</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('comerciantes.empresas.create') }}" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <strong>Nova Empresa</strong>
                            <small class="text-muted">Adicionar nova empresa</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <strong>Relatórios</strong>
                            <small class="text-muted">Ver relatórios</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <strong>Configurações</strong>
                            <small class="text-muted">Configurar conta</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Suas Marcas -->
@if($dashboardData['marcas_recentes']->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags me-2"></i>
                    Suas Marcas Recentes
                </h6>
                <a href="{{ route('comerciantes.marcas.index') }}" class="btn btn-sm btn-outline-primary">
                    Ver Todas
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($dashboardData['marcas_recentes'] as $marca)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        @if($marca->logo_url)
                                            <img src="{{ $marca->logo_url_completo }}" alt="{{ $marca->nome }}" 
                                                 class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-tags text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $marca->nome }}</h6>
                                            <small class="text-muted">
                                                <span class="badge badge-{{ $marca->status == 'ativa' ? 'ativa' : 'inativa' }}">
                                                    {{ ucfirst($marca->status) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $marca->empresas->count() }} empresa(s)
                                        </small>
                                    </div>
                                    
                                    @if($marca->descricao)
                                        <p class="text-muted small mb-3">{{ Str::limit($marca->descricao ?? '', 80) }}</p>
                                    @endif
                                    
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('comerciantes.marcas.show', $marca) }}" 
                                           class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver
                                        </a>
                                        <a href="{{ route('comerciantes.marcas.edit', $marca) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Estado Vazio - Primeira vez -->
@if($dashboardData['is_primeira_vez'])
<div class="row">
    <div class="col-12">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="mb-4">
                    <i class="fas fa-store fa-4x text-muted mb-3"></i>
                    <h4>Bem-vindo ao seu Marketplace!</h4>
                    <p class="text-muted mb-4">
                        Comece criando sua primeira marca para organizar suas empresas e produtos.
                        <br>
                        Depois adicione suas unidades/lojas e configure tudo para começar a vender.
                    </p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('comerciantes.marcas.create') }}" 
                                   class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-tags me-2"></i>
                                    Criar Primeira Marca
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="#" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-question-circle me-2"></i>
                                    Ver Tutorial
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1"></i>
                        Dica: Uma marca pode ter várias empresas. Por exemplo, "Pizzaria Tradição" pode ter unidades em diferentes bairros.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Atualizar estatísticas a cada 30 segundos
    setInterval(function() {
        fetch('{{ route("comerciantes.dashboard") }}/estatisticas')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar os valores nos cards
                    document.querySelector('.stats-card:nth-child(1) .stats-value').textContent = data.data.total_marcas;
                    document.querySelector('.stats-card:nth-child(2) .stats-value').textContent = data.data.total_empresas;
                    document.querySelector('.stats-card:nth-child(3) .stats-value').textContent = data.data.empresas_ativas;
                    document.querySelector('.stats-card:nth-child(4) .stats-value').textContent = data.data.usuarios_vinculados;
                }
            })
            .catch(error => console.log('Erro ao atualizar estatísticas:', error));
    }, 30000);

    // Animação dos cards ao carregar
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stats-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.4s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 100);
        });
    });
</script>
@endpush

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Sistema - MeuFinanceiro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Header Styles */
        .main-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #667eea !important;
        }
        
        /* Sidebar Menu */
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            min-height: calc(100vh - 76px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: #495057;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        /* Main Content */
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            min-height: calc(100vh - 136px);
            border-radius: 15px;
            margin: 1rem 0;
            padding: 2rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        /* Cards */
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 1.5rem;
            background: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .config-item {
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .config-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }
        
        /* Filters */
        .filter-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        /* Footer */
        .main-footer {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            margin-top: auto;
        }
        
        /* Badges */
        .badge {
            border-radius: 6px;
            font-size: 0.75rem;
        }
        
        /* Utilities */
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Loading indicator */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        /* Quick actions buttons */
        .config-actions {
            margin-top: 0.5rem;
        }
        
        .config-actions .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Timeline styles for history modal */
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        
        .timeline-content h6 {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg main-header">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chart-line me-2"></i>
                MeuFinanceiro
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/config">
                            <i class="fas fa-cogs me-1"></i>Configurações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/users">
                            <i class="fas fa-users me-1"></i>Usuários
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/profile"><i class="fas fa-user me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <div class="mt-3">
                <h5>Carregando...</h5>
                <p class="text-muted mb-0">Aguarde um momento</p>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <!-- Busca Rápida -->
                <div class="mb-3">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="quickSearch" placeholder="Busca rápida...">
                        <button class="btn btn-outline-secondary" type="button" onclick="performQuickSearch()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <h6 class="text-muted mb-3">MENU CONFIGURAÇÕES</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.config.index') }}">
                            <i class="fas fa-list me-2"></i>Todas as Configurações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/config-simple">
                            <i class="fas fa-sliders-h me-2"></i>Config Simples
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.config.create') }}">
                            <i class="fas fa-plus me-2"></i>Nova Configuração
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showGroupsModal()">
                            <i class="fas fa-folder me-2"></i>Grupos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.config.export') }}">
                            <i class="fas fa-download me-2"></i>Exportar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showImportModal()">
                            <i class="fas fa-upload me-2"></i>Importar
                        </a>
                    </li>
                </ul>
                
                <hr class="my-3">
                
                <!-- Status Info -->
                <div class="card mb-3" style="background: rgba(255,255,255,0.8);">
                    <div class="card-body p-2 text-center">
                        <small class="text-muted d-block">Configurações Ativas</small>
                        <h6 class="mb-0 text-gradient">
                            {{ isset($configsByGroup) ? array_sum(array_map('count', $configsByGroup)) : (isset($configs) ? $configs->count() : 0) }}
                        </h6>
                    </div>
                </div>
                
                <h6 class="text-muted mb-3">GRUPOS RÁPIDOS</h6>
                <ul class="nav flex-column">
                    @if(isset($grupos))
                        @foreach($grupos as $grupo)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.config.group', $grupo->codigo ?? $grupo->nome ?? 'sistema') }}">
                                    <i class="{{ $grupo->icone ?? 'fas fa-folder' }} me-2"></i>
                                    {{ $grupo->nome ?? 'Grupo' }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.config.index', ['group' => 'sistema']) }}">
                                <i class="fas fa-cog me-2"></i>Sistema
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.config.index', ['group' => 'email']) }}">
                                <i class="fas fa-envelope me-2"></i>Email
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.config.index', ['group' => 'fidelidade']) }}">
                                <i class="fas fa-star me-2"></i>Fidelidade
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.config.index', ['group' => 'pagamento']) }}">
                                <i class="fas fa-credit-card me-2"></i>Pagamento
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="text-gradient mb-1">
                                <i class="fas fa-cogs me-2"></i>
                                Configurações do Sistema
                            </h2>
                            <p class="text-muted mb-0">Gerencie todas as configurações da aplicação</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning me-2" onclick="clearCache()">
                                <i class="fas fa-sync-alt me-1"></i>Limpar Cache
                            </button>
                            <button type="button" class="btn btn-info me-2" onclick="showQuickCreateModal()">
                                <i class="fas fa-magic me-1"></i>Criação Rápida
                            </button>
                            <a href="{{ route('admin.config.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i>Nova Config
                            </a>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-card">
                        <form method="GET" action="{{ route('admin.config.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="group" class="form-label">
                                    <i class="fas fa-folder me-1"></i>Grupo
                                </label>
                                <select name="group" id="group" class="form-select">
                                    <option value="">Todos os grupos</option>
                                    <option value="sistema" {{ request('group') == 'sistema' ? 'selected' : '' }}>Sistema</option>
                                    <option value="email" {{ request('group') == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="fidelidade" {{ request('group') == 'fidelidade' ? 'selected' : '' }}>Fidelidade</option>
                                    <option value="pagamento" {{ request('group') == 'pagamento' ? 'selected' : '' }}>Pagamento</option>
                                    <option value="seguranca" {{ request('group') == 'seguranca' ? 'selected' : '' }}>Segurança</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="tipo" class="form-label">
                                    <i class="fas fa-tags me-1"></i>Tipo
                                </label>
                                <select name="tipo" id="tipo" class="form-select">
                                    <option value="">Todos os tipos</option>
                                    <option value="string" {{ request('tipo') == 'string' ? 'selected' : '' }}>Texto</option>
                                    <option value="boolean" {{ request('tipo') == 'boolean' ? 'selected' : '' }}>Booleano</option>
                                    <option value="integer" {{ request('tipo') == 'integer' ? 'selected' : '' }}>Número</option>
                                    <option value="email" {{ request('tipo') == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="url" {{ request('tipo') == 'url' ? 'selected' : '' }}>URL</option>
                                    <option value="json" {{ request('tipo') == 'json' ? 'selected' : '' }}>JSON</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="search" class="form-label">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </label>
                                <input type="text" name="search" id="search" 
                                    class="form-control" placeholder="Chave ou descrição..."
                                    value="{{ request('search') }}">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-filter me-1"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        @if(request()->hasAny(['group', 'tipo', 'search']))
                            <div class="mt-3">
                                <a href="{{ route('admin.config.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Limpar Filtros
                                </a>
                            </div>
                        @endif
                    </div>
                    <!-- Debug Information -->
                    @if(isset($debug))
                        <div class="alert alert-info">
                            <h6><i class="fas fa-bug me-2"></i>Debug Info:</h6>
                            <ul class="mb-0">
                                @foreach($debug as $key => $value)
                                    <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($error))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $error }}
                        </div>
                    @endif

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-cogs text-primary display-6 mb-2"></i>
                                    <h5 class="card-title">
                                        {{ isset($configsByGroup) ? array_sum(array_map('count', $configsByGroup)) : (isset($configs) ? $configs->count() : 0) }}
                                    </h5>
                                    <p class="card-text text-muted">Total de Configurações</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-folder text-info display-6 mb-2"></i>
                                    <h5 class="card-title">
                                        {{ isset($configsByGroup) ? count($configsByGroup) : 0 }}
                                    </h5>
                                    <p class="card-text text-muted">Grupos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-check-circle text-success display-6 mb-2"></i>
                                    <h5 class="card-title">--</h5>
                                    <p class="card-text text-muted">Configuradas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-exclamation-circle text-warning display-6 mb-2"></i>
                                    <h5 class="card-title">--</h5>
                                    <p class="card-text text-muted">Pendentes</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configurations Display -->
                    @if(isset($configsByGroup) && !empty($configsByGroup))
                        @foreach($configsByGroup as $groupName => $groupConfigs)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-folder me-2"></i>
                                        {{ $groupName ?? 'Sem Grupo' }}
                                        <span class="badge bg-secondary ms-2">{{ count($groupConfigs) }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($groupConfigs as $config)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="config-item">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-1">
                                                            <i class="fas fa-key me-1 text-muted"></i>
                                                            {{ $config->chave ?? 'N/A' }}
                                                            @if($config->obrigatorio ?? false)
                                                                <span class="badge bg-danger ms-1">Obrigatório</span>
                                                            @endif
                                                        </h6>
                                                        <span class="badge bg-secondary">{{ $config->tipo_dado ?? 'string' }}</span>
                                                    </div>
                                                    
                                                    @if($config->descricao ?? false)
                                                        <p class="text-muted small mb-3">{{ $config->descricao }}</p>
                                                    @endif

                                                    <form class="config-form" data-config-chave="{{ $config->chave ?? '' }}">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">
                                                                <i class="fas fa-edit me-1"></i>Valor:
                                                            </label>
                                                            @php
                                                                $currentValue = '';
                                                                if (method_exists($config, 'formatarValor')) {
                                                                    try {
                                                                        $currentValue = $config->formatarValor();
                                                                    } catch (Exception $e) {
                                                                        $currentValue = $config->valor_padrao ?? '';
                                                                    }
                                                                } else {
                                                                    $currentValue = $config->valor_padrao ?? '';
                                                                }
                                                            @endphp
                                                            
                                                            @if(($config->tipo_dado ?? 'string') === 'boolean')
                                                                <select class="form-select form-select-sm" name="valor">
                                                                    <option value="0" {{ $currentValue == '0' ? 'selected' : '' }}>
                                                                        <i class="fas fa-times me-1"></i>Não (false)
                                                                    </option>
                                                                    <option value="1" {{ $currentValue == '1' ? 'selected' : '' }}>
                                                                        <i class="fas fa-check me-1"></i>Sim (true)
                                                                    </option>
                                                                </select>
                                                            @elseif(($config->tipo_dado ?? 'string') === 'integer')
                                                                <input type="number" class="form-control form-control-sm" 
                                                                       name="valor" value="{{ $currentValue }}" placeholder="0">
                                                            @elseif(($config->tipo_dado ?? 'string') === 'email')
                                                                <input type="email" class="form-control form-control-sm" 
                                                                       name="valor" value="{{ $currentValue }}" placeholder="exemplo@dominio.com">
                                                            @elseif(($config->tipo_dado ?? 'string') === 'url')
                                                                <input type="url" class="form-control form-control-sm" 
                                                                       name="valor" value="{{ $currentValue }}" placeholder="https://exemplo.com">
                                                            @elseif(($config->tipo_dado ?? 'string') === 'json')
                                                                <textarea class="form-control form-control-sm" name="valor" rows="3" 
                                                                          placeholder='{"chave": "valor"}'>{{ $currentValue }}</textarea>
                                                            @else
                                                                <input type="text" class="form-control form-control-sm" 
                                                                       name="valor" value="{{ $currentValue }}" placeholder="Digite o valor...">
                                                            @endif
                                                        </div>
                                                        
                                                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                                                            <i class="fas fa-save me-1"></i>
                                                            Salvar Configuração
                                                        </button>
                                                    </form>

                                                    <!-- Ações Rápidas -->
                                                    <div class="d-flex gap-1">
                                                        <button class="btn btn-outline-info btn-sm flex-fill" 
                                                                onclick="showConfigHistory('{{ $config->chave ?? '' }}')"
                                                                title="Histórico">
                                                            <i class="fas fa-history"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm flex-fill" 
                                                                onclick="resetToDefault('{{ $config->chave ?? '' }}')"
                                                                title="Restaurar Padrão">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        <button class="btn btn-outline-secondary btn-sm flex-fill" 
                                                                onclick="copyConfigKey('{{ $config->chave ?? '' }}')"
                                                                title="Copiar Chave">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>

                                                    @if($config->valor_padrao ?? false)
                                                        <div class="mt-3 p-2 bg-light rounded">
                                                            <small class="text-muted">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                <strong>Valor Padrão:</strong> 
                                                                <code class="bg-white px-2 py-1 rounded">{{ $config->valor_padrao }}</code>
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-center mt-4">
                            <div class="alert alert-info">
                                <i class="fas fa-chart-bar me-2"></i>
                                <strong>Resumo:</strong> {{ array_sum(array_map('count', $configsByGroup)) }} configurações em {{ count($configsByGroup) }} grupos
                            </div>
                        </div>
                    @elseif(isset($configs) && $configs->count() > 0)
                        <!-- Fallback para $configs simples -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>Configurações
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($configs as $config)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="config-item">
                                                <h6><i class="fas fa-key me-1 text-muted"></i>{{ $config->chave ?? 'N/A' }}</h6>
                                                <p class="text-muted small">{{ $config->descricao ?? 'Sem descrição' }}</p>
                                                <form class="config-form" data-config-chave="{{ $config->chave ?? '' }}">
                                                    <input type="text" class="form-control form-control-sm" 
                                                           name="valor" value="{{ $config->valor_padrao ?? '' }}" placeholder="Digite o valor...">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">
                                                        <i class="fas fa-save me-1"></i>
                                                        Salvar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-search display-1 text-muted mb-4"></i>
                                <h4 class="text-muted">Nenhuma configuração encontrada</h4>
                                <p class="text-muted">Tente ajustar os filtros ou criar uma nova configuração.</p>
                                <div class="mt-4">
                                    <a href="/admin/config-simple" class="btn btn-primary me-2">
                                        <i class="fas fa-sliders-h me-1"></i>
                                        Config Simples
                                    </a>
                                    <a href="{{ route('admin.config.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus me-1"></i>
                                        Nova Configuração
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-copyright me-1"></i>
                        2025 MeuFinanceiro. Todos os direitos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-code me-1"></i>
                        Versão 1.0.0 | 
                        <a href="#" class="text-decoration-none">Documentação</a> | 
                        <a href="#" class="text-decoration-none">Suporte</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal para Criação Rápida -->
    <div class="modal fade" id="quickCreateModal" tabindex="-1" aria-labelledby="quickCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-gradient" id="quickCreateModalLabel">
                        <i class="fas fa-magic me-2"></i>Criação Rápida de Configuração
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickCreateForm">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quickNome" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Nome da Configuração *
                                </label>
                                <input type="text" class="form-control" id="quickNome" required
                                       placeholder="Ex: Email do Administrador">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quickGrupo" class="form-label">
                                    <i class="fas fa-folder me-1"></i>Grupo *
                                </label>
                                <select class="form-select" id="quickGrupo" required>
                                    <option value="">Selecione um grupo</option>
                                    <option value="sistema">Sistema</option>
                                    <option value="email">Email</option>
                                    <option value="fidelidade">Fidelidade</option>
                                    <option value="pagamento">Pagamento</option>
                                    <option value="seguranca">Segurança</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quickTipo" class="form-label">
                                    <i class="fas fa-cog me-1"></i>Tipo *
                                </label>
                                <select class="form-select" id="quickTipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="string">Texto</option>
                                    <option value="boolean">Verdadeiro/Falso</option>
                                    <option value="integer">Número</option>
                                    <option value="email">Email</option>
                                    <option value="url">URL</option>
                                    <option value="json">JSON</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quickValor" class="form-label">
                                    <i class="fas fa-code me-1"></i>Valor Inicial
                                </label>
                                <input type="text" class="form-control" id="quickValor"
                                       placeholder="Valor inicial da configuração">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="quickDescricao" class="form-label">
                                    <i class="fas fa-info-circle me-1"></i>Descrição
                                </label>
                                <textarea class="form-control" id="quickDescricao" rows="2"
                                          placeholder="Breve descrição da configuração"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Criar Configuração
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Grupos -->
    <div class="modal fade" id="groupsModal" tabindex="-1" aria-labelledby="groupsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-gradient" id="groupsModalLabel">
                        <i class="fas fa-folder me-2"></i>Gerenciar Grupos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Grupos Disponíveis</h6>
                        <button class="btn btn-primary btn-sm" onclick="showNewGroupForm()">
                            <i class="fas fa-plus me-1"></i>Novo Grupo
                        </button>
                    </div>
                    
                    <div id="groupsList">
                        @if(isset($grupos))
                            @foreach($grupos as $grupo)
                                <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="{{ $grupo->icone ?? 'fas fa-folder' }} me-2"></i>
                                            {{ $grupo->nome ?? 'Grupo' }}
                                        </h6>
                                        <small class="text-muted">{{ $grupo->descricao ?? 'Sem descrição' }}</small>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.config.index', ['group' => $grupo->codigo ?? $grupo->nome]) }}" 
                                           class="btn btn-outline-primary btn-sm me-1">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-folder-open display-4 mb-3"></i>
                                <p>Nenhum grupo encontrado</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Importar -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-gradient" id="importModalLabel">
                        <i class="fas fa-upload me-2"></i>Importar Configurações
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="importForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="importFile" class="form-label">
                                <i class="fas fa-file me-1"></i>Arquivo de Configurações
                            </label>
                            <input type="file" class="form-control" id="importFile" name="file" 
                                   accept=".json,.csv,.xlsx" required>
                            <div class="form-text">Formatos aceitos: JSON, CSV, Excel (.xlsx)</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="overwriteExisting" name="overwrite" value="1">
                                <label class="form-check-label" for="overwriteExisting">
                                    Sobrescrever configurações existentes
                                </label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Dica:</strong> O arquivo deve conter as colunas: chave, valor, grupo, tipo, descrição
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Importar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inicialização da página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de configurações carregada');
            console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
            
            // Adicionar animações aos cards
            animateCards();
            
            // Configurar tooltips
            initTooltips();
            
            // Auto-save em inputs
            setupAutoSave();
            
            // Esconder loading inicial
            hideLoading();
        });

        // Funções de loading
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // Animação dos cards
        function animateCards() {
            const cards = document.querySelectorAll('.config-item');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }

        // Configurar tooltips
        function initTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Auto-save functionality
        function setupAutoSave() {
            const inputs = document.querySelectorAll('.config-form input, .config-form select, .config-form textarea');
            inputs.forEach(input => {
                let timeout;
                input.addEventListener('input', function() {
                    clearTimeout(timeout);
                    
                    // Mostrar indicador de mudança
                    const form = this.closest('form');
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.classList.add('btn-warning');
                    submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i>Pendente...';
                    
                    // Auto-save após 2 segundos (comentado por segurança)
                    // timeout = setTimeout(() => {
                    //     submitBtn.click();
                    // }, 2000);
                });
            });
        }

        // Salvar configuração via AJAX
        document.querySelectorAll('.config-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const configChave = this.dataset.configChave;
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                if (!configChave) {
                    showError('Erro: Chave da configuração não encontrada');
                    return;
                }
                
                // Loading state
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-warning');
                submitBtn.classList.add('btn-info');
                submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Salvando...';
                
                try {
                    const response = await fetch('{{ route("admin.config.set-value") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            chave: configChave,
                            valor: formData.get('valor')
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showSuccess(result.message || 'Configuração salva com sucesso!');
                        submitBtn.classList.remove('btn-info');
                        submitBtn.classList.add('btn-success');
                        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Salvo!';
                        
                        // Efeito visual de sucesso
                        const configItem = this.closest('.config-item');
                        configItem.style.borderColor = '#28a745';
                        configItem.style.backgroundColor = '#f8fff9';
                        
                        setTimeout(() => {
                            submitBtn.classList.remove('btn-success');
                            submitBtn.classList.add('btn-primary');
                            submitBtn.innerHTML = originalText;
                            configItem.style.borderColor = '';
                            configItem.style.backgroundColor = '';
                        }, 3000);
                    } else {
                        showError(result.message || 'Erro ao salvar configuração');
                        submitBtn.classList.remove('btn-info');
                        submitBtn.classList.add('btn-danger');
                        submitBtn.innerHTML = '<i class="fas fa-times me-1"></i> Erro!';
                        
                        setTimeout(() => {
                            submitBtn.classList.remove('btn-danger');
                            submitBtn.classList.add('btn-primary');
                            submitBtn.innerHTML = originalText;
                        }, 3000);
                    }
                } catch (error) {
                    showError('Erro ao salvar configuração: ' + error.message);
                    console.error('Erro:', error);
                    
                    submitBtn.classList.remove('btn-info');
                    submitBtn.classList.add('btn-danger');
                    submitBtn.innerHTML = '<i class="fas fa-times me-1"></i> Erro!';
                    
                    setTimeout(() => {
                        submitBtn.classList.remove('btn-danger');
                        submitBtn.classList.add('btn-primary');
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                } finally {
                    submitBtn.disabled = false;
                }
            });
        });

        // Limpar cache com loading
        async function clearCache() {
            const result = await Swal.fire({
                title: 'Limpar Cache?',
                text: 'Isso irá limpar todos os caches do sistema.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, limpar!',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ffc107',
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    try {
                        const response = await fetch('{{ route("admin.config.clear-cache") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        
                        return await response.json();
                    } catch (error) {
                        Swal.showValidationMessage('Erro: ' + error.message);
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
            
            if (result.isConfirmed) {
                if (result.value?.success) {
                    showSuccess(result.value.message || 'Cache limpo com sucesso!');
                } else {
                    showError(result.value?.message || 'Erro ao limpar cache');
                }
            }
        }

        // Filtros dinâmicos
        document.getElementById('group')?.addEventListener('change', function() {
            updateFilters();
        });

        document.getElementById('tipo')?.addEventListener('change', function() {
            updateFilters();
        });

        function updateFilters() {
            const group = document.getElementById('group')?.value;
            const tipo = document.getElementById('tipo')?.value;
            const search = document.getElementById('search')?.value;
            
            // Construir URL com filtros
            const params = new URLSearchParams();
            if (group) params.append('group', group);
            if (tipo) params.append('tipo', tipo);
            if (search) params.append('search', search);
            
            // Auto-submit após delay
            setTimeout(() => {
                if (group || tipo) {
                    window.location.href = '{{ route("admin.config.index") }}?' + params.toString();
                }
            }, 500);
        }

        // Busca em tempo real (debounced)
        let searchTimeout;
        document.getElementById('search')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value;
            
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                searchTimeout = setTimeout(() => {
                    filterConfigs(searchTerm);
                }, 500);
            }
        });

        function filterConfigs(searchTerm) {
            const configItems = document.querySelectorAll('.config-item');
            
            configItems.forEach(item => {
                const chave = item.querySelector('h6')?.textContent?.toLowerCase() || '';
                const descricao = item.querySelector('.text-muted')?.textContent?.toLowerCase() || '';
                
                if (searchTerm === '' || chave.includes(searchTerm.toLowerCase()) || descricao.includes(searchTerm.toLowerCase())) {
                    item.style.display = 'block';
                    item.closest('.col-md-6, .col-lg-4')?.style.setProperty('display', 'block');
                } else {
                    item.style.display = 'none';
                    item.closest('.col-md-6, .col-lg-4')?.style.setProperty('display', 'none');
                }
            });
        }

        // Funções de notificação melhoradas
        function showSuccess(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: message,
                    confirmButtonColor: '#28a745',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            } else {
                alert('✅ ' + message);
            }
        }

        function showError(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: message,
                    confirmButtonColor: '#dc3545',
                    toast: true,
                    position: 'top-end',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            } else {
                alert('❌ ' + message);
            }
        }

        // Atalhos de teclado
        document.addEventListener('keydown', function(e) {
            // Ctrl + S para salvar todas as configurações
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveAllConfigs();
            }
            
            // Ctrl + F para focar na busca
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('search')?.focus();
            }
        });

        function saveAllConfigs() {
            const forms = document.querySelectorAll('.config-form');
            let savePromises = [];
            
            forms.forEach(form => {
                const submitEvent = new Event('submit');
                savePromises.push(form.dispatchEvent(submitEvent));
            });
            
            if (savePromises.length > 0) {
                showSuccess(`Salvando ${savePromises.length} configurações...`);
            }
        }

        // Função para mostrar modal de criação rápida
        function showQuickCreateModal() {
            const modal = new bootstrap.Modal(document.getElementById('quickCreateModal'));
            modal.show();
        }

        // Função para mostrar modal de grupos
        function showGroupsModal() {
            const modal = new bootstrap.Modal(document.getElementById('groupsModal'));
            modal.show();
        }

        // Função para mostrar modal de importar
        function showImportModal() {
            const modal = new bootstrap.Modal(document.getElementById('importModal'));
            modal.show();
        }

        // Função para mostrar formulário de novo grupo
        function showNewGroupForm() {
            Swal.fire({
                title: 'Novo Grupo',
                html: `
                    <div class="text-start">
                        <div class="mb-3">
                            <label class="form-label">Nome do Grupo</label>
                            <input type="text" id="grupoNome" class="form-control" placeholder="Ex: Sistema">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" id="grupoCodigo" class="form-control" placeholder="Ex: sistema">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ícone (FontAwesome)</label>
                            <input type="text" id="grupoIcone" class="form-control" placeholder="Ex: fas fa-cog" value="fas fa-folder">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea id="grupoDescricao" class="form-control" rows="2" placeholder="Descrição do grupo"></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Criar Grupo',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                preConfirm: () => {
                    const nome = document.getElementById('grupoNome').value;
                    const codigo = document.getElementById('grupoCodigo').value;
                    const icone = document.getElementById('grupoIcone').value;
                    const descricao = document.getElementById('grupoDescricao').value;
                    
                    if (!nome || !codigo) {
                        Swal.showValidationMessage('Nome e código são obrigatórios');
                        return false;
                    }
                    
                    return { nome, codigo, icone, descricao };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aqui você pode implementar a criação do grupo via AJAX
                    showSuccess('Funcionalidade de criação de grupo será implementada');
                }
            });
        }

        // Handler para o formulário de criação rápida
        document.getElementById('quickCreateForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const nome = document.getElementById('quickNome').value;
            const grupo = document.getElementById('quickGrupo').value;
            const tipo = document.getElementById('quickTipo').value;
            const valor = document.getElementById('quickValor').value;
            const descricao = document.getElementById('quickDescricao').value;
            
            if (!nome || !grupo || !tipo) {
                showError('Por favor, preencha todos os campos obrigatórios');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Criando...';
            
            try {
                const chave = nome.toLowerCase()
                                 .replace(/[^a-z0-9]/g, '_')
                                 .replace(/_+/g, '_')
                                 .replace(/^_|_$/g, '');
                
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('grupo_id', 1); // Default para teste
                formData.append('nome', nome);
                formData.append('chave', chave);
                formData.append('tipo', tipo);
                formData.append('valor_padrao', valor);
                formData.append('descricao', descricao);
                formData.append('visivel', '1');
                formData.append('editavel', '1');
                formData.append('ordem', '0');
                
                const response = await fetch('{{ route("admin.config.store") }}', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    showSuccess('Configuração criada com sucesso!');
                    bootstrap.Modal.getInstance(document.getElementById('quickCreateModal')).hide();
                    this.reset();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    const errorData = await response.text();
                    showError('Erro ao criar configuração: ' + (errorData || 'Erro desconhecido'));
                }
            } catch (error) {
                showError('Erro ao criar configuração: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Handler para o formulário de importação
        document.getElementById('importForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            if (!formData.get('file')) {
                showError('Selecione um arquivo para importar');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Importando...';
            
            try {
                // Simulação - você pode implementar a rota real de importação
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                showSuccess('Funcionalidade de importação será implementada');
                bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                this.reset();
            } catch (error) {
                showError('Erro ao importar: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Auto-generate código from nome no modal de grupo
        document.addEventListener('input', function(e) {
            if (e.target.id === 'grupoNome') {
                const codigo = e.target.value.toLowerCase()
                                            .replace(/[^a-z0-9]/g, '_')
                                            .replace(/_+/g, '_')
                                            .replace(/^_|_$/g, '');
                const codigoField = document.getElementById('grupoCodigo');
                if (codigoField) {
                    codigoField.value = codigo;
                }
            }
            
            // Auto-preview da chave no modal de criação rápida
            if (e.target.id === 'quickNome') {
                const chave = e.target.value.toLowerCase()
                                          .replace(/[^a-z0-9]/g, '_')
                                          .replace(/_+/g, '_')
                                          .replace(/^_|_$/g, '');
                
                // Mostrar preview da chave
                let previewEl = document.getElementById('chavePreview');
                if (!previewEl) {
                    previewEl = document.createElement('small');
                    previewEl.id = 'chavePreview';
                    previewEl.className = 'text-muted d-block mt-1';
                    e.target.parentNode.appendChild(previewEl);
                }
                previewEl.innerHTML = chave ? `<i class="fas fa-key me-1"></i>Chave: <code>${chave}</code>` : '';
            }
        });

        // Função de busca rápida
        function performQuickSearch() {
            const searchTerm = document.getElementById('quickSearch').value;
            if (searchTerm.trim()) {
                window.location.href = '{{ route("admin.config.index") }}?search=' + encodeURIComponent(searchTerm);
            }
        }

        // Enter na busca rápida
        document.getElementById('quickSearch')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performQuickSearch();
            }
        });

        // Auto-complete na busca rápida (debounced)
        let quickSearchTimeout;
        document.getElementById('quickSearch')?.addEventListener('input', function() {
            clearTimeout(quickSearchTimeout);
            const searchTerm = this.value;
            
            if (searchTerm.length >= 2) {
                quickSearchTimeout = setTimeout(() => {
                    // Highlight matching configs em tempo real
                    highlightConfigs(searchTerm);
                }, 300);
            } else if (searchTerm.length === 0) {
                // Remove highlights
                highlightConfigs('');
            }
        });

        function highlightConfigs(searchTerm) {
            const configItems = document.querySelectorAll('.config-item');
            
            configItems.forEach(item => {
                const chaveEl = item.querySelector('h6');
                const descricaoEl = item.querySelector('.text-muted');
                
                if (!chaveEl) return;
                
                const chave = chaveEl.textContent?.toLowerCase() || '';
                const descricao = descricaoEl?.textContent?.toLowerCase() || '';
                
                if (searchTerm === '') {
                    // Remove highlight
                    item.style.backgroundColor = '';
                    item.style.borderColor = '';
                    item.closest('.col-md-6, .col-lg-4')?.style.setProperty('display', '');
                } else if (chave.includes(searchTerm.toLowerCase()) || descricao.includes(searchTerm.toLowerCase())) {
                    // Highlight match
                    item.style.backgroundColor = '#fff3cd';
                    item.style.borderColor = '#ffc107';
                    item.closest('.col-md-6, .col-lg-4')?.style.setProperty('display', 'block');
                } else {
                    // Fade non-matches
                    item.style.backgroundColor = '#f8f9fa';
                    item.style.borderColor = '#e9ecef';
                    item.closest('.col-md-6, .col-lg-4')?.style.setProperty('display', 'block');
                }
            });
        }

        // Função para mostrar histórico da configuração
        function showConfigHistory(chave) {
            if (!chave) {
                showError('Chave da configuração não encontrada');
                return;
            }
            
            Swal.fire({
                title: `Histórico: ${chave}`,
                html: `
                    <div class="text-start">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Funcionalidade de histórico será implementada
                        </div>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>Criação</h6>
                                    <p class="small text-muted">Configuração criada em ${new Date().toLocaleDateString()}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                width: '600px',
                confirmButtonText: 'Fechar'
            });
        }

        // Função para resetar ao valor padrão
        async function resetToDefault(chave) {
            if (!chave) {
                showError('Chave da configuração não encontrada');
                return;
            }
            
            const result = await Swal.fire({
                title: 'Restaurar Valor Padrão?',
                text: `Isso irá restaurar a configuração "${chave}" ao seu valor padrão.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, restaurar!',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ffc107'
            });
            
            if (result.isConfirmed) {
                try {
                    const response = await fetch('/admin/config/' + encodeURIComponent(chave) + '/restore-value', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success || response.ok) {
                        showSuccess('Valor padrão restaurado com sucesso!');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showError(data.message || 'Erro ao restaurar valor padrão');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showSuccess('Funcionalidade de restaurar valor padrão será implementada');
                }
            }
        }

        // Função para copiar chave da configuração
        function copyConfigKey(chave) {
            if (!chave) {
                showError('Chave da configuração não encontrada');
                return;
            }
            
            navigator.clipboard.writeText(chave).then(() => {
                showSuccess(`Chave "${chave}" copiada para a área de transferência!`);
            }).catch(() => {
                // Fallback para navegadores mais antigos
                const textArea = document.createElement('textarea');
                textArea.value = chave;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                showSuccess(`Chave "${chave}" copiada para a área de transferência!`);
            });
        }
    </script>
</body>
</html>

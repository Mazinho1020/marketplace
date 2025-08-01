<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Sistema - MeuFinanceiro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 1.5rem;
            background: white;
        }
        .config-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Configurações do Sistema
                </h4>
            </div>
            <div class="card-body">
                
                <!-- Debug Information -->
                @if(isset($debug))
                    <div class="alert alert-info">
                        <h6>Debug Info:</h6>
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

                <!-- Navigation Links -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-warning" onclick="clearCache()">
                            <i class="fas fa-refresh me-1"></i>
                            Limpar Cache
                        </button>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="/admin/config-simple" class="btn btn-info">
                            <i class="fas fa-cogs me-1"></i>
                            Config Simples
                        </a>
                        <a href="/teste-layout" class="btn btn-secondary">
                            <i class="fas fa-home me-1"></i>
                            Início
                        </a>
                    </div>
                </div>

                <!-- Configurations Display -->
                @if(isset($configsByGroup) && !empty($configsByGroup))
                    @foreach($configsByGroup as $groupName => $groupConfigs)
                        <div class="config-group mb-4">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-folder me-2"></i>
                                {{ $groupName ?? 'Sem Grupo' }}
                            </h5>

                            <div class="row">
                                @foreach($groupConfigs as $config)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="config-item">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-1">
                                                    {{ $config->chave ?? 'N/A' }}
                                                    @if($config->obrigatorio ?? false)
                                                        <span class="badge bg-danger ms-1">Obrigatório</span>
                                                    @endif
                                                </h6>
                                                <span class="badge bg-secondary">{{ $config->tipo_dado ?? 'string' }}</span>
                                            </div>
                                            
                                            @if($config->descricao ?? false)
                                                <p class="text-muted small mb-2">{{ $config->descricao }}</p>
                                            @endif

                                            <form class="config-form" data-config-chave="{{ $config->chave ?? '' }}">
                                                <div class="mb-2">
                                                    <label class="form-label small">Valor:</label>
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
                                                            <option value="0" {{ $currentValue == '0' ? 'selected' : '' }}>Não (false)</option>
                                                            <option value="1" {{ $currentValue == '1' ? 'selected' : '' }}>Sim (true)</option>
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
                                                
                                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-save me-1"></i>
                                                    Salvar
                                                </button>
                                            </form>

                                            @if($config->valor_padrao ?? false)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <strong>Padrão:</strong> 
                                                        <code class="bg-light px-1 rounded">{{ $config->valor_padrao }}</code>
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Total: {{ array_sum(array_map('count', $configsByGroup)) }} configurações em {{ count($configsByGroup) }} grupos
                        </small>
                    </div>
                @elseif(isset($configs) && $configs->count() > 0)
                    <!-- Fallback para $configs simples -->
                    <div class="row">
                        @foreach($configs as $config)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="config-item">
                                    <h6>{{ $config->chave ?? 'N/A' }}</h6>
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
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhuma configuração encontrada.
                        <br>
                        <a href="/admin/config-simple" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-1"></i>
                            Ir para Config Simples
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

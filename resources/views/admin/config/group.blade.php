@extends('layouts.admin')

@section('title', $group->nome . ' - Configurações')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.config.index') }}">Configurações</a></li>
                        <li class="breadcrumb-item active">{{ $group->nome }}</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="{{ $group->icone_class ?? 'uil uil-cog' }} me-1"></i>
                    {{ $group->nome }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Configurações do Grupo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($group->descricao)
                        <div class="alert alert-info">
                            <i class="uil uil-info-circle me-1"></i>
                            {{ $group->descricao }}
                        </div>
                    @endif

                    @if(count($configs) > 0)
                        <div class="row">
                            @foreach($configs as $chave => $config)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                {{ $config['nome'] }}
                                                @if($config['obrigatorio'] ?? false)
                                                    <span class="badge bg-danger ms-1">Obrigatório</span>
                                                @endif
                                            </h6>
                                            
                                            @if($config['descricao'])
                                                <p class="text-muted small">{{ $config['descricao'] }}</p>
                                            @endif

                                            <div class="config-value-form">
                                                <form onsubmit="saveConfigValue(event, '{{ $chave }}')">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label small">
                                                            Valor:
                                                            <span class="badge bg-secondary ms-1">{{ ucfirst($config['tipo']) }}</span>
                                                            @if(!empty($config['valor_atual']))
                                                                <span class="badge bg-success ms-1">Configurado</span>
                                                            @elseif(!empty($config['valor_padrao']))
                                                                <span class="badge bg-warning ms-1">Padrão</span>
                                                            @else
                                                                <span class="badge bg-danger ms-1">Não Configurado</span>
                                                            @endif
                                                        </label>
                                                        
                                                        @php
                                                            $currentValue = $config['valor_atual'] ?? $config['valor_padrao'] ?? '';
                                                            $placeholder = $config['dica'] ?? "Digite o valor para {$config['nome']}";
                                                        @endphp
                                                        
                                                        @if($config['tipo'] === 'boolean')
                                                            <select name="valor" class="form-select form-select-sm">
                                                                @php
                                                                    $boolValue = filter_var($currentValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                                                                    $isTrue = $boolValue === true || $currentValue === '1' || $currentValue === 'true';
                                                                @endphp
                                                                <option value="1" {{ $isTrue ? 'selected' : '' }}>Sim (true)</option>
                                                                <option value="0" {{ !$isTrue ? 'selected' : '' }}>Não (false)</option>
                                                            </select>
                                                        @elseif($config['tipo'] === 'password')
                                                            <input type="password" name="valor" class="form-control form-control-sm" 
                                                                value="{{ $currentValue }}" 
                                                                placeholder="{{ $placeholder }}">
                                                            @if(!empty($currentValue))
                                                                <small class="text-success">
                                                                    <i class="uil uil-check me-1"></i>
                                                                    Senha configurada ({{ strlen($currentValue) }} caracteres)
                                                                </small>
                                                            @endif
                                                        @elseif(in_array($config['tipo'], ['array', 'json']))
                                                            @php
                                                                if (is_array($currentValue)) {
                                                                    $displayValue = json_encode($currentValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                                                } elseif (!empty($currentValue)) {
                                                                    $decoded = json_decode($currentValue, true);
                                                                    $displayValue = $decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $currentValue;
                                                                } else {
                                                                    $displayValue = $config['tipo'] === 'array' ? '[]' : '{}';
                                                                }
                                                            @endphp
                                                            <textarea name="valor" class="form-control form-control-sm" rows="4" 
                                                                placeholder="{{ $placeholder }}">{{ $displayValue }}</textarea>
                                                            <small class="text-muted">
                                                                <i class="uil uil-info-circle me-1"></i>
                                                                Formato JSON válido obrigatório
                                                            </small>
                                                        @elseif($config['tipo'] === 'email')
                                                            <input type="email" name="valor" class="form-control form-control-sm" 
                                                                value="{{ $currentValue }}" 
                                                                placeholder="{{ $placeholder ?: 'exemplo@dominio.com' }}">
                                                        @elseif($config['tipo'] === 'url')
                                                            <input type="url" name="valor" class="form-control form-control-sm" 
                                                                value="{{ $currentValue }}" 
                                                                placeholder="{{ $placeholder ?: 'https://exemplo.com' }}">
                                                        @elseif($config['tipo'] === 'integer')
                                                            <input type="number" name="valor" class="form-control form-control-sm" 
                                                                value="{{ $currentValue }}" 
                                                                placeholder="{{ $placeholder ?: '0' }}">
                                                        @elseif($config['tipo'] === 'float')
                                                            <input type="number" step="0.01" name="valor" class="form-control form-control-sm" 
                                                                value="{{ $currentValue }}" 
                                                                placeholder="{{ $placeholder ?: '0.00' }}">
                                                        @else
                                                            <input type="text" name="valor" class="form-control form-control-sm" 
                                                                value="{{ $currentValue }}" 
                                                                placeholder="{{ $placeholder }}">
                                                        @endif
                                                        
                                                        @if(!empty($config['valor_atual']) && !empty($config['valor_padrao']) && $config['valor_atual'] != $config['valor_padrao'])
                                                            <small class="text-warning">
                                                                <i class="uil uil-exclamation-triangle me-1"></i>
                                                                Valor diferente do padrão: <code>{{ $config['valor_padrao'] }}</code>
                                                            </small>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-6">
                                                            <select name="site_id" class="form-select form-select-sm">
                                                                <option value="">Todos os sites</option>
                                                                <!-- Sites serão carregados via JavaScript -->
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <select name="ambiente_id" class="form-select form-select-sm">
                                                                <option value="">Todos os ambientes</option>
                                                                <!-- Ambientes serão carregados via JavaScript -->
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex gap-1">
                                                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                                            <i class="uil uil-check me-1"></i>
                                                            Salvar
                                                        </button>
                                                        
                                                        @if(!empty($config['valor_padrao']) && $currentValue != $config['valor_padrao'])
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                                onclick="restoreDefault(this, '{{ $config['valor_padrao'] }}', '{{ $config['tipo'] }}')"
                                                                title="Restaurar valor padrão">
                                                                <i class="uil uil-redo"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        @if(!empty($currentValue))
                                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                onclick="clearValue(this)"
                                                                title="Limpar valor">
                                                                <i class="uil uil-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="mt-2">
                                                @if(!empty($config['valor_padrao']))
                                                    <small class="text-muted d-block">
                                                        <strong>Valor Padrão:</strong> 
                                                        <code class="bg-light px-1 rounded">{{ 
                                                            is_array($config['valor_padrao']) 
                                                                ? json_encode($config['valor_padrao']) 
                                                                : $config['valor_padrao'] 
                                                        }}</code>
                                                    </small>
                                                @endif
                                                
                                                @if(!empty($config['ajuda']))
                                                    <small class="text-info d-block mt-1">
                                                        <i class="uil uil-question-circle me-1"></i>
                                                        {{ $config['ajuda'] }}
                                                    </small>
                                                @endif
                                                
                                                @if(!empty($config['regex_validacao']))
                                                    <small class="text-warning d-block mt-1">
                                                        <i class="uil uil-shield-check me-1"></i>
                                                        Padrão: <code>{{ $config['regex_validacao'] }}</code>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="uil uil-folder-open display-4 text-muted"></i>
                            <h5 class="text-muted mt-2">Nenhuma configuração encontrada</h5>
                            <p class="text-muted">Este grupo ainda não possui configurações definidas.</p>
                            <a href="{{ route('admin.config.create') }}" class="btn btn-primary">
                                <i class="uil uil-plus me-1"></i>
                                Adicionar Configuração
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carregar sites e ambientes
    loadSitesAndEnvironments();
});

async function loadSitesAndEnvironments() {
    try {
        // Carregar sites
        const sitesResponse = await fetch('/admin/config/api/sites');
        const sites = await sitesResponse.json();
        
        // Carregar ambientes
        const environmentsResponse = await fetch('/admin/config/api/environments');
        const environments = await environmentsResponse.json();
        
        // Preencher selects
        document.querySelectorAll('select[name="site_id"]').forEach(select => {
            sites.forEach(site => {
                const option = document.createElement('option');
                option.value = site.id;
                option.textContent = site.nome;
                select.appendChild(option);
            });
        });
        
        document.querySelectorAll('select[name="ambiente_id"]').forEach(select => {
            environments.forEach(env => {
                const option = document.createElement('option');
                option.value = env.id;
                option.textContent = env.nome;
                select.appendChild(option);
            });
        });
    } catch (error) {
        console.error('Erro ao carregar sites e ambientes:', error);
    }
}

async function saveConfigValue(event, chave) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validar campos obrigatórios
    const valorInput = form.querySelector('[name="valor"]');
    if (!valorInput.value.trim() && valorInput.hasAttribute('required')) {
        showError('Este campo é obrigatório!');
        return;
    }
    
    // Validar JSON se necessário
    const tipo = form.closest('.card-body').querySelector('.badge').textContent.toLowerCase();
    if ((tipo === 'json' || tipo === 'array') && valorInput.value.trim()) {
        try {
            JSON.parse(valorInput.value);
        } catch (e) {
            showError('Formato JSON inválido!');
            return;
        }
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Salvando...';
    
    // Adicionar a chave da configuração
    formData.append('chave', chave);
    
    try {
        const response = await fetch('{{ route("admin.config.set-value") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
            submitBtn.innerHTML = '<i class="uil uil-check me-1"></i> Salvo!';
            
            // Atualizar badges de status
            updateStatusBadges(form, valorInput.value);
            
            // Voltar ao texto original após 2 segundos
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
            }, 2000);
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Erro ao salvar configuração.');
        console.error('Erro:', error);
    } finally {
        submitBtn.disabled = false;
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
        }, 2000);
    }
}

function restoreDefault(button, defaultValue, tipo) {
    const form = button.closest('form');
    const valorInput = form.querySelector('[name="valor"]');
    
    if (tipo === 'boolean') {
        valorInput.value = defaultValue === 'true' || defaultValue === '1' ? '1' : '0';
        // Atualizar select
        Array.from(valorInput.options).forEach(option => {
            option.selected = option.value === valorInput.value;
        });
    } else if (tipo === 'json' || tipo === 'array') {
        try {
            const parsed = JSON.parse(defaultValue);
            valorInput.value = JSON.stringify(parsed, null, 2);
        } catch (e) {
            valorInput.value = defaultValue;
        }
    } else {
        valorInput.value = defaultValue;
    }
    
    showSuccess('Valor padrão restaurado!');
}

function clearValue(button) {
    const form = button.closest('form');
    const valorInput = form.querySelector('[name="valor"]');
    const tipo = form.closest('.card-body').querySelector('.badge').textContent.toLowerCase();
    
    if (tipo === 'json') {
        valorInput.value = '{}';
    } else if (tipo === 'array') {
        valorInput.value = '[]';
    } else if (tipo === 'boolean') {
        valorInput.value = '0';
        // Atualizar select
        Array.from(valorInput.options).forEach(option => {
            option.selected = option.value === '0';
        });
    } else {
        valorInput.value = '';
    }
    
    showSuccess('Valor limpo!');
}

function updateStatusBadges(form, newValue) {
    const cardBody = form.closest('.card-body');
    const statusBadges = cardBody.querySelectorAll('.badge');
    
    // Remover badges de status antigos
    statusBadges.forEach(badge => {
        if (badge.textContent.includes('Configurado') || 
            badge.textContent.includes('Não Configurado') || 
            badge.textContent.includes('Padrão')) {
            badge.remove();
        }
    });
    
    // Adicionar novo badge
    const label = cardBody.querySelector('label');
    if (newValue && newValue.trim()) {
        const newBadge = document.createElement('span');
        newBadge.className = 'badge bg-success ms-1';
        newBadge.textContent = 'Configurado';
        label.appendChild(newBadge);
    }
}

// Funções de notificação (usando SweetAlert2)
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: message,
        confirmButtonColor: '#0acf97',
        timer: 3000,
        timerProgressBar: true
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Erro!',
        text: message,
        confirmButtonColor: '#fa5c7c'
    });
}
</script>
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'Histórico de Configuração')

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
                        <li class="breadcrumb-item active">Histórico</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="uil uil-history me-1"></i>
                    Histórico: {{ $config->chave }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Info da configuração -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $config->nome }}</h5>
                            <p class="text-muted mb-0">{{ $config->descricao }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.config.edit', $config->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-1"></i>
                                    Editar
                                </a>
                                <a href="{{ route('admin.config.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="uil uil-arrow-left me-1"></i>
                                    Voltar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.config.history', $config->id) }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="site" class="form-label">Site</label>
                            <select name="site" id="site" class="form-select">
                                <option value="">Todos os sites</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->codigo }}" 
                                        {{ request('site') === $site->codigo ? 'selected' : '' }}>
                                        {{ $site->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="environment" class="form-label">Ambiente</label>
                            <select name="environment" id="environment" class="form-select">
                                <option value="">Todos os ambientes</option>
                                @foreach($environments as $environment)
                                    <option value="{{ $environment->codigo }}" 
                                        {{ request('environment') === $environment->codigo ? 'selected' : '' }}>
                                        {{ $environment->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="acao" class="form-label">Ação</label>
                            <select name="acao" id="acao" class="form-select">
                                <option value="">Todas as ações</option>
                                <option value="create" {{ request('acao') === 'create' ? 'selected' : '' }}>Criação</option>
                                <option value="update" {{ request('acao') === 'update' ? 'selected' : '' }}>Atualização</option>
                                <option value="delete" {{ request('acao') === 'delete' ? 'selected' : '' }}>Exclusão</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="usuario" class="form-label">Usuário</label>
                            <input type="text" name="usuario" id="usuario" 
                                class="form-control" placeholder="Nome do usuário..."
                                value="{{ request('usuario') }}">
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" type="submit">
                                    <i class="uil uil-search me-1"></i>
                                    Filtrar
                                </button>
                                <a href="{{ route('admin.config.history', $config->id) }}" class="btn btn-secondary">
                                    <i class="uil uil-refresh me-1"></i>
                                    Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($history->count() > 0)
                        <div class="timeline-container">
                            @foreach($history as $entry)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $entry->acao === 'create' ? 'success' : ($entry->acao === 'delete' ? 'danger' : 'primary') }}">
                                        <i class="uil uil-{{ $entry->acao === 'create' ? 'plus' : ($entry->acao === 'delete' ? 'trash' : 'edit') }}"></i>
                                    </div>
                                    
                                    <div class="timeline-content">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <span class="badge bg-{{ $entry->acao === 'create' ? 'success' : ($entry->acao === 'delete' ? 'danger' : 'primary') }} me-2">
                                                                {{ ucfirst($entry->acao) }}
                                                            </span>
                                                            @if($entry->site_nome)
                                                                <span class="badge bg-info me-1">{{ $entry->site_nome }}</span>
                                                            @endif
                                                            @if($entry->ambiente_nome)
                                                                <span class="badge bg-secondary me-1">{{ $entry->ambiente_nome }}</span>
                                                            @endif
                                                        </h6>
                                                        <p class="text-muted mb-2">
                                                            <i class="uil uil-clock me-1"></i>
                                                            {{ \Carbon\Carbon::parse($entry->created_at)->format('d/m/Y H:i:s') }}
                                                            <span class="ms-2">
                                                                <i class="uil uil-user me-1"></i>
                                                                {{ $entry->usuario_nome ?: 'Sistema' }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    
                                                    @if($entry->acao !== 'delete')
                                                        <button class="btn btn-sm btn-outline-secondary" 
                                                            type="button" data-bs-toggle="collapse" 
                                                            data-bs-target="#details-{{ $entry->id }}">
                                                            <i class="uil uil-angle-down"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                @if($entry->acao !== 'delete')
                                                    <div class="collapse" id="details-{{ $entry->id }}">
                                                        <hr>
                                                        
                                                        @if($entry->acao === 'update' && $entry->valor_anterior !== null)
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6 class="text-muted">Valor Anterior:</h6>
                                                                    <div class="bg-light p-2 rounded">
                                                                        <code>{{ $entry->valor_anterior ?? 'N/A' }}</code>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6 class="text-muted">Novo Valor:</h6>
                                                                    <div class="bg-light p-2 rounded">
                                                                        <code>{{ $entry->valor_novo ?? 'N/A' }}</code>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div>
                                                                <h6 class="text-muted">Valor:</h6>
                                                                <div class="bg-light p-2 rounded">
                                                                    <code>{{ $entry->valor_novo ?? 'N/A' }}</code>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($entry->contexto_info)
                                                            <div class="mt-3">
                                                                <h6 class="text-muted">Contexto:</h6>
                                                                <div class="bg-light p-2 rounded">
                                                                    <small>{{ $entry->contexto_info }}</small>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($entry->acao === 'update')
                                                            <div class="mt-3">
                                                                <button class="btn btn-sm btn-outline-primary" 
                                                                    onclick="restoreValue({{ $entry->id }})">
                                                                    <i class="uil uil-redo me-1"></i>
                                                                    Restaurar Este Valor
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-muted">
                                                        <i class="uil uil-info-circle me-1"></i>
                                                        Configuração foi removida do sistema
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $history->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="uil uil-history display-4 d-block mb-3"></i>
                                <h5>Nenhum histórico encontrado</h5>
                                <p>Não há registros de alterações para esta configuração com os filtros aplicados.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação para restaurar valor -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restaurar Valor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja restaurar este valor?</p>
                <div id="restore-preview"></div>
                <p class="text-info mt-2">
                    <i class="uil uil-info-circle me-1"></i>
                    Esta ação criará uma nova entrada no histórico.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmRestore">
                    <i class="uil uil-redo me-1"></i>
                    Restaurar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-container {
    position: relative;
    padding-left: 30px;
}

.timeline-container::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3eaef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 10px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    z-index: 1;
}

.timeline-content {
    margin-left: 15px;
}

.timeline-item:last-child .timeline-container::before {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
async function restoreValue(historyId) {
    try {
        // Buscar detalhes do histórico
        const response = await fetch(`{{ route('admin.config.history-detail', ['config' => $config->id, 'history' => '__HISTORY_ID__']) }}`.replace('__HISTORY_ID__', historyId), {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Mostrar preview no modal
            document.getElementById('restore-preview').innerHTML = `
                <div class="bg-light p-2 rounded">
                    <strong>Valor a ser restaurado:</strong><br>
                    <code>${data.valor_formatted}</code>
                </div>
            `;
            
            // Configurar botão de confirmação
            const confirmBtn = document.getElementById('confirmRestore');
            confirmBtn.onclick = () => executeRestore(historyId);
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
            modal.show();
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Erro ao carregar detalhes do histórico.');
        console.error('Erro:', error);
    }
}

async function executeRestore(historyId) {
    try {
        const response = await fetch('{{ route("admin.config.restore-value", $config->id) }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                history_id: historyId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
            // Fechar modal
            bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();
            // Recarregar página após 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Erro ao restaurar valor.');
        console.error('Erro:', error);
    }
}

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

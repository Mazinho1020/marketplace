@extends('layouts.comerciante')

@section('title', 'Notificações')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-bell me-2"></i>
                        Notificações
                    </h1>
                    <p class="text-muted mb-0">Gerencie suas notificações e alertas</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="marcarTodasComoLidas()">
                        <i class="fas fa-check-double me-1"></i>
                        Marcar Todas como Lidas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Notificações
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Não Lidas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['nao_lidas'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['hoje'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Taxa de Leitura
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['taxa_leitura'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.notificacoes.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="entregue" {{ request('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                                <option value="lido" {{ request('status') == 'lido' ? 'selected' : '' }}>Lido</option>
                                <option value="erro" {{ request('status') == 'erro' ? 'selected' : '' }}>Erro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="canal">Canal</label>
                            <select name="canal" id="canal" class="form-control">
                                <option value="">Todos</option>
                                <option value="in_app" {{ request('canal') == 'in_app' ? 'selected' : '' }}>In-App</option>
                                <option value="push" {{ request('canal') == 'push' ? 'selected' : '' }}>Push</option>
                                <option value="email" {{ request('canal') == 'email' ? 'selected' : '' }}>Email</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="data_inicio">Data Início</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="data_fim">Data Fim</label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ request('data_fim') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Notificações -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Notificações</h6>
        </div>
        <div class="card-body">
            @if($notificacoes->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notificacoes as $notificacao)
                        <div class="list-group-item list-group-item-action {{ is_null($notificacao->lido_em) ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <i class="fas fa-bell {{ is_null($notificacao->lido_em) ? 'text-warning' : 'text-muted' }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 {{ is_null($notificacao->lido_em) ? 'font-weight-bold' : '' }}">
                                            {{ $notificacao->titulo }}
                                        </h6>
                                        <p class="mb-1">{{ Str::limit($notificacao->mensagem, 100) }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-tag"></i> {{ ucfirst($notificacao->canal) }}
                                            @if($notificacao->lido_em)
                                                • <i class="fas fa-check text-success"></i> Lida em {{ $notificacao->lido_em->format('d/m/Y H:i') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted">{{ $notificacao->created_at->diffForHumans() }}</small>
                                    <div class="mt-1">
                                        @if(is_null($notificacao->lido_em))
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="marcarComoLida({{ $notificacao->id }})">
                                                <i class="fas fa-check"></i> Marcar como Lida
                                            </button>
                                        @endif
                                        <a href="{{ route('comerciantes.notificacoes.show', $notificacao->id) }}" 
                                           class="btn btn-sm btn-outline-secondary ml-1">
                                            <i class="fas fa-eye"></i> Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notificacoes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma notificação encontrada</h5>
                    <p class="text-muted">Quando você receber notificações, elas aparecerão aqui.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function marcarComoLida(id) {
    fetch(`/comerciantes/notificacoes/${id}/marcar-lida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao marcar como lida');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao marcar como lida');
    });
}

function marcarTodasComoLidas() {
    if (confirm('Tem certeza que deseja marcar todas as notificações como lidas?')) {
        fetch('/comerciantes/notificacoes/marcar-todas-lidas', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao marcar todas como lidas');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao marcar todas como lidas');
        });
    }
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Carteiras de Fidelidade')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-wallet text-primary me-2"></i>
                        Carteiras de Fidelidade
                    </h1>
                    <p class="text-muted mb-0">Gerencie as carteiras dos clientes do programa de fidelidade</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.carteiras.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nova Carteira
                    </a>
                    <a href="{{ route('fidelidade.carteiras.exportar') }}" class="btn btn-outline-success">
                        <i class="fas fa-file-excel me-2"></i>
                        Exportar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar por Cliente</label>
                            <input type="text" name="search" class="form-control" placeholder="ID do cliente ou nome"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Todos</option>
                                <option value="ativa" {{ request('status')==='ativa' ? 'selected' : '' }}>Ativa</option>
                                <option value="bloqueada" {{ request('status')==='bloqueada' ? 'selected' : '' }}>
                                    Bloqueada</option>
                                <option value="suspensa" {{ request('status')==='suspensa' ? 'selected' : '' }}>Suspensa
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Nível</label>
                            <select name="nivel" class="form-select">
                                <option value="">Todos</option>
                                <option value="bronze" {{ request('nivel')==='bronze' ? 'selected' : '' }}>Bronze
                                </option>
                                <option value="prata" {{ request('nivel')==='prata' ? 'selected' : '' }}>Prata</option>
                                <option value="ouro" {{ request('nivel')==='ouro' ? 'selected' : '' }}>Ouro</option>
                                <option value="diamond" {{ request('nivel')==='diamond' ? 'selected' : '' }}>Diamond
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Saldo Mínimo</label>
                            <input type="number" name="saldo_min" class="form-control" placeholder="0.00" step="0.01"
                                value="{{ request('saldo_min') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Total de Carteiras</h6>
                            <h4 class="mb-0">{{ $estatisticas['total_carteiras'] ?? 0 }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Carteiras Ativas</h6>
                            <h4 class="mb-0">{{ $estatisticas['carteiras_ativas'] ?? 0 }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Saldo Total</h6>
                            <h4 class="mb-0">R$ {{ number_format($estatisticas['saldo_total'] ?? 0, 2, ',', '.') }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Cashback Total</h6>
                            <h4 class="mb-0">R$ {{ number_format($estatisticas['cashback_total'] ?? 0, 2, ',', '.') }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Carteiras -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Lista de Carteiras
                        @if(isset($carteiras) && $carteiras->hasPages())
                        ({{ $carteiras->total() }} encontradas)
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($carteiras) && $carteiras->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Nível Atual</th>
                                    <th>Cashback</th>
                                    <th>Pontos</th>
                                    <th>Saldo Total</th>
                                    <th>Status</th>
                                    <th>Última Transação</th>
                                    <th width="200">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carteiras as $carteira)
                                <tr>
                                    <td class="fw-bold">#{{ $carteira->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">Cliente #{{ $carteira->cliente_id }}</div>
                                                <small class="text-muted">ID: {{ $carteira->cliente_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                        $badgeClass = match($carteira->nivel_atual) {
                                        'diamond' => 'bg-info',
                                        'ouro' => 'bg-warning',
                                        'prata' => 'bg-secondary',
                                        default => 'bg-dark'
                                        };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            <i class="fas fa-star me-1"></i>
                                            {{ ucfirst($carteira->nivel_atual) }}
                                        </span>
                                    </td>
                                    <td class="text-success fw-bold">
                                        R$ {{ number_format($carteira->saldo_cashback, 2, ',', '.') }}
                                    </td>
                                    <td class="text-primary fw-bold">
                                        {{ number_format($carteira->saldo_pontos, 0, ',', '.') }}
                                    </td>
                                    <td class="text-info fw-bold">
                                        R$ {{ number_format($carteira->saldo_total_disponivel, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        @php
                                        $statusClass = match($carteira->status) {
                                        'ativa' => 'bg-success',
                                        'bloqueada' => 'bg-danger',
                                        'suspensa' => 'bg-warning',
                                        default => 'bg-secondary'
                                        };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($carteira->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($carteira->ultima_transacao)
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($carteira->ultima_transacao)->format('d/m/Y H:i')
                                            }}
                                        </small>
                                        @else
                                        <small class="text-muted">Nunca</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('fidelidade.carteiras.show', $carteira->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('fidelidade.carteiras.edit', $carteira->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($carteira->status === 'ativa')
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Bloquear"
                                                onclick="bloquearCarteira({{ $carteira->id }})">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                title="Desbloquear" onclick="desbloquearCarteira({{ $carteira->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif

                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                                onclick="excluirCarteira({{ $carteira->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    @if($carteiras->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $carteiras->appends(request()->query())->links() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-wallet fa-4x text-gray-300 mb-3"></i>
                        <h5 class="text-muted">Nenhuma carteira encontrada</h5>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['search', 'status', 'nivel', 'saldo_min']))
                            Tente ajustar os filtros de busca ou
                            <a href="{{ route('fidelidade.carteiras.index') }}">limpar filtros</a>
                            @else
                            Comece criando uma nova carteira para um cliente
                            @endif
                        </p>
                        <a href="{{ route('fidelidade.carteiras.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Criar Nova Carteira
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modais e Scripts -->
<script>
    function bloquearCarteira(id) {
    if (confirm('Tem certeza que deseja bloquear esta carteira?')) {
        fetch(`/fidelidade/carteiras/${id}/bloquear`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao bloquear carteira');
            }
        })
        .catch(error => {
            alert('Erro ao bloquear carteira');
            console.error('Error:', error);
        });
    }
}

function desbloquearCarteira(id) {
    if (confirm('Tem certeza que deseja desbloquear esta carteira?')) {
        fetch(`/fidelidade/carteiras/${id}/desbloquear`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao desbloquear carteira');
            }
        })
        .catch(error => {
            alert('Erro ao desbloquear carteira');
            console.error('Error:', error);
        });
    }
}

function excluirCarteira(id) {
    if (confirm('Tem certeza que deseja EXCLUIR esta carteira?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão perdidos!')) {
        fetch(`/fidelidade/carteiras/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                return response.json().then(data => {
                    alert(data.message || 'Erro ao excluir carteira');
                });
            }
        })
        .catch(error => {
            alert('Erro ao excluir carteira');
            console.error('Error:', error);
        });
    }
}
</script>

<style>
    .avatar-sm {
        width: 2rem;
        height: 2rem;
        font-size: 0.875rem;
    }
</style>
@endsection
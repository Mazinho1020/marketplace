@extends('layouts.app')

@section('title', 'Regras de Cashback')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        Regras de Cashback
                    </h1>
                    <p class="text-muted mb-0">Configure as regras para distribuição de cashback</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.regras.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nova Regra
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
                            <label class="form-label">Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Tipo ou descrição"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Todos</option>
                                <option value="ativa" {{ request('status')==='ativa' ? 'selected' : '' }}>Ativa</option>
                                <option value="inativa" {{ request('status')==='inativa' ? 'selected' : '' }}>Inativa
                                </option>
                                <option value="pausada" {{ request('status')==='pausada' ? 'selected' : '' }}>Pausada
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Regra</label>
                            <select name="tipo" class="form-select">
                                <option value="">Todos</option>
                                <option value="categoria" {{ request('tipo')==='categoria' ? 'selected' : '' }}>Por
                                    Categoria</option>
                                <option value="produto" {{ request('tipo')==='produto' ? 'selected' : '' }}>Por Produto
                                </option>
                                <option value="dia_semana" {{ request('tipo')==='dia_semana' ? 'selected' : '' }}>Por
                                    Dia da Semana</option>
                                <option value="horario" {{ request('tipo')==='horario' ? 'selected' : '' }}>Por Horário
                                </option>
                                <option value="primeira_compra" {{ request('tipo')==='primeira_compra' ? 'selected' : ''
                                    }}>Primeira Compra</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cashback Mín.</label>
                            <input type="number" name="cashback_min" class="form-control" placeholder="0.00" step="0.01"
                                value="{{ request('cashback_min') }}">
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

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase mb-1">Total de Regras</h6>
                            <h4 class="mb-0">{{ $estatisticas['total_regras'] ?? 0 }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x opacity-50"></i>
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
                            <h6 class="text-uppercase mb-1">Regras Ativas</h6>
                            <h4 class="mb-0">{{ $estatisticas['regras_ativas'] ?? 0 }}</h4>
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
                            <h6 class="text-uppercase mb-1">Cashback Médio</h6>
                            <h4 class="mb-0">{{ number_format($estatisticas['cashback_medio'] ?? 0, 1) }}%</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x opacity-50"></i>
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
                            <h6 class="text-uppercase mb-1">Regras Pausadas</h6>
                            <h4 class="mb-0">{{ $estatisticas['regras_pausadas'] ?? 0 }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Regras -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Lista de Regras
                        @if(isset($regras) && $regras->hasPages())
                        ({{ $regras->total() }} encontradas)
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($regras) && $regras->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo de Regra</th>
                                    <th>Condições</th>
                                    <th>Cashback</th>
                                    <th>Período</th>
                                    <th>Status</th>
                                    <th width="150">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regras as $regra)
                                <tr>
                                    <td class="fw-bold">#{{ $regra->id }}</td>
                                    <td>
                                        @php
                                        $tipoLabel = match($regra->tipo_regra) {
                                        'categoria' => 'Por Categoria',
                                        'produto' => 'Por Produto',
                                        'dia_semana' => 'Dia da Semana',
                                        'horario' => 'Por Horário',
                                        'primeira_compra' => 'Primeira Compra',
                                        default => 'Geral'
                                        };
                                        $tipoCor = match($regra->tipo_regra) {
                                        'categoria' => 'bg-primary',
                                        'produto' => 'bg-success',
                                        'dia_semana' => 'bg-info',
                                        'horario' => 'bg-warning',
                                        'primeira_compra' => 'bg-danger',
                                        default => 'bg-secondary'
                                        };
                                        @endphp
                                        <span class="badge {{ $tipoCor }}">{{ $tipoLabel }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @if($regra->dia_semana)
                                            @php
                                            $diasSemana = [
                                            0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça',
                                            3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'
                                            ];
                                            @endphp
                                            Dia: {{ $diasSemana[$regra->dia_semana] ?? 'N/A' }}<br>
                                            @endif
                                            @if($regra->horario_inicio && $regra->horario_fim)
                                            Horário: {{ substr($regra->horario_inicio, 0, 5) }} às {{
                                            substr($regra->horario_fim, 0, 5) }}<br>
                                            @endif
                                            @if($regra->referencia_id)
                                            Ref ID: {{ $regra->referencia_id }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-success">
                                                {{ number_format($regra->percentual_cashback, 2) }}%
                                            </span>
                                            @if($regra->valor_maximo_cashback)
                                            <small class="text-muted">
                                                Máx: R$ {{ number_format($regra->valor_maximo_cashback, 2, ',', '.') }}
                                            </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            Criada: {{ \Carbon\Carbon::parse($regra->created_at)->format('d/m/Y') }}<br>
                                            @if($regra->updated_at != $regra->created_at)
                                            Atualizada: {{ \Carbon\Carbon::parse($regra->updated_at)->format('d/m/Y') }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                        $statusClass = match($regra->ativo) {
                                        1 => 'bg-success',
                                        0 => 'bg-danger',
                                        default => 'bg-secondary'
                                        };
                                        $statusText = $regra->ativo ? 'Ativa' : 'Inativa';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('fidelidade.regras.show', $regra->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('fidelidade.regras.edit', $regra->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($regra->ativo)
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                title="Desativar" onclick="toggleRegra({{ $regra->id }}, false)">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-sm btn-outline-success" title="Ativar"
                                                onclick="toggleRegra({{ $regra->id }}, true)">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($regras->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $regras->appends(request()->query())->links() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-cogs fa-4x text-gray-300 mb-3"></i>
                        <h5 class="text-muted">Nenhuma regra encontrada</h5>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['search', 'status', 'tipo']))
                            Tente ajustar os filtros de busca ou
                            <a href="{{ route('fidelidade.regras.index') }}">limpar filtros</a>
                            @else
                            Configure as primeiras regras para distribuição de cashback
                            @endif
                        </p>
                        <a href="{{ route('fidelidade.regras.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Criar Nova Regra
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRegra(id, ativar) {
    const acao = ativar ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${acao} esta regra?`)) {
        const endpoint = ativar ? 'ativar' : 'desativar';
        fetch(`/fidelidade/regras/${id}/${endpoint}`, {
            method: 'POST',
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
                alert(`Erro ao ${acao} regra`);
            }
        })
        .catch(error => {
            alert(`Erro ao ${acao} regra`);
            console.error('Error:', error);
        });
    }
}
</script>
@endsection
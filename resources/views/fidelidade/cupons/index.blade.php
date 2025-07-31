@extends('layouts.app')

@section('title', 'Cupons de Desconto')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-tags text-primary me-2"></i>
                        Cupons de Desconto
                    </h1>
                    <p class="text-muted mb-0">Gerencie cupons e promoções do programa de fidelidade</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.cupons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Novo Cupom
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
                            <label class="form-label">Buscar Cupom</label>
                            <input type="text" name="search" class="form-control" placeholder="Código ou descrição"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Todos</option>
                                <option value="ativo" {{ request('status')==='ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ request('status')==='inativo' ? 'selected' : '' }}>Inativo
                                </option>
                                <option value="expirado" {{ request('status')==='expirado' ? 'selected' : '' }}>Expirado
                                </option>
                                <option value="esgotado" {{ request('status')==='esgotado' ? 'selected' : '' }}>Esgotado
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="">Todos</option>
                                <option value="percentual" {{ request('tipo')==='percentual' ? 'selected' : '' }}>
                                    Percentual</option>
                                <option value="valor_fixo" {{ request('tipo')==='valor_fixo' ? 'selected' : '' }}>Valor
                                    Fixo</option>
                                <option value="frete_gratis" {{ request('tipo')==='frete_gratis' ? 'selected' : '' }}>
                                    Frete Grátis</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período de Validade</label>
                            <input type="date" name="data_validade" class="form-control"
                                value="{{ request('data_validade') }}">
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
                            <h6 class="text-uppercase mb-1">Total de Cupons</h6>
                            <h4 class="mb-0">{{ $estatisticas['total_cupons'] ?? 0 }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x opacity-50"></i>
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
                            <h6 class="text-uppercase mb-1">Cupons Ativos</h6>
                            <h4 class="mb-0">{{ $estatisticas['cupons_ativos'] ?? 0 }}</h4>
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
                            <h6 class="text-uppercase mb-1">Cupons Utilizados</h6>
                            <h4 class="mb-0">{{ $estatisticas['cupons_utilizados'] ?? 0 }}</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
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
                            <h6 class="text-uppercase mb-1">Taxa de Conversão</h6>
                            <h4 class="mb-0">{{ number_format($estatisticas['taxa_conversao'] ?? 0, 1) }}%</h4>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Cupons -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Lista de Cupons
                        @if(isset($cupons) && $cupons->hasPages())
                        ({{ $cupons->total() }} encontrados)
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($cupons) && $cupons->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Uso</th>
                                    <th>Validade</th>
                                    <th>Status</th>
                                    <th width="150">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cupons as $cupom)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $cupom->codigo }}</td>
                                    <td>{{ $cupom->descricao ?? 'Sem descrição' }}</td>
                                    <td>
                                        @php
                                        $tipoLabel = match($cupom->tipo_desconto) {
                                        'percentual' => 'Percentual',
                                        'valor_fixo' => 'Valor Fixo',
                                        'frete_gratis' => 'Frete Grátis',
                                        default => 'Outro'
                                        };
                                        $tipoClass = match($cupom->tipo_desconto) {
                                        'percentual' => 'bg-info',
                                        'valor_fixo' => 'bg-success',
                                        'frete_gratis' => 'bg-warning',
                                        default => 'bg-secondary'
                                        };
                                        @endphp
                                        <span class="badge {{ $tipoClass }}">{{ $tipoLabel }}</span>
                                    </td>
                                    <td class="fw-bold">
                                        @if($cupom->tipo_desconto === 'percentual')
                                        {{ $cupom->valor_desconto }}%
                                        @elseif($cupom->tipo_desconto === 'valor_fixo')
                                        R$ {{ number_format($cupom->valor_desconto, 2, ',', '.') }}
                                        @else
                                        Grátis
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $cupom->quantidade_usada ?? 0 }} /
                                            {{ $cupom->quantidade_maxima ? $cupom->quantidade_maxima : '∞' }}
                                        </small>
                                        @if($cupom->quantidade_maxima)
                                        @php
                                        $percentual = ($cupom->quantidade_usada / $cupom->quantidade_maxima) * 100;
                                        $progressClass = $percentual < 50 ? 'bg-success' : ($percentual < 80
                                            ? 'bg-warning' : 'bg-danger' ); @endphp <div class="progress mt-1"
                                            style="height: 4px;">
                                            <div class="progress-bar {{ $progressClass }}"
                                                style="width: {{ $percentual }}%"></div>
                    </div>
                    @endif
                    </td>
                    <td>
                        @if($cupom->data_validade)
                        @php
                        $dataValidade = \Carbon\Carbon::parse($cupom->data_validade);
                        $isExpired = $dataValidade->isPast();
                        @endphp
                        <span class="{{ $isExpired ? 'text-danger' : 'text-muted' }}">
                            {{ $dataValidade->format('d/m/Y') }}
                        </span>
                        @if($isExpired)
                        <br><small class="text-danger">Expirado</small>
                        @endif
                        @else
                        <span class="text-success">Sem limite</span>
                        @endif
                    </td>
                    <td>
                        @php
                        $statusClass = match($cupom->status) {
                        'ativo' => 'bg-success',
                        'inativo' => 'bg-secondary',
                        'expirado' => 'bg-danger',
                        'esgotado' => 'bg-warning',
                        default => 'bg-secondary'
                        };
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst($cupom->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('fidelidade.cupons.show', $cupom->id) }}"
                                class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('fidelidade.cupons.edit', $cupom->id) }}"
                                class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                onclick="excluirCupom({{ $cupom->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    </tr>
                    @endforeach
                    </tbody>
                    </table>
                </div>

                @if($cupons->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $cupons->appends(request()->query())->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-muted">Nenhum cupom encontrado</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'tipo']))
                        Tente ajustar os filtros de busca ou
                        <a href="{{ route('fidelidade.cupons.index') }}">limpar filtros</a>
                        @else
                        Comece criando um novo cupom de desconto
                        @endif
                    </p>
                    <a href="{{ route('fidelidade.cupons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Criar Novo Cupom
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<script>
    function excluirCupom(id) {
    if (confirm('Tem certeza que deseja excluir este cupom?')) {
        fetch(`/fidelidade/cupons/${id}`, {
            method: 'DELETE',
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
                alert('Erro ao excluir cupom');
            }
        })
        .catch(error => {
            alert('Erro ao excluir cupom');
            console.error('Error:', error);
        });
    }
}
</script>
@endsection
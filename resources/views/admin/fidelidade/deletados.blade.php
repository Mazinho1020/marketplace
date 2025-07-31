@extends('layouts.admin')

@section('title', 'Registros Deletados - Fidelidade')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-trash-alt me-2"></i>
                    Registros Deletados - {{ ucfirst($tipo) }}
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.fidelidade.index') }}">Fidelidade</a></li>
                        <li class="breadcrumb-item active">Registros Deletados</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.fidelidade.index') }}" class="btn btn-primary">
                    <i class="uil uil-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros por Tipo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Filtrar por Tipo:</h6>
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.fidelidade.deletados', 'carteiras') }}" 
                           class="btn btn-outline-primary {{ $tipo === 'carteiras' ? 'active' : '' }}">
                            Carteiras
                        </a>
                        <a href="{{ route('admin.fidelidade.deletados', 'cupons') }}" 
                           class="btn btn-outline-success {{ $tipo === 'cupons' ? 'active' : '' }}">
                            Cupons
                        </a>
                        <a href="{{ route('admin.fidelidade.deletados', 'creditos') }}" 
                           class="btn btn-outline-info {{ $tipo === 'creditos' ? 'active' : '' }}">
                            Créditos
                        </a>
                        <a href="{{ route('admin.fidelidade.deletados', 'conquistas') }}" 
                           class="btn btn-outline-warning {{ $tipo === 'conquistas' ? 'active' : '' }}">
                            Conquistas
                        </a>
                        <a href="{{ route('admin.fidelidade.deletados', 'transacoes') }}" 
                           class="btn btn-outline-secondary {{ $tipo === 'transacoes' ? 'active' : '' }}">
                            Transações
                        </a>
                        <a href="{{ route('admin.fidelidade.deletados', 'regras') }}" 
                           class="btn btn-outline-danger {{ $tipo === 'regras' ? 'active' : '' }}">
                            Regras
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Registros Deletados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-list-ul me-2"></i>
                        Registros Deletados - {{ ucfirst($tipo) }}
                        <span class="badge bg-warning ms-2">{{ $dados->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($dados->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        @if($tipo === 'carteiras')
                                            <th>Cliente ID</th>
                                            <th>Empresa ID</th>
                                            <th>Status</th>
                                            <th>Nível</th>
                                        @elseif($tipo === 'cupons')
                                            <th>Código</th>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                        @elseif($tipo === 'creditos')
                                            <th>Cliente ID</th>
                                            <th>Tipo</th>
                                            <th>Valor Original</th>
                                            <th>Status</th>
                                        @elseif($tipo === 'conquistas')
                                            <th>Nome</th>
                                            <th>XP Recompensa</th>
                                            <th>Crédito Recompensa</th>
                                            <th>Ativo</th>
                                        @elseif($tipo === 'transacoes')
                                            <th>Cliente ID</th>
                                            <th>Tipo</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                        @endif
                                        <th>Deletado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dados as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            @if($tipo === 'carteiras')
                                                <td>{{ $item->cliente_id }}</td>
                                                <td>{{ $item->empresa_id }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item->status === 'ativa' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ ucfirst($item->nivel_atual) }}</span>
                                                </td>
                                            @elseif($tipo === 'cupons')
                                                <td><code>{{ $item->codigo }}</code></td>
                                                <td>{{ $item->nome }}</td>
                                                <td>{{ $item->tipo }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item->status === 'ativo' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </td>
                                            @elseif($tipo === 'creditos')
                                                <td>{{ $item->cliente_id }}</td>
                                                <td>{{ $item->tipo }}</td>
                                                <td>R$ {{ number_format($item->valor_original, 2, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item->status === 'ativo' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </td>
                                            @elseif($tipo === 'conquistas')
                                                <td>{{ $item->nome }}</td>
                                                <td>{{ $item->xp_recompensa }}</td>
                                                <td>R$ {{ number_format($item->credito_recompensa, 2, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item->ativo ? 'success' : 'danger' }}">
                                                        {{ $item->ativo ? 'Sim' : 'Não' }}
                                                    </span>
                                                </td>
                                            @elseif($tipo === 'transacoes')
                                                <td>{{ $item->cliente_id }}</td>
                                                <td>{{ $item->tipo }}</td>
                                                <td>R$ {{ number_format($item->valor, 2, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($item->status) }}</span>
                                                </td>
                                            @endif
                                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            onclick="restaurarRegistro('{{ $tipo }}', {{ $item->id }})">
                                                        <i class="uil uil-redo"></i>
                                                        Restaurar
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="deletarPermanente('{{ $tipo }}', {{ $item->id }})">
                                                        <i class="uil uil-times"></i>
                                                        Excluir
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $dados->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="uil uil-smile text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhum registro deletado encontrado</h5>
                            <p class="text-muted">Não há {{ $tipo }} deletados no momento.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function restaurarRegistro(tipo, id) {
    if (confirm('Tem certeza que deseja restaurar este registro?')) {
        fetch('{{ route("admin.fidelidade.restaurar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tipo: tipo.slice(0, -1), // Remove 's' do final
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao restaurar registro');
        });
    }
}

function deletarPermanente(tipo, id) {
    if (confirm('ATENÇÃO: Esta ação é irreversível! Tem certeza que deseja deletar permanentemente este registro?')) {
        fetch('{{ route("admin.fidelidade.deletar-permanente") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tipo: tipo.slice(0, -1), // Remove 's' do final
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao deletar registro');
        });
    }
}
</script>
@endpush
@endsection

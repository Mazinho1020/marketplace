@extends('layouts.comerciante')

@section('title', 'Histórico de Preços')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Histórico de Preços</h1>
                    <p class="text-muted">Gerencie o histórico de mudanças de preços dos seus produtos</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.produtos.historico-precos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nova Entrada
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('comerciantes.produtos.historico-precos.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="produto_id" class="form-label">Produto</label>
                                <select name="produto_id" id="produto_id" class="form-select">
                                    <option value="">Todos os produtos</option>
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" {{ request('produto_id') == $produto->id ? 'selected' : '' }}>
                                            {{ $produto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ request('data_fim') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search me-2"></i>Filtrar
                                    </button>
                                    <a href="{{ route('comerciantes.produtos.historico-precos.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Limpar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Histórico -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Histórico de Alterações
                    </h5>
                </div>
                <div class="card-body">
                    @if($historicoPrecos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preço Anterior</th>
                                        <th>Preço Novo</th>
                                        <th>Variação</th>
                                        <th>Data da Alteração</th>
                                        <th>Usuário</th>
                                        <th>Motivo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historicoPrecos as $historico)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($historico->produto->imagem_principal)
                                                    <img src="{{ asset('storage/' . $historico->produto->imagem_principal) }}" 
                                                         alt="{{ $historico->produto->nome }}" 
                                                         class="rounded me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $historico->produto->nome }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $historico->produto->sku }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-muted">R$ {{ number_format($historico->preco_venda_anterior, 2, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">R$ {{ number_format($historico->preco_venda_novo, 2, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $variacao = $historico->preco_venda_novo - $historico->preco_venda_anterior;
                                                $percentual = $historico->preco_venda_anterior > 0 ? (($variacao / $historico->preco_venda_anterior) * 100) : 0;
                                            @endphp
                                            @if($variacao > 0)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-arrow-up me-1"></i>
                                                    +R$ {{ number_format($variacao, 2, ',', '.') }}
                                                    ({{ number_format($percentual, 1) }}%)
                                                </span>
                                            @elseif($variacao < 0)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-arrow-down me-1"></i>
                                                    R$ {{ number_format($variacao, 2, ',', '.') }}
                                                    ({{ number_format($percentual, 1) }}%)
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-minus me-1"></i>
                                                    Sem alteração
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $historico->data_alteracao->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            {{ $historico->usuario->name ?? 'Sistema' }}
                                        </td>
                                        <td>
                                            @if($historico->motivo)
                                                <span class="badge bg-info">{{ ucfirst($historico->motivo) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('comerciantes.produtos.historico-precos.show', $historico) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('comerciantes.produtos.historico-precos.edit', $historico) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('comerciantes.produtos.historico-precos.destroy', $historico) }}" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este histórico?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        @if($historicoPrecos->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $historicoPrecos->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum histórico encontrado</h5>
                            <p class="text-muted mb-4">Não há registros de alterações de preços com os filtros aplicados.</p>
                            <a href="{{ route('comerciantes.produtos.historico-precos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Adicionar Primeira Entrada
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterForm = document.querySelector('form');
    const selects = filterForm.querySelectorAll('select, input[type="date"]');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: auto-submit on filter change
            // filterForm.submit();
        });
    });
});
</script>
@endpush

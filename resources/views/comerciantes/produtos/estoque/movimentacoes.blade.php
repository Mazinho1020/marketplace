@extends('comerciantes.layouts.app')

@section('title', 'Movimentações de Estoque')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>📊 Movimentações de Estoque</h1>
                <div>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNovaMovimentacao">
                        ➕ Nova Movimentação
                    </button>
                    <a href="{{ route('comerciante.produtos.estoque.alertas') }}" class="btn btn-outline-warning">
                        🚨 Ver Alertas
                    </a>
                    <a href="{{ route('comerciante.produtos.index') }}" class="btn btn-secondary">
                        ← Voltar aos Produtos
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">🔍 Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('comerciante.produtos.estoque.movimentacoes') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="produto_id">Produto:</label>
                                <select name="produto_id" id="produto_id" class="form-control">
                                    <option value="">Todos os produtos</option>
                                    @foreach($listaProdutos as $id => $nome)
                                        <option value="{{ $id }}" {{ request('produto_id') == $id ? 'selected' : '' }}>
                                            {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tipo">Tipo:</label>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                    <option value="saida" {{ request('tipo') == 'saida' ? 'selected' : '' }}>Saída</option>
                                    <option value="ajuste" {{ request('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="data_inicio">Data Início:</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="data_fim">Data Fim:</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ request('data_fim') }}">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                                    <a href="{{ route('comerciante.produtos.estoque.movimentacoes') }}" class="btn btn-outline-secondary">Limpar</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Movimentações -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📋 Histórico de Movimentações</h5>
                </div>
                <div class="card-body p-0">
                    @if($produtos->sum(function($produto) { return $produto->movimentacoes->count(); }) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Produto</th>
                                        <th>Tipo</th>
                                        <th>Quantidade</th>
                                        <th>Estoque Anterior</th>
                                        <th>Estoque Posterior</th>
                                        <th>Motivo</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($produtos as $produto)
                                        @foreach($produto->movimentacoes as $movimentacao)
                                        <tr>
                                            <td>
                                                <small>
                                                    {{ $movimentacao->created_at->format('d/m/Y') }}<br>
                                                    {{ $movimentacao->created_at->format('H:i:s') }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong>{{ $produto->nome }}</strong><br>
                                                <small class="text-muted">{{ $produto->sku }}</small>
                                            </td>
                                            <td>
                                                @if($movimentacao->tipo === 'entrada')
                                                    <span class="badge badge-success">⬆️ Entrada</span>
                                                @elseif($movimentacao->tipo === 'saida')
                                                    <span class="badge badge-danger">⬇️ Saída</span>
                                                @else
                                                    <span class="badge badge-warning">🔄 Ajuste</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($movimentacao->tipo === 'entrada')
                                                    <span class="text-success">+{{ $movimentacao->quantidade }}</span>
                                                @elseif($movimentacao->tipo === 'saida')
                                                    <span class="text-danger">-{{ $movimentacao->quantidade }}</span>
                                                @else
                                                    <span class="text-warning">{{ $movimentacao->quantidade }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $movimentacao->estoque_anterior }}</td>
                                            <td>{{ $movimentacao->estoque_posterior }}</td>
                                            <td>{{ $movimentacao->motivo }}</td>
                                            <td>
                                                @if($movimentacao->observacoes)
                                                    <small>{{ $movimentacao->observacoes }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-box-open fa-3x text-muted"></i>
                            </div>
                            <h5>Nenhuma movimentação encontrada</h5>
                            <p class="text-muted">Não há movimentações de estoque para os filtros selecionados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Movimentação -->
<div class="modal fade" id="modalNovaMovimentacao" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('comerciante.produtos.estoque.movimentacao.registrar') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nova Movimentação</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="modal_produto_id">Produto *</label>
                                <select name="produto_id" id="modal_produto_id" class="form-control" required>
                                    <option value="">Selecione um produto</option>
                                    @foreach($listaProdutos as $id => $nome)
                                        <option value="{{ $id }}">{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_tipo">Tipo *</label>
                                <select name="tipo" id="modal_tipo" class="form-control" required>
                                    <option value="">Selecione</option>
                                    <option value="entrada">⬆️ Entrada</option>
                                    <option value="saida">⬇️ Saída</option>
                                    <option value="ajuste">🔄 Ajuste</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_quantidade">Quantidade *</label>
                                <input type="number" name="quantidade" id="modal_quantidade" 
                                       class="form-control" step="0.001" min="0.001" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="modal_motivo">Motivo *</label>
                                <input type="text" name="motivo" id="modal_motivo" 
                                       class="form-control" maxlength="255" required
                                       placeholder="Ex: Compra de fornecedor, Ajuste de inventário...">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="modal_observacoes">Observações</label>
                                <textarea name="observacoes" id="modal_observacoes" 
                                          class="form-control" rows="3" 
                                          placeholder="Informações adicionais (opcional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">💾 Registrar Movimentação</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select2 nos selects de produto
    $('#produto_id, #modal_produto_id').select2({
        placeholder: 'Selecione um produto',
        allowClear: true
    });
});
</script>
@endsection

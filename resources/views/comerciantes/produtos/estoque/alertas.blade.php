@extends('comerciantes.layouts.app')

@section('title', 'Alertas de Estoque')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>🚨 Alertas de Estoque</h1>
                <div>
                    <a href="{{ route('comerciantes.produtos.estoque.movimentacoes') }}" class="btn btn-outline-primary">
                        📊 Ver Movimentações
                    </a>
                    <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-secondary">
                        ← Voltar aos Produtos
                    </a>
                </div>
            </div>

            <!-- Resumo dos Alertas -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-danger">
                        <div class="card-body text-center">
                            <h3 class="text-danger">{{ $produtosSemEstoque->count() }}</h3>
                            <p class="mb-0">Produtos Sem Estoque</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $produtosEstoqueBaixo->count() }}</h3>
                            <p class="mb-0">Estoque Baixo</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $produtosEstoqueAlto->count() }}</h3>
                            <p class="mb-0">Estoque Alto</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produtos Sem Estoque -->
            @if($produtosSemEstoque->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">🔴 Produtos Sem Estoque ({{ $produtosSemEstoque->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Marca</th>
                                    <th>Estoque Atual</th>
                                    <th>Estoque Mínimo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produtosSemEstoque as $produto)
                                <tr>
                                    <td>
                                        <strong>{{ $produto->nome }}</strong><br>
                                        <small class="text-muted">{{ $produto->sku }}</small>
                                    </td>
                                    <td>{{ $produto->categoria->nome ?? '-' }}</td>
                                    <td>{{ $produto->marca->nome ?? '-' }}</td>
                                    <td><span class="badge badge-danger">{{ $produto->estoque_atual }}</span></td>
                                    <td>{{ $produto->estoque_minimo }}</td>
                                    <td>
                                        <a href="{{ route('comerciantes.produtos.edit', $produto->id) }}" 
                                           class="btn btn-sm btn-primary">Editar</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Produtos com Estoque Baixo -->
            @if($produtosEstoqueBaixo->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🟡 Produtos com Estoque Baixo ({{ $produtosEstoqueBaixo->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Marca</th>
                                    <th>Estoque Atual</th>
                                    <th>Estoque Mínimo</th>
                                    <th>Diferença</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produtosEstoqueBaixo as $produto)
                                <tr>
                                    <td>
                                        <strong>{{ $produto->nome }}</strong><br>
                                        <small class="text-muted">{{ $produto->sku }}</small>
                                    </td>
                                    <td>{{ $produto->categoria->nome ?? '-' }}</td>
                                    <td>{{ $produto->marca->nome ?? '-' }}</td>
                                    <td><span class="badge badge-warning">{{ $produto->estoque_atual }}</span></td>
                                    <td>{{ $produto->estoque_minimo }}</td>
                                    <td class="text-danger">{{ $produto->estoque_minimo - $produto->estoque_atual }}</td>
                                    <td>
                                        <a href="{{ route('comerciantes.produtos.edit', $produto->id) }}" 
                                           class="btn btn-sm btn-primary">Editar</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Produtos com Estoque Alto -->
            @if($produtosEstoqueAlto->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">🔵 Produtos com Estoque Alto ({{ $produtosEstoqueAlto->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Marca</th>
                                    <th>Estoque Atual</th>
                                    <th>Estoque Máximo</th>
                                    <th>Excesso</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produtosEstoqueAlto as $produto)
                                <tr>
                                    <td>
                                        <strong>{{ $produto->nome }}</strong><br>
                                        <small class="text-muted">{{ $produto->sku }}</small>
                                    </td>
                                    <td>{{ $produto->categoria->nome ?? '-' }}</td>
                                    <td>{{ $produto->marca->nome ?? '-' }}</td>
                                    <td><span class="badge badge-info">{{ $produto->estoque_atual }}</span></td>
                                    <td>{{ $produto->estoque_maximo }}</td>
                                    <td class="text-primary">+{{ $produto->estoque_atual - $produto->estoque_maximo }}</td>
                                    <td>
                                        <a href="{{ route('comerciantes.produtos.edit', $produto->id) }}" 
                                           class="btn btn-sm btn-primary">Editar</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($produtosSemEstoque->count() == 0 && $produtosEstoqueBaixo->count() == 0 && $produtosEstoqueAlto->count() == 0)
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h4>✅ Tudo Certo!</h4>
                    <p class="text-muted">Não há alertas de estoque no momento.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh a cada 5 minutos
    setTimeout(function(){
        location.reload();
    }, 300000);
});
</script>
@endsection

@extends('layouts.comerciante')

@section('title', 'Detalhes do Produto')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">{{ $produto->nome }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.index') }}">Produtos</a></li>
                            <li class="breadcrumb-item active">{{ $produto->nome }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('comerciantes.produtos.edit', $produto->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                    <a href="{{ route('comerciantes.produtos.imagens.index', $produto->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-images me-2"></i>Galeria
                    </a>
                    <a href="{{ route('comerciantes.produtos.relacionados.index', $produto->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-link me-2"></i>Produtos Relacionados
                    </a>
                    <a href="{{ route('comerciantes.produtos.precos-quantidade.create', ['produto_id' => $produto->id]) }}" class="btn btn-outline-success">
                        <i class="fas fa-percentage me-2"></i>Preços por Quantidade
                    </a>
                    @if($produto->controla_estoque)
                    <a href="{{ route('comerciantes.produtos.estoque.alertas') }}" class="btn btn-outline-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Estoque
                    </a>
                    @endif
                    <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- Status e Informações Rápidas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($produto->ativo)
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Ativo
                                </span>
                                @else
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    <i class="fas fa-pause-circle me-1"></i>Inativo
                                </span>
                                @endif
                            </div>
                            <small class="text-muted">Status</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-shopping-tag text-success fs-4"></i>
                            </div>
                            <h6 class="text-success fw-bold mb-0">R$ {{ number_format($produto->preco_compra ?? 0, 2, ',', '.') }}</h6>
                            <small class="text-muted">Compra</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-tag text-primary fs-4"></i>
                            </div>
                            <h4 class="text-primary fw-bold mb-0">R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}</h4>
                            <small class="text-muted">Preço de Venda</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if($produto->preco_promocional)
                                <i class="fas fa-percentage text-warning fs-4"></i>
                                @else
                                <i class="fas fa-percentage text-muted fs-4"></i>
                                @endif
                            </div>
                            @if($produto->preco_promocional)
                            <h6 class="text-warning fw-bold mb-0">R$ {{ number_format($produto->preco_promocional, 2, ',', '.') }}</h6>
                            <small class="text-muted">Promocional</small>
                            @else
                            <h6 class="text-muted mb-0">-</h6>
                            <small class="text-muted">Sem Promoção</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-cubes text-info fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">{{ $produto->quantidade_estoque ?? 0 }}</h5>
                            <small class="text-muted">Em Estoque</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if($produto->destaque)
                                <i class="fas fa-star text-warning fs-4"></i>
                                @else
                                <i class="far fa-star text-muted fs-4"></i>
                                @endif
                            </div>
                            <h6 class="fw-bold mb-0">{{ $produto->destaque ? 'Sim' : 'Não' }}</h6>
                            <small class="text-muted">Destaque</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="row">
                <!-- Imagens do Produto -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-images text-primary me-2"></i>Imagens do Produto
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($produto->imagem_principal)
                            <div class="text-center mb-3">
                                <img src="{{ $produto->imagem_principal }}"
                                    alt="{{ $produto->nome }}"
                                    class="img-fluid rounded shadow-sm"
                                    style="max-height: 300px;">
                                <p class="text-muted mt-2 mb-0"><small>Imagem Principal</small></p>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-image text-muted fs-1 mb-3"></i>
                                <p class="text-muted mb-0">Nenhuma imagem cadastrada</p>
                            </div>
                            @endif

                            @if($produto->imagens && $produto->imagens->count() > 0)
                            <div class="row g-2 mt-3">
                                @foreach($produto->imagens as $imagem)
                                <div class="col-4">
                                    <img src="{{ $imagem->url }}"
                                        alt="Imagem {{ $loop->iteration }}"
                                        class="img-fluid rounded shadow-sm"
                                        style="height: 80px; object-fit: cover; width: 100%;">
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informações Detalhadas -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>Informações Detalhadas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">SKU</label>
                                        <p class="mb-0">{{ $produto->sku ?? 'Não informado' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Categoria</label>
                                        <p class="mb-0">
                                            @if($produto->categoria)
                                            <span class="badge bg-info">{{ $produto->categoria->nome }}</span>
                                            @else
                                            <span class="text-muted">Sem categoria</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Marca</label>
                                        <p class="mb-0">
                                            @if($produto->marca)
                                            <span class="badge bg-secondary">{{ $produto->marca->nome }}</span>
                                            @else
                                            <span class="text-muted">Sem marca</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Tipo</label>
                                        <p class="mb-0">
                                            <span class="badge bg-primary">{{ ucfirst($produto->tipo) }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Preço de Compra</label>
                                        <p class="mb-0">
                                            @if($produto->preco_compra)
                                            <span class="text-success fw-bold">R$ {{ number_format($produto->preco_compra, 2, ',', '.') }}</span>
                                            @else
                                            <span class="text-muted">Não informado</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Preço Promocional</label>
                                        <p class="mb-0">
                                            @if($produto->preco_promocional)
                                            <span class="text-warning fw-bold">R$ {{ number_format($produto->preco_promocional, 2, ',', '.') }}</span>
                                            <small class="text-muted d-block">Economia: R$ {{ number_format($produto->preco_venda - $produto->preco_promocional, 2, ',', '.') }}</small>
                                            @else
                                            <span class="text-muted">Sem promoção</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Controla Estoque</label>
                                        <p class="mb-0">
                                            @if($produto->controla_estoque)
                                            <span class="badge bg-success">Sim</span>
                                            @else
                                            <span class="badge bg-warning">Não</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Estoque Mínimo</label>
                                        <p class="mb-0">{{ $produto->estoque_minimo ?? 'Não definido' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-semibold text-muted small">Data de Cadastro</label>
                                        <p class="mb-0">{{ $produto->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($produto->descricao)
                            <div class="mt-4">
                                <label class="fw-semibold text-muted small">Descrição</label>
                                <div class="bg-light rounded p-3">
                                    <p class="mb-0">{{ $produto->descricao }}</p>
                                </div>
                            </div>
                            @endif

                            @if($produto->observacoes)
                            <div class="mt-3">
                                <label class="fw-semibold text-muted small">Observações</label>
                                <div class="bg-light rounded p-3">
                                    <p class="mb-0">{{ $produto->observacoes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preços por Quantidade -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-percentage text-primary me-2"></i>Preços por Quantidade
                            </h5>
                            <a href="{{ route('comerciantes.produtos.precos-quantidade.create', ['produto_id' => $produto->id]) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-1"></i>Adicionar Preço
                            </a>
                        </div>
                        <div class="card-body">
                            @php
                            $precosQuantidade = \App\Models\ProdutoPrecoQuantidade::where('produto_id', $produto->id)
                            ->where('empresa_id', session('empresa_id', 1))
                            ->where('ativo', true)
                            ->with('variacao')
                            ->orderBy('quantidade_minima')
                            ->get();
                            @endphp

                            @if($precosQuantidade && $precosQuantidade->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Variação</th>
                                            <th>Quantidade Mínima</th>
                                            <th>Quantidade Máxima</th>
                                            <th>Preço</th>
                                            <th>Desconto</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($precosQuantidade as $preco)
                                        <tr>
                                            <td>
                                                @if($preco->variacao)
                                                <span class="badge bg-info">{{ $preco->variacao->nome }}</span>
                                                @else
                                                <span class="text-muted">Produto base</span>
                                                @endif
                                            </td>
                                            <td>{{ $preco->quantidade_minima }}</td>
                                            <td>{{ $preco->quantidade_maxima ?? '∞' }}</td>
                                            <td>
                                                <span class="fw-bold text-success">R$ {{ number_format($preco->preco, 2, ',', '.') }}</span>
                                            </td>
                                            <td>
                                                @if($preco->desconto_percentual > 0)
                                                <span class="badge bg-warning">{{ $preco->desconto_percentual }}%</span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('comerciantes.produtos.precos-quantidade.edit', $preco->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('comerciantes.produtos.precos-quantidade.index', ['produto_id' => $produto->id]) }}"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>Ver Todos os Preços
                                </a>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-percentage text-muted fs-1 mb-3"></i>
                                <h6 class="text-muted mb-3">Nenhum preço por quantidade cadastrado</h6>
                                <p class="text-muted mb-3">Configure preços especiais baseados na quantidade de compra</p>
                                <a href="{{ route('comerciantes.produtos.precos-quantidade.create', ['produto_id' => $produto->id]) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Cadastrar Primeiro Preço
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Movimentações -->
            @if($produto->controla_estoque && $produto->movimentacoes && $produto->movimentacoes->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history text-primary me-2"></i>Últimas Movimentações de Estoque
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Tipo</th>
                                            <th>Quantidade</th>
                                            <th>Observação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($produto->movimentacoes->take(10) as $movimentacao)
                                        <tr>
                                            <td>{{ $movimentacao->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($movimentacao->tipo == 'entrada')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-plus me-1"></i>Entrada
                                                </span>
                                                @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-minus me-1"></i>Saída
                                                </span>
                                                @endif
                                            </td>
                                            <td>{{ $movimentacao->quantidade }}</td>
                                            <td>{{ $movimentacao->observacao ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .badge {
        font-size: 0.875rem;
    }
</style>
@endpush
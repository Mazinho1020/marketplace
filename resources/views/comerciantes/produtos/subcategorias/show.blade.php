@extends('layouts.comerciante')

@section('title', 'Subcategoria: ' . $subcategoria->nome)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sitemap"></i> {{ $subcategoria->nome }}
        </h1>
        <div>
            <a href="{{ route('comerciantes.produtos.subcategorias.edit', $subcategoria) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('comerciantes.produtos.subcategorias.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações Básicas -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informações da Subcategoria
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-gray-600">Nome</h6>
                            <p class="mb-3">{{ $subcategoria->nome }}</p>

                            <h6 class="text-gray-600">Categoria Principal</h6>
                            <p class="mb-3">
                                <span class="badge badge-primary">{{ $subcategoria->categoria->nome }}</span>
                            </p>

                            @if($subcategoria->parent)
                            <h6 class="text-gray-600">Subcategoria Pai</h6>
                            <p class="mb-3">
                                <span class="badge badge-info">{{ $subcategoria->parent->nome }}</span>
                            </p>
                            @endif

                            <h6 class="text-gray-600">Slug</h6>
                            <p class="mb-3">
                                <code>{{ $subcategoria->slug }}</code>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-gray-600">Status</h6>
                            <p class="mb-3">
                                @if($subcategoria->ativo)
                                    <span class="badge badge-success">Ativo</span>
                                @else
                                    <span class="badge badge-danger">Inativo</span>
                                @endif
                            </p>

                            <h6 class="text-gray-600">Ordem</h6>
                            <p class="mb-3">{{ $subcategoria->ordem ?? 'Não definida' }}</p>

                            @if($subcategoria->icone)
                            <h6 class="text-gray-600">Ícone</h6>
                            <p class="mb-3">
                                <i class="{{ $subcategoria->icone }}"></i> {{ $subcategoria->icone }}
                            </p>
                            @endif

                            @if($subcategoria->cor_fundo)
                            <h6 class="text-gray-600">Cor de Fundo</h6>
                            <p class="mb-3">
                                <span class="badge" style="background-color: {{ $subcategoria->cor_fundo }}; color: white;">
                                    {{ $subcategoria->cor_fundo }}
                                </span>
                            </p>
                            @endif
                        </div>
                    </div>

                    @if($subcategoria->descricao)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-gray-600">Descrição</h6>
                            <p class="mb-0">{{ $subcategoria->descricao }}</p>
                        </div>
                    </div>
                    @endif

                    @if($subcategoria->imagem_url)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-gray-600">Imagem</h6>
                            <img src="{{ $subcategoria->imagem_url }}" alt="{{ $subcategoria->nome }}" 
                                 class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- SEO -->
            @if($subcategoria->meta_title || $subcategoria->meta_description || $subcategoria->meta_keywords)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-search"></i> Informações SEO
                    </h6>
                </div>
                <div class="card-body">
                    @if($subcategoria->meta_title)
                    <h6 class="text-gray-600">Meta Title</h6>
                    <p class="mb-3">{{ $subcategoria->meta_title }}</p>
                    @endif

                    @if($subcategoria->meta_description)
                    <h6 class="text-gray-600">Meta Description</h6>
                    <p class="mb-3">{{ $subcategoria->meta_description }}</p>
                    @endif

                    @if($subcategoria->meta_keywords)
                    <h6 class="text-gray-600">Meta Keywords</h6>
                    <p class="mb-0">{{ $subcategoria->meta_keywords }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Subcategorias Filhas -->
            @if($subcategoria->children->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-layer-group"></i> Subcategorias Filhas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Status</th>
                                    <th>Ordem</th>
                                    <th>Produtos</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subcategoria->children as $filha)
                                <tr>
                                    <td>{{ $filha->nome }}</td>
                                    <td>
                                        @if($filha->ativo)
                                            <span class="badge badge-success">Ativo</span>
                                        @else
                                            <span class="badge badge-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>{{ $filha->ordem ?? '-' }}</td>
                                    <td>{{ $filha->produtos()->count() }}</td>
                                    <td>
                                        <a href="{{ route('comerciantes.produtos.subcategorias.show', $filha) }}" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('comerciantes.produtos.subcategorias.edit', $filha) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Produtos -->
            @if($subcategoria->produtos->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-box"></i> Produtos nesta Subcategoria
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>SKU</th>
                                    <th>Preço</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subcategoria->produtos->take(10) as $produto)
                                <tr>
                                    <td>{{ $produto->nome }}</td>
                                    <td>{{ $produto->sku ?? '-' }}</td>
                                    <td>R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}</td>
                                    <td>
                                        @if($produto->ativo)
                                            <span class="badge badge-success">{{ ucfirst($produto->status) }}</span>
                                        @else
                                            <span class="badge badge-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('comerciantes.produtos.show', $produto) }}" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('comerciantes.produtos.edit', $produto) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($subcategoria->produtos->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('comerciantes.produtos.index', ['subcategoria_id' => $subcategoria->id]) }}" 
                           class="btn btn-outline-primary">
                            Ver todos os {{ $subcategoria->produtos->count() }} produtos
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar com Estatísticas -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie"></i> Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $estatisticas['total_produtos'] }}</h4>
                            <small class="text-gray-600">Total de Produtos</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $estatisticas['produtos_ativos'] }}</h4>
                            <small class="text-gray-600">Produtos Ativos</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $estatisticas['total_filhas'] }}</h4>
                            <small class="text-gray-600">Subcategorias Filhas</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $estatisticas['filhas_ativas'] }}</h4>
                            <small class="text-gray-600">Filhas Ativas</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hierarquia -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sitemap"></i> Hierarquia
                    </h6>
                </div>
                <div class="card-body">
                    <div class="hierarchy">
                        <div class="level-0">
                            <i class="fas fa-tag text-primary"></i> {{ $subcategoria->categoria->nome }}
                        </div>
                        @if($subcategoria->parent)
                        <div class="level-1 mt-2">
                            <i class="fas fa-arrow-right text-muted"></i> 
                            <i class="fas fa-sitemap text-info"></i> {{ $subcategoria->parent->nome }}
                        </div>
                        @endif
                        <div class="level-2 mt-2">
                            <i class="fas fa-arrow-right text-muted"></i> 
                            <i class="fas fa-sitemap text-success"></i> <strong>{{ $subcategoria->nome }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog"></i> Informações do Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <small class="text-gray-600 d-block">ID: {{ $subcategoria->id }}</small>
                    <small class="text-gray-600 d-block">Criado em: {{ $subcategoria->created_at->format('d/m/Y H:i') }}</small>
                    <small class="text-gray-600 d-block">Atualizado em: {{ $subcategoria->updated_at->format('d/m/Y H:i') }}</small>
                    @if($subcategoria->sync_status)
                    <small class="text-gray-600 d-block">Status Sync: {{ ucfirst($subcategoria->sync_status) }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hierarchy .level-0,
.hierarchy .level-1,
.hierarchy .level-2 {
    padding: 5px 0;
}

.hierarchy .level-1 {
    margin-left: 15px;
}

.hierarchy .level-2 {
    margin-left: 30px;
}
</style>
@endsection

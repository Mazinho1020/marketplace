@extends('layouts.comerciante')

@section('title', 'Produtos')

@section('content')
<div class="container-fluid py-4">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-box text-primary me-2"></i>
                Produtos
            </h1>
            <p class="text-muted mb-0">Gerencie o catálogo de produtos da sua empresa</p>
        </div>
        <div>
            <div class="btn-group">
                <a href="{{ route('comerciantes.produtos.kits.index') }}" class="btn btn-outline-success">
                    <i class="fas fa-boxes me-1"></i>
                    Kits/Combos
                </a>
                <a href="{{ route('comerciantes.produtos.precos-quantidade.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-percentage me-1"></i>
                    Preços por Quantidade
                </a>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i>
                        Configurações
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <h6 class="dropdown-header">Gestão de Relacionamentos</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="mostrarProdutosComRelacionamentos()">
                                <i class="fas fa-link me-2"></i>Ver Produtos com Relacionamentos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="mostrarEstatisticasRelacionamentos()">
                                <i class="fas fa-chart-bar me-2"></i>Estatísticas de Cross-sell
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <h6 class="dropdown-header">Outros</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('comerciantes.produtos.categorias.index') }}">
                                <i class="fas fa-tags me-2"></i>Gerenciar Categorias
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('comerciantes.produtos.marcas.index') }}">
                                <i class="fas fa-star me-2"></i>Gerenciar Marcas
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('comerciantes.produtos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Novo Produto
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.produtos.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-control"
                        placeholder="Nome, SKU ou código de barras..."
                        value="{{ request('busca') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas as categorias</option>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}"
                            {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponivel" {{ request('status') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="indisponivel" {{ request('status') == 'indisponivel' ? 'selected' : '' }}>Indisponível</option>
                        <option value="pausado" {{ request('status') == 'pausado' ? 'selected' : '' }}>Pausado</option>
                        <option value="esgotado" {{ request('status') == 'esgotado' ? 'selected' : '' }}>Esgotado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Filtros</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="estoque_baixo"
                            id="estoque_baixo" {{ request('estoque_baixo') ? 'checked' : '' }}>
                        <label class="form-check-label" for="estoque_baixo">
                            Estoque baixo
                        </label>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Links rápidos -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('comerciantes.produtos.categorias.index') }}" class="card text-decoration-none border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-2x text-primary mb-2"></i>
                    <h6 class="card-title mb-0">Categorias</h6>
                    <small class="text-muted">Gerenciar categorias</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('comerciantes.produtos.marcas.index') }}" class="card text-decoration-none border-success">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x text-success mb-2"></i>
                    <h6 class="card-title mb-0">Marcas</h6>
                    <small class="text-muted">Gerenciar marcas</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-link fa-2x text-info mb-2"></i>
                    <h6 class="card-title mb-0">Relacionamentos</h6>
                    <small class="text-muted">Cross-sell e Up-sell</small>
                    <div class="mt-2">
                        <small class="text-muted d-block">Configure produtos relacionados</small>
                        <small class="text-muted">para aumentar suas vendas</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h6 class="card-title mb-0">Estoque Baixo</h6>
                    <small class="text-muted">
                        {{ $produtos->where('estoque_baixo', true)->count() }} produtos
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                    <h6 class="card-title mb-0">Total</h6>
                    <small class="text-muted">{{ $produtos->total() }} produtos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Produtos
            </h5>
        </div>
        <div class="card-body p-0">
            @if($produtos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>SKU</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Relacionamentos</th>
                            <th>Status</th>
                            <th width="140">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produtos as $produto)
                        <tr>
                            <td>
                                <img src="{{ $produto->url_imagem_principal }}"
                                    alt="{{ $produto->nome }}"
                                    class="img-thumbnail produto-img"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $produto->nome }}</strong>
                                    @if($produto->destaque)
                                    <span class="badge bg-warning ms-1">Destaque</span>
                                    @endif
                                    @if($produto->estoque_baixo)
                                    <span class="badge bg-danger ms-1">Estoque Baixo</span>
                                    @endif
                                </div>
                                @if($produto->descricao_curta)
                                <small class="text-muted">{{ \Str::limit($produto->descricao_curta, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($produto->categoria)
                                <span class="badge bg-secondary">{{ $produto->categoria->nome }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <code>{{ $produto->sku ?: '-' }}</code>
                            </td>
                            <td>
                                <strong class="text-success">{{ $produto->preco_venda_formatado }}</strong>
                            </td>
                            <td>
                                @if($produto->controla_estoque)
                                <span class="badge {{ $produto->estoque_baixo ? 'bg-danger' : 'bg-success' }}">
                                    {{ number_format($produto->estoque_atual, 0) }}
                                </span>
                                @else
                                <span class="text-muted">Não controlado</span>
                                @endif
                            </td>
                            <td>
                                @php
                                $totalRelacionamentos = $produto->relacionados()->count();
                                @endphp
                                @if($totalRelacionamentos > 0)
                                <a href="{{ route('comerciantes.produtos.relacionados.index', $produto->id) }}"
                                    class="badge bg-info text-decoration-none"
                                    title="Ver produtos relacionados">
                                    <i class="fas fa-link me-1"></i>{{ $totalRelacionamentos }}
                                </a>
                                @else
                                <a href="{{ route('comerciantes.produtos.relacionados.index', $produto->id) }}"
                                    class="badge bg-light text-muted text-decoration-none"
                                    title="Configurar produtos relacionados">
                                    <i class="fas fa-plus me-1"></i>Adicionar
                                </a>
                                @endif
                            </td>
                            <td>
                                {!! $produto->status_badge !!}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('comerciantes.produtos.show', $produto) }}"
                                        class="btn btn-outline-primary" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('comerciantes.produtos.edit', $produto) }}"
                                        class="btn btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info dropdown-toggle"
                                            data-bs-toggle="dropdown" title="Mais opções">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('comerciantes.produtos.relacionados.index', $produto->id) }}">
                                                    <i class="fas fa-link me-2"></i>Produtos Relacionados
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('comerciantes.produtos.imagens.index', $produto->id) }}">
                                                    <i class="fas fa-images me-2"></i>Galeria de Imagens
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('comerciantes.produtos.precos-quantidade.create', ['produto_id' => $produto->id]) }}">
                                                    <i class="fas fa-percentage me-2"></i>Preços por Quantidade
                                                </a>
                                            </li>
                                            @if($produto->controla_estoque)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('comerciantes.produtos.estoque.movimentacoes') }}?produto_id={{ $produto->id }}">
                                                    <i class="fas fa-boxes me-2"></i>Movimentações de Estoque
                                                </a>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger"
                                        title="Excluir" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $produto->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Modal de Exclusão -->
                                <div class="modal fade" id="deleteModal{{ $produto->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Excluir Produto</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Tem certeza que deseja excluir o produto <strong>{{ $produto->nome }}</strong>?</p>
                                                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('comerciantes.produtos.destroy', $produto) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="card-footer">
                {{ $produtos->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum produto encontrado</h5>
                <p class="text-muted">Que tal começar criando seu primeiro produto?</p>
                <a href="{{ route('comerciantes.produtos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Criar Primeiro Produto
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit do formulário de filtros quando mudar categoria ou status
    document.querySelectorAll('select[name="categoria_id"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Função para mostrar produtos com relacionamentos
    function mostrarProdutosComRelacionamentos() {
        // Filtrar tabela para mostrar apenas produtos com relacionamentos
        const linhas = document.querySelectorAll('tbody tr');
        let encontrados = 0;

        linhas.forEach(linha => {
            const celRelacionamentos = linha.querySelector('td:nth-child(7)'); // Coluna de relacionamentos
            const badge = celRelacionamentos.querySelector('.bg-info');

            if (badge && !badge.textContent.includes('Adicionar')) {
                linha.style.display = '';
                linha.style.backgroundColor = '#f8f9fa';
                encontrados++;
            } else {
                linha.style.display = 'none';
            }
        });

        // Mostrar resultado
        if (encontrados === 0) {
            alert('Nenhum produto com relacionamentos configurados foi encontrado.');
            mostrarTodosOsProdutos();
        } else {
            // Adicionar botão para voltar ao normal
            const header = document.querySelector('.card-header h5');
            if (!header.querySelector('.btn-limpar-filtro')) {
                const btnLimpar = document.createElement('button');
                btnLimpar.className = 'btn btn-sm btn-outline-secondary ms-2 btn-limpar-filtro';
                btnLimpar.innerHTML = '<i class="fas fa-times"></i> Mostrar Todos';
                btnLimpar.onclick = mostrarTodosOsProdutos;
                header.appendChild(btnLimpar);
            }

            toastr.success(`${encontrados} produtos com relacionamentos encontrados`);
        }
    }

    // Função para mostrar todos os produtos
    function mostrarTodosOsProdutos() {
        const linhas = document.querySelectorAll('tbody tr');
        linhas.forEach(linha => {
            linha.style.display = '';
            linha.style.backgroundColor = '';
        });

        // Remover botão de limpar filtro
        const btnLimpar = document.querySelector('.btn-limpar-filtro');
        if (btnLimpar) {
            btnLimpar.remove();
        }
    }

    // Função para mostrar estatísticas de relacionamentos
    function mostrarEstatisticasRelacionamentos() {
        const linhas = document.querySelectorAll('tbody tr');
        let stats = {
            total: linhas.length,
            comRelacionamentos: 0,
            semRelacionamentos: 0,
            relacionamentos: {}
        };

        linhas.forEach(linha => {
            const celRelacionamentos = linha.querySelector('td:nth-child(7)'); // Coluna de relacionamentos
            const badge = celRelacionamentos.querySelector('.bg-info');

            if (badge && !badge.textContent.includes('Adicionar')) {
                stats.comRelacionamentos++;
                const count = parseInt(badge.textContent.match(/\d+/)[0]);
                if (!stats.relacionamentos[count]) {
                    stats.relacionamentos[count] = 0;
                }
                stats.relacionamentos[count]++;
            } else {
                stats.semRelacionamentos++;
            }
        });

        const percentualComRelacionamentos = ((stats.comRelacionamentos / stats.total) * 100).toFixed(1);

        let detalhes = Object.keys(stats.relacionamentos).map(count => {
            return `${stats.relacionamentos[count]} produtos com ${count} relacionamento(s)`;
        }).join('\n');

        const mensagem = `📊 ESTATÍSTICAS DE PRODUTOS RELACIONADOS
        
Total de produtos: ${stats.total}
Com relacionamentos: ${stats.comRelacionamentos} (${percentualComRelacionamentos}%)
Sem relacionamentos: ${stats.semRelacionamentos} (${(100 - percentualComRelacionamentos).toFixed(1)}%)

DETALHES:
${detalhes || 'Nenhum produto com relacionamentos'}

💡 DICA: Configure produtos relacionados para aumentar suas vendas através de cross-sell e up-sell!`;

        alert(mensagem);
    }

    // Tratar erro de carregamento de imagens
    document.addEventListener('DOMContentLoaded', function() {
        const placeholderUrl = '{{ asset("images/placeholder.svg") }}';

        document.querySelectorAll('.produto-img').forEach(function(img) {
            img.addEventListener('error', function() {
                if (this.src !== placeholderUrl) {
                    this.src = placeholderUrl;
                }
            });
        });
    });
</script>
@endpush
@endsection
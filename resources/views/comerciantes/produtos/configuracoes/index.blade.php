@extends('comerciantes.layout')

@section('title', 'Configurações de Produtos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comerciantes.produtos.index') }}">Produtos</a></li>
                    <li class="breadcrumb-item active">Configurações</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-cog me-2"></i>
                Configurações de Produtos
            </h1>
            <p class="text-muted mb-0">Gerencie tamanhos, sabores, ingredientes e outras personalizações</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('comerciantes.produtos.configuracoes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nova Configuração
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.produtos.configuracoes.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               class="form-control" 
                               placeholder="Nome da configuração ou produto..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_configuracao" class="form-label">Tipo</label>
                        <select name="tipo_configuracao" id="tipo_configuracao" class="form-select">
                            <option value="">Todos os tipos</option>
                            @foreach($tiposConfiguracao as $value => $label)
                                <option value="{{ $value }}" {{ request('tipo_configuracao') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-grid gap-2 d-md-flex w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                Filtrar
                            </button>
                            <a href="{{ route('comerciantes.produtos.configuracoes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Configurações -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Configurações Cadastradas
                <span class="badge bg-primary ms-2">{{ $configuracoes->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($configuracoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Configuração</th>
                                <th>Tipo</th>
                                <th>Itens</th>
                                <th>Status</th>
                                <th>Obrigatório</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($configuracoes as $configuracao)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box text-muted me-2"></i>
                                            <div>
                                                <div class="fw-medium">{{ $configuracao->produto->nome }}</div>
                                                <small class="text-muted">{{ $configuracao->produto->sku ?? 'Sem SKU' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $configuracao->nome }}</div>
                                            @if($configuracao->descricao)
                                                <small class="text-muted">{{ Str::limit($configuracao->descricao, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $configuracao->tipo_descricao }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $configuracao->quantidade_itens_ativos }} ativos</span>
                                        @if($configuracao->quantidade_itens != $configuracao->quantidade_itens_ativos)
                                            <span class="badge bg-secondary ms-1">{{ $configuracao->quantidade_itens - $configuracao->quantidade_itens_ativos }} inativos</span>
                                        @endif
                                    </td>
                                    <td>
                                        {!! $configuracao->status_badge !!}
                                    </td>
                                    <td>
                                        {!! $configuracao->obrigatorio_badge !!}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('comerciantes.produtos.configuracoes.show', $configuracao) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comerciantes.produtos.configuracoes.edit', $configuracao) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-{{ $configuracao->ativo ? 'secondary' : 'success' }}" 
                                                    onclick="toggleAtivo({{ $configuracao->id }})"
                                                    title="{{ $configuracao->ativo ? 'Desativar' : 'Ativar' }}">
                                                <i class="fas fa-{{ $configuracao->ativo ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="excluir({{ $configuracao->id }}, '{{ $configuracao->nome }}')"
                                                    title="Excluir">
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
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Mostrando {{ $configuracoes->firstItem() ?? 0 }} até {{ $configuracoes->lastItem() ?? 0 }} 
                        de {{ $configuracoes->total() }} configurações
                    </div>
                    {{ $configuracoes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma configuração encontrada</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'tipo_configuracao', 'produto_id']))
                            Tente ajustar os filtros de busca.
                        @else
                            Comece criando uma configuração para seus produtos.
                        @endif
                    </p>
                    <a href="{{ route('comerciantes.produtos.configuracoes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Criar Primeira Configuração
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a configuração <strong id="nomeConfiguracao"></strong>?</p>
                <p class="text-danger small mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function excluir(id, nome) {
        document.getElementById('nomeConfiguracao').textContent = nome;
        document.getElementById('formExcluir').action = `/comerciantes/produtos/configuracoes/${id}`;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }

    function toggleAtivo(id) {
        fetch(`/comerciantes/produtos/configuracoes/${id}/toggle-ativo`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recarregar a página para atualizar o status
                window.location.reload();
            } else {
                alert('Erro ao alterar status: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar solicitação');
        });
    }
</script>
@endpush

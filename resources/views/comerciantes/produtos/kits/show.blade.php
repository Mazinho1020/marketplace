@extends('layouts.comerciante')

@section('title', 'Kit/Combo: ' . $kit->nome)

@section('styles')
<style>
    .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 1.5rem; }
    .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 0.375rem 0.375rem 0 0 !important; }
    .btn-kit { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; }
    .btn-kit:hover { background: linear-gradient(135deg, #218838 0%, #1e7e34 100%); color: white; }
    
    .kit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .kit-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(25%, -25%);
    }
    
    .kit-price {
        font-size: 2.5rem;
        font-weight: bold;
        color: #fff;
    }
    
    .economia-badge {
        background: rgba(40, 167, 69, 0.9);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875em;
        letter-spacing: 0.5px;
    }
    
    .status-disponivel {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }
    
    .status-indisponivel {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }
    
    .status-pausado {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }
    
    .item-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .item-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 0.375rem;
        border: 2px solid #e9ecef;
    }
    
    .item-badges {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .item-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
    }
    
    .badge-obrigatorio {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .badge-substituivel {
        background: #f3e5f5;
        color: #7b1fa2;
    }
    
    .preco-breakdown {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        border-left: 4px solid #28a745;
    }
    
    .preco-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .preco-item:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.1em;
        margin-top: 0.5rem;
        padding-top: 1rem;
        border-top: 2px solid #28a745;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .info-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        border-left: 3px solid #667eea;
    }
    
    .info-label {
        font-size: 0.875em;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        font-size: 1.1em;
        font-weight: 600;
        color: #374151;
        margin-top: 0.25rem;
    }
    
    .action-buttons {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    
    .floating-actions {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        z-index: 1000;
    }
    
    @media (max-width: 768px) {
        .floating-actions {
            position: static;
            flex-direction: row;
            margin-top: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Kit Header -->
    <div class="kit-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-3">
                    <h1 class="h2 mb-0 me-3">{{ $kit->nome }}</h1>
                    <span class="status-badge status-{{ $kit->status }}">
                        {{ ucfirst($kit->status) }}
                    </span>
                </div>
                
                @if($kit->sku)
                <p class="mb-2 opacity-75">
                    <i class="fas fa-barcode me-2"></i>
                    SKU: {{ $kit->sku }}
                </p>
                @endif
                
                @if($kit->descricao)
                <p class="mb-3 opacity-90">{{ $kit->descricao }}</p>
                @endif
                
                <div class="d-flex align-items-center gap-3">
                    @if($kit->categoria)
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-tag me-1"></i>{{ $kit->categoria->nome }}
                    </span>
                    @endif
                    
                    @if($kit->marca)
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-trademark me-1"></i>{{ $kit->marca->nome }}
                    </span>
                    @endif
                    
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-boxes me-1"></i>{{ $kit->kitsItens->count() }} {{ $kit->kitsItens->count() == 1 ? 'item' : 'itens' }}
                    </span>
                </div>
            </div>
            
            <div class="col-lg-4 text-lg-end">
                <!-- Imagem do Kit -->
                @if($kit->imagem_principal)
                <div class="mb-3">
                    <img src="{{ asset($kit->imagem_principal) }}" 
                         alt="{{ $kit->nome }}" 
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 200px; max-width: 100%; object-fit: cover;">
                </div>
                @endif
                
                <div class="kit-price mb-2">
                    R$ {{ number_format($kit->preco_venda, 2, ',', '.') }}
                </div>
                
                @if($economiaKit > 0)
                <div class="economia-badge">
                    <i class="fas fa-percentage"></i>
                    Economiza R$ {{ number_format($economiaKit, 2, ',', '.') }}
                    ({{ number_format($percentualDesconto, 1) }}%)
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <!-- Produtos do Kit -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        Produtos Inclusos no Kit
                    </h5>
                </div>
                <div class="card-body">
                    @if($kit->kitsItens->count() > 0)
                        @foreach($kit->kitsItens as $item)
                        <div class="item-card">
                            <div class="item-badges">
                                @if($item->obrigatorio)
                                <span class="item-badge badge-obrigatorio">Obrigatório</span>
                                @endif
                                @if($item->substituivel)
                                <span class="item-badge badge-substituivel">Substituível</span>
                                @endif
                            </div>
                            
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($item->produtoItem->imagem_url)
                                    <img src="{{ $item->produtoItem->imagem_url }}" alt="{{ $item->produtoItem->nome }}" class="item-image">
                                    @else
                                    <div class="item-image bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-5">
                                    <h6 class="mb-1">{{ $item->produtoItem->nome }}</h6>
                                    @if($item->produtoItem->sku)
                                    <small class="text-muted d-block">SKU: {{ $item->produtoItem->sku }}</small>
                                    @endif
                                    @if($item->produtoItem->descricao)
                                    <small class="text-muted">{{ Str::limit($item->produtoItem->descricao, 60) }}</small>
                                    @endif
                                </div>
                                
                                <div class="col-md-2 text-center">
                                    <div class="fw-bold fs-5">{{ $item->quantidade }}x</div>
                                    <small class="text-muted">Quantidade</small>
                                </div>
                                
                                <div class="col-md-3 text-end">
                                    @if($item->preco_item)
                                    <div class="fw-bold text-success fs-5">
                                        R$ {{ number_format($item->preco_calculado, 2, ',', '.') }}
                                    </div>
                                    <small class="text-muted">
                                        R$ {{ number_format($item->preco_item, 2, ',', '.') }} unit.
                                    </small>
                                    @if($item->desconto_percentual > 0)
                                    <div class="small text-danger">
                                        <i class="fas fa-arrow-down me-1"></i>
                                        -{{ $item->desconto_percentual }}%
                                    </div>
                                    @endif
                                    @else
                                    <div class="fw-bold text-success fs-5">
                                        R$ {{ number_format($item->produtoItem->preco_venda * $item->quantidade, 2, ',', '.') }}
                                    </div>
                                    <small class="text-muted">
                                        R$ {{ number_format($item->produtoItem->preco_venda, 2, ',', '.') }} unit.
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum produto configurado</h5>
                            <p class="text-muted">Este kit ainda não possui produtos configurados.</p>
                            <a href="{{ route('comerciantes.produtos.kits.edit', $kit) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Adicionar Produtos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Análise de Preços -->
            @if($kit->kitsItens->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Análise de Preços
                    </h5>
                </div>
                <div class="card-body">
                    <div class="preco-breakdown">
                        @foreach($kit->kitsItens as $item)
                        <div class="preco-item">
                            <span>
                                {{ $item->produtoItem->nome }} 
                                ({{ $item->quantidade }}x R$ {{ number_format($item->preco_item ?? $item->produtoItem->preco_venda, 2, ',', '.') }})
                                @if($item->desconto_percentual > 0)
                                <small class="text-danger">-{{ $item->desconto_percentual }}%</small>
                                @endif
                            </span>
                            <span>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</span>
                        </div>
                        @endforeach
                        
                        <div class="preco-item">
                            <span>Total se comprados separadamente:</span>
                            <span>R$ {{ number_format($precoTotalItens, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="preco-item">
                            <span>Preço do Kit:</span>
                            <span class="text-success">R$ {{ number_format($kit->preco_venda, 2, ',', '.') }}</span>
                        </div>
                        
                        @if($economiaKit > 0)
                        <div class="preco-item text-success">
                            <span>
                                <i class="fas fa-chart-line me-2"></i>
                                Economia total:
                            </span>
                            <span>
                                R$ {{ number_format($economiaKit, 2, ',', '.') }} 
                                ({{ number_format($percentualDesconto, 1) }}%)
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar Informações -->
        <div class="col-lg-4">
            <!-- Ações Rápidas -->
            <div class="card action-buttons">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('comerciantes.produtos.kits.edit', $kit) }}" class="btn btn-kit">
                            <i class="fas fa-edit me-1"></i> Editar Kit
                        </a>
                        
                        <button type="button" class="btn btn-outline-primary" onclick="duplicarKit()">
                            <i class="fas fa-copy me-1"></i> Duplicar Kit
                        </button>
                        
                        <button type="button" class="btn btn-outline-info" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Imprimir Detalhes
                        </button>
                        
                        <hr>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="confirmarExclusao()">
                            <i class="fas fa-trash me-1"></i> Excluir Kit
                        </button>
                        
                        <a href="{{ route('comerciantes.produtos.kits.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Voltar aos Kits
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Informações do Kit -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações do Kit
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge status-{{ $kit->status }}">
                                    {{ ucfirst($kit->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Preço de Venda</div>
                            <div class="info-value">R$ {{ number_format($kit->preco_venda, 2, ',', '.') }}</div>
                        </div>
                        
                        @if($kit->estoque_atual !== null)
                        <div class="info-item">
                            <div class="info-label">Estoque Atual</div>
                            <div class="info-value">{{ $kit->estoque_atual ?? 0 }}</div>
                        </div>
                        @endif
                        
                        @if($kit->estoque_minimo !== null)
                        <div class="info-item">
                            <div class="info-label">Estoque Mínimo</div>
                            <div class="info-value">{{ $kit->estoque_minimo ?? 0 }}</div>
                        </div>
                        @endif
                        
                        <div class="info-item">
                            <div class="info-label">Total de Itens</div>
                            <div class="info-value">{{ $kit->kitsItens->count() }}</div>
                        </div>
                        
                        @if($economiaKit > 0)
                        <div class="info-item">
                            <div class="info-label">Economia</div>
                            <div class="info-value text-success">
                                R$ {{ number_format($economiaKit, 2, ',', '.') }}
                            </div>
                        </div>
                        @endif
                        
                        <div class="info-item">
                            <div class="info-label">Criado em</div>
                            <div class="info-value">{{ $kit->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Atualizado em</div>
                            <div class="info-value">{{ $kit->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estatísticas -->
            @if($kit->kitsItens->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-primary mb-1">{{ $kit->kitsItens->where('obrigatorio', true)->count() }}</div>
                                <small class="text-muted">Itens Obrigatórios</small>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-info mb-1">{{ $kit->kitsItens->where('substituivel', true)->count() }}</div>
                                <small class="text-muted">Itens Substituíveis</small>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success mb-1">{{ $kit->kitsItens->where('desconto_percentual', '>', 0)->count() }}</div>
                                <small class="text-muted">Com Desconto</small>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-warning mb-1">R$ {{ number_format($kit->kitsItens->avg('preco_item') ?? 0, 2, ',', '.') }}</div>
                                <small class="text-muted">Preço Médio</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                </div>
                <p class="text-center">Tem certeza que deseja excluir o kit <strong>"{{ $kit->nome }}"</strong>?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita. O kit e todos os seus itens serão permanentemente removidos do sistema.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('comerciantes.produtos.kits.destroy', $kit) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Actions (Mobile) -->
<div class="floating-actions d-lg-none">
    <a href="{{ route('comerciantes.produtos.kits.edit', $kit) }}" class="btn btn-kit btn-lg rounded-circle">
        <i class="fas fa-edit"></i>
    </a>
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function duplicarKit() {
    if (confirm('Deseja criar uma cópia deste kit?')) {
        // Implementar lógica de duplicação
        window.location.href = `{{ route('comerciantes.produtos.kits.create') }}?duplicate={{ $kit->id }}`;
    }
}

// Impressão otimizada
window.addEventListener('beforeprint', function() {
    // Ocultar elementos desnecessários na impressão
    document.querySelectorAll('.floating-actions, .action-buttons').forEach(el => {
        el.style.display = 'none';
    });
});

window.addEventListener('afterprint', function() {
    // Restaurar elementos após impressão
    document.querySelectorAll('.floating-actions, .action-buttons').forEach(el => {
        el.style.display = '';
    });
});
</script>
@endpush

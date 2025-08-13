@extends('layouts.comerciante')

@section('title', 'Detalhes do Código de Barras')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Detalhes do Código de Barras</h1>
                    <p class="text-muted">Visualize e gerencie informações do código</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.produtos.codigos-barras.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                    <a href="{{ route('comerciantes.produtos.codigos-barras.edit', $codigoBarras) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações do Código -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-barcode me-2"></i>Informações do Código
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Código de Barras</label>
                                        <div class="form-control-plaintext h5 text-primary">
                                            {{ $codigoBarras->codigo }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo</label>
                                        <div class="form-control-plaintext">
                                            <span class="badge bg-info">{{ strtoupper($codigoBarras->tipo) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div class="form-control-plaintext">
                                            @if($codigoBarras->ativo)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Ativo
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>Inativo
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Principal</label>
                                        <div class="form-control-plaintext">
                                            @if($codigoBarras->principal)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-star me-1"></i>Principal
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-star-o me-1"></i>Secundário
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Data de Criação</label>
                                        <div class="form-control-plaintext">
                                            {{ $codigoBarras->created_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Última Atualização</label>
                                        <div class="form-control-plaintext">
                                            {{ $codigoBarras->updated_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($codigoBarras->sync_status)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status de Sincronização</label>
                                        <div class="form-control-plaintext">
                                            @switch($codigoBarras->sync_status)
                                                @case('pendente')
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Pendente
                                                    </span>
                                                    @break
                                                @case('sincronizado')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Sincronizado
                                                    </span>
                                                    @break
                                                @case('erro')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Erro
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $codigoBarras->sync_status }}</span>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Código de Barras Visual -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-qrcode me-2"></i>Representação Visual
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="p-4 bg-light rounded">
                                <!-- Simulação de código de barras usando CSS -->
                                <div class="barcode-simulation mb-3">
                                    <div class="d-flex justify-content-center align-items-end" style="height: 60px;">
                                        @for($i = 0; $i < strlen($codigoBarras->codigo); $i++)
                                            <div class="bg-dark me-1" style="width: 2px; height: {{ 30 + (ord($codigoBarras->codigo[$i]) % 30) }}px;"></div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="h6 mb-0 font-monospace">{{ $codigoBarras->codigo }}</div>
                                <small class="text-muted">{{ strtoupper($codigoBarras->tipo) }}</small>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações do Produto -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-box me-2"></i>Produto Associado
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($codigoBarras->produto)
                                <div class="text-center mb-3">
                                    <img src="{{ $codigoBarras->produto->url_imagem_principal }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 150px;" 
                                         alt="{{ $codigoBarras->produto->nome }}">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nome</label>
                                    <div class="form-control-plaintext">
                                        <a href="#" class="text-decoration-none">
                                            {{ $codigoBarras->produto->nome }}
                                        </a>
                                    </div>
                                </div>

                                @if($codigoBarras->produto->sku)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">SKU</label>
                                    <div class="form-control-plaintext">
                                        <code>{{ $codigoBarras->produto->sku }}</code>
                                    </div>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Preço de Venda</label>
                                    <div class="form-control-plaintext">
                                        <span class="h6 text-success">{{ $codigoBarras->produto->preco_venda_formatado }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <div class="form-control-plaintext">
                                        {!! $codigoBarras->produto->status_badge !!}
                                    </div>
                                </div>

                                @if($codigoBarras->produto->controla_estoque)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Estoque</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge {{ $codigoBarras->produto->estoque_baixo ? 'bg-warning' : 'bg-success' }}">
                                            {{ number_format($codigoBarras->produto->estoque_atual, 2, ',', '.') }} {{ $codigoBarras->produto->unidade_medida }}
                                        </span>
                                    </div>
                                </div>
                                @endif

                                <div class="d-grid">
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>Ver Produto
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                    <p>Produto não encontrado</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Outros Códigos do Produto -->
                    @if($codigoBarras->produto && $outrosCodigos->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Outros Códigos do Produto
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach($outrosCodigos as $outro)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $outro->codigo }}</div>
                                        <small class="text-muted">{{ strtoupper($outro->tipo) }}</small>
                                    </div>
                                    <div>
                                        @if($outro->principal)
                                            <span class="badge bg-warning">Principal</span>
                                        @endif
                                        @if($outro->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-2">
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                @if($codigoBarras->ativo)
                                    <form method="POST" action="#" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="ativo" value="0">
                                        <button type="submit" class="btn btn-outline-warning" 
                                                onclick="return confirm('Deseja realmente desativar este código?')">
                                            <i class="fas fa-pause me-2"></i>Desativar
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="#" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="ativo" value="1">
                                        <button type="submit" class="btn btn-outline-success">
                                            <i class="fas fa-play me-2"></i>Ativar
                                        </button>
                                    </form>
                                @endif

                                @if(!$codigoBarras->principal && $codigoBarras->ativo)
                                    <form method="POST" action="#" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="principal" value="1">
                                        <button type="submit" class="btn btn-outline-warning"
                                                onclick="return confirm('Deseja definir este como código principal?')">
                                            <i class="fas fa-star me-2"></i>Tornar Principal
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('comerciantes.produtos.codigos-barras.edit', $codigoBarras) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Editar
                                </a>

                                @if($codigoBarras->podeSerDeletado())
                                    <form method="POST" action="{{ route('comerciantes.produtos.codigos-barras.destroy', $codigoBarras) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Tem certeza que deseja excluir este código? Esta ação não pode ser desfeita.')">
                                            <i class="fas fa-trash me-2"></i>Excluir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.barcode-simulation {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
}

@media print {
    .btn, .card-header, .breadcrumb {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush

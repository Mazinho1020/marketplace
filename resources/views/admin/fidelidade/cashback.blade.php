@extends('admin.layouts.fidelidade')

@section('title', 'Cashback Fidelidade')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="mdi mdi-cash-multiple text-primary"></i> Sistema de Cashback
                </h2>
                <p class="text-muted mb-0">Configurações gerais e regras do sistema de cashback</p>
            </div>
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaRegra">
                    <i class="mdi mdi-plus"></i> Nova Regra
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Total de Regras</h6>
                    <h4 class="mb-0">{{ $stats['total_regras'] ?? 0 }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-cog text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Regras Ativas</h6>
                    <h4 class="mb-0">{{ $stats['regras_ativas'] ?? 0 }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Cashback Pago</h6>
                    <h4 class="mb-0">R$ {{ number_format($stats['cashback_pago'] ?? 0, 2, ',', '.') }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-cash text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card danger">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Economia Total</h6>
                    <h4 class="mb-0">R$ {{ number_format($stats['economia_total'] ?? 0, 2, ',', '.') }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-currency-usd text-danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Filtros e Lista -->
<div class="table-container">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                <input type="text" class="form-control" placeholder="Buscar regra...">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">Todos os Status</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">Todos os Tipos</option>
                <option value="percentual">Percentual</option>
                <option value="fixo">Valor Fixo</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select">
                <option value="nome">Ordenar por Nome</option>
                <option value="valor">Ordenar por Valor</option>
                <option value="data">Ordenar por Data</option>
            </select>
        </div>
    </div>

    <!-- Lista de Regras -->
    <div class="row">
        @forelse($regras ?? [] as $regra)
        <div class="col-md-6 mb-3">
            <div class="rule-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1">{{ $regra->nome ?? 'Cashback Padrão' }}</h5>
                        <p class="text-muted small mb-0">{{ $regra->descricao ?? 'Regra padrão do sistema' }}</p>
                    </div>
                    <div>
                        @if(($regra->status ?? 'ativo') == 'ativo')
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-warning">Inativo</span>
                        @endif
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="cashback-badge">
                                {{ $regra->valor ?? 2.5 }}{{ ($regra->tipo ?? 'percentual') == 'percentual' ? '%' : '' }}
                            </div>
                            <small class="text-muted d-block mt-1">
                                @if(($regra->tipo ?? 'percentual') == 'percentual')
                                    Cashback Percentual
                                @else
                                    Cashback Valor Fixo
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="text-muted">Valor Mínimo:</small>
                        <br><strong>R$ {{ number_format($regra->valor_minimo_compra ?? 10, 2, ',', '.') }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Valor Máximo:</small>
                        <br><strong>R$ {{ number_format($regra->valor_maximo_compra ?? 999999, 2, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Criado em {{ \Carbon\Carbon::parse($regra->created_at ?? now())->format('d/m/Y') }}
                    </small>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" title="Editar">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Desativar">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="mdi mdi-cash-multiple text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">Nenhuma regra de cashback encontrada</h4>
                <p class="text-muted">Configure as primeiras regras para o sistema de cashback</p>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaRegra">
                    <i class="mdi mdi-plus"></i> Criar Primeira Regra
                </button>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Nova Regra -->
<div class="modal fade" id="modalNovaRegra" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-cash-plus"></i> Nova Regra de Cashback
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    Esta é uma página administrativa apenas para visualização. 
                    Para criar regras, utilize o sistema operacional completo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Paginação -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="pagination-info">
            <small class="text-muted">
                @if(method_exists($regras, 'firstItem'))
                    Mostrando <span>{{ $regras->firstItem() ?? 0 }}</span> a <span>{{ $regras->lastItem() ?? 0 }}</span> de <span>{{ $regras->total() ?? 0 }}</span> registros
                @else
                    Mostrando {{ $regras->count() ?? 0 }} registros
                @endif
            </small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end">
            @if(method_exists($regras, 'links'))
                {{ $regras->links() }}
            @endif
        </div>
    </div>
</div>

@endsection

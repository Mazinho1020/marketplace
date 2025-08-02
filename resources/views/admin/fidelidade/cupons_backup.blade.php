@extends('admin.layouts.fidelidade')

@section('title', 'Cupons Fidelidade')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="mdi mdi-ticket-percent text-primary"></i> Sistema de Cupons
                </h2>
                <p class="text-muted mb-0">Gerenciamento geral de cupons do programa de fidelidade</p>
            </div>
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoCupom">
                    <i class="mdi mdi-plus"></i> Novo Cupom
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
                    <h6 class="text-muted mb-1">Total de Cupons</h6>
                    <h4 class="mb-0">{{ $stats['total_cupons'] ?? 0 }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-ticket-percent text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Cupons Ativos</h6>
                    <h4 class="mb-0">{{ $stats['cupons_ativos'] ?? 0 }}</h4>
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
                    <h6 class="text-muted mb-1">Cupons Utilizados</h6>
                    <h4 class="mb-0">{{ $stats['cupons_utilizados'] ?? 0 }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-check text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card danger">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Desconto Total</h6>
                    <h4 class="mb-0">R$ {{ number_format($stats['desconto_total'] ?? 0, 2, ',', '.') }}</h4>
                </div>
                <div class="align-self-center">
                    <i class="mdi mdi-currency-usd text-danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="table-container">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                <input type="text" class="form-control" placeholder="Buscar cupom...">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">Todos os Status</option>
                <option value="ativo">Ativo</option>
                <option value="usado">Usado</option>
                <option value="expirado">Expirado</option>
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
                <option value="codigo">Ordenar por Código</option>
                <option value="data">Ordenar por Data</option>
                <option value="desconto">Ordenar por Desconto</option>
            </select>
        </div>
    </div>

    <!-- Lista de Cupons -->
    <div class="row">
        @if(isset($cupons) && $cupons->count() > 0)
            @foreach($cupons as $cupom)
            <div class="col-md-6 mb-3">
                <div class="cupom-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="mdi mdi-ticket-percent text-success me-2" style="font-size: 1.5rem;"></i>
                                <strong class="coupon-code">{{ $cupom->codigo ?? 'EXEMPLO' }}</strong>
                            </div>
                            <h6 class="mb-1">{{ $cupom->nome ?? 'Nome do Cupom' }}</h6>
                            <p class="text-muted small mb-2">{{ $cupom->descricao ?? 'Descrição do cupom' }}</p>
                            
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Desconto:</small>
                                    <br><strong class="text-success">{{ $cupom->valor ?? '0' }}{{ ($cupom->tipo_desconto ?? 'percentual') == 'percentual' ? '%' : ' R$' }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Programa:</small>
                                    <br><strong>{{ $cupom->programa_nome ?? 'Geral' }}</strong>
                                </div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Criado:</small>
                                    <br><small>{{ isset($cupom->created_at) ? \Carbon\Carbon::parse($cupom->created_at)->format('d/m/Y') : 'N/A' }}</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Expira:</small>
                                    <br><small>{{ isset($cupom->data_fim) ? \Carbon\Carbon::parse($cupom->data_fim)->format('d/m/Y') : 'Sem limite' }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ms-3">
                            <span class="badge bg-{{ ($cupom->status ?? 'ativo') == 'ativo' ? 'success' : 'secondary' }}">
                                {{ ucfirst($cupom->status ?? 'Ativo') }}
                            </span>
                            <div class="mt-2">
                                <div class="btn-group-vertical">
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
                </div>
            </div>
            @endforeach
        @else
        <!-- Exemplo de cupons quando não há dados -->
        <div class="col-md-6 mb-3">
            <div class="cupom-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="mdi mdi-ticket-percent text-success me-2" style="font-size: 1.5rem;"></i>
                            <strong class="coupon-code">BEMVINDO10</strong>
                        </div>
                        <h6 class="mb-1">Cupom de Boas-vindas</h6>
                        <p class="text-muted small mb-2">Desconto de 10% na primeira compra</p>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Desconto:</small>
                                <br><strong class="text-success">10%</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Cliente:</small>
                                <br><strong>Geral</strong>
                            </div>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Criado:</small>
                                <br><small>02/08/2025</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Expira:</small>
                                <br><small>01/09/2025</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ms-3">
                        <span class="badge bg-success">Ativo</span>
                        <div class="mt-2">
                            <div class="btn-group-vertical">
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
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="cupom-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="mdi mdi-ticket-percent text-success me-2" style="font-size: 1.5rem;"></i>
                            <strong class="coupon-code">FIDELIDADE20</strong>
                        </div>
                        <h6 class="mb-1">Cupom Fidelidade</h6>
                        <p class="text-muted small mb-2">Desconto de 20% para clientes fiéis</p>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Desconto:</small>
                                <br><strong class="text-success">20%</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Cliente:</small>
                                <br><strong>VIP</strong>
                            </div>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Criado:</small>
                                <br><small>01/08/2025</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Expira:</small>
                                <br><small>31/12/2025</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ms-3">
                        <span class="badge bg-success">Ativo</span>
                        <div class="mt-2">
                            <div class="btn-group-vertical">
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
            </div>
        </div>
        @endif
        @endforelse
    </div>

    <!-- Paginação -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="pagination-info">
                <small class="text-muted">
                    @if(isset($cupons) && method_exists($cupons, 'total'))
                        Mostrando <span>{{ $cupons->firstItem() ?? 0 }}</span> a <span>{{ $cupons->lastItem() ?? 0 }}</span> de <span>{{ $cupons->total() ?? 0 }}</span> registros
                    @else
                        Mostrando dados de exemplo (2 cupons)
                    @endif
                </small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                @if(isset($cupons) && method_exists($cupons, 'links'))
                    {{ $cupons->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Cupom -->
<div class="modal fade" id="modalNovoCupom" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-ticket-plus"></i> Novo Cupom
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    Esta é uma página administrativa apenas para visualização. 
                    Para criar cupons, utilize o sistema operacional completo.
                </div>
                <form id="form-novo-cupom">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código do Cupom *</label>
                            <input type="text" class="form-control" name="codigo" placeholder="Ex: DESCONTO10" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Cupom *</label>
                            <input type="text" class="form-control" name="nome" placeholder="Ex: Cupom de Desconto" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="descricao" rows="2" placeholder="Descrição do cupom..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Desconto *</label>
                            <select class="form-select" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="percentual">Percentual (%)</option>
                                <option value="fixo">Valor Fixo (R$)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valor do Desconto *</label>
                            <input type="number" class="form-control" name="valor" placeholder="Ex: 10" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data de Expiração</label>
                            <input type="date" class="form-control" name="expires_at">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Limite de Uso</label>
                            <input type="number" class="form-control" name="limite_uso" placeholder="0 = ilimitado">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="salvarCupom()">
                    <i class="mdi mdi-check"></i> Salvar Cupom
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function salvarCupom() {
        showToast('Cupom salvo com sucesso!', 'success');
    }
</script>
@endsection
<div class="table-container">
<div class="row mb-3">
<div class="col-md-4">
<div class="input-group">
<span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
<input type="text" class="form-control" placeholder="Buscar cupom...">
<div class="col-md-3">
<select class="form-select">
<option value="">Todos os Status</option>
<option value="ativo">Ativo</option>
<option value="usado">Usado</option>
<option value="expirado">Expirado</option>
</select>
<div class="col-md-3">
<select class="form-select">
<option value="">Todos os Tipos</option>
<option value="percentual">Percentual</option>
<option value="fixo">Valor Fixo</option>
</select>
<div class="col-md-2">
<select class="form-select">
<option value="codigo">Ordenar por Código</option>
<option value="data">Ordenar por Data</option>
<option value="desconto">Ordenar por Desconto</option>
</select>
<!-- Lista de Cupons -->
<div class="row">
@forelse($cupons as $cupom)
<div class="col-md-6 mb-3">
<div class="coupon-card">
<div class="d-flex justify-content-between align-items-start">
<div class="flex-grow-1">
<div class="d-flex align-items-center mb-2">
<i class="mdi mdi-ticket-percent text-success me-2" style="font-size: 1.5rem;"></i>
<span class="coupon-code">{{ $cupom->codigo }}</span>
<h6 class="mb-1">{{ $cupom->nome ?? 'Cupom de Desconto' }}</h6>
<p class="text-muted small mb-2">{{ $cupom->descricao ?? 'Cupom do sistema de fidelidade' }}</p>
<div class="row g-2 mb-2">
<div class="col-6">
<small class="text-muted">Desconto:</small>
<div class="discount-badge">
{{ $cupom->valor_desconto ?? 10 }}{{ ($cupom->tipo_desconto ?? 'percentual') == 'percentual' ? '%' : '' }}
<div class="col-6">
<small class="text-muted">Cliente:</small>
<br><strong>{{ $cupom->cliente_id ?? 'Geral' }}</strong>
<div class="row g-2">
<div class="col-6">
<small class="text-muted">Criado:</small>
<br><small>{{ \Carbon\Carbon::parse($cupom->data_criacao ?? now())->format('d/m/Y') }}</small>
<div class="col-6">
<small class="text-muted">Expira:</small>
<br><small>{{ \Carbon\Carbon::parse($cupom->data_expiracao ?? now()->addDays(30))->format('d/m/Y') }}</small>
<div class="ms-3">
@if(($cupom->status ?? 'ativo') == 'ativo')
<span class="badge bg-success">Ativo</span>
@elseif($cupom->status == 'usado')
<span class="badge bg-warning">Usado</span>
@elseif($cupom->status == 'expirado')
<span class="badge bg-danger">Expirado</span>
@else
<span class="badge bg-secondary">{{ ucfirst($cupom->status ?? 'Ativo') }}</span>
@endif
<div class="mt-2">
<div class="btn-group-vertical">
<button class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
<i class="mdi mdi-eye"></i>
<button class="btn btn-sm btn-outline-warning" title="Editar">
<i class="mdi mdi-pencil"></i>
<button class="btn btn-sm btn-outline-danger" title="Desativar">
<i class="mdi mdi-close"></i>
@empty
<div class="col-12">
<div class="text-center py-5">
<i class="mdi mdi-ticket-percent text-muted" style="font-size: 4rem;"></i>
<h4 class="mt-3 text-muted">Nenhum cupom encontrado</h4>
<p class="text-muted">Comece criando o primeiro cupom do sistema</p>
<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoCupom">
<i class="mdi mdi-plus"></i> Criar Primeiro Cupom
@endforelse
            <!-- Paginação -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="pagination-info">
                        <small class="text-muted">
                            @if(isset($cupons) && method_exists($cupons, 'total'))
                                Mostrando <span>{{ $cupons->firstItem() ?? 0 }}</span> a <span>{{ $cupons->lastItem() ?? 0 }}</span> de <span>{{ $cupons->total() ?? 0 }}</span> registros
                            @else
                                Mostrando registros de exemplo
                            @endif
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        @if(isset($cupons) && method_exists($cupons, 'links'))
                            {{ $cupons->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Cupom -->
<div class="modal fade" id="modalNovoCupom" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-ticket-plus"></i> Novo Cupom
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    Esta é uma página administrativa apenas para visualização. 
                    Para criar cupons, utilize o sistema operacional completo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

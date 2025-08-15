@extends('layouts.comerciante')

@section('title', 'Histórico de Pagamentos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Histórico de Pagamentos</h1>
            <p class="text-muted mb-0">Acompanhe todas as suas transações</p>
        </div>
        <div>
            <a href="{{ route('comerciantes.planos.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-success">R$ {{ number_format($statsHistorico['total_pago'], 2, ',', '.') }}</h4>
                    <p class="text-muted mb-0">Total Pago</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $statsHistorico['total_transacoes'] }}</h4>
                    <p class="text-muted mb-0">Total de Transações</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $statsHistorico['pendentes'] }}</h4>
                    <p class="text-muted mb-0">Pendentes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="aprovado" {{ request('status') === 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                            <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            <option value="recusado" {{ request('status') === 'recusado' ? 'selected' : '' }}>Recusado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <select class="form-select" name="forma_pagamento">
                            <option value="">Todas</option>
                            <option value="pix" {{ request('forma_pagamento') === 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="credit_card" {{ request('forma_pagamento') === 'credit_card' ? 'selected' : '' }}>Cartão</option>
                            <option value="bank_slip" {{ request('forma_pagamento') === 'bank_slip' ? 'selected' : '' }}>Boleto</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Início</label>
                        <input type="date" class="form-control" name="data_inicio" value="{{ request('data_inicio') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Fim</label>
                        <input type="date" class="form-control" name="data_fim" value="{{ request('data_fim') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Transações</h5>
        </div>
        <div class="card-body p-0">
            @if($transacoes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Forma de Pagamento</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transacoes as $transacao)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $transacao->created_at->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $transacao->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $transacao->descricao }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $transacao->codigo_transacao }}</small>
                                </div>
                            </td>
                            <td>
                                @switch($transacao->forma_pagamento)
                                @case('pix')
                                <span class="badge bg-primary">
                                    <i class="fas fa-qrcode me-1"></i>PIX
                                </span>
                                @break
                                @case('credit_card')
                                <span class="badge bg-success">
                                    <i class="fas fa-credit-card me-1"></i>Cartão
                                </span>
                                @break
                                @case('bank_slip')
                                <span class="badge bg-warning">
                                    <i class="fas fa-barcode me-1"></i>Boleto
                                </span>
                                @break
                                @default
                                <span class="badge bg-secondary">{{ $transacao->forma_pagamento }}</span>
                                @endswitch
                            </td>
                            <td>
                                <strong>R$ {{ number_format($transacao->valor_final, 2, ',', '.') }}</strong>
                                @if($transacao->valor_desconto > 0)
                                <br>
                                <small class="text-success">
                                    Desconto: R$ {{ number_format($transacao->valor_desconto, 2, ',', '.') }}
                                </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $transacao->status_cor }}">
                                    <i class="{{ $transacao->status_icone }} me-1"></i>
                                    {{ ucfirst($transacao->status) }}
                                </span>
                                @if($transacao->aprovado_em)
                                <br>
                                <small class="text-muted">{{ $transacao->aprovado_em->format('d/m/Y H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary"
                                        onclick="verDetalhes('{{ $transacao->uuid }}')"
                                        title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($transacao->status === 'pendente' && $transacao->forma_pagamento === 'bank_slip')
                                    <a href="#" class="btn btn-outline-secondary" title="Baixar Boleto">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif

                                    @if($transacao->status === 'pendente')
                                    <a href="{{ route('comerciantes.planos.checkout', $transacao->uuid) }}"
                                        class="btn btn-outline-success" title="Finalizar Pagamento">
                                        <i class="fas fa-credit-card"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="card-footer">
                {{ $transacoes->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma transação encontrada</h5>
                <p class="text-muted">Quando você realizar pagamentos, eles aparecerão aqui.</p>
                <a href="{{ route('comerciantes.planos.planos') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Escolher um Plano
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Transação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhes-content">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function verDetalhes(uuid) {
        // Implementar modal com detalhes da transação
        $('#modalDetalhes').modal('show');
        $('#detalhes-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>');

        // Simular carregamento de detalhes
        setTimeout(() => {
            $('#detalhes-content').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6>Informações da Transação</h6>
                    <table class="table table-sm">
                        <tr><td><strong>UUID:</strong></td><td>${uuid}</td></tr>
                        <tr><td><strong>Data:</strong></td><td>{{ now()->format('d/m/Y H:i') }}</td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-success">Aprovado</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Informações do Pagamento</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Forma:</strong></td><td>PIX</td></tr>
                        <tr><td><strong>Valor:</strong></td><td>R$ 50,00</td></tr>
                        <tr><td><strong>Gateway:</strong></td><td>PIX Interno</td></tr>
                    </table>
                </div>
            </div>
        `);
        }, 1000);
    }
</script>
@endpush
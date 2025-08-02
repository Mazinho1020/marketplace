@extends('layouts.admin')

@section('title', 'Gateways de Pagamento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-server-network me-2"></i>
                    Gateways de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.dashboard') }}">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Gateways</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payments.settings') }}" class="btn btn-primary">
                    <i class="uil uil-plus me-1"></i>
                    Configurar Gateway
                </a>
            </div>
        </div>
    </div>

    <!-- Lista de Gateways -->
    <div class="row">
        @forelse($gateways as $gateway)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($gateway->logo_url)
                            <img src="{{ $gateway->logo_url }}" 
                                 alt="{{ $gateway->name }}" 
                                 style="width: 40px; height: 40px; object-fit: contain;"
                                 class="me-3">
                        @else
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                <i class="uil uil-server-network"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-0">{{ $gateway->name }}</h5>
                            <small class="text-muted">{{ $gateway->provider }}</small>
                        </div>
                    </div>
                    <div>
                        @if($gateway->is_active)
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <h4 class="text-primary mb-0">{{ $gateway->total_transactions }}</h4>
                                <small class="text-muted">Transações</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <h4 class="text-success mb-0">
                                    {{ $gateway->approved_transactions }}
                                </h4>
                                <small class="text-muted">Aprovadas</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <h4 class="text-warning mb-0">
                                    {{ $gateway->pending_transactions }}
                                </h4>
                                <small class="text-muted">Pendentes</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">Valor Total Processado:</small>
                        <h5 class="text-success mb-0">
                            R$ {{ number_format($gateway->approved_amount, 2, ',', '.') }}
                        </h5>
                    </div>

                    @if($gateway->total_transactions > 0)
                    <div class="mb-3">
                        <small class="text-muted">Taxa de Aprovação:</small>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $gateway->success_rate }}%"
                                 title="{{ number_format($gateway->success_rate, 1) }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($gateway->success_rate, 1) }}%</small>
                    </div>
                    @endif

                    @if($gateway->description)
                    <div class="mb-3">
                        <small class="text-muted">{{ Str::limit($gateway->description, 100) }}</small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted">
                            <strong>Criado:</strong> {{ $gateway->created_at->format('d/m/Y H:i') }}
                        </small>
                        @if($gateway->updated_at != $gateway->created_at)
                        <br>
                        <small class="text-muted">
                            <strong>Atualizado:</strong> {{ $gateway->updated_at->format('d/m/Y H:i') }}
                        </small>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100">
                        <a href="{{ route('admin.payments.gateway-details', $gateway->id) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="uil uil-eye me-1"></i>
                            Detalhes
                        </a>
                        <a href="{{ route('admin.payments.transactions', ['gateway_id' => $gateway->id]) }}" 
                           class="btn btn-outline-info btn-sm">
                            <i class="uil uil-transaction me-1"></i>
                            Transações
                        </a>
                        <button type="button" 
                                class="btn btn-outline-{{ $gateway->is_active ? 'warning' : 'success' }} btn-sm"
                                onclick="toggleGateway({{ $gateway->id }}, {{ $gateway->is_active ? 'false' : 'true' }})">
                            <i class="uil uil-{{ $gateway->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $gateway->is_active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="uil uil-server-network text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Nenhum Gateway Configurado</h4>
                    <p class="text-muted">Configure um gateway de pagamento para começar a processar transações</p>
                    <a href="{{ route('admin.payments.settings') }}" class="btn btn-primary">
                        <i class="uil uil-plus me-1"></i>
                        Configurar Primeiro Gateway
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Resumo Geral -->
    @if($gateways->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-pie me-2"></i>
                        Resumo Geral dos Gateways
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h3 class="text-primary">{{ $gateways->count() }}</h3>
                            <p class="text-muted mb-0">Total de Gateways</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-success">{{ $gateways->where('is_active', true)->count() }}</h3>
                            <p class="text-muted mb-0">Gateways Ativos</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-info">{{ $gateways->sum('total_transactions') }}</h3>
                            <p class="text-muted mb-0">Total de Transações</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-warning">
                                R$ {{ number_format($gateways->sum('approved_amount'), 2, ',', '.') }}
                            </h3>
                            <p class="text-muted mb-0">Valor Total Processado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function toggleGateway(gatewayId, activate) {
    if (confirm(`Tem certeza que deseja ${activate ? 'ativar' : 'desativar'} este gateway?`)) {
        // Implementar ativação/desativação via AJAX
        fetch(`/admin/payments/gateways/${gatewayId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_active: activate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar status do gateway');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao comunicar com o servidor');
        });
    }
}

// Atualizar dados a cada 30 segundos
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Detalhes do Webhook')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-webhook me-2"></i>
                    Detalhes do Webhook #{{ $webhook->id }}
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.dashboard') }}">Pagamentos</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.webhooks') }}">Webhooks</a></li>
                        <li class="breadcrumb-item active">Detalhes</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payments.webhooks') }}" class="btn btn-outline-secondary">
                    <i class="uil uil-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações do Webhook -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-info-circle me-2"></i>
                        Informações do Webhook
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><code>{{ $webhook->id }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Evento:</strong></td>
                                    <td>
                                        @if($webhook->event_type)
                                            <span class="badge bg-info">{{ $webhook->event_type }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($webhook->status === 'processed')
                                            <span class="badge bg-success">
                                                <i class="uil uil-check me-1"></i>Processado
                                            </span>
                                        @elseif($webhook->status === 'failed')
                                            <span class="badge bg-danger">
                                                <i class="uil uil-times me-1"></i>Falhou
                                            </span>
                                        @elseif($webhook->status === 'received')
                                            <span class="badge bg-warning">
                                                <i class="uil uil-clock me-1"></i>Recebido
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($webhook->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tentativas:</strong></td>
                                    <td>
                                        @if($webhook->attempts > 1)
                                            <span class="badge bg-warning">{{ $webhook->attempts }}x</span>
                                        @else
                                            <span class="text-muted">{{ $webhook->attempts ?? 1 }}x</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Recebido em:</strong></td>
                                    <td>{{ $webhook->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Processado em:</strong></td>
                                    <td>
                                        @if($webhook->processed_at)
                                            {{ $webhook->processed_at->format('d/m/Y H:i:s') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($webhook->error_message)
                                <tr>
                                    <td><strong>Erro:</strong></td>
                                    <td>
                                        <span class="text-danger">{{ $webhook->error_message }}</span>
                                    </td>
                                </tr>
                                @endif
                                @if($webhook->gateway_signature)
                                <tr>
                                    <td><strong>Assinatura:</strong></td>
                                    <td><code class="small">{{ Str::limit($webhook->gateway_signature ?? '', 30) }}</code></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transação Relacionada -->
            @if($webhook->transaction)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-transaction me-2"></i>
                        Transação Relacionada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>ID da Transação:</strong>
                                <a href="{{ route('admin.payments.transaction-details', $webhook->transaction->id) }}" class="text-decoration-none">
                                    <code>#{{ $webhook->transaction->external_id ?? $webhook->transaction->id }}</code>
                                </a>
                            </div>
                            <div class="mb-3">
                                <strong>Valor:</strong>
                                <span class="text-success">R$ {{ number_format($webhook->transaction->amount, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Status:</strong>
                                @if($webhook->transaction->status === 'approved')
                                    <span class="badge bg-success">Aprovada</span>
                                @elseif($webhook->transaction->status === 'pending')
                                    <span class="badge bg-warning">Pendente</span>
                                @elseif($webhook->transaction->status === 'rejected')
                                    <span class="badge bg-danger">Rejeitada</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($webhook->transaction->status) }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <strong>Gateway:</strong>
                                {{ $webhook->transaction->gateway->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.payments.transaction-details', $webhook->transaction->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="uil uil-eye me-1"></i>
                        Ver Detalhes da Transação
                    </a>
                </div>
            </div>
            @endif

            <!-- Dados Brutos -->
            @if($webhook->gateway_data)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-code-branch me-2"></i>
                        Dados do Webhook
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleRawData()">
                        <i class="uil uil-eye me-1"></i>
                        Mostrar/Ocultar
                    </button>
                </div>
                <div class="card-body" id="rawDataContainer" style="display: none;">
                    <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($webhook->gateway_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-setting me-2"></i>
                        Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($webhook->status === 'failed')
                        <button type="button" class="btn btn-warning" onclick="reprocessWebhook()">
                            <i class="uil uil-refresh me-1"></i>
                            Reprocessar Webhook
                        </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-info" onclick="exportWebhook()">
                            <i class="uil uil-export me-1"></i>
                            Exportar Dados
                        </button>
                        
                        @if($webhook->transaction)
                        <a href="{{ route('admin.payments.transaction-details', $webhook->transaction->id) }}" class="btn btn-outline-primary">
                            <i class="uil uil-transaction me-1"></i>
                            Ver Transação
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informações Técnicas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-info me-2"></i>
                        Informações Técnicas
                    </h5>
                </div>
                <div class="card-body">
                    @if($webhook->ip_address)
                    <div class="mb-3">
                        <small class="text-muted">IP de Origem:</small>
                        <div><code>{{ $webhook->ip_address }}</code></div>
                    </div>
                    @endif
                    
                    @if($webhook->user_agent)
                    <div class="mb-3">
                        <small class="text-muted">User Agent:</small>
                        <div class="small">{{ Str::limit($webhook->user_agent ?? '', 50) }}</div>
                    </div>
                    @endif
                    
                    @if($webhook->headers)
                    <div class="mb-3">
                        <small class="text-muted">Headers HTTP:</small>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleHeaders()">
                                <i class="uil uil-eye me-1"></i>
                                Ver Headers
                            </button>
                        </div>
                        <div id="headersContainer" style="display: none;" class="mt-2">
                            <pre class="bg-light p-2 rounded small" style="max-height: 200px; overflow-y: auto;"><code>{{ json_encode($webhook->headers, JSON_PRETTY_PRINT) }}</code></pre>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <small class="text-muted">Tamanho dos Dados:</small>
                        <div>{{ $webhook->gateway_data ? Str::of(json_encode($webhook->gateway_data))->length() . ' bytes' : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleRawData() {
    const container = document.getElementById('rawDataContainer');
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
}

function toggleHeaders() {
    const container = document.getElementById('headersContainer');
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
}

function reprocessWebhook() {
    if (confirm('Tem certeza que deseja reprocessar este webhook?')) {
        fetch(`/admin/payments/webhooks/{{ $webhook->id }}/reprocess`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Webhook reprocessado com sucesso');
                location.reload();
            } else {
                alert('Erro ao reprocessar webhook: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao comunicar com o servidor');
        });
    }
}

function exportWebhook() {
    const data = @json($webhook);
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `webhook-${data.id}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection

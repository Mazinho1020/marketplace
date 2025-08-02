@extends('layouts.admin')

@section('title', 'Webhooks de Pagamento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-webhook me-2"></i>
                    Webhooks de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.dashboard') }}">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Webhooks</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="uil uil-filter me-2"></i>
                Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.webhooks') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Recebido</option>
                            <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Processado</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Falhou</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Final</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-filter me-1"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Webhooks -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="uil uil-list-ul me-2"></i>
                Webhooks Recebidos ({{ $webhooks->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Transação</th>
                            <th>Evento</th>
                            <th>Status</th>
                            <th>Tentativas</th>
                            <th>Data/Hora</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($webhooks as $webhook)
                        <tr>
                            <td><code>#{{ $webhook->id }}</code></td>
                            <td>
                                @if($webhook->transaction)
                                    <a href="{{ route('admin.payments.transaction-details', $webhook->transaction->id) }}" 
                                       class="text-decoration-none">
                                        <code>#{{ $webhook->transaction->external_id ?? $webhook->transaction->id }}</code>
                                    </a>
                                    <br>
                                    <small class="text-muted">R$ {{ number_format($webhook->transaction->amount, 2, ',', '.') }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($webhook->event_type)
                                    <span class="badge bg-info">{{ $webhook->event_type }}</span>
                                @else
                                    <span class="text-muted">Webhook</span>
                                @endif
                            </td>
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
                            <td>
                                @if($webhook->attempts > 1)
                                    <span class="badge bg-warning">{{ $webhook->attempts }}x</span>
                                @else
                                    <span class="text-muted">{{ $webhook->attempts ?? 1 }}x</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $webhook->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $webhook->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payments.webhook-details', $webhook->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                    @if($webhook->status === 'failed')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                title="Reprocessar"
                                                onclick="reprocessWebhook({{ $webhook->id }})">
                                            <i class="uil uil-refresh"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="uil uil-webhook text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">Nenhum webhook encontrado</h5>
                                <p class="text-muted">Os webhooks recebidos dos gateways aparecerão aqui</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Informações de registros -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="pagination-info">
                <small class="text-muted">
                    Mostrando {{ $webhooks->count() }} 
                    de {{ $webhooks->count() }} registros
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function reprocessWebhook(webhookId) {
    if (confirm('Tem certeza que deseja reprocessar este webhook?')) {
        fetch(`/admin/payments/webhooks/${webhookId}/reprocess`, {
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
</script>
@endsection

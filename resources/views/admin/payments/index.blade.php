@extends('layouts.admin')

@section('title', 'Pagamentos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-credit-card me-2"></i>
                    Pagamentos
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pagamentos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payments.transactions') }}" class="btn btn-primary">
                    <i class="uil uil-transaction me-1"></i>
                    Ver Transações
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary">
                            <i class="uil uil-transaction"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="text-muted mb-0 small">Total de Transações</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success">
                            <i class="uil uil-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ number_format($stats['completed'] ?? 0) }}</h3>
                            <p class="text-muted mb-0 small">Aprovadas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning">
                            <i class="uil uil-clock"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ number_format($stats['pending'] ?? 0) }}</h3>
                            <p class="text-muted mb-0 small">Pendentes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info">
                            <i class="uil uil-dollar-sign"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">R$ {{ number_format($stats['completed_amount'] ?? 0, 2, ',', '.') }}</h3>
                            <p class="text-muted mb-0 small">Total Processado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transações Recentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="uil uil-list-ul me-2"></i>
                            Transações Recentes
                        </h5>
                        <a href="{{ route('admin.payments.transactions') }}" class="btn btn-outline-primary btn-sm">
                            Ver Todas
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($transactions) && count($transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Merchant</th>
                                        <th>Valor</th>
                                        <th>Gateway</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <code>{{ $transaction->transaction_code ?? $transaction->id }}</code>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $transaction->merchant_name ?? 'N/A' }}</strong>
                                                @if(isset($transaction->merchant_email))
                                                    <br><small class="text-muted">{{ $transaction->merchant_email }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <strong>R$ {{ number_format($transaction->final_amount ?? 0, 2, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $transaction->gateway_name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($transaction->status ?? 'pending') {
                                                    'completed' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger',
                                                    'cancelled' => 'secondary',
                                                    default => 'info'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($transaction->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ isset($transaction->created_at) ? \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') : 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.payments.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="uil uil-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="uil uil-transaction fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma transação encontrada</h5>
                            <p class="text-muted">As transações aparecerão aqui quando disponíveis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}
</style>
@endpush

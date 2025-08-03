@extends('layouts.admin')

@section('title', 'Transações de Pagamento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-transaction me-2"></i>
                    Transações de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.dashboard') }}">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Transações</li>
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
                Filtros de Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.transactions') }}">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="aprovado" {{ request('status') === 'aprovado' ? 'selected' : '' }}>Aprovada</option>
                            <option value="recusado" {{ request('status') === 'recusado' ? 'selected' : '' }}>Rejeitada</option>
                            <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gateway</label>
                        <select name="gateway_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($gateways as $gateway)
                                <option value="{{ $gateway->id }}" {{ request('gateway_id') == $gateway->id ? 'selected' : '' }}>
                                    {{ $gateway->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Método</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Todos</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->value }}" {{ request('payment_method') === $method->value ? 'selected' : '' }}>
                                    {{ $method->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Final</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="ID, email..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="uil uil-filter me-1"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('admin.payments.transactions') }}" class="btn btn-outline-secondary">
                            <i class="uil uil-refresh me-1"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="uil uil-list-ul me-2"></i>
                Lista de Transações ({{ $transactions->count() }})
            </h5>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm">
                    <i class="uil uil-export me-1"></i>
                    Exportar
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID Transação</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Email</th>
                            <th>Data/Hora</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <div>
                                    <code>#{{ $transaction->codigo_transacao ?? $transaction->id }}</code>
                                    @if($transaction->descricao)
                                        <br><small class="text-muted">{{ Str::limit($transaction->descricao, 30) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="text-primary">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($transaction->forma_pagamento) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($transaction->gateway)
                                        <div class="me-2">
                                            @if($transaction->gateway->logo_url)
                                                <img src="{{ $transaction->gateway->logo_url }}" 
                                                     alt="{{ $transaction->gateway->nome }}" 
                                                     style="width: 24px; height: 24px; object-fit: contain;">
                                            @else
                                                <i class="uil uil-server-network text-primary"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $transaction->gateway->nome }}</strong>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($transaction->status === 'aprovado')
                                    <span class="badge bg-success">
                                        <i class="uil uil-check me-1"></i>Aprovada
                                    </span>
                                @elseif($transaction->status === 'pendente')
                                    <span class="badge bg-warning">
                                        <i class="uil uil-clock me-1"></i>Pendente
                                    </span>
                                @elseif($transaction->status === 'recusado')
                                    <span class="badge bg-danger">
                                        <i class="uil uil-times me-1"></i>Rejeitada
                                    </span>
                                @elseif($transaction->status === 'cancelado')
                                    <span class="badge bg-secondary">
                                        <i class="uil uil-ban me-1"></i>Cancelada
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->payer_email)
                                    <div>{{ $transaction->payer_email }}</div>
                                    @if($transaction->payer_name)
                                        <small class="text-muted">{{ $transaction->payer_name }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payments.transaction-details', $transaction->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                    @if($transaction->external_url)
                                        <a href="{{ $transaction->external_url }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-info" title="Ver no Gateway">
                                            <i class="uil uil-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="uil uil-credit-card text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">Nenhuma transação encontrada</h5>
                                <p class="text-muted">Tente ajustar os filtros ou aguarde novas transações</p>
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
                    Mostrando {{ $transactions->count() }} 
                    de {{ $transactions->count() }} registros
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

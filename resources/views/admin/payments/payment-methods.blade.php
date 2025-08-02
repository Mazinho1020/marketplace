@extends('layouts.admin')

@section('title', 'Métodos de Pagamento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-credit-card me-2"></i>
                    Métodos de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.dashboard') }}">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Métodos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Método -->
    <div class="row">
        @forelse($paymentMethodsStats as $method)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            @if($method->method === 'credit_card')
                                <i class="uil uil-credit-card text-primary me-2"></i>
                                Cartão de Crédito
                            @elseif($method->method === 'debit_card')
                                <i class="uil uil-credit-card text-success me-2"></i>
                                Cartão de Débito
                            @elseif($method->method === 'pix')
                                <i class="uil uil-qrcode-scan text-info me-2"></i>
                                PIX
                            @elseif($method->method === 'bank_slip')
                                <i class="uil uil-bill text-warning me-2"></i>
                                Boleto Bancário
                            @elseif($method->method === 'bank_transfer')
                                <i class="uil uil-exchange text-secondary me-2"></i>
                                Transferência Bancária
                            @else
                                <i class="uil uil-money-bill text-dark me-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $method->method)) }}
                            @endif
                        </h5>
                    </div>
                    <span class="badge bg-primary">{{ $method->total_transactions }} transações</span>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h4 class="text-success mb-0">R$ {{ number_format($method->total_amount, 2, ',', '.') }}</h4>
                            <small class="text-muted">Valor Total</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-primary mb-0">R$ {{ number_format($method->avg_amount, 2, ',', '.') }}</h4>
                            <small class="text-muted">Ticket Médio</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Distribuição de Uso:</small>
                        @php
                            $totalTransactions = $paymentMethodsStats->sum('total_transactions');
                            $percentage = $totalTransactions > 0 ? ($method->total_transactions / $totalTransactions) * 100 : 0;
                        @endphp
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ $percentage }}%"
                                 title="{{ number_format($percentage, 1) }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($percentage, 1) }}% do total</small>
                    </div>

                    <a href="{{ route('admin.payments.transactions', ['payment_method' => $method->method]) }}" 
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="uil uil-eye me-1"></i>
                        Ver Transações
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="uil uil-credit-card text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Nenhuma Transação Encontrada</h4>
                    <p class="text-muted">Quando houver transações, as estatísticas por método aparecerão aqui</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Resumo Geral -->
    @if($paymentMethodsStats->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-pie me-2"></i>
                        Resumo por Método de Pagamento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th>Transações</th>
                                    <th>Valor Total</th>
                                    <th>Ticket Médio</th>
                                    <th>Participação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethodsStats->sortByDesc('total_amount') as $method)
                                <tr>
                                    <td>
                                        @if($method->method === 'credit_card')
                                            <i class="uil uil-credit-card text-primary me-2"></i>
                                            Cartão de Crédito
                                        @elseif($method->method === 'debit_card')
                                            <i class="uil uil-credit-card text-success me-2"></i>
                                            Cartão de Débito
                                        @elseif($method->method === 'pix')
                                            <i class="uil uil-qrcode-scan text-info me-2"></i>
                                            PIX
                                        @elseif($method->method === 'bank_slip')
                                            <i class="uil uil-bill text-warning me-2"></i>
                                            Boleto Bancário
                                        @elseif($method->method === 'bank_transfer')
                                            <i class="uil uil-exchange text-secondary me-2"></i>
                                            Transferência Bancária
                                        @else
                                            <i class="uil uil-money-bill text-dark me-2"></i>
                                            {{ ucfirst(str_replace('_', ' ', $method->method)) }}
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($method->total_transactions) }}</strong></td>
                                    <td><strong class="text-success">R$ {{ number_format($method->total_amount, 2, ',', '.') }}</strong></td>
                                    <td>R$ {{ number_format($method->avg_amount, 2, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $totalAmount = $paymentMethodsStats->sum('total_amount');
                                            $percentage = $totalAmount > 0 ? ($method->total_amount / $totalAmount) * 100 : 0;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <small>{{ number_format($percentage, 1) }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.payments.transactions', ['payment_method' => $method->method]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="uil uil-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

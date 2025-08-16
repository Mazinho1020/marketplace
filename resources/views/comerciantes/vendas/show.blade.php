@extends('comerciantes.layout')

@section('title', 'Detalhes da Venda #' . $venda->numero_venda)

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Venda #{{ $venda->numero_venda }}</h1>
                    <p class="text-muted">{{ $venda->data_venda->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.index', $empresa) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    
                    @if($venda->status === 'pendente')
                        <a href="{{ route('comerciantes.empresas.vendas.gerenciar.edit', [$empresa, $venda->id]) }}" class="btn btn-outline-success">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        
                        <form method="POST" action="{{ route('comerciantes.empresas.vendas.gerenciar.confirmar', [$empresa, $venda->id]) }}" 
                              style="display: inline;" onsubmit="return confirm('Confirmar esta venda?')">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Confirmar Venda
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="cancelarVenda()">
                            <i class="fas fa-times"></i> Cancelar Venda
                        </button>
                    @endif
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="imprimirVenda()">
                                <i class="fas fa-receipt"></i> Cupom Fiscal
                            </a>
                            <a class="dropdown-item" href="#" onclick="imprimirRelatorio()">
                                <i class="fas fa-file-alt"></i> Relatório Detalhado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações da Venda -->
        <div class="col-lg-8">
            <!-- Status e Informações Básicas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações da Venda</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Número:</strong></td>
                                    <td>{{ $venda->numero_venda }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Data/Hora:</strong></td>
                                    <td>{{ $venda->data_venda->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td><span class="badge badge-secondary">{{ $venda->tipo_venda_formatado }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            $statusClass = match($venda->status) {
                                                'pendente' => 'warning',
                                                'confirmada' => 'success',
                                                'cancelada' => 'danger',
                                                'entregue' => 'info',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ $venda->status_formatado }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Cliente:</strong></td>
                                    <td>
                                        @if($venda->cliente)
                                            {{ $venda->cliente->nome }}
                                            @if($venda->cliente->telefone)
                                                <br><small class="text-muted">{{ $venda->cliente->telefone }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Cliente não informado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Vendedor:</strong></td>
                                    <td>{{ $venda->usuario ? $venda->usuario->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Observações:</strong></td>
                                    <td>{{ $venda->observacoes ?: 'Nenhuma observação' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itens da Venda -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Itens da Venda</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd</th>
                                    <th>Valor Unit.</th>
                                    <th>Desconto</th>
                                    <th>Total</th>
                                    <th>Obs.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venda->itens as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->produto ? $item->produto->nome : 'Produto não encontrado' }}</strong>
                                        @if($item->produto && $item->produto->codigo_sistema)
                                            <br><small class="text-muted">Código: {{ $item->produto->codigo_sistema }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantidade_formatada }}</td>
                                    <td>{{ $item->valor_unitario_formatado }}</td>
                                    <td>
                                        @if($item->desconto_total > 0)
                                            <span class="text-danger">{{ $item->desconto_total_formatado }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><strong>{{ $item->valor_total_formatado }}</strong></td>
                                    <td>
                                        @if($item->observacoes)
                                            <small class="text-info">{{ $item->observacoes }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Histórico de Pagamentos -->
            @if($venda->pagamentos->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Pagamentos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Forma</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venda->pagamentos as $pagamento)
                                <tr>
                                    <td>{{ $pagamento->data_pagamento ? \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $pagamento->formaPagamento ? $pagamento->formaPagamento->nome : 'N/A' }}</td>
                                    <td><strong>{{ $pagamento->valor_formatado }}</strong></td>
                                    <td>
                                        @php
                                            $statusClass = match($pagamento->status_pagamento) {
                                                'confirmado' => 'success',
                                                'processando' => 'warning',
                                                'cancelado' => 'danger',
                                                'estornado' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ $pagamento->status_formatado }}</span>
                                    </td>
                                    <td>{{ $pagamento->observacao ?: '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar com Totais e Ações -->
        <div class="col-lg-4">
            <!-- Totais -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Totais</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-right"><strong>{{ $venda->valor_total_formatado }}</strong></td>
                        </tr>
                        @if($venda->valor_desconto > 0)
                        <tr>
                            <td>Desconto:</td>
                            <td class="text-right text-danger">
                                <strong>- R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}</strong>
                            </td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td><strong>Total Líquido:</strong></td>
                            <td class="text-right">
                                <h4 class="text-success mb-0">{{ $venda->valor_liquido_formatado }}</h4>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informações Financeiras -->
            @if($venda->lancamento)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Lançamento Financeiro</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td>ID Lançamento:</td>
                            <td><strong>{{ $venda->lancamento->id }}</strong></td>
                        </tr>
                        <tr>
                            <td>Situação:</td>
                            <td>
                                @php
                                    $situacaoClass = match($venda->lancamento->situacao_financeira) {
                                        'confirmado', 'pago' => 'success',
                                        'pendente' => 'warning',
                                        'cancelado' => 'danger',
                                        'parcialmente_pago' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $situacaoClass }}">
                                    {{ ucfirst($venda->lancamento->situacao_financeira) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Valor Pago:</td>
                            <td>
                                <strong>R$ {{ number_format($venda->lancamento->valor_pago, 2, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Saldo:</td>
                            <td>
                                <strong>R$ {{ number_format($venda->lancamento->valor_saldo, 2, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </table>
                    
                    @if($venda->status === 'confirmada' && $venda->lancamento->valor_saldo > 0)
                    <div class="mt-3">
                        <a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}" class="btn btn-sm btn-outline-primary btn-block">
                            <i class="fas fa-plus"></i> Registrar Pagamento
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button onclick="imprimirVenda()" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-print"></i> Imprimir Cupom
                        </button>
                        
                        @if($venda->cliente && $venda->cliente->email)
                        <button onclick="enviarPorEmail()" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope"></i> Enviar por Email
                        </button>
                        @endif
                        
                        @if($venda->cliente && $venda->cliente->telefone)
                        <button onclick="enviarWhatsApp()" class="btn btn-outline-success btn-sm">
                            <i class="fab fa-whatsapp"></i> Enviar WhatsApp
                        </button>
                        @endif
                        
                        <a href="{{ route('comerciantes.empresas.vendas.gerenciar.create', $empresa) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-copy"></i> Duplicar Venda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cancelar Venda -->
<div class="modal fade" id="cancelarVendaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Venda #{{ $venda->numero_venda }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('comerciantes.empresas.vendas.gerenciar.cancelar', [$empresa, $venda->id]) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="motivo">Motivo do Cancelamento *</label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="3" required 
                                  placeholder="Informe o motivo do cancelamento da venda..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Esta ação não pode ser desfeita. O estoque dos produtos será restaurado automaticamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Cancelar Venda</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Área de impressão (oculta) -->
<div id="impressaoVenda" style="display: none;">
    <div class="text-center">
        <h3>{{ config('app.name') }}</h3>
        <p>Venda #{{ $venda->numero_venda }}</p>
        <p>{{ $venda->data_venda->format('d/m/Y H:i:s') }}</p>
        <hr>
        
        @if($venda->cliente)
        <p><strong>Cliente:</strong> {{ $venda->cliente->nome }}</p>
        @endif
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #ccc;">
                    <th style="text-align: left;">Produto</th>
                    <th style="text-align: center;">Qtd</th>
                    <th style="text-align: right;">Valor</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venda->itens as $item)
                <tr>
                    <td>{{ $item->produto ? $item->produto->nome : 'Produto não encontrado' }}</td>
                    <td style="text-align: center;">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                    <td style="text-align: right;">{{ $item->valor_unitario_formatado }}</td>
                    <td style="text-align: right;">{{ $item->valor_total_formatado }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <hr>
        <p style="text-align: right;">
            <strong>Subtotal: {{ $venda->valor_total_formatado }}</strong><br>
            @if($venda->valor_desconto > 0)
            Desconto: R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}<br>
            @endif
            <strong>Total: {{ $venda->valor_liquido_formatado }}</strong>
        </p>
        
        @if($venda->observacoes)
        <p><strong>Observações:</strong> {{ $venda->observacoes }}</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function cancelarVenda() {
    $('#cancelarVendaModal').modal('show');
}

function imprimirVenda() {
    const conteudo = document.getElementById('impressaoVenda').innerHTML;
    const janela = window.open('', '_blank');
    janela.document.write(`
        <html>
            <head>
                <title>Venda #{{ $venda->numero_venda }}</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 14px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { padding: 5px; }
                    hr { border: 1px solid #ccc; }
                </style>
            </head>
            <body>${conteudo}</body>
        </html>
    `);
    janela.document.close();
    janela.print();
}

function imprimirRelatorio() {
    window.print();
}

@if($venda->cliente && $venda->cliente->email)
function enviarPorEmail() {
    // Implementar envio por email
    alert('Funcionalidade de envio por email será implementada em breve.');
}
@endif

@if($venda->cliente && $venda->cliente->telefone)
function enviarWhatsApp() {
    const telefone = '{{ $venda->cliente->telefone }}';
    const mensagem = `Olá! Segue o resumo da sua compra:\n\nVenda #{{ $venda->numero_venda }}\nData: {{ $venda->data_venda->format('d/m/Y H:i') }}\nTotal: {{ $venda->valor_liquido_formatado }}\n\nObrigado pela preferência!`;
    const url = `https://wa.me/${telefone.replace(/\D/g, '')}?text=${encodeURIComponent(mensagem)}`;
    window.open(url, '_blank');
}
@endif
</script>
@endpush

@push('styles')
<style>
@media print {
    .card-header,
    .btn,
    .navbar,
    .sidebar {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
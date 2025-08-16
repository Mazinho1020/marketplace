@extends('comerciantes.layout')

@section('title', 'Venda #' . $venda->numero_venda)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Venda #{{ $venda->numero_venda }}
                    </h1>
                    <p class="text-muted mb-0">Detalhes da venda realizada em {{ $venda->data_venda->format('d/m/Y \à\s H:i') }}</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.vendas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Voltar à Lista
                    </a>
                    @if(!$venda->isCancelada() && $venda->status_venda !== 'finalizada')
                        <a href="{{ route('comerciantes.vendas.edit', $venda->id) }}" class="btn btn-warning ms-2">
                            <i class="fas fa-edit me-1"></i>
                            Editar
                        </a>
                    @endif
                    <a href="{{ route('comerciantes.vendas.imprimir', $venda->id) }}" class="btn btn-primary ms-2" target="_blank">
                        <i class="fas fa-print me-1"></i>
                        Imprimir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status e Ações -->
    @if($venda->status_venda === 'pendente')
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Venda Pendente:</strong> Esta venda ainda não foi confirmada. Clique em "Confirmar Venda" para baixar o estoque.
                    </div>
                    <button type="button" class="btn btn-success" onclick="confirmarVenda()">
                        <i class="fas fa-check me-1"></i>
                        Confirmar Venda
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Informações Principais -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações da Venda
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Número da Venda:</strong></td>
                                    <td>#{{ $venda->numero_venda }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Data da Venda:</strong></td>
                                    <td>{{ $venda->data_venda->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Venda:</strong></td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($venda->tipo_venda) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Origem:</strong></td>
                                    <td><span class="badge bg-info">{{ ucfirst($venda->origem) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Vendedor:</strong></td>
                                    <td>{{ $venda->usuario->name ?? 'N/A' }}</td>
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
                                            <span class="text-muted">Cliente Avulso</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status da Venda:</strong></td>
                                    <td>{!! $venda->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status do Pagamento:</strong></td>
                                    <td>
                                        @switch($venda->status_pagamento)
                                            @case('pago')
                                                <span class="badge bg-success">Pago</span>
                                                @break
                                            @case('parcial')
                                                <span class="badge bg-warning">Parcial</span>
                                                @break
                                            @case('estornado')
                                                <span class="badge bg-danger">Estornado</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Pendente</span>
                                        @endswitch
                                    </td>
                                </tr>
                                @if($venda->data_entrega_prevista)
                                    <tr>
                                        <td><strong>Entrega Prevista:</strong></td>
                                        <td>{{ $venda->data_entrega_prevista->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if($venda->cupom_desconto)
                                    <tr>
                                        <td><strong>Cupom Utilizado:</strong></td>
                                        <td><code>{{ $venda->cupom_desconto }}</code></td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($venda->observacoes)
                        <div class="mt-3">
                            <strong>Observações:</strong>
                            <p class="text-muted mb-0">{{ $venda->observacoes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Resumo Financeiro -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Resumo Financeiro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>{{ $venda->subtotal_formatado }}</span>
                    </div>
                    @if($venda->desconto_valor > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Desconto ({{ number_format($venda->desconto_percentual, 1) }}%):</span>
                            <span>- R$ {{ number_format($venda->desconto_valor, 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($venda->acrescimo_valor > 0)
                        <div class="d-flex justify-content-between mb-2 text-warning">
                            <span>Acréscimo ({{ number_format($venda->acrescimo_percentual, 1) }}%):</span>
                            <span>+ R$ {{ number_format($venda->acrescimo_valor, 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($venda->total_impostos > 0)
                        <div class="d-flex justify-content-between mb-2 text-info">
                            <span>Impostos:</span>
                            <span>R$ {{ number_format($venda->total_impostos, 2, ',', '.') }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-success">{{ $venda->valor_total_formatado }}</strong>
                    </div>
                    
                    @if($venda->valor_comissao_marketplace > 0)
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Comissão Marketplace:</span>
                            <span>R$ {{ number_format($venda->valor_comissao_marketplace, 2, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Itens da Venda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>
                        Itens da Venda ({{ $venda->itens->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-end">Valor Unit.</th>
                                    <th class="text-center">Desconto</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Estoque</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venda->itens as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->produto && $item->produto->imagemPrincipal)
                                                    <img src="{{ $item->produto->url_imagem_principal }}" 
                                                         alt="{{ $item->nome_produto }}" 
                                                         class="me-3" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->nome_produto }}</strong>
                                                    @if($item->codigo_produto)
                                                        <br><small class="text-muted">Código: {{ $item->codigo_produto }}</small>
                                                    @endif
                                                    @if($item->observacoes)
                                                        <br><small class="text-info">{{ $item->observacoes }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->quantidade, 2, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            {{ $item->valor_unitario_formatado }}
                                        </td>
                                        <td class="text-center">
                                            @if($item->desconto_percentual > 0)
                                                <span class="badge bg-warning">{{ number_format($item->desconto_percentual, 1) }}%</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ $item->valor_total_formatado }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($item->estoque_baixado)
                                                <span class="badge bg-success" title="Estoque baixado em {{ $item->data_baixa_estoque?->format('d/m/Y H:i') }}">
                                                    <i class="fas fa-check"></i> Baixado
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Pendente
                                                </span>
                                            @endif
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

    <!-- Pagamentos -->
    @if($venda->pagamentos->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Pagamentos ({{ $venda->pagamentos->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Forma de Pagamento</th>
                                        <th>Data</th>
                                        <th class="text-center">Parcelas</th>
                                        <th class="text-end">Valor</th>
                                        <th class="text-end">Taxa</th>
                                        <th class="text-end">Líquido</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($venda->pagamentos as $pagamento)
                                        <tr>
                                            <td>
                                                {{ $pagamento->formaPagamento->nome ?? 'N/A' }}
                                                @if($pagamento->bandeira)
                                                    <br><small class="text-muted">{{ $pagamento->bandeira->nome }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $pagamento->data_pagamento->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">{{ $pagamento->parcelas }}x</td>
                                            <td class="text-end">{{ $pagamento->valor_pagamento_formatado }}</td>
                                            <td class="text-end">
                                                @if($pagamento->valor_taxa > 0)
                                                    R$ {{ number_format($pagamento->valor_taxa, 2, ',', '.') }}
                                                    <br><small class="text-muted">{{ number_format($pagamento->taxa_percentual, 2) }}%</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $pagamento->valor_liquido_formatado }}</td>
                                            <td>{!! $pagamento->status_badge !!}</td>
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

    <!-- Dados de Entrega -->
    @if($venda->dados_entrega && $venda->tipo_venda === 'delivery')
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-truck me-2"></i>
                            Dados de Entrega
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @if(isset($venda->dados_entrega['endereco']))
                                    <p><strong>Endereço:</strong> {{ $venda->dados_entrega['endereco'] }}</p>
                                @endif
                                @if(isset($venda->dados_entrega['bairro']))
                                    <p><strong>Bairro:</strong> {{ $venda->dados_entrega['bairro'] }}</p>
                                @endif
                                @if(isset($venda->dados_entrega['cidade']))
                                    <p><strong>Cidade:</strong> {{ $venda->dados_entrega['cidade'] }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if(isset($venda->dados_entrega['cep']))
                                    <p><strong>CEP:</strong> {{ $venda->dados_entrega['cep'] }}</p>
                                @endif
                                @if(isset($venda->dados_entrega['telefone']))
                                    <p><strong>Telefone:</strong> {{ $venda->dados_entrega['telefone'] }}</p>
                                @endif
                                @if(isset($venda->dados_entrega['observacoes']))
                                    <p><strong>Observações:</strong> {{ $venda->dados_entrega['observacoes'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Ações Adicionais -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($venda->status_venda === 'pendente')
                                <button type="button" class="btn btn-success me-2" onclick="confirmarVenda()">
                                    <i class="fas fa-check me-1"></i>
                                    Confirmar Venda
                                </button>
                            @endif
                            @if(!$venda->isCancelada())
                                <button type="button" class="btn btn-danger me-2" onclick="cancelarVenda()">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar Venda
                                </button>
                            @endif
                        </div>
                        <div>
                            <small class="text-muted">
                                Venda criada em {{ $venda->created_at->format('d/m/Y H:i') }}
                                @if($venda->updated_at != $venda->created_at)
                                    • Última atualização: {{ $venda->updated_at->format('d/m/Y H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarVenda() {
    if (confirm('Tem certeza que deseja confirmar esta venda? O estoque dos produtos será baixado.')) {
        fetch(`/comerciantes/vendas/{{ $venda->id }}/confirmar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao confirmar venda');
        });
    }
}

function cancelarVenda() {
    if (confirm('Tem certeza que deseja cancelar esta venda? Esta ação irá devolver o estoque dos produtos baixados.')) {
        fetch(`/comerciantes/vendas/{{ $venda->id }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.href = '/comerciantes/vendas';
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao cancelar venda');
        });
    }
}
</script>
@endpush
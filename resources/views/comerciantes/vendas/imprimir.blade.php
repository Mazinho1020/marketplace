<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupom Fiscal - Venda #{{ $venda->numero_venda }}</title>
    <style>
        @media print {
            body { 
                margin: 0; 
                padding: 0;
            }
            .no-print { 
                display: none; 
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            margin: 20px;
            background: white;
        }
        
        .cupom {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 10px;
        }
        
        .cabecalho {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .empresa-nome {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .linha {
            border-bottom: 1px dashed #333;
            margin: 10px 0;
            padding-bottom: 10px;
        }
        
        .item {
            margin-bottom: 8px;
        }
        
        .item-nome {
            font-weight: bold;
        }
        
        .item-detalhes {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .totais {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #333;
        }
        
        .total-linha {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .total-final {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .pagamentos {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #333;
        }
        
        .rodape {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #333;
            font-size: 10px;
        }
        
        .botoes {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
    </style>
</head>
<body>
    <div class="cupom">
        <!-- Cabeçalho -->
        <div class="cabecalho">
            <div class="empresa-nome">{{ $venda->empresa->nome ?? 'MARKETPLACE' }}</div>
            @if($venda->empresa->cnpj ?? false)
                <div>CNPJ: {{ $venda->empresa->cnpj }}</div>
            @endif
            @if($venda->empresa->endereco ?? false)
                <div>{{ $venda->empresa->endereco }}</div>
            @endif
            @if($venda->empresa->telefone ?? false)
                <div>Tel: {{ $venda->empresa->telefone }}</div>
            @endif
        </div>

        <!-- Informações da Venda -->
        <div class="linha">
            <div><strong>CUPOM FISCAL</strong></div>
            <div>Venda: #{{ $venda->numero_venda }}</div>
            <div>Data: {{ $venda->data_venda->format('d/m/Y H:i') }}</div>
            <div>Tipo: {{ ucfirst($venda->tipo_venda) }}</div>
            @if($venda->cliente)
                <div>Cliente: {{ $venda->cliente->nome }}</div>
                @if($venda->cliente->cpf_cnpj)
                    <div>CPF/CNPJ: {{ $venda->cliente->cpf_cnpj }}</div>
                @endif
            @else
                <div>Cliente: CONSUMIDOR</div>
            @endif
            <div>Vendedor: {{ $venda->usuario->name ?? 'N/A' }}</div>
        </div>

        <!-- Itens -->
        <div class="linha">
            <div><strong>ITENS:</strong></div>
            @foreach($venda->itens as $item)
                <div class="item">
                    <div class="item-nome">{{ $item->nome_produto }}</div>
                    <div class="item-detalhes">
                        <span>{{ number_format($item->quantidade, 2, ',', '.') }} x {{ number_format($item->valor_unitario, 2, ',', '.') }}</span>
                        <span>{{ number_format($item->valor_total_item, 2, ',', '.') }}</span>
                    </div>
                    @if($item->desconto_percentual > 0)
                        <div style="font-size: 10px; color: #666;">
                            Desconto: {{ number_format($item->desconto_percentual, 1) }}% 
                            (-R$ {{ number_format($item->desconto_valor, 2, ',', '.') }})
                        </div>
                    @endif
                    @if($item->observacoes)
                        <div style="font-size: 10px; font-style: italic;">{{ $item->observacoes }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Totais -->
        <div class="totais">
            <div class="total-linha">
                <span>Subtotal:</span>
                <span>R$ {{ number_format($venda->subtotal, 2, ',', '.') }}</span>
            </div>
            @if($venda->desconto_valor > 0)
                <div class="total-linha">
                    <span>Desconto ({{ number_format($venda->desconto_percentual, 1) }}%):</span>
                    <span>-R$ {{ number_format($venda->desconto_valor, 2, ',', '.') }}</span>
                </div>
            @endif
            @if($venda->acrescimo_valor > 0)
                <div class="total-linha">
                    <span>Acréscimo ({{ number_format($venda->acrescimo_percentual, 1) }}%):</span>
                    <span>+R$ {{ number_format($venda->acrescimo_valor, 2, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-final">
                <span>TOTAL:</span>
                <span>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Formas de Pagamento -->
        @if($venda->pagamentos->count() > 0)
            <div class="pagamentos">
                <div><strong>FORMAS DE PAGAMENTO:</strong></div>
                @foreach($venda->pagamentos as $pagamento)
                    <div class="total-linha">
                        <span>
                            {{ $pagamento->formaPagamento->nome ?? 'N/A' }}
                            @if($pagamento->parcelas > 1)
                                ({{ $pagamento->parcelas }}x)
                            @endif
                        </span>
                        <span>R$ {{ number_format($pagamento->valor_pagamento, 2, ',', '.') }}</span>
                    </div>
                    @if($pagamento->valor_taxa > 0)
                        <div style="font-size: 10px; color: #666;">
                            Taxa: R$ {{ number_format($pagamento->valor_taxa, 2, ',', '.') }}
                            ({{ number_format($pagamento->taxa_percentual, 2) }}%)
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Dados de Entrega -->
        @if($venda->dados_entrega && $venda->tipo_venda === 'delivery')
            <div class="linha">
                <div><strong>ENTREGA:</strong></div>
                @if(isset($venda->dados_entrega['endereco']))
                    <div>{{ $venda->dados_entrega['endereco'] }}</div>
                @endif
                @if(isset($venda->dados_entrega['bairro']))
                    <div>{{ $venda->dados_entrega['bairro'] }}</div>
                @endif
                @if(isset($venda->dados_entrega['cidade']))
                    <div>{{ $venda->dados_entrega['cidade'] }}</div>
                @endif
                @if(isset($venda->dados_entrega['cep']))
                    <div>CEP: {{ $venda->dados_entrega['cep'] }}</div>
                @endif
                @if(isset($venda->dados_entrega['telefone']))
                    <div>Tel: {{ $venda->dados_entrega['telefone'] }}</div>
                @endif
                @if($venda->data_entrega_prevista)
                    <div>Previsão: {{ $venda->data_entrega_prevista->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        @endif

        <!-- Observações -->
        @if($venda->observacoes)
            <div class="linha">
                <div><strong>OBSERVAÇÕES:</strong></div>
                <div>{{ $venda->observacoes }}</div>
            </div>
        @endif

        <!-- Rodapé -->
        <div class="rodape">
            <div>{{ now()->format('d/m/Y H:i:s') }}</div>
            <div>Status: {{ ucfirst($venda->status_venda) }}</div>
            @if($venda->nf_numero)
                <div>NF-e: {{ $venda->nf_numero }}</div>
            @endif
            <div style="margin-top: 10px;">
                <strong>Obrigado pela preferência!</strong>
            </div>
            <div>Sistema Marketplace</div>
        </div>
    </div>

    <!-- Botões (não aparece na impressão) -->
    <div class="botoes no-print">
        <button class="btn" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <a href="{{ route('comerciantes.vendas.show', $venda->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <script>
        // Auto-imprimir quando abrir em nova aba
        window.onload = function() {
            // Verifica se foi aberto em nova aba/janela
            if (window.opener) {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        };
    </script>
</body>
</html>
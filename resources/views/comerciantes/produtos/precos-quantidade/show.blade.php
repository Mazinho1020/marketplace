@extends('layouts.comerciante')

@section('title', 'Visualizar Regra de Preço - ' . $precoQuantidade->produto->nome)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">👁️ Visualizar Regra de Preço por Quantidade</h5>
                    <div>
                        <a href="{{ route('comerciantes.produtos.precos-quantidade.edit', $precoQuantidade->id) }}" class="btn btn-primary btn-sm">
                            ✏️ Editar
                        </a>
                        <a href="{{ route('comerciantes.produtos.precos-quantidade.por-produto', $precoQuantidade->produto->id) }}" class="btn btn-secondary btn-sm">
                            ← Voltar
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Informações do Produto -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Produto: {{ $precoQuantidade->produto->nome }}</h6>
                        <p class="mb-0">
                            <strong>SKU:</strong> {{ $precoQuantidade->produto->sku }} | 
                            <strong>Preço Base:</strong> R$ {{ number_format($precoQuantidade->produto->preco_venda, 2, ',', '.') }}
                        </p>
                    </div>

                    <!-- Detalhes da Regra -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    📊 Informações da Regra
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">ID:</th>
                                            <td>#{{ $precoQuantidade->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Variação:</th>
                                            <td>
                                                @if($precoQuantidade->variacao)
                                                    <span class="badge badge-secondary">{{ $precoQuantidade->variacao->nome }}</span>
                                                @else
                                                    <span class="text-muted">Produto base</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Quantidade Mínima:</th>
                                            <td><strong>{{ number_format($precoQuantidade->quantidade_minima, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Quantidade Máxima:</th>
                                            <td>
                                                @if($precoQuantidade->quantidade_maxima)
                                                    <strong>{{ number_format($precoQuantidade->quantidade_maxima, 0, ',', '.') }}</strong>
                                                @else
                                                    <span class="text-muted">Ilimitada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @if($precoQuantidade->ativo)
                                                    <span class="badge badge-success">Ativo</span>
                                                @else
                                                    <span class="badge badge-secondary">Inativo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    💰 Cálculos de Preço
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Preço Base:</th>
                                            <td><strong>R$ {{ number_format($precoQuantidade->preco, 2, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Desconto:</th>
                                            <td>
                                                @if($precoQuantidade->desconto_percentual > 0)
                                                    <span class="badge badge-warning">{{ number_format($precoQuantidade->desconto_percentual, 1) }}% OFF</span>
                                                @else
                                                    <span class="text-muted">Sem desconto</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Preço Final:</th>
                                            <td><strong class="text-success">R$ {{ number_format($precoQuantidade->preco_com_desconto, 2, ',', '.') }}</strong></td>
                                        </tr>
                                        @if($precoQuantidade->economia > 0)
                                        <tr>
                                            <th>Economia:</th>
                                            <td><strong class="text-success">R$ {{ number_format($precoQuantidade->economia, 2, ',', '.') }}</strong></td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Faixa de Aplicação -->
                    <div class="card mt-4">
                        <div class="card-header">
                            📏 Faixa de Aplicação
                        </div>
                        <div class="card-body">
                            <div class="alert alert-light">
                                <h6>Esta regra se aplica quando a quantidade estiver entre:</h6>
                                <h4 class="text-primary">
                                    {{ number_format($precoQuantidade->quantidade_minima, 0, ',', '.') }}
                                    @if($precoQuantidade->quantidade_maxima)
                                        até {{ number_format($precoQuantidade->quantidade_maxima, 0, ',', '.') }} unidades
                                    @else
                                        ou mais unidades
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Exemplo de Cálculo -->
                    <div class="card mt-4">
                        <div class="card-header">
                            🧮 Simulação de Compra
                        </div>
                        <div class="card-body">
                            <form id="simulacaoForm" class="row">
                                <div class="col-md-8">
                                    <label for="quantidade_sim">Quantidade para simular</label>
                                    <input type="number" id="quantidade_sim" class="form-control" 
                                           min="{{ $precoQuantidade->quantidade_minima }}" 
                                           max="{{ $precoQuantidade->quantidade_maxima ?? 999999 }}"
                                           value="{{ $precoQuantidade->quantidade_minima }}" 
                                           placeholder="Digite a quantidade">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" id="simularBtn" class="btn btn-primary w-100">
                                        🧮 Simular
                                    </button>
                                </div>
                            </form>
                            
                            <div id="resultadoSimulacao" class="mt-3">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Quantidade:</strong> {{ number_format($precoQuantidade->quantidade_minima, 0, ',', '.') }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Preço Unitário:</strong> R$ {{ number_format($precoQuantidade->preco_com_desconto, 2, ',', '.') }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total:</strong> <span class="text-success">R$ {{ number_format($precoQuantidade->quantidade_minima * $precoQuantidade->preco_com_desconto, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            @if($precoQuantidade->economia > 0)
                                                <strong>Economia Total:</strong> <span class="text-success">R$ {{ number_format($precoQuantidade->quantidade_minima * $precoQuantidade->economia, 2, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">Sem economia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('comerciantes.produtos.precos-quantidade.edit', $precoQuantidade->id) }}" class="btn btn-primary">
                                ✏️ Editar Regra
                            </a>
                            <button type="button" class="btn btn-outline-danger ml-2" onclick="removerPreco({{ $precoQuantidade->id }})">
                                🗑️ Remover Regra
                            </button>
                            <a href="{{ route('comerciantes.produtos.precos-quantidade.por-produto', $precoQuantidade->produto->id) }}" class="btn btn-secondary ml-2">
                                📋 Ver Todas as Regras
                            </a>
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
$(document).ready(function() {
    // Simulação de cálculo
    $('#simularBtn').click(function() {
        const quantidade = parseInt($('#quantidade_sim').val());
        const precoUnitario = {{ $precoQuantidade->preco_com_desconto }};
        const economia = {{ $precoQuantidade->economia }};
        const total = quantidade * precoUnitario;
        const economiaTotal = quantidade * economia;
        
        let html = `
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Quantidade:</strong> ${quantidade.toLocaleString('pt-BR')}
                    </div>
                    <div class="col-md-3">
                        <strong>Preço Unitário:</strong> R$ ${precoUnitario.toFixed(2).replace('.', ',')}
                    </div>
                    <div class="col-md-3">
                        <strong>Total:</strong> <span class="text-success">R$ ${total.toFixed(2).replace('.', ',')}</span>
                    </div>
                    <div class="col-md-3">
        `;
        
        if (economia > 0) {
            html += `<strong>Economia Total:</strong> <span class="text-success">R$ ${economiaTotal.toFixed(2).replace('.', ',')}</span>`;
        } else {
            html += `<span class="text-muted">Sem economia</span>`;
        }
        
        html += `
                    </div>
                </div>
            </div>
        `;
        
        $('#resultadoSimulacao').html(html);
    });
    
    // Atualizar simulação quando quantidade mudar
    $('#quantidade_sim').on('input', function() {
        $('#simularBtn').click();
    });
});

// Função para remover preço
function removerPreco(id) {
    if (confirm('Tem certeza que deseja remover esta regra de preço? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('comerciantes.produtos.precos-quantidade.destroy', '__ID__') }}`.replace('__ID__', id);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

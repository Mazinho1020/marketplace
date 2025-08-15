@extends('layouts.comerciante')

@section('title', $produto ? 'Preços por Quantidade - ' . $produto->nome : 'Preços por Quantidade')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if($produto)
                {{-- VIEW ESPECÍFICA DO PRODUTO --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>💰 Preços por Quantidade</h1>
                        <p class="text-muted mb-0">{{ $produto->nome }}</p>
                    </div>
                    <div>
                        <a href="{{ route('comerciantes.produtos.precos-quantidade.create') }}?produto_id={{ $produto->id }}" class="btn btn-success">
                            ➕ Nova Regra de Preço
                        </a>
                        <a href="{{ route('comerciantes.produtos.show', $produto->id) }}" class="btn btn-outline-primary">
                            👁️ Ver Produto
                        </a>
                        <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-secondary">
                            ← Voltar aos Produtos
                        </a>
                    </div>
                </div>

                <!-- Informações do Produto -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5>{{ $produto->nome }}</h5>
                                <p class="mb-1"><strong>SKU:</strong> {{ $produto->sku }}</p>
                                <p class="mb-1"><strong>Preço Base:</strong> R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}</p>
                                <p class="mb-0"><strong>Categoria:</strong> {{ $produto->categoria->nome ?? '-' }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="badge badge-primary badge-lg">
                                    {{ $precosQuantidade->total() }} regra(s) de preço
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calculadora de Preço -->
                <div class="card mb-4">
                    <div class="card-header">
                        🧮 Calculadora de Preço por Quantidade
                    </div>
                    <div class="card-body">
                        <form id="calculadoraForm" class="row">
                            <div class="col-md-4">
                                <label for="quantidade">Quantidade</label>
                                <input type="number" id="quantidade" class="form-control" min="1" placeholder="Digite a quantidade">
                            </div>
                            <div class="col-md-4">
                                <label for="variacao_calc">Variação (opcional)</label>
                                <select id="variacao_calc" class="form-control">
                                    <option value="">Produto base</option>
                                    @foreach($variacoes as $variacao)
                                        <option value="{{ $variacao->id }}">{{ $variacao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" id="calcularBtn" class="btn btn-primary w-100">
                                    🧮 Calcular Preço
                                </button>
                            </div>
                        </form>
                        
                        <div id="resultadoCalculo" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <h6>💡 Resultado do Cálculo:</h6>
                                <div id="detalhesCalculo"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- VIEW GERAL (LISTAGEM) --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>💰 Preços por Quantidade</h1>
                        <p class="text-muted mb-0">Todas as regras de preços por quantidade</p>
                    </div>
                    <div>
                        <a href="{{ route('comerciantes.produtos.precos-quantidade.create') }}" class="btn btn-success">
                            ➕ Nova Regra de Preço
                        </a>
                        <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-secondary">
                            ← Voltar aos Produtos
                        </a>
                    </div>
                </div>
            @endif

            @if($precosQuantidade->count() > 0)
                <!-- Lista de Preços por Quantidade -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Regras de Preço por Quantidade</h5>
                        <small class="text-muted">{{ $precosQuantidade->total() }} regra(s) cadastrada(s)</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        @unless($produto)
                                            <th>Produto</th>
                                        @endunless
                                        <th>Variação</th>
                                        <th>Faixa de Quantidade</th>
                                        <th>Preço</th>
                                        <th>Desconto</th>
                                        <th>Preço Final</th>
                                        <th>Status</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($precosQuantidade as $preco)
                                        <tr>
                                            @unless($produto)
                                                <td>
                                                    <strong>{{ $preco->produto->nome ?? 'N/A' }}</strong>
                                                    <br><small class="text-muted">{{ $preco->produto->sku ?? 'N/A' }}</small>
                                                </td>
                                            @endunless
                                            <td>
                                                @if($preco->variacao)
                                                    <span class="badge badge-secondary">{{ $preco->variacao->nome }}</span>
                                                @else
                                                    <span class="text-muted">Produto base</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ number_format($preco->quantidade_minima, 0, ',', '.') }}</strong>
                                                @if($preco->quantidade_maxima)
                                                    até <strong>{{ number_format($preco->quantidade_maxima, 0, ',', '.') }}</strong>
                                                @else
                                                    <span class="text-muted">ou mais</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>R$ {{ number_format($preco->preco, 2, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                @if($preco->desconto_percentual > 0)
                                                    <span class="badge badge-warning">
                                                        {{ number_format($preco->desconto_percentual, 1) }}% OFF
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    R$ {{ number_format($preco->preco_com_desconto, 2, ',', '.') }}
                                                </strong>
                                                @if($preco->economia > 0)
                                                    <br><small class="text-success">
                                                        Economia: R$ {{ number_format($preco->economia, 2, ',', '.') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($preco->ativo)
                                                    <span class="badge badge-success">Ativo</span>
                                                @else
                                                    <span class="badge badge-secondary">Inativo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('comerciantes.produtos.precos-quantidade.show', $preco->id) }}" 
                                                       class="btn btn-outline-info" title="Visualizar">
                                                        👁️
                                                    </a>
                                                    <a href="{{ route('comerciantes.produtos.precos-quantidade.edit', $preco->id) }}" 
                                                       class="btn btn-outline-primary" title="Editar">
                                                        ✏️
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="removerPreco({{ $preco->id }})" title="Remover">
                                                        🗑️
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($precosQuantidade->hasPages())
                        <div class="card-footer">
                            {{ $precosQuantidade->links() }}
                        </div>
                    @endif
                </div>
            @else
                <!-- Estado Vazio -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-chart-line fa-3x text-muted"></i>
                        </div>
                        @if($produto)
                            <h5>Nenhuma regra de preço por quantidade encontrada</h5>
                            <p class="text-muted">Crie regras de preço baseadas na quantidade para oferecer descontos progressivos.</p>
                            <a href="{{ route('comerciantes.produtos.precos-quantidade.create') }}?produto_id={{ $produto->id }}" class="btn btn-success">
                                ➕ Criar Primeira Regra
                            </a>
                        @else
                            <h5>Nenhuma regra de preço por quantidade encontrada</h5>
                            <p class="text-muted">Você ainda não criou regras de preço baseadas na quantidade.</p>
                            <a href="{{ route('comerciantes.produtos.precos-quantidade.create') }}" class="btn btn-success">
                                ➕ Criar Primeira Regra
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if($produto)
    // Calculadora de preço (apenas para produto específico)
    $('#calcularBtn').click(function() {
        const quantidade = $('#quantidade').val();
        const variacaoId = $('#variacao_calc').val();
        
        if (!quantidade || quantidade < 1) {
            alert('Digite uma quantidade válida');
            return;
        }
        
        $.ajax({
            url: '{{ route("comerciantes.produtos.precos-quantidade.calcular") }}',
            method: 'POST',
            data: {
                quantidade: quantidade,
                variacao_id: variacaoId,
                produto_id: {{ $produto->id }},
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let html = `
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Quantidade:</strong> ${quantidade}
                        </div>
                        <div class="col-md-3">
                            <strong>Preço Original:</strong> R$ ${parseFloat(response.preco_original).toFixed(2).replace('.', ',')}
                        </div>
                        <div class="col-md-3">
                            <strong>Preço Final:</strong> <span class="text-success">R$ ${parseFloat(response.preco_com_desconto).toFixed(2).replace('.', ',')}</span>
                        </div>
                        <div class="col-md-3">
                `;
                
                if (response.desconto_percentual > 0) {
                    html += `<strong>Desconto:</strong> <span class="text-warning">${response.desconto_percentual}%</span>`;
                } else {
                    html += `<span class="text-muted">Sem desconto</span>`;
                }
                
                html += `</div></div>`;
                
                if (response.faixa) {
                    html += `<div class="mt-2"><small class="text-muted">Faixa aplicável: ${response.faixa.minima} - ${response.faixa.maxima || '∞'}</small></div>`;
                }
                
                $('#detalhesCalculo').html(html);
                $('#resultadoCalculo').show();
            },
            error: function() {
                alert('Erro ao calcular preço');
            }
        });
    });
    
    // Enter no campo quantidade
    $('#quantidade').keypress(function(e) {
        if (e.which === 13) {
            $('#calcularBtn').click();
        }
    });
    @endif
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

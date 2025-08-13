@extends('comerciantes.layouts.app')

@section('title', $produto ? 'Nova Regra de Preço - ' . $produto->nome : 'Nova Regra de Preço por Quantidade')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">➕ Nova Regra de Preço por Quantidade</h5>
                    <div>
                        @if($produto)
                            <a href="{{ route('comerciantes.produtos.precos-quantidade.por-produto', $produto->id) }}" class="btn btn-secondary btn-sm">
                                ← Voltar
                            </a>
                        @else
                            <a href="{{ route('comerciantes.produtos.precos-quantidade.index') }}" class="btn btn-secondary btn-sm">
                                ← Voltar
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if($produto)
                        <!-- Informações do Produto -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Produto: {{ $produto->nome }}</h6>
                            <p class="mb-0">
                                <strong>SKU:</strong> {{ $produto->sku }} | 
                                <strong>Preço Base:</strong> R$ {{ number_format($produto->preco_venda, 2, ',', '.') }}
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('comerciantes.produtos.precos-quantidade.store') }}">
                        @csrf
                        
                        @if($produto)
                            <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                        @else
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="produto_id">Produto *</label>
                                    <select name="produto_id" id="produto_id" class="form-control @error('produto_id') is-invalid @enderror" required>
                                        <option value="">Selecione um produto...</option>
                                        @foreach($produtos as $produtoOpcao)
                                            <option value="{{ $produtoOpcao->id }}" {{ old('produto_id') == $produtoOpcao->id ? 'selected' : '' }}>
                                                {{ $produtoOpcao->nome }} ({{ $produtoOpcao->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('produto_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="variacao_id">Variação do Produto</label>
                                <select name="variacao_id" id="variacao_id" class="form-control @error('variacao_id') is-invalid @enderror">
                                    <option value="">Produto base (sem variação)</option>
                                    @if($produto)
                                        @foreach($variacoes as $variacao)
                                            <option value="{{ $variacao->id }}" {{ old('variacao_id') == $variacao->id ? 'selected' : '' }}>
                                                {{ $variacao->nome }}
                                                @if($variacao->preco_adicional)
                                                    (+ R$ {{ number_format($variacao->preco_adicional, 2, ',', '.') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('variacao_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Selecione uma variação específica ou deixe vazio para aplicar ao produto base.
                                </small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantidade_minima">Quantidade Mínima *</label>
                                <input type="number" name="quantidade_minima" id="quantidade_minima" 
                                       class="form-control @error('quantidade_minima') is-invalid @enderror" 
                                       value="{{ old('quantidade_minima') }}" min="1" step="1" required>
                                @error('quantidade_minima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Quantidade mínima para aplicar esta regra.
                                </small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="quantidade_maxima">Quantidade Máxima</label>
                                <input type="number" name="quantidade_maxima" id="quantidade_maxima" 
                                       class="form-control @error('quantidade_maxima') is-invalid @enderror" 
                                       value="{{ old('quantidade_maxima') }}" min="1" step="1">
                                @error('quantidade_maxima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Quantidade máxima (deixe vazio para "ou mais").
                                </small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="preco">Preço *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">R$</span>
                                    </div>
                                    <input type="text" name="preco" id="preco" 
                                           class="form-control money-mask @error('preco') is-invalid @enderror" 
                                           value="{{ old('preco') }}" placeholder="0,00" required>
                                    @error('preco')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Preço unitário para esta faixa de quantidade.
                                </small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="desconto_percentual">Desconto Adicional (%)</label>
                                <div class="input-group">
                                    <input type="number" name="desconto_percentual" id="desconto_percentual" 
                                           class="form-control @error('desconto_percentual') is-invalid @enderror" 
                                           value="{{ old('desconto_percentual', 0) }}" min="0" max="100" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('desconto_percentual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Desconto percentual adicional sobre o preço informado.
                                </small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="ativo" value="0">
                                    <input type="checkbox" name="ativo" id="ativo" class="custom-control-input" 
                                           value="1" {{ old('ativo', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ativo">
                                        Regra ativa
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Desmarque para manter a regra inativa temporariamente.
                                </small>
                            </div>
                        </div>
                        
                        <!-- Preview do cálculo -->
                        <div id="previewCalculo" class="alert alert-light" style="display: none;">
                            <h6>📊 Preview do Cálculo:</h6>
                            <div id="detalhesPreview"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">
                                    💾 Salvar Regra de Preço
                                </button>
                                @if($produto)
                                    <a href="{{ route('comerciantes.produtos.precos-quantidade.por-produto', $produto->id) }}" class="btn btn-secondary ml-2">
                                        Cancelar
                                    </a>
                                @else
                                    <a href="{{ route('comerciantes.produtos.precos-quantidade.index') }}" class="btn btn-secondary ml-2">
                                        Cancelar
                                    </a>
                                @endif
                                <button type="button" id="previewBtn" class="btn btn-info ml-2">
                                    👁️ Preview
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    // Máscara para valores monetários
    $('.money-mask').mask('#.##0,00', {
        reverse: true,
        translation: {
            '#': {pattern: /[0-9]/}
        }
    });
    
    @unless($produto)
    // Carregar variações quando produto for selecionado
    $('#produto_id').change(function() {
        const produtoId = $(this).val();
        const variacaoSelect = $('#variacao_id');
        
        // Limpar variações
        variacaoSelect.html('<option value="">Produto base (sem variação)</option>');
        
        if (produtoId) {
            $.ajax({
                url: '{{ route("comerciantes.produtos.precos-quantidade.index") }}',
                method: 'GET',
                data: { produto_id: produtoId, ajax: 1 },
                success: function(response) {
                    if (response.variacoes) {
                        response.variacoes.forEach(function(variacao) {
                            const precoAdicional = variacao.preco_adicional ? 
                                ` (+ R$ ${parseFloat(variacao.preco_adicional).toFixed(2).replace('.', ',')})` : '';
                            variacaoSelect.append(
                                `<option value="${variacao.id}">${variacao.nome}${precoAdicional}</option>`
                            );
                        });
                    }
                },
                error: function() {
                    console.error('Erro ao carregar variações');
                }
            });
        }
    });
    @endunless
    
    // Validação de quantidade máxima
    $('#quantidade_maxima').on('input', function() {
        const minima = parseFloat($('#quantidade_minima').val()) || 0;
        const maxima = parseFloat($(this).val()) || 0;
        
        if (maxima > 0 && maxima <= minima) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">A quantidade máxima deve ser maior que a mínima.</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });
    
    // Preview do cálculo
    $('#previewBtn').click(function() {
        const minima = $('#quantidade_minima').val();
        const maxima = $('#quantidade_maxima').val();
        const preco = $('#preco').val();
        const desconto = $('#desconto_percentual').val() || 0;
        
        if (!minima || !preco) {
            alert('Preencha pelo menos a quantidade mínima e o preço');
            return;
        }
        
        // Converter preço para número
        const precoNumerico = parseFloat(preco.replace(/\./g, '').replace(',', '.'));
        const precoComDesconto = precoNumerico * (1 - (desconto / 100));
        const economia = precoNumerico - precoComDesconto;
        
        let html = `
            <div class="row">
                <div class="col-md-4">
                    <strong>Faixa:</strong> ${minima}${maxima ? ' - ' + maxima : ' ou mais'}
                </div>
                <div class="col-md-4">
                    <strong>Preço Original:</strong> R$ ${precoNumerico.toFixed(2).replace('.', ',')}
                </div>
                <div class="col-md-4">
                    <strong>Preço Final:</strong> <span class="text-success">R$ ${precoComDesconto.toFixed(2).replace('.', ',')}</span>
                </div>
            </div>
        `;
        
        if (desconto > 0) {
            html += `
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Desconto:</strong> <span class="text-warning">${desconto}%</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Economia:</strong> <span class="text-success">R$ ${economia.toFixed(2).replace('.', ',')}</span>
                    </div>
                </div>
            `;
        }
        
        $('#detalhesPreview').html(html);
        $('#previewCalculo').show();
    });
    
    // Atualizar preview automaticamente
    $('input').on('input', function() {
        $('#previewCalculo').hide();
    });
});
</script>
@endpush

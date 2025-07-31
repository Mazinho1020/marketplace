@extends('layouts.app')

@section('title', 'Novo Cupom de Fidelidade')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-ticket-alt text-primary"></i>
                        Novo Cupom de Fidelidade
                    </h1>
                    <p class="text-muted mb-0">Criar um novo cupom de desconto</p>
                </div>
                <a href="{{ route('fidelidade.cupons.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-plus"></i>
                                Dados do Cupom
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fidelidade.cupons.store') }}" method="POST">
                                @csrf

                                <!-- Informações Básicas -->
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-info-circle"></i> Informações Básicas
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="titulo" class="form-label">
                                            <i class="fas fa-tag"></i> Título *
                                        </label>
                                        <input type="text" class="form-control @error('titulo') is-invalid @enderror"
                                            id="titulo" name="titulo" value="{{ old('titulo') }}"
                                            placeholder="Ex: Desconto de Boas-Vindas" required>
                                        @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="codigo" class="form-label">
                                            <i class="fas fa-qrcode"></i> Código (Opcional)
                                        </label>
                                        <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                            id="codigo" name="codigo" value="{{ old('codigo') }}"
                                            placeholder="Deixe vazio para gerar automaticamente">
                                        @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Se deixar vazio, será gerado automaticamente</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descricao" class="form-label">
                                        <i class="fas fa-align-left"></i> Descrição *
                                    </label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                        id="descricao" name="descricao" rows="3" required
                                        placeholder="Descreva o cupom e suas condições...">{{ old('descricao') }}</textarea>
                                    @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Configurações de Desconto -->
                                <h6 class="text-muted mt-4 mb-3">
                                    <i class="fas fa-percent"></i> Configurações de Desconto
                                </h6>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="tipo_desconto" class="form-label">
                                            <i class="fas fa-calculator"></i> Tipo de Desconto *
                                        </label>
                                        <select class="form-select @error('tipo_desconto') is-invalid @enderror"
                                            id="tipo_desconto" name="tipo_desconto" required>
                                            <option value="">Selecione</option>
                                            <option value="percentual" {{ old('tipo_desconto')=='percentual'
                                                ? 'selected' : '' }}>
                                                Percentual (%)
                                            </option>
                                            <option value="valor_fixo" {{ old('tipo_desconto')=='valor_fixo'
                                                ? 'selected' : '' }}>
                                                Valor Fixo (R$)
                                            </option>
                                        </select>
                                        @error('tipo_desconto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="valor_desconto" class="form-label">
                                            <i class="fas fa-money-bill-wave"></i> Valor do Desconto *
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="prefixo-desconto">%</span>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('valor_desconto') is-invalid @enderror"
                                                id="valor_desconto" name="valor_desconto"
                                                value="{{ old('valor_desconto') }}" required>
                                        </div>
                                        @error('valor_desconto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="valor_minimo_compra" class="form-label">
                                            <i class="fas fa-shopping-cart"></i> Valor Mínimo (Opcional)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('valor_minimo_compra') is-invalid @enderror"
                                                id="valor_minimo_compra" name="valor_minimo_compra"
                                                value="{{ old('valor_minimo_compra') }}" placeholder="0,00">
                                        </div>
                                        @error('valor_minimo_compra')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Limites e Validade -->
                                <h6 class="text-muted mt-4 mb-3">
                                    <i class="fas fa-clock"></i> Limites e Validade
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_inicio" class="form-label">
                                            <i class="fas fa-calendar-alt"></i> Data de Início *
                                        </label>
                                        <input type="datetime-local"
                                            class="form-control @error('data_inicio') is-invalid @enderror"
                                            id="data_inicio" name="data_inicio" value="{{ old('data_inicio') }}"
                                            required>
                                        @error('data_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="data_validade" class="form-label">
                                            <i class="fas fa-calendar-times"></i> Data de Validade *
                                        </label>
                                        <input type="datetime-local"
                                            class="form-control @error('data_validade') is-invalid @enderror"
                                            id="data_validade" name="data_validade" value="{{ old('data_validade') }}"
                                            required>
                                        @error('data_validade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="quantidade_maxima" class="form-label">
                                            <i class="fas fa-hashtag"></i> Quantidade Máxima
                                        </label>
                                        <input type="number" min="1"
                                            class="form-control @error('quantidade_maxima') is-invalid @enderror"
                                            id="quantidade_maxima" name="quantidade_maxima"
                                            value="{{ old('quantidade_maxima') }}" placeholder="Ilimitado">
                                        @error('quantidade_maxima')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Deixe vazio para ilimitado</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="limite_uso_cliente" class="form-label">
                                            <i class="fas fa-user-check"></i> Limite por Cliente
                                        </label>
                                        <input type="number" min="1"
                                            class="form-control @error('limite_uso_cliente') is-invalid @enderror"
                                            id="limite_uso_cliente" name="limite_uso_cliente"
                                            value="{{ old('limite_uso_cliente') }}" placeholder="Ilimitado">
                                        @error('limite_uso_cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="empresa_id" class="form-label">
                                            <i class="fas fa-building"></i> Empresa (Opcional)
                                        </label>
                                        <select class="form-select @error('empresa_id') is-invalid @enderror"
                                            id="empresa_id" name="empresa_id">
                                            <option value="">Todas as empresas</option>
                                            <option value="1" {{ old('empresa_id')=='1' ? 'selected' : '' }}>Empresa
                                                Principal</option>
                                            <option value="2" {{ old('empresa_id')=='2' ? 'selected' : '' }}>Filial 1
                                            </option>
                                            <option value="3" {{ old('empresa_id')=='3' ? 'selected' : '' }}>Filial 2
                                            </option>
                                        </select>
                                        @error('empresa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Configurações Avançadas -->
                                <h6 class="text-muted mt-4 mb-3">
                                    <i class="fas fa-cogs"></i> Configurações Avançadas
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nivel_minimo_cliente" class="form-label">
                                            <i class="fas fa-medal"></i> Nível Mínimo do Cliente
                                        </label>
                                        <select class="form-select @error('nivel_minimo_cliente') is-invalid @enderror"
                                            id="nivel_minimo_cliente" name="nivel_minimo_cliente">
                                            <option value="">Qualquer nível</option>
                                            <option value="bronze" {{ old('nivel_minimo_cliente')=='bronze' ? 'selected'
                                                : '' }}>Bronze</option>
                                            <option value="prata" {{ old('nivel_minimo_cliente')=='prata' ? 'selected'
                                                : '' }}>Prata</option>
                                            <option value="ouro" {{ old('nivel_minimo_cliente')=='ouro' ? 'selected'
                                                : '' }}>Ouro</option>
                                            <option value="diamond" {{ old('nivel_minimo_cliente')=='diamond'
                                                ? 'selected' : '' }}>Diamond</option>
                                        </select>
                                        @error('nivel_minimo_cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="primeira_compra_apenas"
                                                name="primeira_compra_apenas" value="1" {{ old('primeira_compra_apenas')
                                                ? 'checked' : '' }}>
                                            <label class="form-check-label" for="primeira_compra_apenas">
                                                <i class="fas fa-star"></i> Apenas para primeira compra
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="acumulativo_cashback"
                                                name="acumulativo_cashback" value="1" {{ old('acumulativo_cashback')
                                                ? 'checked' : '' }}>
                                            <label class="form-check-label" for="acumulativo_cashback">
                                                <i class="fas fa-plus-circle"></i> Acumular com cashback
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('fidelidade.cupons.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Criar Cupom
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Dicas -->
                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb"></i>
                                Dicas para Cupons
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-percentage"></i> Tipos de Desconto</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Percentual:</strong> Ex: 10% de desconto</li>
                                    <li><strong>Valor Fixo:</strong> Ex: R$ 25,00 de desconto</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-clock"></i> Configurações de Tempo</h6>
                                <ul class="mb-0 small">
                                    <li>Data de início deve ser anterior à validade</li>
                                    <li>Configure horários específicos se necessário</li>
                                    <li>Considere fusos horários dos clientes</li>
                                </ul>
                            </div>

                            <div class="alert alert-success">
                                <h6><i class="fas fa-users"></i> Segmentação</h6>
                                <ul class="mb-0 small">
                                    <li>Use níveis mínimos para recompensar fidelidade</li>
                                    <li>Cupons para primeira compra atraem novos clientes</li>
                                    <li>Limite por cliente evita abuso</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Preview do Cupom -->
                    <div class="card shadow mt-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye"></i>
                                Preview do Cupom
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="border rounded p-3 bg-light">
                                <h5 class="text-primary" id="preview-titulo">Título do Cupom</h5>
                                <div class="bg-primary text-white rounded p-2 my-2">
                                    <h4 class="mb-0" id="preview-codigo">CODIGO123</h4>
                                </div>
                                <p class="small text-muted" id="preview-desconto">Desconto de X%</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    Válido até: <span id="preview-validade">--/--/----</span>
                                </small>
                            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
    // Alterar prefixo do desconto baseado no tipo
    const tipoDesconto = document.getElementById('tipo_desconto');
    const prefixoDesconto = document.getElementById('prefixo-desconto');
    
    tipoDesconto.addEventListener('change', function() {
        if (this.value === 'percentual') {
            prefixoDesconto.textContent = '%';
        } else if (this.value === 'valor_fixo') {
            prefixoDesconto.textContent = 'R$';
        }
        updatePreview();
    });

    // Preview dinâmico do cupom
    function updatePreview() {
        const titulo = document.getElementById('titulo').value || 'Título do Cupom';
        const codigo = document.getElementById('codigo').value || 'CODIGO123';
        const tipo = document.getElementById('tipo_desconto').value;
        const valor = document.getElementById('valor_desconto').value || 'X';
        const validade = document.getElementById('data_validade').value;
        
        document.getElementById('preview-titulo').textContent = titulo;
        document.getElementById('preview-codigo').textContent = codigo;
        
        let desconto = '';
        if (tipo === 'percentual') {
            desconto = `${valor}% de desconto`;
        } else if (tipo === 'valor_fixo') {
            desconto = `R$ ${valor} de desconto`;
        } else {
            desconto = 'Desconto de X%';
        }
        document.getElementById('preview-desconto').textContent = desconto;
        
        if (validade) {
            const data = new Date(validade);
            const dataFormatada = data.toLocaleDateString('pt-BR');
            document.getElementById('preview-validade').textContent = dataFormatada;
        }
    }

    // Atualizar preview em tempo real
    document.getElementById('titulo').addEventListener('input', updatePreview);
    document.getElementById('codigo').addEventListener('input', updatePreview);
    document.getElementById('valor_desconto').addEventListener('input', updatePreview);
    document.getElementById('data_validade').addEventListener('change', updatePreview);
    
    // Gerar código automático se campo estiver vazio
    document.getElementById('titulo').addEventListener('blur', function() {
        const codigoField = document.getElementById('codigo');
        if (!codigoField.value && this.value) {
            const codigo = this.value.toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 10) + Math.floor(Math.random() * 1000);
            codigoField.value = codigo;
            updatePreview();
        }
    });
});
</script>
@endpush
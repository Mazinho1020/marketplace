@extends('layouts.app')

@section('title', 'Editar Cupom - ' . $cupom->titulo)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-warning"></i>
                        Editar Cupom de Fidelidade
                    </h1>
                    <p class="text-muted mb-0">Editando: <strong>{{ $cupom->titulo }}</strong> ({{ $cupom->codigo }})
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('fidelidade.cupons.show', $cupom->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Cupom
                    </a>
                    <a href="{{ route('fidelidade.cupons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-edit"></i>
                                Editar Dados do Cupom
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fidelidade.cupons.update', $cupom->id) }}" method="POST">
                                @csrf
                                @method('PUT')

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
                                            id="titulo" name="titulo" value="{{ old('titulo', $cupom->titulo) }}"
                                            required>
                                        @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="codigo" class="form-label">
                                            <i class="fas fa-qrcode"></i> Código
                                        </label>
                                        <input type="text" class="form-control" value="{{ $cupom->codigo }}" readonly>
                                        <small class="text-muted">O código não pode ser alterado após a criação</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descricao" class="form-label">
                                        <i class="fas fa-align-left"></i> Descrição *
                                    </label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                        id="descricao" name="descricao" rows="3"
                                        required>{{ old('descricao', $cupom->descricao) }}</textarea>
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
                                            <option value="percentual" {{ old('tipo_desconto', $cupom->tipo_desconto) ==
                                                'percentual' ? 'selected' : '' }}>
                                                Percentual (%)
                                            </option>
                                            <option value="valor_fixo" {{ old('tipo_desconto', $cupom->tipo_desconto) ==
                                                'valor_fixo' ? 'selected' : '' }}>
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
                                            <span class="input-group-text" id="prefixo-desconto">
                                                {{ $cupom->tipo_desconto === 'percentual' ? '%' : 'R$' }}
                                            </span>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('valor_desconto') is-invalid @enderror"
                                                id="valor_desconto" name="valor_desconto"
                                                value="{{ old('valor_desconto', $cupom->valor_desconto) }}" required>
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
                                                value="{{ old('valor_minimo_compra', $cupom->valor_minimo_compra) }}">
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
                                            id="data_inicio" name="data_inicio"
                                            value="{{ old('data_inicio', \Carbon\Carbon::parse($cupom->data_inicio)->format('Y-m-d\TH:i')) }}"
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
                                            id="data_validade" name="data_validade"
                                            value="{{ old('data_validade', \Carbon\Carbon::parse($cupom->data_validade)->format('Y-m-d\TH:i')) }}"
                                            required>
                                        @error('data_validade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="quantidade_maxima" class="form-label">
                                            <i class="fas fa-hashtag"></i> Quantidade Máxima
                                        </label>
                                        <input type="number" min="1"
                                            class="form-control @error('quantidade_maxima') is-invalid @enderror"
                                            id="quantidade_maxima" name="quantidade_maxima"
                                            value="{{ old('quantidade_maxima', $cupom->quantidade_maxima) }}">
                                        @error('quantidade_maxima')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="limite_uso_cliente" class="form-label">
                                            <i class="fas fa-user-check"></i> Limite por Cliente
                                        </label>
                                        <input type="number" min="1"
                                            class="form-control @error('limite_uso_cliente') is-invalid @enderror"
                                            id="limite_uso_cliente" name="limite_uso_cliente"
                                            value="{{ old('limite_uso_cliente', $cupom->limite_uso_cliente) }}">
                                        @error('limite_uso_cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="empresa_id" class="form-label">
                                            <i class="fas fa-building"></i> Empresa
                                        </label>
                                        <select class="form-select @error('empresa_id') is-invalid @enderror"
                                            id="empresa_id" name="empresa_id">
                                            <option value="">Todas as empresas</option>
                                            <option value="1" {{ old('empresa_id', $cupom->empresa_id) == '1' ?
                                                'selected' : '' }}>Empresa Principal</option>
                                            <option value="2" {{ old('empresa_id', $cupom->empresa_id) == '2' ?
                                                'selected' : '' }}>Filial 1</option>
                                            <option value="3" {{ old('empresa_id', $cupom->empresa_id) == '3' ?
                                                'selected' : '' }}>Filial 2</option>
                                        </select>
                                        @error('empresa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="status" class="form-label">
                                            <i class="fas fa-toggle-on"></i> Status *
                                        </label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="ativo" {{ old('status', $cupom->status) == 'ativo' ?
                                                'selected' : '' }}>
                                                Ativo
                                            </option>
                                            <option value="inativo" {{ old('status', $cupom->status) == 'inativo' ?
                                                'selected' : '' }}>
                                                Inativo
                                            </option>
                                            <option value="expirado" {{ old('status', $cupom->status) == 'expirado' ?
                                                'selected' : '' }}>
                                                Expirado
                                            </option>
                                        </select>
                                        @error('status')
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
                                            <option value="bronze" {{ old('nivel_minimo_cliente', $cupom->
                                                nivel_minimo_cliente) == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                            <option value="prata" {{ old('nivel_minimo_cliente', $cupom->
                                                nivel_minimo_cliente) == 'prata' ? 'selected' : '' }}>Prata</option>
                                            <option value="ouro" {{ old('nivel_minimo_cliente', $cupom->
                                                nivel_minimo_cliente) == 'ouro' ? 'selected' : '' }}>Ouro</option>
                                            <option value="diamond" {{ old('nivel_minimo_cliente', $cupom->
                                                nivel_minimo_cliente) == 'diamond' ? 'selected' : '' }}>Diamond</option>
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
                                                name="primeira_compra_apenas" value="1" {{ old('primeira_compra_apenas',
                                                $cupom->primeira_compra_apenas) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="primeira_compra_apenas">
                                                <i class="fas fa-star"></i> Apenas para primeira compra
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="acumulativo_cashback"
                                                name="acumulativo_cashback" value="1" {{ old('acumulativo_cashback',
                                                $cupom->acumulativo_cashback) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="acumulativo_cashback">
                                                <i class="fas fa-plus-circle"></i> Acumular com cashback
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informações de Uso -->
                                <div class="alert alert-info mt-4">
                                    <h6><i class="fas fa-chart-bar"></i> Estatísticas de Uso</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Quantidade Utilizada:</strong><br>
                                            {{ number_format($cupom->quantidade_utilizada ?? 0) }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Criado em:</strong><br>
                                            {{ $cupom->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Última atualização:</strong><br>
                                            {{ $cupom->updated_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('fidelidade.cupons.show', $cupom->id) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Preview -->
                <div class="col-lg-4">
                    <!-- Preview do Cupom -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye"></i>
                                Preview do Cupom
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="border rounded p-3 bg-light">
                                <h5 class="text-primary" id="preview-titulo">{{ $cupom->titulo }}</h5>
                                <div class="bg-primary text-white rounded p-2 my-2">
                                    <h4 class="mb-0">{{ $cupom->codigo }}</h4>
                                </div>
                                <p class="small text-muted" id="preview-desconto">
                                    @if($cupom->tipo_desconto === 'percentual')
                                    {{ number_format($cupom->valor_desconto, 1) }}% de desconto
                                    @else
                                    R$ {{ number_format($cupom->valor_desconto, 2, ',', '.') }} de desconto
                                    @endif
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    Válido até: <span id="preview-validade">{{
                                        \Carbon\Carbon::parse($cupom->data_validade)->format('d/m/Y') }}</span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-tools"></i>
                                Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('fidelidade.cupons.show', $cupom->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </a>

                                <button type="button" class="btn btn-primary" onclick="copiarCodigo()">
                                    <i class="fas fa-copy"></i> Copiar Código
                                </button>

                                @if($cupom->status === 'ativo')
                                <form action="{{ route('fidelidade.cupons.update', $cupom->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="inativo">
                                    <button type="submit" class="btn btn-secondary w-100"
                                        onclick="return confirm('Deseja desativar este cupom?')">
                                        <i class="fas fa-pause"></i> Desativar
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('fidelidade.cupons.update', $cupom->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="ativo">
                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Deseja ativar este cupom?')">
                                        <i class="fas fa-play"></i> Ativar
                                    </button>
                                </form>
                                @endif
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
        const titulo = document.getElementById('titulo').value;
        const tipo = document.getElementById('tipo_desconto').value;
        const valor = document.getElementById('valor_desconto').value;
        const validade = document.getElementById('data_validade').value;
        
        if (titulo) {
            document.getElementById('preview-titulo').textContent = titulo;
        }
        
        let desconto = '';
        if (tipo === 'percentual' && valor) {
            desconto = `${valor}% de desconto`;
        } else if (tipo === 'valor_fixo' && valor) {
            desconto = `R$ ${valor} de desconto`;
        }
        
        if (desconto) {
            document.getElementById('preview-desconto').textContent = desconto;
        }
        
        if (validade) {
            const data = new Date(validade);
            const dataFormatada = data.toLocaleDateString('pt-BR');
            document.getElementById('preview-validade').textContent = dataFormatada;
        }
    }

    // Atualizar preview em tempo real
    document.getElementById('titulo').addEventListener('input', updatePreview);
    document.getElementById('valor_desconto').addEventListener('input', updatePreview);
    document.getElementById('data_validade').addEventListener('change', updatePreview);
});

function copiarCodigo() {
    const codigo = '{{ $cupom->codigo }}';
    navigator.clipboard.writeText(codigo).then(function() {
        alert('Código copiado para a área de transferência!');
    }, function(err) {
        console.error('Erro ao copiar código: ', err);
        alert('Erro ao copiar código');
    });
}
</script>
@endpush
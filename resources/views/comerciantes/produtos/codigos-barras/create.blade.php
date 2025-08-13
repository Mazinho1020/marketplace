@extends('layouts.comerciante')

@section('title', 'Novo Código de Barras')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Novo Código de Barras</h1>
                    <p class="text-muted">Adicione um novo código de barras para um produto</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.produtos.codigos-barras.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-barcode me-2"></i>Dados do Código de Barras
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.produtos.codigos-barras.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="produto_id" class="form-label">Produto <span class="text-danger">*</span></label>
                                    <select name="produto_id" id="produto_id" class="form-select @error('produto_id') is-invalid @enderror" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            <option value="{{ $produto->id }}" {{ old('produto_id') == $produto->id ? 'selected' : '' }}>
                                                {{ $produto->nome }} - {{ $produto->sku }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('produto_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codigo" class="form-label">Código de Barras <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="codigo" 
                                           id="codigo" 
                                           class="form-control @error('codigo') is-invalid @enderror" 
                                           value="{{ old('codigo') }}" 
                                           placeholder="Digite ou escaneie o código de barras"
                                           required>
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Código</label>
                                    <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror">
                                        <option value="ean13" {{ old('tipo', 'ean13') == 'ean13' ? 'selected' : '' }}>EAN-13</option>
                                        <option value="ean8" {{ old('tipo') == 'ean8' ? 'selected' : '' }}>EAN-8</option>
                                        <option value="upca" {{ old('tipo') == 'upca' ? 'selected' : '' }}>UPC-A</option>
                                        <option value="upce" {{ old('tipo') == 'upce' ? 'selected' : '' }}>UPC-E</option>
                                        <option value="code128" {{ old('tipo') == 'code128' ? 'selected' : '' }}>Code 128</option>
                                        <option value="code39" {{ old('tipo') == 'code39' ? 'selected' : '' }}>Code 39</option>
                                        <option value="qrcode" {{ old('tipo') == 'qrcode' ? 'selected' : '' }}>QR Code</option>
                                        <option value="datamatrix" {{ old('tipo') == 'datamatrix' ? 'selected' : '' }}>Data Matrix</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="principal" 
                                               id="principal" 
                                               value="1" 
                                               {{ old('principal') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="principal">
                                            Código Principal do Produto
                                        </label>
                                    </div>
                                    <small class="text-muted">Marque se este é o código principal do produto</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="ativo" 
                                               id="ativo" 
                                               value="1" 
                                               {{ old('ativo', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ativo">
                                            Código Ativo
                                        </label>
                                    </div>
                                    <small class="text-muted">Códigos inativos não são utilizados nas operações</small>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('comerciantes.produtos.codigos-barras.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Código
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.getElementById('codigo');
    const tipoSelect = document.getElementById('tipo');
    
    // Auto-detectar tipo de código baseado no formato
    codigoInput.addEventListener('input', function() {
        const codigo = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos
        
        if (codigo.length === 13) {
            tipoSelect.value = 'ean13';
        } else if (codigo.length === 8) {
            tipoSelect.value = 'ean8';
        } else if (codigo.length === 12) {
            tipoSelect.value = 'upca';
        } else if (codigo.length === 6 || codigo.length === 7) {
            tipoSelect.value = 'upce';
        }
    });
    
    // Validação em tempo real
    codigoInput.addEventListener('blur', function() {
        const codigo = this.value.trim();
        
        if (codigo) {
            // Aqui você pode adicionar validação AJAX para verificar se o código já existe
            verificarCodigoExistente(codigo);
        }
    });
    
    function verificarCodigoExistente(codigo) {
        // Implementar verificação AJAX se necessário
        console.log('Verificando código:', codigo);
    }
});
</script>
@endpush

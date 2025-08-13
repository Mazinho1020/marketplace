@extends('layouts.comerciante')

@section('title', 'Editar Código de Barras')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Editar Código de Barras</h1>
                    <p class="text-muted">Edite as informações do código de barras</p>
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
                        <i class="fas fa-barcode me-2"></i>Editar Código de Barras
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.produtos.codigos-barras.update', $codigoBarras->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="produto_id" class="form-label">Produto <span class="text-danger">*</span></label>
                                    <select name="produto_id" id="produto_id" class="form-select @error('produto_id') is-invalid @enderror" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            <option value="{{ $produto->id }}" 
                                                    {{ (old('produto_id', $codigoBarras->produto_id) == $produto->id) ? 'selected' : '' }}>
                                                {{ $produto->nome }}
                                                @if($produto->sku)
                                                    - SKU: {{ $produto->sku }}
                                                @endif
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
                                    <label for="variacao_id" class="form-label">Variação</label>
                                    <select name="variacao_id" id="variacao_id" class="form-select @error('variacao_id') is-invalid @enderror">
                                        <option value="">Produto principal (sem variação)</option>
                                        @if($codigoBarras->produto && $codigoBarras->produto->variacoes)
                                            @foreach($codigoBarras->produto->variacoes as $variacao)
                                                <option value="{{ $variacao->id }}" 
                                                        {{ (old('variacao_id', $codigoBarras->variacao_id) == $variacao->id) ? 'selected' : '' }}>
                                                    {{ $variacao->nome }}
                                                    @if($variacao->cor) - {{ $variacao->cor }} @endif
                                                    @if($variacao->tamanho) - {{ $variacao->tamanho }} @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('variacao_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="codigo" class="form-label">Código de Barras <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="codigo" 
                                           id="codigo" 
                                           class="form-control font-monospace @error('codigo') is-invalid @enderror" 
                                           value="{{ old('codigo', $codigoBarras->codigo) }}" 
                                           placeholder="Digite ou escaneie o código de barras"
                                           required>
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Tipos suportados: EAN-13, EAN-8, UPC-A, Code 128, etc.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Código <span class="text-danger">*</span></label>
                                    <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="ean13" {{ old('tipo', $codigoBarras->tipo) == 'ean13' ? 'selected' : '' }}>EAN-13</option>
                                        <option value="ean8" {{ old('tipo', $codigoBarras->tipo) == 'ean8' ? 'selected' : '' }}>EAN-8</option>
                                        <option value="code128" {{ old('tipo', $codigoBarras->tipo) == 'code128' ? 'selected' : '' }}>Code 128</option>
                                        <option value="interno" {{ old('tipo', $codigoBarras->tipo) == 'interno' ? 'selected' : '' }}>Código Interno</option>
                                        <option value="outro" {{ old('tipo', $codigoBarras->tipo) == 'outro' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="principal" 
                                           id="principal" 
                                           value="1"
                                           {{ old('principal', $codigoBarras->principal) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="principal">
                                        <strong>Código Principal</strong>
                                        <br><small class="text-muted">Usar como código principal do produto</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="ativo" 
                                           id="ativo" 
                                           value="1"
                                           {{ old('ativo', $codigoBarras->ativo ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        <strong>Código Ativo</strong>
                                        <br><small class="text-muted">Habilitar uso do código</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações</label>
                                    <textarea name="observacoes" 
                                              id="observacoes" 
                                              class="form-control @error('observacoes') is-invalid @enderror" 
                                              rows="3"
                                              placeholder="Observações adicionais sobre este código de barras">{{ old('observacoes', $codigoBarras->observacoes) }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Prévia do Código de Barras -->
                        <div class="card bg-light mb-3" id="preview-container" style="display: none;">
                            <div class="card-body text-center">
                                <h6 class="card-title">Prévia do Código de Barras</h6>
                                <div id="barcode-preview" class="mb-2"></div>
                                <div class="text-muted">
                                    <strong>Tipo detectado:</strong> <span id="tipo-detectado">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="card bg-info bg-opacity-10 mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="fas fa-info-circle me-2"></i>Informações do Registro
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Criado em:</strong> {{ $codigoBarras->created_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Última atualização:</strong> {{ $codigoBarras->updated_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('comerciantes.produtos.codigos-barras.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Atualizar Código
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
    const previewContainer = document.getElementById('preview-container');
    const barcodePreview = document.getElementById('barcode-preview');
    const tipoDetectado = document.getElementById('tipo-detectado');
    
    // Detecção automática do tipo de código
    function detectarTipoCodigo(codigo) {
        if (!codigo) return null;
        
        // Remove espaços e caracteres especiais
        codigo = codigo.replace(/\s+/g, '');
        
        // EAN-13 (13 dígitos)
        if (/^\d{13}$/.test(codigo)) {
            return 'EAN-13';
        }
        
        // EAN-8 (8 dígitos)
        if (/^\d{8}$/.test(codigo)) {
            return 'EAN-8';
        }
        
        // UPC-A (12 dígitos)
        if (/^\d{12}$/.test(codigo)) {
            return 'UPC-A';
        }
        
        // UPC-E (6 dígitos)
        if (/^\d{6}$/.test(codigo)) {
            return 'UPC-E';
        }
        
        // ITF-14 (14 dígitos)
        if (/^\d{14}$/.test(codigo)) {
            return 'ITF';
        }
        
        // Code 128 (alfanumérico variável)
        if (/^[A-Za-z0-9\-\.\s]+$/.test(codigo) && codigo.length >= 6) {
            return 'CODE128';
        }
        
        return 'PERSONALIZADO';
    }
    
    // Atualizar tipo quando código mudar
    codigoInput.addEventListener('input', function() {
        const codigo = this.value;
        const tipoDetectadoValor = detectarTipoCodigo(codigo);
        
        if (tipoDetectadoValor && tipoSelect.value === '') {
            tipoDetectado.textContent = tipoDetectadoValor;
            previewContainer.style.display = 'block';
            
            // Simular prévia (em uma implementação real, usaria uma biblioteca de códigos de barras)
            barcodePreview.innerHTML = `
                <div class="p-3 bg-white border rounded">
                    <div class="text-center">
                        <div style="font-family: 'Courier New', monospace; font-size: 24px; letter-spacing: 2px;">
                            ${codigo}
                        </div>
                        <div class="mt-2" style="font-size: 12px;">
                            ${tipoDetectadoValor}
                        </div>
                    </div>
                </div>
            `;
        } else if (!codigo) {
            previewContainer.style.display = 'none';
        }
    });
    
    // Trigger inicial para códigos já preenchidos
    if (codigoInput.value) {
        codigoInput.dispatchEvent(new Event('input'));
    }
    
    // Validação do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const codigo = codigoInput.value.trim();
        
        if (!codigo) {
            e.preventDefault();
            alert('Por favor, digite um código de barras válido.');
            codigoInput.focus();
            return false;
        }
        
        // Validações específicas por tipo
        const tipoSelecionado = tipoSelect.value || detectarTipoCodigo(codigo);
        
        if (tipoSelecionado === 'EAN-13' && !/^\d{13}$/.test(codigo.replace(/\s+/g, ''))) {
            e.preventDefault();
            alert('Código EAN-13 deve ter exatamente 13 dígitos.');
            codigoInput.focus();
            return false;
        }
        
        if (tipoSelecionado === 'EAN-8' && !/^\d{8}$/.test(codigo.replace(/\s+/g, ''))) {
            e.preventDefault();
            alert('Código EAN-8 deve ter exatamente 8 dígitos.');
            codigoInput.focus();
            return false;
        }
    });
});
</script>
@endpush

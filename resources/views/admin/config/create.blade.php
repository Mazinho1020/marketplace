
  

                           @extends('layouts.app')

@section('title', 'Nova Configuração')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Nova Configuração
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome *</label>
                                <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chave *</label>
                                <input type="text" name="chave" class="form-control" value="{{ old('chave') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="string" {{ old('tipo') == 'string' ? 'selected' : '' }}>Texto</option>
                                    <option value="boolean" {{ old('tipo') == 'boolean' ? 'selected' : '' }}>Verdadeiro/Falso</option>
                                    <option value="integer" {{ old('tipo') == 'integer' ? 'selected' : '' }}>Número</option>
                                    <option value="email" {{ old('tipo') == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="url" {{ old('tipo') == 'url' ? 'selected' : '' }}>URL</option>
                                    <option value="json" {{ old('tipo') == 'json' ? 'selected' : '' }}>JSON</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grupo ID *</label>
                                <input type="number" name="grupo_id" class="form-control" value="{{ old('grupo_id', 1) }}" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Valor Padrão</label>
                                <input type="text" name="valor_padrao" class="form-control" value="{{ old('valor_padrao') }}">
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3">{{ old('descricao') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="visivel" value="1" class="form-check-input" {{ old('visivel', true) ? 'checked' : '' }}>
                                <label class="form-check-label">Visível</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="editavel" value="1" class="form-check-input" {{ old('editavel', true) ? 'checked' : '' }}>
                                <label class="form-check-label">Editável</label>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Salvar
                            </button>
                            <a href="{{ route('admin.config.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
                               

                               
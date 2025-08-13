@extends('comerciantes.layouts.app')

@section('title', 'Editar Conta Gerencial')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.dashboard.empresa', $empresa) }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.contas.index', $empresa) }}">Contas Gerenciais</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id]) }}">{{ $conta->nome }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-edit"></i> Editar Conta Gerencial
        </h1>
        <a href="{{ route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Alertas -->
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Formulário -->
    <form action="{{ route('comerciantes.empresas.financeiro.contas.update', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
          method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <!-- Informações Básicas -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Informações Básicas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="nome" class="form-label">Nome da Conta *</label>
                                    <input type="text" 
                                           class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" 
                                           name="nome" 
                                           value="{{ old('nome', $conta->nome) }}" 
                                           required>
                                    @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="codigo" class="form-label">Código</label>
                                    <input type="text" 
                                           class="form-control @error('codigo') is-invalid @enderror" 
                                           id="codigo" 
                                           name="codigo" 
                                           value="{{ old('codigo', $conta->codigo) }}" 
                                           placeholder="Ex: 1.1.01">
                                    @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="3">{{ old('descricao', $conta->descricao) }}</textarea>
                            @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="natureza" class="form-label">Natureza *</label>
                                    <select class="form-control @error('natureza') is-invalid @enderror" 
                                            id="natureza" 
                                            name="natureza" 
                                            required>
                                        <option value="">Selecione...</option>
                                        @foreach($naturezas as $natureza)
                                        <option value="{{ $natureza->value }}" 
                                                {{ old('natureza', $conta->natureza->value) === $natureza->value ? 'selected' : '' }}>
                                            {{ $natureza->label() }} ({{ $natureza->value }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('natureza')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nivel" class="form-label">Nível</label>
                                    <input type="number" 
                                           class="form-control @error('nivel') is-invalid @enderror" 
                                           id="nivel" 
                                           name="nivel" 
                                           value="{{ old('nivel', $conta->nivel) }}" 
                                           min="0"
                                           max="10">
                                    @error('nivel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relacionamentos -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sitemap"></i> Relacionamentos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="categoria_id" class="form-label">Categoria</label>
                                    <select class="form-control @error('categoria_id') is-invalid @enderror" 
                                            id="categoria_id" 
                                            name="categoria_id">
                                        <option value="">Nenhuma categoria</option>
                                        @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" 
                                                {{ old('categoria_id', $conta->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="conta_pai_id" class="form-label">Conta Pai</label>
                                    <select class="form-control @error('conta_pai_id') is-invalid @enderror" 
                                            id="conta_pai_id" 
                                            name="conta_pai_id">
                                        <option value="">Conta raiz (sem pai)</option>
                                        @foreach($contasPai as $contaPai)
                                        <option value="{{ $contaPai->id }}" 
                                                {{ old('conta_pai_id', $conta->conta_pai_id) == $contaPai->id ? 'selected' : '' }}>
                                            {{ $contaPai->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('conta_pai_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="classificacao_dre_id" class="form-label">Classificação DRE</label>
                                    <select class="form-control @error('classificacao_dre_id') is-invalid @enderror" 
                                            id="classificacao_dre_id" 
                                            name="classificacao_dre_id">
                                        <option value="">Nenhuma classificação</option>
                                        @foreach($classificacoesDre as $classificacao)
                                        <option value="{{ $classificacao->id }}" 
                                                {{ old('classificacao_dre_id', $conta->classificacao_dre_id) == $classificacao->id ? 'selected' : '' }}>
                                            {{ $classificacao->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('classificacao_dre_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tipo_id" class="form-label">Tipo</label>
                                    <select class="form-control @error('tipo_id') is-invalid @enderror" 
                                            id="tipo_id" 
                                            name="tipo_id">
                                        <option value="">Nenhum tipo</option>
                                        @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" 
                                                {{ old('tipo_id', $conta->tipo_id) == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('tipo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações Avançadas -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs"></i> Configurações Avançadas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="grupo_dre" class="form-label">Grupo DRE</label>
                                    <input type="text" 
                                           class="form-control @error('grupo_dre') is-invalid @enderror" 
                                           id="grupo_dre" 
                                           name="grupo_dre" 
                                           value="{{ old('grupo_dre', $conta->grupo_dre) }}" 
                                           placeholder="Ex: Receitas Operacionais">
                                    @error('grupo_dre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ordem_exibicao" class="form-label">Ordem de Exibição</label>
                                    <input type="number" 
                                           class="form-control @error('ordem_exibicao') is-invalid @enderror" 
                                           id="ordem_exibicao" 
                                           name="ordem_exibicao" 
                                           value="{{ old('ordem_exibicao', $conta->ordem_exibicao) }}" 
                                           min="0">
                                    @error('ordem_exibicao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Switches de Configuração -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="hidden" name="ativo" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="ativo" 
                                           name="ativo" 
                                           value="1"
                                           {{ old('ativo', $conta->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Conta Ativa
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="aceita_lancamento" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="aceita_lancamento" 
                                           name="aceita_lancamento" 
                                           value="1"
                                           {{ old('aceita_lancamento', $conta->aceita_lancamento) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="aceita_lancamento">
                                        Aceita Lançamentos
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_sintetica" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_sintetica" 
                                           name="e_sintetica" 
                                           value="1"
                                           {{ old('e_sintetica', $conta->e_sintetica) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="e_sintetica">
                                        É Conta Sintética
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_custo" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_custo" 
                                           name="e_custo" 
                                           value="1"
                                           {{ old('e_custo', $conta->e_custo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="e_custo">
                                        É Conta de Custo
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_despesa" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_despesa" 
                                           name="e_despesa" 
                                           value="1"
                                           {{ old('e_despesa', $conta->e_despesa) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="e_despesa">
                                        É Conta de Despesa
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_receita" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_receita" 
                                           name="e_receita" 
                                           value="1"
                                           {{ old('e_receita', $conta->e_receita) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="e_receita">
                                        É Conta de Receita
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Visual -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-palette"></i> Personalização Visual
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="cor" class="form-label">Cor</label>
                            <input type="color" 
                                   class="form-control form-control-color @error('cor') is-invalid @enderror" 
                                   id="cor" 
                                   name="cor" 
                                   value="{{ old('cor', $conta->cor ?? '#007bff') }}"
                                   title="Escolha uma cor">
                            @error('cor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="icone" class="form-label">Ícone</label>
                            <input type="text" 
                                   class="form-control @error('icone') is-invalid @enderror" 
                                   id="icone" 
                                   name="icone" 
                                   value="{{ old('icone', $conta->icone) }}" 
                                   placeholder="Ex: fas fa-money-bill">
                            <small class="form-text text-muted">
                                Use classes do Font Awesome. <a href="https://fontawesome.com/icons" target="_blank">Ver ícones</a>
                            </small>
                            @error('icone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(old('icone', $conta->icone))
                        <div class="text-center">
                            <i class="{{ old('icone', $conta->icone) }}" style="font-size: 2rem;"></i>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Ações -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-save"></i> Ações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            
                            <a href="{{ route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview do ícone
    const iconeInput = document.getElementById('icone');
    const previewContainer = document.querySelector('.text-center');
    
    if (iconeInput) {
        iconeInput.addEventListener('input', function() {
            const icone = this.value.trim();
            if (icone && previewContainer) {
                previewContainer.innerHTML = `<i class="${icone}" style="font-size: 2rem;"></i>`;
            } else if (previewContainer) {
                previewContainer.innerHTML = '';
            }
        });
    }
});
</script>
@endpush

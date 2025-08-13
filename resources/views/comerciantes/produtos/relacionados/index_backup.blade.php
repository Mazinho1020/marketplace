@extends('comerciantes.layouts.app')

@section('title', 'Produtos Relacionados')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>🔗 Produtos Relacionados</h1>
                    <p class="text-muted mb-0">
                        <strong>{{ $produto->nome }}</strong> ({{ $produto->sku }})
                    </p>
                </div>
                <div>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoRelacionado">
                        ➕ Adicionar Relacionamento
                    </button>

                    <a href="{{ route('comerciantes.produtos.index') }}" class="btn btn-secondary">
                        ← Voltar aos Produtos
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">🔍 Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="tipo_relacao">Tipo de Relação:</label>
                                <select name="tipo_relacao" id="tipo_relacao" class="form-control">
                                    <option value="">Todos os tipos</option>
                                    @foreach(\App\Models\ProdutoRelacionado::getTiposRelacao() as $key => $label)
                                    <option value="{{ $key }}" {{ request('tipo_relacao') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="ativo">Status:</label>
                                <select name="ativo" id="ativo" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('ativo') == '1' ? 'selected' : '' }}>Ativo</option>
                                    <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                                    <a href="{{ route('comerciantes.produtos.relacionados.index', $produto->id) }}" class="btn btn-outline-secondary">Limpar</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Relacionamentos por Tipo -->
            @if($relacionados->count() > 0)
            @foreach($relacionados->groupBy('tipo_relacao') as $tipo => $grupo)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        @switch($tipo)
                        @case('similar')
                        🔄 Produtos Similares
                        @break
                        @case('complementar')
                        🧩 Produtos Complementares
                        @break
                        @case('acessorio')
                        🔧 Acessórios
                        @break
                        @case('substituto')
                        🔀 Produtos Substitutos
                        @break
                        @case('kit')
                        📦 Componentes de Kit
                        @break
                        @case('cross-sell')
                        💡 Cross-sell
                        @break
                        @case('up-sell')
                        📈 Up-sell
                        @break
                        @default
                        {{ ucfirst($tipo) }}
                        @endswitch
                        <span class="badge badge-primary">{{ $grupo->count() }}</span>
                    </h5>
                    <small class="text-muted">Arraste para reordenar</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 sortable-table" data-tipo="{{ $tipo }}">
                            <thead>
                                <tr>
                                    <th width="50">📋</th>
                                    <th>Produto</th>
                                    <th>SKU</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Ordem</th>
                                    <th>Status</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="sortable-body">
                                @foreach($grupo->sortBy('ordem') as $relacionado)
                                <tr data-id="{{ $relacionado->id }}" data-ordem="{{ $relacionado->ordem }}">
                                    <td class="drag-handle" style="cursor: move;">
                                        <i class="fas fa-grip-vertical text-muted"></i>
                                    </td>
                                    <td>
                                        <strong>{{ $relacionado->produtoRelacionado->nome }}</strong>
                                    </td>
                                    <td>
                                        <code>{{ $relacionado->produtoRelacionado->sku }}</code>
                                    </td>
                                    <td>
                                        <span class="text-success font-weight-bold">
                                            R$ {{ number_format($relacionado->produtoRelacionado->preco_venda, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($relacionado->produtoRelacionado->controla_estoque)
                                        <span class="badge {{ $relacionado->produtoRelacionado->estoque_atual > 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $relacionado->produtoRelacionado->estoque_atual }}
                                        </span>
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number" class="form-control ordem-input"
                                            value="{{ $relacionado->ordem }}"
                                            data-id="{{ $relacionado->id }}"
                                            style="width: 80px;" min="0">
                                    </td>
                                    <td>
                                        {!! $relacionado->status_badge !!}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-editar"
                                                data-id="{{ $relacionado->id }}"
                                                data-tipo="{{ $relacionado->tipo_relacao }}"
                                                data-ordem="{{ $relacionado->ordem }}"
                                                data-ativo="{{ $relacionado->ativo }}"
                                                data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST"
                                                action="{{ route('comerciantes.produtos.relacionados.destroy', [$produto->id, $relacionado->id]) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Deseja remover este relacionamento?')"
                                                    data-bs-toggle="tooltip" title="Remover">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-link fa-3x text-muted"></i>
                    </div>
                    <h5>Nenhum produto relacionado</h5>
                    <p class="text-muted">Este produto ainda não possui relacionamentos configurados.</p>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoRelacionado">
                        ➕ Adicionar Primeiro Relacionamento
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Novo Relacionamento -->
<div class="modal fade" id="modalNovoRelacionado" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('comerciantes.produtos.relacionados.store', $produto->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">➕ Adicionar Produto Relacionado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_produto_relacionado_id">Produto *</label>
                                <select name="produto_relacionado_id" id="modal_produto_relacionado_id"
                                    class="form-control select2-ajax" required>
                                    <!-- Options serão carregadas via AJAX -->
                                </select>
                                <small class="text-muted">Digite pelo menos 1 caractere para buscar</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_tipo_relacao">Tipo de Relação *</label>
                                <select name="tipo_relacao" id="modal_tipo_relacao" class="form-control" required>
                                    <option value="">Selecione o tipo</option>
                                    @foreach(\App\Models\ProdutoRelacionado::getTiposRelacao() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_ordem">Ordem</label>
                                <input type="number" name="ordem" id="modal_ordem"
                                    class="form-control" value="0" min="0">
                                <small class="text-muted">Ordem de exibição (0 = primeiro)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_ativo">Status</label>
                                <select name="ativo" id="modal_ativo" class="form-control">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Informações sobre tipos de relação -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Tipos de Relacionamento:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Similar:</strong> Produtos parecidos (relacionamento bidirecional)</li>
                            <li><strong>Complementar:</strong> Produtos que se complementam (relacionamento bidirecional)</li>
                            <li><strong>Acessório:</strong> Acessórios para este produto</li>
                            <li><strong>Substituto:</strong> Produtos que podem substituir este</li>
                            <li><strong>Cross-sell:</strong> Sugestões de produtos adicionais</li>
                            <li><strong>Up-sell:</strong> Produtos de maior valor para upgrade</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">💾 Adicionar Relacionamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Relacionamento -->
<div class="modal fade" id="modalEditarRelacionado" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="formEditarRelacionado">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Editar Relacionamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo_relacao">Tipo de Relação *</label>
                                <select name="tipo_relacao" id="edit_tipo_relacao" class="form-control" required>
                                    @foreach(\App\Models\ProdutoRelacionado::getTiposRelacao() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ordem">Ordem</label>
                                <input type="number" name="ordem" id="edit_ordem"
                                    class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit_ativo">Status</label>
                                <select name="ativo" id="edit_ativo" class="form-control">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">💾 Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- CSS adicional para modal e Select2 -->
<style>
    .modal.show {
        display: block !important;
        pointer-events: auto !important;
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
        pointer-events: auto !important;
    }

    .modal-dialog {
        pointer-events: auto !important;
    }

    .modal-content {
        pointer-events: auto !important;
    }

    .btn-close {
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='m.235.694l15.07 15.07a.5.5 0 0 0 .708-.708L.943-.014a.5.5 0 0 0-.708.708z'/%3e%3cpath d='m15.735.694l-15.07 15.07a.5.5 0 0 0 .708.708L16.043.014a.5.5 0 0 0-.708-.708z'/%3e%3c/svg%3e") center/1em auto no-repeat;
        border: 0;
        border-radius: 0.375rem;
        opacity: 0.5;
        padding: 0.375rem;
        width: 1em;
        height: 1em;
    }

    .btn-close:hover {
        opacity: 0.75;
    }

    /* Estilos específicos para Select2 no modal */
    .select2-container {
        width: 100% !important;
        display: block !important;
        z-index: 9999;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        padding: 0 12px !important;
        color: #495057 !important;
    }

    .select2-container .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 10px !important;
    }

    .select2-dropdown {
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        z-index: 9999 !important;
        background: white !important;
    }

    .select2-search--dropdown {
        padding: 8px !important;
        display: block !important;
        visibility: visible !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        padding: 6px 12px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Forçar visibilidade do campo de busca */
    .select2-dropdown .select2-search {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
        overflow: visible !important;
    }

    /* Garantir que o dropdown tenha altura mínima para mostrar a busca */
    .select2-dropdown {
        min-height: 60px !important;
    }

    .select2-results {
        max-height: 200px !important;
        overflow-y: auto !important;
    }

    .select2-results__option {
        padding: 8px 12px !important;
        cursor: pointer !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff !important;
        color: white !important;
    }

    /* Z-index para modal */
    .modal {
        z-index: 1055 !important;
    }

    .modal-backdrop {
        z-index: 1050 !important;
    }

    .select2-container--open .select2-dropdown {
        z-index: 9999 !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 6px 12px;
    }

    /* Select original como fallback */
    #modal_produto_relacionado_id {
        width: 100%;
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    /* Esconder o select original apenas quando Select2 estiver ativo */
    .select2-hidden-accessible {
        display: none !important;
    }
</style>

<script>
    // ========================================
    // CARREGAMENTO DINÂMICO DE DEPENDÊNCIAS
    // ========================================

    // Função para carregar bibliotecas dinamicamente
    function loadLibrariesIfNeeded() {
        const promises = [];

        // Carregar Select2 se necessário
        if (typeof $.fn.select2 === 'undefined') {
            console.log('📦 Carregando Select2...');

            const select2Promise = new Promise((resolve, reject) => {
                // Carregar CSS primeiro
                const select2CSS = document.createElement('link');
                select2CSS.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css';
                select2CSS.rel = 'stylesheet';
                document.head.appendChild(select2CSS);

                // Carregar JS e aguardar conclusão
                const select2JS = document.createElement('script');
                select2JS.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js';
                select2JS.onload = () => {
                    // Aguardar um pouco mais para garantir que está totalmente carregado
                    setTimeout(() => {
                        if (typeof $.fn.select2 !== 'undefined') {
                            console.log('✅ Select2 carregado e verificado dinamicamente');
                            resolve();
                        } else {
                            console.warn('⚠️ Select2 carregado mas não detectado');
                            resolve(); // Resolve mesmo assim para não travar
                        }
                    }, 200);
                };
                select2JS.onerror = () => {
                    console.error('❌ Erro ao carregar Select2');
                    reject();
                };
                document.head.appendChild(select2JS);
            });
            promises.push(select2Promise);
        } else {
            console.log('✅ Select2 já estava disponível');
        }

        // Carregar SortableJS se necessário
        if (typeof Sortable === 'undefined') {
            console.log('📦 Carregando SortableJS...');

            const sortablePromise = new Promise((resolve, reject) => {
                const sortableJS = document.createElement('script');
                sortableJS.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
                sortableJS.onload = () => {
                    console.log('✅ SortableJS carregado dinamicamente');
                    resolve();
                };
                sortableJS.onerror = () => {
                    console.error('❌ Erro ao carregar SortableJS');
                    reject();
                };
                document.head.appendChild(sortableJS);
            });
            promises.push(sortablePromise);
        } else {
            console.log('✅ SortableJS já estava disponível');
        }

        return Promise.all(promises);
    }

    // ========================================
    // FUNÇÕES GLOBAIS (definidas primeiro)
    // ========================================

    // Função para inicializar Select2 com busca garantida
    function initializeSelect2() {
        console.log('🚀 Iniciando configuração do Select2...');

        const elemento = $('#modal_produto_relacionado_id');
        if (elemento.length === 0) {
            console.error('❌ Elemento não encontrado!');
            return;
        }

        // Verificar se Select2 está disponível
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('⚠️ Select2 não disponível ainda, aguardando...');
            // Tentar novamente em 500ms
            setTimeout(() => initializeSelect2(), 500);
            return;
        }

        // Limpar Select2 existente
        if (elemento.hasClass('select2-hidden-accessible')) {
            elemento.select2('destroy');
        }

        const modal = $('#modalNovoRelacionado');
        const buscarUrl = '{{ route("comerciantes.produtos.relacionados.buscar", $produto->id) }}';

        try {
            // Configuração robusta do Select2
            elemento.select2({
                dropdownParent: modal,
                placeholder: 'Digite para buscar produtos...',
                allowClear: true,
                minimumInputLength: 1,
                width: '100%',
                closeOnSelect: true,
                language: {
                    inputTooShort: function() {
                        return 'Digite pelo menos 1 caractere para buscar';
                    },
                    noResults: function() {
                        return 'Nenhum produto encontrado';
                    },
                    searching: function() {
                        return 'Buscando produtos...';
                    }
                },
                ajax: {
                    url: buscarUrl,
                    dataType: 'json',
                    delay: 250,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        if (data && data.results) {
                            return data;
                        } else if (Array.isArray(data)) {
                            return { results: data };
                        } else {
                            return { results: [] };
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Erro na busca:', error);
                        
                        if (xhr.responseText && xhr.responseText.indexOf('<!DOCTYPE') === 0) {
                            alert('⚠️ Sessão expirada! Redirecionando...');
                            setTimeout(() => {
                                window.location.href = '{{ route("comerciantes.login") }}';
                            }, 2000);
                        }
                    },
                    cache: true
                }
            });

            console.log('✅ Select2 configurado com sucesso!');

            // Adicionar evento para focar no campo de busca quando abrir
            elemento.on('select2:open', function() {
                setTimeout(() => {
                    $('.select2-search__field').focus();
                }, 50);
            });

        } catch (error) {
            console.error('❌ Erro ao configurar Select2:', error);
            
            // Fallback: Select2 básico
            try {
                elemento.select2({
                    dropdownParent: modal,
                    placeholder: 'Clique para buscar produtos...',
                    allowClear: true,
                    width: '100%'
                });
                console.log('✅ Select2 básico aplicado');
            } catch (fallbackError) {
                console.error('❌ Erro no fallback:', fallbackError);
            }
        }
    }

    // Função de debug para o campo Select2
    function debugSelect2Field() {
        console.log('🔍 DEBUGANDO CAMPO SELECT2...');

        const elemento = $('#modal_produto_relacionado_id');
        const modal = $('#modalNovoRelacionado');

        console.log('==== INFORMAÇÕES DO ELEMENTO ====');
        console.log('Elemento encontrado:', elemento.length > 0);
        console.log('ID:', elemento.attr('id'));
        console.log('Classes:', elemento.attr('class'));
        console.log('Elemento HTML:', elemento[0]);
        console.log('Valor atual:', elemento.val());
        console.log('Visível:', elemento.is(':visible'));
        console.log('Display CSS:', elemento.css('display'));

        console.log('==== INFORMAÇÕES DO MODAL ====');
        console.log('Modal visível:', modal.is(':visible'));
        console.log('Modal classes:', modal.attr('class'));
        console.log('Modal display:', modal.css('display'));

        console.log('==== SELECT2 STATUS ====');
        console.log('Select2 carregado:', typeof $.fn.select2 !== 'undefined');
        console.log('Select2 aplicado:', elemento.hasClass('select2-hidden-accessible'));

        if (elemento.hasClass('select2-hidden-accessible')) {
            console.log('✅ Select2 está aplicado!');
            const container = elemento.next('.select2-container');
            console.log('Container Select2:', container.length > 0);
            console.log('Container visível:', container.is(':visible'));
            console.log('Container display:', container.css('display'));
        } else {
            console.log('❌ Select2 NÃO está aplicado!');
            console.log('Tentando aplicar novamente...');

            try {
                elemento.select2({
                    dropdownParent: modal,
                    placeholder: 'Busque por nome ou SKU...',
                    allowClear: true,
                    minimumInputLength: 2,
                    width: '100%'
                });
                console.log('✅ Select2 aplicado com sucesso via debug!');
            } catch (error) {
                console.error('❌ Erro ao aplicar Select2:', error);
            }
        }

        // Mostrar estrutura HTML do modal
        console.log('==== HTML DO CAMPO ====');
        console.log(elemento.parent().html());

        alert('Debug completo! Verifique o console para detalhes.');
    }

    // Função de teste para verificar conexão
    function testarConexao() {
        console.log('🧪 Testando conexão com API de busca...');

        const url = '{{ route("comerciantes.produtos.relacionados.buscar", $produto->id) }}';

        $.ajax({
            url: url,
            method: 'GET',
            data: {
                q: 'teste'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data, status, xhr) {
                console.log('✅ Teste de conexão bem-sucedido!');
                console.log('Status:', xhr.status);
                console.log('Content-Type:', xhr.getResponseHeader('content-type'));
                console.log('Dados:', data);

                alert('✅ Conexão funcionando!\n\nVerifique o console para detalhes.');
            },
            error: function(xhr, status, error) {
                console.error('❌ Teste de conexão falhou!');
                console.error('Status:', xhr.status);
                console.error('Resposta:', xhr.responseText.substring(0, 200));

                if (xhr.responseText.indexOf('<!DOCTYPE') === 0) {
                    alert('❌ Sessão expirada!\n\nRedirecionando para login...');
                    setTimeout(() => {
                        window.location.href = '{{ route("comerciantes.login") }}';
                    }, 2000);
                } else {
                    alert('❌ Erro na conexão!\n\nStatus: ' + xhr.status + '\nVerifique o console para detalhes.');
                }
            }
        });
    }

    // Função de teste para Select2
    function testarSelect2() {
        console.log('🔧 Testando inicialização do Select2...');

        // Forçar abertura do modal
        const modal = new bootstrap.Modal(document.getElementById('modalNovoRelacionado'));
        modal.show();

        // Aguardar um pouco e tentar inicializar
        setTimeout(() => {
            initializeSelect2();

            // Verificar se funcionou
            setTimeout(() => {
                const elemento = $('#modal_produto_relacionado_id');
                const temSelect2 = elemento.hasClass('select2-hidden-accessible');

                console.log('🔍 Select2 aplicado:', temSelect2 ? 'SIM' : 'NÃO');

                if (temSelect2) {
                    alert('✅ Select2 funcionando!\n\nTente digitar para buscar produtos.');
                } else {
                    alert('❌ Select2 não foi aplicado!\n\nVerifique o console para mais detalhes.');
                }
            }, 500);
        }, 500);
    }

    // Verificar se as funções foram carregadas corretamente
    console.log('🔧 Funções globais carregadas:');
    console.log('testarSelect2:', typeof testarSelect2 !== 'undefined' ? '✅' : '❌');
    console.log('initializeSelect2:', typeof initializeSelect2 !== 'undefined' ? '✅' : '❌');

    // ========================================
    // INICIALIZAÇÃO DO DOCUMENTO
    // ========================================

    $(document).ready(function() {
        // Carregar bibliotecas necessárias primeiro
        loadLibrariesIfNeeded().then(() => {
            console.log('🚀 Todas as bibliotecas carregadas, iniciando aplicação...');
            initializeApp();
        });
    });

    // Função principal de inicialização
    function initializeApp() {
        // Debug: Verificar se todas as bibliotecas estão carregadas
        console.log('🔍 Verificação de bibliotecas:');
        console.log('Bootstrap:', typeof bootstrap !== 'undefined' ? '✅ Carregado' : '❌ NÃO carregado');
        console.log('jQuery:', typeof $ !== 'undefined' ? '✅ Carregado' : '❌ NÃO carregado');
        console.log('Select2:', typeof $.fn.select2 !== 'undefined' ? '✅ Carregado' : '❌ NÃO carregado');
        console.log('Sortable:', typeof Sortable !== 'undefined' ? '✅ Carregado' : '❌ NÃO carregado');

        // Interceptar erros JavaScript
        window.addEventListener('error', function(e) {
            console.error('❌ Erro JavaScript:', e.message, 'em', e.filename, 'linha', e.lineno);
        });

        // Configuração simples de modais - sem interferência
        $('[data-bs-toggle="modal"]').off('click').on('click', function(e) {
            const targetModal = $(this).data('bs-target');
            console.log('🖱️ Abrindo modal:', targetModal);

            if (typeof bootstrap !== 'undefined' && targetModal) {
                const modalElement = document.querySelector(targetModal);
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            }
        });

        // Fechar modal - versão simplificada
        $('[data-bs-dismiss="modal"]').off('click').on('click', function(e) {
            const modal = $(this).closest('.modal');
            console.log('�️ Fechando modal:', modal.attr('id'));

            if (typeof bootstrap !== 'undefined') {
                const modalElement = modal[0];
                const bsModal = bootstrap.Modal.getInstance(modalElement);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    const newModal = new bootstrap.Modal(modalElement);
                    newModal.hide();
                }
            }
        });

        // Inicializar Select2 quando o modal for mostrado
        $('#modalNovoRelacionado').off('shown.bs.modal').on('shown.bs.modal', function() {
            console.log('🎯 Modal mostrado, inicializando Select2...');
            setTimeout(() => {
                initializeSelect2();
            }, 100);
        });

        // Limpar Select2 quando o modal for fechado
        $('#modalNovoRelacionado').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            console.log('🧹 Modal fechado, limpando Select2...');
            const elemento = $('#modal_produto_relacionado_id');
            if (elemento.hasClass('select2-hidden-accessible')) {
                elemento.select2('destroy');
            }
            elemento.val('').trigger('change');
        });

        // Sortable para reordenação
        if (typeof Sortable !== 'undefined') {
            $('.sortable-body').each(function() {
                const tipo = $(this).closest('.sortable-table').data('tipo');
                new Sortable(this, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function(evt) {
                        updateOrdem(tipo);
                    }
                });
            });
        } else {
            console.warn('⚠️ Sortable não está disponível, drag-and-drop desabilitado');
        }

        // Editar relacionamento
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            const tipo = $(this).data('tipo');
            const ordem = $(this).data('ordem');
            const ativo = $(this).data('ativo');

            $('#edit_tipo_relacao').val(tipo);
            $('#edit_ordem').val(ordem);
            $('#edit_ativo').val(ativo ? 1 : 0);

            $('#formEditarRelacionado').attr('action',
                '{{ route("comerciantes.produtos.relacionados.update", [$produto->id, ":id"]) }}'.replace(':id', id)
            );

            // Bootstrap 5 modal
            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(document.getElementById('modalEditarRelacionado'));
                modal.show();
            } else {
                $('#modalEditarRelacionado').show();
            }
        });

        // Atualizar ordem via input
        $('.ordem-input').on('change', function() {
            const id = $(this).data('id');
            const novaOrdem = $(this).val();
            const tipo = $(this).closest('.sortable-table').data('tipo');

            // Atualizar data-ordem para o sortable
            $(this).closest('tr').attr('data-ordem', novaOrdem);

            updateOrdemIndividual(id, novaOrdem);
        });

        function updateOrdem(tipo) {
            const relacionados = [];
            $(`.sortable-table[data-tipo="${tipo}"] tbody tr`).each(function(index) {
                relacionados.push({
                    id: $(this).data('id'),
                    ordem: index
                });
                // Atualizar input visual
                $(this).find('.ordem-input').val(index);
            });

            $.ajax({
                url: '{{ route("comerciantes.produtos.relacionados.update-ordem", $produto->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    relacionados: relacionados
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Ordem atualizada com sucesso!');
                    }
                },
                error: function() {
                    toastr.error('Erro ao atualizar ordem');
                }
            });
        }

        function updateOrdemIndividual(id, ordem) {
            $.ajax({
                url: '{{ route("comerciantes.produtos.relacionados.update", [$produto->id, ":id"]) }}'.replace(':id', id),
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    ordem: ordem,
                    tipo_relacao: $(`tr[data-id="${id}"]`).closest('.sortable-table').data('tipo'),
                    ativo: 1
                },
                success: function(response) {
                    toastr.success('Ordem atualizada!');
                },
                error: function() {
                    toastr.error('Erro ao atualizar ordem');
                }
            });
        }
    }

    // ========================================
    // FUNÇÕES JÁ DEFINIDAS GLOBALMENTE
    // ========================================
    function initializeSelect2() {
        console.log('🚀 Iniciando configuração do Select2...');

        // Verificar se o elemento existe
        const elemento = $('#modal_produto_relacionado_id');
        console.log('🎯 Elemento encontrado:', elemento.length > 0 ? 'SIM' : 'NÃO');

        if (elemento.length === 0) {
            console.error('❌ Elemento não encontrado!');
            return;
        }

        console.log('🎯 ID do elemento:', elemento.attr('id'));
        console.log('🎯 Classes do elemento:', elemento.attr('class'));

        if (typeof $.fn.select2 !== 'undefined') {
            console.log('✅ Select2 está disponível');
            const buscarUrl = '{{ route("comerciantes.produtos.relacionados.buscar", $produto->id) }}';
            console.log('📍 URL de busca:', buscarUrl);

            // Verificar se o modal está visível
            const modal = $('#modalNovoRelacionado');
            console.log('🏠 Modal visível:', modal.is(':visible') ? 'SIM' : 'NÃO');
            console.log('🏠 Modal classes:', modal.attr('class'));

            // Destruir Select2 existente se houver
            if (elemento.hasClass('select2-hidden-accessible')) {
                console.log('🔄 Destruindo Select2 existente...');
                elemento.select2('destroy');
            }

            try {
                // Forçar visibilidade do elemento primeiro
                elemento.show();

                // Aplicar Select2 com configuração completa
                elemento.select2({
                    dropdownParent: modal.length > 0 ? modal : $('body'),
                    placeholder: 'Busque por nome ou SKU...',
                    allowClear: true,
                    minimumInputLength: 2,
                    width: '100%',
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    language: {
                        inputTooShort: function() {
                            return 'Digite pelo menos 2 caracteres para buscar';
                        },
                        noResults: function() {
                            return 'Nenhum produto encontrado';
                        },
                        searching: function() {
                            return 'Buscando produtos...';
                        },
                        errorLoading: function() {
                            return 'Erro ao carregar resultados';
                        }
                    },
                    ajax: {
                        url: buscarUrl,
                        dataType: 'json',
                        delay: 250,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: function(params) {
                            console.log('📤 Enviando busca:', params.term);
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            console.log('[' + new Date().toLocaleTimeString() + '] [SUCCESS] ✅ Dados recebidos:', data);

                            // Verificar se a resposta tem o formato correto
                            if (data && data.results) {
                                return data;
                            } else if (Array.isArray(data)) {
                                return {
                                    results: data
                                };
                            } else {
                                console.warn('[' + new Date().toLocaleTimeString() + '] [WARNING] ⚠️ Formato de resposta inesperado:', data);
                                return {
                                    results: []
                                };
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('[' + new Date().toLocaleTimeString() + '] [ERROR] ❌ Erro na busca AJAX:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                error: error,
                                response: xhr.responseText.substring(0, 200)
                            });

                            // Verificar se a resposta é HTML (página de login)
                            if (xhr.responseText && xhr.responseText.indexOf('<!DOCTYPE') === 0) {
                                console.error('[' + new Date().toLocaleTimeString() + '] [ERROR] 🔒 Sessão expirada - redirecionando para login');

                                alert('⚠️ Sua sessão expirou!\n\nVocê será redirecionado para fazer login novamente.');

                                setTimeout(() => {
                                    window.location.href = '{{ route("comerciantes.login") }}';
                                }, 2000);

                                return;
                            }

                            // Mostrar erro específico baseado no status
                            let errorMessage = 'Erro na busca';
                            switch (xhr.status) {
                                case 401:
                                    errorMessage = 'Sessão expirada - Faça login novamente';
                                    break;
                                case 403:
                                    errorMessage = 'Sem permissão para buscar produtos';
                                    break;
                                case 404:
                                    errorMessage = 'Serviço de busca não encontrado';
                                    break;
                                case 500:
                                    errorMessage = 'Erro interno do servidor';
                                    break;
                            }

                            // Mostrar alerta para o usuário
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMessage);
                            } else {
                                alert(errorMessage);
                            }
                        }
                    }
                });

                console.log('✅ Select2 inicializado com sucesso!');

                // Verificar se foi aplicado
                setTimeout(() => {
                    if (elemento.hasClass('select2-hidden-accessible')) {
                        console.log('✅ Select2 confirmado como aplicado!');
                        const container = elemento.next('.select2-container');
                        if (container.length > 0) {
                            console.log('✅ Container Select2 encontrado!');
                            container.show(); // Forçar visibilidade
                        }
                    } else {
                        console.warn('⚠️ Select2 não foi aplicado, tentando alternativa...');
                        // Tentativa alternativa sem AJAX
                        elemento.select2({
                            placeholder: 'Busque por nome ou SKU...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                }, 100);

            } catch (error) {
                console.error('❌ Erro ao inicializar Select2:', error);

                // Fallback: Select2 simples sem AJAX
                try {
                    console.log('🔄 Tentando Select2 simples...');
                    elemento.select2({
                        placeholder: 'Busque por nome ou SKU...',
                        allowClear: true,
                        width: '100%'
                    });
                    console.log('✅ Select2 simples aplicado!');
                } catch (error2) {
                    console.error('❌ Erro no fallback Select2:', error2);
                    // Último recurso: mostrar select normal
                    elemento.show();
                    console.log('🔄 Usando select HTML padrão...');
                }
            }
        } else {
            console.warn('⚠️ Select2 não está disponível, usando select normal');

            // Fallback: carregar produtos manualmente
            console.log('🔄 Carregando lista de produtos para fallback...');
            const buscarUrl = '{{ route("comerciantes.produtos.relacionados.buscar", $produto->id) }}';
            $.ajax({
                url: buscarUrl,
                data: {
                    q: ''
                }, // Busca vazia para pegar alguns produtos
                success: function(response) {
                    if (response.results) {
                        const select = $('#modal_produto_relacionado_id');
                        select.empty().append('<option value="">Selecione um produto...</option>');

                        response.results.forEach(function(produto) {
                            select.append(`<option value="${produto.id}">${produto.text}</option>`);
                        });

                        console.log('✅ Produtos carregados no select normal');
                    }
                },
                error: function() {
                    console.error('❌ Erro ao carregar produtos para fallback');
                }
            });
        }
    }

    // Função de debug para o campo Select2
    function debugSelect2Field() {
        console.log('🔍 DEBUGANDO CAMPO SELECT2...');

        const elemento = $('#modal_produto_relacionado_id');
        const modal = $('#modalNovoRelacionado');

        console.log('==== INFORMAÇÕES DO ELEMENTO ====');
        console.log('Elemento encontrado:', elemento.length > 0);
        console.log('ID:', elemento.attr('id'));
        console.log('Classes:', elemento.attr('class'));
        console.log('Elemento HTML:', elemento[0]);
        console.log('Valor atual:', elemento.val());
        console.log('Visível:', elemento.is(':visible'));
        console.log('Display CSS:', elemento.css('display'));

        console.log('==== INFORMAÇÕES DO MODAL ====');
        console.log('Modal visível:', modal.is(':visible'));
        console.log('Modal classes:', modal.attr('class'));
        console.log('Modal display:', modal.css('display'));

        console.log('==== SELECT2 STATUS ====');
        console.log('Select2 carregado:', typeof $.fn.select2 !== 'undefined');
        console.log('Select2 aplicado:', elemento.hasClass('select2-hidden-accessible'));

        if (elemento.hasClass('select2-hidden-accessible')) {
            console.log('✅ Select2 está aplicado!');
            const container = elemento.next('.select2-container');
            console.log('Container Select2:', container.length > 0);
            console.log('Container visível:', container.is(':visible'));
            console.log('Container display:', container.css('display'));
        } else {
            console.log('❌ Select2 NÃO está aplicado!');
            console.log('Tentando aplicar novamente...');

            try {
                elemento.select2({
                    dropdownParent: modal,
                    placeholder: 'Busque por nome ou SKU...',
                    allowClear: true,
                    minimumInputLength: 2,
                    width: '100%'
                });
                console.log('✅ Select2 aplicado com sucesso via debug!');
            } catch (error) {
                console.error('❌ Erro ao aplicar Select2:', error);
            }
        }

        // Mostrar estrutura HTML do modal
        console.log('==== HTML DO CAMPO ====');
        console.log(elemento.parent().html());

        alert('Debug completo! Verifique o console para detalhes.');
    }

    // Verificar se as funções foram carregadas corretamente
    console.log('🔧 Funções globais carregadas:');
    console.log('debugSelect2Field:', typeof debugSelect2Field !== 'undefined' ? '✅' : '❌');
    console.log('testarConexao:', typeof testarConexao !== 'undefined' ? '✅' : '❌');
    console.log('testarSelect2:', typeof testarSelect2 !== 'undefined' ? '✅' : '❌');
    console.log('initializeSelect2:', typeof initializeSelect2 !== 'undefined' ? '✅' : '❌');

@endpush
@extends('layouts.comerciante')

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

    /* Estilos para o select de produtos */
    #modal_produto_relacionado_id {
        width: 100%;
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }

    #modal_produto_relacionado_id:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Estilos para o select com carregamento automático */
    .produto-select {
        background-image: linear-gradient(45deg, transparent 25%, rgba(0, 123, 255, 0.1) 25%),
            linear-gradient(-45deg, transparent 25%, rgba(0, 123, 255, 0.1) 25%),
            linear-gradient(45deg, rgba(0, 123, 255, 0.1) 75%, transparent 75%),
            linear-gradient(-45deg, rgba(0, 123, 255, 0.1) 75%, transparent 75%);
        background-size: 10px 10px;
        background-position: 0 0, 0 5px, 5px -5px, -5px 0px;
        transition: all 0.3s ease;
    }

    .produto-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        border-color: #007bff;
    }

    .produto-select:hover {
        background-color: #e3f2fd !important;
        cursor: pointer;
    }

    .produto-select.carregando {
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }

    .produto-select.sucesso {
        background-color: #d1e7dd;
        border-color: #badbcc;
        color: #0f5132;
    }

    .produto-select.erro {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
</style>

<script>
    // Função para carregar SortableJS se necessário
    function loadSortableJS() {
        return new Promise((resolve) => {
            if (typeof Sortable !== 'undefined') {
                console.log('✅ SortableJS já estava disponível');
                resolve();
                return;
            }

            if (document.querySelector('script[src*="sortablejs"]')) {
                console.log('⚠️ SortableJS script já existe no DOM');
                resolve();
                return;
            }

            console.log('📦 Carregando SortableJS...');
            const sortableJS = document.createElement('script');
            sortableJS.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
            sortableJS.onload = () => {
                console.log('✅ SortableJS carregado dinamicamente');
                resolve();
            };
            sortableJS.onerror = () => {
                console.error('❌ Erro ao carregar SortableJS');
                resolve(); // Não bloquear por SortableJS
            };
            document.head.appendChild(sortableJS);
        });
    }

    // Função para configurar o select de produtos com AJAX
    function setupProductSelect() {
        console.log('🔄 Configurando select de produtos com AJAX...');
        const elemento = $('#modal_produto_relacionado_id');

        // Limpar o select e adicionar uma opção padrão
        elemento.empty().append('<option value="">📋 Clique para carregar produtos disponíveis</option>');

        // Adicionar classes de estilo
        elemento.addClass('produto-select');

        // Adicionar tooltip explicativo
        elemento.attr('title', 'Clique para carregar produtos disponíveis');

        // Adicionar evento de foco para buscar produtos
        elemento.off('focus.produtos').on('focus.produtos', function() {
            if ($(this).find('option').length <= 1) {
                loadProductsForSelect();
            }
        });

        // Adicionar evento de click como alternativa
        elemento.off('click.produtos').on('click.produtos', function() {
            if ($(this).find('option').length <= 1) {
                loadProductsForSelect();
            }
        });

        console.log('✅ Select de produtos configurado com eventos e estilos');
    }

    // Carregar produtos para o select
    function loadProductsForSelect() {
        const elemento = $('#modal_produto_relacionado_id');
        const buscarUrl = '{{ route("comerciantes.produtos.relacionados.buscar", $produto->id) }}';

        // Indicar carregamento
        elemento.prop('disabled', true);
        elemento.removeClass('produto-select sucesso erro').addClass('carregando');
        elemento.empty().append('<option value="">⏳ Carregando produtos disponíveis...</option>');

        $.ajax({
            url: buscarUrl,
            method: 'GET',
            data: {
                q: '',
                page: 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                elemento.empty().append('<option value="">✅ Selecione um produto...</option>');

                const results = data.results || data || [];
                results.forEach(function(item) {
                    elemento.append(`<option value="${item.id}">${item.text}</option>`);
                });

                elemento.prop('disabled', false);
                elemento.removeClass('carregando erro').addClass('sucesso');

                // Mostrar pequena notificação de sucesso
                const small = elemento.siblings('small');
                const originalText = small.text();
                small.text(`✅ ${results.length} produtos carregados com sucesso!`).css('color', '#0f5132');

                setTimeout(() => {
                    small.text(originalText).css('color', '');
                    elemento.removeClass('sucesso').addClass('produto-select');
                }, 3000);

                console.log(`✅ ${results.length} produtos carregados no select`);
            },
            error: function(xhr, status, error) {
                console.error('❌ Erro ao carregar produtos:', error);
                elemento.empty().append('<option value="">❌ Erro ao carregar produtos</option>');
                elemento.prop('disabled', false);
                elemento.removeClass('carregando sucesso').addClass('erro');

                // Mostrar erro para o usuário
                const small = elemento.siblings('small');
                const originalText = small.text();
                small.text('❌ Erro ao carregar produtos. Tente novamente.').css('color', '#721c24');

                setTimeout(() => {
                    small.text(originalText).css('color', '');
                    elemento.empty().append('<option value="">📋 Clique para tentar novamente</option>');
                    elemento.removeClass('erro').addClass('produto-select');
                }, 5000);
            }
        });
    } // Função para inicializar Select2
    function initializeSelect2() {
        select2InitAttempts++;
        console.log(`🚀 Iniciando configuração do Select2... (tentativa ${select2InitAttempts}/${MAX_INIT_ATTEMPTS})`);

        const elemento = $('#modal_produto_relacionado_id');
        if (elemento.length === 0) {
            console.error('❌ Elemento não encontrado!');
            return false;
        }

        // Verificar se Select2 está disponível
        if (typeof $.fn.select2 === 'undefined') {
            if (select2InitAttempts >= MAX_INIT_ATTEMPTS) {
                console.error('❌ Select2 não disponível após máximo de tentativas, usando fallback');
                setupFallbackSelect();
                return false;
            }
            if (!select2LoadFailed) {
                console.warn(`⚠️ Select2 não disponível ainda, aguardando... (${select2InitAttempts}/${MAX_INIT_ATTEMPTS})`);
                setTimeout(() => initializeSelect2(), 1000);
                return false;
            } else {
                console.log('🔄 Select2 falhou, configurando fallback');
                setupFallbackSelect();
                return false;
            }
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
                            return {
                                results: data
                            };
                        } else {
                            return {
                                results: []
                            };
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

            return true;

        } catch (error) {
            console.error('❌ Erro ao configurar Select2:', error);
            setupFallbackSelect();
            return false;
        }
    }

    // Função de fallback para quando o Select2 não funcionar
    function setupFallbackSelect() {
        console.log('🔄 Configurando select fallback sem Select2...');
        const elemento = $('#modal_produto_relacionado_id');

        // Remover qualquer Select2 existente
        if (elemento.hasClass('select2-hidden-accessible')) {
            elemento.select2('destroy');
        }

        // Limpar o select e adicionar uma opção padrão
        elemento.empty().append('<option value="">📋 Clique para carregar produtos disponíveis</option>');

        // Adicionar estilo visual para indicar fallback
        elemento.addClass('fallback-select').css({
            'border': '2px solid #28a745',
            'background-color': '#f8f9fa'
        });

        // Adicionar tooltip explicativo
        elemento.attr('title', 'Modo alternativo ativo - Clique para carregar produtos');

        // Adicionar evento de foco para buscar produtos
        elemento.off('focus.fallback').on('focus.fallback', function() {
            if ($(this).find('option').length <= 1) {
                loadProductsForFallback();
            }
        });

        // Adicionar evento de click como alternativa
        elemento.off('click.fallback').on('click.fallback', function() {
            if ($(this).find('option').length <= 1) {
                loadProductsForFallback();
            }
        });

        console.log('✅ Select fallback configurado com eventos e estilos');
    }

    // Carregar produtos para o fallback
    function loadProductsForFallback() {
        const elemento = $('#modal_produto_relacionado_id');
        const buscarUrl = '{{ route("comerciantes.produtos.relacionados.buscar", $produto->id) }}';

        // Indicar carregamento
        elemento.prop('disabled', true);
        elemento.empty().append('<option value="">⏳ Carregando produtos disponíveis...</option>');
        elemento.css('color', '#6c757d');

        $.ajax({
            url: buscarUrl,
            method: 'GET',
            data: {
                q: '',
                page: 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                elemento.empty().append('<option value="">✅ Selecione um produto...</option>');

                const results = data.results || data || [];
                results.forEach(function(item) {
                    elemento.append(`<option value="${item.id}">${item.text}</option>`);
                });

                elemento.prop('disabled', false);
                elemento.css('color', '#495057');

                // Mostrar pequena notificação de sucesso
                const small = elemento.siblings('small');
                const originalText = small.text();
                small.text(`✅ ${results.length} produtos carregados com sucesso!`).css('color', '#28a745');

                setTimeout(() => {
                    small.text(originalText).css('color', '');
                }, 3000);

                console.log(`✅ ${results.length} produtos carregados no fallback`);
            },
            error: function(xhr, status, error) {
                console.error('❌ Erro ao carregar produtos:', error);
                elemento.empty().append('<option value="">❌ Erro ao carregar produtos</option>');
                elemento.prop('disabled', false);
                elemento.css('color', '#dc3545');

                // Mostrar erro para o usuário
                const small = elemento.siblings('small');
                const originalText = small.text();
                small.text('❌ Erro ao carregar produtos. Tente novamente.').css('color', '#dc3545');

                setTimeout(() => {
                    small.text(originalText).css('color', '');
                    elemento.empty().append('<option value="">📋 Clique para tentar novamente</option>');
                    elemento.css('color', '#495057');
                }, 5000);
            }
        });
    }

    // Inicialização do documento
    $(document).ready(function() {
        console.log('📊 Sistema de Produtos Relacionados Iniciando...');
        console.log('🚀 Versão: 2.0.0 - Native Select + AJAX');

        // Carregar SortableJS se necessário
        loadSortableJS().then(() => {
            console.log('🚀 SortableJS processado, iniciando aplicação...');
            initializeApp();
        }).catch((error) => {
            console.warn('⚠️ Erro ao carregar SortableJS:', error);
            initializeApp();
        });
    });

    // Função principal de inicialização
    function initializeApp() {
        // Debug básico
        console.log('🔍 Verificação de bibliotecas:');
        console.log('Bootstrap:', typeof bootstrap !== 'undefined' ? '✅' : '❌');
        console.log('jQuery:', typeof $ !== 'undefined' ? '✅' : '❌');
        console.log('Sortable:', typeof Sortable !== 'undefined' ? '✅' : '❌');

        // Configuração de modais
        $('[data-bs-toggle="modal"]').off('click').on('click', function(e) {
            const targetModal = $(this).data('bs-target');
            if (typeof bootstrap !== 'undefined' && targetModal) {
                const modalElement = document.querySelector(targetModal);
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            }
        });

        // Fechar modal
        $('[data-bs-dismiss="modal"]').off('click').on('click', function(e) {
            const modal = $(this).closest('.modal');
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

        // Inicializar select de produtos quando o modal for mostrado
        $('#modalNovoRelacionado').off('shown.bs.modal').on('shown.bs.modal', function(e) {
            console.log('🎯 Modal mostrado, configurando select de produtos...');
            setupProductSelect();
        });

        // Limpar select quando o modal for fechado
        $('#modalNovoRelacionado').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            console.log('🧹 Modal fechado, limpando select...');
            const elemento = $('#modal_produto_relacionado_id');
            elemento.val('').trigger('change');
            elemento.removeClass('produto-select carregando sucesso erro');
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

            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(document.getElementById('modalEditarRelacionado'));
                modal.show();
            }
        });

        // Atualizar ordem via input
        $('.ordem-input').on('change', function() {
            const id = $(this).data('id');
            const novaOrdem = $(this).val();
            updateOrdemIndividual(id, novaOrdem);
        });

        // Funções auxiliares
        function updateOrdem(tipo) {
            const relacionados = [];
            $(`.sortable-table[data-tipo="${tipo}"] tbody tr`).each(function(index) {
                relacionados.push({
                    id: $(this).data('id'),
                    ordem: index
                });
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
                    if (response.success && typeof toastr !== 'undefined') {
                        toastr.success('Ordem atualizada com sucesso!');
                    }
                },
                error: function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Erro ao atualizar ordem');
                    }
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
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Ordem atualizada!');
                    }
                },
                error: function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Erro ao atualizar ordem');
                    }
                }
            });
        }
    }
</script>
@endpush
@extends('layouts.comerciante')

@section('title', 'Códigos de Barras')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-barcode"></i> Códigos de Barras
        </h1>
        <div>
            <a href="{{ route('comerciantes.produtos.codigos-barras.scanner') }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-camera"></i> Scanner
            </a>
            <a href="{{ route('comerciantes.produtos.codigos-barras.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Código
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtros
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comerciantes.produtos.codigos-barras.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="produto_id">Produto</label>
                        <select name="produto_id" id="produto_id" class="form-control">
                            <option value="">Todos os produtos</option>
                            @foreach($produtos as $produto)
                                <option value="{{ $produto->id }}" 
                                    {{ request('produto_id') == $produto->id ? 'selected' : '' }}>
                                    {{ $produto->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="tipo">Tipo</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="">Todos os tipos</option>
                            @foreach($tipos as $valor => $descricao)
                                <option value="{{ $valor }}" 
                                    {{ request('tipo') == $valor ? 'selected' : '' }}>
                                    {{ $descricao }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="ativo">Status</label>
                        <select name="ativo" id="ativo" class="form-control">
                            <option value="">Todos</option>
                            <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativos</option>
                            <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="busca">Buscar</label>
                        <input type="text" name="busca" id="busca" class="form-control" 
                               placeholder="Código ou nome do produto..." value="{{ request('busca') }}">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('comerciantes.produtos.codigos-barras.index') }}" 
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Códigos de Barras -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Lista de Códigos de Barras
                <small class="text-muted">({{ $codigosBarras->total() }} encontrados)</small>
            </h6>
        </div>
        <div class="card-body">
            @if($codigosBarras->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Produto</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Principal</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($codigosBarras as $codigo)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-barcode text-primary mr-2"></i>
                                            <div>
                                                <strong class="font-monospace">{{ $codigo->codigo }}</strong>
                                                @if($codigo->observacoes)
                                                    <br><small class="text-muted">{{ Str::limit($codigo->observacoes, 30) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $codigo->produto->nome }}</strong>
                                            <br><small class="text-muted">SKU: {{ $codigo->produto->sku }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $codigo->tipo_descricao }}</span>
                                        @if(!$codigo->isValido())
                                            <br><small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Inválido
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-ativo" 
                                                   id="ativo_{{ $codigo->id }}" 
                                                   data-id="{{ $codigo->id }}"
                                                   {{ $codigo->ativo ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="ativo_{{ $codigo->id }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($codigo->principal)
                                            <span class="badge badge-success">
                                                <i class="fas fa-star"></i> Principal
                                            </span>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary definir-principal" 
                                                    data-id="{{ $codigo->id }}" title="Definir como principal">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $codigo->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('comerciantes.produtos.codigos-barras.show', $codigo) }}" 
                                               class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comerciantes.produtos.codigos-barras.edit', $codigo) }}" 
                                               class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($codigo->podeSerDeletado())
                                                <form action="{{ route('comerciantes.produtos.codigos-barras.destroy', $codigo) }}" 
                                                      method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled 
                                                        title="Único código do produto - não pode ser excluído">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center">
                    {{ $codigosBarras->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-barcode fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum código de barras encontrado</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['produto_id', 'tipo', 'ativo', 'busca']))
                            Tente ajustar os filtros ou 
                            <a href="{{ route('comerciantes.produtos.codigos-barras.index') }}">remover todos os filtros</a>.
                        @else
                            Comece criando códigos de barras para seus produtos.
                        @endif
                    </p>
                    <a href="{{ route('comerciantes.produtos.codigos-barras.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Código de Barras
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Códigos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $codigosBarras->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-barcode fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Códigos Ativos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $codigosBarras->where('ativo', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Produtos com Códigos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $codigosBarras->unique('produto_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Códigos EAN</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $codigosBarras->whereIn('tipo', ['ean13', 'ean8'])->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-gray-300"></i>
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
$(document).ready(function() {
    // Toggle ativo via AJAX
    $('.toggle-ativo').change(function() {
        const id = $(this).data('id');
        const ativo = $(this).is(':checked');
        
        $.ajax({
            url: `/comerciantes/produtos/codigos-barras/${id}/toggle-ativo`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(ativo ? 'Código ativado!' : 'Código desativado!');
                }
            },
            error: function() {
                toastr.error('Erro ao alterar status do código');
                // Reverter o checkbox
                $(`#ativo_${id}`).prop('checked', !ativo);
            }
        });
    });

    // Definir como principal
    $('.definir-principal').click(function() {
        const id = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: `/comerciantes/produtos/codigos-barras/${id}/definir-principal`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload(); // Recarregar para atualizar os badges
                }
            },
            error: function() {
                toastr.error('Erro ao definir código como principal');
            }
        });
    });

    // Confirmação de exclusão
    $('.form-delete').submit(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Confirmar exclusão?',
            text: 'Esta ação não pode ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush

@extends('layouts.comerciante')

@section('title', 'Categorias de Conta')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Categorias de Conta - Empresa {{ $empresa }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a></li>
                        <li class="breadcrumb-item active">Categorias</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h5 class="card-title mb-0">Lista de Categorias</h5>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a href="{{ route('comerciantes.empresas.financeiro.categorias.create', $empresa) }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Nova Categoria
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100" id="categorias-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categorias as $categoria)
                                <tr>
                                    <td>
                                        <h5 class="font-14 my-1">{{ $categoria->nome }}</h5>
                                        <span class="text-muted font-13">{{ $categoria->nome_completo }}</span>
                                    </td>
                                    <td>
                                        @if($categoria->e_custo)
                                            <span class="badge bg-info">Custo</span>
                                        @endif
                                        @if($categoria->e_despesa)
                                            <span class="badge bg-danger">Despesa</span>
                                        @endif
                                        @if($categoria->e_receita)
                                            <span class="badge bg-success">Receita</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($categoria->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('comerciantes.empresas.financeiro.categorias.show', [$empresa, $categoria->id]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('comerciantes.empresas.financeiro.categorias.edit', [$empresa, $categoria->id]) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="py-4">
                                            <i class="mdi mdi-folder-open-outline h1 text-muted"></i>
                                            <h5 class="text-muted">Nenhuma categoria encontrada</h5>
                                            <p class="text-muted">Comece criando sua primeira categoria de conta.</p>
                                            <a href="{{ route('comerciantes.empresas.financeiro.categorias.create', $empresa) }}" class="btn btn-primary">
                                                <i class="mdi mdi-plus"></i> Criar Primeira Categoria
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($categorias instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="row">
                            <div class="col-12">
                                {{ $categorias->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection









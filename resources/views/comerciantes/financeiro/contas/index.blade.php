@extends('comerciantes.layouts.app')

@section('title', 'Contas Gerenciais')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Contas Gerenciais - Empresa {{ $empresa }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a></li>
                        <li class="breadcrumb-item active">Contas</li>
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
                            <h5 class="card-title mb-0">Plano de Contas</h5>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a href="{{ route('comerciantes.empresas.financeiro.contas.create', $empresa) }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Nova Conta
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100" id="contas-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Natureza</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contas as $conta)
                                <tr>
                                    <td>
                                        <strong>{{ $conta->codigo }}</strong>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1">{{ $conta->nome }}</h5>
                                        @if($conta->descricao)
                                            <span class="text-muted font-13">{{ $conta->descricao }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($conta->categoria)
                                            <span class="badge bg-primary">{{ $conta->categoria->nome }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $conta->natureza->color() }}">{{ $conta->natureza->value }}</span>
                                    </td>
                                    <td>
                                        @if($conta->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('comerciantes.empresas.financeiro.contas.show', [$empresa, $conta->id]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('comerciantes.empresas.financeiro.contas.edit', [$empresa, $conta->id]) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="py-4">
                                            <i class="mdi mdi-bank-outline h1 text-muted"></i>
                                            <h5 class="text-muted">Nenhuma conta encontrada</h5>
                                            <p class="text-muted">Comece criando seu plano de contas.</p>
                                            <a href="{{ route('comerciantes.empresas.financeiro.contas.create', $empresa) }}" class="btn btn-primary">
                                                <i class="mdi mdi-plus"></i> Criar Primeira Conta
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($contas instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="row">
                            <div class="col-12">
                                {{ $contas->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

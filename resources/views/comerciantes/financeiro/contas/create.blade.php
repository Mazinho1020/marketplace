@extends('comerciantes.layouts.app')

@section('title', 'Nova Conta')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Nova Conta - Empresa {{ $empresa }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.dashboard', $empresa) }}">Financeiro</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.financeiro.contas.index', $empresa) }}">Contas</a></li>
                        <li class="breadcrumb-item active">Nova</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Criar Nova Conta</h5>
                    
                    <form action="{{ route('comerciantes.empresas.financeiro.contas.store', $empresa) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="codigo" class="form-label">Código *</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="natureza" class="form-label">Natureza da Conta *</label>
                                    <select class="form-select" id="natureza" name="natureza" required>
                                        <option value="">Selecione...</option>
                                        <option value="D">Débito</option>
                                        <option value="C">Crédito</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" checked>
                                        <label class="form-check-label" for="ativo">
                                            Ativo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i> Salvar
                                </button>
                                <a href="{{ route('comerciantes.empresas.financeiro.contas.index', $empresa) }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Voltar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection









@extends('comerciantes.layouts.app')

@section('title', 'Detalhes da Conta Gerencial')

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
            <li class="breadcrumb-item active" aria-current="page">{{ $conta->nome }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $conta->nome }}</h1>
        <div class="btn-group">
            <a href="{{ route('comerciantes.empresas.financeiro.contas.edit', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
               class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('comerciantes.empresas.financeiro.contas.index', $empresa) }}" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações Principais -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informações da Conta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Nome:</dt>
                                <dd class="col-sm-8">{{ $conta->nome }}</dd>

                                @if($conta->codigo)
                                <dt class="col-sm-4">Código:</dt>
                                <dd class="col-sm-8">
                                    <code>{{ $conta->codigo }}</code>
                                </dd>
                                @endif

                                <dt class="col-sm-4">Natureza:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-{{ $conta->natureza->color() }}">
                                        {{ $conta->natureza->label() }}
                                    </span>
                                </dd>

                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-{{ $conta->ativo ? 'success' : 'secondary' }}">
                                        {{ $conta->ativo ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </dd>

                                @if($conta->nivel)
                                <dt class="col-sm-4">Nível:</dt>
                                <dd class="col-sm-8">{{ $conta->nivel }}</dd>
                                @endif
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <dl class="row">
                                @if($conta->categoria)
                                <dt class="col-sm-4">Categoria:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-info">{{ $conta->categoria->nome }}</span>
                                </dd>
                                @endif

                                @if($conta->classificacaoDre)
                                <dt class="col-sm-4">Classificação DRE:</dt>
                                <dd class="col-sm-8">{{ $conta->classificacaoDre->nome }}</dd>
                                @endif

                                @if($conta->tipo)
                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8">{{ $conta->tipo->nome }}</dd>
                                @endif

                                @if($conta->contaPai)
                                <dt class="col-sm-4">Conta Pai:</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->contaPai->id]) }}">
                                        {{ $conta->contaPai->nome }}
                                    </a>
                                </dd>
                                @endif

                                <dt class="col-sm-4">Aceita Lançamento:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-{{ $conta->aceita_lancamento ? 'success' : 'warning' }}">
                                        {{ $conta->aceita_lancamento ? 'Sim' : 'Não' }}
                                    </span>
                                </dd>

                                <dt class="col-sm-4">É Sintética:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-{{ $conta->e_sintetica ? 'info' : 'light' }}">
                                        {{ $conta->e_sintetica ? 'Sim' : 'Não' }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    @if($conta->descricao)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Descrição:</strong>
                            <p class="mt-2">{{ $conta->descricao }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Classificações -->
            @if($conta->e_custo || $conta->e_despesa || $conta->e_receita)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags"></i> Classificações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @if($conta->e_custo)
                            <span class="badge badge-warning">Custo</span>
                        @endif
                        @if($conta->e_despesa)
                            <span class="badge badge-danger">Despesa</span>
                        @endif
                        @if($conta->e_receita)
                            <span class="badge badge-success">Receita</span>
                        @endif
                    </div>

                    @if($conta->grupo_dre)
                    <div class="mt-3">
                        <strong>Grupo DRE:</strong> {{ $conta->grupo_dre }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Contas Filhas -->
            @if($conta->filhos && $conta->filhos->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sitemap"></i> Contas Filhas ({{ $conta->filhos->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Código</th>
                                    <th>Natureza</th>
                                    <th>Status</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conta->filhos as $filho)
                                <tr>
                                    <td>{{ $filho->nome }}</td>
                                    <td>
                                        @if($filho->codigo)
                                            <code>{{ $filho->codigo }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $filho->natureza->color() }} badge-sm">
                                            {{ $filho->natureza->value }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $filho->ativo ? 'success' : 'secondary' }} badge-sm">
                                            {{ $filho->ativo ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $filho->id]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('comerciantes.empresas.financeiro.contas.edit', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Editar Conta
                        </a>
                        
                        @if($conta->aceita_lancamento)
                        <button class="btn btn-success btn-sm" type="button">
                            <i class="fas fa-plus"></i> Novo Lançamento
                        </button>
                        @endif

                        <button class="btn btn-info btn-sm" type="button">
                            <i class="fas fa-chart-line"></i> Ver Relatórios
                        </button>

                        <hr class="my-3">

                        <button class="btn btn-outline-danger btn-sm" 
                                onclick="if(confirm('Tem certeza que deseja excluir esta conta?')) { document.getElementById('delete-form').submit(); }">
                            <i class="fas fa-trash"></i> Excluir Conta
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informações Técnicas -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Informações Técnicas
                    </h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <strong>ID:</strong> {{ $conta->id }}
                        </div>
                        <div class="mb-2">
                            <strong>Criado em:</strong> {{ $conta->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="mb-2">
                            <strong>Atualizado em:</strong> {{ $conta->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @if($conta->ordem_exibicao)
                        <div class="mb-2">
                            <strong>Ordem:</strong> {{ $conta->ordem_exibicao }}
                        </div>
                        @endif
                    </small>
                </div>
            </div>

            <!-- Visual -->
            @if($conta->cor || $conta->icone)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-palette"></i> Visual
                    </h5>
                </div>
                <div class="card-body">
                    @if($conta->cor)
                    <div class="mb-3">
                        <strong>Cor:</strong>
                        <span class="d-inline-block ms-2" 
                              style="width: 20px; height: 20px; background-color: {{ $conta->cor }}; border: 1px solid #ddd; border-radius: 3px;"></span>
                        <code class="ms-2">{{ $conta->cor }}</code>
                    </div>
                    @endif

                    @if($conta->icone)
                    <div>
                        <strong>Ícone:</strong>
                        <i class="{{ $conta->icone }} ms-2"></i>
                        <code class="ms-2">{{ $conta->icone }}</code>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Form para deletar -->
<form id="delete-form" 
      action="{{ route('comerciantes.empresas.financeiro.contas.destroy', ['empresa' => $empresa, 'id' => $conta->id]) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
.badge-sm {
    font-size: 0.75em;
}

.gap-2 > * {
    margin-bottom: 0.5rem;
}

dl.row {
    margin-bottom: 0;
}

dl.row dt {
    font-weight: 600;
}

dl.row dd {
    margin-bottom: 0.5rem;
}
</style>
@endpush

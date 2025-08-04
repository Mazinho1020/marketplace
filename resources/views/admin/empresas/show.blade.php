@extends('layouts.admin')

@section('title', 'Detalhes da Empresa')

@php
    $pageTitle = 'Detalhes da Empresa';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Empresas', 'url' => route('admin.empresas.index')],
        ['title' => $empresa->nome_fantasia, 'url' => '#']
    ];
@endphp

@section('content')
<div class="container-fluid">
    <!-- Header da Empresa -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-building fa-2x text-white"></i>
                                </div>
                                <div>
                                    <h2 class="mb-1">{{ $empresa->nome_fantasia }}</h2>
                                    <p class="text-muted mb-1">{{ $empresa->razao_social }}</p>
                                    <div class="d-flex gap-2">
                                        {!! $empresa->status_badge !!}
                                        {!! $empresa->plano_badge !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.empresas.edit', $empresa->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Editar
                                </a>
                                <form method="POST" action="{{ route('admin.empresas.toggle-status', $empresa->id) }}" 
                                      class="d-inline" onsubmit="return confirm('Alterar status da empresa?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fas fa-toggle-on me-2"></i>Alterar Status
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas da Empresa -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                    <h4>{{ number_format($stats['total_transacoes']) }}</h4>
                    <p class="text-muted mb-0">Total de Transações</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                    <h4>R$ {{ number_format($stats['valor_total_transacoes'], 2, ',', '.') }}</h4>
                    <p class="text-muted mb-0">Volume Total</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-month fa-2x text-info mb-2"></i>
                    <h4>{{ number_format($stats['transacoes_mes']) }}</h4>
                    <p class="text-muted mb-0">Transações este Mês</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4>{{ $stats['dias_desde_cadastro'] }}</h4>
                    <p class="text-muted mb-0">Dias Ativo</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações da Empresa -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informações da Empresa</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Nome Fantasia:</strong></div>
                        <div class="col-sm-8">{{ $empresa->nome_fantasia }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Razão Social:</strong></div>
                        <div class="col-sm-8">{{ $empresa->razao_social }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>CNPJ:</strong></div>
                        <div class="col-sm-8">{{ $empresa->cnpj }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>E-mail:</strong></div>
                        <div class="col-sm-8">{{ $empresa->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Telefone:</strong></div>
                        <div class="col-sm-8">{{ $empresa->telefone ?: 'Não informado' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Endereço:</strong></div>
                        <div class="col-sm-8">{{ $empresa->endereco ?: 'Não informado' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Cidade/Estado:</strong></div>
                        <div class="col-sm-8">{{ $empresa->cidade ? $empresa->cidade . '/' . $empresa->estado : 'Não informado' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>CEP:</strong></div>
                        <div class="col-sm-8">{{ $empresa->cep ?: 'Não informado' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações Comerciais -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informações Comerciais</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">{!! $empresa->status_badge !!}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Plano:</strong></div>
                        <div class="col-sm-8">{!! $empresa->plano_badge !!}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Valor Mensal:</strong></div>
                        <div class="col-sm-8">
                            @if($empresa->valor_mensalidade)
                                <strong>R$ {{ number_format($empresa->valor_mensalidade, 2, ',', '.') }}</strong>
                            @else
                                <span class="text-muted">Não definido</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Vencimento:</strong></div>
                        <div class="col-sm-8">
                            @if($empresa->data_vencimento)
                                <span class="{{ $empresa->vencido ? 'text-danger' : ($empresa->vencimento_proximo ? 'text-warning' : '') }}">
                                    {{ $empresa->data_vencimento->format('d/m/Y') }}
                                    @if($empresa->vencido)
                                        <small class="text-danger">(Vencido)</small>
                                    @elseif($empresa->vencimento_proximo)
                                        <small class="text-warning">(Próximo)</small>
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">Não definido</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Cadastrado em:</strong></div>
                        <div class="col-sm-8">{{ $empresa->created_at ? $empresa->created_at->format('d/m/Y H:i') : 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Última atualização:</strong></div>
                        <div class="col-sm-8">{{ $empresa->updated_at ? $empresa->updated_at->format('d/m/Y H:i') : 'N/A' }}</div>
                    </div>
                    @if($stats['ultima_transacao'])
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Última transação:</strong></div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($stats['ultima_transacao']->data_transacao)->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Atividade Recente -->
    @if($atividadeRecente->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Atividade Recente</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Gateway</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atividadeRecente as $atividade)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($atividade->data_transacao)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $atividade->tipo ?? 'Transação' }}</td>
                                    <td>R$ {{ number_format($atividade->valor_final ?? 0, 2, ',', '.') }}</td>
                                    <td>
                                        @if($atividade->status === 'aprovada')
                                            <span class="badge bg-success">Aprovada</span>
                                        @elseif($atividade->status === 'pendente')
                                            <span class="badge bg-warning">Pendente</span>
                                        @else
                                            <span class="badge bg-danger">Falhou</span>
                                        @endif
                                    </td>
                                    <td>{{ $atividade->gateway ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Observações -->
    @if($empresa->observacoes)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Observações</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $empresa->observacoes }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}

.card h4 {
    font-weight: 600;
}

.card .fas {
    opacity: 0.8;
}
</style>
@endpush
